<?php

namespace iutnc\NetVOD\action;

class FooterAction extends Action
{

    public function execute(): string
    {
        $html = <<<END
        <footer>
            <div class = "nous">
                <p> A propos </p>
                <ul>
                    <li><a href="https://github.com/Flotss">Florian MANGIN</a></li>
                    <li><a href="https://github.com/julienBernard3">Julien Bernard</a></li>
                    <li><a href="https://github.com/DIDIer5454">Didier AKO OBONO</a></li>
                    <li><a href="https://github.com/Romain0A">Romain ABURTIN</a></li>
                </ul>
            </div>
            <div class="projet">
                <p> Projet </p>
                <p>Vous pouvez aller consulter le projet sur ce lien
                    <a href="https://github.com/Flotss/S3.1-SAE-DEV-WEB">Github</a></li><br>
                    Entre autre, le projet est hébergé sur un serveur web qui est le serveur de l'IUT de Nancy Charlemagne soit sur <a href="https://webetu.iutnc.univ-lorraine.fr/www/mangin215u/">webetu</a>.
                </p>
            </div> 
        </footer>
        END;
        return $html;
    }
}