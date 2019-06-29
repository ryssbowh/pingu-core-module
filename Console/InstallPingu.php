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
        $installed = storage_path('installed');
        if(file_exists($installed)){
            return $this->error("the file $installed already exists, remove it if you want to force the install");
        }

        $this->info("Let's install Pingu !");

        if($errors = $this->checkRequirements()){
            return $this->showErrors($errors);
        }
        if($errors = $this->checkPermissions()){
            return $this->showErrors($errors);
        }
        if($errors = $this->checkCommands()){
            return $this->showErrors($errors);
        }

        $env = $this->askEnv();
        
        $this->writeEnvFile($env);

        // $this->performInstall();
    }

    protected function askEnv()
    {
        $out = [];
        foreach(config('installer.env') as $name => $details){
            if($details['type'] == 'open'){
                $out[$name] = $this->askOpen($details);
            }
            elseif($details['type'] == 'choice'){
                $out[$name] = $this->askChoice($details);
            }
        }
        $out = array_merge($out, $this->askDbDetails());
        return $out;
    }

    protected function askChoice(array $details)
    {
        return $this->choice($details['name'] ?? '', $details['values'] ?? []);
    }

    protected function asOpen(array $details)
    {
        $out = false;
        while(!$out){
            $out = $this->ask($details['name'] ?? '');
            if(isset($details['filter'])){
                $out = filter_var($out, $details['filter']);
            }
        }
        return $out;
    }

    protected function writeEnvFile(array $env)
    {
        $this->info("Creating .env file...");
        $content = '';
        foreach($env as $name => $value){
            $content .= $name.'='.$value."\n";
        }
        file_put_contents('.env2', $content);
    }

    protected function checkPermissions()
    {
        $errors = [];
        foreach(config('installer.permissions') as $folder => $perm){
            $permission = substr(sprintf('%o', fileperms(base_path($folder))), -4);
            if($permission < $perm){
                $errors[] = "Not enough permissions for folder $folder, found $permission, needed $perm";
            }
        }
        return $errors;
    }

    protected function checkCommands()
    {
        $errors = [];
        foreach(config('installer.requirements.commands') as $command => $version){
            $str = exec($command, $output, $return);
            if($return != 0){
                $errors[] = "'$command' could not be found";
            }
            else{
                $v = trim($str, 'v');
                if(version_compare($v, $version) < 0){
                    $errors[] = "'command' should at least be of version $version, found $v";
                }
            }
        }
        return $errors;
    }

    protected function checkRequirements()
    {
        $errors = [];
        $version = phpversion();
        if(version_compare(phpversion(), config('installer.phpMinVersion')) < 0){
            $errors[] = "Php version (".phpversion().") is below the minimum required (".config('installer.phpMinVersion').")";
        }
        foreach(config('installer.requirements.php') as $extension){
            if(!phpversion($extension)){
                $errors[] = "Php extension $extension is not installed";
            }
        }
        $modules = apache_get_modules();
        foreach(config('installer.requirements.apache') as $module){
            if(!in_array($module, $modules)){
                $errors[] = "Apache module $module is not installed";
            }
        }
        return $errors;
    }

    protected function showErrors(array $errors)
    {
        $this->error("Some errors were encountered:");
        foreach($errors as $error){
            $this->error('- '.$error);
        }
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

        $this->info("Testing connection...");

        try{
            $con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
            $connection = true;
        }
        catch(\ErrorException $e){
            $this->warn("Can't connect to database (".$e->getMessage()."), let's try again");
            return $this->askDbDetails();
        }

        return [
            'DB_CONNECTION' => $dbdriver, 
            'DB_HOST' => $dbhost, 
            'DB_DATABASE' => $dbname, 
            'DB_USERNAME' => $dbuser, 
            'DB_PASSWORD' => $dbpassword
        ];
    }

    public function performInstall()
    {
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
}
