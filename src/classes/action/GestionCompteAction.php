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
        $listGenre = $db->query("SELECT * from genre");
        $listPublic = $db->query("SELECT * from public");
        $listbuttonGenre ='';
        while ($g = $listGenre->fetch()){
            $listbuttonGenre .= <<<END
 <option value="{$g['libele']}" >{$g['libele']}</option> 
END;
        }

        $listbuttonPublic ='';
        while ($g = $listPublic->fetch()){
            $listbuttonPublic .= <<<END
 <option value="{$g['libele']}" name="{$g['libele']}" >{$g['libele']}</option> 
END;

        }

        if ($this->http_method == 'GET'){
            return $this->getForm($infoUser, $listbuttonGenre, $listbuttonPublic);
        }else { // POST

            if (isset($_POST['valider'])) {

                // Filtre les entrées
                $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
                $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

                // affiche toute les entrees

                if (password_verify($password, $infoUser['password'])) {
                    $db->query("UPDATE user SET nom = '$nom', prenom = '$prenom', email = '$email' WHERE id = " . $_SESSION['id']);
                    $infoUser = $db->query("SELECT * from user where id = " . $_SESSION['id']);
                    $infoUser = $infoUser->fetch();
                    $html = $this->getForm($infoUser,$listbuttonGenre, $listbuttonPublic);
                    $html .= '<p>Vos informations ont bien été modifiées</p>';
                    $_SESSION['user'] = $infoUser['prenom'];
                    return $html;
                } else {
                    $html .= $this->getForm($infoUser,$listbuttonGenre, $listbuttonPublic);
                    $html .= 'Mot de passe incorrect';
                    return $html;
                }
            } else if (isset($_POST['genre'])){
                $genre = $_POST["selectgenre"];
                if($genre != '') {
                    if (strpos("{$infoUser['genreUser']}", $genre) === false) {
                        $db->query("UPDATE user SET genreUser = '{$infoUser['genreUser']} $genre' WHERE id = " . $_SESSION['id']);
                    } else {
                        $nGenre = explode($genre, "{$infoUser['genreUser']}");
                        $val = "{$nGenre[0]} {$nGenre[1]}";
                        if ("{$nGenre[0]}" === " " || "{$nGenre[0]}" === "  ") {
                            $val = "{$nGenre[1]}";
                        } else if ("{$nGenre[1]}" === " " || "{$nGenre[1]}" === "  ") {
                            $val = "{$nGenre[0]}";
                        }
                        $val = str_replace("  ", " ", $val);
                        $db->query("UPDATE user SET genreUser = '$val' WHERE id = " . $_SESSION['id']);
                    }
                    $infoUser = $db->query("SELECT * from user where id = " . $_SESSION['id']);
                    $infoUser = $infoUser->fetch();
                }



                $html .= $this->getForm($infoUser,$listbuttonGenre, $listbuttonPublic);
                return $html;
            }else{

                $public = $_POST["selectpublic"];
                if($public != '') {
                    if (strpos("{$infoUser['publicUser']}", $public) === false) {
                        $temp = str_replace("'","\'", "{$infoUser['publicUser']}");
                        $public = str_replace("'", "\'", $public);
                        $db->query("UPDATE user SET publicUser = '$temp $public' WHERE id = " . $_SESSION['id']);
                    } else {

                        $nPublic = explode($public, "{$infoUser['publicUser']}");
                        $val = "{$nPublic[0]} {$nPublic[1]}";
                        if ("{$nPublic[0]}" === " " || "{$nPublic[0]}" === "  ") {
                            $val = "{$nPublic[1]}";
                        } else if ("{$nPublic[1]}" === " " || "{$nPublic[1]}" === "  ") {
                            $val = "{$nPublic[0]}";
                        }
                        $temp = str_replace("'","\'", $val);
                        $temp = str_replace("  ", " ", $temp);
                        $db->query("UPDATE user SET publicUser = '$temp' WHERE id = " . $_SESSION['id']);
                    }
                    $infoUser = $db->query("SELECT * from user where id = " . $_SESSION['id']);
                    $infoUser = $infoUser->fetch();
                }

                $html .= $this->getForm($infoUser,$listbuttonGenre, $listbuttonPublic);
                return $html;
            }
        }
    }

    private function getForm($infoUser, $listbuttonGenre, $listbuttonPublic ): string{
        return  <<<END
            <h3>Vous pouvez ici changer vos informations</h3>
            <div class="enteteAccueil">
            <form method="POST" action="?action=gestionCompte">
                <label for="nom">Nom</label><input type="text" name="nom" id="nom" value="{$infoUser['nom']}">
                
                <label for="prenom">Prenom</label><input type="text" name="prenom" id="prenom" value="{$infoUser['prenom']}" >
                
                <label for="email">Email</label><input type="email" name="email" id="email" value="{$infoUser['email']}">
                
                <label for="mdp">Mot de passe</label><input type="password" name="password" id="password">
                <input type="submit" value="Valider" name="valider">
                <label for="genre">Vos genres préféré: {$infoUser['genreUser']}</label>
                <label for="genre">Ajouter/ supprimer un genre:</label>
                
                    <select name="selectgenre" >
                        <option value='' ></option>
                        $listbuttonGenre
                    </select>
                    <button type='submit' name="genre">Ajouter / Supprimer</button>
                
                <label for="genre">Vos public: {$infoUser['publicUser']}</label>
                <label for="genre">Ajouter/ supprimer un public:</label>
                    <select name=selectpublic >
                        <option value='' ></option>
                $listbuttonPublic
                </select>
                    <button type='submit' name="public">Ajouter / Supprimer</button>
                
            </form>
            
            
            </div>
        END;

    }

}