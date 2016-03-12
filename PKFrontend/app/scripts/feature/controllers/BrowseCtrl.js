'use strict';

angular.module('pkfrontendApp')
    .controller('BrowseCtrl', BrowseCtrl);

BrowseCtrl.$inject = ['$scope', '$log', 'svcPkLayer', 'svcSecurity', '$window', 'svcLayer', '$filter'];
function BrowseCtrl($scope, $log, svcPkLayer, svcSecurity, $window, svcLayer, $filter) {
    var vm = this;
    vm.init = init;
    vm.viewLayer = viewLayer;
    vm.editLayer = editLayer;
    vm.getData = getData;
    vm.downloadLayer = downloadLayer;

    init();

    function init(){
        vm.isLoading = false;
    }

    function downloadLayer(layer, workspace){
        svcLayer.getFeatureCollectionGeoJson(workspace, layer, function(response){
            var blob = new Blob([angular.toJson(response)], { type:"application/json;charset=utf-8;" });
            var downloadLink = angular.element('<a></a>');
            downloadLink.attr('href',$window.URL.createObjectURL(blob));
            downloadLink.attr('download', layer+'.json');
            downloadLink[0].click();
        });
    }

    function getData(tableState){
        vm.isLoading = true;

        var pagination = tableState.pagination;
        console.log(tableState);
        var start = pagination.start || 1;
        var number = pagination.number || 5;
        svcPkLayer.getLayers(0, 0, function(response){
            var items = response.items;

            var filtered = tableState.search.predicateObject ? $filter('filter')(items, tableState.search.predicateObject) : items;

            if (tableState.sort.predicate) {
                filtered = $filter('orderBy')(filtered, tableState.sort.predicate, tableState.sort.reverse);
            }

            var result = filtered.slice(start, start + number);

            vm.dataTables = items;
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