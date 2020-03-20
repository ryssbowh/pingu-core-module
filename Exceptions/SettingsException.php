<?php
namespace Pingu\Core\Exceptions;

use Pingu\Core\Support\Accessor;

class SettingsException extends \Exception
{
    public static function repositoryNotFound(string $repo)
    {
        return new static("Settings repository $repo doesn't exist");
    }

    public static function alreadyDefined(string $name)
    {
        return new static("Can't create setting $name; it already exist");
    }
}