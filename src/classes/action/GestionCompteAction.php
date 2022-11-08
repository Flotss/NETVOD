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
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $mdp = $_POST['mdp'];

            if (password_verify($mdp, $infoUser['password'])){
                $hash = password_hash($mdp, PASSWORD_DEFAULT, ['cost' => 12]);
                $db->query("UPDATE user SET nom = '$nom', prenom = '$prenom', email = '$email', password = '$hash' WHERE id = " . $_SESSION['id']);

                $infoUser = $db->query("SELECT * from user where id = " . $_SESSION['id']);
                $infoUser = $infoUser->fetch();
                $html = $this->getForm($infoUser);
                $html .= '<p>Vos informations ont bien été modifiées</p>';
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
            <form method="POST">
                <label for="nom">Nom</label><input type="text" name="nom" id="nom" value="{$infoUser['nom']}">
                
                <label for="prenom">Prenom</label><input type="text" name="prenom" id="prenom" value="{$infoUser['prenom']}" >
                
                <label for="email">Email</label><input type="email" name="email" id="email" value="{$infoUser['email']}">
                
                <label for="mdp">Mot de passe</label><input type="password" name="mdp" id="mdp">
                <input type="submit" value="Valider">
            </form>
        END;

    }
}