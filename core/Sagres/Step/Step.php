<?php
namespace Sagres\Step;

class Step
{
    private $class;
    private $arguments;
    private $calls = array();


    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setArguments(Arguments $args)
    {
        $this->arguments = $args;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function addCall(Call $call)
    {
        $this->calls[] = $call;
    }

    public function getCalls()
    {
        return $this->calls;
    }
}