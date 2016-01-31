'use strict';

angular.module('pkfrontendApp')
  .controller('MapCtrl', ['$scope', 'svcSharedProperties', function ($scope, svcSharedProperties) {
      var vm = this;
      vm.drawType = '';
      vm.drawValue = [];
      var point = [], line=[], poly=[];

      vm.selectedDrawType = selectedDrawType;

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
          mouseposition: '',
          projection: 'EPSG:4326'
      });

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

          svcSharedProperties.setLayerValues({'point':point, 'line':line, 'poly':poly});
      });

      $scope.$on('openlayers.map.pointermove', function(event, data){
         $scope.$apply(function(){
             if($scope.projection == data.projection){
                 vm.mouseposition = data.coord;
             } else{
                 var p = ol.proj.transform([data.coord[0], data.coord[1]], data.projection, $scope.projection);

                 vm.mouseposition = 'lat:' + p[1] + ', lon:' + p[0];
             }
         })
      });

      function selectedDrawType(value){
          vm.drawType = value;
      }
  }]);
