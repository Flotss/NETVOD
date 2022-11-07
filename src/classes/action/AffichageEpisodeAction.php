<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\dispatch\DispatcherEpisode;

class AffichageEpisodeAction extends Action
{

    public function execute(): string
    {
        header(Episode.php);
        exit;
        $html = ' ';
        return $html;
    }
}