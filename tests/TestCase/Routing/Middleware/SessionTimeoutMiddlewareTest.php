<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Routing\Middleware;

use ButterCream\Routing\Middleware\SessionTimeoutMiddleware;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use Laminas\Diactoros\Uri;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * ButterCream\Routing\Middleware\SessionTimeoutMiddleware Test Case
 */
class SessionTimeoutMiddlewareTest extends TestCase
{
    /**
     * Test middleware with default config
     *
     * @return void
     */
    public function testMiddlewareDefaultConfig(): void
    {
        $middleware = new SessionTimeoutMiddleware();

        $this->assertInstanceOf(SessionTimeoutMiddleware::class, $middleware);
    }

    /**
     * Test middleware with custom timeout
     *
     * @return void
     */
    public function testMiddlewareCustomTimeout(): void
    {
        $middleware = new SessionTimeoutMiddleware(['timeout' => 30]);

        $this->assertInstanceOf(SessionTimeoutMiddleware::class, $middleware);
    }

    /**
     * Test process sets lastAccess on non-AJAX request
     *
     * @return void
     */
    public function testProcessSetsLastAccessOnNonAjax(): void
    {
        $middleware = new SessionTimeoutMiddleware(['timeout' => 15]);

        $session = new Session();
        $request = new ServerRequest([
            'url' => '/articles/index',
            'session' => $session,
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response(['type' => 'text/html', 'charset' => 'UTF-8']));

        $middleware->process($request, $handler);

        $lastAccess = $session->read('SessionTimeoutFilter.lastAccess');
        $this->assertNotNull($lastAccess);
        $this->assertIsInt($lastAccess);
    }

    /**
     * Test process does not set lastAccess on AJAX request by default
     *
     * @return void
     */
    public function testProcessDoesNotSetLastAccessOnAjax(): void
    {
        $middleware = new SessionTimeoutMiddleware(['timeout' => 15]);

        $session = new Session();
        $request = new ServerRequest([
            'url' => '/articles/index',
            'session' => $session,
            'environment' => [
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            ],
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response(['type' => 'text/html', 'charset' => 'UTF-8']));

        $middleware->process($request, $handler);

        $lastAccess = $session->read('SessionTimeoutFilter.lastAccess');
        $this->assertNull($lastAccess);
    }

    /**
     * Test process sets lastAccess on AJAX request with session_timeout=extend
     *
     * @return void
     */
    public function testProcessSetsLastAccessOnAjaxWithExtend(): void
    {
        $middleware = new SessionTimeoutMiddleware(['timeout' => 15]);

        $session = new Session();
        $request = new ServerRequest([
            'url' => '/articles/index',
            'session' => $session,
            'query' => ['session_timeout' => 'extend'],
            'environment' => [
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            ],
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response(['type' => 'text/html', 'charset' => 'UTF-8']));

        $middleware->process($request, $handler);

        $lastAccess = $session->read('SessionTimeoutFilter.lastAccess');
        $this->assertNotNull($lastAccess);
    }

    /**
     * Test process destroys expired session
     *
     * @return void
     */
    public function testProcessDestroysExpiredSession(): void
    {
        $middleware = new SessionTimeoutMiddleware(['timeout' => 1]); // 1 minute timeout

        $session = new Session();
        // Set last access to 2 minutes ago (expired)
        $session->write('SessionTimeoutFilter.lastAccess', time() - 120);
        $session->write('test.data', 'value');

        $request = new ServerRequest([
            'url' => '/articles/index',
            'session' => $session,
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response(['type' => 'text/html', 'charset' => 'UTF-8']));

        $middleware->process($request, $handler);

        // Session should be destroyed
        $testData = $session->read('test.data');
        $this->assertNull($testData);
    }

    /**
     * Test process does not destroy valid session
     *
     * @return void
     */
    public function testProcessDoesNotDestroyValidSession(): void
    {
        $middleware = new SessionTimeoutMiddleware(['timeout' => 15]);

        $session = new Session();
        // Set last access to 5 minutes ago (still valid)
        $session->write('SessionTimeoutFilter.lastAccess', time() - 300);
        $session->write('test.data', 'value');

        $request = new ServerRequest([
            'url' => '/articles/index',
            'session' => $session,
        ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response(['type' => 'text/html', 'charset' => 'UTF-8']));

        $middleware->process($request, $handler);

        // Session should still have data
        $testData = $session->read('test.data');
        $this->assertEquals('value', $testData);
    }
}
