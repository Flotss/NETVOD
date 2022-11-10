<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

/**
 * Class ResearchAction
 * Cette classe permet de rechercher une série avec des mots clés
 */
class ResearchAction extends Action
{
    /**
     * Méthode rendre un html qui recherche une série avec des mots clés
     * @return string $html code html de la page
     */
    public function execute(): string
    {
        if (isset($_POST['research'])) {
            $research = filter_input(INPUT_POST, 'research', FILTER_SANITIZE_STRING);

            // Utilisation REGEPX pour trouver les séries qui contiennent les mots clés
            // avec le titre et la description
            $research = str_replace(' ', '|', $research);

            // Affichage des séries qui contiennent les mots clés
            $requete = "SELECT titre, img FROM serie WHERE titre REGEXP '$research' or descriptif REGEXP '$research'";
            $html = $this->generateDiv($requete, '', 'Résultat recherche : ', 1);

        } else {
            // Affichage du formulaire
            $html = <<<END
                <form action="?action=research" method="post">
                    <input type="text" name="research" placeholder="Recherche">
                    <input type="submit" value="Rechercher">
                </form>    
                END;

        }

        return $html;
    }


    /**
     * Méthode qui permet de générer un wrapper de div pour les séries
     * @param string $requete requête sql
     * @param string $html code html de l'ancienne page
     * @param string $operation opération à faire
     * @param $numero numero de la série
     * @return string $html nouveau code html de la page
     */
    private function generateDiv(string $requete, string $html, string $operation, $numero): string
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();

        // Récupération des séries en foncion de la requête
        $statement = $db->prepare($requete);
        $statement->execute();

        // Affichage de l'intitulé
        $html .= "<h3>$operation</h3>";

        // si il n'y a pas de série
        if ($statement->rowCount() == 0) {

            $html .= <<<END
                    <div class="aucuneSerie$numero">
                         <p>Aucune série trouvé</p>
                     </div>
            END;
            return $html;
        }


        // Savoir le nombre section pour le wrapper
        $nbrSlide = $statement->rowCount()/3;
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
            if ($nbrSlide == 2){
                $idPrecedent = 1;
                $idSuivant = 2;
            }else{
                $idPrecedent = ($i > 1) ? $i-1 : $nbrSlide;
                $idSuivant = ($i == $nbrSlide) ? $i+1 : 1;
            }


            // Affichage de la section
            $html .= '<section id="'. 'section' . $operation . $numeroSection . '">';
            $html .= '<a href="#'.'section' . $operation . $idPrecedent .'" class="arrow__btn left-arrow">‹</a>';

            // Pour 3 serie par section
            for ($j = 0; $j < 3; $j++) {
                if ($nbrRow === $statement->rowCount()) {
                    break;
                }

                // Récupération des données de la série
                $data = $statement->fetch(PDO::FETCH_ASSOC);
                $titre = $data['titre'];
                $img = $data['img'];

                // Affichage de la série
                $html .= <<<END
                    <div class="item">
                         <a href="?action=affichage-page-serie&titre-serie=$titre">
                            <img alt="descritpion" src="$chemin/ressource/image/$img"></br>
                            <h1 class="heading">$titre</h1>
                         </a>
                    </div>
                    END;

                // Incrémentation de l'indice d'itération des séries affichées
                $nbrRow++;
            }
            // Fin de section
            $html .= '<a href="#'.'section' . $operation . $idSuivant .'" class="arrow__btn right-arrow">›</a>';
            $html .=  '</section>';

            // Incrémentation de l'indice d'itération des id de section
            $idSectionSuivante++;
            $numeroSection++;
        }
        $html .= '</div>';
        return $html;
    }
}