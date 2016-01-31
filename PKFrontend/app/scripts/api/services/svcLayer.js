'use strict';

angular.module('pkfrontendApp')
    .factory('svcLayer', [
        '$q', '$http', 'CONFIG', function($q, $http, CONFIG) {

            function getLayerByWorkspace(workspace, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layers'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getLayersWithDrawType(workspace, layer, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layer/'+layer+'/draw'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getLayersInGeoJSON(workspace, layer, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layer/'+layer+'/geojson'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getLayerDrawTypeInGeoJSON(workspace, layer, drawType, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layer/'+layer+'/draw'+drawType+'/geojson'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function addLayer(body, doneCallback){console.log(body);
                $http(setupRequest('/layer', 'POST', body))
                    .then(function (response){
                        doneCallback(response.data);
                    });
            }

            function editLayer(body, doneCallback){
                $http(setupRequest('/layer/1', 'PUT', body))
                    .then(function (response){
                        doneCallback(response.data);
                    });
            }

            function setupRequest(uri, method, data){
                return {
                    url: CONFIG.http.rest_host + uri,
                    method: method,
                    data: data
                }
            }

            return {
                addLayer: addLayer,
                editLayer: editLayer,
                getLayerByWorkspace: getLayerByWorkspace,
                getLayersWithDrawType: getLayersWithDrawType,
                getLayersInGeoJSON: getLayersInGeoJSON,
                getLayerDrawTypeInGeoJSON: getLayerDrawTypeInGeoJSON
            }
        }]);