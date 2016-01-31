'use strict';

angular.module('pkfrontendApp')
    .controller('ViewLayerCtrl', ['$scope', '$stateParams', function ($scope, $stateParams) {
        var vm = this;

        angular.extend($scope, {
            defaults: {
                events: {
                    map: ['pointermove']
                }
            },
            indonesia: {
                lat: -1.5767477849425404,
                lon: 123.91423963552285,
                zoom: 5
            },
            mouseposition: ''
        });


    }]);
