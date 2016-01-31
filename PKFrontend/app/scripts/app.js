'use strict';

angular
  .module('pkfrontendApp', [
      'ngAnimate',
      'ui.router',
      'ui.bootstrap',
      'ngSanitize'
  ])
    .constant('CONFIG', {
        'http': {
            'rest_host': 'http://localhost/vhosts/PetaKami/PKBackend/v1/gs',
            'redirectUri': 'http://localhost:9000/'
        }
    })
    //.config(['$locationProvider', function ($locationProvider) {
    //    $locationProvider.html5Mode(true);
    //}])
    .config(['$urlRouterProvider', '$stateProvider',
        function ($urlRouterProvider, $stateProvider) {
            $urlRouterProvider.otherwise('/');
            $stateProvider
                .state('home', {
                    url: '/',
                    templateUrl: 'views/map/map.html',
                    controller: 'MapCtrl as CMP'
                })
                .state('view', {
                    url: '/view/:layer',
                    templateUrl: 'views/map/map.html',
                    controller: 'ViewLayerCtrl as CVL'
                });
      }]);
