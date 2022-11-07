<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

class accueilAction extends Action
{

    public function execute(): string
    {
        Redirection::redirection('AccueilUtilisateur.php');
        return '';
    }
}