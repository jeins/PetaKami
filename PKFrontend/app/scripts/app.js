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
            'rest_host': 'http://localhost/vhosts/PetaKami/PKBackend/v1/gs',
            'redirectUri': 'http://localhost:9000/'
        }
    })
    .config(['$urlRouterProvider', '$stateProvider', '$locationProvider',
        function ($urlRouterProvider, $stateProvider, $locationProvider) {
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
                })
                .state('upload', {
                    url: '/upload',
                    templateUrl: 'views/navigation/upload.html',
                    controller: 'UploadCtrl as UC'
                })
            ;
            //$locationProvider.html5Mode(true);
      }]);
