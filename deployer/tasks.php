<?php

namespace Deployer;

/*** Generate .env file ***/
// Generate the .env file from specified environment variables,
//   defined in deployer/hosts.php, for example:
// ->set('env', ['APPLICATION_ENVIRONMENT' => 'qa'])
// This means, it will generate o ".env" file on the server,
//   on the release/project root, with the following contents:
// APPLICATION_ENVIRONMENT="qa"
desc('Generating .env file');
task('deploy:generate_env_file', function () {
    run('cd {{release_path}} && touch .env');

    $env = get('env');
    foreach ($env as $key => $val) {
        $str = $key.'="'.$val.'"';
        run('cd {{release_path}} && echo "'.addslashes($str).'" >> .env');
    }
});
after('deploy:update_code', 'deploy:generate_env_file');

/*** Bower vendors ***/
// Intall bower vendors
desc('Installing bower vendors');
task('deploy:vendors_bower', function () {
    $command = 'export BOWER_TOKEN='.get('BOWER_TOKEN').' && '.
        'bower --allow-root login -t '.get('BOWER_TOKEN').' &&  '.
        'bower --allow-root install';
    run('cd {{release_path}} && '.$command);
});
after('deploy:vendors', 'deploy:vendors_bower');

/*** Writable ***/
// Prepare writable files & folders (defined inside deployer/config.php)
after('deploy:vendors_bower', 'deploy:writable');

/*** Doctrine schema update ***/
// Migrate the database schema
desc('Updating database schema');
task('database:schema_update', function () {
    run('{{bin/php}} {{bin/console}} orm:schema:update --force');
});
after('deploy:vendors', 'database:schema_update');

/*** PHP FPM ***/
// If you have FPM installed on your server(-s)
desc('Reloading PHP-FPM');
task('php-fpm:reload', function () {
    run('service php7.0-fpm reload');
})->onStage('prod'); // You can specify on which stage you want it to be executed
after('deploy:unlock', 'php-fpm:reload');

/*** Deployment unlock ***/
// Unlock on failed deployment
after('deploy:failed', 'deploy:unlock');
