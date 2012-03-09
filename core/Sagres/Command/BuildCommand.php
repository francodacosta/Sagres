<?php
namespace Sagres\Command;


use Sagres\Handler\CommandHandlerBuilder;
use Sagres\Defenition\CommandDefenitionBuilder;

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
            new InputOption('command', 'c', InputArgument::OPTIONAL, 'The command to execute, if it is not specified it will default to the first one'),
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
        $instructions =  $input->getArgument('file');

        $config = $this->loadConfig($instructions, $input->getOption('parameter'));

        if (! $config->hasSection('commands') ) {
            throw new InvalidConfig('Instructions file does not contain any commands to execute');
        }

        $this->serviceContainer = $this->setupServices($config);


        $command = $input->getOption('command');

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
        $container = $this->serviceContainer;
        $config = $this->getConfig();
        $commandDefenition = new CommandDefenitionBuilder($name, $this->getConfig());
        $commandHandler = new CommandHandlerBuilder($this->serviceContainer, $this->getLogger());
        $command = $commandHandler->build($commandDefenition->build());

        $command->handle();
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