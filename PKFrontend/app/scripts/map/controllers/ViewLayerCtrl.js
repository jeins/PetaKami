'use strict';

angular.module('pkfrontendApp')
    .controller('ViewLayerCtrl', [
        '$scope', '$stateParams', 'svcSharedProperties',
        function ($scope, $stateParams, svcSharedProperties) {
            var vm = this;
            vm.init = init;

            init();

            function init(){
                svcSharedProperties.setSelectedNav('browse');

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

            }
        }]);
