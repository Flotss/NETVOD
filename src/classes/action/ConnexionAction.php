<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\auth\Auth;
use iutnc\NetVOD\Redirect\Redirection;


class ConnexionAction extends Action
{
    public function execute(): string{
        $html = '';
        if ($this->http_method === 'GET'){
            $html .= <<<END
                <form method="post" action="?action=signin">
                    <label>Email :<input type="email" name="email" placeholder="<email>"></label>
                    <label>passwd :<input type="password" name="pass" placeholder="<mot de passe>"></label>
                    <button type="submit">Connexion</button>
                </form>
            END;
        }else{ // POST
            try{
                Auth::authenticate($_POST['email'], $_POST['pass']);

                Redirection::redirection('AccueilUtilisateur');
            }catch(\iutnc\NetVOD\AuthException\AuthException $e){
                $html .= "<h4> échec authentification : {$e->getMessage()}</h4>";
            }
        }
        return html;
    }
}