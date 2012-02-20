<?php
namespace Sagres\Step;

use Sagres\Exception\InvalidConfig;

class Factory
{
    public function createStep(array $instructions)
    {
        $step = new Step();

        if (!array_key_exists('class', $instructions)) {
            throw new InvalidConfig('step requires an attribute named class');
        }

        $step->setClass($instructions['class']);

        if (array_key_exists('arguments', $instructions)) {
            $args = new Arguments();
            foreach($instructions['arguments'] as $name => $value) {
                $args->add($name, $value);
            }


            $step->setArguments($args);
        }

        if (array_key_exists('calls', $instructions)) {
            $callList = $instructions['calls'];
            foreach($callList as $method => $arguments) {
                $call = new Call();
                $call->setMethod($method);
                $args = new Arguments();
                foreach($arguments as $name => $value) {
                    $args->add($name, $value);
                }
                $call->setArguments($args);
                $step->addCall($call);
            }

        }

     return $step;
    }
}