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
        $temp = str_replace("'","\'",$_COOKIE['nomSerie']);
        $requete ="SELECT commentaire FROM seriecomnote sc INNER JOIN serie s ON s.id = sc.id_serie WHERE s.titre = '$temp'";
        $statement = $this->db->prepare($requete);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            $html = "La série n'a pas encore reçu de commentaire";
        }else {
            while ($d1 = $statement->fetch()) {
                $html .= <<<END
                        <li class="commentaire">
                                Commentaire: {$d1['commentaire']}
                        </li>
                END;
            }
        }

        return $html;
    }



}