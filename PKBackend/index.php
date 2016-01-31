<?php

require 'Config/App.php';

$app = new \PetaKami\Config\App();
$response = $app->response;
//$response->setContentType('application/json', 'UTF-8');
//$response->setHeader('Content-type: ', 'application/json');
$response->setHeader('Access-Control-Allow-Origin', '*');
$response->setHeader("Access-Control-Allow-Methods", "POST, GET, PUT");
$response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
$response->sendHeaders();
$app->handle();