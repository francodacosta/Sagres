<?php
namespace Sagres\Command;

use Sagres\Step\Factory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Sagres\Configuration\YamlConfigurationReader;
use Sagres\Configuration\ConfigurationStore;
use Sagres\Configuration\ConfigurationFactory;
use Sagres\Configuration\ConfigurationStoreInterface;
use Sagres\Exception\InvalidConfig;

class BuildCommand extends Command
{
    private $input;
    private $output;

    public function configure()
    {
        $this->setName('build');
        $this->setDescription('executes the steps specified in the instructions file');

        $this->setDefinition(array(
            new InputArgument('file', InputArgument::REQUIRED, 'The instructions file')
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $output->writeln('<info>parsing instructions file </info>');
        $file = $input->getArgument('file');
        $output->writeln("<comment>$file</comment>");
        try {
            $config = $this->loadConfig($file);
//             var_dump($config);
            if (!isset($config['steps'])) {
                throw new InvalidConfig('Instructions file does not contain any steps');
            }
            $this->executeSteps($config['steps']);


        } catch (\Exception $e) {
            $message = $e->getMessage();
            $output->writeln("<error>$message</error>");
        }
    }

    private function executeSteps(array $steps)
    {
        $output = $this->output;
        foreach($steps as $name => $instructions) {
            $output->writeln("<info>executing step $name</info>");
            $this->executeStep($instructions);
        }
    }

    private function executeStep(array $instructions)
    {
        $step = new Factory();
        $step = $step->createStep($instructions);

        var_dump($step);
    }

    private function loadConfig($file)
    {
        $yamlLoader = new YamlConfigurationReader(new ConfigurationStore(), new \Symfony\Component\Yaml\Yaml());

        $files = array($file);

        $configLoader = new ConfigurationFactory($files, $yamlLoader);

        $config = $configLoader->getInstance();

        return $config;
    }
}