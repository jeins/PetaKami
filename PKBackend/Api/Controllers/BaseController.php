<?php


namespace PetaKami\Controllers;

use Phalcon\Di;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;


class BaseController extends Controller
{
    public function afterExecuteRoute(Dispatcher $dispatcher) {
        $this->optionsBase();
        $this->response->setContentType('application/json', 'UTF-8');
        $data = $dispatcher->getReturnedValue();

        if (is_array($data)) {
            $data['success'] = isset($data['success']) ?: true;
            $data['message'] = isset($data['message']) ?: '';
            $data = json_encode($data);
        }

        $this->response->setContent($data);
    }

    protected function getRequestBody()
    {
        $request = $this->di->get('request');
        return $request->getJsonRawBody();
    }
}