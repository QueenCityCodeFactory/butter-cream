<?php
declare(strict_types=1);

namespace ButterCream\Filesystem;

use ButterCream\Message\Exception\StatusMessageException;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Exception;
use Imagick;

/**
 * File API
 */
class FileApi
{
    /**
     * The TableRegistry object for the files database table
     *
     * @var \Cake\ORM\Table|null
     */
    protected static $_filesTable = null;

    /**
     * A default image width if none is specified for resizing
     *
     * @var int
     */
    public static $defaultImageWidth = 300;

    /**
     * A default image height if none is specified for resizing
     *
     * @var int
     */
    public static $defaultImageHeight = 300;

    /**
     * Allowed Mime Types for image processing & conversion
     *
     * @var array mime types
     */
    public static $validImageMimeTypes = [
        'image/gif',
        'image/jpeg',
        'image/png',
    ];

    /**
     * Sets up the _filesTable object if not already instantiated and then returns it
     *
     * @return \Cake\ORM\Table|null a Table object for the files database table
     */
    protected static function _setupFilesTable()
    {
        if (!isset(static::$_filesTable)) {
            static::$_filesTable = TableRegistry::get('files');
        }

        return static::$_filesTable;
    }

    /**
     * Get a file data from the file server/database
     *
     * @param string $id The id of the file to get
     * @param bool $contents Do you want the file content?
     * @return \ButterCream\Model\Entity\File The file object, patched with the file contents
     */
    public static function get(string $id, bool $contents = false)
    {
        $file = static::data($id);
        $file->path = Configure::read('FileApi.basePath') . $file->category . DS . $file->tag . DS . $file->filename;
        if ($contents !== false) {
            $file->contents = file_get_contents(
                Configure::read('FileApi.basePath') . $file->category . DS . $file->tag . DS . $file->filename
            );
        }

        return $file;
    }

    /**
     * Get the Base file data
     *
     * @param string $id The id of the file
     * @return \ButterCream\Model\Entity\File
     */
    public static function data(string $id)
    {
        $filesTable = static::_setupFilesTable();
        /** @var \ButterCream\Model\Entity\File $file */
        $file = $filesTable->get($id);
        if (!empty($file) && is_object($file)) {
            return $file;
        }
        throw new NotFoundException('The Panda was unable to retrieve the requested file.');
    }

    /**
     * Gets the Local File Path to a File
     *
     * @param string $id The id of the file
     * @return string|false The Local File Path
     */
    public static function getLocalPath(string $id)
    {
        $file = static::data($id);
        $path = Configure::read('FileApi.basePath') . $file->category . DS . $file->tag . DS . $file->filename;

        return file_exists($path) ? $path : false;
    }

    /**
     * Get a file from the file server/database
     *
     * @param string $id The id of the file to get
     * @return string|false The file object, patched with the file contents
     */
    public static function fetchContent(string $id)
    {
        $file = static::data($id);
        $path = Configure::read('FileApi.basePath') . $file->category . DS . $file->tag . DS . $file->filename;

        return file_exists($path) ? file_get_contents($path) : false;
    }

    /**
     * Returns the MIME type of the file
     *
     * @param string $id The file ID
     * @return string|false the mime type of the file
     */
    public static function fetchMime(string $id)
    {
        $file = static::data($id);
        $fileObj = new File(
            Configure::read('FileApi.basePath') . $file->category . DS . $file->tag . DS . $file->filename
        );

        return $fileObj->mime();
    }

