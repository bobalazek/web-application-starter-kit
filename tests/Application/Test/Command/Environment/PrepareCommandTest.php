<?php

namespace Application\Test\Command\Environment;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PrepareCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(
            new \Application\Command\Environment\PrepareCommand()
        );

        $command = $application->find('application:environment:prepare');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $this->assertRegExp(
            '/The environment was successfully prepared!/',
            $commandTester->getDisplay()
        );
    }
}
