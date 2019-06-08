<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Module;
use Exception;
use Theme;

class MergePackages extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:merge-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges package.json for all modules';

    protected $autoResolve = 'ask';

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
     * returns all unique keys from an array of arrays
     * @param  array  $array
     * @return array
     */
    public function array_keys_recursive(array $array)
    {
        $out = [];
        foreach($array as $sub){
            $out = array_merge($out, array_keys($sub));
        }
        return array_unique($out);
    }

    /**
     * Returns all the keys that have a given value in an array
     * @param  array  $values
     * @param  $value
     * @return array
     */
    public function array_same_value_keys(array $values, $value)
    {
        $out = [];
        foreach($values as $name => $val){
            if($val == $value) $out[] = $name;
        }
        return $out;
    }

    /**
     * Verion of array_unique that merges the keys
     * @param  array  $values
     * @return array
     */
    public function array_unique(array $values){
        $out = [];
        $counts = array_count_values($values);
        foreach($counts as $value => $count){
            if($count > 1){
                $sameValueKeys = $this->array_same_value_keys($values, $value);
                $out[implode(',', $sameValueKeys)] = $value;
            }
            else{
                $out[array_search($value, $values)] = $value;
            }
        }
        return $out;
    }

    /**
     * Checks for conflicts of version in the given array of arrays
     * @param  array  $array
     * @return array
     */
    public function checkFinalConflicts(array $array)
    {
        $out = [];
        $allKeys = $this->array_keys_recursive($array);

        foreach($allKeys as $key){
            $values = [];
            foreach($array as $name => $sub){
                if(isset($sub[$key])) $values[$name] = $sub[$key];
            }
            $values = $this->array_unique($values);
            if(sizeof($values) > 1){
                if($this->autoResolve == 'ask'){
                    $this->error("Conflict : several versions available for '$key'");
                    list($choices, $answers) = $this->buildChoices($values);
                    $out[$key] = $this->askConflictChoice($choices, $answers);
                }
                elseif($this->autoResolve == 'higher'){
                    $value = $this->getHigherVersion($values);
                    $this->info("Conflict for '$key' : higher version $value was kept");
                    $out[$key] = $value;
                }
                else{
                    $value = $this->getLowerVersion($values);
                    $this->info("Conflict for '$key' : lower version $value was kept");
                    $out[$key] = $value;
                }
            }
            else{
                $out[$key] = array_values($values)[0];
            }
        }
        return $out;
    }

    /**
     * Builds fancy questions and answers associated to each
     * @param  array  $array
     * @return [array $choices, array $answers]
     */
    public function buildChoices(array $array)
    {
        $choices = $answers = [];
        foreach($array as $key => $value){
            $choices[] = $key.' : '.$value;
            $answers[$key.' : '.$value] = $value;
        }
        return [$choices, $answers];
    }

    /**
     * Get the higher version of an array of versions
     * @param  array  $versions
     * @return string         
     */
    public function getHigherVersion(array $versions)
    {
        $versions = array_values($versions);
        $out = $versions[0];
        foreach($versions as $version){
            $out = version_compare($out, $version) == -1 ? $version : $out;
        }
        return $out;
    }

    /**
     * get the lower version of an array of versions
     * @param  array  $versions
     * @return string
     */
    public function getLowerVersion(array $versions)
    {
        $versions = array_values($versions);
        $out = $versions[0];
        foreach($versions as $version){
            $out = version_compare($out, $version) == -1 ? $out : $version;
        }
        return $out;
    }

    /**
     * Merges two arrays (devDependencies and dependencies) into a single array, checking for conflicts
     * @param  array  $dep
     * @param  array  $dev
     * @param  string $name
     * @return array
     */
    public function mergeDevAndDependencies(array $dep, array $dev, $name)
    {
        $out = array_merge($dep, $dev);
        if($crosses = array_intersect_key($dep, $dev)){
            foreach($crosses as $key => $value){

                if($this->autoResolve == 'ask'){
                    $this->error("Conflict : '$key' exists both in dependencies and devDependencies of $name");
                    list($choices, $answers) = $this->buildChoices(['dependencies' => $dep[$key], 'devDependencies' => $dev[$key]]);
                    $out[$key] = $this->askConflictChoice($choices, $answers);
                }
                elseif($this->autoResolve == 'higher'){
                    $value  = version_compare($dep[$key], $dev[$key]) == -1 ? $dev[$key] : $dep[$key];
                    $this->info("Conflict for '$key' in $name: higher version $value was kept");
                    $out[$key] = $value;
                }
                else{
                    $value = version_compare($dep[$key], $dev[$key]) == -1 ? $dep[$key] : $dev[$key];
                    $this->info("Conflict for '$key' in $name: lower version $value was kept");
                    $out[$key] = $value;
                }
                
            }
        }
        return $out;
    }

    /**
     * Ask a question and return the value associated to the answer
     * @param  array  $choices
     * @param  array  $answers
     * @return string
     */
    public function askConflictChoice(array $choices, array $answers){
        $question = "Which one do you want to keep ?";
        $answer = $this->choice($question, $choices);
        return $answers[$answer];
    }

    /**
     * Decodes a package.json file into an array
     * @param  string $file
     * @return array
     */
    protected function decodeFile($file){
        if(!$string = file_get_contents($file)){
            throw new Exception($file.' could not be read');
        }
        if(!$array = json_decode($string, true)){
            throw new Exception($file.' is not json valid');
        }
        return $array;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        $this->autoResolve = $this->argument('auto-resolve');
        if(!in_array($this->autoResolve, ['ask','lower','higher'])){
            throw new Exception('Auto resolve argument is not valid, accepted values are : ask, higher, lower');
        }

        if($modules = Module::getOrdered()){
            foreach ($modules as $index => $module) {
                $file = $module->getPath().'/package.json';
                if(file_exists($file)){
                    $moduleJson = $this->decodeFile($file);
                    if($index == 'Core'){
                        //Core module is responsible for the base package, the others only define dependencies
                        $packages = $moduleJson;
                    }
                    $final[$module->getName().' module'] = $this->mergeDevAndDependencies($moduleJson['dependencies'] ?? [], $moduleJson['devDependencies'] ?? [], $module->getName());
                }
            }
        }

        if($themes = Theme::all()){
            foreach ($themes as $theme) {
                $file = $theme->getPath('/package.json');
                if(file_exists($file)){
                    $themeJson = $this->decodeFile($file);
                    $final[$theme->name.' theme'] = $this->mergeDevAndDependencies($themeJson['dependencies'] ?? [], $themeJson['devDependencies'] ?? [], $theme->name);
                }
            }
        }

        $packages['devDependencies'] = $this->checkFinalConflicts($final);
        if(isset($packages['dependencies'])) unset($packages['dependencies']);

        $f = fopen(base_path().'/package.json', 'w');
        fwrite($f, json_encode($packages, JSON_PRETTY_PRINT));
        fclose($f);
        $this->info("package.json generated ! you may run 'npm install' or 'npm update'");
        return true;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['auto-resolve', InputArgument::OPTIONAL, "Auto resolve conflicts:\nhigher: keep the higher version\nlower : keep the lower version\nask : ask (default)", 'ask']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
