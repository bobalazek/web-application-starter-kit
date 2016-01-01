<?php

namespace Application\Tool;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Environment
{
    /**
     * @return void
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
