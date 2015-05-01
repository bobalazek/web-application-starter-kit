<?php

namespace Application\Test\Functional;

use Application\Test\WebTestCase;

class PagesTest
    extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testIfMainPagesExist($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/'),
            array('/members-area/login'),
            array('/members-area/register'),
            array('/members-area/reset-password'),
        );
    }
}
