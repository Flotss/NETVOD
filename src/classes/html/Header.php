<?php

namespace iutnc\NetVOD\html;

class Header
{

    public function execute(): string
    {
        $deconnexion = '';
        $gestion = '';
        $accueil ='';
        $research = '';
        if (isset($_SESSION['id'])) {
            $accueil = '<a href="?action=accueil" class="accueil">Retour à l’accueil</a>';
            $deconnexion = '<a href="?action=deconnexion" class="deconnexion">Déconnexion</a>';
            $gestion = '<a class="gestionCompte" href="?action=gestionCompte">Gestion du compte</a></li>';
            $research = '<a class="research" href="?action=research">Recherche</a></li>';
        }

        $html = <<<END
        <header>
            <div class="logo">
                <a href="?action=accueil" style="text-decoration: none;
                                    font-size: 5em">NetVOD</a>
            </div>
            $accueil
            $gestion   
            $research
            $deconnexion
        </header>
        END;
        return $html;
    }
}