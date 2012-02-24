<?php
namespace Sagres\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Sagres\Exception\InvalidConfig;


class DescribeCommand extends BaseCommand
{
    protected $output;

    public function configure()
    {
        $this->setName('describe');
        $this->setAliases(array('desc'));

        $this->setDescription('Describes the commands available on the intructions file');

        $this->setDefinition(array(
                new InputArgument('file', InputArgument::REQUIRED, 'The instructions file'),
                new InputOption('services', 's', InputOption::VALUE_NONE, 'also show services'),
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instructions = $input->getArgument('file');
        $config = $this->loadConfig($instructions);
        $this->output = $output;

        if ($input->getOption('services')) {
            $services = $config->getSection('services');
            if (is_null($services)) {
                throw new InvalidConfig('Instructions file does not contain any services');
            }
            $this->describeServices($services, $output);
        }

        $commands = $config->getSection('commands');
        if (is_null($commands)) {
            throw new InvalidConfig('Instructions file does not contain any commands');
        }
        $this->describeCommands($commands, $output);


    }

    protected function describeServices(array $services, $output)
    {
        $output->writeln('Available Services:');

        foreach($services as $name => $instructions) {
            $this->describeService($name, $instructions, $output);
        }

        $output->writeln("");
        $output->writeln("");
    }

    protected function describeService($name, array $command, OutputInterface $output)
    {
        if (array_key_exists('desc', $command)){
            $summary = $command['desc'];
        } else {
            $summary = '';
        }
        $class = $command['class'];
        $output->writeln("");
        $output->writeln("\tname   : $name");
        $output->writeln("\tsummary: $summary");
        $output->writeln("\tclass  : $class");

    }

    protected function describeCommands(array $commands, $output)
    {
        $output->writeln('Available Commands:');

        foreach($commands as $name => $instructions) {
            $this->describeCommand($name, $instructions, $output);
        }
    }

    protected function describeCommand($name, array $command, OutputInterface $output)
    {
        $summary = $command['desc'];
        $output->writeln("");
        $output->writeln("\tname    : $name");
        $output->writeln("\tsummary : $summary");
        $output->writeln("\texecutes:");
        foreach($command['execute'] as  $class)
        {
            $output->writeln("\t\t-> $class");
        }
    }
}