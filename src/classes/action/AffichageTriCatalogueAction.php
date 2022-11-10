<?php

namespace iutnc\NetVOD\action;

use iutnc\NetVOD\db\ConnectionFactory;

class AffichageTriCatalogueAction extends AffichageSerieAction
{
    public function execute(): string
    {
        $html = '';
        try {
            $this->db = ConnectionFactory::makeConnection();
        } catch (DBExeption $e) {
            throw new AuthException($e->getMessage());
        }
        $requete="select * from serie order by".$_COOKIE['trie'];
        $_COOKIE['trie'];
        $reponse=$this->db->query($requete);

        return $html;
    }
}