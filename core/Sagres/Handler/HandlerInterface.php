<?php
namespace Sagres\Handler;

use Symfony\Component\Console\Output\OutputInterface;
use Sagres\Step\Step;

interface HandlerInterface
{
    public function __construct(OutputInterface $output);
    public function run(Step $step);
    public function setPreference($name, $value);
    public function getPreference($name);
}