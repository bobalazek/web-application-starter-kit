<?php

namespace Deployer;

host('qa_server')
    ->hostname('123.123.123.123')
    ->user('root')
    ->port(22)
    ->forwardAgent(true)
    ->multiplexing(true)
    ->stage('qa')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/wask/deployment/qa')
    ->set('env', [
        'APPLICATION_ENVIRONMENT' => 'qa',
    ]);

host('prod_server')
    ->hostname('123.123.123.123')
    ->user('root')
    ->port(22)
    ->forwardAgent(true)
    ->multiplexing(true)
    ->stage('prod')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/wask/deployment/prod')
    ->set('env', [
        'APPLICATION_ENVIRONMENT' => 'prod',
    ]);
