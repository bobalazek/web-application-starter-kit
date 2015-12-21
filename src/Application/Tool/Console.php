<?php

namespace Application\Tool;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class Console
{
    /**
     * @return string|null
     */
    public static function execute($command = '', $quiet = true)
    {
        return $quiet
            ? @shell_exec($command)
            : shell_exec($command)
        ;
    }

    /**
     * @return string
     */
    public static function updateDatabaseSchema()
    {
        echo \Application\Tool\Console::execute('php bin/console orm:schema-tool:update --force --dump-sql --complete');
    }

    /**
     * @return void
     */
    public static function prepare($output = false)
    {
        if ($output) {
            $output->writeln('<info>Preparing storage...</info>');
        }
        \Application\Tool\Storage::prepare();

        if ($output) {
            $output->writeln('<info>Preparing environment...</info>');
        }
        \Application\Tool\Environment::prepare();

        if ($output) {
            $output->writeln('<info>Preparing composer...</info>');
        }
        \Application\Tool\Composer::download();
        \Application\Tool\Composer::update();

        if (\Application\Tool\Bower::isInstalled()) {
            if ($output) {
                $output->writeln('<info>Preparing bower...</info>');
            }
            \Application\Tool\Bower::update();
        } else {
            if ($output) {
                $output->writeln('<info>Preparing bowerphp...</info>');
            }
            \Application\Tool\BowerPhp::download();
            \Application\Tool\BowerPhp::update();
        }

        if ($output) {
            $output->writeln('<info>Updating database schema...</info>');
        }
        \Application\Tool\Console::updateDatabaseSchema();
    }
}
