<?php

namespace iutnc\NetVOD\dispatch;
use iutnc\NetVOD\action;
use iutnc\NetVOD\Redirect\Redirection;
use iutnc\NetVOD\html;


class DispatcherPageSerie
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
            //Cas ou l'utilisateur clique sur le bouton de retour à l'accueil
            case 'accueil':
                $act = new action\AccueilUtilisateurAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton de gestion de compte
            case 'gestionCompte':
                Redirection::redirection('GestionCompte.php');
                break;
            //Cas ou l'utilisateur clique sur le bouton de deconnexion
            case 'deconnexion':
                $act = new action\DeconnexionAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton d'affichage de commentaire
            case 'affichage-commentaire':
                $act = new action\AffichageCommentaireAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton d'ajout aux préférences
            case 'ajout-preference':
                $act = new action\AjoutPreferenceAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton de suppression des préférences
            case 'supr-preference':
                $act = new action\SuprPreferenceAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur un épisode
            case 'affichage-episode':
                setcookie('nomEpisode', $_GET['titre-episode'], time() + 3600, '/');
                $act = new action\AffichageEpisodeAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton de recherche
            case 'research':
                Redirection::redirection('AccueilUtilisateur.php?action=research');
                break;
            default:
                break;
        }
        //affichage de la page de base
        $act = new action\AffichageDetailleeSerieAction();
        $htmlBase = $act->execute();

        $this->renderPage($html, $htmlBase);
    }


    private function renderPage($html, $htmlBase)
    {
        $act = new html\Header();
        $header = $act->execute();

        echo <<<END
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel=stylesheet href="src/CSS/cssDefault.css">
                    <link rel=stylesheet href="src/CSS/cssPageSerie.css">
                    <title>NetVOD</title>
                </head>
                <body>
                    <div class="container">
                        $header
                        $htmlBase
                        $html
                    </div>
                </body>
            </html>
        END;
    }

}