<?php
namespace ButterCream\Shell\Task;

use Migrations\Shell\Task\SeedTask as BaseTask;

/**
 * Seed Task Override
 */
class SeedTask extends BaseTask
{

    /**
     * Get Path
     * @return string The Path
     */
    public function getPath()
    {
        $path = ROOT . DS . $this->pathFragment;

        if (!empty($this->params['folder'])) {
            $path = $path . DS . $this->params['folder'] . DS;
        }

        if (isset($this->plugin)) {
            $path = $this->_pluginPath($this->plugin) . $this->pathFragment;
        }

        return str_replace('/', DS, $path);
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

        $parser->addOption('folder', [
            'help' => 'Folder to bake into.'
        ]);

        return $parser;
    }
}
