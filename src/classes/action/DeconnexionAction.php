<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\Redirect\Redirection;

class DeconnexionAction extends Action
{

    public function execute(): string
    {
        session_destroy();
        echo $this->hostname."<br>";
        echo $this->script_name;
        Redirection::redirection('index', $this);

        return '';
    }
}