<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

class AffichageEpisodeAction extends Action
{

    public function execute(): string
    {
        Redirection::redirection('Episode.php');
        exit;
        $html = ' ';
        return $html;
    }
}