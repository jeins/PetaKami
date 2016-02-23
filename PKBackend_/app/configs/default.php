<?php

return [

    'application' => [
        'baseUri' => '/',
    ],

    'authentication' => [
        'secret' => 'this_should_be_changed',
        'expirationTime' => 86400 * 7, // One week till token expires
    ],
    'acl' => [
        'publicEndpoints' => [
            '/user/authenticate',
            '/workspace/all',
            '/workspace/{workspace}/draw',
            '/layer/{workspace}',
            '/layer/{workspace}/{layer}/geojson'
        ],
        'privateEndpoints' => [
            '/user/me'
        ],
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
