<?php

namespace iutnc\NetVOD\auth;

use iutnc\NetVOD\AuthException\AuthException;
use iutnc\NetVOD\db\ConnectionFactory as ConnectionFactory;
use PDO;
use PDOException;

/**
 * Class Auth
 * Cette classe permet de gérer l'authentification des utilisateurs
 */
class Auth
{

    /**
     * Methode d'authentification
     * @param string $email email de l'utilisateur
     * @param string $mdpUser mot de passe de l'utilisateur
     * @return bool true si l'authentification est réussie, false sinon
     */
    public static function authenticate(string $email, string $mdpUser): bool
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();

        // Recuperation de l'utilisateur
        $query = "select * from user where email = ?";
        $stmt = $db->prepare($query);
        $res = $stmt->execute([$email]);

        try {
            // Si il y a une erreur d'exécution de la requête
            // on lève une exception
            if (!$res) throw new AuthException("auth error : db query failed");

            // Récuperation du résultat
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si l'utilisateur n'existe pas
            if (!$user) throw new AuthException("auth failed : Utilisateur inconnu");

            // Si le mot de passe ne correspond pas
            if (!password_verify($mdpUser, $user['password'])) throw new AuthException("auth failed : mot de passe incorrect");

            // Ajout des informations de l'utilisateur dans la session
            $_SESSION['id'] = $user['id'];
            $_SESSION['user'] = $user['prenom'];
        } catch (AuthException $e) {
            return false;
        }
        return true;
    }


    /**
     * Methode d'incription
     * @param string $email email de l'utilisateur
     * @param string $pass mot de passe de l'utilisateur
     * @param string $pass2 confirmation du mot de passe
     * @param string $nom nom de l'utilisateur
     * @param string $prenom prenom de l'utilisateur
     * @return bool true si l'inscription est réussie, false sinon
     * @throws AuthException si l'inscription échoue
     */
    public static function register(string $email, string $pass, string $pass2, string $nom, string $prenom): bool
    {
        // Verification de la complexité du mot de passe
        try{
            self::checkPasswordStrength($pass, 8);
        }catch (AuthException $e){
            throw new AuthException("Erreur de mot de passe : " . $e->getMessage());
        }

        // Verification de la correspondance des mots de passe
        if ($pass !== $pass2) throw new AuthException("Les mots de passe ne correspondent pas");

        // Hashage du mot de passe
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

        // Connexion à la base de données
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (PDOException $e) {
            throw new AuthException($e->getMessage());
        }

        // Verification de la forme de l'email
        $regex = "/^[-+.\w]{1,64}@[-.\w]{1,64}\.[-.\w]{2,6}$/i";
        if (! preg_match($regex, $email)) {
            throw new AuthException("email invalide");
        }

        // Verification de l'unicité de l'email
        $query_email = "select * from user where email = ?";
        $stmt = $db->prepare($query_email);
        $res = $stmt->execute([$email]);
        if ($stmt->fetch()) throw new AuthException("compte deja existant");

        // Insertion de l'utilisateur dans la base de données
        try {
            $query = "insert into user (email, password, nom, prenom, genreUser, publicUser) values (?, ?, ?, ?, '', '')";
            $stmt = $db->prepare($query);
            $stmt->execute([$email, $hash, $nom, $prenom]);

            // On récupère l'id de l'utilisateur pour pouvoir l'utiliser plus tard
            $query = "select id from user where email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            // La connection est établie et l'id de l'utilisateur est stocké
            $_SESSION['id'] = $res['id'];
            $_SESSION['user'] = $prenom;
        } catch (PDOException $e) {
            throw new AuthException("Erreur de création de compte : " . $e->getMessage());
        }

        return true;
    }


    /**
     * Methode de verification de la force du mot de passe
     * @param string $pass mot de passe de l'utilisateur
     * @param int $minimumLength longueur minimal du mot de passe
     * @return bool true si la force est force sinon false
     * @throws AuthException si un test échoue
     */
    public static function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        $length = (strlen($pass) < $minimumLength); // longueur minimale
        if ($length) throw new AuthException("La taille du mot de passe doit être de minimum {$minimumLength}");

        $digit = preg_match("#[\d]#", $pass); // au moins un digit
        if (!$digit) throw new AuthException("Le mot de passe doit avoir au moins un chiffre");

        $special = preg_match("#[\W]#", $pass); // au moins un car. spécial
        if (!$special) throw new AuthException("Le mot de passe doit avoir au moins un caractère spécial");

        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        if (!$lower) throw new AuthException("Le mot de passe doit avoir au moins une minuscule");

        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule
        if (!$upper) throw new AuthException("Le mot de passe doit avoir au moins une majuscule");

        return true;
    }
}


?>