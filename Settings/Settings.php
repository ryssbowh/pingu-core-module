<?php

namespace Pingu\Core\Settings;

use Cache;
use DB, Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Pingu\Core\Exceptions\SettingsException;
use Pingu\Core\Settings\SettingsRepository;
use Pingu\Core\Entities\Settings as SettingModel;
use Pingu\Core\Events\SettingChanged;
use Pingu\Core\Events\SettingChanges;

class Settings
{
    /**
     * @var Collection
     */
    protected $settings;

    protected $repositories = [];

    /**
     * Loads all db settings in memory
     *
     * @return void
     */
    public function __construct()
    {
        $this->settings = collect();
        if (Schema::hasTable('settings')) {
            if (env('APP_ENV') != 'production' ) {
                Cache::forget('settings');
            }
            $this->settings = $this->decryptHandler($this->resolveCache());
        }
    }

    /**
     * Registers a settings repository
     * 
     * @param SettingsRepository $repository
     * @param Application        $app
     */
    public function register(SettingsRepository $repository, Application $app)
    {
        $key = 'settings.'.$repository->name();
        $app->singleton($key, function () use ($repository) {
            return $repository;
        });
        $this->repositories[$repository->name()] = $repository;
    }

    /**
     * Repository getter
     * 
     * @param string $name
     * 
     * @return SettingsRepository
     */
    public function repository(string $name): SettingsRepository
    {
        if (!isset($this->repositories[$name])) {
            throw SettingsException::repositoryNotFound($name);
        }
        return $this->repositories[$name];
    }

    /**
     * Boots all registered repositories
     */
    public function bootRepositories()
    {
        foreach ($this->repositories as $repository) {
            $repository->boot();
        }
    }

    /**
     * Returns all settings models
     * 
     * @return Collection
     */
    public function all()
    {
        return $this->settings;
    }

    protected function resolveCache()
    {
        return Cache::rememberForever('settings', function () {
            return SettingModel::all()->sortBy('weight')->keyBy('name');
        });
    }
    
    /**
     * Decrypt values that need decrypted
     * 
     * @param  array $settings
     * @return array
     */
    protected function decryptHandler($settings)
    {
        foreach ($settings as $object) {
            if ($object->encrypted && !empty($object->value) ) {
                $object->value = decrypt($object->value);
            }
        }
        return $settings;
    }
    
    /**
     * Get one or several settings
     * 
     * @param  mixed $key
     * @return mixed
     */
    public function get($key = NULL)
    {   
        $settings = $this->decryptHandler($this->resolveCache());

        if($key and !isset($settings[$key])) return false;

        // no key passed, assuming get all settings
        if ($key == NULL) {
            return $settings;
        }
        
        // array of keys passed, return those settings only
        $result = collect();
        if (is_array($key)) {
            foreach ($key as $key) {
                $result[] = $settings[$key];
            }
            return $result;
        }

        // single key passed, return that setting only
        if ($this->has($key)) {
            return $settings[$key]; 
        } 
        return false;
        
    }

    /**
     * Returns all settings for a section
     * 
     * @param string $section
     *
     * @return Collection
     */
    public function getBySection(string $section)
    {
        return $this->get()->filter(function ($item, $key) use ($section) {
            return $item->section == $section;
        })->sortBy('weight');
    }

    

    /**
     * Creates a new setting in database
     * 
     * @param string $name
     * @param string $repository
     * @param bool   $encrypted
     * 
     * @return SettingsModel|false
     */
    public function create(string $name, string $repository, bool $encrypted)
    {
        if ($this->settings->has($name)) {
            return false;
        }

        SettingModel::unguard();
        $setting = SettingModel::create([
            'name' => $name,
            'encrypted' => $encrypted,
            'value' => config($name),
            'repository' => $repository
        ]);

        Cache::forget('settings');

        return $setting;
    }

    /**
     * Set the value (and options) of a setting
     * 
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, $changes)
    {
        if(!$this->has($name)) return false;

        $setting = $this->get($name);

        if ($setting->encrypted && isset($changes['value'])) {
            $changes['value'] = encrypt($changes['value']);
        }

        config([$name => $changes['value']]);
        $setting->update($changes);

        Cache::forget('settings');

        return $setting;
    }

    public function has($key)
    {
        return $this->settings->has($key);
    }
}