<?php

namespace Application\Tool;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Environment
{
    /**
     * Prepares some files for the development environment.
     */
    public static function prepare()
    {
        $root = dirname(dirname(dirname(dirname(__FILE__))));

        if (!file_exists($root.'/app/configs/global-local.php')) {
            fopen($root.'/app/configs/global-local.php', 'w');
        }

        if (!file_exists($root.'/app/configs/environments/development.php')) {
            fopen($root.'/app/configs/environments/development.php', 'w');
        }
    }
}
