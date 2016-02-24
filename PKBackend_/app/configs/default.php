<?php

return [

    'application' => [
        'baseUri' => '/',
        'tmpFolder' => __DIR__ . '/../../tmp_files/',
    ],

    'authentication' => [
        'secret' => 'this_should_be_changed',
        'expirationTime' => 86400 * 7, // One week till token expires
    ],
    'geoserver' => [
        'username'      => 'admin',
        'password'      => 'geoserver',
        'db_host'       => 'localhost',
        'rest_url'      => 'http://192.168.1.134:8080/geoserver/rest',#'http://128.199.125.35:8080/geoserver/rest',
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
