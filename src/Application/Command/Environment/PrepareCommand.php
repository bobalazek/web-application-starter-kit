<?php

namespace Application\Command\Environment;

use Application\Tool\Environment;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PrepareCommand extends ContainerAwareCommand
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
        Environment::prepare();

        $output->writeln(
            '<info>The environment was successfully prepared!</info>'
        );
    }
}
