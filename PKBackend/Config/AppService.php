<?php


namespace PetaKami\Config;

use PetaKami\Routes\RouteLoader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url;
use Phalcon\Db\Adapter\Pdo\Postgresql as DatabaseConnection;

require __DIR__ . '/AppLoader.php';

class AppService extends AppLoader
{
    private $di;

    public function __construct()
    {
        parent::__construct();

        $this->di = new FactoryDefault();
        $this->setRouteCollections();
        $this->setConfig();
        $this->di->setShared('url', $this->setUrl());
        $this->di->setShared('db', $this->setDB());
    }

    public function getDependencyInjection(){
        return $this->di;
    }

    private function setConfig()
    {
        $this->di->set('config', function(){
           return json_encode(array_values(array_merge(
               $this->setupDB(),
               $this->setupApplication(),
               $this->setupGeoServer()
           )));
        });
    }

    private function setRouteCollections()
    {
        $this->di->set('collections', function(){
            return RouteLoader::getRouteCollections();
        });
    }

    private function setUrl()
    {
        $url = new Url();
        if(!$this->application->debug){
            $url->setBaseUri($this->application->production->baseUri);
            $url->setStaticBaseUri($this->application->production->staticBaseUri);
        } else {
            $url->setBaseUri($this->application->development->baseUri);
            $url->setStaticBaseUri($this->application->development->staticBaseUri);
        }
        return $url;
    }

    private function setDB()
    {
        $connection = new DatabaseConnection($this->database->toArray());
        $debug = $this->application->debug;
        if($debug){
            //SETUP DEBUG
        }

        return $connection;
    }
}