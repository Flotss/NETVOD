<?php

namespace iutnc\NetVOD\action;

class AffichageEpisodeAction extends Action
{

    public function execute(): string
    {
        $html = '';
        try{
            $db = ConnectionFactory::makeConnection();
        }catch(DBExeption $e){
            throw new AuthException($e->getMessage());
        }
        $q1 = $db->query("SELECT * from episode where id = ");//Ajouter un cookies pour savoir qu'elle episode afficher l'episode
        $d1=$q1->fetch();
        $html .= '<h4>' . $d1['titre'] . " Ep:" . $d1['numero'] . "</h4>" . "<video>" . $d1['file'] . "</video>" . "<p> durée:" . $d1['duree'] . "</p><p>Resume" . $d1['resume'] . "</p>";
        return $html;
    }
}