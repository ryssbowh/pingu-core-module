<?php

namespace Pingu\Core\Components;

class JsConfig
{	
	protected $config = [];

	public function __construct()
	{
		foreach(config('core.ajaxConfig', []) as $key){
			$this->setFromConfig($key);
		}
	}

	public function setFromConfig(string $key)
	{
		if(!is_null(config($key))){
			$this->set($key, config($key));
		}
	}

	public function setManyFromConfig(array $configs)
	{
		foreach($configs as $config){
			$this->setFromConfig($config);
		}
	}

	public function set(string $key, $value)
	{
		data_set($this->config, $key, $value);
	}

	public function get($key = null)
	{
		return $key == null ? $this->config : ($this->config[$key] ?? null);
	}
}