<?php
namespace Sagres\Defenition;


use Sagres\Exception\InvalidConfig;

use Sagres\Configuration\ConfigurationStore;

class CommandDefenitionBuilder
{
    private $name = null;
    private $instructions = array();

    /**
     *
     * @param string $name the command name
     * @param array $instructions - the instructions specified in the command execute defenition
     */
    public function __construct($name, ConfigurationStore $instructions)
    {
        $this->name = $name;
        $this->instructions = $instructions;
    }

    public function build()
    {
        $command = new Command();
        $command->setName($this->name);

        if(! $this->instructions->hasSection('commands')) {
            throw new InvalidConfig('section commands not found');
        }

        $commands = $this->instructions->getSection('commands');

        if(! array_key_exists($this->name, $commands)) {
            throw new InvalidConfig($this->name . ' command not found');
        }

        if(! array_key_exists('execute', $commands[$this->name])) {
            throw new InvalidConfig($this->name . ' command: execute section not found');
        }
        $instructions = $commands[$this->name]['execute'];
        foreach ($instructions as $instruction)
        {
            $action = current($instruction);
            $type = key($instruction);
            switch($type) {
                case 'step' :
                    $command->addExecute(new Execute($type, $action));
                    break;
                case 'command':
                    $commandDefenitionBuilder = new CommandDefenitionBuilder($action,$this->instructions );
                    $command->addExecute(new Execute($type, $commandDefenitionBuilder->build()));
                    break;
            }
        }


        return $command;
    }
}