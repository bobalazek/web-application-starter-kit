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
        echo self::execute('php bin/console orm:schema-tool:update --force --dump-sql --complete');
    }

    /**
     * @return void
     */
    public static function prepare($output = false)
    {
        if ($output) {
            $output->writeln('<info>Preparing storage...</info>');
        }
        Storage::prepare();

        if ($output) {
            $output->writeln('<info>Preparing environment...</info>');
        }
        Environment::prepare();

        if ($output) {
            $output->writeln('<info>Preparing composer...</info>');
        }
        Composer::download();
        Composer::update();

        if ($output) {
            $output->writeln('<info>Updating database schema...</info>');
        }
        self::updateDatabaseSchema();
    }
}
