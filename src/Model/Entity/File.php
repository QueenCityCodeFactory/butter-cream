<?php
namespace ButterCream\Model\Entity;

use Cake\Core\Configure;
use Cake\Filesystem\File as CakeFile;
use Cake\ORM\Entity;

/**
 * File Entity.
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
        $file = new CakeFile($this->path);

        return 'data:' . $file->mime() . ';base64,' . base64_encode($file->read());
    }
}
