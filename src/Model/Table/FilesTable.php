<?php
declare(strict_types=1);

namespace ButterCream\Model\Table;

use ArrayObject;
use ButterCream\Message\Exception\StatusMessageException;
use ButterCream\Model\Table\AppTable as Table;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Exception;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Files Model
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FilesTable extends Table
{
    /**
     * Skip afterSave
     *
     * @var bool
     */
    private bool $skipAfterSave = false;

    /**
     * Set Skip After Save
     *
     * @param bool $skip True/False
     * @return void
     */
    public function setSkipAfterSave(bool $skip): void
    {
        $this->skipAfterSave = $skip;
    }

    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('files');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');

        // Configure schema column types
        $this->getSchema()->setColumnType('meta', 'json');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->uuid('uuid')
            ->notEmptyString('uuid');

        $validator
            ->scalar('model')
            ->maxLength('model', 255)
            ->requirePresence('model', 'create')
            ->notEmptyString('model');

        $validator
            ->scalar('foreign_key')
            ->maxLength('foreign_key', 36)
            ->requirePresence('foreign_key', 'create')
            ->notEmptyString('foreign_key');

        $validator
            ->scalar('filename')
            ->maxLength('filename', 255)
            ->requirePresence('filename', 'create')
            ->notEmptyString('filename');

        $validator
            ->scalar('original_filename')
            ->maxLength('original_filename', 255)
            ->allowEmptyString('original_filename');

        $validator
            ->integer('size')
            ->allowEmptyString('size');

        return $validator;
    }

    /**
     * BeforeMarshal Callback - Auto-generate UUID for new records
     *
     * @param \Cake\Event\EventInterface<\Cake\ORM\Table> $event The beforeMarshal event that was fired
     * @param \ArrayObject<string, mixed> $data ArrayObject instance.
     * @param \ArrayObject<string, mixed> $options ArrayObject instance.
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        parent::beforeMarshal($event, $data, $options);

        if (!isset($data['uuid'])) {
            $data['uuid'] = Text::uuid();
        }
    }

    /**
     * Get the base path for file storage.
     *
     * Override this method to customize the storage location in subclasses.
     *
     * @return string
     */
    protected function getBasePath(): string
    {
        return (string)Configure::read('FileService.basePath');
    }

    /**
     * Get the temporary path for file uploads.
     *
     * Override this method to customize the temp location in subclasses.
     *
     * @return string
     */
    protected function getTmpPath(): string
    {
        return (string)Configure::read('FileService.tmpPath');
    }

    /**
     * Build the relative file path for an entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity The file entity
     * @return string
     */
    protected function buildRelativePath(EntityInterface $entity): string
    {
        /** @var \ButterCream\Model\Entity\File $entity */
        return $entity->model . DS . $entity->foreign_key . DS . $entity->filename;
    }

    /**
     * AfterSave Callback
     *
     * @param \Cake\Event\EventInterface<\Cake\ORM\Table> $event The event object
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject<string, mixed> $options The options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        /** @var \ButterCream\Model\Entity\File $entity */
        parent::afterSave($event, $entity, $options);

        if ($this->skipAfterSave !== true && $entity->isNew()) {
            $filePath = $entity->filename;

            $sourceAdapter = new LocalFilesystemAdapter($this->getTmpPath());
            $sourceFilesystem = new Filesystem($sourceAdapter);

            if (!$sourceFilesystem->fileExists($filePath)) {
                throw new StatusMessageException('file_service_missing_tmp_file');
            }

            if (!isset($entity->model) || !isset($entity->foreign_key)) {
                throw new StatusMessageException('file_service_missing_metadata');
            }

            if (empty($entity->original_filename)) {
                $entity->original_filename = basename($filePath);
            }

            $destinationAdapter = new LocalFilesystemAdapter($this->getBasePath());
            $destinationFilesystem = new Filesystem($destinationAdapter);
            try {
                $relativePath = $this->buildRelativePath($entity);
                $destinationFilesystem->createDirectory(
                    $this->getBasePath() . $entity->model . DS . $entity->foreign_key,
                );
                $destinationFilesystem->write(
                    $relativePath,
                    $sourceFilesystem->read($filePath),
                );
                $sourceFilesystem->delete($filePath);
            } catch (Exception) {
                throw new StatusMessageException('file_service_can_not_copy_file');
            }
        }
    }

    /**
     * Event fired before the record has been deleted
     *
     * @param \Cake\Event\EventInterface<\Cake\ORM\Table> $event The event object
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject<string, mixed> $options The options
     * @return void
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        /** @var \ButterCream\Model\Entity\File $entity */
        $basePath = $this->getBasePath();

        parent::beforeDelete($event, $entity, $options);

        $sourceAdapter = new LocalFilesystemAdapter($basePath);
        $sourceFilesystem = new Filesystem($sourceAdapter);

        $sourceFilesystem->delete($this->buildRelativePath($entity));

        $directories = [
            $entity->model . DS . $entity->foreign_key,
            $entity->model,
        ];

        foreach ($directories as $directory) {
            $contents = $sourceFilesystem->listContents($directory, false);

            $isEmpty = true;

            foreach ($contents as $item) {
                if ($item instanceof DirectoryAttributes || $item instanceof FileAttributes) {
                    $isEmpty = false;
                    break;
                }
            }

            if ($isEmpty) {
                $sourceFilesystem->deleteDirectory($directory);
            }
        }
    }
}
