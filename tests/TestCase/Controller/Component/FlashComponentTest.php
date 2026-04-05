<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Controller\Component;

use ButterCream\Controller\Component\FlashComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * ButterCream\Controller\Component\FlashComponent Test Case
 */
class FlashComponentTest extends TestCase
{
    /**
     * @var \ButterCream\Controller\Component\FlashComponent
     */
    protected FlashComponent $Flash;

    /**
     * @var \Cake\Http\Session
     */
    protected Session $session;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->session = new Session();
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_URI' => '/',
            ],
            'session' => $this->session,
        ]);
        $controller = new Controller($request);
        $registry = new ComponentRegistry($controller);
        $this->Flash = new FlashComponent($registry);
    }

    /**
     * Test that escape defaults to false
     *
     * @return void
     */
    public function testEscapeDefaultsFalse(): void
    {
        $this->Flash->set('Test <b>bold</b> message');

        $flash = $this->session->read('Flash.flash');
        $this->assertNotEmpty($flash);
        $this->assertFalse($flash[0]['params']['escape']);
    }

    /**
     * Test that explicit escape true overrides default
     *
     * @return void
     */
    public function testExplicitEscapeTrue(): void
    {
        $this->Flash->set('Test message', ['escape' => true]);

        $flash = $this->session->read('Flash.flash');
        $this->assertNotEmpty($flash);
        $this->assertTrue($flash[0]['params']['escape']);
    }

    /**
     * Test set stores message text
     *
     * @return void
     */
    public function testSetStoresMessage(): void
    {
        $this->Flash->set('Hello World');

        $flash = $this->session->read('Flash.flash');
        $this->assertNotEmpty($flash);
        $this->assertEquals('Hello World', $flash[0]['message']);
    }

    /**
     * Test set with element option
     *
     * @return void
     */
    public function testSetWithElement(): void
    {
        $this->Flash->set('Success!', ['element' => 'success']);

        $flash = $this->session->read('Flash.flash');
        $this->assertNotEmpty($flash);
        $this->assertStringContainsString('success', $flash[0]['element']);
    }

    /**
     * Test set with Throwable
     *
     * @return void
     */
    public function testSetWithException(): void
    {
        $exception = new RuntimeException('Something went wrong', 500);
        $this->Flash->set($exception);

        $flash = $this->session->read('Flash.flash');
        $this->assertNotEmpty($flash);
        $this->assertEquals('Something went wrong', $flash[0]['message']);
    }

    /**
     * Test consecutive calls stack messages
     *
     * @return void
     */
    public function testMessagesStack(): void
    {
        $this->Flash->set('First message');
        $this->Flash->set('Second message');

        $flash = $this->session->read('Flash.flash');
        $this->assertCount(2, $flash);
        $this->assertEquals('First message', $flash[0]['message']);
        $this->assertEquals('Second message', $flash[1]['message']);
    }
}
