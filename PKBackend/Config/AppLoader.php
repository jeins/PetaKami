<?php


namespace PetaKami\Config;

use Phalcon\Loader;

require __DIR__ . '/AppConfig.php';

class AppLoader extends AppConfig
{
    private $loader;

    public function __construct()
    {
        parent::__construct();

        $this->loader = new Loader();
        $this->loader->registerNamespaces(
            [
                'PetaKami\Config'       => $this->application->configDir,
                'PetaKami\Controllers'  => $this->application->controllersDir,
                'PetaKami\Common'       => $this->application->commonsDir,
                'PetaKami\Routes'       => $this->application->routeDir
            ]
        );
        $this->loader->register();
    }
}