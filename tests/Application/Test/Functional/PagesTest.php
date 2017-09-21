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
                $client->getResponse()->getStatusCode() === 200,
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
                $client->getResponse()->getStatusCode() === 200,
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
            $client->request('GET', $url);

            $this->assertTrue(
                $client->getResponse()->getStatusCode() === 200,
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
        return [
            '/',
            '/members-area/login',
            '/members-area/register',
            '/members-area/reset-password',
        ];
    }

    /**
     * URLs for logined users.
     *
     * @return array
     */
    public function getUserUrls()
    {
        return [
            '/members-area/my',
            '/members-area/my/profile',
            '/members-area/my/settings',
            '/members-area/my/password',
            '/members-area/my/actions',
        ];
    }

    /**
     * URLs for admin users.
     *
     * @return array
     */
    public function getAdminUserUrls()
    {
        return [
            '/members-area/users',
            '/members-area/user-actions',
            '/members-area/errors',
            '/members-area/posts',
            '/members-area/statistics',
            '/members-area/tools',
            '/members-area/tools/email',
            '/members-area/tools/email/preview-templates',
            '/members-area/tools/database-backup',
            '/members-area/settings',
        ];
    }
}
