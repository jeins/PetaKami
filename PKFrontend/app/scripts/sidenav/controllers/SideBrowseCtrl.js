'use strict';

angular.module('pkfrontendApp')
    .controller('SideBrowseCtrl', [
        '$scope', 'svcLayer', 'svcWorkspace',
        function ($scope, svcLayer, svcWorkspace) {
            var vm = this;

            vm.setWorkspaces = [];
            vm.layers = [];
            vm.init = init;
            vm.changeWorkspace = changeWorkspace;
            vm.viewLayer = viewLayer;

            init();

            function init(){
                svcWorkspace.getWorkspaces(function(result){
                    vm.setWorkspaces = result.records;
                });
            }

            function viewLayer(layer){

            }

            function changeWorkspace(workspace){
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
                        vm.layers.push({'name':name, 'type': type});console.log(type);
                    }
                });
            }
    }]);
