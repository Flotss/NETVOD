<?php

namespace iutnc\NetVOD\action;

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
        $this->generateDiv("SELECT * from serie", $html, 'Catalogue');

        //On gere l'ensemble des series en cours de l'utilisateur
        $this->generateDiv("SELECT * from userpref where id_user like ?", $html, 'Series préférées');

        //On gere les series en cours de l'utilisateur
        $this->generateDiv("select * from etatserie where etat like 'en cours' and id_user=? and id_serie=?", $html, 'Series en cours');
        return $html;
    }

    /*
     * fonction generant une partie de html
     */
    private function generateDiv(string $requete, string $html, string $operation)
    {
        $html .= "<div ><h3>$operation</h3>";
        $q3 = $this->db->query($requete);
        while ($d1 = $q3->fetch()) {
            $html .= ('<p>' . $q3['titre'] . '</p><img src="' . $q3['img'] . "'></br>");
        }
        $html .= '</div>';
    }
}