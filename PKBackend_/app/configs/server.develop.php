<?php

return [

    'debugMode' => 1, // 0; no developer messages // 1; developer messages and CoreExceptions
    'hostName' => 'http://phalcon-rest-boilerplate.vagrantserver.com',
    'clientHostName' => 'http://phalcon-rest-app.vagrantserver.com',
    'database' => [
        'host'       => '192.168.1.134',#'128.199.125.35',
        'username'   => 'geouser',#'pk_geouser',
        'password'   => 'geouser',#'P3t4K4M1!',
        'db_geo'     => 'geodb',#'pk_geodb',
        'db_pk'      => 'db_petakami'
    ]
];