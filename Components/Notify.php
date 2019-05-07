<?php

namespace Pingu\Core\Components;

class Notify
{
	public static $levels = ['info', 'success', 'warning', 'danger'];

	public function put(string $level, string $message)
	{
		if(!in_array($level, $this::$levels)){
			throw new Exception("Notify : $level is not a valid level of message");
		}
		$messages = session('notify-'.$level, []);
		$messages[] = $message;
		session(['notify-'.$level => $messages]);
	}

	public function get($level = null)
	{
		$return = [];
		if(is_null($level)){
			foreach($this::$levels as $level2){
				if(session()->has('notify-'.$level2)){
					$return[$level2] = session('notify-'.$level2);
					session()->forget('notify-'.$level2);
				}
			}
		}
		else{
			if(in_array($level, $this::$levels) and session()->has('notify-'.$level)){
				$return = session('notify-'.$level);
				session()->forget('notify-'.$level);
			}
		}
		return $return;
	}

	public function success(string $message)
	{
		$this->put('success', $message);
	}

	public function info(string $message)
	{
		$this->put('info', $message);
	}

	public function warning(string $message)
	{
		$this->put('warning', $message);
	}

	public function danger(string $message)
	{
		$this->put('danger', $message);
	}
}