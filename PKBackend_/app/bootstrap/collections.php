<?php

$app->mount(new \PhalconRest\Collection\ResourceCollection());
$app->mount(new \PetaKami\Collections\WorkspaceCollection());
$app->mount(new \PetaKami\Collections\LayerCollection());
$app->mount(new \PetaKami\Collections\PkUserCollection());
$app->mount(new \PetaKami\Collections\PkLayerCollection());
