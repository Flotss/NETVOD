<?php

namespace iutnc\NetVOD\auth;

use iutnc\NetVOD\db\ConnectionFactory as cF;
use PDO;

class User
{

    protected $email;
    protected $passwd;

    function __construct($pEmail, $pPassWd, $pRole = 1)
    {
        $this->email = $pEmail;
        $this->passwd = $pPassWd;
        $this->role = $pRole;
    }


    function getId(): int // TODO : CHANGER CETTE METHODE
    {
        $db = cF::makeConnection();

        $sql = "SELECT id
                FROM user
                WHERE user.email = '$this->email'";
        $stm = $db->prepare($sql);
        $stm->execute();

        $row = $stm->fetch(PDO::FETCH_ASSOC);
        return $row['id'];
    }
}

?>