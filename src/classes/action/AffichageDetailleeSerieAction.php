<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class AffichageDetailleeSerieAction extends Action
{
    private $db;

    public function execute(): string
    {

        $html = '';
        try{
            $this->db = ConnectionFactory::makeConnection();
        }catch(DBExeption $e){
            throw new AuthException($e->getMessage());
        }
        $_COOKIE['nomSerie'] = str_replace("'","\'",$_COOKIE['nomSerie']);
        $infoSerie = $this->db->query("SELECT s.titre as titre, s.descriptif, date_ajout, annee, img, s.id as id, COUNT(e.numero) as nbEp from serie s INNER JOIN episode e ON s.id = e.serie_id where s.titre = '{$_COOKIE['nomSerie']}' GROUP BY (s.titre)  ");
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


        $html .= $this->generateDiv("SELECT * from episode where serie_id = {$infoSerie['id']}",
             'Episodes de la série');


        return $html;
    }

    private function generateDiv(string $requete, string $operation): string
    {
        $html2 = "<div class='$operation'><h3>$operation</h3>";
        $html2 .= "<ul =presentation episode>";
        $q3 = $this->db->query($requete);
        while ($d1 = $q3->fetch()) {
            $html2 .= <<<END
                    <a href="?action=affichage-episode&titre-episode={$d1['titre']}" style="color: black; text-decoration: none">
                        <li class="decriptif">
                                <h2>Episode {$d1['numero']}</h2>
                                <p>Titre {$d1['titre']}</p>
                                <p>Durée:  {$d1['duree']}</p>
                        </li>
                    </a>
                END;
        }
        $html2 .= "</ul></div>";

        return $html2;
    }
}

/*
 *
. La liste des
épisodes de la série est également affichée, avec pour chaque épisode : numéro, titre, durée,
image.

 */