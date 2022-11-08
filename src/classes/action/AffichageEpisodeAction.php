<?php

namespace iutnc\NetVOD\action;

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