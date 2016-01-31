'use strict';

/**
 * @ngdoc overview
 * @name pkfrontendApp
 * @description
 * # pkfrontendApp
 *
 * Main module of the application.
 */
angular
  .module('pkfrontendApp', [
      'ngAnimate',
      'ngRoute',
      'ui.bootstrap',
      'ngSanitize'
  ])
    .constant('CONFIG', {
        'http': {
            'rest_host': 'http://localhost/vhosts/PetaKami/PKBackend/v1/gs',
            'redirectUri': 'http://localhost:9000/'
        }
    })
    .config(function ($routeProvider) {
        $routeProvider
            .when('/', {
                controller: 'MapCtrl',
                controllerAs: 'CMP',
                templateUrl: 'views/map/map.html'
            })
            .when('/view/:layer', {
                controller: 'ViewLayerCtrl',
                controllerAs: 'CVL',
                templateUrl: 'views/map/map.html'
            })
            .otherwise({
                redirectTo: '/'
            });
  });
