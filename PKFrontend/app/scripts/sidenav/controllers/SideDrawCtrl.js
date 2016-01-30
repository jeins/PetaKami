'use strict';

angular.module('pkfrontendApp')
    .controller('SideDrawCtrl', ['$scope', 'svcWorkspace', function ($scope, svcWorkspace) {
        var vm = this;

        vm.setDrawTypes = [];
        vm.setWorkspaces = [];
        vm.selectedDrawType = '';
        vm.layerGroupName = '';
        vm.changeWorkspace = changeWorkspace;

        svcWorkspace.getWorkspaces(function(result){
            vm.setWorkspaces = result.records;
        });

        function changeWorkspace(workspace){
            svcWorkspace.getWorkspaceWithDrawTyp(workspace, function(result){
                vm.setDrawTypes = result.records;
            });
        }
    }]);
