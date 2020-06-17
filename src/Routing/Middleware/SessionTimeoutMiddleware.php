<?php
declare(strict_types=1);

namespace ButterCream\Routing\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Set and maintain as session variable `SessionTimeoutFilter.lastAccess` for determining
 * if the session should be extended. Usually, used with AJAX heavy applications or applications
 * where the user might be sitting on a page filling out a form for a long time. An Ajax ping to
 * extend the session can be sent.
 */
class SessionTimeoutMiddleware implements MiddlewareInterface
{

    /**
     * Default Config
     * @var [type]
     */
    protected $_config = [
        'timeout' => 15
    ];

    /**
     * Constructor
     *
     * @param array $config Config options. See $_config for valid keys.
     */
    public function __construct(array $config = [])
    {
        $this->_config = $config + $this->_config;
    }

    /**
     * Checks the session to see if it has timed out. If so destroy, otherwise set the lastAccess time for the session
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute('session');
        $lastAccess = $session->read('SessionTimeoutFilter.lastAccess');

        if ($lastAccess !== null && time() - $lastAccess > $this->_config['timeout'] * 60) {
            $session->destroy();
        }

        if ((!$request->is('ajax') || ($request->getQuery('session_timeout') && strtolower($request->getQuery('session_timeout')) === 'extend')) && $request->getParam('plugin') !== 'DebugKit') {
            $session->write('SessionTimeoutFilter.lastAccess', time());
        }

        return $handler->handle($request);
    }
}
