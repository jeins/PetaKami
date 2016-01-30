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
  .config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            controller: 'MapCtrl',
            controllerAs: 'CMP',
            templateUrl: 'views/map/map.html'
        })
        .otherwise({
            redirectTo: '/'
        });
  });
