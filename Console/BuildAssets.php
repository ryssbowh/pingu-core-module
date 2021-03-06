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
        exec('npm run merge 1>/dev/null', $output, $return);
        if($return !== 0) {
            throw AssetException::merging($output);
        }
        $output = [];
        exec('npm install 1>/dev/null', $output, $return);
        if($return !== 0) {
            throw AssetException::installing($output);
        }
        $output = [];
        if(config('app.env') == 'production') {
            exec('npm run production 1>/dev/null', $output, $return);
        }
        else{
            exec('npm run development 1>/dev/null', $output, $return);
        }
        if($return !== 0) {
            throw AssetException::compiling($output);
        }
        $this->info('Assets rebuilt !');
    }
}
