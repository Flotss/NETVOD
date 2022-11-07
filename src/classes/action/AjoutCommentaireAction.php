<?php

namespace iutnc\NetVOD\action;

class AjoutCommentaireAction extends Action
{

    public function execute(): string
    {
        $html = '';
        if(false /* utilisateur n'a pas lis de commentaire */) {
            if ($this->http_method === 'GET') {
                $html .= <<<END
                <form method="post" action="?action=signin">
                    <label>Commentaire :<input type="text" name="commentaire" placeholder="<commentaire>"></label>
                    <button type="submit">Connexion</button>
                </form>
            END;
            } else { // POST
                $com = filter_var($_POST['commentaire'], FILTER_SANITIZE_STRING);
                try {
                    $db = ConnectionFactory::makeConnection();
                } catch (DBExeption $e) {
                    throw new AuthException($e->getMessage());
                }
                $insert = $db->exec("INSERT INTO ");//ajouter un bd pour les com
                $html = "commentaire ajoutée";
            }
        }else{
            $html = "vous avez deja commentée cette episode";
        }
        return $html;
    }
}