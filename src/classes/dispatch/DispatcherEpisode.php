<?php

namespace iutnc\NetVOD\dispatch;
use iutnc\NetVOD\action;
use iutnc\NetVOD\AuthException\AuthException;
use iutnc\NetVOD\db\ConnectionFactory;
use iutnc\NetVOD\Redirect\Redirection;
use iutnc\NetVOD\html;



class DispatcherEpisode
{
    protected ?string $action = null;

    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }


    public function run(): void
    {
        // SECURITE
        if (! (isset($_SESSION['id']))) Redirection::redirection('index');


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
            case 'ajout-commentaire':
                $act = new action\AjoutCommentaireAction();
                $html .= $act->execute();
                break;
            case 'ajout-note':
                $act = new action\AjoutNoteAction();
                $html .= $act->execute();
                break;
            case 'gestionCompte':
                $act = new action\GestionCompteAction();
                $html .= $act->execute();
                break;
            default:
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

        try{
            $db = ConnectionFactory::makeConnection();
        }catch(\PDOException $e){
            throw new AuthException($e->getMessage());
        }
        if(isset($_COOKIE['nomEpisode'])){
            $titre = $_COOKIE['nomEpisode'];
            $titre = str_replace("'","\'",$titre);
        }else {
            Redirection::redirection('PageSerie');
        }

        $q1 = $db->query("SELECT file,numero,duree,resume,episode.titre,serie.titre AS serieTitre from episode,serie where episode.serie_id = serie.id AND episode.titre = '" . $titre . "'");
        $d1=$q1->fetch();

        // Chemin pour trouver le fichier
        $scriptNameExplode = explode('/', $_SERVER['SCRIPT_NAME']);
        $chemin = '';
        for ($k = 0; $k < count($scriptNameExplode) - 1; $k++) {
            $chemin .= $scriptNameExplode[$k] . '/';
        }
        $episode = <<<END
            <h4> {$d1['serieTitre']} - Episode {$d1['numero']} </h4>
            <video controls width="1080">
                    <source src="{$chemin}/ressource/video/{$d1['file']}" type="video/mp4">
            </video>
            <p> Durée : {$d1['duree']} secondes </p>  
            <p> Résumé : {$d1['resume']} </p>
        END;

        $q2 = $db->query("SELECT etat from etatserie where id_user = " . $_SESSION['id'] . " AND id_serie = (SELECT serie_id from episode where titre = '" . $titre . "')");
        if(!$d2=$q2->fetch()){
            $db->exec("INSERT INTO etatserie(id_user,id_serie,etat) VALUES(" . $_SESSION['id'] . ",(SELECT serie_id from episode where titre = '" . $titre . "'),'en cours')");
        }



        $comment = "<p>Vous aimez l'épisode " . $_SESSION['user'] . " ? n'ésitait pas a commenter et laisser une note!</p>";
        echo <<<END
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel=stylesheet href="src/CSS/cssDefault.css">
                    <link rel=stylesheet href="src/CSS/affichageSerie.css">
                    <title>NetVOD</title>
                </head>
                <body>
                    <div class="container">
                        <div class="content">
                            $header
                           <a href="PageSerie.php">Retour</a>
                           $episode
                           $html
                           $comment
                           <a href="?action=ajout-commentaire&titre={$titre}" style="color: darkorange; text-decoration: none">-Commenter!</a><br>
                           <a href="?action=ajout-note&titre={$titre}" style="color: darkorange; text-decoration: none">-Noter!</a>
                           $footer
                        </div>
                     </div>
                   
                </body>
            </html>
        END;
    }

}