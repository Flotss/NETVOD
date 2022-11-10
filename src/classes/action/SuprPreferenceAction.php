<?php

namespace iutnc\NetVOD\action;
use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

class SuprPreferenceAction extends Action
{

    /**
     * methode qui supprime un preference si elle existe et retourne un string html
     * @return string
     */
    public function execute(): string
    {
        $html = '';
        //connection a la bd
        try {
        $db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }
        //Verifie que le cookies essentielle existe puis recupere l'id de la serie
        if(isset($_COOKIE['nomSerie'])){
            $serie = "{$_COOKIE['nomSerie']}";
            $serie = str_replace("'","\'",$serie);
            $query = $db->query("SELECT id FROM serie WHERE titre = '{$serie}'");
            $serie = $query->fetch();
        }else {
            return "<p>Erreur dans la suppression: pb dans le nom de la serie(absente)</p>";
        }

        //prepare la requete pour verifier l'existence de la preference
        $requete="SELECT * from userPref where id_user = {$_SESSION['id']} AND id_serie = {$serie['id']} GROUP BY id_user";

        //execute la requete pour verifier l'existence de la preference
        $statement = $db->prepare($requete);
        $statement->execute();

        //supprime la preference s'il elle existe
        if ($statement->rowCount() == 1) {
            $supr = $db->exec("DELETE FROM userPref WHERE id_user ={$_SESSION['id']} AND id_serie= {$serie['id']}");
            $html="<p>Série supprimée des series préférés";
        }else{
            $html="<p>La série n'est pas dans les series préférés";
        }
        return $html;
    }
}