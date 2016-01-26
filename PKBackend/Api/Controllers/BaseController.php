<?php


namespace PetaKami\Controllers;

use Phalcon\Di;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;


class BaseController extends Controller
{
    public function afterExecuteRoute(Dispatcher $dispatcher) {
        $this->response->setContentType('application/json', 'UTF-8');
        $data = $dispatcher->getReturnedValue();

        if (is_array($data)) {
            $data['success'] = isset($data['success']) ?: true;
            $data['message'] = isset($data['message']) ?: '';
            $data = json_encode($data);
        }

        $this->response->setContent($data);
    }

    protected function optionsBase(){
        $response = $this->di->get('response');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, HEAD');
        $response->setHeader('Access-Control-Allow-Origin', $this->di->get('request')->header('Origin'));
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Allow-Headers', "origin, x-requested-with, content-type");
        $response->setHeader('Access-Control-Max-Age', '86400');
        return true;
    }

    protected function getRequestBody()
    {
        $request = $this->di->get('request');
        return $request->getJsonRawBody();
    }
}