<?php

return array(
    'debug' => false,

    // Database stuff
    'databaseOptions' => array(
        'default' => array(
            'driver' => 'pdo_sqlite',
            'path' => STORAGE_DIR.'/database/testing.db',
        ),
    ),

    // Error options
    'errorOptions' => array(
        'saveIntoTheDatabase' => false,
        'sendByEmail' => false,
    ),
);
