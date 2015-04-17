<?php

namespace Application\Tool;

class Environment
{
    public static function prepare()
    {
        if (! file_exists('app/configs/global-local.php')) {
            fopen('app/configs/global-local.php', 'w');
        }
    }
}
