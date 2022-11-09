<?php

namespace iutnc\NetVOD\html;

class Footer
{

    public function execute(): string
    {
        $html = <<<END
        <footer>
            <div class = "nous">
                <h3> A propos </h3>
                <ul class="liengit">
                    <li><a href="https://github.com/DIDIer5454">AKO OBONO Didier</a></li>
                    <li><a href="https://github.com/Romain0A">AUBURTIN Romain</a></li>
                    <li><a href="https://github.com/julienBernard3">BERNARD Julien</a></li>
                    <li><a href="https://github.com/Flotss">MANGIN Florian</a></li>
                </ul>
            </div>
            <div class="projet">
                <h3> Projet </h3>
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