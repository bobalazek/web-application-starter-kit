<?php

namespace Application\Test\Functional;

use Application\Test\WebTestCase;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class PagesTest extends WebTestCase
{
    /**
     * Setss up the environment (inclusive preparation the tables).
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        shell_exec('bin/console orm:schema-tool:update -f --dump-sql');
    }

    /**
     * Test the routes for a anonymous user.
     */
    public function testAnonymousUserUrls()
    {
        $client = $this->createClient();

        $urls = $this->getAnonymousUserUrls();
        foreach ($urls as $url) {
            $client->request('GET', $url);

            $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                'The url "'.$url.'" could not be loaded by a anonymous user.'
            );
        }
    }

    /**
     * Test the routes for an normal user.
     */
    public function testUserUrls()
    {
        $client = $this->doLogin('user', ['ROLE_USER']);

        $userUrls = $this->getUserUrls();
        foreach ($userUrls as $url) {
            $client->request('GET', $url);

            $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                'The url "'.$url.'" could not be loaded by a default user.'
            );
        }
    }

    /**
     * Test the routes for an admin user.
     */
    public function testAdminUserUrls()
    {
        $client = $this->doLogin('admin', ['ROLE_ADMIN']);

        $urls = $this->getAdminUserUrls();
        foreach ($urls as $url) {
            // A hacky way to work around the KNP paginator bug
            //   (https://github.com/KnpLabs/knp-components/issues/90).
            unset($_GET['sort']);

            $client->request('GET', $url);

            $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                'The url "'.$url.'" could not be opened by an admin user.'
            );
        }
    }

    /**
     * Check for a 404 page.
     */
    public function test404()
    {
        $client = $this->createClient();
        $client->request('GET', '/just-a-404-page');

        $this->assertEquals(
            404,
            $client->getResponse()->getStatusCode(),
            'A code 404 page could not be found.'
        );
    }

    /**
     * URLs for non-logined users.
     *
     * @return array
     */
    public function getAnonymousUserUrls()
    {
        return []; // TODO
    }

    /**
     * URLs for logined users.
     *
     * @return array
     */
    public function getUserUrls()
    {
        return []; // TODO
    }

    /**
     * URLs for admin users.
     *
     * @return array
     */
    public function getAdminUserUrls()
    {
        return []; // TODO
    }
}
