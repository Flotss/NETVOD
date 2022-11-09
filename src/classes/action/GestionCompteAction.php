<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class GestionCompteAction extends Action
{

    public function execute(): string
    {

        $html = '';
        // Recuperation des donnees de l'utilisateur
        $db = ConnectionFactory::makeConnection();
        $infoUser = $db->query("SELECT * from user where id = " . $_SESSION['id']);
        $infoUser = $infoUser->fetch();



        if ($this->http_method == 'GET'){
            return $this->getForm($infoUser);
        }else{ // POST
            // Filtre les entrées
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

            // affiche toute les entrees

            if (password_verify($password, $infoUser['password'])){
                $db->query("UPDATE user SET nom = '$nom', prenom = '$prenom', email = '$email' WHERE id = " . $_SESSION['id']);
                $infoUser = $db->query("SELECT * from user where id = " . $_SESSION['id']);
                $infoUser = $infoUser->fetch();
                $html = $this->getForm($infoUser);
                $html .= '<p>Vos informations ont bien été modifiées</p>';
                $_SESSION['user'] = $infoUser['prenom'];
                return $html;
            }else{
                $html .= $this->getForm($infoUser);
                $html .= 'Mot de passe incorrect';
                return $html;
            }
        }
    }

    private function getForm($infoUser): string{
        return  <<<END
            <h3>Vous pouvez ici changer vos informations</h3>
            <div class="enteteAccueil">
            <form method="POST" action="?action=gestionCompte">
                <label for="nom">Nom</label><input type="text" name="nom" id="nom" value="{$infoUser['nom']}">
                
                <label for="prenom">Prenom</label><input type="text" name="prenom" id="prenom" value="{$infoUser['prenom']}" >
                
                <label for="email">Email</label><input type="email" name="email" id="email" value="{$infoUser['email']}">
                
                <label for="mdp">Mot de passe</label><input type="password" name="password" id="password">
                <input type="submit" value="Valider">
            </form>
            </div>
        END;

    }
}