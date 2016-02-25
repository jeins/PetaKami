'use strict';

angular.module('pkfrontendApp')
    .controller('EditLayerCtrl', controller);

controller.$inject = ['$scope', '$stateParams', 'svcSharedProperties', 'svcLayer', 'olData', 'svcSecurity', '$http'];

function controller($scope, $stateParams, svcSharedProperties, svcLayer, olData, svcSecurity, $http){
    var vm = this;
    vm.init = init;

    init();

    function init(){
        $http({
            method: 'GET',
            url: 'http://openlayers.org/en/v3.7.0/examples/data/geojson/countries.geojson'
        }).then(function successCallback(response) {
            $scope.layer = {
                source: {
                    type: 'GeoJSON',
                    geojson: {
                        object: response.data,
                        projection: 'EPSG:3857'
                    }
                }
            };
        });


        angular.extend($scope, {
            defaults: {
                events: {
                    map: ['pointermove']
                }
            },
            indonesia: {
                lat: 0,
                lon: 0,
                zoom: 2
            },
            mouseposition: '',
            layer: {}
        });
    }
}