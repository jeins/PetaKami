'use strict';

angular.module('pkfrontendApp')
    .controller('SideDrawCtrl', ['$scope', function ($scope) {
        var vm = this;

        vm.setDrawTypes = [
            {value: 'point', label: 'Point'},
            {value: 'line', label: 'LineString'},
            {value: 'polygon', label: 'Polygon'}
        ];
        vm.selectedDrawType = '';


    }]);
