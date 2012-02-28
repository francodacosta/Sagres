<?php
namespace Sagres\Defenitionn;


class CommandDefenitionBuilder
{
    private $name = null;
    private $instructions = array();

    /**
     *
     * @param string $name the command name
     * @param array $instructions - the instructions specified in the command execute defenition
     */
    public function __construct($name, array $instructions)
    {
        $this->name = $name;
        $this->instructions = $instructions;
    }

    public function build()
    {
        $command = new Command();
        $command->setName($name);

        foreach ($this->instructions as $type => $action)
        {
            $command->addExecute(new Execute($type, $action));
        }

        return $command;
    }
}