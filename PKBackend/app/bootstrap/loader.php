<?php

$appPath = __DIR__ . '/../';
$vendorPath = __DIR__ . '/../../vendor/';

// Require Composer autoload
require_once $vendorPath . 'autoload.php';

$loader = new \Phalcon\Loader();

$loader->registerNamespaces([
    'PetaKami'              => $appPath . 'library/App/',
    'PetaKami\Controllers'  => $appPath . 'controllers/',
    'PetaKami\Models'       => $appPath . 'models/',
    'PetaKami\Transformers' => $appPath . 'transformers/',
    'PetaKami\Collections'  => $appPath . 'collections/'
]);

$loader->register();
