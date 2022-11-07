<?php
namespace iutnc\NetVOD\Redirect;

use iutnc\NetVOD\action\Action;

class Redirection
{
    public static function redirection(string $url, Action $action): void
    {
        header("Location: $url".'.php');
        die();
    }
}