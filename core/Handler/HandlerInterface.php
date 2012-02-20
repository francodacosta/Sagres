<?php
namespace Sagres\Handler;

use Symfony\Component\Console\Output\OutputInterface;

interface HandlerInterface
{
    public function __construct(OutputInterface $output);
    public function run();
    public function setPreference($name, $value);
    public function getPreference($name);
}