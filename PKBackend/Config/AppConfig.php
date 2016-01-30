<?php


namespace PetaKami\Config;

use Phalcon\Config;

class AppConfig extends Config
{
    public function __construct()
{
    parent::__construct(array_merge($this->setupDB(), $this->setupApplication()));
}

    /**
     * @return array
     */
    public function setupDB()
    {
        return [
          'database' => [
              'host'       => '192.168.1.134',
              'dbname'     => 'geodb',
              'username'   => 'geouser',
              'password'   => 'geouser',
          ]
        ];
    }

    /**
     * @return array
     */
    public function setupApplication()
    {
        return [
            'application' => [
                'controllersDir' => __DIR__ . '/../Api/Controllers/',
                'commonsDir'     => __DIR__ . '/../Api/Common/',
                'routeDir'       => __DIR__ . '/../Api/Routes/',
                'processorDir'   => __DIR__ . '/../Api/Processors/',
                'configDir'      => __DIR__ . '/../Config/',
                'logsDir'        => __DIR__ . '/../Logs/',
                'development'    => [
                    'staticBaseUri' => '',
                    'baseUri'       => '/'
                ],
                'production' => [
                    'staticBaseUri' => '',
                    'baseUri'       => '/'
                ],
                'debug' => true,
            ]
        ];
    }

    public function setupGeoServer(){
        return [
            'geoserver' => [
                'DB_HOST'       => 'localhost',
                'REST_URL'      => 'http://192.168.1.134:8080/geoserver/rest',
                'DATASTORE_TYP' => 'PostGIS',
                'WORKSPACE'     => [
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
    }
}