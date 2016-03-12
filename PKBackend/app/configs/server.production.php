<?php

return [
    'debugMode' => 0,
    'hostName' => 'http://petakami.com',
    'clientHostName' => 'http://petakami.com',
    'database' => [
        'host'       => '128.199.125.35',
        'username'   => 'pk_geouser',
        'password'   => 'P3t4K4M1!',
        'db_geo'     => 'pk_geodb',
        'db_pk'      => 'db_petakami'
    ],
    'geoserver' => [
        'username'      => 'admin',
        'password'      => 'geoserver',
        'db_host'       => 'localhost',
        'rest_url'      => 'http://128.199.125.35:8080/geoserver/rest',
        'datastore_type'=> 'PostGIS',
        'workspaces'    => [
            'IDBangunan'        => ['Point', 'LineString', 'Polygon'],
            'IDTransportasi'    => ['LineString'],
            'IDHipsografi'      => ['Point', 'LineString'],
            'IDBatasDaerah'     => ['LineString', 'Polygon'],
            'IDTutupanLahan'    => ['LineString', 'Polygon'],
            'IDHydrografi'      => ['Point', 'LineString', 'Polygon'],
            'IDToponomi'        => ['Point']
        ]
    ]
];