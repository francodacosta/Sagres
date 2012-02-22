<?php
namespace Sagres\Handler;

use Sagres\Exception\NotFound;

use Sagres\Step\Step;

use Symfony\Component\Console\Output\OutputInterface;


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
        $className = $step->getClass();
        if (! class_exists($className)) {
            throw new NotFound("The class $className was not found");
        }

        $class = new \ReflectionClass($className);
        $constructor = $class->getConstructor();
        $arguments = $step->getArguments();
        var_dump($constructor);

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