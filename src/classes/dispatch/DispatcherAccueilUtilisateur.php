<?php

namespace iutnc\NetVOD\dispatch;
use iutnc\deefy\action;


class DispatcherAccueilUtilisateur
{
    protected ?string $action = null;

    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }


    public function run(): void
    {
        $html = '';
        switch ($this->action) {
            case 'affichage-serie':
                $act = new action\AffichageSerieAction();
                $html .= $act->execute();
                break;
            case 'deconnexion':
                $act = new action\DeconnexionAction();
                $html .= $act->execute();
            break;
            default:
                break;
        }

        $this->renderPage($html);
    }


    private function renderPage($html)
    {
        echo <<<END
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>NetVOD</title>
                </head>
                <body>
                    
                </body>
            </html>
        END;
    }

}