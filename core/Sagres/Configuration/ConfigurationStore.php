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

    /**
     * merges both array and overwrite keys
     * @param array $Arr1
     * @param array $Arr2
     * @return array
     */
    protected function mergeArrays($Arr1, $Arr2)
    {
        foreach($Arr2 as $key => $Value)
        {
            if(array_key_exists($key, $Arr1) && is_array($Value))
                $Arr1[$key] = $this->MergeArrays($Arr1[$key], $Arr2[$key]);

            else
                $Arr1[$key] = $Value;

        }

        return $Arr1;

    }


    /**
     * (non-PHPdoc)
     * @see Sagres\Configuration.ConfigurationStoreInterface::setData()
     */
    public function setData(array $data)
    {
        $this->properties = $this->MergeArrays($this->properties,  $data);
    }

    /**
     * (non-PHPdoc)
     * @see Sagres\Configuration.ConfigurationStoreInterface::getData()
     */
    public function getData()
    {
        return $this->properties;
    }

    /**
     * (non-PHPdoc)
     * @see Sagres\Configuration.ConfigurationStoreInterface::getSection()
     */
    public function getSection($name)
    {
        if ($this->hasSection($name)) {
           return  $this->properties[$name];
        }
        return null;
    }

    /**
     * (non-PHPdoc)
     * @see Sagres\Configuration.ConfigurationStoreInterface::hasSection()
     */
    public function hasSection($name)
    {
        return isset($this->properties[$name]);
    }


    /**
     * (non-PHPdoc)
     * @see Sagres\Configuration.ConfigurationStoreInterface::setSection()
     */
    public function setSection($name, array $value)
    {
        $this->properties[$name] = $value;
    }

}