<?php


declare(strict_types=1);

use iutnc\NetVOD\dispatch\Dispatcher;

session_start();

require 'vendor/autoload.php';
\iutnc\NetVOD\db\ConnectionFactory::setConfig('config/config.ini');

// DISPATCHER
$d = new Dispatcher();
$d->run();
