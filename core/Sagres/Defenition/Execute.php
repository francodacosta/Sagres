<?php
namespace Sagres\Defenition;
class Execute
{
    private $type = null;
    private $action = null;

    public function __construct($type = null, $action = null)
    {
        $this->setType($type);
        $this->setAction($action);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

}
