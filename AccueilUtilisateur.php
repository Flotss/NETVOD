<?php


declare(strict_types=1);

use iutnc\NetVOD\dispatch\DispatcherAccueilUtilisateur;

session_start();

require 'vendor/autoload.php';
\iutnc\NetVOD\db\ConnectionFactory::setConfig('config.ini');

// DISPATCHER
$d = new DispatcherAccueilUtilisateur();
$d->run();
