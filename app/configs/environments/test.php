<?php

return [
    'debug' => true,
    'show_profiler' => false,

    // Database stuff
    'database_options' => [
        'default' => [
            'driver' => 'pdo_sqlite',
            'path' => STORAGE_DIR.'/database/testing.db',
        ],
    ],

    // Error options
    'error_options' => [
        'save_into_the_database' => false,
        'send_by_email' => false,
    ],
];
