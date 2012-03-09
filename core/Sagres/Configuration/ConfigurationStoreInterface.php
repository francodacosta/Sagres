<?php
namespace Sagres\Configuration;

Interface ConfigurationStoreInterface
{
    /**
     * Creates teh class and initializes its properties with the provided values
     * @param array $properties
     */
    public function __construct(array $properties = null);

    /**
     * merges $data array with current properties
     * @param array $data
     */
    public function setData(array $data);

    /**
     * gets current properties
     */
    public function getData();

    /**
     * returns porperties for a specific section of the config file
     * sections are the top level keys on the config file
     * @param String $name
     */
    public function getSection($name);
    /**
     * returns true if the section is present
     * @param unknown_type $name
     */
    public function hasSection($name);

    /**
     * sets data for that specific section, data will be overwritten
     * @param String $name
     * @param array $value
     */
    public function setSection($name, array $value);

    /**
     * Adds a value to a specifi section
     *
     * @param String $section
     * @param String $key
     * @param mixed $value
     */
    public function add($section, $key, $value);

    /**
     * gets a value from a specific section
     *
     * @param String $section
     * @param String $key
     * @param mixed $value
     */
    public function get($section, $key, $default = null);

}