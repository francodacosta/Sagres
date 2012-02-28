<?php
require_once __DIR__ . '/../../vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\Console\Command\Command;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => __DIR__ . '/../../vendors',
    'Monolog' => __DIR__ . '/../../vendors/Monolog/src',
    'Sagres'  => __DIR__ . '/..',
    'Task'  => __DIR__ . '/..',

));

$loader->register();
