<?php

namespace Application\Tool;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Composer
{
    /**
     * Post install composer commands.
     */
    public static function postInstallCmd()
    {
        Storage::prepare();
        Environment::prepare();
        Console::updateDatabaseSchema();
    }

    /**
     * Post update composer commands.
     */
    public static function postUpdateCmd()
    {
        self::postInstallCmd();
    }

    /**
     * Downloads composer.
     */
    public static function download()
    {
        return Console::execute('curl -sS https://getcomposer.org/installer | php -- --install-dir=bin');
    }

    /**
     * Updates composer.
     */
    public static function update()
    {
        return Console::execute('php bin/composer.phar update');
    }

    /**
     * @return bool
     */
    public static function isInstalled()
    {
        if (`which composer`) {
            return true;
        }

        return false;
    }
}
