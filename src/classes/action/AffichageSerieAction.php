<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;
use iutnc\NetVOD\AuthException\AuthException;
use PDOException;

class AffichageSerieAction extends Action
{

    private $db;

    public function execute(): string
    {
        $html = '';
        try {
            $this->db = ConnectionFactory::makeConnection();
        } catch (PDOException $e) {
            throw new AuthException($e->getMessage());
        }

        // Saluer l'utilisateur
        $html .= '<h2>Bonjour ' . $_SESSION['user'] . '</h2>';



//        On gere l'ensemble des series de la BD
        $html = $this->generateDiv("SELECT * from serie
                                            where id not in (SELECT id from serie s inner join userpref u on u.id_serie = s.id  where id_user = {$_SESSION['id']})
                                            and id not in (select id from serie s1 inner join etatserie e on e.id_serie = s1.id where etat like 'en cours' and id_user = {$_SESSION['id']})",
                                    $html, 'Catalogue');

        //On gere l'ensemble des series en cours de l'utilisateur
        $html = $this->generateDiv("SELECT * from serie s inner join userpref u on u.id_serie = s.id  where id_user = {$_SESSION['id']}",
                                    $html, 'Series préférées');

        //On gere les series en cours de l'utilisateur
        $html = $this->generateDiv("select * from serie s inner join etatserie e on e.id_serie = s.id where etat like 'en cours' and id_user = {$_SESSION['id']}",
                                    $html, 'Series en cours');
        return $html;
    }


    /*
     * fonction generant une partie de html
     */
    private function generateDiv(string $requete, string $html, string $operation): string
    {
        $html .= "<div class='$operation'><h3>$operation</h3>";
        $html .= "<ul =presentation serie>";
        $q3 = $this->db->query($requete);
        while ($d1 = $q3->fetch()) {
            $html .= <<<END
                    <li class="decriptif">
                        <a href="?action=affichage-page-serie&titre-serie={$d1['titre']}" style="color: black; text-decoration: none">
                            <h4 style="margin: 0; padding: 0"> {$d1['titre']}  </h4>
                            <img alt="" src="{$d1['img']}"></br>
                            <p style="margin-top: 0; padding-top: 0">{$d1['descriptif']}</p>
                        </a>
                    </li>
                END;
        }
        $html .= "</ul></div>";

        return $html;
    }
}