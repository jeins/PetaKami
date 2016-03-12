<?php

return [
    'acl' => [
        'publicEndpoints' => [
            '/user/authenticate',
            '/user/register',
            '/user/active/{hash}',
            '/layer/{workspace}/{layers}/bylayer/geojson'
        ],
        'privateEndpoints' => [
            '/user/me',
            '/ulayer/add',
            '/ulayer/{limit}/{currentPage}',
            '/ulayer/user',
            '/ulayer/workspace/{workspace}',
            '/workspace/all',
            '/workspace/{workspace}/draw',
            '/layer/geoserver',
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