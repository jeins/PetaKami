'use strict';

angular.module('pkfrontendApp')
    .factory('svcSharedProperties', svcSharedProperties);

svcSharedProperties.$inject = ['$timeout'];

function svcSharedProperties($timeout){
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

    function sendBroadcast(doneCallback){
        $timeout(function(){
            doneCallback("ok");
        }, 10);
    }

    return {
        setSelectedNav:setSelectedNav,
        getSelectedNav:getSelectedNav,
        setLayerValues: setLayerValues,
        getLayerValues: getLayerValues,
        sendBroadcast: sendBroadcast
    }
}