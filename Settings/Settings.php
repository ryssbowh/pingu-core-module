<?php

namespace Pingu\Core\Settings;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Pingu\Core\Exceptions\SettingsException;
use Pingu\Core\Settings\SettingsRepository;
use Pingu\Core\Entities\Settings as SettingModel;

class Settings
{
    protected $settings = [];

    protected $repositories = [];

    protected $cacheKey = 'settings';

    /**
     * Registers a settings repository
     * 
     * @param SettingsRepository $repository
     * @param Application        $app
     */
    public function register(SettingsRepository $repository, Application $app)
    {
        $key = 'settings.'.$repository->name();
        $app->singleton(
            $key, function () use ($repository) {
                return $repository;
            }
        );
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
     * Get all registered repositories
     * 
     * @return array
     */
    public function allRepositories(): array
    {
        return $this->repositories;
    }

    /**
     * 
     * @param string $name
     * 
     * @return SettingsRepository
     */
    public function hasRepository(string $name): bool
    {
        return isset($this->repositories[$name]);
    }

    /**
     * Load all settings, either from cache or database
     * 
     * @return array
     */
    public function all(): array
    {
        if (config('core.settings.useCache')) {
            $_this = $this;
            return \Cache::rememberForever(
                $this->cacheKey, function () use ($_this) {
                    return $_this->loadAll();
                }
            );
        }
        return $this->loadAll();
    }

    /**
     * Load all settings from database
     * 
     * @return array
     */
    protected function loadAll()
    {
        if (!\Schema::hasTable('settings')) {
            return [];
        }
        $settings = SettingModel::all()->sortBy('weight')->keyBy('name');
        $out = [];
        foreach ($settings as $setting) {
            if ($this->hasRepository($setting->repository)) {
                $out[$setting->name] = $setting->value;
            }
        }
        return $out;
    }

    /**
     * Creates a new setting in database
     * 
     * @param string $name
     * @param string $repository
     * @param bool   $encrypted
     * @param mixed  $value
     *
     * @return SettingsModel|false
     */
    public function create(string $name, string $repository, bool $encrypted, $value = null)
    {
        $all = $this->all();
        if (isset($all[$name])) {
            throw SettingsException::alreadyDefined($name);
        }

        SettingModel::unguard();
        $setting = SettingModel::create(
            [
            'name' => $name,
            'encrypted' => $encrypted,
            'value' => $value ? $value : config($name),
            'repository' => $repository
            ]
        );

        $this->forgetCache();

        return $setting;
    }

    public function forgetCache()
    {
        \Cache::forget($this->cacheKey);
    }

    /**
     * Set the value (and options) of a setting
     * 
     * @param string $name
     * @param mixed  $value
     *
     * @return bool
     */
    public function set(string $name, $value)
    {
        $setting = SettingModel::where(['name' => $name])->first();
        if (!$setting) {
            return false;
        }

        $setting->value = $value;
        $setting->save();

        config([$name => $setting->value]);

        return true;
    }
}