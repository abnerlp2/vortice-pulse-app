<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Password
    |--------------------------------------------------------------------------
    |
    | This password is used to access the administrative dashboard.
    | It is retrieved from the .env file for security and cacheability.
    |
    */
    'admin_password' => env('ADMIN_PASSWORD'),
];
