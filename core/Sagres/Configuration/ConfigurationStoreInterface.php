<?php
namespace Sagres\Configuration;

Interface ConfigurationStoreInterface
{
    public function __construct(array $properties = null);

    public function setData(array $data);

}