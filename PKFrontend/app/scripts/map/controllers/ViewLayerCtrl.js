'use strict';

angular.module('pkfrontendApp')
    .controller('ViewLayerCtrl', [
        '$scope', '$stateParams', 'svcSharedProperties', 'svcLayer', 'olData',
        function ($scope, $stateParams, svcSharedProperties, svcLayer, olData) {
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

                    svcLayer.getBBox(workspace, layer, function(response){
                        var records = response.records;
                        for(var i=0; i<records.length; i++){
                            records[i] = parseFloat(records[i]);
                        }
                        olData.getMap().then(function(map) {
                            var view = map.getView();
                            var extent = records;
                            extent = ol.extent.applyTransform(extent, ol.proj.getTransform("EPSG:4326", "EPSG:3857"));
                            view.fit(extent, map.getSize(), {nearest: true});
                        });
                    });
                });

                angular.extend($scope, {
                    defaults: {
                        events: {
                            map: ['pointermove']
                        }
                    },
                    indonesia: {
                        lat: -1.5767477849425404,
                        lon: 123.91423963552285,
                        zoom: 5
                    },
                    mouseposition: '',
                    layer: {}
                });



            }
        }]);
