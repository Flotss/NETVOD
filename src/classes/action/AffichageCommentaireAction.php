<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;
use iutnc\NetVOD\AuthException\AuthException;
use PDOException;

class AffichageCommentaireAction extends Action
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


        $html .= '<h4>Commentaire </h4>';


        $q3 = $this->db->query("SELECT commentaire FROM seriecomnote WHERE id_serie = {$_COOKIE['nomSerie']}");
        while ($d1 = $q3->fetch()) {
            $html .= <<<END
                        <li class="commentaire">
                                <p style="margin-top: 0; padding-top: 0">Commentaire: {$d1['commentaire']}</p>
                        </li>
                END;
        }

        return $html;
    }



}