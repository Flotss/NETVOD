<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

/**
 * Class AjoutCommentaireAction
 */
class AjoutCommentaireAction extends Action
{

    /**
     * Methode execute qui permet d'ajouter un commentaire
     * @return string $html code html de la page
     */
    public function execute(): string
    {
        $html = '';

        // Verification que le nom de l'episode est bien renseigné
        // Sinon redirection vers la page des séries
        if(isset($_COOKIE['nomEpisode'])){
            $titre = $_COOKIE['nomEpisode'];
            $titre = str_replace("'","\'",$titre);
        }else {
            Redirection::redirection('PageSerie.php');
        }

        // Connexion à la base de données
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }

        // Affichage du commentaire de l'utilisateur
        $q1 = $db->query("SELECT commentaire from serieComNote, episode where serieComNote.id_serie = episode.serie_id AND id_user = " . $_SESSION['id'] . " AND titre = '" . $titre . "' AND commentaire IS NOT NULL");
        if($d1=$q1->fetch()){
            $html = "Votre commentaire actuelle : " . $d1['commentaire'];
        }

        //Formulaire ou entrer le commentaire
        if ($this->http_method === 'GET') { // GET Affichage du formulaire
            $html .= <<<END
                <form method="post" action="?action=ajout-commentaire">
                    <label>Commentaire :<input type="text" name="commentaire" placeholder="<commentaire>"></label>
                    <button type="submit">Commenter</button>
                </form>
            END;
        } else { // POST
            //Sanetisation puis ajout du commentaire
            $com = filter_var($_POST['commentaire'], FILTER_SANITIZE_STRING);

            // Verification que l'utilisateur a déjà commenté
            $q2 = $db->query("SELECT * from serieComNote, episode where serieComNote.id_serie = episode.serie_id AND id_user = " . $_SESSION['id'] . " AND titre = '" . $titre . "'");
            if(!$d2 = $q2->fetch()){ // Si l'utilisateur n'a pas encore commenté, ajout du commentaire
                $insert = $db->exec("INSERT INTO serieComNote(id_user,id_serie,commentaire) VALUES(" . $_SESSION['id'] . ", (SELECT serie_id from episode where titre = '" . $titre . "') ,'" . $com . "')");
            }else{ // Si l'utilisateur a déjà commenté, mise a jour du commentaire
                $insert = $db->exec("UPDATE serieComNote SET commentaire = '" . $com . "' where id_user =" . $_SESSION['id'] . " AND id_serie = (SELECT serie_id from episode where titre = '" . $titre . "')");
            }
            $html = "Votre commentaire \"$com\" a été mis a jour avec succès";
        }
        return $html;
    }
}