<?php

namespace Application\Tool;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Storage
{
    /**
     * Prepare all the folders and files for storage.
     */
    public static function prepare()
    {
        self::prepareFolders(array(
            'var',
            'var/backups',
            'var/backups/database',
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
     * Prepare the folders for storage.
     *
     * @param array $paths
     * @param bool  $removeIfExists
     */
    public static function prepareFolders(array $paths = array(), $removeIfExists = false)
    {
        if (empty($paths)) {
            return false;
        }

        $fs = new Filesystem();

        foreach ($paths as $path) {
            if (
                $removeIfExists &&
                $fs->exists($path)
            ) {
                $fs->remove($path);
            }

            $fs->mkdir($paths);
            $fs->chmod($path, 0777);
        }
    }

    /**
     * Prepare the uploads folder (so images can be uploaded).
     *
     * @param string $uploadsDirectory
     */
    public static function prepareUploadsFolder($uploadsDirectory)
    {
        if (!$uploadsDirectory) {
            return false;
        }

        $fs = new Filesystem();

        $uploadsDirectory = 'web/assets/uploads';

        if (!$fs->exists($uploadsDirectory)) {
            $fs->mkdir($uploadsDirectory, 0755);
        }

        $user = PHP_OS == 'Darwin' // Fix for OSX
            ? get_current_user()
            : 'www-data'
        ;

        try {
            $fs->chown($uploadsDirectory, $user);
            $fs->chmod($uploadsDirectory, 0755);
        } catch (\Exception $e) {
            // Could not chown and / or chmod the uploads directory
        }
    }

    /**
     * Prepare the log files.
     *
     * @param array $paths
     * @param bool  $removeIfExists
     */
    public static function prepareLogFiles(array $paths, $removeIfExists = false)
    {
        $fs = new Filesystem();

        foreach ($paths as $path) {
            if (
                $removeIfExists &&
                $fs->exists($path)
            ) {
                $fs->remove($path);
            }

            $fs->touch($path);
            $fs->chmod($path, 0777);
        }
    }
}
