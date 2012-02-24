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

    protected function loadConfig($file, array $parameterOverrides = array())
    {
        $yamlLoader = new YamlConfigurationReader(new ConfigurationStore(), new \Symfony\Component\Yaml\Yaml());

        $files = array($file);

        $configLoader = new ConfigurationFactory($files, $yamlLoader);

        $config = $configLoader->getInstance();


        if (! $config->hasSection('parameters')) {
            $parameters = array();
        } else {
            $parameters = $config->getSection('parameters');
        }


        foreach($parameterOverrides as $override) {
            list ($key, $value) = explode('=', $override);
            if (! $key || ! $value) {
                throw new InvalidConfig("Expecting paramter override to be in the format key=value, but received $override");
            }
            $parameters[$key] = $value;
        }

        $config->setSection('parameters', $parameters);

        $this->setConfig($config);
        return $config;
    }

}