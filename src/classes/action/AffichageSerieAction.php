<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;
use iutnc\NetVOD\AuthException\AuthException;
use PDOException;
use PDO;

/**
 * Class AffichageSerieAction
 */
class AffichageSerieAction extends Action
{

    private $tri = "";

    /**
     * @var PDO $db
     */
    private PDO $db;

    public function execute(): string
    {
        $html = '';

        // Connexion à la base de données
        try {
            $this->db = ConnectionFactory::makeConnection();
        } catch (PDOException $e) {
            throw new AuthException($e->getMessage());
        }

        // Saluer l'utilisateur
        $html .= '<h2>Bonjour ' . $_SESSION['user'] . '</h2>';
        if (isset($_GET["Trier"])) {
            $this->tri = str_replace("_", " ", $_GET["Trier"]);
        }

        /**
         * On recupere les donnees des tables public et genre pour les gerer en checkbox
         * On recupera les case coché à l'aide de la table post ,
         * puis apliquera la fonction Filtre
         */
        $checkPublic='';
        $checkGenre='';
        $stm=$this->db->prepare("SELECT * FROM genre");
        $stm->execute();
        while ($data = $stm->fetch()) {
            $checkGenre .= "<label>" . $data['libele'] . ":<input type=checkbox name='FiltreGenre[]' value=$data[libele]></label>&nbsp&nbsp";
        }
        $stm=$this->db->prepare("SELECT * FROM public");
        $stm->execute();
        while ($data = $stm->fetch()) {
            $checkPublic .= "<label>" . $data['libele'] . ":<input type=checkbox name='FiltrePublic[]' value=$data[libele]></label>&nbsp&nbsp";
        }
        // Option pour trier les séries
        $html .= "
                <form action='?action=AccueilUtilisateurAction.php' method='get' id='accueil'>
                    <legend >trier par : " . $this->tri . "</legend>
                    <select name=Trier >
                        <option value='' ></option>
                        <option value=date_ajout >Date</option>
                        <option value=titre >Titre</option>
                        <option value=NombreEpisode >Nombre depisode</option>
                        <option value='Note'>Note</option>
                    </select>
                    <button type='submit'>Trier</button>
                </form>
                 <form action='?action=AccueilUtilisateurAction.php' method='post' id='accueil'>
                 <legend>Filtrer par :</legend>
                 <span>Genre :&nbsp&nbsp</span>" . $checkGenre . "<span><br>Public visé :&nbsp&nbsp</span>" . $checkPublic . "
                 <br><button type='submit'>Filtrer</button></form>";


//        On gere l'ensemble des series de la BD
        $rq='SELECT DISTINCT s.id,s.titre,s.descriptif,s.img,s.annee,s.date_ajout 
                    from serie s 
                    inner join episode ep on ep.serie_id=s.id';

        $requete='';
        $requete=$this->Filtre($rq,);
        if(isset($_GET['Trier'])){
        $requete=$this->Trie($rq);
        }
        $html = $this->generateDiv($requete,
            $html, 'Catalogue', 1);

        //On gere l'ensemble des series en cours de l'utilisateur
        $html = $this->generateDiv("$rq inner join userPref u on u.id_serie = s.id   where id_user = {$_SESSION['id']}",
            $html, 'Series préférées', 2);

        //On gere les series en cours de l'utilisateur
        $html = $this->generateDiv("$rq inner join etatSerie e on e.id_serie = s.id  where etat like 'en cours' and id_user = {$_SESSION['id']}",
            $html, 'Series en cours', 3);

        //On gere les series daja visionée de l'utilisateur
        $html = $this->generateDiv("$rq inner join etatSerie e on e.id_serie = s.id where etat like 'visionnee' and id_user = {$_SESSION['id']}",
            $html, 'Series deja visionnée', 4);
        return $html;
    }


