<?php

namespace Deployer;

localhost('local_server')
    ->stage('dev')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/html/wask')
    ->set('env', [
        'APPLICATION_ENVIRONMENT' => 'dev',
    ])
;
