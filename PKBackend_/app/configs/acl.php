<?php

return [
    'acl' => [
        'publicEndpoints' => [
            '/user/authenticate',
            '/workspace/all',
            '/workspace/{workspace}/draw',
            '/layer/add',
            '/layer/{workspace}',
            '/layer/{workspace}/{layerGroupName}/geojson',
            '/layer/{workspace}/{layers}/bylayer/geojson',
            '/layer/{workspace}/{layerGroupName}/bbox',
            '/layer/{workspace}/{layerGroupName}/{layer}/drawtype',
            '/layer/{workspace}/{layerGroupName}',
            '/layer/upload_files/{type}/{key}',
            '/layer/upload_layers/{workspace}/{dataStore}/{key}'
        ],
        'privateEndpoints' => [
            '/user/me'
        ],
    ],
];