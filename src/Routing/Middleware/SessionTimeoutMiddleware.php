<?php
namespace ButterCream\Routing\Middleware;

use Cake\Core\Configure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Sets the runtime default locale for the request based on the
 * Accept-Language header. The default will only be set if it
 * matches the list of passed valid locales.
 */
class SessionTimeoutMiddleware
{
    /**
     * @param ServerRequestInterface $request  The request.
     * @param ResponseInterface $response The response.
     * @param callable $next The next middleware to call.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $session = $request->getAttribute('session');
        $lastAccess = $session->read('SessionTimeoutFilter.lastAccess');

        if ($lastAccess !== null && time() - $lastAccess > Configure::read('Session.timeout') * 60) {
            $session->destroy();
        }

        if ((!$request->is('ajax') || ($request->getQuery('session_timeout') && strtolower($request->getQuery('session_timeout')) === 'extend')) && $request->getParam('plugin') !== 'DebugKit') {
            $session->write('SessionTimeoutFilter.lastAccess', time());
        }

        return $next($request, $response);
    }
}
