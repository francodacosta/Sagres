<?php
namespace Sagres\Step;

class Call
{
    private $method;
    private $arguments;

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setArguments(Arguments $args)
    {
        $this->arguments = $args;
    }

    public function getArguments()
    {
        return $this->arguments;
    }
}