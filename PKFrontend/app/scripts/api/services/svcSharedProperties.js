'use strict';

angular.module('pkfrontendApp')
    .factory('svcSharedProperties', [
        '$q', function($q) {
            var layerValues;

            function setLayerValues(values){
                layerValues = values;
            }

            function getLayerValues(){
                return layerValues;
            }

            return {
                setLayerValues: setLayerValues,
                getLayerValues: getLayerValues
            }
        }]);