<?php
declare(strict_types=1);

namespace ButterCream\Message;

/**
 * Status Message Class - Library of application status messages.
 *
 * Provides a centralized registry of status messages with codes, HTTP status,
 * and types for consistent error/success messaging across the application.
 */
class StatusMessage
{
    /**
     * Application Status Messages
     *
     * @var array<string, array{status: int, responseText: string, code: string, type: string}>
     */
    protected static array $messages = [
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
        'file_service_missing_tmp_file' => [
            'status' => 500,
            'responseText' => 'Missing or corrupt "tmp" file.',
            'code' => 'FILESERVICE-1',
            'type' => 'error',
        ],
        'file_service_missing_metadata' => [
            'status' => 500,
            'responseText' => 'Missing required Meta Data: Model and Foreign Key are required.',
            'code' => 'FILESERVICE-2',
            'type' => 'error',
        ],
        'file_service_missing_fileserver' => [
            'status' => 500,
            'responseText' => 'Problem creating or locating uploads directory.',
            'code' => 'FILESERVICE-3',
            'type' => 'error',
        ],
        'file_service_can_not_copy_file' => [
            'status' => 500,
            'responseText' => 'Unable to copy or save tmp file to final destination.',
            'code' => 'FILESERVICE-4',
            'type' => 'error',
        ],
        'file_service_resize_missing_file' => [
            'status' => 500,
            'responseText' => 'Missing or corrupt file, the resizing can not be completed.',
            'code' => 'FILESERVICE-5',
            'type' => 'error',
        ],
        'file_service_resize_invalid_type' => [
            'status' => 500,
            'responseText' => 'The selected file is not an image, therefore can not be resized.',
            'code' => 'FILESERVICE-6',
            'type' => 'error',
        ],
    ];

    /**
     * Get all of the messages
     *
     * @return array<string, array{status: int, responseText: string, code: string, type: string}>
     */
    public static function getMessages(): array
    {
        return static::$messages;
    }

    /**
     * Get a message by key
     *
     * @param string $key The array key for the message
     * @return array{status: int, responseText: string, code: string, type: string}|false The message array or false if not found
     */
    public static function getMessage(string $key): array|false
    {
        return static::$messages[$key] ?? false;
    }

    /**
     * Get a message's status
     *
     * @param string $key The array key for the message
     * @return int|false The HTTP status code or false if not found
     */
    public static function getStatus(string $key): int|false
    {
        return static::$messages[$key]['status'] ?? false;
    }

    /**
     * Get Response Text
     *
     * @param string $key The array key for the message
     * @return string|false The message response text or false if not found
     */
    public static function getResponseText(string $key): string|false
    {
        return static::$messages[$key]['responseText'] ?? false;
    }

    /**
     * Get Code
     *
     * @param string $key The array key for the message
     * @return string|false The message code or false if not found
     */
    public static function getCode(string $key): string|false
    {
        return static::$messages[$key]['code'] ?? false;
    }

    /**
     * Get type
     *
     * @param string $key The array key for the message
     * @return string|false The message type or false if not found
     */
    public static function getType(string $key): string|false
    {
        return static::$messages[$key]['type'] ?? false;
    }

    /**
     * To String - Message/Type/Code
     *
     * @param string $key The array key for the message
     * @return string The message string with type and code
     */
    public static function toString(string $key): string
    {
        $code = static::$messages[$key]['code'] ?? '';
        $message = static::$messages[$key]['responseText'] ?? '';
        $type = static::$messages[$key]['type'] ?? 'notice';

        return trim($message . ' ' . strtoupper($type) . ': ' . $code);
    }
}
