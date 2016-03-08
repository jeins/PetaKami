<?php

return [
    'acl' => [
        'publicEndpoints' => [
            '/user/authenticate',
            '/user/register',
            '/layer/{workspace}/{layers}/bylayer/geojson'
        ],
        'privateEndpoints' => [
            '/user/me',
            '/ulayer',
            '/ulayer/user',
            '/ulayer/workspace/{workspace}',
            '/workspace/all',
            '/workspace/{workspace}/draw',
            '/layer/add',
            '/layer/edit',
            '/layer/{workspace}',
            '/layer/{workspace}/{layerGroupName}/geojson',
            '/layer/{workspace}/{layerGroupName}/bbox',
            '/layer/{workspace}/{layerGroupName}/{layer}/drawtype',
            '/layer/{workspace}/{layerGroupName}',
            '/layer/upload_files/{type}/{key}',
            '/layer/upload_layers/{workspace}/{dataStore}/{key}'
        ],
    ],
];