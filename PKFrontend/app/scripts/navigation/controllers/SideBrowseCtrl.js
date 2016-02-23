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
            vm.layerGroupName = '';
            vm.tmpDrawTypes = [];
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
                    var drawTypes =param.split(':')[2].split(',');
                    for(var i=0; i<drawTypes.length; i++){
                        var draw = drawTypes[i];
                        svcLayer.getDrawTypeFromLayer(workspace, layer, draw, function(res){
                            if(res.records[0] == 'point') {
                                vm.selectedLayer['point'] = true;
                                vm.tmpDrawTypes.push({point : res.records[1]});
                            }
                            if(res.records[0] == 'linestring'){
                                vm.selectedLayer['linestring'] = true;
                                vm.tmpDrawTypes.push({linestring : res.records[1]});
                            }
                            if(res.records[0] == 'polygon') {
                                vm.selectedLayer['polygon'] = true;
                                vm.tmpDrawTypes.push({polygon : res.records[1]});
                            }
                            if(res.records[0] != undefined ){
                                vm.layers.push({'name':res.records[1], 'type': res.records[0]});
                            }
                        });
                    }

                    vm.layerGroupName = layer.replace(/_/g, ' ');

                    vm.selectedWorkspace = workspace;
                    //
                    //svcLayer.getLayersWithDrawType(workspace, layer, function(result){
                    //    var records = result.records;console.log(records)
                    //    for(var i=0; i<records.length; i++){
                    //        var tmp = records[i].split('_');
                    //        var type = tmp[tmp.length-1];
                    //
                    //        svcLayer.getDrawTypeFromLayer(workspace, layer, draw, function(res){
                    //            if(res.records[0] != undefined ){
                    //                vm.layers.push({'name':res.records[1], 'type': res.records[0]});
                    //            }
                    //        })
                    //
                    //
                    //    }
                    //})
                }
            }

            function layerSelectChange(){
                var dTypes = '';

                if(vm.selectedLayer['point']){
                    dTypes += _getLayer('point')+',';
                }
                if(vm.selectedLayer['linestring']) {
                    dTypes += _getLayer('linestring')+',';
                }
                if(vm.selectedLayer['polygon']) {
                    dTypes += _getLayer('polygon')+',';
                }
console.log(dTypes + " ");
                var layer = vm.layerGroupName.replace(/[ ]+/g, '_');
                $window.location.href = '/#/view/' + vm.selectedWorkspace+':'+layer+':'+dTypes;
            }

            function _getLayer(key){
                var layer = '';
                var arr = vm.tmpDrawTypes;
                for(var i=0; i<arr.length; i++){
                    if(arr[i][key] != undefined){
                        layer = arr[i][key];
                    }
                }
                return layer;
            }

            function viewLayer(workspace, layer){
                layer = layer.replace(/[ ]+/g, '_');
                vm.setType = '';
                svcLayer.getLayerFromWorkspace(workspace, layer, function(response){
                    var records = response.records;
                    for(var i=0; i<records.length; i++){
                        vm.setType += records[i]+',';
                    }
                    $window.location.href = '/#/view/' + workspace+':'+layer+':'+vm.setType;
                });

                //svcLayer.getLayerByWorkspace(workspace, function (response){
                //    layer = layer.replace(/[ ]+/g, '_');
                //    var records = response.records;
                //    var setType = 'd';
                //    for(var i=0; i<records.length; i++){
                //        if(records[i][0] == layer){
                //            var tmpType = records[i][1].split('_');
                //            for(var j=0; j<tmpType.length; j++){
                //                if(tmpType[j] == 'point') setType += '_p';
                //                if(tmpType[j] == 'line') setType += '_l';
                //                if(tmpType[j] == 'poly') setType += '_pl';
                //            }
                //        }
                //    }
                //    $window.location.href = '/#/view/' + workspace+':'+layer+':'+setType;
                //})
            }

            function changeWorkspace(workspace){
                vm.displayLayer = false;
                vm.layerGroups = [];
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
                        vm.layerGroups.push({'name':name, 'type': type});
                    }
                });
            }
    }]);
