<?php
declare(strict_types=1);

namespace ButterCream\Command;

use Bake\Command\ModelCommand as BakeModelCommand;
use ButterCream\Utility\TemplateRenderer;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\ORM\Table;

/**
 * Command for generating model files.
 */
class ModelCommand extends BakeModelCommand
{
    /**
     * Bake an entity class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     * @param \Cake\Console\Arguments $args CLI Arguments
     * @param \Cake\Console\ConsoleIo $io CLI io
     * @return void
     */
    public function bakeEntity(Table $model, array $data, Arguments $args, ConsoleIo $io): void
    {
        if ($args->getOption('no-entity')) {
            return;
        }
        $name = $this->_entityName($model->getAlias());

        $namespace = Configure::read('App.namespace');
        $pluginPath = '';
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
            $pluginPath = $this->plugin . '.';
        }

        $data += [
            'name' => $name,
            'namespace' => $namespace,
            'plugin' => $this->plugin,
            'pluginPath' => $pluginPath,
            'primaryKey' => [],
        ];

        $renderer = new TemplateRenderer($this->theme);
        $renderer->set($data);
        $out = $renderer->generate('Bake.Model/entity');

        $path = $this->getPath($args);
        $filename = $path . 'Entity' . DS . $name . '.php';
        $io->out("\n" . sprintf('Baking entity class for %s...', $name), 1, ConsoleIo::QUIET);
        $io->createFile($filename, $out, $args->getOption('force'));

        $emptyFile = $path . 'Entity' . DS . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $io);
    }
}
