<?php
namespace Sagres;

use Symfony\Component\Console\Application as ConsoleApplication;



class Application extends ConsoleApplication
{

    public function loadConfig()
    {
        try {



        } catch (\Exception $e) {
            // not output handler defines at the time, using a sensible one
            $this->renderException($e, new ConsoleOutput());
        }
    }
}


