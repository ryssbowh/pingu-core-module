<?php

use Composer\Script\Event;

class ComposerScripts
{
	public static function postInstall(Event $event)
	{
		dump($event);
	}
}