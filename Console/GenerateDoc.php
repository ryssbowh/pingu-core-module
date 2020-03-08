<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateDoc extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate-doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates documentation with php documentor, doc will go in the docs folder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $phar = module_path('Core').'/phpDocumentor.phar';
        $this->info('Generating docs...');
        exec($phar, $output);
        $this->info('Docs generated !');
    }
}
