<?php
require_once __DIR__ . '/core/Sagres/autoload.php';

use Sagres\Application;
use Sagres\Command\BuildCommand;


$sagres = new Application('sagres', '0.1');
// $sagres->loadConfig();
$sagres->add(new BuildCommand);
$sagres->run();