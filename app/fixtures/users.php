<?php

return [
    [
        'id' => 1,
        'username' => 'admin',
        'email' => 'admin@myapp.com',
        'plainPassword' => 'test',
        'profile' => [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'male',
            'birthdate' => '1990-01-01',
        ],
        'roles' => [
            'ROLE_SUPER_ADMIN',
            'ROLE_ADMIN',
        ],
    ],
    [
        'id' => 2,
        'username' => 'test',
        'email' => 'test@myapp.com',
        'plainPassword' => 'test',
        'profile' => [
            'firstName' => 'John',
            'lastName' => 'Doe',
        ],
        'roles' => [
            'ROLE_TESTER',
        ],
    ],
];
