<?php

return [
    'acl' => [
        'publicEndpoints' => [
            '/user/authenticate',
            '/workspace/all',
            '/workspace/{workspace}/draw',
            '/layer/{workspace}',
            '/layer/{workspace}/{layer}/geojson',
            '/layer/{workspace}/{layerGroupName}/bbox',
            '/layer/{workspace}/{layerGroupName}/{layer}/drawtype',
            '/layer/{workspace}/{layerGroupName}'
        ],
        'privateEndpoints' => [
            '/user/me'
        ],
    ],
];