<?php

namespace Pingu\Core\Console;

use Composer\Script\Event;

class ComposerScripts
{
	public static function postInstall(Event $event)
	{
		exec('./artisan install');
	}

	public static function postUpdate(Event $event)
	{
		exec('./artisan update');
	}
}