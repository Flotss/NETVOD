<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

/**
 * Class AccueilUtilisateurAction
 */
class AffichageEpisodeAction extends Action
{

    /**
     * Méthode qui permet de rediriger vers la page d'épisode
     * @return string Aucune donnée
     */
    public function execute(): string
    {
        Redirection::redirection('Episode.php');
        return '';
    }
}