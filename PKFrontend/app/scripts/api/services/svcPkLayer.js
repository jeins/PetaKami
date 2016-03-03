'use strict';

angular.module('pkfrontendApp')
    .factory('svcPkLayer', service);

service.$inject = ['$log'];
function service($log){
	return {
		getUserLayers: getUserLayers,
		getLayers: getLayers,
		addUserLayer: addUserLayer
	}

	function getUserLayers(){

	}

	function getLayers(){

	}

	function addUserLayer(){
		
	}
}