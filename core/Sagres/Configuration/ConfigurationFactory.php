<?php
namespace Sagres\Configuration;

use Sagres\Exception\NotFound;

class ConfigurationFactory
{
    public static $instance = null;

    private $loader = null;
    private $files = array();

    public function __construct (array $files = null, ConfigurationReaderInterface $loader = null)
    {
        $this->files = $files;
        $this->loader = $loader;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function &getInstance()
    {
        if (is_null(self::$instance)) {
            echo "new config instance\n";
            self::$instance = & $this->load($this->getFiles());
        }

        return self::$instance;
    }

    /**
     * loads a configuration file and process any imports file statments found
     * @param array $files the files to process
     * @throws NotFound if a file can not be found
     * @return ConfigurationReaderInterface populated with data from the config file
     */
    private function load(array $files)
    {
        $loader = $this->getLoader();

        foreach($files as $file) {
            if (! file_exists($file)) {
                throw new NotFound("The file $file was not found");
            }
            $loader->parse(file_get_contents($file));
            $store =&  $loader->getConfigStore();

            // array_key_exists does not work with arrayaccess interface
            if ($store->hasSection('imports')) {
                $imports = $store->getSection('imports');
                $store = $this->load($imports);
            }

        }

        return $store;
    }
}