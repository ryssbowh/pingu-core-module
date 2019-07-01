<?php

namespace Pingu\Core\Components;

class JsConfig
{	
	protected $config = [];

	public function __construct()
	{
		$this->addFromConfig('app.name');
		$this->addFromConfig('app.env');
		$this->addFromConfig('app.debug');
		$this->addFromConfig('app.url');
	}

	public function addFromConfig(string $key)
	{
		if(!is_null(config($key))){
			array_set($this->config, $key, config($key));
		}
	}

	public function add(string $key, $value)
	{
		$this->config[$key] = $value;
	}

	public function get($key = null)
	{
		return $key == null ? $this->config : ($this->config[$key] ?? null);
	}
}