<?php
namespace ButterCream\Routing\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * TrustProxyMiddleware
 */
class TrustProxyMiddleware
{
    /**
     * @param ServerRequestInterface $request  The request.
     * @param ResponseInterface $response The response.
     * @param callable $next The next middleware to call.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $request->trustProxy = true;

        return $next($request, $response);
    }
}
