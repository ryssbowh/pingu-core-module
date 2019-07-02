<?php

namespace Pingu\Core\Components;

use Illuminate\Support\Arr;

class ThemeConfig
{
	private $config;

	/**
	 * Sets a new array of config
	 * @param array $config
	 */
	public function setConfig(array $config)
	{
		$this->config = Arr::dot($config);
	}

	/**
	 * Get a config value. Will default to normal config if not found
	 * @param  string $name
	 * @return mix  
	 */
	public function get(string $name, $default = null)
	{
		return $this->config[$name] ?? $default ?? config($name);
	}

	/**
	 * Sets a new config
	 * @param string $name
	 * @param mixed $value
	 */
	public function set(array $values)
	{
		foreach($values as $name => $value){
			$this->config[$name] = $value;
		}
	}
}