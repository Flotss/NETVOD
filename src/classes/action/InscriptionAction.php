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
                // Filtre les entrées
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
                $password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);
                $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
                $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);


                Auth::register($email, $password, $password2, $nom, $prenom);
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
                <form method="post" action="?action=inscription">
                        <label> Email :  <input type="email" name="email" placeholder="<email>"> </label>
                        <label> Mot de passe :  <input type="password" name="password" placeholder = "<mot de passe>"> </label>
                        <label> Confirmer le mot de passe :  <input type="password" name="password2" placeholder = "<mot de passe>"> </label>
                        <label> Nom : <input type="text" name="nom" placeholder="<nom>"> </label>
                        <label> Prénom : <input type="text" name="prenom" placeholder="<prenom>"> </label>
                        <button type="submit"> S'enregistrer </button>
                     <div>
                        <label>Vous avez un compte ?</label>
                        <a href="?action=connexion">Se connecter</a>
                    </div>
                </form>
                </div>
            END;
    }
}