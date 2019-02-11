<?php
namespace ButterCream\Shell\Task;

use ButterCream\Filesystem\FileApi;
use Cake\Cache\Cache;
use Cake\Console\ConsoleOutput;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\I18n\Time;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Exception;

/**
 * Task for determining which records are missing corresponding files
 */
class MissingFilesTask extends Shell
{
    /**
     * Initializes the Shell
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Files');
    }

    /**
     * Delete orphaned files in the uploads directory.
     *
     * @return void
     */
    public function killOrphans()
    {
        $type = $this->_getCategory();

        $this->out('<success>Beginning deletion of orphaned files for the ' . $type . ' category</success>');

        $categoryPath = Configure::read('FileApi.basePath') . $type;
        $categoryFiles = new Folder($categoryPath);
        if (empty($categoryFiles->path)) {
            $this->out('<info>No files found for ' . $type . '</info>');
            exit;
        }
        $fileNames = $categoryFiles->findRecursive('.*', true);

        $logPath = LOGS . 'missing-records' . DS . 'deleted' . DS;
        $logFolder = new Folder($logPath, true);
        $fileName = $type . '.log';
        $logFile = new File($logPath . $fileName);
        if ($logFile->exists() === false) {
            $logFile->create();
        }
        $logFile->write('Deleted file results for ' . $categoryPath . ' as of ' . Time::now()->i18nFormat('yyyy-MM-dd') . "\n\n");
        foreach ($fileNames as $filePath) {
            $file = new File($filePath);
            $parentFolder = ($file->Folder());
            $exists = $this->Files->exists(['category' => $type, 'filename' => $file->name]);
            if ($exists === false) {
                if (!$file->delete()) {
                    $logFile->append("ERROR: " . $file->path . " could not be deleted.\n");
                } else {
                    $logFile->append($file->path . " was successfully removed.\n");
                    if ($parentFolder->path !== $categoryPath) {
                        $folderContents = $parentFolder->findRecursive('.*', true);
                        if (is_array($folderContents) && count($folderContents) === 0) {
                            $parentFolder->delete();
                        }
                    }
                }
            }
        }

        $this->out('<success>Search complete. Results logged to ' . $logPath . $fileName . '</success>', 1, Shell::QUIET);
    }

    /**
     * Use records from the Files table to attempt to locate the corresponding file on the server.
     *
     * @return void
     */
    public function findFiles()
    {
        $type = $this->_getCategory();

        $this->out('<success>Beginning missing file check for the ' . $type . ' category</success>');
        $files = $this->Files->find()
            ->select([
                'id',
                'category',
                'tag',
                'filename',
                'original_filename',
                'meta',
                'created'
            ])
            ->order([
                'category' => 'asc',
                'tag' => 'asc'
            ]);
        if ($type != 'all') {
            $files
                ->where(['category' => $type])
                ->orderAsc('tag');
        }

        $logPath = LOGS . 'missing-files' . DS;
        $logFolder = new Folder($logPath, true);
        $currentCategory = '';
        $count = 1;
        if ($type === 'all') {
            $fileName = 'all.log';
            $logFile = new File($logPath . $fileName);
            if ($logFile->exists() === false) {
                $logFile->create();
            }
        }
        foreach ($files as $file) {
            if ($currentCategory != $file->category) {
                if ($type !== 'all') {
                    $fileName = $file->category . '.log';
                    $logFile = new File($logPath . $fileName);
                    if ($logFile->exists() === false) {
                        $logFile->create();
                    }
                }
                $categoryPath = Configure::read('FileApi.basePath') . $file->category;
                $logFile->write('Missing file results for ' . $categoryPath . ' as of ' . Time::now()->i18nFormat('yyyy-MM-dd') . "\n\n");
            }
            $path = $categoryPath . DS . $file->tag . DS . $file->filename;
            $exists = file_exists($path);
            if ($exists === false) {
                $logFile->append($path . " does not exist.\n");
                $logFile->append("File ID: " . $file->id . "\n");
                $logFile->append('File uploaded on: ' . $file->created->format('m/d/Y H:i:s') . "\n");
                $logFile->append('Original Filename: ' . $file->original_filename . "\n");
                if (!empty($file->meta)) {
                    $meta = json_decode(json_encode($file->meta), true);
                    $logFile->append(print_r($meta, true));
                }
                $logFile->append("\n\n");
            }
            $currentCategory = $file->category;
            $count++;
        }
        $logFile->close();

        $this->out('<success>Search complete. Results logged to ' . $logPath . $fileName . '</success>', 1, Shell::QUIET);
    }

    /**
     * Use existing files on the server to attempt to locate a record in the Files table.
     *
     * @return void
     */
    public function findRecords()
    {
        $type = $this->_getCategory();
        if ($type == 'all') {
            $categories = $this->_getCategory(true);
        } else {
            $categories = [$type];
        }
        foreach ($categories as $type) {
            $this->out('<success>Beginning missing record check for the ' . $type . ' category</success>');

            $categoryPath = Configure::read('FileApi.basePath') . $type;
            $categoryFiles = new Folder($categoryPath);
            if (empty($categoryFiles->path)) {
                $this->out('<info>No files found for ' . $type . '</info>');
                continue;
            }
            $fileNames = $categoryFiles->findRecursive('.*', true);

            $logPath = LOGS . 'missing-records' . DS;
            $logFolder = new Folder($logPath, true);
            $fileName = $type . '.log';
            $logFile = new File($logPath . $fileName);
            if ($logFile->exists() === false) {
                $logFile->create();
            }
            $logFile->write('Missing record results for ' . $categoryPath . ' as of ' . Time::now()->i18nFormat('yyyy-MM-dd') . "\n\n");
            foreach ($fileNames as $filePath) {
                $file = new File($filePath);
                $exists = $this->Files->exists(['category' => $type, 'filename' => $file->name]);
                if ($exists === false) {
                    $logFile->append($file->path . " does not have a corresponding table row.\n");
                }
            }

            $this->out('<success>Search complete. Results logged to ' . $logPath . $fileName . '</success>', 1, Shell::QUIET);
        }
    }

    /**
     * Fetch list of file categories from database for the user to limit their search with.
     *
     * @param bool $all All Categories
     * @return string
     */
    private function _getCategory($all = false)
    {
        $categories = $this->Files->find()->select('category')->orderAsc('category')->distinct();
        $categories = Hash::extract($categories->toArray(), '{n}.category');
        if ($all === true) {
            return $categories;
        }
        array_unshift($categories, 'all');

        $this->out('<success>Missing Record Check</success>');
        foreach ($categories as $index => $type) {
            $this->out("\t<info>" . $index . ': ' . $type . '</info>');
        }

        $chosenType = null;
        while (empty($chosenType) && empty($categories[$chosenType])) {
            $chosenType = $this->in('Please select the number of one of the specified file types: ');
            if (empty($categories[$chosenType])) {
                $this->out('<error>Please provide one of the listed numbers.</error>');
                $chosenType = null;
            }
        }

        return $categories[$chosenType];
    }
}