    /*
     * fonction generant une partie de html
     */
    private function generateDiv(string $requete, string $html, string $operation, int $numero): string
    {

        // Récupération des séries en foncion de la requête
        $statement = $this->db->prepare($requete);
        $statement->execute();

        // Affichage de l'intitulé
        $html .= "<h3>$operation</h3>";

        // si il n'y a pas de série
        if ($statement->rowCount() == 0) {

            $html .= <<<END
                    <div class="aucuneSerie$numero">
                         <p>Vous n'avez pas de serie dans cette categorie</p>
                     </div>
            END;
            return $html;
        }

        // Savoir le nombre section pour le wrapper
        $nbrSlide = $statement->rowCount() / 3;
        $nbrSlide = ceil($nbrSlide);

        // id pour les section du wrapper
        $numeroSection = 1;
        $idSectionSuivante = $nbrSlide;

        // Recherche du chemin pour trouver les images
        $scriptNameExplode = explode('/', $this->getScriptName());
        $chemin = '';
        for ($k = 0; $k < count($scriptNameExplode) - 1; $k++) {
            $chemin .= $scriptNameExplode[$k] . '/';
        }

        // Indice itération pour le nombre de séries affichées
        $nbrRow = 0;

        // Affichage des séries avec un wrapper
        $html .= '<div class="wrapper">';
        // Pour chaque section
        for ($i = 1; $i <= $nbrSlide; $i++) {
            // Mettre un id pour chaque section
            if ($nbrSlide == 2) {
                $idPrecedent = 1;
                $idSuivant = 2;
            } else {
                $idPrecedent = ($i > 1) ? $i - 1 : $nbrSlide;
                $idSuivant = ($i == $nbrSlide) ? $i + 1 : 1;
            }


            // Affichage de la section
            $html .= '<section id="' . 'section' . $operation . $numeroSection . '">';
            $html .= '<a href="#' . 'section' . $operation . $idPrecedent . '" class="arrow__btn left-arrow">‹</a>';

            // Pour 3 serie par section
            for ($j = 0; $j < 3; $j++) {
                if ($nbrRow === $statement->rowCount()) {
                    break;
                }

                // Récupération des données de la série
                $data = $statement->fetch(PDO::FETCH_ASSOC);
                $titre = $data['titre'];
                $img = $data['img'];


                if ($numero == 3) {
                    $titreR = str_replace("'", "\'", $titre);
                    $r = $this->db->query("SELECT episode.titre from episode,episodeVisionnee,serie where episodeVisionnee.id_episode = episode.id AND serie.id = episode.serie_id AND serie.titre = '{$titreR}' AND episodeVisionnee.id_user = {$_SESSION['id']} AND episodeVisionnee.etat = 0 AND episode.numero <= ALL(select episode.numero from episode,episodeVisionnee,serie where episode.id = episodeVisionnee.id_episode AND episode.serie_id = serie.id AND id_user = {$_SESSION['id']} AND serie.titre = '{$titreR}' AND etat = 0)");
                    $episode = $r->fetch();

                    $html .= <<<END
                    <div class="item">
                         <a href="?action=affichage-episode&titre-episode={$episode['titre']}">
                            <img alt="descritpion" src="$chemin/ressource/image/$img"></br>
                            <h1 class="heading">$titre</h1>
                         </a>
                    </div>
                    END;
                } else {
                    $html .= <<<END
                    <div class="item">
                         <a href="?action=affichage-page-serie&titre-serie=$titre">
                            <img alt="descritpion" src="$chemin/ressource/image/$img"></br>
                            <h1 class="heading">$titre</h1>
                         </a>
                    </div>
                    END;
                }

                // Incrémentation de l'indice d'itération des séries affichées
                $nbrRow++;
            }
            // Fin de section
            $html .= '<a href="#' . 'section' . $operation . $idSuivant . '" class="arrow__btn right-arrow">›</a>';
            $html .= '</section>';

            // Incrémentation de l'indice d'itération des id de section
            $idSectionSuivante++;
            $numeroSection++;
        }
        $html .= '</div>';
        return $html;
    }


    /** Méthode qui trie les series
     * @param string $requete : la requete a trier
     * @return string : la requete triée
     */
    private function Trie(string $requete): string
    {
        $ajout = '';
        $this->tri = $_GET['Trier'];
        if ($_GET['Trier'] != '') {

            $ajout = " order by " . $_GET['Trier'];
        }
        if ($_GET['Trier'] == 'NombreEpisode') {
            $ajout = ' GROUP by 
            s.id,s.titre,s.descriptif,s.img,s.annee,s.date_ajout 
            order by (select count(ep.id) group by s.id) DESC';
        }
        if ($_GET['Trier'] == 'Note') {
            $ajout = ' GROUP by s.id,s.titre,s.descriptif,s.img,s.annee,s.date_ajout order by (
SELECT ROUND(AVG(note),1) as moyenne FROM serieComNote scm where scm.id_serie=s.id  GROUP BY id_serie) DESC';
            }
        return $requete.$ajout;
    }


    /**
     * Fonction filtrant les séries à afficher
     * @param string $re
     * @return string
     */
    private function Filtre(string $re): string
    {
        $requete = $re;

        //booleen servant de repere pour placer le 'where' dans la requete principale
        $n = true;

        //Test de presence du cookie Post pour les filtres de genre
        if (isset($_POST['FiltreGenre'])) {

            foreach ($_POST['FiltreGenre'] as $k) {
                if ($n) {
                    $requete .= " where genreSerie like '%" . $k . "%'";
                    $n = false;
                } else {
                    $requete .= " and genreSerie like '%" . $k . "%'";
                }

            }
        }
        //Test de presence du cookie Post pour les filtres de public vise
        if (isset($_POST['FiltrePublic'])) {
            foreach ($_POST['FiltrePublic'] as $k) {
                $k = str_replace("'", "\'", $k);
                if ($n) {
                    $requete .= " where publicSerie like '%" . $k . "%'";
                    $n = false;
                } else {
                    $requete .= " and publicSerie like '%" . $k . "%'";
                }

            }
        }

        return $requete;
    }
}