<?php

namespace Application\Test\Functional;

use Application\Test\WebTestCase;

class PagesTest
    extends WebTestCase
{
    /**
     * Checks if the required pages exist
     *
     * @dataProvider urlExistingPagesProvider
     */
    public function testIfMainPagesExist($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * Check for a 404 page
     */
    public function test404()
    {
        $client = $this->createClient();
        $client->request('GET', '/just-a-404-page');

        $this->assertEquals(
            404,
            $client->getResponse()->getStatusCode()
        );
    }

    /**
     * @return array
     */
    public function urlExistingPagesProvider()
    {
        return array(
            array('/'),
            array('/members-area/login'),
            array('/members-area/register'),
            array('/members-area/reset-password'),
        );
    }
}
