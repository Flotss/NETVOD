<?php

namespace iutnc\NetVOD\action;

class headerAction extends Action
{

    public function execute(): string
    {
        $deconnection = '';
        if (isset($_SESSION['id'])) {
            $deconnection = '<a href="index.php?action=connexion" class="deconnection">Connexion</a>';
        }

        $html = <<<END
        <header>
            <div class="logo">
                <a href="?action=accueil" style="text-decoration: none;
                                    font-size: 5em">NetVOD</a>
            </div>
            $deconnection
        </header>
        END;
        return $html;
    }
}