<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

class AffichageEpisodeAction extends Action
{

    public function execute(): string
    {
        Redirection::redirection('Episode');
        exit;
        $html = ' ';
        return $html;
    }
}