<?php

namespace Application\Command\Environment;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareCommand
    extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName(
                'application:environment:prepare'
            )
            ->setDescription(
                'Prepare environment (create local config files, ...)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Application\Tool\Environment::prepare();

        $output->writeln(
            '<info>The environment was successfully prepared!</info>'
        );
    }
}
