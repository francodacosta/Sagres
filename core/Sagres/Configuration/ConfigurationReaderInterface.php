<?php
namespace Sagres\Configuration;

Interface ConfigurationReaderInterface
{

    /**
     *
     * @param ConfigurationObjectInterface $config the object to be populated
     * @param Mixed $readerHelper helper library to parse the file format, may not always be needed
     */
    public function __construct(ConfigurationStoreInterface $configStore, $readerHelper = null);

    /**
     * parses a string
     * @param String $configuration
     */
    public function parse($configuration);

    /**
     * returns a properly initialized Object
     * @return ConfigurationObjectInterface
     */
    public function getConfigStore();

}