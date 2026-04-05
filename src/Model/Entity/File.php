<?php
declare(strict_types=1);

namespace ButterCream\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * File Entity
 *
 * @property int $id
 * @property string $uuid
 * @property string $model
 * @property string $foreign_key
 * @property string $filename
 * @property string|null $original_filename
 * @property int|null|false $size
 * @property string $path
 * @property string|false $contents
 * @property string $base64
 * @property \Cake\ORM\Entity\text|array|null $meta
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class File extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false,
        'Referer' => false,
    ];

    /**
     * Get Path
     *
     * @return string File Path
     */
    protected function _getPath(): string
    {
        return Configure::read('FileService.basePath') . $this->model . DS . $this->foreign_key . DS . $this->filename;
    }

    /**
     * Get URI
     *
     * @return string File base64 contents
     */
    protected function _getBase64(): string
    {
        $filePath = $this->model . DS . $this->foreign_key . DS . $this->filename;

        $adapter = new LocalFilesystemAdapter(Configure::read('FileService.basePath'));
        $filesystem = new Filesystem($adapter);

        $contents = '';
        if ($filesystem->fileExists($filePath)) {
            $contents = $filesystem->read($filePath);
        }

        return 'data:' . $filesystem->mimeType($filePath) . ';base64,' . base64_encode((string)$contents);
    }
}
