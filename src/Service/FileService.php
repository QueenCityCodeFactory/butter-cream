<?php
declare(strict_types=1);

namespace ButterCream\Service;

use ButterCream\Message\Exception\StatusMessageException;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\Utility\Text;
use Exception;
use Imagick;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnableToWriteFile;

/**
 * File Service
 *
 * Manages file storage, retrieval, and manipulation using Flysystem.
 * Uses a model/foreign_key pattern to associate files with any table record.
 */
class FileService
{
    use LocatorAwareTrait;

    /**
     * The Table object for the files database table
     *
     * @var \Cake\ORM\Table
     */
    protected Table $filesTable;

    /**
     * A default image width if none is specified for resizing
     *
     * @var int
     */
    public int $defaultImageWidth = 300;

    /**
     * A default image height if none is specified for resizing
     *
     * @var int
     */
    public int $defaultImageHeight = 300;

    /**
     * Allowed Mime Types for image processing & conversion
     *
     * @var array<string> mime types
     */
    public array $validImageMimeTypes = [
        'image/gif',
        'image/jpeg',
        'image/png',
    ];

    /**
     * Constructor
     *
     * @param string $tableName The table alias to use for file records (default: 'Files')
     */
    public function __construct(string $tableName = 'Files')
    {
        $this->filesTable = $this->fetchTable($tableName);
    }

    /**
     * Get the files table instance.
     *
     * @return \Cake\ORM\Table
     */
    public function getFilesTable(): Table
    {
        return $this->filesTable;
    }

    /**
     * Get the base path for file storage.
     *
     * @return string
     */
    protected function getBasePath(): string
    {
        return (string)Configure::read('FileService.basePath');
    }

    /**
     * Build the relative filesystem path for a file entity.
     *
     * @param \Cake\Datasource\EntityInterface $file The file entity
     * @return string
     */
    protected function buildRelativePath(EntityInterface $file): string
    {
        return $file->model . DS . $file->foreign_key . DS . $file->filename;
    }

    /**
     * Get a file with data from the database, optionally including file contents.
     *
     * @param int|string $id The id of the file to get
     * @param bool $contents Whether to include the file contents
     * @return \Cake\Datasource\EntityInterface The file entity
     */
    public function get(int|string $id, bool $contents = false): EntityInterface
    {
        $file = $this->data($id);
        $file->path = $this->getBasePath() . $this->buildRelativePath($file);
        if ($contents !== false) {
            $file->contents = file_get_contents(
                $this->getBasePath() . $this->buildRelativePath($file),
            );
        }

        return $file;
    }

    /**
     * Get the base file data from the database.
     *
     * @param int|string $id The id of the file
     * @return \Cake\Datasource\EntityInterface
     */
    public function data(int|string $id): EntityInterface
    {
        /** @var \ButterCream\Model\Entity\File $file */
        $file = $this->filesTable->get($id);
        if (!empty($file) && is_object($file)) {
            return $file;
        }
        throw new NotFoundException('The requested file could not be found.');
    }

    /**
     * Gets the local file path to a file.
     *
     * @param int|string $id The id of the file
     * @return string|false The local file path, or false if not found
     */
    public function getLocalPath(int|string $id): string|false
    {
        $file = $this->data($id);
        $path = $this->getBasePath() . $this->buildRelativePath($file);

        return file_exists($path) ? $path : false;
    }

    /**
     * Fetch the contents of a file.
     *
     * @param int|string $id The id of the file
     * @return string|false The file contents, or false if not found
     */
    public function fetchContent(int|string $id): string|false
    {
        $file = $this->data($id);
        $path = $this->getBasePath() . $this->buildRelativePath($file);

        return file_exists($path) ? file_get_contents($path) : false;
    }

    /**
     * Returns the MIME type of the file.
     *
     * @param int|string $id The file ID
     * @return string|false The mime type, or false if not found
     */
    public function fetchMime(int|string $id): string|false
    {
        $file = $this->data($id);
        $filePath = $this->buildRelativePath($file);

        $adapter = new LocalFilesystemAdapter($this->getBasePath());
        $filesystem = new Filesystem($adapter);

        if ($filesystem->fileExists($filePath)) {
            return $filesystem->mimeType($filePath);
        }

        return false;
    }

