<?php
namespace Sagres\Exception;

class NotFound extends \Exception
{
     /**
      * @param unknown_type $message
      * @param unknown_type $code
      * @param unknown_type $previous
      */
     public function __construct($message = 'File not found', $code = 404, $previous = null)
     {
         parent::__construct($message, $code, $previous);
     }

}