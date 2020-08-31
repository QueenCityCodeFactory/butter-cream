<?php
declare(strict_types=1);

namespace ButterCream\Model\Entity;

use Cake\Core\Configure;
use Cake\Filesystem\File as CakeFile;
use Cake\ORM\Entity;

/**
 * File Entity
 *
 * @property string $id
 * @property string $category
 * @property string $tag
 * @property string $filename
 * @property string|null $original_filename
 * @property int|null|false $size
 * @property string $path
 * @property string|false $contents
 * @property string $base64
 * @property \Cake\ORM\Entity\text|array|null $meta
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class File extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     * Note that '*' is set to true, which allows all unspecified fields to be
     * mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'Referer' => false,
    ];

    /**
     * Get Path
     *
     * @return string File Path
     */
    protected function _getPath()
    {
        return Configure::read('FileApi.basePath') . $this->category . DS . $this->tag . DS . $this->filename;
    }

    /**
     * Get URI
     *
     * @return string File base64 contents
     */
    protected function _getBase64()
    {
        $file = new CakeFile($this->_getPath());

        $contents = $file->read();
        if (!$contents) {
            $contents = '';
        }

        return 'data:' . $file->mime() . ';base64,' . base64_encode($contents);
    }
}
