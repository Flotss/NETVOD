<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\auth\Auth;
use iutnc\NetVOD\Redirect\Redirection;

class InscriptionAction extends Action
{
    public function execute() : string
    {
        if ($this->http_method === 'GET') {
            return $this->getForm();
        } else { // POST
            try {
                Auth::register($_POST['email'], $_POST['pass']);
                Redirection::redirection('AccueilUtilisateur');
            } catch (\iutnc\NetVOD\AuthException\AuthException $e) {
                $html = $this->getForm();
                $html .= "<h4>erreur lors de la création du compte : {$e->getMessage()}";
            }

            return $html;
        }
    }



    private function getForm(): string
    {
        return <<<END
                <div class="enteteAccueil">
                    <label>Se connecter</label>
                </div>
                <form method="post" action="?action=connexion">
                        <label> Email :  <input type="email" name="email" placeholder="<email>"> </label>
                        <label> Mot de passe :  <input type="password" name="password" placeholder = "<mot de passe>"> </label>
                        
                        <button type="submit"> Connexion </button>
                </form>
                <div class="AutreChoixAccueil">
                    <label>Pas de compte ?</label>
                    <a href="?action=inscription">Créer Un Compte</a>
                </div>
            END;
    }
}