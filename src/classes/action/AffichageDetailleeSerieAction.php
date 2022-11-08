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
        $_COOKIE['nomSerie'] = str_replace("'","\'",$_COOKIE['nomSerie']);
        $infoSerie = $db->query("SELECT * from serie where titre = '{$_COOKIE['nomSerie']}'");
        $infoSerie = $infoSerie->fetch();
        $html .= <<<END
                <h2>  {$infoSerie['titre']}  </h2>
                <img alt="" src="{$infoSerie['img']}">
                <p>{$infoSerie['descriptif']}</p>
                <p>Date d'ajout : {$infoSerie['date_ajout']}</p>
                <p>Année : {$infoSerie['annee']}</p>
                END;

        $infoSaison = $db->query("SELECT numero from episode where serie_id = {$infoSerie['id']}");
        while ($infoSaison = $infoSaison->fetch()){
            $html .= <<<END
                <h2>Saison {$infoSaison['numero']}</h2>
                END;
        }

        return $html;
    }
}