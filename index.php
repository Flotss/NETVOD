<?php


declare(strict_types=1);

use iutnc\deefy\dispatch\Dispatcher;

session_start();

require 'vendor/autoload.php';
\iutnc\deefy\db\ConnectionFactory::setConfig('config.ini');

// DISPATCHER
$d = new Dispatcher();
$d->run();
