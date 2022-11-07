<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\auth\Auth;

class InscriptionAction extends Action
{
    public function execute() : string
    {

        if ($this->http_method === 'GET') {
            return <<<END
                    <form method="post" action="?action=add-user">
                        <label>
                            Email : <input type="email" name="email" placeholder="<email>">
                        </label>
                        <label>
                            Mots de passe :<input type="password" name="pass" placeholder="<mot de passe>">
                        </label>
                        <button type="submit">s'enregistrer</button>
                    </form>
                END;
        } else {
            try {
                Auth::register($_POST['email'], $_POST['pass']);
                $html = "<h4>compte créé avec succés - vous pouvez vous connecter</h4>>";
            } catch (\iutnc\NetVOD\AuthException\AuthException $e) {
                $html = "<h4>erreur lors de la création du compte : {$e->getMessage()}";
            }

            return $html;
        }
    }
}