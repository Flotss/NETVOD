<?php

namespace iutnc\NetVOD\action;
use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

class AjoutNoteAction extends Action
{

    public function execute(): string
    {
        $html = '';
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }
        $q1 = $db->query("SELECT note from serieComNote where id_user = " . $_SESSION['id'] . " AND id_serie = 1");
        if(!$d1=$q1->fetch()){
            if ($this->http_method === 'GET') {
                $html .= <<<END
                <form method="post" action="?action=signin">
                    <label>Note :<input type="number" name="note" placeholder="<note>"></label>
                    <button type="submit">Connexion</button>
                </form>
            END;
            } else { // POST
                $note = filter_var($_POST['note'], FILTER_SANITIZE_NUMBER_INT);
                try {
                    $db = ConnectionFactory::makeConnection();
                } catch (DBExeption $e) {
                    throw new AuthException($e->getMessage());
                }
                $q2 = $db->query("SELECT * from serieComNote where id_user = " . $_SESSION['id'] . " AND id_serie = 1");
                if($q2['id_user'] == null){
                    $insert = $db->exec("INSERT INTO serieComNote(id_user,id_serie,note) VALUES(" . $_SESSION['id'] . ",1," . $note );
                }else{
                    $insert = $db->exec("INSERT INTO serieComNote(note) where id_user =" . $_SESSION['id'] . " AND id_serie = 1" . " VALUE(" . $note . ")");
                }
                $html = "commentaire ajoutée";
            }
        }else{
            $html = "vous avez deja noté cette episode";
        }
        return $html;
    }
}