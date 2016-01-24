<?php


namespace PetaKami\Controllers;

use Phalcon\DI;
use Phalcon\DI\Injectable;


class BaseController extends Injectable
{

    public $connection;

    public $config;

    public function __construct(){
        //parent::__construct();
        $di = DI::getDefault();
        $this->setDI($di);

        $this->connection = $this->di->get('db');
        $this->config = $this->di->get('config');
    }
}