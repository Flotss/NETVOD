<?php

namespace iutnc\NetVOD\action;

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