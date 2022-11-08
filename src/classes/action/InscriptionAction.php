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
                Auth::register($_POST['email'], $_POST['password'], $_POST['password2'],$_POST['nom'], $_POST['prenom']);
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
                    <label>S'inscrire</label>
                </div>
                <form method="post" action="?action=inscription">
                        <label> Email :  <input type="email" name="email" placeholder="<email>"> </label>
                        <label> Mot de passe :  <input type="password" name="password" placeholder = "<mot de passe>"> </label>
                        <label> Confirmer le mot de passe :  <input type="password" name="password2" placeholder = "<mot de passe>"> </label>
                        <label> Nom : <input type="text" name="nom" placeholder="<nom>"> </label>
                        <label> Prénom : <input type="text" name="prenom" placeholder="<prenom>"> </label>
                        <button type="submit"> S'enregistrer </button>
                </form>
                <div class="AutreChoixAccueil">
                    <label>Vous avez un compte ?</label>
                    <a href="?action=connexion">Se connecter</a>
                </div>
            END;
    }
}