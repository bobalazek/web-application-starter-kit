<?php

// Used for php web server
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$appMinPath = dirname(dirname(__FILE__)).'/app-min/bootstrap.php';
$appPath = dirname(dirname(__FILE__)).'/app/bootstrap.php';

if (file_exists($appMinPath)) {
    $app = require $appMinPath;
} else {
    $app = require $appPath;
}

if ($app['debug']) {
    $app->run();
} else {
    $app['http_cache']->run();
}
