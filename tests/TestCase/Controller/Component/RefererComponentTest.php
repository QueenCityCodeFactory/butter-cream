<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Controller\Component;

use ButterCream\Controller\Component\RefererComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Controller\Component\RefererComponent Test Case
 */
class RefererComponentTest extends TestCase
{
    /**
     * @var \ButterCream\Controller\Component\RefererComponent
     */
    protected RefererComponent $Referer;

    /**
     * @var \Cake\Controller\Controller
     */
    protected Controller $controller;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Router::reload();
        Router::setRequest(new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'localhost',
                'REQUEST_URI' => '/articles/view/1',
                'HTTP_REFERER' => 'http://localhost/articles/index',
            ],
        ]));
        $request = new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'localhost',
                'HTTP_REFERER' => 'http://localhost/articles/index',
                'REQUEST_URI' => '/articles/view/1',
            ],
        ]);
        $this->controller = new Controller($request);
        $registry = new ComponentRegistry($this->controller);
        $this->Referer = new RefererComponent($registry);
    }

    /**
     * Test normalizeUrl strips host when matching
     *
     * @return void
     */
    public function testNormalizeUrlStripsHost(): void
    {
        $result = $this->Referer->normalizeUrl('http://localhost/articles/index');
        $this->assertEquals('/articles/index', $result);
    }

    /**
     * Test normalizeUrl returns null for null input
     *
     * @return void
     */
    public function testNormalizeUrlNull(): void
    {
        $this->assertNull($this->Referer->normalizeUrl(null));
    }

    /**
     * Test normalizeUrl preserves external URLs
     *
     * @return void
     */
    public function testNormalizeUrlPreservesExternal(): void
    {
        $url = 'https://example.com/page';
        $result = $this->Referer->normalizeUrl($url);
        $this->assertEquals($url, $result);
    }

    /**
     * Test normalizeUrl preserves query string
     *
     * @return void
     */
    public function testNormalizeUrlPreservesQueryString(): void
    {
        $result = $this->Referer->normalizeUrl('http://localhost/articles?page=2&sort=name');
        $this->assertEquals('/articles?page=2&sort=name', $result);
    }

    /**
     * Test getReferer returns normalized referer
     *
     * @return void
     */
    public function testGetReferer(): void
    {
        $result = $this->Referer->getReferer();
        $this->assertEquals('/articles/index', $result);
    }

    /**
     * Test getReferer with form data override
     *
     * @return void
     */
    public function testGetRefererFromPostData(): void
    {
        $request = new ServerRequest([
            'post' => ['Referer' => ['url' => '/custom/referer']],
            'environment' => [
                'HTTP_HOST' => 'localhost',
                'HTTP_REFERER' => 'http://localhost/ignored',
                'REQUEST_URI' => '/',
            ],
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $referer = new RefererComponent($registry);

        $this->assertEquals('/custom/referer', $referer->getReferer());
    }

    /**
     * Test ignore adds URL to ignore list
     *
     * @return void
     */
    public function testIgnore(): void
    {
        $this->Referer->ignore('/articles/delete');
        $ignored = $this->Referer->getConfig('ignored');
        $this->assertContains('/articles/delete', $ignored);
    }

    /**
     * Test isMatch returns true for matching URLs
     *
     * @return void
     */
    public function testIsMatchTrue(): void
    {
        $this->assertTrue($this->Referer->isMatch('http://localhost/articles/index'));
    }

    /**
     * Test isMatch returns false for non-matching URLs
     *
     * @return void
     */
    public function testIsMatchFalse(): void
    {
        $this->assertFalse($this->Referer->isMatch('/different/url'));
    }

    /**
     * Test redirect uses referer when not ignored
     *
     * @return void
     */
    public function testRedirectUsesReferer(): void
    {
        $response = $this->Referer->redirect('/fallback');
        $this->assertNotNull($response);
        $this->assertStringEndsWith('/articles/index', $response->getHeaderLine('Location'));
    }

    /**
     * Test redirect uses fallback when referer is ignored
     *
     * @return void
     */
    public function testRedirectUsesFallbackWhenIgnored(): void
    {
        $this->Referer->ignore('/articles/index');
        $response = $this->Referer->redirect('/fallback');
        $this->assertNotNull($response);
        $this->assertStringEndsWith('/fallback', $response->getHeaderLine('Location'));
    }

    /**
     * Test redirect uses fallback when referer is root
     *
     * @return void
     */
    public function testRedirectUsesFallbackWhenRoot(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'localhost',
                'HTTP_REFERER' => 'http://localhost/',
                'REQUEST_URI' => '/',
            ],
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $referer = new RefererComponent($registry);

        $response = $referer->redirect('/fallback');
        $this->assertNotNull($response);
        $this->assertStringEndsWith('/fallback', $response->getHeaderLine('Location'));
    }

    /**
     * Test redirect with custom status code
     *
     * @return void
     */
    public function testRedirectWithCustomStatus(): void
    {
        $response = $this->Referer->redirect('/fallback', 301);
        $this->assertNotNull($response);
        $this->assertEquals(301, $response->getStatusCode());
    }

    /**
     * Test default config
     *
     * @return void
     */
    public function testDefaultConfig(): void
    {
        $this->assertEquals([], $this->Referer->getConfig('ignored'));
    }
}
