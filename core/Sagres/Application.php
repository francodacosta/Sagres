<?php
namespace Sagres;

use Symfony\Component\Console\Output\ConsoleOutput;

use Symfony\Component\Console\Application as ConsoleApplication;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;


class Application extends ConsoleApplication
{

    public function getLogger()
    {
        if (is_null($this->logger)) {
            $this->configureLogger();
        }
        return  $this->logger;
    }

    public function configureLogger($stream = null) {
        if (is_null($stream)) {
            $stream = 'php://stdout';
        }
        $logger = new Logger('Sagres');

        $format = "%message% \n";
        $formatter = new LineFormatter($format);

        $handler = new StreamHandler($stream, Logger::DEBUG);
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);

        $this->logger = $logger;
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->configureLogger();

        // make sure output has colors
        if (is_null($output)) {
            $output = new ConsoleOutput();
        }
        $output->setDecorated(true);

        return parent::run($input, $output);
    }
}


