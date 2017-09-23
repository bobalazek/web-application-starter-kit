<?php

namespace Deployer;

set('timezone', 'Europe/Berlin');

set('ssh_type', 'native');
set('ssh_multiplexing', true);

set('repository', 'git@github.com:bobalazek/web-application-starter-kit.git');
set('default_stage', 'qa');
set('BOWER_TOKEN', '[MY_BOWER_TOKEN]');

set('shared_dirs', ['var/cache', 'var/logs', 'var/sessions', 'web/assets/uploads']);
set('writable_dirs', ['var/cache', 'var/logs', 'var/sessions', 'web/assets/uploads']);

set('bin/console', '{{release_path}}/bin/console');

set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');
