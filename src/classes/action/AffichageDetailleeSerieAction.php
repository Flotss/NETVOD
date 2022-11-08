<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class AffichageDetailleeSerieAction extends Action
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
            $html .= ('<h4>' . $d1['titre'] . '</h4><img src="' . $d1['img'] . "'><p>" . $d1['descriptif'] . "</p><p>année:" . $d1['annee']);
        }
        return $html;
    }
}