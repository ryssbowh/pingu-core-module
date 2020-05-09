<?php 

namespace Pingu\Core;

use Pingu\Core\Settings\Settings;
use Pingu\Core\Support\Plugin;

class Test extends Plugin
{
    public static $target = Settings::class;
}