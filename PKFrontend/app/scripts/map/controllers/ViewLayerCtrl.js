'use strict';

angular.module('pkfrontendApp')
    .controller('ViewLayerCtrl', [
        '$scope', '$stateParams', 'svcSharedProperties', 'svcLayer', 'olData', 'svcSecurity',
        function ($scope, $stateParams, svcSharedProperties, svcLayer, olData, svcSecurity) {
            var vm = this;
            vm.init = init;

            init();

            function init(){
                svcSharedProperties.setSelectedNav('browse');

                var params = svcSecurity.decode($stateParams.layer);
                var workspace = params.split(':')[0];
                var layerGroup = params.split(':')[1];
                var layerAndDrawType = params.split(':')[2].split(';');
                var layers = '';
                for(var i=0; i<layerAndDrawType.length; i++){
                    var layer = layerAndDrawType[i].split('?')[0];
                    if(layer != ""){
                        layers += layer + ',';
                    }
                }

                svcLayer.getFeatureCollectionFilterByLayer(workspace, layers, function(response){
                    $scope.layer = {
                        source: {
                            type: 'GeoJSON',
                            geojson: {
                                object: response,
                                projection: 'EPSG:3857'
                            }
                        }
                    };

                    svcLayer.getBbox(workspace, layerGroup, function(response){
                        var records = response.data;
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
                        lat: -0.4055727193536711,
                        lon: 116.19846321160155,
                        zoom: 5
                    },
                    mouseposition: '',
                    layer: {}
                });
            }
        }]);
