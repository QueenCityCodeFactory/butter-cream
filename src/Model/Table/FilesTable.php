<?php
declare(strict_types=1);

namespace ButterCream\Model\Table;

use ArrayObject;
use ButterCream\Message\Exception\StatusMessageException;
use ButterCream\Model\Table\AppTable as Table;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use Exception;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Files Model
 *
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @property \App\Model\Table\PhotosTable&\Cake\ORM\Association\BelongsTo $Photos
 * @property \App\Model\Table\SexesTable&\Cake\ORM\Association\BelongsTo $Sexes
 * @property \App\Model\Table\OrganizationsTable&\Cake\ORM\Association\BelongsTo $Organizations
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FilesTable extends Table
{
    /**
     * Searchable Filter Args
     *
     * @var array
     */
    public array $filterArgs = [
    ];

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
     * @param array $config The configuration for the Table.
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
            ->uuid('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('category')
            ->maxLength('category', 45)
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

        $validator
            ->scalar('tag')
            ->maxLength('tag', 36)
            ->requirePresence('tag', 'create')
            ->notEmptyString('tag');

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
     * AfterSave Callback
     *
     * @param \Cake\Event\EventInterface $event The event object
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options The options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        /** @var \ButterCream\Model\Entity\File $entity */
        parent::afterSave($event, $entity, $options);

        if ($this->skipAfterSave !== true && $entity->isNew()) {
            $filePath = $entity->filename;

            $sourceAdapter = new LocalFilesystemAdapter(Configure::read('FileApi.tmpPath'));
            $sourceFilesystem = new Filesystem($sourceAdapter);

            if (!$sourceFilesystem->fileExists($filePath)) {
                throw new StatusMessageException('file_api_missing_tmp_file');
            }

            if (!isset($entity->category) || !isset($entity->tag)) {
                throw new StatusMessageException('file_api_missing_metadata');
            }

            if (empty($entity->original_filename)) {
                $entity->original_filename = basename($filePath);
            }

            $destinationAdapter = new LocalFilesystemAdapter(Configure::read('FileApi.basePath'));
            $destinationFilesystem = new Filesystem($destinationAdapter);
            try {
                $destinationFilesystem->createDirectory(
                    Configure::read('FileApi.basePath') . $entity->category . DS . $entity->tag,
                );
                $destinationFilesystem->write(
                    $entity->category . DS . $entity->tag . DS . $entity->filename,
                    $sourceFilesystem->read($filePath),
                );
                $sourceFilesystem->delete($filePath);
            } catch (Exception) {
                throw new StatusMessageException('file_api_can_not_copy_file');
            }
        }
    }

    /**
     * Event fired after the record has been deleted
     *
     * @param \Cake\Event\EventInterface $event The event object
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options The options
     * @return void
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        /** @var \ButterCream\Model\Entity\File $entity */
        $basePath = Configure::read('FileApi.basePath');

        parent::beforeDelete($event, $entity, $options);

        $sourceAdapter = new LocalFilesystemAdapter($basePath);
        $sourceFilesystem = new Filesystem($sourceAdapter);

        $sourceFilesystem->delete($entity->category . DS . $entity->tag . DS . $entity->filename);

        $directories = [
            $entity->category . DS . $entity->tag,
            $entity->category,
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
