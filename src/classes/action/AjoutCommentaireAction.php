<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class AjoutCommentaireAction extends Action
{

    public function execute(): string
    {
        $html = '';
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }
        $q1 = $db->query("SELECT commentaire from serieComNote where id_user = " . $_SESSION['id'] . " AND id_serie = 1");
        if(!$d1=$q1->fetch()) {
            if ($this->http_method === 'GET') {
                $html .= <<<END
            <form method="post" action="?action=signin">
                <label>Commentaire :<input type="text" name="commentaire" placeholder="<commentaire>"></label>
                <button type="submit">Connexion</button>
            </form>
        END;
            } else { // POST
                $com = filter_var($_POST['commentaire'], FILTER_SANITIZE_STRING);
                $q2 = $db->query("SELECT * from serieComNote where id_user = " . $_SESSION['id'] . " AND id_serie = 1");
                if($q2['id_user'] == null){
                    $insert = $db->exec("INSERT INTO serieComNote(id_user,id_serie,commentaire) VALUES(" . $_SESSION['id'] . ",1," . $com );
                }else{
                    $insert = $db->exec("INSERT INTO serieComNote(commentaire) where id_user =" . $_SESSION['id'] . "AND id_serie = 1" . " VALUE(" . $com . ")");
                }
                $html = "commentaire ajoutée";
            }
        }else{
            $html = "vous avez deja mis un commentaire";
        }
        return $html;
    }
}