<?php
namespace Sagres\Handler;

Interface HandlerInterface
{
    public function __construct($name);
    public function handle();
    public function getName();

}