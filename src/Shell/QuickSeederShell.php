<?php
namespace ButterCream\Shell;

use Cake\Console\Shell;
use Cake\Utility\Inflector;

/**
 * Quick Seeder
 *
 * Example query to get non empty tables:
 *     SELECT `table_name` FROM information_schema.tables WHERE table_rows >= 1 AND TABLE_SCHEMA='panda';
 */
class QuickSeederShell extends Shell
{

    /**
     * Main Shell Function
     * @return void
     */
    public function main()
    {
        $tables = [];
        if (!empty($this->args[0])) {
            $tables = explode(',', $this->args[0]);
        }

        foreach ($tables as $table) {
            $this->dispatchShell('bake seed ' . Inflector::camelize($table) . ' --table ' . $table . ' --data' . (!empty($this->params['folder']) ? ' --theme ButterCream --folder ' . $this->params['folder'] : ''));
        }
    }

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @link http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser
            ->addArgument('tables', [
                'help' => 'A list of tables to seed (e.g: users, groups, clients).'
            ])
            ->addOption('folder', [
                'short' => 'f',
                'help' => 'The seed folder (e.g: Sandbox)',
            ]);

        return $parser;
    }
}
