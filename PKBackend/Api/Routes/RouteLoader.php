<?php


namespace PetaKami\Routes;


class RouteLoader
{
    public static function getRouteCollections()
    {
        return call_user_func(function(){

            $collections = [];
            $collectionDirs = scandir(__DIR__ . '/RouteCollections');

            foreach($collectionDirs as $collectionDir){
                if($collectionDir == '.' || $collectionDir == '..') continue;
                $collectionFiles = scandir(__DIR__ . '/RouteCollections/' . $collectionDir);

                foreach($collectionFiles as $collectionFile){
                    if($collectionFile == '.' ||$collectionFile == '..') continue;
                    $pathinfo = pathinfo($collectionFile);

                    if($pathinfo['extension'] === 'php'){
                        $collections[] = include(__DIR__ .'/RouteCollections/' .$collectionDir .'/'. $collectionFile);
                    }
                }
            }
            return $collections;
        });
    }
}