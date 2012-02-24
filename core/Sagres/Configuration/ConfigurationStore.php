<?php
namespace Sagres\Configuration;

class ConfigurationStore implements ConfigurationStoreInterface
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
        $this->properties = array_merge_recursive($this->properties,  $data);
    }

    public function getSection($name)
    {
        if ($this->hasSection($name)) {
           return  $this->properties[$name];
        }
        return null;
    }

    public function hasSection($name)
    {
        return isset($this->properties[$name]);
    }


    public function setSection($name, array $value)
    {
        $this->properties[$name] = $value;
    }

}