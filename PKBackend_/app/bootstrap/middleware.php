<?php

use PetaKami\Constants\Services as PKServices;

$eventsManager = $di->get(PKServices::EVENTS_MANAGER);

/**
 * NotFound handler
 */
$eventsManager->attach('micro', new \PhalconRest\Middleware\NotFound);

/**
 * Authenticate user
 */
$eventsManager->attach('micro', new \PhalconRest\Middleware\Authentication);

/**
 * Authorize endpoints
 */
$eventsManager->attach('micro', new \PhalconRest\Middleware\Acl($config->acl->privateEndpoints, $config->acl->publicEndpoints));

/**
 * Fractal - Set includes
 */
$eventsManager->attach('micro', new \PhalconRest\Middleware\Fractal);

/**
 * Request - Allow OPTIONS
 */
$eventsManager->attach('micro', new \PhalconRest\Middleware\OptionsResponse);

/**
 * Queries - Process queries
 */
$eventsManager->attach('micro', new \PhalconRest\Middleware\UrlQuery);