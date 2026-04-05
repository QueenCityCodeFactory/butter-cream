<?php
declare(strict_types=1);

namespace ButterCream\Filesystem;

use ButterCream\Model\Entity\File;
use ButterCream\Service\FileService;

/**
 * File API
 *
 * @deprecated Use \ButterCream\Service\FileService instead.
 */
class FileApi
{
    /**
     * @var \ButterCream\Service\FileService|null
     */
    protected static ?FileService $service = null;

    /**
     * Get the FileService singleton instance.
     *
     * @return \ButterCream\Service\FileService
     */
    protected static function getService(): FileService
    {
        if (!isset(static::$service)) {
            static::$service = new FileService();
        }

        return static::$service;
    }

    /**
     * Get a file data from the file server/database
     *
     * @param int|string $id The id of the file to get
     * @param bool $contents Do you want the file content?
     * @return \ButterCream\Model\Entity\File
     * @deprecated Use FileService::get() instead.
     */
    public static function get(int|string $id, bool $contents = false): File
    {
        trigger_error('FileApi::get() is deprecated. Use FileService::get() instead.', E_USER_DEPRECATED);

        return static::getService()->get($id, $contents);
    }

    /**
     * Get the Base file data
     *
     * @param int|string $id The id of the file
     * @return \ButterCream\Model\Entity\File
     * @deprecated Use FileService::data() instead.
     */
    public static function data(int|string $id): File
    {
        trigger_error('FileApi::data() is deprecated. Use FileService::data() instead.', E_USER_DEPRECATED);

        return static::getService()->data($id);
    }

    /**
     * Gets the Local File Path to a File
     *
     * @param int|string $id The id of the file
     * @return string|false
     * @deprecated Use FileService::getLocalPath() instead.
     */
    public static function getLocalPath(int|string $id): string|false
    {
        trigger_error('FileApi::getLocalPath() is deprecated. Use FileService::getLocalPath() instead.', E_USER_DEPRECATED);

        return static::getService()->getLocalPath($id);
    }

    /**
     * Get file contents
     *
     * @param int|string $id The id of the file
     * @return string|false
     * @deprecated Use FileService::fetchContent() instead.
     */
    public static function fetchContent(int|string $id): string|false
    {
        trigger_error('FileApi::fetchContent() is deprecated. Use FileService::fetchContent() instead.', E_USER_DEPRECATED);

        return static::getService()->fetchContent($id);
    }

    /**
     * Returns the MIME type of the file
     *
     * @param int|string $id The file ID
     * @return string|false
     * @deprecated Use FileService::fetchMime() instead.
     */
    public static function fetchMime(int|string $id): string|false
    {
        trigger_error('FileApi::fetchMime() is deprecated. Use FileService::fetchMime() instead.', E_USER_DEPRECATED);

        return static::getService()->fetchMime($id);
    }

    /**
     * Puts a file on the fileserver
     *
     * @param array|string $tmpFilePath The file path
     * @param array $metaData The metadata array (use 'model' and 'foreign_key' instead of 'category' and 'tag')
     * @return int|string|bool
     * @deprecated Use FileService::put() instead.
     */
    public static function put(array|string $tmpFilePath, array $metaData = []): int|string|bool
    {
        trigger_error('FileApi::put() is deprecated. Use FileService::put() instead.', E_USER_DEPRECATED);

        return static::getService()->put($tmpFilePath, $metaData);
    }

    /**
     * Resizes a specified file id
     *
     * @param int|string $id The id of the File to modify
     * @param array $options The array of options for the resize
     * @return bool
     * @deprecated Use FileService::resize() instead.
     */
    public static function resize(int|string $id, array $options = []): bool
    {
        trigger_error('FileApi::resize() is deprecated. Use FileService::resize() instead.', E_USER_DEPRECATED);

        return static::getService()->resize($id, $options);
    }

    /**
     * Deletes a file
     *
     * @param int|string $id id of the file to delete
     * @return bool
     * @deprecated Use FileService::delete() instead.
     */
    public static function delete(int|string $id): bool
    {
        trigger_error('FileApi::delete() is deprecated. Use FileService::delete() instead.', E_USER_DEPRECATED);

        return static::getService()->delete($id);
    }
}
