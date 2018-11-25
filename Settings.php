<?php
namespace Modules\Core;

use DB;
use Cache;
use Modules\Core\Events\SettingChanged;
use Modules\Core\Events\SettingChanges;

class Settings
{
    protected $registered = []; //prout
    protected $encrypted = [];

    /**
     * Loads all db settings in memory and registers all settings in config settings.register
     */
    public function init(){
        if( env('APP_ENV') != 'production' ) Cache::forget('settings');
        $settings = $this->resolveCache();

        foreach($settings as $name => $object){
            $this->registered[] = $name;
            $value = $object->value;
            if( $object->encrypted ){
                $value = decrypt($object->value);
                $this->encrypted[] = $name;
            }
            config($name, $value);
        }
    }

    /**
     * Check if this setting is registered
     * @param  string  $name
     * @return boolean
     */
    public function isRegistered( string $name ){
        return in_array( $name, $this->registered );
    }

    /**
     * Check if this setting is set as encrypted
     * @param  string  $name
     * @return boolean
     */
    public function isEncrypted( string $name ){
        return in_array( $name, $this->encrypted );
    }

    /**
     * Registers a setting. Will be created in db if not existing, with the default value of config
     * @param  string  $name    [description]
     * @param  boolean $encrypt [description]
     */
    public function register( string $name, $encrypt = false, $title = '', $section = '' ){
        if( $this->isRegistered($name) ) return false;
        if( is_null(config($name)) ) throw new \Exception($name.' isn\'t a valid config name');

        if( $encrypt and !$this->isEncrypted($name) ) $this->encrypted[] = $name;
        $this->registered[] = $name;
        if( !isset($this->resolveCache()[$name]) ) $this->create( $name, config($name), $title, $section );
    }

    /**
     * Registers many settings
     * @param  array  $names   [description]
     * @param  array  $encrypt [description]
     * @return [type]          [description]
     */
    public function registerMany( array $names, $encrypt = [] ){
        foreach( $names as $name => $details ){
            $this->register( $name, in_array($name, $encrypt), $details['title'], $details['section'] );
        }
    }

    public function resolveCache() {
        return Cache::rememberForever('settings', function () {
            return DB::table('settings')->get()->keyBy('key')->toArray();
        });
    }
    
    public function decryptHandler($settings) {
        // DO WE NEED TO DECRYPT ANYTHING?
        foreach ($settings as $key => $object) {
            if ( $this->isEncrypted($key) && !empty($object->value) ) {
                $settings[$key]->value = decrypt($settings[$key]->value);
            }
        }
        return $settings;
    }
    
    public function get($key = NULL)
    {   
        if(!$this->isRegistered($key)) return false;

        $settings = $this->decryptHandler($this->resolveCache());
        // no key passed, assuming get all settings
        if ($key == NULL) {
            return $settings;
        }
        
        // array of keys passed, return those settings only
        if (is_array($key)) {
            foreach ($key as $key) {
                $result[] = $settings[$key];
            }
            return $result;
        }

        // single key passed, return that setting only
        if (array_key_exists($key, $settings)) {
            return $settings[$key]; 
        } 
        return false;
        
    }

    protected function create($name, $value, $encrypted, $title, $section){
        if ($encrypted && !empty($value)) {
            $value = encrypt($value);
        }
        DB::table('settings')->insert(['key'=>$name, 'value'=>$value, 'title'=>$title, 'section'=>$section]);
        return true;
    }

    public function set($name, $value, $changes = []){
        if( !$this->isRegistered($name) ) return false;

        event(new SettingChanges($name, $value, $changes));

        config($name, $value);

        if ($this->isEncrypted($name) && !empty($value)) {
            $value = encrypt($value);
        }

        $changes['value'] = $value;

        DB::table('settings')->where('key', $name)->update($changes);

        event(new SettingChanged($name, $value, $changes));

        Cache::forget('settings');

        return true;
    }
    
    public function setMany($changes)
    {   
        foreach ($changes as $key => $value) {
            $this->set($key, $name);
        }
        
        return true;
    }
}