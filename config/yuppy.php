<?php

return [

    /*
    | Admin account created by Database\Seeders\AdminSeeder. Admins cannot
    | self-register. In production the password MUST be provided via env.
    */
    'seed_admin' => [
        'email' => env('SEED_ADMIN_EMAIL', 'admin@yuppy.test'),
        'password' => env('SEED_ADMIN_PASSWORD'),
    ],

];
