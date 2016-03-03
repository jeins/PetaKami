'use strict';

angular.module('pkfrontendApp')
    .factory('svcPkLayer', service);

service.$inject = ['$http', 'CONFIG'];
function service($http, CONFIG){
	return {
		getUserLayers: getUserLayers,
		getByWorkspace: getByWorkspace,
		getLayers: getLayers,
		addUserLayer: addUserLayer
	};

	function getUserLayers(doneCallback){
        $http(_setupRequest('/ulayer/user', 'GET'))
            .then(function(response){
                doneCallback(response.data);
            });
	}

	function getByWorkspace(workspace, doneCallback){
        $http(_setupRequest('/ulayer/workspace/' + workspace, 'GET'))
            .then(function(response){
                doneCallback(response.data);
            });
	}

	function getLayers(doneCallback){
        $http(_setupRequest('/ulayer', 'GET'))
            .then(function(response){
                doneCallback(response.data);
            });
	}

	function addUserLayer(body, doneCallback){
        $http(_setupRequest('/ulayer', 'POST', body))
            .then(function (response){
                doneCallback(response.data);
            });
	}

    function _setupRequest(uri, method, data){
        return {
            url: CONFIG.http.rest_host + uri,
            method: method,
            data: data
        }
    }
}