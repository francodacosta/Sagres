<?php
namespace Sagres\Framework;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Monolog\Logger;

abstract class BaseFrameworkAction implements BaseFrameworkActionInterface
{
    private $logger = null;
    private $container = null;

    /**
     * @return Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Monolog\Logger $logger
     */

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Symfony\Component\DependencyInjection\ContainerBuilder
     */

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     */

    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function log($message, $level = 'info')
    {
        $logger = $this->getLogger();
        if (is_null($logger)) {
            return;
        }

        $levels = array(
                100 => 'DEBUG',
                200 => 'INFO',
                300 => 'WARNING',
                400 => 'ERROR',
                500 => 'CRITICAL',
                550 => 'ALERT',
        );

        if(! in_array(strtoupper($level), $levels)) {
            throw new UnexpectedValueException($level . ' is not a valid log level');
        }

        $logger->addRecord($levels[$level], $message);
    }
}
