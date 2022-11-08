<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class AffichageSerieAction extends Action
{

    private $db;

    public function execute(): string
    {
        $html = '';
        try {
            $this->db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }

        //On gere l'ensemble des series de la BD
        $html = $this->generateDiv("SELECT * from serie", $html, 'Catalogue');

        //On gere l'ensemble des series en cours de l'utilisateur
        $html = $this->generateDiv("SELECT * from userpref where id_user like 'user1'", $html, 'Series préférées');

        //On gere les series en cours de l'utilisateur
        $html = $this->generateDiv("select * from etatserie e inner join serie s on s.id=e.id_serie where etat like 'en cours' and id_user like 'user1'
", $html, 'Series en cours');
        return $html;
    }


    /*
     * fonction generant une partie de html
     */
    private function generateDiv(string $requete, string $html, string $operation): string
    {
        $html .= "<div><h3>$operation</h3>";
        $html .= "<ul =presentation serie>";
        $q3 = $this->db->query($requete);
        while ($d1 = $q3->fetch()) {
            $html .= '<li>
                            <h4>' . $d1['titre'] . '</h4>
                            <img src="' . $d1['img'] . "></br>
                            <p>".$d1['descriptif']."</p></li>";
        }
        $html .= '</ul></div>';

        return $html;
    }
}