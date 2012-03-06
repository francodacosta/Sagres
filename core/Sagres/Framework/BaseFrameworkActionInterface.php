<?php
namespace Sagres\Framework;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Monolog\Logger;

interface BaseFrameworkActionInterface
{
    public function setLogger(Logger $logger);
    public function getLogger();
    public function setContainer(ContainerBuilder $container);
    public function getContainer();
}