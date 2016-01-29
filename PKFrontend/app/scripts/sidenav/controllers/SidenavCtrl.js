'use strict';

angular.module('pkfrontendApp')
    .controller('SidenavCtrl', ['$scope', function ($scope) {
        var vm = this;

        vm.sideNavContent = 'home';

        vm.toggleSideNav = toggleSideNav;
        vm.hideSideNav = hideSideNav;
        vm.isNavActive = isNavActive;

        vm.hideSideNav();

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
