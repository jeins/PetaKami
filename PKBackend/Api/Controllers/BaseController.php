<?php


namespace PetaKami\Controllers;

use Phalcon\DI;
use Phalcon\DI\Injectable;


class BaseController extends Injectable
{

    public function __construct(){
        //parent::__construct();
        $di = DI::getDefault();
        $this->setDI($di);
    }
}