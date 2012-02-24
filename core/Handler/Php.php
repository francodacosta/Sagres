<?php
namespace Sagres\Handler;

use Symfony\Component\Console\Output\OutputInterface;
use Sagres\Step\Step:

class Php implements HandlerInterface
{
    private $paralel = false;
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }
    public function run(Step $step)
    {
        // get class name

        // class exists ?

        // get constructor

        // get constructor parameters

        // invoke constructor

        // foreach calls
            // get call
            // get call parameters

        // run execute call

    }
    public function setPreference($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new \InvalidArgumentException('unknown preference ' . $name);
        }
    }
    public function getPreference($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }

}