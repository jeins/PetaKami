<?php

use PetaKami\Constants\Services as PKServices;
use PetaKami\Auth\UserAccountType;
use PetaKami\Services\UserService;
use Phalcon\Db\Adapter\Pdo\Postgresql as DatabaseConnection;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\Router;
use PhalconRest\Auth\TokenParser\JWT;
use PhalconRest\Auth\Manager;

$di = new \PhalconRest\Di\FactoryDefault();

$di->setShared(PKServices::CONFIG, function() use($config){
   return $config;
});

$di->set(PKServices::DB_GEO, function() use($config, $di){
    $connection = new DatabaseConnection([
        'host'       => $config->database->host,
        'dbname'     => $config->database->db_geo,
        'username'   => $config->database->username,
        'password'   => $config->database->password,
    ]);

    $connection->setEventsManager($di->get(PKServices::EVENTS_MANAGER));

    return $connection;
});

$di->set(PKServices::DB_PK, function() use($config, $di){
    $connection = new DatabaseConnection([
        'host'       => $config->database->host,
        'dbname'     => $config->database->db_pk,
        'username'   => $config->database->username,
        'password'   => $config->database->password,
    ]);

    $connection->setEventsManager($di->get(PKServices::EVENTS_MANAGER));

    return $connection;
});

$di->set(PKServices::URL, function() use($config){
    $url = new Url();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

$di->set(PKServices::ROUTER, function(){
    return new Router();
});

/**
 * @description TokenParser
 */
$di->setShared(PKServices::TOKEN_PARSER, function () use ($di, $config) {

    return new JWT($config->authentication->secret, JWT::ALGORITHM_HS256);
});

/**
 * @description AuthManager
 */
$di->setShared(PKServices::AUTH_MANAGER, function () use ($di, $config) {

    $authManager = new Manager($config->authentication->expirationTime);
    $authManager->registerAccountType(UserAccountType::EMAIL, new UserAccountType());

    return $authManager;
});

/**
 * @description App - \Library\App\Services\UserService
 */
$di->setShared(PKServices::USER_SERVICE, function () {
    return new UserService;
});

return $di;