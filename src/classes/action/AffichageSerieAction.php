<?php

namespace iutnc\NetVOD\action;

class AffichageSerieAction extends Action
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
            $html .= ('<p>' . $q1['titre'] . '</p><img src="' . $q1['img'] . "'></br>");
        }
        return $html;
    }
}