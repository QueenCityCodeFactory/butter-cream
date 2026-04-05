<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase;

use ButterCream\ButterCreamPlugin;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\TestSuite\TestCase;

/**
 * ButterCream plugin test
 */
class ButterCreamPluginTest extends TestCase
{
    /**
     * @var \ButterCream\ButterCreamPlugin
     */
    protected ButterCreamPlugin $plugin;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->plugin = new ButterCreamPlugin();
    }

    /**
     * Test plugin extends BasePlugin
     *
     * @return void
     */
    public function testExtendsBasePlugin(): void
    {
        $this->assertInstanceOf(BasePlugin::class, $this->plugin);
    }

    /**
     * Test plugin name
     *
     * @return void
     */
    public function testPluginName(): void
    {
        $this->assertEquals('ButterCream', $this->plugin->getName());
    }

    /**
     * Test console adds commands without errors
     *
     * @return void
     */
    public function testConsoleAddsCommands(): void
    {
        $commands = new CommandCollection();
        $result = $this->plugin->console($commands);
        $this->assertInstanceOf(CommandCollection::class, $result);
    }
}
