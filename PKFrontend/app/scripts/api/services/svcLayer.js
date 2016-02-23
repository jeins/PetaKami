'use strict';

angular.module('pkfrontendApp')
    .factory('svcLayer', [
        '$q', '$http', 'CONFIG', function($q, $http, CONFIG) {

            function getLayerByWorkspace(workspace, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layers', 'GET'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getLayersWithDrawType(workspace, layer, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layer/'+layer+'/draw', 'GET'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getLayersInGeoJSON(workspace, layer, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layer/'+layer+'/geojson', 'GET'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getLayerDrawTypeInGeoJSON(workspace, layer, drawType, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layer/'+layer+'/draw/'+drawType+'/geojson', 'GET'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getBBox(workspace, layergroup, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/layer/'+layergroup+'/bbox', 'GET'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function addLayer(body, doneCallback){
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

            function uploadFileToGeoServer(workspace, layer, key, doneCallback){
                $http(setupRequest('/uploadtogs/' + workspace +'/' +layer +'/'+ key, 'PUT'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getDrawTypeFromLayer(workspace, layerGroup, layer, doneCallback){
                $http(setupRequest('/drawtype/' + workspace +'/' + layerGroup +'/' + layer +'', 'GET'))
                    .then(function(response){
                        doneCallback(response.data);
                    });
            }

            function getLayerFromWorkspace(workspace, layerGroup, doneCallback){
                $http(setupRequest('/layers/' + workspace +'/' + layerGroup, 'GET'))
                    .then(function(response){
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
                getLayerDrawTypeInGeoJSON: getLayerDrawTypeInGeoJSON,
                getBBox: getBBox,
                getDrawTypeFromLayer: getDrawTypeFromLayer,
                getLayerFromWorkspace: getLayerFromWorkspace,
                uploadFileToGeoServer: uploadFileToGeoServer
            }
        }]);