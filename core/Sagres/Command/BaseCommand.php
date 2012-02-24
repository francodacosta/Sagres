<?php
namespace Sagres\Command;

use Symfony\Component\Console\Command\Command;

use Sagres\Configuration\YamlConfigurationReader;
use Sagres\Configuration\ConfigurationStore;
use Sagres\Configuration\ConfigurationFactory;
use Sagres\Configuration\ConfigurationStoreInterface;
use Sagres\Exception\InvalidConfig;


class BaseCommand extends command
{
    private $config;


    protected function getConfig()
    {
        return $this->config;
    }

    protected function setConfig(ConfigurationStoreInterface $config)
    {
        $this->config = $config;
    }

    protected function loadConfig($file)
    {
        $yamlLoader = new YamlConfigurationReader(new ConfigurationStore(), new \Symfony\Component\Yaml\Yaml());

        $files = array($file);

        $configLoader = new ConfigurationFactory($files, $yamlLoader);

        $config = $configLoader->getInstance();

        $this->setConfig($config);
        return $config;
    }

}