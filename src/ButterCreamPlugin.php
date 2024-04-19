<?php
declare(strict_types=1);

namespace ButterCream;

use Bake\Command\BakeCommand;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use Cake\Utility\Inflector;
use DirectoryIterator;
use ReflectionClass;
use ReflectionException;

/**
 * Plugin for ButterCream
 */
class ButterCreamPlugin extends BasePlugin
{
    /**
     * Plugin name.
     *
     * @var string
     */
    protected ?string $name = 'ButterCream';

    /**
     * Load all the plugin configuration and bootstrap logic.
     *
     * The host application is provided as an argument. This allows you to load
     * additional plugin dependencies, or attach events.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        $app->addPlugin('BootstrapUI');
        parent::bootstrap($app);
    }

    /**
     * Add routes for the plugin.
     *
     * If your plugin has many routes and you would like to isolate them into a separate file,
     * you can create `$plugin/config/routes.php` and delete this method.
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        parent::routes($routes);
    }

    /**
     * Add middleware for the plugin.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to update.
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue = parent::middleware($middlewareQueue);

        return $middlewareQueue;
    }

    /**
     * Add console commands for the plugin.
     *
     * @param \Cake\Console\CommandCollection $commands The command collection to update
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        $commands = parent::console($commands);
        $commands = $this->discoverCommands($commands);

        return $commands;
    }

    /**
     * Scan plugins and application to find commands that are intended
     * to be used with bake.
     *
     * Non-Abstract commands extending `Bake\Command\BakeCommand` are included.
     * Plugins are scanned in the order they are listed in `Plugin::loaded()`
     *
     * @param \Cake\Console\CommandCollection $commands The CommandCollection to add commands into.
     * @return \Cake\Console\CommandCollection The updated collection.
     */
    protected function discoverCommands(CommandCollection $commands): CommandCollection
    {
        $found = $this->findInPath($this->name, $this->path . DS . 'src' . DS);
        if (count($found)) {
            $commands->addMany($found);
        }

        return $commands;
    }

    /**
     * Search a path for commands.
     *
     * @param string $namespace The namespace classes are expected to be in.
     * @param string $path The path to look in.
     * @return array<string>
     * @psalm-return array<string, class-string<\Bake\Command\BakeCommand>>
     */
    protected function findInPath(string $namespace, string $path): array
    {
        $hasSubfolder = false;
        $path .= 'Command/';
        $namespace .= '\Command\\';

        if (file_exists($path . 'Bake/')) {
            $hasSubfolder = true;
            $path .= 'Bake/';
            $namespace .= 'Bake\\';
        } elseif (!file_exists($path)) {
            return [];
        }

        $iterator = new DirectoryIterator($path);
        $candidates = [];
        foreach ($iterator as $item) {
            if ($item->isDot() || $item->isDir()) {
                continue;
            }
            /** @psalm-var class-string<\Bake\Command\BakeCommand> $class */
            $class = $namespace . $item->getBasename('.php');

            if (!$hasSubfolder) {
                try {
                    $reflection = new ReflectionClass($class);
                /** @phpstan-ignore-next-line */
                } catch (ReflectionException $e) {
                    continue;
                }
                /** @psalm-suppress TypeDoesNotContainType */
                if (!$reflection->isInstantiable() || !$reflection->isSubclassOf(BakeCommand::class)) {
                    continue;
                }
            }

            $candidates[$class::defaultName()] = $class;
        }

        return $candidates;
    }
}
