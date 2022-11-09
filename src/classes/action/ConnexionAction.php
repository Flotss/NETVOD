<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\auth\Auth;
use iutnc\NetVOD\AuthException\AuthException;
use iutnc\NetVOD\Redirect\Redirection;


class ConnexionAction extends Action
{
    public function execute(): string
    {
        $html = '';
        if ($this->http_method === 'GET') {
            $html .= $this->getForm();
        } else { // POST
            try {
                if (!(isset($_POST['email']) && isset($_POST['password']))) {
                    throw new AuthException("Erreur : email ou mot de passe non renseigné");
                }
                // Filtre les entrées
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);


                $res = Auth::authenticate($email, $password);
                echo $res;
                if ($res) {
                    Redirection::redirection('AccueilUtilisateur');
                } else {
                    throw new AuthException("Erreur : email ou mot de passe incorrect");
                }

            } catch (\iutnc\NetVOD\AuthException\AuthException $e) {
                $html .= $this->getForm();
                $html .= "<h4> échec authentification : {$e->getMessage()}</h4>";
            }
        }
        return $html;
    }

    private function getForm(): string
    {
        return <<<END
                <div class="enteteAccueil">
                <label>Se connecter</label>
                <form method="post" action="?action=connexion">
                        <label> Email :  <input type="email" name="email" placeholder="<email>"> </label>
                        <label> Mot de passe :  <input type="password" name="password" placeholder = "<mot de passe>"> </label>
                        
                        <button type="submit"> Connexion </button>
                        <div>
                            <label>Pas de compte ?</label>
                            <a href="?action=inscription">Créer Un Compte</a>
                        </div>
                </form>
                </div>

            END;
    }
}