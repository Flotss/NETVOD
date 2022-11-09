<?php

namespace iutnc\NetVOD\action;
use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

class AjoutNoteAction extends Action
{

    public function execute(): string
    {
        $html = '';
        if(isset($_COOKIE['nomEpisode'])){
            $titre = $_COOKIE['nomEpisode'];
            $titre = str_replace("'","\'",$titre);
        }else {
            Redirection::redirection('PageSerie.php');
        }
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }
        $q1 = $db->query("SELECT note from serieComNote, episode where serieComNote.id_serie = episode.serie_id AND id_user = " . $_SESSION['id'] . " AND titre = '" . $titre . "' AND note IS NOT NULL");
        if($d1=$q1->fetch()){
            $html = "Votre note actuelle : " . $d1['note'];
        }
        if ($this->http_method === 'GET') {
            $html .= <<<END
                <form method="post" action="?action=ajout-note">
                    <label>Note :<input type="number" name="note" placeholder="<note>"></label>
                    <button type="submit">Noter</button>
                </form>
            END;
        } else { // POST
            $note = filter_var($_POST['note'], FILTER_SANITIZE_NUMBER_INT);
            if($note <= 5 && $note >= 0) {
                try {
                    $db = ConnectionFactory::makeConnection();
                } catch (DBExeption $e) {
                    throw new AuthException($e->getMessage());
                }
                $q2 = $db->query("SELECT * from serieComNote, episode where serieComNote.id_serie = episode.serie_id AND id_user = " . $_SESSION['id'] . " AND titre = '" . $titre . "'");
                if (!$d2 = $q2->fetch()) {
                    $insert = $db->exec("INSERT INTO serieComNote(id_user,id_serie,note) VALUES(" . $_SESSION['id'] . ", (SELECT serie_id from episode where titre = '" . $titre . "') ," . $note . ")");
                } else {
                    $insert = $db->exec("Update serieComNote SET note = " . $note . " where id_user =" . $_SESSION['id'] . " AND id_serie = (SELECT serie_id from episode where titre = '" . $titre . "')");
                }
                $html = "Note ajoutée";
            }else{
                $html = "La note doit etre entre 0 et 5";
            }
        }
        return $html;
    }
}