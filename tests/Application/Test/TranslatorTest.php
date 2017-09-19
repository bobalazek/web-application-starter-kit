<?php

namespace Application\Test;

use Symfony\Component\Console\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class TranslatorTest extends WebTestCase
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
