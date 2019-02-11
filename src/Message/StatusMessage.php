<?php
namespace ButterCream\Message;

use Cake\Core\Configure;

/**
 * Status Message Class - Library of application status messages.
 */
class StatusMessage
{

    /**
     * Application Status Messages
     *
     * @var array
     */
    protected static $_messages = [
        'missing_field' => [
            'status' => 400,
            'responseText' => 'Missing Field!',
            'code' => 'A100',
            'type' => 'error',
        ],
        'missing_entity' => [
            'status' => 500,
            'responseText' => 'Missing Entity!',
            'code' => 'A101',
            'type' => 'error',
        ],
        'missing_user' => [
            'status' => 500,
            'responseText' => 'Missing User!',
            'code' => 'A105',
            'type' => 'error',
        ],
        'unauthorized' => [
            'status' => 401,
            'responseText' => 'Unauthorized Access!',
            'code' => 'B100',
            'type' => 'error',
        ],
        'invalid_record' => [
            'status' => 500,
            'responseText' => 'Invalid Record!',
            'code' => 'B101',
            'type' => 'error',
        ],
        'file_api_missing_tmp_file' => [
            'status' => 500,
            'responseText' => 'Missing or corrupt "tmp" file.',
            'code' => 'FILEAPI-1',
            'type' => 'error'
        ],
        'file_api_missing_metadata' => [
            'status' => 500,
            'responseText' => 'Missing required Meta Data: Category and Tag are required.',
            'code' => 'FILEAPI-2',
            'type' => 'error'
        ],
        'file_api_missing_fileserver' => [
            'status' => 500,
            'responseText' => 'Problem creating or locating uploads directory.',
            'code' => 'FILEAPI-3',
            'type' => 'error'
        ],
        'file_api_can_not_copy_file' => [
            'status' => 500,
            'responseText' => 'Unable to copy or save tmp file to final destination.',
            'code' => 'FILEAPI-4',
            'type' => 'error'
        ],
        'file_api_resize_missing_file' => [
            'status' => 500,
            'responseText' => 'Missing or corrupt file, the resizing can not be completed.',
            'code' => 'FILEAPI-5',
            'type' => 'error'
        ],
        'file_api_resize_invalid_type' => [
            'status' => 500,
            'responseText' => 'The selected file is not an image, therefore can not be resized.',
            'code' => 'FILEAPI-6',
            'type' => 'error'
        ],
    ];

    /**
     * [getMessages description]
     * @return [type] [description]
     */
    public static function getMessages()
    {
        return isset(static::$_messages) ? static::$_messages : false;
    }

    /**
     * [getMessage description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function getMessage($key)
    {
        return isset(static::$_messages[$key]) ? static::$_messages[$key] : false;
    }

    /**
     * [getStatus description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function getStatus($key)
    {
        return isset(static::$_messages[$key]['status']) ? static::$_messages[$key]['status'] : false;
    }

    /**
     * [getResponseText description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function getResponseText($key)
    {
        return isset(static::$_messages[$key]['responseText']) ? static::$_messages[$key]['responseText'] : false;
    }

    /**
     * [getCode description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function getCode($key)
    {
        return isset(static::$_messages[$key]['code']) ? static::$_messages[$key]['code'] : false;
    }

    /**
     * [getType description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function getType($key)
    {
        return isset(static::$_messages[$key]['type']) ? static::$_messages[$key]['type'] : false;
    }

    /**
     * [getCode description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function toString($key)
    {
        $code = isset(static::$_messages[$key]['code']) ? static::$_messages[$key]['code'] : '';
        $message = isset(static::$_messages[$key]['responseText']) ? static::$_messages[$key]['responseText'] : '';
        $type = isset(static::$_messages[$key]['type']) ? static::$_messages[$key]['type'] : 'notice';

        return trim($message . ' ' . strtoupper($type) . ': ' . $code);
    }
}
