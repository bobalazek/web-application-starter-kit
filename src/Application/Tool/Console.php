<?php

namespace Application\Tool;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class Console
{
    /**
     * @param string $command
     *
     * @return string|null
     */
    public static function execute($command = '')
    {
        return shell_exec($command);
    }

    /**
     * @return string
     */
    public static function updateDatabaseSchema()
    {
        echo self::execute('php bin/console orm:schema-tool:update --force --dump-sql --complete');
    }

    /**
     * @param OutputInterface $output
     */
    public static function prepare(OutputInterface $output = null)
    {
        if ($output) {
            $output->writeln('<info>Preparing storage...</info>');
        }
        Storage::prepare();

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
