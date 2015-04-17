<?php

return array(
    array(
        'id' => 1,
        'username' => 'bobalazek',
        'email' => 'bobalazek124@gmail.com',
        'plainPassword' => 'test',
        'profile' => array(
            'firstName' => 'Borut',
            'lastName' => 'Balažek',
            'gender' => 'male',
            'birthdate' => '03-09-1992',
        ),
        'roles' => array(
            'ROLE_SUPER_ADMIN',
            'ROLE_ADMIN',
        ),
    ),
    array(
        'id' => 2,
        'username' => 'test',
        'email' => 'test@test.com',
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