    /**
     * Puts a file on the fileserver
     *
     * @param array|string $tmpFilePath The file path to the original file (usually a tmp_name from a file upload)
     * @param array $metaData The array of data associated with the file
     *
     * ### Required items in this metadata array:
     *  - category - the type/category for this file
     *  - tag - some sort of tag to organize this file in the category
     *  - original_filename - the original uploaded filename
     * @return string|bool the file id if successfully added, otherwise false
     */
    public static function put($tmpFilePath, array $metaData = [])
    {
        if (is_array($tmpFilePath)) {
            $tmpFile = new File($tmpFilePath['tmp_name']);
            $metaData['original_filename'] = $tmpFilePath['name'];
        } else {
            $tmpFile = new File($tmpFilePath);
        }

        if (!$tmpFile->exists()) {
            throw new StatusMessageException('file_api_missing_tmp_file');
        }

        if (!isset($metaData['category']) || !isset($metaData['tag'])) {
            throw new StatusMessageException('file_api_missing_metadata');
        }

        if (!isset($metaData['original_filename'])) {
            $metaData['original_filename'] = $tmpFile->name;
        }

        /** @var \Cake\ORM\Table|null $filesTable */
        $filesTable = static::_setupFilesTable();

        /** @var \ButterCream\Model\Entity\File $file */
        $file = $filesTable->newEntity([]);
        $file->category = $metaData['category'];
        $file->tag = $metaData['tag'];
        $file->size = $tmpFile->size();
        $file->original_filename = $metaData['original_filename'];
        if (isset($metaData['meta']) && is_array($metaData['meta'])) {
            $file->meta = $metaData['meta'];
        }
        /** @var array $pathInfo */
        $pathInfo = pathinfo($metaData['original_filename']);
        $file->filename = Text::uuid() . isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : null;

        $folder = new Folder(Configure::read('FileApi.basePath') . $file->category . DS . $file->tag, true, 0755);
        if (empty($folder->path) || (!empty($folder->path) && file_exists($folder->path) !== true)) {
            throw new StatusMessageException('file_api_missing_fileserver');
        }
        $destFile = new File($folder->path . DS . $file->filename);
        if (!$tmpFile->copy($destFile->path)) {
            throw new StatusMessageException('file_api_can_not_copy_file');
        }

        $tmpFile->delete();
        $tmpFile->close();
        $destFile->close();

        $filesTable->getEventManager()->off('Model.afterSave');
        if ($filesTable->save($file)) {
            return $file->id;
        }

        return false;
    }

    /**
     * Resizes a specified file id.  Options include width and height as integer values
     *
     * @param string $id The id of the File to modify
     * @param array $options The array of options for the resize
     * @return bool true if the image was properly resized, false otherwise
     */
    public static function resize(string $id, array $options = [])
    {
        $record = static::data($id);
        if (empty($record)) {
            return false;
        }

        // Create the file object from the file info
        $file = new File(
            Configure::read('FileApi.basePath') . $record->category . DS . $record->tag . DS . $record->filename
        );

        // Make sure the file exists, otherwise we're done!
        if (!$file->exists()) {
            throw new StatusMessageException('file_api_resize_missing_file');
        }

        // Get additional image data
        try {
            $imageInfo = getimagesize($file->path);
        } catch (Exception $e) {
            $imageInfo = [];
        }

        if ($imageInfo !== false && !in_array($imageInfo['mime'], self::$validImageMimeTypes)) {
            throw new StatusMessageException('file_api_resize_invalid_type');
        }

        // Make sure the Imagick class is available to use, otherwise just copy it.
        if (class_exists('Imagick')) {
            // Resize and Save Image
            $image = new Imagick($file->path);
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();

            $imgWidth = self::$defaultImageWidth; // Set default width
            $imgHeight = self::$defaultImageHeight; // Set default height

            // Override the width
            if (!empty($options['width'])) {
                $imgWidth = $options['width'];
            }

            // Override the height
            if (!empty($options['height'])) {
                $imgHeight = $options['height'];
            }

            $imgWidth = $imgWidth >= 0 ? $imgWidth : self::$defaultImageWidth;
            $imgHeight = $imgHeight >= 0 ? $imgHeight : self::$defaultImageHeight;

            // Keep image width in bounds and preserve aspect ratio
            if ($width > $imgWidth) {
                $image->scaleImage($imgWidth, $imgHeight, true);
            }

            // Keep image height in bounds and preserve aspect ratio
            if ($height > $imgHeight) {
                $image->scaleImage($imgWidth, $imgHeight, true);
            }

            $image->writeImage();
        } else {
            return false;
        }

        return true;
    }

    /**
     * Deletes a file from the file server / database
     *
     * @param string $id id of the file to delete
     * @return bool true if the file was deleted, false otherwise
     */
    public static function delete(string $id)
    {
        $filesTable = static::_setupFilesTable();
        $file = $filesTable->get($id);
        if ($filesTable->delete($file)) {
            return true;
        }

        return false;
    }
}
