<?php
namespace Sagres\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sagres\Exception\InvalidConfig;

use Sagres\DependencyInjection\ArrayLoader;
use Symfony\Component\Config\FileLocator;

class BuildCommand extends BaseCommand
{
    private $input;
    private $output;

    /**
     * holds an instance of the Dependency injection container
     * @var ContainerBuilder
     */
    private $serviceContainer = null;

    /**
     * configures this command
     */
    public function configure()
    {
        $this->setName('build');
        $this->setDescription('executes the commands specified in the instructions file');

        $this->setDefinition(array(
            new InputArgument('file', InputArgument::REQUIRED, 'The instructions file'),
            new InputArgument('step', InputArgument::OPTIONAL, 'The command to execute, if it is not specified it will default to the first one'),
            new InputOption('parameter', 'p', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'also show services', array()),
        ));
    }

    /**
     * executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws InvalidConfig
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $instructions =  $input->getArgument('file');

        $config = $this->loadConfig($instructions, $input->getOption('parameter'));

        if (! $config->hasSection('commands') ) {
            throw new InvalidConfig('Instructions file does not contain any commands');
        }

        $this->serviceContainer = $this->setupServices($config);

        $command = $input->getArgument('step');
        if (!$command) {
            $commands = array_keys($config->getSection('commands'));
            $command = $commands[0];
        }
        $this->executeCommand($command);
    }

    /**
     * Executes a command as specified in the intructions file
     * runs all actions defined fot that command
     *
     * @param string $name the name of the command to execute
     * @throws \InvalidArgumentException
     * @throws InvalidConfig
     */
    private function executeCommand($name)
    {
        $output = $this->output;
        $container = $this->serviceContainer;
        $config = $this->getConfig();

        $output->writeln("<info>running $name command</info>");
        $commands = $config->getSection('commands');

        if(! array_key_exists($name, $commands)) {
            throw new \InvalidArgumentException("Command $name not found");
        }

        $command = $commands[$name];
        if (! array_key_exists('execute', $command)) {
            throw new InvalidConfig("Command $name is missing an entry named execute");
        }

        $actions = $command['execute'];
        if (! is_array($actions)) {
            throw new InvalidConfig("Actions for command $name should be an array");
        }

        foreach($actions as $action) {
            $output->writeln("<info>\t -> $action command</info>");
            $class = $container->get($action);

            if (method_exists($class, 'execute')) {
                $class->execute();
            }

        }

    }


    /**
     * parses configuration file and returns a properly configured DI container
     *
     * @param string $file
     */
    private function setupServices($config)
    {

        $locator = new FileLocator(array('./',getcwd()));
        $serviceContainer = new ContainerBuilder();

        $loader = new ArrayLoader($serviceContainer, $locator);
        $loader->load(array(
            'parameters' => $config->getSection('parameters'),
            'services' => $config->getSection('services'),
        ));

        return $serviceContainer;
    }

}