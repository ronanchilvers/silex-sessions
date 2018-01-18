<?php

namespace Ronanchilvers\Silex\Sessions\Console\Command;

use Defuse\Crypto\Key;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to generate a new encryption key
 *
 * @author Ronan Chilvers <ronan@d3r.com>
 */
class GenerateKeyCommand extends Command
{
    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    public function configure()
    {
        $this
            ->setName('session:key:generate');
    }

    /**
     * @author Ronan Chilvers <ronan@d3r.com>
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating new random encryption key');
        $key = Key::createNewRandomKey();
        $ascii = $key->saveToAsciiSafeString();
        $output->writeln('Key : ' . $ascii);
    }
}
