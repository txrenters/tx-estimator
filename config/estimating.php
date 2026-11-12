<?php

return [

    // Optional first superadmin created by `php artisan db:seed`.
    'superadmin' => [
        'email' => env('DESK_SUPERADMIN_EMAIL'),
        'password' => env('DESK_SUPERADMIN_PASSWORD'),
    ],

    'pricing_defaults' => [
        'markup' => 30,
        'discount' => 0,
        'trip' => 0,
        'authorization' => 250,
        'manager' => 1000,
        'target' => 25,
    ],

];
