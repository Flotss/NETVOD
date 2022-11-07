<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\auth\Auth;
use iutnc\NetVOD\Redirect\Redirection;


class ConnexionAction extends Action
{
    public function execute(): string
    {
        $html = '';
        if ($this->http_method === 'GET') {
            $html .= <<<END
                <form method="post" action="?action=connexion">
                        <label> User :  <input type="User" name="user" placeholder="user"> </label>
                        <label> Passwd :  <input type="password" name="passwd" placeholder = "<mot de passe>"> </label>
                        
                        <button type="submit"> Connexion </button>
                </form>
                <div class="inscription">
                <label>Pas de compte ?</label>
                    <a href="?action=inscription">Créer Un Compte</a>
                </div>
            END;
        } else { // POST
            try {
                Auth::authenticate($_POST['user'], $_POST['passwd']);

                Redirection::redirection('AccueilUtilisateur');
            } catch (\iutnc\NetVOD\AuthException\AuthException $e) {
                $html .= "<h4> échec authentification : {$e->getMessage()}</h4>";
            }
//        }
        }
        return html;
    }
}