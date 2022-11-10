<?php

namespace iutnc\NetVOD\dispatch;

use iutnc\NetVOD\action;
use iutnc\NetVOD\html;
use iutnc\NetVOD\Redirect\Redirection;

class Dispatcher
{
    protected ?string $action = null;

    /**
     * construit le dispacher
     */
    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }

    /**
     * @return void
     * lance le dispacher qui verifie l'action et agis en consequence
     */
    public function run(): void
    {
        if (isset($_SESSION['id'])) Redirection::redirection('AccueilUtilisateur.php');

        $html = '';
        switch ($this->action) {
            //cas ou l'utilisateur clique sur le bouton pour s'inscrire
            case 'inscription':
                $act = new action\InscriptionAction();
                $html .= $act->execute();
                break;
                //Cas ou l'utilisateur se connecte
            default:
                $act = new action\ConnexionAction();
                $html .= $act->execute();
                break;
        }

        $this->renderPage($html);
    }

    /**
     * @param $html
     * @return void
     * Affiche la page contenant le resultat de l'action executait
     */
    private function renderPage($html)
    {
        $act = new html\Header();
        $header = $act->execute();

        echo <<<END
        <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <link rel=stylesheet href="src/CSS/cssDefault.css">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>NetVOD</title>
                </head>
                <body>
                    $header
                    $html
                </body>
            </html>
        END;
    }

}