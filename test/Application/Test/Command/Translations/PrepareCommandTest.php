<?php

namespace Application\Test\Command\Translations;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PrepareCommandTest
    extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $app = require dirname(__FILE__).'/../../../../../app/bootstrap.php';
        $app->boot();

        $application = new Application();
        $application->add(
            new \Application\Command\Translations\PrepareCommand(
                'application:translations:prepare',
                $app
            )
        );

        $command = $application->find('application:translations:prepare');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertRegExp(
            '/The Translations for en_US were successfully prepared!/',
            $commandTester->getDisplay()
        );
    }
}
