<?php
namespace Pingu\Core\Exceptions;

use Pingu\Core\Support\Accessor;

class SettingsException extends \Exception
{
    public static function repositoryNotFound(string $repo)
    {
        return new static("Settings repository $name doesn't exist");
    }
}