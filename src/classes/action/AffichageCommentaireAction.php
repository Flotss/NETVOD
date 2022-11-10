<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;
use iutnc\NetVOD\AuthException\AuthException;
use PDO;
use PDOException;

/**
 * Class AffichageCommentaireAction
 */
class AffichageCommentaireAction extends Action
{
    /**
     * Méthode qui permet d'afficher les commentaires d'une série
     * @return string $html code html de la page
     * @throws AuthException si la connexion à la base de données échoue
     */
    public function execute(): string
    {
        $html = '';

        // Connexion à la base de données
        try {
            $db = ConnectionFactory::makeConnection();
        } catch (PDOException $e) {
            throw new AuthException($e->getMessage());
        }

        // Affichage des commentaires
        $html .= '<h4>Commentaire </h4>';

        // Récupération des commentaires
        // On récupère le nom de la série
        $temp = str_replace("'","\'",$_COOKIE['nomSerie']);

        // On récupère les commentaires
        $requete ="SELECT commentaire FROM serieComNote sc INNER JOIN serie s ON s.id = sc.id_serie WHERE s.titre = '$temp'";        $statement = $db->prepare($requete);
        $statement->execute();

        // Affichage les commentaires
        if ($statement->rowCount() == 0) { // si il n'y a pas de commentaire
            $html = "La série n'a pas encore reçu de commentaire";
        }else { // si il y a des commentaires
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