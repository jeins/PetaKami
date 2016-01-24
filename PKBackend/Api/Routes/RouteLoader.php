<?php


namespace PetaKami\Routes;


class RouteLoader
{
    public static function getRouteCollections()
    {
        return call_user_func(function(){

            $collections = [];
            $collectionFiles = scandir(__DIR__ . '/RouteCollections');

            foreach($collectionFiles as $collectionFile){
                $pathinfo = pathinfo($collectionFile);
                //Only include php files
                if($pathinfo['extension'] === 'php'){
                    // The collection files return their collection objects, so mount
                    // them directly into the router.
                    $collections[] = include(__DIR__ .'/RouteCollections/' . $collectionFile);
                }
            }
            return $collections;
        });
    }
}