'use strict';

angular.module('pkfrontendApp')
    .controller('SideDrawCtrl', controller);

controller.$inject = ['$scope', 'svcWorkspace', 'svcSharedProperties', 'svcLayer'];

function controller($scope, svcWorkspace, svcSharedProperties, svcLayer){
        var vm = this;
        vm.addAlert = addAlert;
        vm.closeAlert = closeAlert;
        vm.changeWorkspace = changeWorkspace;
        vm.saveLayer = saveLayer;
        vm.isDisabled = isDisabled;

        init();

        function init(){
            var point = [], line=[], poly=[];
            vm.setDrawTypes = [];
            vm.setWorkspaces = [];
            vm.selectedDrawType = '';
            vm.layerGroupName = '';
            vm.alerts = [];

            svcWorkspace.getWorkspaces(function(result){
                vm.setWorkspaces = result.data;
            });


            $scope.$on('pk.draw.feature', function(event, data){
                var feature = data;
                switch(feature.getGeometry().getType()){
                    case 'Point':
                        if(point.id != feature.getProperties().id){
                            point[feature.getProperties().id] = feature.getGeometry().getCoordinates();
                        }
                        else {
                            point.id = data;
                        }
                        break;
                    case 'LineString':
                        if(line.id != feature.getProperties().id){
                            line[feature.getProperties().id] = feature.getGeometry().getCoordinates();
                        }
                        else {
                            line.id = data;
                        }
                        break;
                    case 'Polygon':
                        if(poly.id != feature.getProperties().id){
                            poly[feature.getProperties().id] = feature.getGeometry().getCoordinates();
                        }
                        else {
                            poly.id = data;
                        }
                        break;
                }
            });
        }

        function isDisabled(text){
            var tmpVal = svcSharedProperties.getLayerValues();
            if(text == undefined || tmpVal == undefined){
                return true;
            }
            return false;
        }

        function addAlert(layerGroupName){
            vm.alerts.push({type: 'success', msg: 'Layer '+layerGroupName+' telah dibuat!'});
        }

        function closeAlert(index){
            vm.alerts.splice(index, 1);
        }

        function changeWorkspace(workspace){
            svcWorkspace.getWorkspaceWithDrawTyp(workspace, function(result){
                vm.setDrawTypes = result.data;
            });
        }

        function saveLayer(workspace, layerGroupName){
            var tmpVal = svcSharedProperties.getLayerValues();
            var tmpType = {'point': '', 'line': '', 'poly':''};

            if(tmpVal.point.length > 0){
                tmpType.point =  tmpVal.point;
            } else {
                delete tmpType['point'];
            }
            if(tmpVal.line.length > 0){
                tmpType.line =  tmpVal.line;
            }else {
                delete tmpType['line'];
            }
            if(tmpVal.poly.length > 0){
                tmpType.poly =  tmpVal.poly;
            }else {
                delete tmpType['poly'];
            }


            var obj = {
                "name": layerGroupName,
                "workspace": workspace,
                "type": tmpType
            };

            svcLayer.addLayer(obj, function(response){
                //TODO: what should i do after add layer?
            });
        }
}