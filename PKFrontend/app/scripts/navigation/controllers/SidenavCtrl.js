'use strict';

angular.module('pkfrontendApp')
    .controller('SidenavCtrl', ['$scope', '$location', function ($scope, $location) {
        var vm = this;
        vm.sideNavContent = 'home';

        vm.init = init;
        vm.toggleSideNav = toggleSideNav;
        vm.hideSideNav = hideSideNav;
        vm.isNavActive = isNavActive;

        init();

        function init(){
            hideSideNav();

            if($location.path().match("view")){
                toggleSideNav('browse');
            } else if($location.path().match("upload")){
                toggleSideNav('upload');
            }
        }

        function toggleSideNav(content){
            vm.sidenavClass = "pk-sidenav_show";
            vm.mapClass = "pk-map_canvas80";
            vm.sideNavContent = content;
        }

        function hideSideNav(){
            vm.sidenavClass = "pk-sidenav_hide";
            vm.mapClass = "pk-map_canvas100";
            vm.sideNavContent = 'home';
        }

        function isNavActive(content){
            if(vm.sideNavContent == content){
                return 'active';
            }
            return '';
        }
    }]);
