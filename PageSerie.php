<?php


declare(strict_types=1);

use iutnc\NetVOD\dispatch\DispatcherPageSerie;

session_start();

require 'vendor/autoload.php';
\iutnc\NetVOD\db\ConnectionFactory::setConfig('config.ini');

// DISPATCHER
$d = new DispatcherPageSerie();
$d->run();
