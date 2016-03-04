'use strict';

angular
  .module('pkfrontendApp', [
      'ngAnimate', 'ui.router', 'ui.bootstrap',
      'ngSanitize', 'ngFileUpload', 'satellizer'
  ])
    .constant('CONFIG', {
        'http': {
            'rest_host': 'http://pkbackend.127.0.0.1.xip.io'
        },
        'session':{
            'key': 'satellizer_token'
        }
    })
    .config(['$logProvider', '$provide', function($logProvider, $provide){
        // Setup Logging/Debug
        $logProvider.debugEnabled(false);

        $provide.decorator('$log', function ($delegate) {
            var origInfo = $delegate.info, origLog = $delegate.log,
                origError = $delegate.error, origWarn = $delegate.warn;

            $delegate.info = function () {if ($logProvider.debugEnabled()) origInfo.apply(null, arguments)};
            $delegate.log = function () {if ($logProvider.debugEnabled()) origLog.apply(null, arguments)};
            $delegate.error = function () {if ($logProvider.debugEnabled()) origError.apply(null, arguments)};
            $delegate.warn = function () {if ($logProvider.debugEnabled()) origWarn.apply(null, arguments)};

            return $delegate;
        });
    }])
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
                    controller: 'UploadCtrl as vm'
                })
                .state('register', {
                    url: '/register',
                    templateUrl: 'views/auth/register.html',
                    controller: 'AuthCtrl as vm'
                })
                .state('login', {
                    url: '/login',
                    templateUrl: 'views/auth/login.html',
                    controller: 'AuthCtrl as vm'
                })
            ;
            //$locationProvider.html5Mode(true);
      }])
  .config(['$authProvider', 'CONFIG', 
    function($authProvider, CONFIG){
      $authProvider.signupUrl = CONFIG.http.rest_host + '/user/register';
      $authProvider.loginUrl = CONFIG.http.rest_host + '/user/authenticate';
    }])

;
