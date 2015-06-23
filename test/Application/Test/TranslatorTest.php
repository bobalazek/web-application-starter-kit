<?php

namespace Application\Test;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TranslatorTest
    extends WebTestCase
{
    public function testSwitchLocale()
    {
        $app = $this->createApplication();
        $app['application.translator']->setLocale('de_DE');

        $locale = $app['locale'];

        $this->assertTrue(
            $locale == 'de_DE'
        );
    }
}
