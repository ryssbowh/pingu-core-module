<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Pingu\Core\Exceptions\AssetException;

class BuildAssets extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'build-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build assets for all modules and themes';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $output = [];
        exec('npm run merge', $output, $return);
        if($return !== 0){
            throw AssetException::merging($output);
        }
        $output = [];
        exec('npm install', $output, $return);
        if($return !== 0){
            throw AssetException::installing($output);
        }
        $output = [];
        if(config('app.env') == 'production'){
            exec('npm run production', $output, $return);
        }
        else{
            exec('npm run development', $output, $return);
        }
        if($return !== 0){
            throw AssetException::compiling($output);
        }
        $this->info('Assets rebuilt !');
    }
}
