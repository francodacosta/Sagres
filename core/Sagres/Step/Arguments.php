<?php
namespace Sagres\Step;

class Arguments
{
    private $arguments = array();

    public function add($name, $value)
    {
        $this->arguments[$name] = $value;
    }

    public function get($name = null)
    {
        if (is_null($name)) {
            return $this->arguments;
        }

        if (array_key_exists($name, $this->arguments)) {
            return $this->arguments[$name];
        }

        return null;
    }
}