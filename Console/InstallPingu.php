<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallPingu extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Pingu';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(file_exists(".env")){
            // return $this->error('the file .env already exists, remove it if you want to force the install');
        }

        $this->info("Let's install Pingu !");
        if(!$local = $this->option('local')){
            $this->info("Running in production mode, node dependencies will not be installed (-l for local mode)");
        }

        $url = $this->ask("Enter the site url");
        if(substr($url, 0, 7) != 'http://'){
            $url = 'http://'.$url;
        }

        $connection = false;
        while(!$connection){
            list($dbdriver, $dbhost, $dbname, $dbuser, $dbpassword) = $this->askDbDetails();

            $this->info("Testing connection...");

            try{
                $con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
                $connection = true;
            }
            catch(\ErrorException $e){
                $this->warn("Can't connect to database (".$e->getMessage()."), let's try again");
            }
        }

        $this->info("Connection working, creating .env file...");
        $this->createEnvFile($url, $dbdriver, $dbhost, $dbname, $dbuser, $dbpassword);

        $output = [];

        try{
            $this->info("Publishing configuration...");
            \Artisan::call('module:publish-config');
            
            $this->info("migrating laravel...");
            \Artisan::call('migrate');

            $this->info("migrating modules...");
            \Artisan::call('module:migrate');

            $this->info("Seeding modules...");
            \Artisan::call('module:seed');

            if($local){
                $this->info("Merging package.json...");
                \Artisan::call('module:merge-packages', ['auto-resolve' => 'higher']);

                $this->info("Installing node modules...");
                exec('npm install', $output);

                $output = [];

                $this->info("Compiling assets...");
                exec('npm run dev', $output);

                $output = [];
            }

            $this->info("Creating key...");
            \Artisan::call('key:generate');

            $this->info("Symlinking storage...");
            \Artisan::call('storage:link');

            $this->info("Symlinking themes...");
            \Artisan::call('theme:link');

            $this->info("Symlinking modules...");
            \Artisan::call('module:link');

            $this->info("Clearing cache...");
            \Artisan::call('cache:clear');
        }
        catch(\Exception $e){
            $this->error("ERROR WHILE INSTALLING : {$e->getMessage()}");
            $this->error("OUTPUT :");
            $this->error(implode("\n", $output));
            unlink(".env");
        }

        $this->info("Installation complete !");
    }

    public function createEnvFile($url, $dbdriver, $dbhost, $dbname, $dbuser, $dbpassword)
    {
        $env = file_get_contents('.env.example');
        $env = explode("\n", $env);
        $env = array_map(function($item) use ($url, $dbdriver, $dbhost, $dbname, $dbuser, $dbpassword){
            if(substr($item, 0, 7) == "APP_URL"){
                return "APP_URL=".$url;
            }
            elseif(substr($item, 0, 13) == "DB_CONNECTION"){
                return "DB_CONNECTION=".$dbdriver;
            }
            elseif(substr($item, 0, 7) == "DB_HOST"){
                return "DB_HOST=".$dbhost;
            }
            elseif(substr($item, 0, 11) == "DB_DATABASE"){
                return "DB_DATABASE=".$dbname;
            }
            elseif(substr($item, 0, 11) == "DB_USERNAME"){
                return "DB_USERNAME=".$dbuser;
            }
            elseif(substr($item, 0, 11) == "DB_PASSWORD"){
                return "DB_PASSWORD=".$dbuser;
            }
            return $item;
        }, $env);
        
        // file_put_contents('.env', implode("\n", $env));
    }

    public function askDbDetails()
    {
        $this->info("Enter your database details:");

        $dbdriver = 'mysql';

        $dbhost = '';
        while($dbhost == ''){
            $dbhost = $this->ask('Host');
        }

        $dbname = '';
        while($dbname == ''){
            $dbname = $this->ask("Database name");
        }

        $dbuser = '';
        while($dbuser == ''){
            $dbuser = $this->ask('User');
        }

        $dbpassword = '';
        while($dbpassword == ''){
            $dbpassword = $this->ask('Password');
        }
        return [$dbdriver, $dbhost, $dbname, $dbuser, $dbpassword];
    }

    protected function getOptions()
    {
        return [
            ['local', 'l', InputArgument::OPTIONAL, 'Local installation, will install node dependencies and compile them', false]
        ];
    }
}
