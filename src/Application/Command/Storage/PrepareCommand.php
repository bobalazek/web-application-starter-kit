<?php

namespace Application\Command\Storage;

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
                'application:storage:prepare'
            )
            ->setDescription(
                'Prepare storage (create storage folder with all files in it)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Application\Tool\Storage::prepare();

        $output->writeln(
            '<info>The storage was successfully prepared!</info>'
        );
    }
}
