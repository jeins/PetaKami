<?php

use PetaKami\Constants\PKConst;
use PetaKami\Auth\UserAccountType;
use PetaKami\Services\UserService;
use Phalcon\Db\Adapter\Pdo\Postgresql as DatabaseConnection;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Model\Manager;
use PhalconRest\Auth\TokenParser\JWT;
use PhalconRest\Auth\Manager as PRManager;
use PhalconRest\DI\FactoryDefault;

$di = new FactoryDefault();

$di->setShared(PKConst::CONFIG, function() use($config){
   return $config;
});

$di->set(PKConst::DB_GEO, function() use($config, $di){
    $connection = new DatabaseConnection([
        'host'       => $config->database->host,
        'dbname'     => $config->database->db_geo,
        'username'   => $config->database->username,
        'password'   => $config->database->password,
    ]);

    $connection->setEventsManager($di->get(PKConst::EVENTS_MANAGER));

    return $connection;
});

$di->set(PKConst::DB, function() use($config, $di){
    $connection = new DatabaseConnection([
        'host'       => $config->database->host,
        'dbname'     => $config->database->db_pk,
        'username'   => $config->database->username,
        'password'   => $config->database->password,
    ]);

    $connection->setEventsManager($di->get(PKConst::EVENTS_MANAGER));

    return $connection;
});

$di->set(PKConst::URL, function() use($config){
    $url = new Url();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

$di->set(PKConst::ROUTER, function(){
    return new Router();
});

/**
 * @description TokenParser
 */
$di->setShared(PKConst::TOKEN_PARSER, function () use ($di, $config) {

    return new JWT($config->authentication->secret, JWT::ALGORITHM_HS256);
});

/**
 * @description AuthManager
 */
$di->setShared(PKConst::AUTH_MANAGER, function () use ($di, $config) {

    $authManager = new PRManager($config->authentication->expirationTime);
    $authManager->registerAccountType(UserAccountType::EMAIL, new UserAccountType());

    return $authManager;
});

/**
 * @description Phalcon - \Phalcon\Mvc\Model\Manager
 */
$di->setShared(PKConst::MODELS_MANAGER, function () use ($di) {

    $modelsManager = new Manager;
    return $modelsManager->setEventsManager($di->get(PKConst::EVENTS_MANAGER));
});

/**
 * @description App - \Library\App\Services\UserService
 */
$di->setShared(PKConst::USER_SERVICE, function () {
    return new UserService;
});

return $di;