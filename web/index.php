<?php

// Used for php web server
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = require dirname(dirname(__FILE__)).'/app/bootstrap.php';

if ($app['debug']) {
    $app->run();
} else {
    $app['http_cache']->run();
}
