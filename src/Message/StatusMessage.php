<?php
declare(strict_types=1);

namespace ButterCream\Message;

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
            'type' => 'error',
        ],
        'file_api_missing_metadata' => [
            'status' => 500,
            'responseText' => 'Missing required Meta Data: Category and Tag are required.',
            'code' => 'FILEAPI-2',
            'type' => 'error',
        ],
        'file_api_missing_fileserver' => [
            'status' => 500,
            'responseText' => 'Problem creating or locating uploads directory.',
            'code' => 'FILEAPI-3',
            'type' => 'error',
        ],
        'file_api_can_not_copy_file' => [
            'status' => 500,
            'responseText' => 'Unable to copy or save tmp file to final destination.',
            'code' => 'FILEAPI-4',
            'type' => 'error',
        ],
        'file_api_resize_missing_file' => [
            'status' => 500,
            'responseText' => 'Missing or corrupt file, the resizing can not be completed.',
            'code' => 'FILEAPI-5',
            'type' => 'error',
        ],
        'file_api_resize_invalid_type' => [
            'status' => 500,
            'responseText' => 'The selected file is not an image, therefore can not be resized.',
            'code' => 'FILEAPI-6',
            'type' => 'error',
        ],
    ];

    /**
     * Get all of the messages
     *
     * @return mixed The all the messages or false if no messages
     */
    public static function getMessages()
    {
        return static::$_messages ?? false;
    }

    /**
     * Get a messages by key
     *
     * @param string $key The array key for the message
     * @return mixed The message array or false if not found
     */
    public static function getMessage(string $key)
    {
        return static::$_messages[$key] ?? false;
    }

    /**
     * Get a message's status
     *
     * @param string $key The array key for the message
     * @return mixed The message status or false if not found
     */
    public static function getStatus(string $key)
    {
        return static::$_messages[$key]['status'] ?? false;
    }

    /**
     * Get Response Text
     *
     * @param string $key The array key for the message
     * @return mixed The message response text or false if not found
     */
    public static function getResponseText(string $key)
    {
        return static::$_messages[$key]['responseText'] ?? false;
    }

    /**
     * Get Code
     *
     * @param string $key The array key for the message
     * @return mixed The message code or false if not found
     */
    public static function getCode(string $key)
    {
        return static::$_messages[$key]['code'] ?? false;
    }

    /**
     * Get type
     *
     * @param string $key The array key for the message
     * @return mixed The message type or false if not found
     */
    public static function getType(string $key)
    {
        return static::$_messages[$key]['type'] ?? false;
    }

    /**
     * To Sting - Message/Type/Code
     *
     * @param string $key The array key for the message
     * @return mixed The message string with type and code or false if not found
     */
    public static function toString(string $key)
    {
        $code = static::$_messages[$key]['code'] ?? '';
        $message = static::$_messages[$key]['responseText'] ?? '';
        $type = static::$_messages[$key]['type'] ?? 'notice';

        return trim($message . ' ' . strtoupper($type) . ': ' . $code);
    }
}
