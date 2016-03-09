'use strict';

angular.module('pkfrontendApp')
    .controller('BrowseCtrl', BrowseCtrl);

BrowseCtrl.$inject = ['$scope', '$log', 'svcPkLayer', 'svcSecurity', '$window', 'svcLayer'];
function BrowseCtrl($scope, $log, svcPkLayer, svcSecurity, $window, svcLayer) {
    var vm = this;
    vm.init = init;
    vm.viewLayer = viewLayer;
    vm.editLayer = editLayer;
    vm.getData = getData;

    init();

    function init(){
        vm.isLoading = false;
    }

    function getData(tableState){
        vm.isLoading = true;

        var pagination = tableState.pagination;

        var currentPage = pagination.start || 1;
        var limit = pagination.number || 5;
        $log.info("CurrentPage: " + currentPage); $log.info("Limit: " + limit);
        svcPkLayer.getLayers(limit, currentPage, function(response){
            vm.dataTables = response.items;
            tableState.pagination.numberOfPages = response.total_pages;
            vm.isLoading = false;
        })
    }

    function viewLayer(layer, workspace){
        layer = layer.replace(/[ ]+/g, '_');
        vm.setType = '';
        svcLayer.getLayerAndDrawType(workspace, layer, function(response){
            var records = response.data;
            for(var i=0; i<records.length; i++){
                vm.setType += records[i].layer + '?' + records[i].drawType +';';
            }

            $window.location.href = '/#/view/' + svcSecurity.encode(workspace+':'+layer+':'+vm.setType);
            $window.location.reload();
        });
    }

    function editLayer(layer, workspace){
        layer = layer.replace(/[ ]+/g, '_');
        vm.setType = '';
        svcLayer.getLayerAndDrawType(workspace, layer, function(response){
            var records = response.data;
            for(var i=0; i<records.length; i++){
                vm.setType += records[i].layer + '?' + records[i].drawType +';';
            }

            $window.location.href = '/#/edit/' + svcSecurity.encode(workspace+':'+layer+':'+vm.setType);
            $window.location.reload();
        });
    }
}