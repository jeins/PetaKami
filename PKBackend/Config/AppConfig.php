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
    private function setupDB()
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
    private function setupApplication()
    {
        return [
            'application' => [
                'controllersDir' => __DIR__ . '/../Api/Controllers/',
                'commonsDir'     => __DIR__ . '/../Api/Common/',
                'routeDir'       => __DIR__ . '/../Api/Routes/',
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
                'debug' => true
            ]
        ];
    }
}