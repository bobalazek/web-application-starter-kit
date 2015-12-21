<?php

namespace Application\Tool;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Composer
{
    /**
     * @return void
     */
    public static function postInstallCmd()
    {
        \Application\Tool\Storage::prepare();
        \Application\Tool\Environment::prepare();
        \Application\Tool\Console::updateDatabaseSchema();
    }

    /**
     * @return void
     */
    public static function postUpdateCmd()
    {
        self::postInstallCmd();
    }

    /**
     * @return void
     */
    public static function download()
    {
        return \Application\Tool\Console::execute('curl -sS https://getcomposer.org/installer | php -- --install-dir=bin');
    }

    /**
     * @return void
     */
    public static function update()
    {
        return \Application\Tool\Console::execute('php bin/composer.phar update');
    }

    /**
     * @return boolean
     */
    public static function isInstalled()
    {
        $installed = false;

        if (`which composer`) {
            $installed = true;
        }

        return $installed;
    }
}
