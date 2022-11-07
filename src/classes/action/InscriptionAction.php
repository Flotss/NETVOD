<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\auth\Auth;
use iutnc\NetVOD\Redirect\Redirection;

class InscriptionAction extends Action
{
    public function execute() : string
    {
        if ($this->http_method === 'GET') {
            return <<<END
                    <div class="enteteAccueil">
                        <label>Créer un compte</label>
                    </div>
                    <form method="post" action="?action=inscription">
                        <label>
                            Email : <input type="email" name="email" placeholder="<email>">
                        </label>
                        <label>
                            Mots de passe :<input type="password" name="pass" placeholder="<mot de passe>">
                        </label>
                        <button type="submit">s'enregistrer</button>
                    </form>
                    <div class="AutreChoixAccueil">
                    <label>Vous avez déjà un compte ?</label>
                        <a href="?action=connexion">Se Connecter</a>
                    </div>
                END;
        } else { // POST
            try {
                Auth::register($_POST['email'], $_POST['pass']);
                Redirection::redirection('AccueilUtilisateur', $this);
            } catch (\iutnc\NetVOD\AuthException\AuthException $e) {
                $html = "<h4>erreur lors de la création du compte : {$e->getMessage()}";
            }

            return $html;
        }
    }
}