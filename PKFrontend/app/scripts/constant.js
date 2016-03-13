'use strict';

angular
    .module('pkfrontendApp')
    .constant('CONFIG', {
        'http': {
            'rest_host': 'http://api.petakami.com'
        },
        'session':{
            'key': 'satellizer_token',
            'user': 'user',
            'guest': 'guest'
        },
        'translation':{
            'path': '/translations/',
            'suffix': '.json',
            'sanitize': 'escaped',
            'default': 'dev'
        }
    });