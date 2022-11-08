<?php

namespace iutnc\NetVOD\html;

class Header
{

    public function execute(): string
    {
        $deconnexion = '';
        if (isset($_SESSION['id'])) {
            $deconnexion = '<a href="?action=deconnexion" class="deconnexion">Déconnexion</a>';
        }

        $html = <<<END
        <header>
            <div class="logo">
                <a href="?action=accueil" style="text-decoration: none;
                                    font-size: 5em">NetVOD</a>
            </div>
            $deconnexion
        </header>
        END;
        return $html;
    }
}