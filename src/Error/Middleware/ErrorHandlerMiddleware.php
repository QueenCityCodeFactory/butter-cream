<?php
namespace ButterCream\Error\Middleware;

use Cake\Core\Configure;
use Cake\Core\Exception\Exception as CakeException;
use Cake\Error\Middleware\ErrorHandlerMiddleware as CakeErrorHandlerMiddleware;

class ErrorHandlerMiddleware extends CakeErrorHandlerMiddleware
{

    /**
     * Generate the error log message.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The current request.
     * @param \Exception $exception The exception to log a message for.
     * @return string Error message
     */
    protected function getMessage($request, $exception)
    {
        $message = sprintf(
            '[%s] %s',
            get_class($exception),
            $exception->getMessage()
        );
        $debug = Configure::read('debug');

        if ($debug && $exception instanceof CakeException) {
            $attributes = $exception->getAttributes();
            if ($attributes) {
                $message .= "\nException Attributes: " . var_export($exception->getAttributes(), true);
            }
        }
        $message .= "\nRequest URL: " . $request->getRequestTarget();
        $referer = $request->getHeaderLine('Referer');
        if ($referer) {
            $message .= "\nReferer URL: " . $referer;
        }

        $userAgent = $request->getEnv('HTTP_USER_AGENT');
        if ($userAgent) {
            $message .= "\nUserAgent: " . $userAgent;
        }
        $clientIp = $request->clientIp();
        if ($clientIp && $clientIp !== '::1') {
            $message .= "\nUser's IP: " . $clientIp;
        }

        $session = $request->session()->read();
        if (isset($session['Auth']['User']['id'])) {
            $message .= "\nUser's ID: " . $session['Auth']['User']['id'];
        }
        if (isset($session['Auth']['User']['username'])) {
            $message .= "\nUser's Username: " . $session['Auth']['User']['username'];
        }
        if (isset($session['Auth']['User']['security_group']['name'])) {
            $message .= "\nUser's Security Group: " . $session['Auth']['User']['security_group']['name'];
        }
        if (isset($session['Client']->id)) {
            $message .= "\nActive Client ID: " . $session['Client']->id;
        }

        if ($this->getConfig('trace')) {
            $message .= "\nStack Trace:\n" . $exception->getTraceAsString() . "\n\n";
        }

        return $message;
    }
}
