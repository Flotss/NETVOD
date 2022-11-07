<?php

namespace iutnc\NetVOD\dispatch;
use iutnc\NetVOD\action;

class DispatcherPageSerie
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
            case 'accueil':
                $act = new action\AccueilUtilisateurAction();
                $html .= $act->execute();
                break;
            case 'deconnexion':
                $act = new action\DeconnexionAction();
                $html .= $act->execute();
                break;
            case 'affichage-commentaire':
                $act = new action\AffichageCommentaireAction();
                $html .= $act->execute();
                break;
            case 'ajout-preference':
                $act = new action\AjoutPreferenceAction();
                $html .= $act->execute();
                break;
            case 'affichage-episode':
                $act = new action\AffichageEpisodeAction();
                $html .= $act->execute();
                break;
            default:
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