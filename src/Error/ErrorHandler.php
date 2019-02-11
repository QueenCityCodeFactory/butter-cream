<?php
namespace ButterCream\Error;

use Cake\Error\ErrorHandler as CakeErrorHandler;

class ErrorHandler extends CakeErrorHandler
{

    /**
     * Get the request context for an error/exception trace.
     *
     * @param \Cake\Http\ServerRequest $request The request to read from.
     * @return string
     */
    protected function _requestContext($request)
    {
        $message = "\nRequest URL: " . $request->getRequestTarget();

        $referer = $request->getEnv('HTTP_REFERER');
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

        return $message;
    }
}
