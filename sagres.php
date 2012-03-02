<?php
require_once __DIR__ . '/core/Sagres/autoload.php';

use Sagres\Application;
use Sagres\Command\BuildCommand;
use Sagres\Command\DescribeCommand;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\FileLocator;

$sagres = new Application('sagres', '0.1');
$sagres->add(new BuildCommand);
$sagres->add(new DescribeCommand);
$sagres->run();

