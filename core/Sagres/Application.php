<?php
namespace Sagres;

use Symfony\Component\Console\Output\ConsoleOutput;

use Symfony\Component\Console\Application as ConsoleApplication;



class Application extends ConsoleApplication
{

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        // make sure output has colors
        if (is_null($output)) {
            $output = new ConsoleOutput();
        }
        $output->setDecorated(true);

        return parent::run($input, $output);
    }
}


