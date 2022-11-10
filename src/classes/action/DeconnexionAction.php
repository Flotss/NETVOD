<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

class DeconnexionAction extends Action
{

    public function execute(): string
    {
        session_destroy();
        unset($_SESSION['id']);
        Redirection::redirection('index.php');
        return '';
    }
}