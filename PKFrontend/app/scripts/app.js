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
      .otherwise({
        redirectTo: '/'
      });
  });
