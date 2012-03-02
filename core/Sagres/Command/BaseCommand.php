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
    protected $logger = null;

    /**
     * @return ConfigurationStoreInterface - the currently in use configuration
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     *
     * @param ConfigurationStoreInterface $config the configuratiion object
     */
    protected function setConfig(ConfigurationStoreInterface $config)
    {
        $this->config = $config;
    }


    /**
     * loads a configuration file, optionally overrides the paramters specified
     * in the configuration file with the ones specified
     *
     * @param string $file
     * @param array $parameterOverrides key value array of parameters to verride
     * @throws InvalidConfig
     */
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

    public function getLogger()
    {
       return $this->getApplication()->getLogger();
    }
}