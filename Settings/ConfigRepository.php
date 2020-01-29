<?php

namespace Pingu\Core\Settings;

use Illuminate\Support\Arr;
use Pingu\Core\Settings\Settings;

class ConfigRepository extends \Illuminate\Config\Repository
{
    protected $settings = [];

    public function loadSettings(array $settings)
    {
        foreach ($settings as $name => $value) {
            $this->settings[] = $name;
            Arr::set($this->items, $name, $value);
        }
    }

    protected function isSetting($key)
    {
        return in_array($key, $this->settings);
    }

    /**
     * Set a given configuration value.
     * Will not override config that are settings
     *
     * @param array|string $key
     * @param mixed        $value
     * 
     * @return void
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        // dump($key, $value);

        foreach ($keys as $key => $value) {
            if ($this->isSetting($key)) {
                continue;
            }
            Arr::set($this->items, $key, $value);
        }
    }
}
