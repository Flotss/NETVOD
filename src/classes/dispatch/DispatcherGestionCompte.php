<?php

namespace iutnc\NetVOD\dispatch;
use iutnc\NetVOD\action;
use iutnc\NetVOD\Redirect\Redirection;
use iutnc\NetVOD\html;


class DispatcherGestionCompte
{
    protected ?string $action = null;

    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }


    public function run(): void
    {
        // SECURITE
        if (! (isset($_SESSION['id']))) Redirection::redirection('index.php');

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
            case 'research':
                $act = new action\ResearchAction();
                $html .= $act->execute();
                break;
            default:
                $act = new action\GestionCompteAction();
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
                    <link href="src/CSS/cssDefault.css" rel="stylesheet">
                    <link href="src/CSS/affichageSerie.css" rel="stylesheet">
                    <title>NetVOD</title>
                </head>
                <body>
                    <div class="container">
                        $header
                        $html
                        $footer
                    </div>
                </body>
            </html>
        END;
    }

}