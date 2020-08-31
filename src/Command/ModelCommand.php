<?php
declare(strict_types=1);

namespace ButterCream\Command;

use Bake\Command\ModelCommand as BakeModelCommand;
use ButterCream\Utility\TemplateRenderer;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
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
        $force = !empty($args->getOption('force'));
        $io->createFile($filename, $out, $force);

        $emptyFile = $path . 'Entity' . DS . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $io);
    }

    /**
     * Bake a table class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     * @param \Cake\Console\Arguments $args CLI Arguments
     * @param \Cake\Console\ConsoleIo $io CLI Arguments
     * @return void
     */
    public function bakeTable(Table $model, array $data, Arguments $args, ConsoleIo $io): void
    {
        $data += [
            'noCallbacks' => !empty($args->getOption('no-callbacks')),
        ];

        parent::bakeTable($model, $data, $args, $io);
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to configure
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser->addOption('no-callbacks', [
            'boolean' => true,
            'help' => 'Disable generating table callbacks',
        ]);

        return $parser;
    }
}
