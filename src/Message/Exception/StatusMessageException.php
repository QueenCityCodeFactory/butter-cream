<?php
declare(strict_types=1);

namespace ButterCream\Message\Exception;

use ButterCream\Message\StatusMessage;
use Cake\Http\Exception\HttpException;

/**
 * Represents an HTTP 400 error
 */
class StatusMessageException extends HttpException
{

    /**
     * Constructor
     *
     * @param string $key The Status Message Key
     */
    public function __construct($key = null)
    {
        $message = StatusMessage::getResponseText($key);
        $code = StatusMessage::getStatus($key);

        if ($message === false) {
            $message = 'Whoops! It looks like an invalid `$key` was provided';
        }
        if ($code === false || $code === null) {
            $code = 500;
        }

        parent::__construct($message, $code);
    }
}
