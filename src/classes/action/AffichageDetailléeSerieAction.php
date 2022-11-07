<?php

namespace iutnc\NetVOD\action;

class AffichageDetailléeSerieAction extends Action
{

    public function execute(): string
    {
        $html = '';
        try{
            $db = ConnectionFactory::makeConnection();
        }catch(DBExeption $e){
            throw new AuthException($e->getMessage());
        }
        $q1 = $db->query("SELECT * from serie");
        while($d1=$q1->fetch()){
            $html .= ('<h4>' . $q1['titre'] . '</h4><img src="' . $q1['img'] . "'><p>" . $q1['descriptif'] . "</p><p>année:" . $q1['annee']);
        }
        return $html;
    }
}