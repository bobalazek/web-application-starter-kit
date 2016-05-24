<?php

namespace Application\Controller\MembersArea;

use Application\Tool\Helpers;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ToolsController
{
    /**
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Application $app)
    {
        if (
            !$app['security']->isGranted('ROLE_TOOLS') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        return new Response(
            $app['twig']->render(
                'contents/members-area/tools/index.html.twig'
            )
        );
    }

    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function databaseBackupAction(Request $request, Application $app)
    {
        if (
            !$app['security']->isGranted('ROLE_TOOLS') &&
            !$app['security']->isGranted('ROLE_ADMIN')
        ) {
            $app->abort(403);
        }

        $data = array();

        $backups = array();
        $action = $request->query->get('action');
        $selectedBackup = $request->query->get('backup');
        $backupData = null;
        $backupsDirectory = STORAGE_DIR.'/backups/database';

        if ($action == 'new') {
            $username = $app['database_options']['default']['user'];
            $password = $app['database_options']['default']['password'];
            $database = $app['database_options']['default']['dbname'];
            $file = $backupsDirectory.'/'.date('Ymd_His').'_new.sql';

            shell_exec('mysqldump -u '.$username.' -p'.$password.' '.$database.' > '.$file);

            $app['flashbag']->add(
                'success',
                'A new backup was successfully created!'
            );

            return $app->redirect(
                $app['url_generator']->generate(
                    'members-area.tools.database-backup'
                )
            );
        }

        if ($action == 'restore') {
            $username = $app['database_options']['default']['user'];
            $password = $app['database_options']['default']['password'];
            $database = $app['database_options']['default']['dbname'];
            $file = $backupsDirectory.'/'.$selectedBackup.'.sql';
            $fileOld = $backupsDirectory.'/'.date('Ymd_His').'_before_restore.sql';

            // First create a new backup!
            shell_exec('mysqldump -u '.$username.' -p'.$password.' '.$database.' > '.$fileOld);

            // Then restore it!
            shell_exec('mysqldump -u '.$username.' -p'.$password.' '.$database.' < '.$file);

            $app['flashbag']->add(
                'success',
                'The database has been successfully restored.'
            );

            return $app->redirect(
                $app['url_generator']->generate(
                    'members-area.tools.database-backup'
                )
            );
        }

        $backupsArray = Helpers::rglob($backupsDirectory.'/*.sql');
        if (!empty($backupsArray)) {
            foreach ($backupsArray as $backupFilePath) {
                $backupFileName = str_replace(
                    $backupsDirectory.'/',
                    '',
                    $backupFilePath
                );

                $backups[] = array(
                    'name' => $backupFileName,
                    'path' => $backupFilePath,
                    'size' => filesize($backupFilePath),
                );
            }

            $backups = array_reverse($backups);
        }

        if ($selectedBackup) {
            $backupData = file_get_contents($backupsDirectory.'/'.$selectedBackup);

            if (strlen($backupData) > 5000) {
                $backupData = substr($backupData, 0, 5000).' ...';
            }
        }

        $data['backups'] = $backups;
        $data['selectedBackup'] = $selectedBackup;
        $data['backupData'] = $backupData;

        return new Response(
            $app['twig']->render(
                'contents/members-area/tools/database-backup.html.twig',
                $data
            )
        );
    }
}
