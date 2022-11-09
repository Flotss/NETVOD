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
        $temp = str_replace("'","\'",$_COOKIE['nomSerie']);
        $infoSerie = $this->db->query("SELECT s.titre as titre, s.descriptif, date_ajout, annee, img, s.id as id, COUNT(e.numero) as nbEp from serie s INNER JOIN episode e ON s.id = e.serie_id where s.titre = '$temp' GROUP BY (s.titre)  ");
        $infoSerie = $infoSerie->fetch();
        $requete ="SELECT ROUND(AVG(note),1) as moyenne FROM seriecomnote WHERE id_serie = {$infoSerie['id']} GROUP BY id_serie";
        $statement = $this->db->prepare($requete);
        $statement->execute();
        if ($statement->rowCount() == 0) {
            $note="La série n'a pas encore reçu de note";
        }else{
            $statement = $statement->fetch();
            $note="{$statement['moyenne']}";
        }
        $html .= <<<END
                <h2>  {$infoSerie['titre']}  </h2>
                <img alt="" src="{$infoSerie['img']}">
                <p>{$infoSerie['descriptif']}</p>
                <p>genre: ??</p>
                <p>public visée: ??</p>
                <p>Date d'ajout : {$infoSerie['date_ajout']}</p>
                <p>Année de sortie : {$infoSerie['annee']}</p>
                <p>Nombre d'épisode : {$infoSerie['nbEp']}</p>
                <p>Note de la série : $note</p>
                <form method="post" action="?action=ajout-preference" class="action">
                        <button type="submit">Ajouter à mes préférences</button>
                </form>
                
                END;


        $html .= $this->generateDiv("SELECT * from episode where serie_id = {$infoSerie['id']}",
             'Episodes de la série');

        $html .= <<<END
                <form method="post" action="?action=affichage-commentaire" class="action">
                        <button type="submit">Voir les commentaires</button>
                </form>
                END;

        return $html;
    }

    private function generateDiv(string $requete, string $operation): string
    {
        $html2 = "<div class='episodeSerie'><h3>$operation</h3>";
        $html2 .= "<ul =presentation episode>";
        $q3 = $this->db->query($requete);
        while ($d1 = $q3->fetch()) {
            $html2 .= <<<END
                    <a href="?action=affichage-episode&titre-episode={$d1['titre']}">
                        <li class="decriptif">
                                <h2>Episode {$d1['numero']}</h2>
                                <p>Titre: {$d1['titre']}</p>
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