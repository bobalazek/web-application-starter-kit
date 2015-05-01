<?php

namespace Application\Command\Translations;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareCommand
    extends ContainerAwareCommand
{
    protected $app;

    public function __construct(
        $name,
        \Silex\Application $app
    ) {
        parent::__construct($name);

        $this->app = $app;
    }

    protected function configure()
    {
        $this
            ->setName(
                'application:translations:prepare'
            )
            ->setDescription('Prepare translations')
            ->addOption(
                'locale',
                'l',
                InputOption::VALUE_OPTIONAL,
                'On which locale?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->app;

        $locale = $input->getOption('locale')
            ? $input->getOption('locale')
            : 'en_US'
        ;

        $results = $app['application.translator']
            ->prepare($app, $locale)
        ;

        $allMessages = $results['allMessages'];
        $translatedMessages = $results['translatedMessages'];
        $untranslatedMessages = $results['untranslatedMessages'];

        $output->writeln('<info>The Translations for '.$locale.' were successfully prepared!</info>');
        $output->writeln(
            '<info>All: '.count($allMessages).'; '.
            'Translated: '.count($translatedMessages).'; '.
            'Untranslated: '.count($untranslatedMessages).'!</info>'
        );
    }
}
