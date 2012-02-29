<?php
namespace Sagres\Handler;

use Monolog\Logger;

Interface HandlerInterface
{
    public function __construct($name);
    public function handle();
    public function getName();
    public function setLogger(Logger $logger);

}