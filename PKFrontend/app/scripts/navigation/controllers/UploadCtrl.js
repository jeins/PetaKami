'use strict';

angular.module('pkfrontendApp')
    .controller('UploadCtrl', [
        '$scope', 'Upload', 'svcWorkspace', 'svcLayer','CONFIG', '$timeout',
        function ($scope, Upload, svcWorkspace, svcLayer, CONFIG, $timeout) {
            var vm = this;

            vm.timeNow = '';
            vm.workspaces = '';
            vm.selectedWorkspace = '';
            vm.layerGroupName = '';
            vm.showPoint = false; vm.showLineString = false; vm.showPolygon = false;
            vm.isCollapsed = true;
            vm.isPointSelected = 'pk-dropbox_brown'; vm.isLineSelected = 'pk-dropbox_brown'; vm.isPolySelected = 'pk-dropbox_brown';
            vm.init = init;
            vm.changeWorkspace = changeWorkspace;
            vm.uploadLayerGroup = uploadLayerGroup;
            vm.uploadToGeoServer = uploadToGeoServer;

            init();

            function init(){
                svcWorkspace.getWorkspaces(function(result){
                    vm.workspaces = result.records;
                });
            }

            function changeWorkspace(workspace){
                vm.isCollapsed = false;
                vm.showPoint = false;
                vm.showLineString = false;
                vm.showPolygon = false;
                svcWorkspace.getWorkspaceWithDrawTyp(workspace, function(result){
                    var drawTypes = result.records;
                    var date = new Date();
                    vm.timeNow = date.getTime();
                    for(var i=0; i<drawTypes.length; i++){
                        if(drawTypes[i] == "Point"){
                            vm.showPoint = true;
                        } else if(drawTypes[i] == "LineString"){
                            vm.showLineString = true;
                        } else if(drawTypes[i] == "Polygon"){
                            vm.showPolygon = true;
                        }
                    }
                });
            }

            function uploadLayerGroup($file, type){
                if(_isMimeTypeAllow($file.name) && !$file.$error){
                    if(type == 'point'){
                        vm.isPointSelected = 'pk-dropbox_green';
                    } else if(type == 'linestring'){
                        vm.isLineSelected = 'pk-dropbox_green';
                    } else if(type == 'polygon'){
                        vm.isPolySelected = 'pk-dropbox_green';
                    }

                    Upload.upload({
                        url: CONFIG.http.rest_host + '/upload/' + type +'/' + vm.timeNow,
                        method: 'POST',
                        file: $file
                    }).then(function(response){
                        $timeout(function(){
                            console.log(response);
                        });
                    })
                }
            }

            function _isMimeTypeAllow(name){
                var res = name.toLowerCase().split('.');
                if(res[res.length-1] == 'zip' || res[res.length-1] == 'csv' || res[res.length-1] == 'json'){
                    return true;
                }
                return false;
            }

            function uploadToGeoServer(){
                svcLayer.uploadFileToGeoServer(vm.selectedWorkspace, vm.layerGroupName, vm.timeNow, function(result){

                });
            }
        }
    ]);
