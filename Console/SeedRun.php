<?php

namespace Pingu\Core\Console;

use Illuminate\Console\ConfirmableTrait;
use Pingu\Core\Console\AbstractSeedCommand;
use Pingu\Core\Seeding\SeederMigrator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedRun extends AbstractSeedCommand
{
    use ConfirmableTrait;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with records (and saves a record in seeds table)';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:seed';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        // Prepare the migrator.
        $this->prepareMigrator();

        // Execute the migrator.
        $this->migrator->run($this->getMigrationPath(), $this->getMigrationOptions());
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to run the seeders from.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}