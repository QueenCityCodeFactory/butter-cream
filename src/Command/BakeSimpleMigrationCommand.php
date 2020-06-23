<?php
declare(strict_types=1);

namespace ButterCream\Command;

use Bake\Utility\TemplateRenderer;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Migrations\Command\BakeSimpleMigrationCommand as MigrationsBakeSimpleMigrationCommand;

/**
 * Task class for generating migration snapshot files.
 */
abstract class BakeSimpleMigrationCommand extends MigrationsBakeSimpleMigrationCommand
{
    /**
     * @inheritDoc
     */
    public function bake(string $name, Arguments $args, ConsoleIo $io): void
    {
        $this->io = $io;
        $migrationWithSameName = glob($this->getPath($args) . '*_' . $name . '.php');
        if (!empty($migrationWithSameName)) {
            $force = $args->getOption('force');
            if (!$force) {
                $io->abort(
                    sprintf(
                        'A migration with the name `%s` already exists. Please use a different name.',
                        $name
                    )
                );
            }

            $io->info(sprintf('A migration with the name `%s` already exists, it will be deleted.', $name));
            foreach ($migrationWithSameName as $migration) {
                $io->info(sprintf('Deleting migration file `%s`...', $migration));
                if (unlink($migration)) {
                    $io->success(sprintf('Deleted `%s`', $migration));
                } else {
                    $io->err(sprintf('An error occurred while deleting `%s`', $migration));
                }
            }
        }

        $renderer = new TemplateRenderer($this->theme);
        $renderer->set('name', $name);
        $renderer->set($this->templateData($args));
        $contents = $renderer->generate($this->template());

        $filename = $this->getPath($args) . $this->fileName($name);
        $this->createFile($filename, $contents, $args, $io);

        $emptyFile = $this->getPath($args) . '.gitkeep';
        $this->deleteEmptyFile($emptyFile, $io);
    }
}
