<?php

namespace iutnc\NetVOD\action;

class headerAction extends Action
{

    public function execute(): string
    {
        $html = <<<END
        <header>
            <div class="logo">
                <a href="?action=accueil" style="text-decoration: none;
                                    font-size: 5em">NetVOD</a>
            </div>
            <div class="Deconnection">
                <ul>
                    <li><a href="?action=deconnexion">Accueil</a></li>
                </ul>
            </div>
        </header>
        END;
        return $html;
    }
}