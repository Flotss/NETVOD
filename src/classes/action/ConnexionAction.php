<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\auth\Auth;
use iutnc\NetVOD\AuthException\AuthException;
use iutnc\NetVOD\Redirect\Redirection;

/**
 * Class AccueilUtilisateurAction
 */
class ConnexionAction extends Action
{
    /**
     * Méthode qui permet de connecter l'utilisateur
     * @return string Html
     */
    public function execute(): string
    {
        $html = '';

        if ($this->http_method === 'GET') { // Si la méthode est GET alors on affiche le formulaire de connexion
            $html .= $this->getForm();
        } else { // POST Traitement du formulaire
            try {
                // Vérification que les champs sont bien remplis
                if (!(isset($_POST['email']) && isset($_POST['password']))) {
                    throw new AuthException("Erreur : email ou mot de passe non renseigné");
                }
                // Filtre les entrées
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

                // L'utilisateur se connecte
                $res = Auth::authenticate($email, $password);

                // Si l'utilisateur est connecté, redirection vers la page d'accueil, sinon affichage d'un message d'erreur
                if ($res) {
                    Redirection::redirection('AccueilUtilisateur.php');
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