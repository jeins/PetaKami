<?php

return [
    'acl' => [
        'publicEndpoints' => [
            '/user/authenticate',
            '/user/register',
            '/user/active/{hash}',
            '/workspace/all',
            '/ulayer/workspace/{workspace}',
            '/layer/{workspace}/{layerGroupName}',
            '/layer/{workspace}/{layerGroupName}/bbox',
            '/layer/{workspace}/{layers}/bylayer/geojson'
        ],
        'privateEndpoints' => [
            '/user/me',
            '/ulayer/add',
            '/ulayer/{limit}/{currentPage}',
            '/ulayer/user',
            '/workspace/{workspace}/draw',
            '/layer/geoserver',
            '/layer/add',
            '/layer/edit',
            '/layer/{workspace}',
            '/layer/{workspace}/{layerGroupName}/geojson',
            '/layer/{workspace}/{layerGroupName}/{layer}/drawtype',
            '/layer/upload_files/{type}/{key}',
            '/layer/upload_layers/{workspace}/{dataStore}/{key}'
        ],
    ],
];