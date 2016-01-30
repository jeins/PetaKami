<?php

require 'Config/App.php';

$app = new \PetaKami\Config\App();
$response = $app->response;
$response->setContentType('application/json', 'UTF-8');
$response->setHeader('Access-Control-Allow-Origin', '*');
$response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
$response->setHeader('Access-Control-Allow-Headers: ', 'Authorization');
$response->setHeader('Content-type: ', 'application/json');
$response->sendHeaders();
$app->handle();