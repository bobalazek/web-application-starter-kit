<?php

return array(
    'environment' => 'development',
    'debug' => true,
    'name' => 'Web Application Starter Kit',
    'version' => '1.0.0-rc.5',
    'author' => 'Borut Balazek',

    // Admin email (& name)
    'email' => array('info@bobalazek.com' => 'Web Application Starter Kit Mailer'),

    // Default Locale / Language stuff
    'locale' => 'en_US', // Default locale
    'language_code' => 'en', // Default language code
    'language_name' => 'English',
    'country_code' => 'us', // Default country code
    'flag_code' => 'us',
    'date_format' => 'd.m.Y',
    'date_time_format' => 'd.m.Y H:i:s',

    'locales' => array( // All available locales
        'en_US' => array(
            'language_code' => 'en',
            'language_name' => 'English',
            'country_code' => 'us',
            'flag_code' => 'us',
            'date_format' => 'd.m.Y',
            'date_time_format' => 'd.m.Y H:i:s',
        ),
        'de_DE' => array(
            'language_code' => 'de',
            'language_name' => 'Deutsch',
            'country_code' => 'de',
            'flag_code' => 'de',
            'date_format' => 'd.m.Y',
            'date_time_format' => 'd.m.Y H:i:s',
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

    // Database / Doctrine options
    // http://silex.sensiolabs.org/doc/providers/doctrine.html#parameters
    'database_options' => array(
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
    'swiftmailer_options' => array(
        'host' => 'corcosoft.com',
        'port' => 465,
        'username' => 'info@corcosoft.com',
        'password' => '',
        'encryption' => 'ssl',
        'auth_mode' => null,
    ),

    // Remember me options
    // http://silex.sensiolabs.org/doc/providers/remember_me.html#options
    'remember_me_options' => array(
        'key' => 'someRandomKey',
        'name' => 'user',
        'remember_me_parameter' => 'remember_me',
    ),

    // User System options
    'user_system_options' => array(
        'roles' => array(
            'ROLE_SUPER_ADMIN' => 'Super admin',
            'ROLE_ADMIN' => 'Admin',
            'ROLE_USERS_EDITOR' => 'Users editor',
            'ROLE_POSTS_EDITOR' => 'Posts editor',
            'ROLE_USER' => 'User',
        ),
        'registrations_enabled' => true,
    ),

    // Error options
    'error_options' => array(
        'save_into_the_database' => true,
        'send_by_email' => false,
    ),

    // Default settings (the setting values from the DB
    //   will override this values)
    'settings' => array(
        'foo' => 'bar',
    ),
);
