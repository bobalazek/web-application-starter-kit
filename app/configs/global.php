<?php

return array(
    'environment' => 'development',
    'debug' => true,
    'name' => 'Web Application Starter Kit',
    'version' => '0.14.1',
    'author' => 'Borut Balazek',

    // Admin email (& name)
    'email' => 'info@bobalazek.com',
    'emailName' => 'Web Application Starter Kit Mailer',

    // Default Locale / Language stuff
    'locale' => 'en_US', // Default locale
    'languageCode' => 'en', // Default language code
    'languageName' => 'English',
    'countryCode' => 'us', // Default country code
    'flagCode' => 'us',
    'dateFormat' => 'd.m.Y',
    'dateTimeFormat' => 'd.m.Y H:i:s',

    'locales' => array( // All available locales
        'en_US' => array(
            'languageCode' => 'en',
            'languageName' => 'English',
            'countryCode' => 'us',
            'flagCode' => 'us',
            'dateFormat' => 'd.m.Y',
            'dateTimeFormat' => 'd.m.Y H:i:s',
        ),
        'de_DE' => array(
            'languageCode' => 'de',
            'languageName' => 'Deutsch',
            'countryCode' => 'de',
            'flagCode' => 'de',
            'dateFormat' => 'd.m.Y',
            'dateTimeFormat' => 'd.m.Y H:i:s',
        ),
    ),

    // Environments
    'environments' => array(
        'testing' => array(
            'domain' => 'testing.example.com',
            'uri' => '/',
            'directory' => '/home/example/domains/example.com/subdomains/testing',
        ),
        'staging' => array(
            'domain' => 'staging.example.com',
            'uri' => '/',
            'directory' => '/home/example/domains/example.com/subdomains/staging',
        ),
        'production' => array(
            'domain' => 'example.com',
            'uri' => '/',
            'directory' => '/home/example/domains/example.com/public_html',
        ),
    ),

    // Time and date
    'currentTime' => date('H:i:s'),
    'currentDate' => date('Y-m-d'),
    'currentDateTime' => date('Y-m-d H:i:s'),

    // Database / Doctrine options
    // http://silex.sensiolabs.org/doc/providers/doctrine.html#parameters
    'databaseOptions' => array(
        'default' => array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'wask',
            'user' => 'wask',
            'password' => 'wask',
            'charset' => 'utf8',
        ),
    ),

    // Swiftmailer options
    // http://silex.sensiolabs.org/doc/providers/swiftmailer.html#parameters
    'swiftmailerOptions' => array(
        'host' => 'corcosoft.com',
        'port' => 465,
        'username' => 'info@corcosoft.com',
        'password' => '',
        'encryption' => 'ssl',
        'auth_mode' => null,
    ),

    // Remember me options
    // http://silex.sensiolabs.org/doc/providers/remember_me.html#options
    'rememberMeOptions' => array(
        'key' => 'someRandomKey',
        'name' => 'user',
        'remember_me_parameter' => 'remember_me',
    ),

    // User System options
    'userSystemOptions' => array(
        'roles' => array(
            'ROLE_SUPER_ADMIN' => 'Super admin',
            'ROLE_ADMIN' => 'Admin',
            'ROLE_USERS_EDITOR' => 'Users editor',
            'ROLE_POSTS_EDITOR' => 'Posts editor',
            'ROLE_USER' => 'User',
        ),
        'registrationEnabled' => true,
    ),
    
    // Error options
    'errorOptions' => array(
        'saveIntoTheDatabase' => true,
        'sendByEmail' => false,
    ),

    // Default settings (the setting values from the DB
    //   will override this values)
    'settings' => array(
        'foo' => 'bar',
    ),
);
