<?php

return [
    'debugMode' => 1,
    'hostName' => 'http://pkbackend.127.0.0.1.xip.io',
    'clientHostName' => 'http://localhost:9000',
    'database' => [
        'host'       => '192.168.1.122',
        'username'   => 'geouser',
        'password'   => 'geouser',
        'db_geo'     => 'db_geodb',
        'db_pk'      => 'db_petakami'
    ],
    'geoserver' => [
        'username'      => 'admin',
        'password'      => 'geoserver',
        'db_host'       => 'localhost',
        'rest_url'      => 'http://192.168.1.122:8081/geoserver/rest',
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