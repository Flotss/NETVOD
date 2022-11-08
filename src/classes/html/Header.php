<?php

namespace iutnc\NetVOD\html;

class Header
{

    public function execute(): string
    {
        $deconnexion = '';
        $gestion = '';
        if (isset($_SESSION['id'])) {
            $deconnexion = '<a href="?action=deconnexion" class="deconnexion">Déconnexion</a>';
            $gestion = '<a class="gestionCompte" href="?action=gestionCompte">Gestion du compte</a></li>';
        }

        $html = <<<END
        <header>
            <div class="logo">
                <a href="?action=accueil" style="text-decoration: none;
                                    font-size: 5em">NetVOD</a>
            </div>
            $gestion   
            $deconnexion
        </header>
        END;
        return $html;
    }
}