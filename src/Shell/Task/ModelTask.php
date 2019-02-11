<?php
namespace ButterCream\Shell\Task;

use Bake\Shell\Task\ModelTask as CakeModelTask;
use Cake\Console\ConsoleIo;

class ModelTask extends CakeModelTask
{

    /**
     * tasks
     *
     * @var array
     */
    public $tasks = [
        'Bake.Fixture',
        'ButterCream.BakeTemplate',
        'Bake.Test'
    ];

    /**
     * Constructs this Shell instance.
     *
     * @param \Cake\Console\ConsoleIo|null $io An io instance.
     * @link https://book.cakephp.org/3.0/en/console-and-shells.html#Shell
     */
    public function __construct(ConsoleIo $io = null)
    {
        parent::__construct($io);

        // Need to unset the original BakeTemplate so we can use the one from ButterCream
        unset($this->tasks['Bake.BakeTemplate']);
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addOption('callbacks', [
            'boolean' => true,
            'help' => 'Stub out callback methods (beforeMarshal, beforeSave)'
        ]);

        return $parser;
    }

    /**
     * Bake a table class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     * @return string|null
     */
    public function bakeTable($model, array $data = [])
    {
        $data += [
            'callbacks' => !empty($this->params['callbacks'])
        ];

        return parent::bakeTable($model, $data);
    }
}
