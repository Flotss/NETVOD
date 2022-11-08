<?php

namespace iutnc\NetVOD\dispatch;
use iutnc\NetVOD\action;



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
            ///Affichage Serie???
          /*  case 'affichage-serie':
                $act = new action\AffichageSerieAction();
                $html .= $act->execute();
                echo 'hi';
                break;*/
            case 'accueil':
                $act = new action\AccueilUtilisateurAction();
                $html .= $act->execute();
                echo 'hi2';
                break;
            case 'deconnexion':
                $act = new action\DeconnexionAction();
                $html .= $act->execute();
            break;
            default:
                $act = new action\AffichageSerieAction();
                $html .= $act->execute();
                break;
        }

        $this->renderPage($html);
    }


    private function renderPage($html)
    {
        $act = new action\headerAction();
        $header = $act->execute();

        $act = new action\footerAction();
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