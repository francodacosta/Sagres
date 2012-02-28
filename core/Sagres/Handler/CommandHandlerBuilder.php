<?php
namespace Sagres\Handler;

use Sagres\Exception\InvalidConfig;

use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sagres\Defenition\Command as CommandDefenition;
use Sagres\Defenition\Execute;
class CommandHandlerBuilder
{

    private $container;
    private $logger;

    public function __construct(ContainerBuilder $container, Logger $logger)
    {
        $this->setContainer($container);
        $this->setLogger($logger);
    }

    private function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    private function getContainer()
    {
        return $this->container;
    }

    private function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    private function getLogger()
    {
        return $this->logger;
    }


    private function buildStep($name)
    {

    }

    private function buildCommand($name)
    {

    }

    private function parseExecute(Execute $execute)
    {
        $type = $execute->getType();

        switch(str2lower($type)) {
            case 'step':
                return $this->buildStep($execute->getAction());
                break;

            case 'command':
                return $this->buildCommand($execute->getAction());
                break;

            default:
                throw new InvalidConfig('Unknown execute action type ' . $type);
                break;
        }
    }
    /**
     * builds a command handler based on the command defenition
     * this method will parse the Execute section of the command and generates
     * the Command Handler class needed to execute all steps / commands defined
     *
     * @returns Command - the command handler
     */
    public function build(CommandDefenition $defenition)
    {
        $command = new Command($defenition->getName());

        $executes = $defenition->getExecutes();

        foreach($executes as $execute) {
            $command->addAction($this->parseExecute($execute));
        }
    }
}