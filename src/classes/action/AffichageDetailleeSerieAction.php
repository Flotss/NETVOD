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
        $infoSerie = $db->query("SELECT s.titre as titre, s.descriptif, date_ajout, annee, img, s.id as id, COUNT(e.numero) as nbEp from serie s INNER JOIN episode e ON s.id = e.serie_id where s.titre = '{$_COOKIE['nomSerie']}' GROUP BY (s.titre)  ");
        $infoSerie = $infoSerie->fetch();
        $html .= <<<END
                <h2>  {$infoSerie['titre']}  </h2>
                <img alt="" src="{$infoSerie['img']}">
                <p>{$infoSerie['descriptif']}</p>
                <p>genre: ??</p>
                <p>public visée: ??</p>
                <p>Date d'ajout : {$infoSerie['date_ajout']}</p>
                <p>Année de sortie : {$infoSerie['annee']}</p>
                <p>Nombre d'épisode : {$infoSerie['nbEp']}</p>
                END;

        $infoSaison = $db->query("SELECT * from episode where serie_id = {$infoSerie['id']}");
        while ($infoSaisons = $infoSaison->fetch()){
            $html .= <<<END
                <h2>Episode {$infoSaisons['numero']}</h2>
                <p>Titre {$infoSaisons['titre']}</p>
                <p>Durée:  {$infoSaisons['duree']}</p>
                END;
        }

        return $html;
    }
}

/*
 *
. La liste des
épisodes de la série est également affichée, avec pour chaque épisode : numéro, titre, durée,
image.

 */