<?php

namespace Application\Tool;

class Composer
{
    public static function postInstallCmd()
    {
        \Application\Tool\Storage::prepare();
        \Application\Tool\Environment::prepare();
        \Application\Tool\Console::updateDatabaseSchema();
    }

    public static function postUpdateCmd()
    {
        self::postInstallCmd();
    }

    public static function download()
    {
        return \Application\Tool\Console::execute('curl -sS https://getcomposer.org/installer | php -- --install-dir=bin');
    }

    public static function update()
    {
        return \Application\Tool\Console::execute('php bin/composer.phar update');
    }

    public static function isInstalled()
    {
        $installed = false;

        if (`which composer`) {
            $installed = true;
        }

        return $installed;
    }
}
