'use strict';

angular.module('pkfrontendApp')
    .factory('svcSession', service);

service.$inject = ['$rootScope', '$log', 'CONFIG', '$window'];
function service($rootScope, $log, CONFIG, $window){
    var session = {};

    function resetSession(){
        session.loggedIn = false;
        $rootScope.$emit('session:update', session);
        $log.info("Session reset");
    }

    function init(){
        if($window.localStorage[CONFIG.session.key]){
            setSession();
        } else{
            resetSession();
        }
    }

    function setSession(){
        session.loggedIn = true;
        $rootScope.$emit('session:update', session);
        $log.info("Session set");
    }

    function getSession(){
        return session;
    }

    init();

    return {
        getSession: getSession,
        resetSession: resetSession,
        setSession: setSession
    };
}