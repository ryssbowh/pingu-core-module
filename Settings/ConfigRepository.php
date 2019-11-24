<?php

namespace Pingu\Core\Settings;

use Illuminate\Support\Arr;
use Pingu\Core\Settings\Settings;

class ConfigRepository extends \Illuminate\Config\Repository
{
    /**
     * Create a new configuration repository.
     *
     * @param  array  $config
     * @param  Settings $settings
     * @return void
     */
    public function loadSettings(array $settings)
    {
        foreach ($settings as $name => $value) {
            Arr::set($this->items, $name, $value);
        }
    }
}
