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
