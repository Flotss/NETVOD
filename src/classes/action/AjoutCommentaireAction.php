<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class AjoutCommentaireAction extends Action
{

    public function execute(): string
    {
        $html = '';
        if(isset($_COOKIE['nomEpisode'])){
            $titre = $_COOKIE['nomEpisode'];
            $titre = str_replace("'","\'",$titre);
        }else {
            Redirection::redirection('PageSerie');
        }
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }
        $q1 = $db->query("SELECT commentaire from serieComNote, episode where serieComNote.id_serie = episode.serie_id AND id_user = " . $_SESSION['id'] . " AND titre = '" . $titre . "' AND commentaire IS NOT NULL");
        if($d1=$q1->fetch()){
            $html = "Votre commentaire actuelle : " . $d1['commentaire'];
        }
        if ($this->http_method === 'GET') {
            $html .= <<<END
                <form method="post" action="?action=ajout-commentaire">
                    <label>Commentaire :<input type="text" name="commentaire" placeholder="<commentaire>"></label>
                    <button type="submit">Commenter</button>
                </form>
            END;
        } else { // POST
            $com = filter_var($_POST['commentaire'], FILTER_SANITIZE_STRING);
            $q2 = $db->query("SELECT * from serieComNote, episode where serieComNote.id_serie = episode.serie_id AND id_user = " . $_SESSION['id'] . " AND titre = '" . $titre . "'");
            if(!$d2 = $q2->fetch()){
                $insert = $db->exec("INSERT INTO serieComNote(id_user,id_serie,commentaire) VALUES(" . $_SESSION['id'] . ", (SELECT serie_id from episode where titre = '" . $titre . "') ,'" . $com . "')");
            }else{
                $insert = $db->exec("UPDATE serieComNote SET commentaire = '" . $com . "' where id_user =" . $_SESSION['id'] . " AND id_serie = (SELECT serie_id from episode where titre = '" . $titre . "')");
            }
            $html = "commentaire ajoutée";
        }
        return $html;
    }
}