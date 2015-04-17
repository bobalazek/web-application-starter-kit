<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

umask(0000);

/********** Set Timezone **********/
date_default_timezone_set('Europe/Vienna');

/********** Definitions **********/
include __DIR__.'/core/definitions.php';

/********** Autoloader **********/
$vendorAutoloaderFilePath = VENDOR_DIR.'/autoload.php';
if (! file_exists($vendorAutoloaderFilePath) && php_sapi_name() != 'cli') {
    exit('Please run "<b>composer install</b>" first!');
}

$autoloader = require $vendorAutoloaderFilePath;

/********** Application **********/
$app = new \Silex\Application();

include_once APP_DIR.'/core/functions.php';
include_once APP_DIR.'/core/providers.php';
include_once APP_DIR.'/core/middlewares.php';
include_once APP_DIR.'/core/routes.php';

Request::enableHttpMethodParameterOverride();

return $app;