    /**
     * Store a file and create a database record.
     *
     * @param array|string $tmpFilePath The file path to the original file (usually a tmp_name from a file upload)
     * @param array $metaData The metadata for the file
     *
     * ### Required items in this metadata array:
     *  - model - the model/table name this file belongs to
     *  - foreign_key - the foreign key of the related record
     *  - original_filename - the original uploaded filename
     * @return int|string|bool The file id if successfully added, otherwise false
     */
    public function put(array|string $tmpFilePath, array $metaData = []): int|string|bool
    {
        $adapter = new LocalFilesystemAdapter($this->getBasePath());
        $filesystem = new Filesystem($adapter);

        $originalFilename = $metaData['original_filename'] ?? null;

        if (is_array($tmpFilePath)) {
            $path = $tmpFilePath['tmp_name'];
            $originalFilename = $tmpFilePath['name'];
        } else {
            $path = $tmpFilePath;
        }

        if (!$filesystem->fileExists($path)) {
            throw new StatusMessageException('file_service_missing_tmp_file');
        }

        if (!isset($metaData['model']) || !isset($metaData['foreign_key'])) {
            throw new StatusMessageException('file_service_missing_metadata');
        }

        if (!$originalFilename) {
            $originalFilename = basename($path);
        }

        /** @var \ButterCream\Model\Entity\File $file */
        $file = $this->filesTable->newEmptyEntity();
        $file->uuid = Text::uuid();
        $file->model = $metaData['model'];
        $file->foreign_key = $metaData['foreign_key'];
        $file->size = $filesystem->fileSize($path);
        $file->original_filename = $originalFilename;
        $file->meta = $metaData['meta'] ?? null;

        /** @var array $pathInfo */
        $pathInfo = pathinfo((string)$originalFilename);
        $file->filename = Text::uuid() . (isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '');

        $targetPath = $this->buildRelativePath($file);

        try {
            $filesystem->copy($path, $targetPath);
            $filesystem->delete($path);
        } catch (UnableToWriteFile $exception) {
            throw new StatusMessageException('file_service_can_not_copy_file');
        }

        $eventManager = $this->filesTable->getEventManager();
        foreach ($eventManager->listeners('Model.afterSave') as $listener) {
            $eventManager->off('Model.afterSave', $listener['callable']);
        }

        if ($this->filesTable->save($file)) {
            return $file->id;
        }

        return false;
    }

    /**
     * Resizes a specified file. Options include width and height as integer values.
     *
     * @param int|string $id The id of the file to modify
     * @param array $options The array of options for the resize
     * @return bool True if the image was properly resized, false otherwise
     */
    public function resize(int|string $id, array $options = []): bool
    {
        $record = $this->data($id);
        if (empty($record)) {
            return false;
        }

        $filePath = $this->getBasePath() . $this->buildRelativePath($record);

        // Make sure the file exists, otherwise we're done!
        if (!file_exists($filePath)) {
            throw new StatusMessageException('file_service_resize_missing_file');
        }

        // Get additional image data
        try {
            $imageInfo = getimagesize($filePath);
        } catch (Exception) {
            $imageInfo = [];
        }

        if ($imageInfo !== false && !in_array($imageInfo['mime'], $this->validImageMimeTypes)) {
            throw new StatusMessageException('file_service_resize_invalid_type');
        }

        // Make sure the Imagick class is available to use, otherwise just copy it.
        if (class_exists('Imagick')) {
            /** @var \Imagick $image */
            $image = new Imagick($filePath);
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();

            $imgWidth = $this->defaultImageWidth;
            $imgHeight = $this->defaultImageHeight;

            if (!empty($options['width'])) {
                $imgWidth = $options['width'];
            }

            if (!empty($options['height'])) {
                $imgHeight = $options['height'];
            }

            $imgWidth = $imgWidth >= 0 ? $imgWidth : $this->defaultImageWidth;
            $imgHeight = $imgHeight >= 0 ? $imgHeight : $this->defaultImageHeight;

            if ($width > $imgWidth) {
                $image->scaleImage($imgWidth, $imgHeight, true);
            }

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
     * Deletes a file from the filesystem and database.
     *
     * @param int|string $id The id of the file to delete
     * @return bool True if the file was deleted, false otherwise
     */
    public function delete(int|string $id): bool
    {
        $file = $this->filesTable->get($id);
        if ($this->filesTable->delete($file)) {
            return true;
        }

        return false;
    }
}
