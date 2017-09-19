<?php

return [
    'environment' => 'dev',
    'debug' => true,
    'show_profiler' => true,
    'name' => 'WASK',
    'version' => '3.0.0-rc1',
    'author' => 'Borut Balazek',

    // Admin email (& name)
    'email' => [
        'info@bobalazek.com' => 'WASK Mailer',
    ],

    // Default Locale / Language stuff
    'locale' => 'en_US', // Default locale
    'language_code' => 'en', // Default language code
    'language_name' => 'English',
    'country_code' => 'us', // Default country code
    'flag_code' => 'us',
    'date_format' => 'm/d/Y',
    'date_time_format' => 'm/d/Y H:i:s',

    'locales' => [ // All available locales
        'en_US' => [
            'language_code' => 'en',
            'language_name' => 'English',
            'country_code' => 'us',
            'flag_code' => 'us',
            'date_format' => 'm/d/Y',
            'date_time_format' => 'm/d/Y H:i:s',
        ],
        'de_DE' => [
            'language_code' => 'de',
            'language_name' => 'Deutsch',
            'country_code' => 'de',
            'flag_code' => 'de',
            'date_format' => 'd.m.Y',
            'date_time_format' => 'd.m.Y H:i:s',
        ],
    ],

    // Database / Doctrine options
    // http://silex.sensiolabs.org/doc/providers/doctrine.html#parameters
    'database_options' => [
        'default' => [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => getenv('APPLICATION_DATABASE_NAME') ?: 'wask',
            'user' => getenv('APPLICATION_DATABASE_USER') ?: 'wask',
            'password' => getenv('APPLICATION_DATABASE_PASSWORD') ?: 'wask',
            'charset' => 'utf8',
        ],
    ],

    // Swiftmailer options
    // http://silex.sensiolabs.org/doc/providers/swiftmailer.html#parameters
    'swiftmailer_options' => [
        'host' => 'corcosoft.com',
        'port' => 465,
        'username' => 'info@corcosoft.com',
        'password' => '',
        'encryption' => 'ssl',
        'auth_mode' => null,
    ],

    // Remember me options
    // http://silex.sensiolabs.org/doc/providers/remember_me.html#options
    'remember_me_options' => [
        'key' => 'my123random456super789key',
        'name' => 'user',
        'remember_me_parameter' => 'remember_me',
    ],

    // User System options
    'user_system_options' => [
        'roles' => [
            'ROLE_SUPER_ADMIN' => 'Super admin',
            'ROLE_ADMIN' => 'Admin',
            'ROLE_USERS_EDITOR' => 'Users editor',
            'ROLE_POSTS_EDITOR' => 'Posts editor',
            'ROLE_USER' => 'User',
        ],
        'registrations_enabled' => true,
        'reset_password_expiry_time' => '15 minutes', // How how long should the token / code be valid? It also prevents the user from re-requesting the password again in this time-frame, so you may not set that too high
    ],

    // Error options (will be executed only when debug is set to false)
    'error_options' => [
        'save_into_the_database' => true,
        'send_by_email' => false,
    ],

    // Default settings (the setting values from the DB
    //   will override this values)
    'settings' => [
        'foo' => 'bar',
    ],
];
