<?php 

namespace Pingu\Core\Support;

class Plugin
{
    public static $disabled = false;

    public static $target = '';

    public static $order = false;

    public static function register()
    {
        \Compiler::register(static::class, static::$target);
    }
}