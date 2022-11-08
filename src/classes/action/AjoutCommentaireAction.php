<?php

namespace iutnc\NetVOD\action;

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
        $q1 = $db->query("SELECT commentaire from serieComNote where id_user = " . $_SESSION['id'] . "AND id_serie = " . id_serie);
        $d1=$q1->fetch();
        if(d1 != null) {
            if ($this->http_method === 'GET') {
                $html .= <<<END
            <form method="post" action="?action=signin">
                <label>Commentaire :<input type="text" name="commentaire" placeholder="<commentaire>"></label>
                <button type="submit">Connexion</button>
            </form>
        END;
            } else { // POST
                $com = filter_var($_POST['commentaire'], FILTER_SANITIZE_STRING);
                $q2 = $db->query("SELECT * from serieComNote where id_user = " . $_SESSION['id'] . "AND id_serie = " . id_serie);
                if($q2['id_user'] == null){
                    $insert = $db->exec("INSERT INTO serieComNote(id_user,id_serie,commentaire) VALUES(" . $_SESSION['id'] . "," . id_serie . "," . $com );
                }else{
                    $insert = $db->exec("INSERT INTO serieComNote(commentaire) where id_user =" . $_SESSION['id'] . "AND id_serie = " . id_serie . " VALUE(" . $com . ")");
                }
                $html = "commentaire ajoutée";
            }
        }else{
            $html = "vous avez deja mis un commentaire";
        }
        return $html;
    }
}