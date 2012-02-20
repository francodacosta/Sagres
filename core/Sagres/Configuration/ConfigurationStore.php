<?php
namespace Sagres\Configuration;

class ConfigurationStore implements ConfigurationStoreInterface, \ArrayAccess
{
    private $properties = array();
    public function __construct(array $properties = null)
    {
        if(!is_null($properties)) {
            $this->properties = $properties;
        }
    }

    public function setData(array $data)
    {
        $this->properties = $data;
    }

//     public function get($key)
//     {
//         $props = $this->properties;
//         if (array_key_exists($key, $props)) {
//             return $props[$key];
//         }

//         return null;
//     }


    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->properties[] = $value;
        } else {
            $this->properties[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->properties[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->properties[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->properties[$offset]) ? $this->properties[$offset] : null;
    }
}