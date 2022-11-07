<?php
namespace iutnc\NetVOD\Redirect;


class Redirection
{
    public static function redirection(string $url): void
    {
        header("Location: $url".'.php');
        die();
    }
}