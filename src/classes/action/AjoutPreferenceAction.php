<?php

namespace iutnc\NetVOD\action;
use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

class AjoutPreferenceAction extends Action
{

    public function execute(): string
    {
        $html = '';
        try {
        $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }
        if(isset($_COOKIE['nomSerie'])){
            $serie = "{$_COOKIE['nomSerie']}";
            $serie = str_replace("'","\'",$serie);
            $query = $db->query("SELECT id FROM serie WHERE titre = '{$serie}'");
            $serie = $query->fetch();
        }else {
            return "<p>Erreur dans l'ajout: pb dans le nom de la serie(absente)</p>";
        }

        $requete="SELECT * from userpref where id_user = {$_SESSION['id']} AND id_serie = {$serie['id']} GROUP BY id_user";


        $statement = $db->prepare($requete);
        $statement->execute();

        if ($statement->rowCount() == 0) {
            $insert = $db->exec("INSERT INTO userpref(id_user,id_serie) VALUES({$_SESSION['id']}, {$serie['id']})");
            $html="<p>Serie ajouté aux series préférés";
        }else{
            $html="<p>Serie déjà ajouté aux series préférés";
        }
        return $html;
    }
}