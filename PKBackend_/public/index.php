<?php

use PetaKami\Constants\Services as PKServices;

$application_env = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development';

$response = null;

try{
    // Read the configuration based on env
    $config = require __DIR__ . '/../app/bootstrap/config.php';

    // Include loader
    require __DIR__ . "/../app/bootstrap/loader.php";

    // Setup all required services (DI)
    $di = require __DIR__ . "/../app/bootstrap/services.php";

    // Instantiate main application
    $app = new \Phalcon\Mvc\Micro($di);

    // Attach the EventsManager to the main application in order to attach Middleware
    $eventsManager = $app->di->get(PKServices::EVENTS_MANAGER);
    $app->setEventsManager($eventsManager);

    // Attach Middleware to EventsManager
    require __DIR__ . "/../app/bootstrap/middleware.php";

    // Mount Collections
    require __DIR__ . "/../app/bootstrap/collections.php";

    // Start the application
    $app->handle();

    // Set content
    $returnedValue = $app->getReturnedValue();

    if($returnedValue != null){
        if(is_string($returnedValue)) $app->response->setContent($returnedValue);
        else $app->response->setJsonContent($returnedValue);
    }

    $response = $app->response;
} catch(Exception $e){
    $response = $di->get(PKServices::RESPONSE);
    $response->setErrorContent($e, $application_env == 'development');
}

// Send response
if($response){
    $response->sendHeaders();
    $response->send();
}