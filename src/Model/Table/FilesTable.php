<?php
declare(strict_types=1);

namespace ButterCream\Model\Table;

use ArrayObject;
use ButterCream\Message\Exception\StatusMessageException;
use ButterCream\Model\Table\AppTable as Table;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Filesystem\File as CakeFile;
use Cake\Filesystem\Folder;
use Cake\Validation\Validator;

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
    public $filterArgs = [
    ];

    /**
     * Skip afterSave
     *
     * @var bool
     */
    private $skipAfterSave = false;

    /**
     * Set Skip After Save
     *
     * @param bool $skip True/False
     * @return void
     */
    public function setSkipAfterSave(bool $skip)
    {
        $this->skipAfterSave = $skip;
    }

    /**
     * Initialize Schema
     *
     * @param \Cake\Database\Schema\TableSchemaInterface $schema Schema Settings
     * @return \Cake\Database\Schema\TableSchemaInterface The initialized schema
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('meta', 'json');

        return $schema;
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
            ->requirePresence('category', 'create')
            ->notEmptyString('category');

        $validator
            ->requirePresence('filename', 'create')
            ->notEmptyString('filename');

        $validator
            ->allowEmptyString('original_filename');

        $validator
            ->add('size', 'valid', ['rule' => 'numeric'])
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
            $tmpFilePath = Configure::read('FileApi.tmpPath') . $entity->filename;
            $tmpFile = new CakeFile($tmpFilePath);
            if (!$tmpFile->exists()) {
                throw new StatusMessageException('file_api_missing_tmp_file');
            }

            if (!isset($entity->category) || !isset($entity->tag)) {
                throw new StatusMessageException('file_api_missing_metadata');
            }

            if (empty($entity->original_filename)) {
                $entity->original_filename = $tmpFile->name;
            }

            $folder = new Folder(
                Configure::read('FileApi.basePath') . $entity->category . DS . $entity->tag,
                true,
                0755
            );
            $destFile = new CakeFile($folder->path . DS . $entity->filename);
            if (!$tmpFile->copy($destFile->path)) {
                throw new StatusMessageException('file_api_can_not_copy_file');
            }

            $tmpFile->delete();
            $tmpFile->close();
            $destFile->close();
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
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        /** @var \ButterCream\Model\Entity\File $entity */
        $basePath = Configure::read('FileApi.basePath');

        $file = new CakeFile($basePath . $entity->category . DS . $entity->tag . DS . $entity->filename);
        $file->delete();

        $folder = new Folder($basePath . $entity->category . DS . $entity->tag);
        $folderContents = $folder->read();
        if (empty($folderContents[0]) && empty($folderContents[1])) {
            // if the folder is empty of files and folders (0 and 1), delete it
            $folder->delete();
        }

        $folder = new Folder($basePath . $entity->category);
        $folderContents = $folder->read();
        if (empty($folderContents[0]) && empty($folderContents[1])) {
            // if the folder is empty of files and folders (0 and 1), delete it
            $folder->delete();
        }
    }
}
