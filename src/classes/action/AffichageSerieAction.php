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



//        On gere l'ensemble des series de la BD
        $html = $this->generateDiv("SELECT * from serie",
                                    $html, 'Catalogue');

        //On gere l'ensemble des series en cours de l'utilisateur
        $html = $this->generateDiv("SELECT * from serie s inner join userpref u on u.id_serie = s.id  where id_user = {$_SESSION['id']}",
                                    $html, 'Series préférées');

        //On gere les series en cours de l'utilisateur
        $html = $this->generateDiv("select * from serie s inner join etatserie e on e.id_serie = s.id where etat like 'en cours' and id_user = {$_SESSION['id']}",
                                    $html, 'Series en cours');
        return $html;
    }


    /*
     * fonction generant une partie de html
     */
    private function generateDiv(string $requete, string $html, string $operation): string
    {
        $statement = $this->db->prepare($requete);
        $statement->execute();


        $html .= "<h3>$operation</h3>";

        if ($statement->rowCount() == 0) {

            $html .= '<div class="aucuneSerie">
                         <p>Vous n avez pas de serie dans cette categorie</p>
                     </div>';
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
            $idPrecedent = ($i >1) ? $i-1 : $nbrSlide;
            $idSuivant = ($i < $nbrSlide) ? $i+1 : 1;

            $html .= '<section id="section' . $numeroSection . '">';
            $html .= '<a href="?#section' . $idPrecedent .'" class="arrow__btn left-arrow">‹</a>';
            for ($j = 0; $j < 3; $j++) {
                if ($nbrRow === $statement->rowCount()) {
                    break;
                }

                $data = $statement->fetch(PDO::FETCH_ASSOC);
                $titre = $data['titre'];
                $img = $data['img'];




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
            $html .= '<a href="?#section' . $idSuivant .'" class="arrow__btn right-arrow">›</a>';
            $html .=  '</section>';
            $idSectionSuivante++;
            $numeroSection++;
        }
        $html .= '</div>';
        return $html;
    }
}