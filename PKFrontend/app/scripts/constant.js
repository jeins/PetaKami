'use strict';

angular
    .module('pkfrontendApp')
    .constant('CONFIG', {
        'http': {
            'rest_host': 'http://pkbackend.127.0.0.1.xip.io'//'http://api.petakami.com'
        },
        'session':{
            'key': 'satellizer_token'
        },
        'translation':{
            'path': '/translations/',
            'suffix': '.json',
            'sanitize': 'escaped',
            'default': 'dev'
        }
    });