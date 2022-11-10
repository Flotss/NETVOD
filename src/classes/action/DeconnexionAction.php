<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

/**
 * Class AccueilUtilisateurAction
 */
class DeconnexionAction extends Action
{

    /**
     * Méthode qui permet de se déconnecter de se rediriger vers la page d'accueil
     * @return string Aucune donnée
     */
    public function execute(): string
    {
        session_destroy();
        unset($_SESSION['id']);
        Redirection::redirection('index.php');
        return '';
    }
}