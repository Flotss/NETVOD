<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class AffichageDetailleeSerieAction extends Action
{
    /**
     * @var PDO $db La connexion à la base de données
     */
    private $db;

    /**
     * Methode execute
     * @return string Le code html de la page
     */
    public function execute(): string
    {

        $html = '';

        // Connexion à la base de données
        try{
            $this->db = ConnectionFactory::makeConnection();
        }catch(DBExeption $e){
            throw new AuthException($e->getMessage());
        }

        //  Requetage de la base de données pour afficher les données essentielles a de la série
        $temp = str_replace("'","\'",$_COOKIE['nomSerie']);
        $infoSerie = $this->db->query("SELECT s.titre as titre, s.descriptif, date_ajout, annee, img, s.id as id, COUNT(e.numero) as nbEp, genreSerie, publicSerie from serie s INNER JOIN episode e ON s.id = e.serie_id where s.titre = '$temp' GROUP BY (s.titre)  ");
        $infoSerie = $infoSerie->fetch();

        // Requetaage de la base de données pour afficher la note moyenne de la série
        $requete ="SELECT ROUND(AVG(note),1) as moyenne FROM serieComNote WHERE id_serie = {$infoSerie['id']} GROUP BY id_serie";        $statement = $this->db->prepare($requete);
        $statement->execute();
        if ($statement->rowCount() == 0) { // Si aucune note n'a été attribuée
            $note="La série n'a pas encore reçu de note";
        }else{ // Si au moins une note a été attribuée
            $statement = $statement->fetch();
            $note="{$statement['moyenne']}";
        }

        // Affichage des données de la série
        $html .= <<<END
                <h2>  {$infoSerie['titre']}  </h2>
                <img alt="" src="{$infoSerie['img']}">
                <p>{$infoSerie['descriptif']}</p>
                <p>genre: {$infoSerie['genreSerie']}</p>
                <p>public visée: {$infoSerie['publicSerie']}</p>
                <p>Date d'ajout : {$infoSerie['date_ajout']}</p>
                <p>Année de sortie : {$infoSerie['annee']}</p>
                <p>Nombre d'épisode : {$infoSerie['nbEp']}</p>
                <p>Note de la série : $note</p>
                <form method="post" action="?action=ajout-preference" class="action">
                        <button type="submit">Ajouter à mes préférences</button>
                </form>
                <form method="post" action="?action=supr-preference" class="action">
                        <button type="submit">Supprimer de mes préférences</button>
                </form>
                
                END;

        // Affichage de l'épisode en cours
        $etat = $this->db->query("select * from etatSerie where etat = 'en cours' AND id_serie = {$infoSerie['id']}");
        if($f = $etat->fetch()){
            $html .= $this->generateDiv("SELECT * from episode,episodeVisionnee, serie where episodeVisionnee.id_episode = episode.id AND episode.serie_id = serie.id AND episodeVisionnee.id_user = {$_SESSION['id']} AND episodeVisionnee.etat = 0 AND episode.numero <= ALL(select episode.numero from episode,episodeVisionnee where episode.id = episodeVisionnee.id_episode AND id_user = {$_SESSION['id']} AND episode.serie_id = {$infoSerie['id']} AND etat = 0) AND episode.serie_id = {$infoSerie['id']}",
                'Prochain Episode');
        }

        // Affichage de tous les épisodes
        $html .= $this->generateDiv("SELECT numero, episode.titre as titre, img, numero, duree from episode inner join serie on serie.id = episode.serie_id where serie_id = {$infoSerie['id']}",
             'Episodes de la série');

        // Boutton pour l'affichage de commentaire
        $html .= <<<END
                <form method="post" action="?action=affichage-commentaire" class="action">
                        <button type="submit">Voir les commentaires</button>
                </form>
                END;

        return $html;
    }

    private function generateDiv(string $requete, string $operation): string
    {
        // Recherche du chemin pour trouver les images
        $scriptNameExplode = explode('/', $this->getScriptName());
        $chemin = '';
        for ($k = 0; $k < count($scriptNameExplode) - 1; $k++) {
            $chemin .= $scriptNameExplode[$k] . '/';
        }


        // Creation d'une liste d'episode
        $html2 = "<div class='episodeSerie'><h3>$operation</h3>";
        $html2 .= "<ul =presentation episode>";

        // Pour chaque épidsode de la liste
        // Ajout d'une ligne dans la liste
        $q3 = $this->db->query($requete);
        while ($d1 = $q3->fetch()) {
            $html2 .= <<<END
                    <a href="?action=affichage-episode&titre-episode={$d1['titre']}">
                        <li class="decriptif">
                                <h2>Episode {$d1['numero']}</h2>
                                <p>Titre: {$d1['titre']}</p>
                                <p>Durée:  {$d1['duree']}</p>
                                <img alt="" src="$chemin/ressource/image/{$d1['img']}">
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