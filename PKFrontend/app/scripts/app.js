'use strict';

angular
  .module('pkfrontendApp', [
      'ngAnimate',
      'ui.router',
      'ui.bootstrap',
      'ngSanitize',
      'ngFileUpload'
  ])
    .constant('CONFIG', {
        'http': {
            'rest_host': 'http://pkbackend.127.0.0.1.xip.io'
        }
    })
    .config(['$urlRouterProvider', '$stateProvider', '$locationProvider',
        function ($urlRouterProvider, $stateProvider, $locationProvider) {
            $urlRouterProvider.otherwise('/');
            $stateProvider
                .state('home', {
                    url: '/',
                    templateUrl: 'views/map/map.html',
                    controller: 'MapCtrl as vm'
                })
                .state('view', {
                    url: '/view/:layer',
                    templateUrl: 'views/map/map.html',
                    controller: 'ViewLayerCtrl as CVL'
                })
                .state('edit', {
                    url: '/edit/:layer',
                    templateUrl: 'views/map/map.html',
                    controller: 'EditLayerCtrl as vm'
                })
                .state('upload', {
                    url: '/upload',
                    templateUrl: 'views/navigation/upload.html',
                    controller: 'UploadCtrl as CU'
                })
            ;
            //$locationProvider.html5Mode(true);
      }]);
