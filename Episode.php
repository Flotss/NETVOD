<?php


declare(strict_types=1);

use iutnc\NetVOD\dispatch\DispatcherEpisode;

session_start();

require 'vendor/autoload.php';
\iutnc\NetVOD\db\ConnectionFactory::setConfig('config/config.ini');

// DISPATCHER
$d = new DispatcherEpisode();
$d->run();
