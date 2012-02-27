<?php
namespace Sagres\Configuration;

Interface ConfigurationStoreInterface
{
    public function __construct(array $properties = null);

    public function setData(array $data);
    public function getData();

    public function getSection($name);
    public function hasSection($name);
    public function setSection($name, array $value);

}