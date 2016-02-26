'use strict';

angular.module('pkfrontendApp')
    .controller('EditLayerCtrl', controller);

controller.$inject = ['$scope', '$stateParams', 'svcSharedProperties', 'svcLayer', 'olData', 'svcSecurity', '$http'];

function controller($scope, $stateParams, svcSharedProperties, svcLayer, olData, svcSecurity, $http){
    var vm = this;
    vm.init = init;
    vm.selectedDrawType = selectedDrawType;
    vm.setDrawOrModify = setDrawOrModify;

    init();

    function init(){
        vm.drawType = '';
        vm.drawValue = [];
        vm.isDrawOrModify = 'draw';
        vm.geoproperties = '';
        var point = [], line=[], poly=[];
        if($stateParams.layer != undefined){
            var request = getRequestProperties(svcSecurity.decode($stateParams.layer));
        }

        setupMap();

        $scope.geoproperties = {
            'workspace': request.workspace,
            'layerGroup': request.layerGroup,
            'layers': request.layers
        };

        $scope.$on('pk.draw.feature', function(event, data) {
            var feature = data;
            switch(feature.getGeometry().getType()){
                case 'Point':
                    var pointCoor = new ol.geom.Point(feature.getGeometry().getCoordinates()).transform("EPSG:3857", "EPSG:4326");
                    if(point.id != feature.getProperties().id)
                        point[feature.getProperties().id] = pointCoor.getCoordinates();
                    else point.id = data;
                    break;
                case 'LineString':
                    var lineCoor = new ol.geom.LineString(feature.getGeometry().getCoordinates()).transform("EPSG:3857", "EPSG:4326");
                    if(line.id != feature.getProperties().id)
                        line[feature.getProperties().id] = lineCoor.getCoordinates();
                    else line.id = data;
                    break;
                case 'Polygon':
                    var polyCoor = new ol.geom.Polygon(feature.getGeometry().getCoordinates()).transform("EPSG:3857", "EPSG:4326");
                    if(poly.id != feature.getProperties().id)
                        poly[feature.getProperties().id] = polyCoor.getCoordinates();
                    else poly.id = data;
                    break;
            }

            svcSharedProperties.setLayerValues({'point':point, 'linestring':line, 'polygon':poly});
        });
    }

    function setupMap()
    {
        angular.extend($scope, {
            defaults: {
                events: {
                    map: ['pointermove']
                }
            },
            indonesia: {
                lat: 0,
                lon: 0,
                zoom: 2
            },
            mouseposition: '',
            layer: {},
            projection: 'EPSG:4326'
        });
    }

    function getRequestProperties(params){console.log(params)
        var request = {};
        request.workspace = params.split(':')[0];
        request.layerGroup = params.split(':')[1];
        var layerAndDrawType = params.split(':')[2].split(';');
        request.layers = '';
        for(var i=0; i<layerAndDrawType.length; i++){
            var layer = layerAndDrawType[i].split('?')[0];
            if(layer != ""){
                request.layers += layer + ',';
            }
        }
        return request;
    }

    function setDrawOrModify(value){
        vm.isDrawOrModify = value;
    }

    function selectedDrawType(value){
        vm.drawType = value;
    }
}