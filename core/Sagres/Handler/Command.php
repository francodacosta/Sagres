<?php
namespace Sagres\Handler;
use Monolog\Logger;

class Command implements HandlerInterface
{
    private $actions;
    private $logger;
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }


    public function getName()
    {
        return $this->name;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function addAction(HandlerInterface $action)
    {
        $this->actions[] = $action;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function handle()
    {
        $logger = $this->getLogger();
        $logger->addInfo("executing " . $this->getName() . " command ");
        $actions = $this->getActions();
        foreach($actions as $action) {
            $logger->addDebug("dispatching to " . $action->getName() . " handler");
            $action->handle();
        }
    }
}