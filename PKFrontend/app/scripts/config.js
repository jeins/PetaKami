'use strict';

angular
    .module('pkfrontendApp')
    .config(['$logProvider', '$provide', '$authProvider', 'CONFIG', '$translateProvider'
        , function($logProvider, $provide, $authProvider, CONFIG, $translateProvider){
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

            // Setup Authprovider
            $authProvider.signupUrl = CONFIG.http.rest_host + '/user/register';
            $authProvider.loginUrl = CONFIG.http.rest_host + '/user/authenticate';

            // Setup Translation
            $translateProvider
                .useStaticFilesLoader({
                    prefix: CONFIG.translation.path,
                    suffix: CONFIG.translation.suffix
                })
                .useSanitizeValueStrategy(CONFIG.translation.sanitize)
                .preferredLanguage(CONFIG.translation.default)
            ;
    }]);