<?php

return array(
    array(
        'id' => 1,
        'username' => 'admin',
        'email' => 'admin@myapp.com',
        'plainPassword' => 'test',
        'profile' => array(
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'male',
            'birthdate' => '01-01-1990',
        ),
        'roles' => array(
            'ROLE_SUPER_ADMIN',
            'ROLE_ADMIN',
        ),
    ),
    array(
        'id' => 2,
        'username' => 'test',
        'email' => 'test@myapp.com',
        'plainPassword' => 'test',
        'profile' => array(
            'firstName' => 'John',
            'lastName' => 'Doe',
        ),
        'roles' => array(
            'ROLE_TESTER',
        ),
    ),
);
