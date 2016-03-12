<?php

return [

    'application' => [
        'baseUri' => '/',
        'tmpFolder' => __DIR__ . '/../../tmp_files/',
    ],

    'authentication' => [
        'secret' => '$PK8sN9+<c_p@7YfY0`+_>%3V5x}FK d?]4*{QoHAYr-bAy|*BCl`I;%rBFAH~$@',
        'expirationTime' => 86400 * 7, // One week till token expires
    ],

    'mail' => [
        'fromName'  => 'PetaKami',
        'fromEmail' => 'petakami@gmail.com',
        'smtp'  => [
            'server'    => 'smtp.gmail.com',
            'port'      => 465,
            'security'  => 'ssl',
            'username'  => 'petakami@gmail.com',
            'password'  => 'P3t4K4M1!'
        ]
    ]
];
