<?php

namespace iutnc\NetVOD\auth;

use iutnc\NetVOD\AuthException\AuthException as AuthException;
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
            if (!password_verify($mdpUser, $user['passwd'])) throw new AuthException("auth failed : invalid credentials");
        } catch (AuthException $e) {
            echo $e->getMessage();
        }
        return true;
    }

    public static function register(string $email, string $pass): bool
    {
        if (!self::checkPasswordStrength($pass, 4))
            throw new AuthException("password trop faible");
        $hash = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (DBException $e) {
            throw new AuthException($e->getMessage());
        }
        $query_email = "select * from user where email = ?";
        $stmt = $db->prepare($query_email);
        $res = $stmt->execute([$email]);
        if ($stmt->fetch()) throw new AuthException("compte deja existant");

        try {
            $query = "insert into user (email, passwd) values (?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$email, $hash]);

            // On récupère l'id de l'utilisateur pour pouvoir l'utiliser plus tard
            $query = "select id from user where email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            $_SESSION['id'] = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        } catch (PDOException $e) {
            throw new AuthException("erreur de création de compte : " . $e->getMessage());
        }

        return true;
    }

    public static function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        $length = true; //(strlen($pass) < $minimumLength); // longueur minimale
        $digit = true;//preg_match("#[\d]#", $pass); // au moins un digit
        $special = true; //preg_match("#[\W]#", $pass); // au moins un car. spécial
        $lower = true; //preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = true; //preg_match("#[A-Z]#", $pass); // au moins une majuscule
        if (!$length || !$digit || !$special || !$lower || !$upper) return false;
        return true;
    }
}


?>