<?php
namespace ButterCream\Shell;

use Cake\Console\ConsoleOutput;
use Cake\Console\Shell;

/**
 * Shell for determining if a file in the database is missing on the server
 */
class MissingFilesShell extends Shell
{

    /**
     * Loaded Tasks
     *
     * @var array
     */
    public $tasks = ['ButterCream.MissingFiles'];

    /**
     * Main Function for Help
     *
     * @return void
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
    }

    /**
     * Validate the existence of files using database records
     *
     * @return bool
     */
    public function findFiles()
    {
        $this->MissingFiles->findFiles();

        return true;
    }

    /**
     * Validate the existence of database records using files
     *
     * @return bool
     */
    public function findRecords()
    {
        $this->MissingFiles->findRecords();

        return true;
    }

    /**
     * Removed orphaned files in the uploads directory
     *
     * @return bool
     */
    public function killOrphans()
    {
        $this->MissingFiles->killOrphans();

        return true;
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description('A console tool for determining if a file in the database is missing on the server.')
            ->addSubcommand('find-files', [
                'help' => 'Use database records from the Files table to check the existence of files.'
            ])
            ->addSubcommand('find-records', [
                'help' => 'Use existing files to check the existence of database records in the Files table.'
            ])
            ->addSubcommand('kill-orphans', [
                'help' => 'It\'s all in the name'
            ]);

        return $parser;
    }

    /**
     * Override the welcome function to remove the default "Welcome to CakePHP message"
     *
     * @return void
     */
    protected function _welcome()
    {
        $this->out('
    __  ____           _                _______ __
   /  |/  (_)_________(_)___  ____ _   / ____(_) /__  _____
  / /|_/ / / ___/ ___/ / __ \/ __ `/  / /_  / / / _ \/ ___/
 / /  / / (__  |__  ) / / / / /_/ /  / __/ / / /  __(__  )
/_/  /_/_/____/____/_/_/ /_/\__, /  /_/   /_/_/\___/____/
                           /____/                          ');
    }
}
