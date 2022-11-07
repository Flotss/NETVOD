<?php

namespace iutnc\NetVOD\action;

class AjoutNoteAction extends Action
{

    public function execute(): string
    {
        $html = '';
//        if(/* utilisateur n'a pas deja mis de note */) {
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
                $insert = $db->exec("INSERT INTO serieComNote ");//ajouter un bd pour les note
                $html = "commentaire ajoutée";
            }
//        }else{
//            $html = "vous avez deja noté cette episode";
//        }
        return $html;
    }
}