<?php

namespace iutnc\NetVOD\action;
use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

/**
 * Class AffichageSerieAction
 */
class AjoutPreferenceAction extends Action
{
    /**
     * Methode qui permet d'ajouter une preference
     * @return string Html
     */
    public function execute(): string
    {
        $html = '';

        // Connexion à la base de données
        try {
        $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }

        // On récupère l'id de la série
        if(isset($_COOKIE['nomSerie'])){
            $serie = "{$_COOKIE['nomSerie']}";
            $serie = str_replace("'","\'",$serie);
            $query = $db->query("SELECT id FROM serie WHERE titre = '{$serie}'");
            $serie = $query->fetch();
        }else {
            return "<p>Erreur dans l'ajout: pb dans le nom de la serie(absente)</p>";
        }

        // On récupère si elle existe la ligne entre l'utilisateur et sa série préféré
        $requete="SELECT * from userPref where id_user = {$_SESSION['id']} AND id_serie = {$serie['id']} GROUP BY id_user";
        $statement = $db->prepare($requete);
        $statement->execute();

        // Si la ligne n'existe pas on l'ajoute
        if ($statement->rowCount() == 0) {
            $insert = $db->exec("INSERT INTO userPref(id_user,id_serie) VALUES({$_SESSION['id']}, {$serie['id']})");
            $html="<p>Serie ajouté aux series préférés";
        }else{ // Sinon on affiche un message que la série est déjà dans les préférés
            $html="<p>Serie déjà ajouté aux series préférés";
        }
        return $html;
    }
}