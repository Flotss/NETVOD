<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

class AccueilUtilisateurAction extends Action
{

    public function execute(): string
    {
        Redirection::redirection('AccueilUtilisateur.php');
        return '';
    }
}