'use strict';

angular.module('pkfrontendApp')
    .controller('ViewLayerCtrl', [
        '$scope', '$stateParams', 'svcSharedProperties', 'svcLayer',
        function ($scope, $stateParams, svcSharedProperties, svcLayer) {
            var vm = this;
            vm.init = init;

            init();

            function init(){
                svcSharedProperties.setSelectedNav('browse');

                var workspace = $stateParams.layer.split(':')[0];
                var layer = $stateParams.layer.split(':')[1];
                var drawType = $stateParams.layer.split(':')[2];

                svcLayer.getLayerDrawTypeInGeoJSON(workspace, layer, drawType, function(response){
                    $scope.layer = {
                        source: {
                            type: 'GeoJSON',
                            geojson: {
                                object: response.records,
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
                        lat: -6.1766409627685,
                        lon: 106.82906985283,
                        zoom: 18
                    },
                    mouseposition: '',
                    layer: {}
                });



            }
        }]);
