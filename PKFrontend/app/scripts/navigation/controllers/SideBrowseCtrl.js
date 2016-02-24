'use strict';

angular.module('pkfrontendApp')
    .controller('SideBrowseCtrl', [
        '$scope', 'svcLayer', 'svcWorkspace', '$window', '$stateParams', '$log', 'svcSecurity',
        function ($scope, svcLayer, svcWorkspace, $window, $stateParams, $log, svcSecurity) {
            var vm = this;

            vm.setWorkspaces = [];
            vm.layerGroups = [];
            vm.layers = [];
            vm.selectedLayer = [];
            vm.selectedWorkspace = '';
            vm.displayLayer = false;
            vm.layerGroupName = '';
            vm.tmpDrawTypes = [];
            vm.init = init;
            vm.changeWorkspace = changeWorkspace;
            vm.viewLayer = viewLayer;
            vm.layerSelectChange = layerSelectChange;

            init();

            function init(){
                svcWorkspace.getWorkspaces(function(result){
                    vm.setWorkspaces = result.data;
                });

                if($stateParams.layer != undefined){
                    vm.displayLayer = true;
                    var param = svcSecurity.decode($stateParams.layer); $log.info("Request URI" + param);
                    var workspace = param.split(':')[0];
                    var layerGroup = param.split(':')[1];
                    var layersAndDrawTypes = param.split(':')[2].split(';');
                    for(var i=0; i<layersAndDrawTypes.length; i++){

                        if(layersAndDrawTypes[i] == "") {
                            continue;
                        }
                        var layerAndDrawType = layersAndDrawTypes[i].split('?');
                        var layer = layerAndDrawType[0];
                        var drawType = layerAndDrawType[1];
                        $log.info("Display Layer: " + layer);
                        if(layer != ""){
                            vm.selectedLayer[drawType] = true;
                        }
                        vm.tmpDrawTypes.push({drawType: layer});
                        vm.layers.push({'name': layer, 'type': drawType});
                    }

                    vm.layerGroupName = layerGroup.replace(/_/g, ' ');
                    vm.selectedWorkspace = workspace;
                }
            }

            function layerSelectChange(){
                var layer = vm.layerGroupName.replace(/[ ]+/g, '_');
                vm.setType = '';
                svcLayer.getLayerAndDrawType(vm.selectedWorkspace, layer, function(response){
                    var records = response.data;
                    for(var i=0; i<records.length; i++){
                        if(vm.selectedLayer['point'] && records[i].drawType == 'point'){
                            vm.setType += records[i].layer;
                        }
                        if(vm.selectedLayer['linestring'] && records[i].drawType == 'linestring'){
                            vm.setType += records[i].layer;
                        }
                        if(vm.selectedLayer['polygon'] && records[i].drawType == 'polygon'){
                            vm.setType += records[i].layer;
                        }

                        vm.setType += '?' + records[i].drawType +';'
                    }
                    $window.location.href = '/#/view/' + svcSecurity.encode(vm.selectedWorkspace+':'+layer+':'+vm.setType);
                });
            }

            function viewLayer(workspace, layer){
                layer = layer.replace(/[ ]+/g, '_');
                vm.setType = '';
                svcLayer.getLayerAndDrawType(workspace, layer, function(response){
                    var records = response.data;
                    for(var i=0; i<records.length; i++){
                        vm.setType += records[i].layer + '?' + records[i].drawType +';';
                    }

                    $window.location.href = '/#/view/' + svcSecurity.encode(workspace+':'+layer+':'+vm.setType);
                });
            }

            function changeWorkspace(workspace){
                vm.displayLayer = false;
                vm.layerGroups = [];
                svcLayer.getLayersFromWorkspace(workspace, function(response){
                    var records = response.data;
                    $log.info('Layers From Workspace %s :', workspace);
                    $log.info(records);
                    for(var layerName in records){
                        var type = '';
                        vm.layerGroups.push({'name':(layerName).replace(/_/g, ' '), 'type': type});
                    }
                });
            }
    }]);
