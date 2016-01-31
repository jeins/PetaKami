'use strict';

angular.module('pkfrontendApp')
    .controller('SideDrawCtrl', [
        '$scope', 'svcWorkspace', 'svcSharedProperties', 'svcLayer',
        function ($scope, svcWorkspace, svcSharedProperties, svcLayer) {
        var vm = this;

        var point = [], line=[], poly=[];
        vm.setDrawTypes = [];
        vm.setWorkspaces = [];
        vm.selectedDrawType = '';
        vm.layerGroupName = '';
        vm.changeWorkspace = changeWorkspace;
        vm.saveLayer = saveLayer;

        init();

        function init(){
            svcWorkspace.getWorkspaces(function(result){
                vm.setWorkspaces = result.records;
            });


            $scope.$on('pk.draw.feature', function(event, data){console.log("OK");
                var feature = data;
                switch(feature.getGeometry().getType()){
                    case 'Point':
                        if(point.id != feature.getProperties().id)
                            point[feature.getProperties().id] = feature.getGeometry().getCoordinates();
                        else point.id = data;
                        break;
                    case 'LineString':
                        if(line.id != feature.getProperties().id)
                            line[feature.getProperties().id] = feature.getGeometry().getCoordinates();
                        else line.id = data;
                        break;
                    case 'Polygon':
                        if(poly.id != feature.getProperties().id)
                            poly[feature.getProperties().id] = feature.getGeometry().getCoordinates();
                        else poly.id = data;
                        break;
                }
            });
        }

        function changeWorkspace(workspace){
            svcWorkspace.getWorkspaceWithDrawTyp(workspace, function(result){
                vm.setDrawTypes = result.records;
            });
        }

        function saveLayer(workspace, layerGroupName){
            var tmpVal = svcSharedProperties.getLayerValues();

            var obj = {
                "name": layerGroupName,
                "workspace": workspace,
                "type": {
                    'point': tmpVal.point,
                    'line': tmpVal.line,
                    'poly': tmpVal.poly
                }
            };

            svcLayer.addLayer(obj, function(response){
                //TODO: what should i do after add layer?
            });
        }
    }]);
