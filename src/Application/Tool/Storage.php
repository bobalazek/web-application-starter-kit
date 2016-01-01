<?php

namespace Application\Tool;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Storage
{
    /**
     * @return void
     */
    public static function prepare()
    {
        self::prepareFolders(array(
            'var',
            'var/cache',
            'var/cache/assetic',
            'var/cache/file',
            'var/cache/http',
            'var/cache/profiler',
            'var/cache/proxy',
            'var/cache/template',
            'var/cache/security',
            'var/database',
            'var/logs',
            'var/sessions',
            'var/mailer',
            'var/mailer/spool',
        ));

        self::prepareLogFiles(array(
            'var/logs/development.log',
            'var/logs/testing.log',
            'var/logs/staging.log',
            'var/logs/production.log',
        ));
    }

    /**
     * @return void
     */
    public static function prepareFolders(array $paths = array(), $uploadsPath = false)
    {
        if (empty($paths)) {
            return false;
        }

        $fs = new Filesystem();

        foreach ($paths as $path) {
            $fs->remove($path);
            $fs->mkdir($paths);
            $fs->chmod($path, 0777);
        }

        self::prepareUploadsFolder($uploadsPath);
    }

    /**
     * @return void
     */
    public static function prepareUploadsFolder($uploadsDirectory)
    {
        if (!$uploadsDirectory) {
            return false;
        }

        $fs = new Filesystem();

        $uploadsDirectory = 'web/assets/uploads';

        if (!$fs->exists($uploadsDirectory)) {
            $fs->mkdir($uploadsDirectory, 0777);
        }

        $user = PHP_OS == 'Darwin' // Fix for OSX
            ? get_current_user()
            : 'www-data'
        ;

        try {
            $fs->chown($uploadsDirectory, $user);
            $fs->chmod($uploadsDirectory, 0777);
        } catch (\Exception $e) {
            // Not sure If we need to show this errors. Let's think about that...
        }
    }

    /**
     * @return void
     */
    public static function prepareSharedFolders(array $paths = array())
    {
        if (empty($paths)) {
            return false;
        }

        $fs = new Filesystem();

        $releaseRoot = dirname(dirname(dirname(dirname(__FILE__)))).'/'; // Current version root
        $root = dirname(dirname($releaseRoot));

        $sharedDirectory = $root.'/shared/';

        // Create the shared directory first (if it does not exists)
        if (!$fs->exists($sharedDirectory)) {
            $fs->mkdir($sharedDirectory, 0777);
        }

        foreach ($paths as $path) {
            $pathDirectory = $releaseRoot.$path;
            $sharedPathDirectory = $sharedDirectory.$path;

            if (!$fs->exists($sharedPathDirectory)) {
                $fs->mkdir($sharedPathDirectory, 0777);
            }

            $pathDirectoryTmp = $pathDirectory.'_tmp';

            // Symlink it per hand
            exec("ln -f -s $sharedPathDirectory $pathDirectoryTmp");
            exec("rm -rf $pathDirectory");
            exec("mv -Tf $pathDirectoryTmp $pathDirectory");
        }
    }

    /**
     * @return void
     */
    public static function prepareLogFiles(array $paths)
    {
        $fs = new Filesystem();

        foreach ($paths as $path) {
            $fs->remove($path);
            $fs->touch($paths);
            $fs->chmod($path, 0777);
        }
    }
}
