<?php
namespace iutnc\NetVOD\Redirect;

/**
 * Class Redirection
 * Cette classe permet de gérer les redirections
 */
class Redirection
{
    /**
     * Methode de redirection vers une page
     * @param $page string page vers laquelle rediriger
     * @return void
     */
    public static function redirection(string $url): void
    {
        header("Location: $url");
        die();
    }
}