<?php

namespace iutnc\NetVOD\html;

class Header
{

    public function execute(): string
    {
        $deconnexion = '';
        $gestion = '';
        if (isset($_SESSION['id'])) {
            $acueil = '<a href="?action=accueil" class="accueil">Retour à l’accueil</a>';
            $deconnexion = '<a href="?action=deconnexion" class="deconnexion">Déconnexion</a>';
            $gestion = '<a class="gestionCompte" href="?action=gestionCompte">Gestion du compte</a></li>';
        }

        $html = <<<END
        <header>
            <div class="logo">
                <a href="?action=accueil" style="text-decoration: none;
                                    font-size: 5em">NetVOD</a>
            </div>
            $acueil
            $gestion   
            $deconnexion
        </header>
        END;
        return $html;
    }
}