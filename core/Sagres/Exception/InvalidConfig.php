<?php
namespace Sagres\Exception;

class InvalidConfig extends \Exception
{
     /**
      * @param unknown_type $message
      * @param unknown_type $code
      * @param unknown_type $previous
      */
     public function __construct($message = 'invalid config', $code = 400, $previous = null)
     {
         parent::__construct($message, $code, $previous);
     }

}