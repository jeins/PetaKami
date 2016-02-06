'use strict';

angular.module('pkfrontendApp')
    .controller('UploadCtrl', ['$scope', 'Upload', function ($scope, Upload) {
        var vm = this

        vm.init = init;

        init();

        function init(){

        }
    }]);
