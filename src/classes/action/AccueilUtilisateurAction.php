<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

/**
 * Class AccueilUtilisateurAction
 */
class AccueilUtilisateurAction extends Action
{
    /**
     * Méthode qui permet de rediriger vers la page d'accueil de l'utilisateur
     * @return string
     */
    public function execute(): string
    {
        Redirection::redirection('AccueilUtilisateur.php');
        return '';
    }
}