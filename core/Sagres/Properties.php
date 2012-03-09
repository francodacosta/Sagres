<?php
namespace Sagres;

class Properties
{
    private $properties = array();

    public function __construct(Array $properties = array())
    {
        $this->properties = $properties;
    }

    public function add($key, $value)
    {
        $this->properties[$key] = $value;
    }

    public function get($key, $default = null)
    {
        if(array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        }

        return $default;
    }
}