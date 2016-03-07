<?php

return array(
    'debug' => false,

    // Database stuff
    'database_options' => array(
        'default' => array(
            'driver' => 'pdo_sqlite',
            'path' => STORAGE_DIR.'/database/testing.db',
        ),
    ),

    // Error options
    'error_options' => array(
        'saveIntoTheDatabase' => false,
        'sendByEmail' => false,
    ),
);
