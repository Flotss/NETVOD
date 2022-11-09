<?php

namespace iutnc\NetVOD\action;
use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

class SuprPreferenceAction extends Action
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
            return "<p>Erreur dans la suppression: pb dans le nom de la serie(absente)</p>";
        }

        $requete="SELECT * from userpref where id_user = {$_SESSION['id']} AND id_serie = {$serie['id']} GROUP BY id_user";


        $statement = $db->prepare($requete);
        $statement->execute();

        if ($statement->rowCount() == 1) {
            $supr = $db->exec("DELETE FROM userpref WHERE id_user ={$_SESSION['id']} AND id_serie= {$serie['id']}");
            $html="<p>Série supprimée des series préférés";
        }else{
            $html="<p>La série n'est pas dans les series préférés";
        }
        return $html;
    }
}