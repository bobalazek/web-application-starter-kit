<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

umask(0000);

/********** Set Timezone **********/
date_default_timezone_set('Europe/Berlin');

/********** Definitions **********/
require __DIR__.'/core/definitions.php';

/********** Autoloader **********/
$vendorAutoloaderFilePath = VENDOR_DIR.'/autoload.php';
if (
    php_sapi_name() != 'cli' &&
    !file_exists($vendorAutoloaderFilePath)
) {
    throw new \Exception('Please run "composer install" first!');
}

$autoloader = require $vendorAutoloaderFilePath;

/********** Dotenv **********/
if (file_exists(ROOT_DIR.'/.env')) {
    $dotenv = new Dotenv\Dotenv(ROOT_DIR);
    $dotenv->load();
}

/********** Application **********/
$app = new Application();

require APP_DIR.'/core/providers.php';
require APP_DIR.'/core/middlewares.php';
require APP_DIR.'/core/routes.php';

Request::enableHttpMethodParameterOverride();

return $app;
