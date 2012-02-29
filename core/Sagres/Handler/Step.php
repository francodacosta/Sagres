<?php
namespace Sagres\Handler;
use Monolog\Logger;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class Step implements HandlerInterface
{
    private $name;
    private $container;
    private $logger;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    public function getName()
    {
        return $this->name;
    }

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function handle()
    {
        $action = $this->getName();
        $container = $this->getContainer();

        $this->logger->addInfo("\t-> executing step $action");
        $class = $container->get($action);
        if (method_exists($class, 'execute')) {
            $class->execute();
        }
    }
}