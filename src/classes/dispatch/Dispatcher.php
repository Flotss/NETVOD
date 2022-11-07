<?php

namespace iutnc\NetVOD\dispatch;
use iutnc\deefy\action;


class Dispatcher
{
    protected ?string $action = null;

    public function __construct()
    {
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
    }


    public function run(): void
    {
        $html = '';
        switch ($this->action) {
            case 'inscription':
                $act = new action\InscriptionAction();
                $html+= $act->execute();
                break; //tous les cas d'inscription sont géré dans InscritpionAction
            case 'connexion':
                $act = new action\ConnexionAction();
                $html+= $act->execute();
            break;
            default:
                break;
        }

        $this->renderPage($html);
    }


    private function renderPage($html)
    {
        echo <<<END
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>NetVOD</title>
                </head>
                <body>
                    <label> User :  <input type="User" name="user" placeholder="user"> </label>
                    <label> Passwd :  <input type="password" name="passwd" placeholder = "<mot de passe>"> </label>
                    
                    <button type="submit"> Valider </button> 
                    <button type="inscription"> Inscription </button> 
                </body>
            </html>
        END;
    }

}