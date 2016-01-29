'use strict';

angular.module('pkfrontendApp')
  .controller('MapCtrl', ['$scope', 'olData', function ($scope, olData) {
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
          mousepositiones: '',
          projection: 'EPSG:4326'
      });

      $scope.$on('pk.draw.coordinate', function(event, data) { console.log(data); });

      $scope.$on('openlayers.map.pointermove', function(event, data){
         $scope.$apply(function(){
             if($scope.projection == data.projection){
                 $scope.mouseposition = data.coord;
             } else{
                 var p = ol.proj.transform([data.coord[0], data.coord[1]], data.projection, $scope.projection);

                 $scope.mouseposition = 'lat:' + p[1] + ', lon:' + p[0];
             }
         })
      });

  }]);
