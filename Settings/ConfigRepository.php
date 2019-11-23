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
    public function __construct(array $config, Settings $settings)
    {
        $this->items = $config;
        if ($array = $settings->all()) {
            foreach ($array as $object) {
                Arr::set($this->items, $object->name, $object->value);
            }
        }
    }
}
