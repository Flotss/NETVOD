<?php

namespace iutnc\NetVOD\dispatch;

use iutnc\NetVOD\action;
use iutnc\NetVOD\Redirect\Redirection;
use iutnc\NetVOD\html;

class Dispatcher
{
    protected ?string $action = null;

    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }


    public function run(): void
    {
        if (isset($_SESSION['id'])) Redirection::redirection('AccueilUtilisateur');

        $html = '';
        switch ($this->action) {
            case 'inscription':
                $act = new action\InscriptionAction();
                $html .= $act->execute();
                break; //tous les cas d'inscription sont géré dans InscritpionAction
            case 'deconnexion':
                $act = new action\DeconnexionAction();
                $html .= $act->execute();
                break;
            default:
                $act = new action\ConnexionAction();
                $html .= $act->execute();
                break;
        }

        $this->renderPage($html);
    }


    private function renderPage($html)
    {
        $act = new html\Header();
        $header = $act->execute();

        $act = new html\Footer();
        $footer = $act->execute();
        echo <<<END
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>NetVOD</title>
                </head>
                <body>
                    $header
                    $html
                    $footer
                </body>
            </html>
        END;
    }

}