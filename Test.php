<?php 

namespace Pingu\Core;

use PinguFramework\Compiling\Plugin\Plugin;
use Pingu\Core\Settings\Settings;

class Test extends Plugin
{
    public static $target = Settings::class;
}