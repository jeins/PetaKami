'use strict';

angular.module('pkfrontendApp')
    .factory('svcWorkspace', [
        '$q', '$http', 'CONFIG', function($q, $http, CONFIG) {
            function getWorkspaces(doneCallback){
                $http(setupRequest('/workspaces', 'GET', ''))
                    .then(function (response){
                        doneCallback(response.data);
                    });
            }

            function getWorkspaceWithDrawTyp(workspace, doneCallback){
                $http(setupRequest('/workspace/'+workspace+'/draw', 'GET', ''))
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
                getWorkspaces: getWorkspaces,
                getWorkspaceWithDrawTyp: getWorkspaceWithDrawTyp
            }
        }]);