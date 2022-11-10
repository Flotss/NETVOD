<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;
use iutnc\NetVOD\AuthException\AuthException;
use PDOException;
use PDO;

class AffichageSerieAction extends Action
{

    private $db;

    public function execute(): string
    {
        $html = '';
        try {
            $this->db = ConnectionFactory::makeConnection();
        } catch (PDOException $e) {
            throw new AuthException($e->getMessage());
        }

        // Saluer l'utilisateur
        $html .= '<h2>Bonjour ' . $_SESSION['user'] . '</h2>';


        $html .= "
                <form action='?action=AccueilUtilisateurAction.php' method='get'>
                    <legend >trier par :</legend>
                    <select name=Trier >
                        <option value='' ></option>
                        <option value=date_ajout >Date</option>
                        <option value=titre >Titre</option>
                        <option value=NombreEpisode >Nombre depisode</option>
                    </select>
                    <button type='submit'>Trier</button>
                </form>
                ";



//        On gere l'ensemble des series de la BD
        $rq="SELECT s.id,s.titre,s.descriptif,s.img,s.annee,s.date_ajout from serie s inner join episode ep on s.id=ep.serie_id";
        $html = $this->generateDiv($rq,
            $html, 'Catalogue', 1);

        //On gere l'ensemble des series en cours de l'utilisateur
        $html = $this->generateDiv("SELECT * from serie s inner join userPref u on u.id_serie = s.id  where id_user = {$_SESSION['id']}",
                                    $html, 'Series préférées', 2);

        //On gere les series en cours de l'utilisateur
        $html = $this->generateDiv("select * from serie s inner join etatSerie e on e.id_serie = s.id where etat like 'en cours' and id_user = {$_SESSION['id']}",
                                    $html, 'Series en cours', 3);

        //On gere les series daja visionée de l'utilisateur
        $html = $this->generateDiv("select * from serie s inner join etatSerie e on e.id_serie = s.id where etat like 'visionnee' and id_user = {$_SESSION['id']}",
            $html, 'Series deja visionnée', 4);
        return $html;
    }


    /*
     * fonction generant une partie de html
     */
    private function generateDiv(string $requete, string $html, string $operation, int $numero): string
    {
        //////////////////////////////////////
        if(isset($_GET['Trier'])){
            $requete=$this->Trie($requete);
        }
        ///////////////////////////////////////


        $statement = $this->db->prepare($requete);
        $statement->execute();


        $html .= "<h3>$operation</h3>";

        if ($statement->rowCount() == 0) {

            $html .= <<<END
                    <div class="aucuneSerie$numero">
                         <p>Vous n'avez pas de serie dans cette categorie</p>
                     </div>
            END;
            return $html;
        }



        $nbrSlide = $statement->rowCount()/3;
        $nbrSlide = ceil($nbrSlide);

        $numeroSection = 1;
        $idSectionSuivante = $nbrSlide;


        $scriptNameExplode = explode('/', $this->getScriptName());
        $chemin = '';
        for ($k = 0; $k < count($scriptNameExplode) - 1; $k++) {
            $chemin .= $scriptNameExplode[$k] . '/';
        }

        $nbrRow = 0;


        $html .= '<div class="wrapper">';
        for ($i = 1; $i <= $nbrSlide; $i++) {
            if ($nbrSlide == 2){
                $idPrecedent = 1;
                $idSuivant = 2;
            }else{
                $idPrecedent = ($i > 1) ? $i-1 : $nbrSlide;
                $idSuivant = ($i == $nbrSlide) ? $i+1 : 1;
            }



            $html .= '<section id="'. 'section' . $operation . $numeroSection . '">';
            $html .= '<a href="#'.'section' . $operation . $idPrecedent .'" class="arrow__btn left-arrow">‹</a>';
            for ($j = 0; $j < 3; $j++) {
                if ($nbrRow === $statement->rowCount()) {
                    break;
                }

                $data = $statement->fetch(PDO::FETCH_ASSOC);
                $titre = $data['titre'];
                $img = $data['img'];



                if($numero == 3){
                    $titreR = str_replace("'","\'",$titre);
                    $r = $this->db->query("SELECT episode.titre from episode,episodevisionnee,serie where episodevisionnee.id_episode = episode.id AND serie.id = episode.serie_id AND serie.titre = '{$titreR}' AND episodevisionnee.id_user = {$_SESSION['id']} AND episodevisionnee.etat = 0 AND episode.numero <= ALL(select episode.numero from episode,episodevisionnee,serie where episode.id = episodevisionnee.id_episode AND episode.serie_id = serie.id AND id_user = {$_SESSION['id']} AND serie.titre = '{$titreR}' AND etat = 0)");
                    $episode = $r->fetch();

                    $html .= <<<END
                    <div class="item">
                         <a href="?action=affichage-episode&titre-episode={$episode['titre']}">
                            <img alt="descritpion" src="$chemin/ressource/image/$img"></br>
                            <h1 class="heading">$titre</h1>
                         </a>
                    </div>
                    END;
                }else {
                    $html .= <<<END
                    <div class="item">
                         <a href="?action=affichage-page-serie&titre-serie=$titre">
                            <img alt="descritpion" src="$chemin/ressource/image/$img"></br>
                            <h1 class="heading">$titre</h1>
                         </a>
                    </div>
                    END;
                $nbrRow++;
            }
            $html .= '<a href="#'.'section' . $operation . $idSuivant .'" class="arrow__btn right-arrow">›</a>';
            $html .=  '</section>';
            $idSectionSuivante++;
            $numeroSection++;
        }
        $html .= '</div>';
        return $html;
    }



    private function Trie(string $requete):string{

            if ($_GET['Trier'] != '') {
                $requete .= " order by " . $_GET['Trier'];
            }
            if ($_GET['Trier'] == 'NombreEpisode') {
                $requete = 'select s.id,s.titre,s.descriptif,s.img,s.annee,s.date_ajout 
            from serie s inner join episode e 
            on e.serie_id=s.id GROUP by 
            s.id,s.titre,s.descriptif,s.img,s.annee,s.date_ajout 
            order by (select count(e.id) group by s.id)';
            }
        return $requete;
    }
}