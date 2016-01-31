'use strict';

angular.module('pkfrontendApp')
    .controller('SideBrowseCtrl', [
        '$scope', 'svcLayer', 'svcWorkspace', '$window', '$stateParams',
        function ($scope, svcLayer, svcWorkspace, $window, $stateParams) {
            var vm = this;

            vm.setWorkspaces = [];
            vm.layerGroups = [];
            vm.layers = [];
            vm.selectedLayer = [];
            vm.selectedWorkspace = '';
            vm.displayLayer = false;
            vm.init = init;
            vm.changeWorkspace = changeWorkspace;
            vm.viewLayer = viewLayer;
            vm.layerSelectChange = layerSelectChange;

            init();

            function init(){
                svcWorkspace.getWorkspaces(function(result){
                    vm.setWorkspaces = result.records;
                });

                if($stateParams.layer != undefined){
                    vm.displayLayer = true;
                    var param = $stateParams.layer;
                    var workspace = param.split(':')[0];
                    var layer = param.split(':')[1];

                    vm.selectedWorkspace = workspace;

                    svcLayer.getLayersWithDrawType(workspace, layer, function(result){
                        var records = result.records;
                        for(var i=0; i<records.length; i++){
                            var tmp = records[i].split('_');
                            var type = tmp[tmp.length-1];
                            var name = (records[i]).replace(/_/g, ' ');
                            vm.layers.push({'name':name, 'type': type});
                        }
                    })
                }
            }

            function layerSelectChange(){
                console.log(vm.selectedLayer);
            }

            function viewLayer(workspace, layer){
                layer = layer.replace(/[ ]+/g, '_');
                $window.location.href = '/#/view/' + workspace+':'+layer;
            }

            function changeWorkspace(workspace){
                vm.displayLayer = false;
                svcLayer.getLayerByWorkspace(workspace, function(response){
                    var records = response.records;
                    for(var i=0; i<records.length; i++){
                        var types = records[i][1].split('_');
                        var type = '';
                        /*for(var j=0; j<types.length; j++){
                         if(types[j] == 'point') type += '<img src="images/point_draw_type.png">';
                         else if(types[j] == 'line') type += '<img src="images/line_draw_type.png">';
                         else if(types[j] == 'poly') type += '<img src="images/polygon_draw_type.png">';
                         }*/
                        var name = (records[i][0]).replace(/_/g, ' ');
                        vm.layerGroups.push({'name':name, 'type': type});console.log(type);
                    }
                });
            }
    }]);
