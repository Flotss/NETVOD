<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;
use PDO;

class ResearchAction extends Action
{

    public function execute(): string
    {
        if (isset($_POST['research'])) {
            $research = $_POST['research'];
            $research = str_replace(' ', '%', $research);
            $research = '%' . $research . '%';
            $requete = "SELECT titre, img FROM serie WHERE titre LIKE '$research' or descriptif LIKE '$research'";
            $html = $this->generateDiv($requete, '', 'Résultat recherche', 1);

        } else {
            $html = <<<END
                <form action="?action=research" method="post">
                    <input type="text" name="research" placeholder="Recherche">
                    <input type="submit" value="Rechercher">
                </form>    
                END;

        }

        return $html;
    }


    /*
    * fonction generant une partie de html
    */
    private function generateDiv(string $requete, string $html, string $operation, $numero): string
    {
        $db = ConnectionFactory::makeConnection();

        $statement = $db->prepare($requete);
        $statement->execute();


        $html .= "<h3>$operation</h3>";

        if ($statement->rowCount() == 0) {

            $html .= <<<END
                    <div class="aucuneSerie$numero">
                         <p>Aucune série trouvé</p>
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
}