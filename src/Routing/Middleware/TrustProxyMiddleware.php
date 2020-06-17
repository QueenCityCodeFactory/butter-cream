<?php
declare(strict_types=1);

namespace ButterCream\Routing\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TrustProxyMiddleware
 */
class TrustProxyMiddleware implements MiddlewareInterface
{

    /**
     * Constructor
     *
     * @param array $config Config options. See $_config for valid keys.
     */
    public function __construct(bool $trust = true)
    {
        $this->trust = $trust;
    }

    /**
     * Set the TrustProxy to true by default
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request->trustProxy = $this->trust;

        return $handler->handle($request);
    }
}
