<?php

namespace iutnc\NetVOD\auth;

use iutnc\NetVOD\AuthException\AuthException;
use iutnc\NetVOD\db\ConnectionFactory as ConnectionFactory;
use PDO;
use PDOException;

class Auth
{

    // controler la solidité des mots de passe avant de les hacher dans la base
    public static function authenticate(string $email, string $mdpUser): bool
    {

        $query = "select * from user where email = ?";
        $db = ConnectionFactory::makeConnection();

        $stmt = $db->prepare($query);
        $res = $stmt->execute([$email]);

        try {
            // execute renvoie un booleen si aucune donnee execute, pareil pour fetch

            if (!$res) throw new AuthException("auth error : db query failed");

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) throw new AuthException("auth failed : invalid credentials");
            if (!password_verify($mdpUser, $user['password'])) throw new AuthException("auth failed : invalid credentials");
            $_SESSION['id'] = $user['id'];
            $_SESSION['user'] = $user['prenom'];
        } catch (AuthException $e) {
            return false;
        }
        return true;
    }

    public static function register(string $email, string $pass, string $pass2, string $nom, string $prenom): bool
    {
        try{
            self::checkPasswordStrength($pass, 8);
        }catch (AuthException $e){
            throw new AuthException("Erreur de mot de passe : " . $e->getMessage());
        }

        if ($pass !== $pass2) throw new AuthException("Les mots de passe ne correspondent pas");
        $hash = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (PDOException $e) {
            throw new AuthException($e->getMessage());
        }

        $regex = "/^[-+.\w]{1,64}@[-.\w]{1,64}\.[-.\w]{2,6}$/i";
        if (! preg_match($regex, $email)) {
            throw new AuthException("email invalide");
        }

        $query_email = "select * from user where email = ?";
        $stmt = $db->prepare($query_email);
        $res = $stmt->execute([$email]);
        if ($stmt->fetch()) throw new AuthException("compte deja existant");

        try {
            $query = "insert into user (email, password, nom, prenom) values (?, ?, ?, ?)";
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