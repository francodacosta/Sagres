<?php
namespace Sagres\Handler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Step implements HandlerInterface
{
    private $action;
    private $container;
    private $logger;

    public function __construct($name)
    {
        $this->action = $name;
    }

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setAction($name)
    {
        $this->action = $name;
    }

    public function handle()
    {
        $action = $this->getAction();
        $container = $this->getContainer();

        $class = $container->get($action);
        if (method_exists($class, 'execute')) {
            $class->execute();
        }
    }
}