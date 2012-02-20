<?php
namespace Sagres\Configuration;
use Symfony\Component\Yaml\Yaml;

class YamlConfigurationReader implements ConfigurationReaderInterface
{
    private $configStore;
    private $helper;

    public function __construct(ConfigurationStoreInterface $configStore, $readerHelper = null)
    {
        $this->configStore = $configStore;
        $this->helper = $readerHelper;
    }

    public function parse($configuration)
    {
        $data = $this->helper->parse($configuration);
        if ( is_null($data)) {
            $data = array();
        }
        $this->configStore->setData($data);
    }

    public function getConfigStore()
    {
        return $this->configStore;
    }
}