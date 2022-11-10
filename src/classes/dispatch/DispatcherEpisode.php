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

    /**
     * construit le Dispacher
     */
    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }

    /**
     * @return void
     * @throws AuthException
     * Lance le dispacher qui agis en fonction de action
     */
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
            //Cas ou l'utilisateur clique sur le bouton de deconnexion
            case 'deconnexion':
                $act = new action\DeconnexionAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton d'ajout de commentaire
            case 'ajout-commentaire':
                $act = new action\AjoutCommentaireAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton d'ajout de note
            case 'ajout-note':
                $act = new action\AjoutNoteAction();
                $html .= $act->execute();
                break;
            //Cas ou l'utilisateur clique sur le bouton de gestion de compte
            case 'gestionCompte':
                Redirection::redirection('GestionCompte.php');
                break;
            //Cas ou l'utilisateur clique sur le bouton de recherche
            case 'research':
                Redirection::redirection('AccueilUtilisateur.php?action=research');
                break;
            default:
                break;
        }

        $this->renderPage($html);
    }

    /**
     * @param $html
     * @return void
     * @throws AuthException
     * affiche la page web contenant le resultat de action
     */
    private function renderPage($html)
    {
        $act = new html\Header();
        $header = $act->execute();


        try{
            $db = ConnectionFactory::makeConnection();
        }catch(\PDOException $e){
            throw new AuthException($e->getMessage());
        }
        if(isset($_COOKIE['nomEpisode'])){
            $titre = $_COOKIE['nomEpisode'];
            $titre = str_replace("'","\'",$titre);
        }else {
            Redirection::redirection('PageSerie.php');
        }

        $statementEpisode = $db->query("SELECT file,numero,duree,resume, episode.titre as episodeTitre ,serie.titre AS serieTitre 
                                                        from episode
                                                        INNER JOIN serie ON serie.id = episode.serie_id
                                                        where  episode.titre = '" . $titre . "'");
        $resEpisode = $statementEpisode->fetch();

        // Chemin pour trouver le fichier
        $scriptNameExplode = explode('/', $_SERVER['SCRIPT_NAME']);
        $chemin = '';
        for ($k = 0; $k < count($scriptNameExplode) - 1; $k++) {
            $chemin .= $scriptNameExplode[$k] . '/';
        }


        $episode = <<<END
            <h4> {$resEpisode['serieTitre']} - Episode {$resEpisode['numero']} </h4>
            <video controls width="1080">
                    <source src="{$chemin}/ressource/video/{$resEpisode['file']}" type="video/mp4">
            </video>
            <p> Durée : {$resEpisode['duree']} secondes </p>  
            <p> Résumé : {$resEpisode['resume']} </p>
        END;

        //vérifie si l'utilisateur est dans la table des épisode visionnée avec cette série si non il est ajoutée
        $q2 = $db->query("SELECT * from episodeVisionnee where id_user = " . $_SESSION['id'] . " AND id_episode = ANY(SELECT id from episode where serie_id = (SELECT serie_id from episode where titre = '" . $titre . "'))");
        if(!$d2=$q2->fetch()){
            $q5 = $db->query("SELECT id FROM episode where serie_id = (SELECT serie_id from episode where titre = '" . $titre . "')");
            while ($d5=$q5->fetch()) {
                $db->exec("INSERT INTO episodeVisionnee(id_user,id_episode,etat) VALUE(" . $_SESSION['id'] . "," . $d5['id'] . ",0)");
            }
        }

        //marque l'épisode comme vu par l'utilisateur
        $db->exec("UPDATE episodeVisionnee SET etat = 1 WHERE id_user = " . $_SESSION['id'] . " AND id_episode = (SELECT id from episode where titre = '" . $titre ."')");


        //si la serie n'a pas d'état pour l'utilisateur elle est mis en cours
        $q3 = $db->query("SELECT etat from etatSerie where id_user = " . $_SESSION['id'] . " AND id_serie = (SELECT serie_id from episode where titre = '" . $titre . "')");

        // Ajout de la série en cours
        if(!$d3=$q3->fetch()){
            $statementSerieID = $db->query("SELECT serie_id from episode where titre = '" . $titre . "'");
            $resSerieID = $statementSerieID->fetch();
            $idUser = $_SESSION['id'];
            $db->query("INSERT INTO etatSerie(id_user,id_serie,etat) VALUES({$idUser},{$resSerieID['serie_id']},'en cours')");
        }


        //si tous les épisodes ont été vus la série est marqué comme visionné
        $q4 = $db->query("SELECT * from episodeVisionnee where id_user = " . $_SESSION['id'] . " AND id_episode = ANY(SELECT id from episode where serie_id = (SELECT serie_id from episode where titre = '" . $titre . "')) AND etat != 1");
        if(!$d4=$q4->fetch()){
            $db->exec("Update etatSerie SET etat = 'visionnee' where id_user = " . $_SESSION['id'] . " AND id_serie = (SELECT serie_id from episode where titre = '" . $titre . "')");
        }

        $comment = "<p>Vous aimez l'épisode " . $_SESSION['user'] . " ? n'hésitez pas a commenter et laisser une note!</p>";
        //HTML de la page
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
                           <a href="PageSerie.php">Retour au série</a>
                           $episode
                           $html
                           $comment
                           <a href="?action=ajout-commentaire&titre={$titre}" style="color: darkorange; text-decoration: none">-Commenter!</a><br>
                           <a href="?action=ajout-note&titre={$titre}" style="color: darkorange; text-decoration: none">-Noter!</a>
                        </div>
                     </div>
                   
                </body>
            </html>
        END;
    }

}