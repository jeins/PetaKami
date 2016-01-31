'use strict';

angular.module('pkfrontendApp')
    .factory('svcSharedProperties', [
        '$q', function($q) {
            var layerValues;
            var selectedNav;

            function setLayerValues(values){
                layerValues = values;
            }

            function getLayerValues(){
                return layerValues;
            }

            function setSelectedNav(nav){
                selectedNav = nav;
            }

            function getSelectedNav(){
                return selectedNav;
            }

            return {
                setSelectedNav:setSelectedNav,
                getSelectedNav:getSelectedNav,
                setLayerValues: setLayerValues,
                getLayerValues: getLayerValues
            }
        }]);