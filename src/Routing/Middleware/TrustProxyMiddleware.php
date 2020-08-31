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
     * Trust Proxy
     *
     * @var bool
     */
    public $trust = true;

    /**
     * Constructor
     *
     * @param bool $trust True or False
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
        // @phpstan-ignore-next-line
        $request->trustProxy = $this->trust;

        return $handler->handle($request);
    }
}
