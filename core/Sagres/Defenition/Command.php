<?php
namespace Sagres\Defenition;
class Command
{
    private $name = null;
    private $summary = null;
    private $executes = array();

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function getExecutes()
    {
        return $this->execute;
    }

    public function addExecute(Execute $execute)
    {
        $this->executes[] = $execute;
    }

}
