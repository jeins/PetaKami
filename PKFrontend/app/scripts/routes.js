'use strict';

angular
    .module('pkfrontendApp')
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
            .state('browse', {
                url:'/browse',
                templateUrl: 'views/features/browse.html',
                controller: 'BrowseCtrl as vm'
            })
            .state('edit', {
                url: '/edit/:layer',
                templateUrl: 'views/map/map.html',
                controller: 'EditLayerCtrl as vm'
            })
            .state('upload', {
                url: '/upload',
                templateUrl: 'views/features/upload.html',
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
    }]);