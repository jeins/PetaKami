'use strict';

angular.module('pkfrontendApp')
    .directive('pkMaps', [
        "$q", "$compile", "olHelpers", "olMapDefaults", "olData",
        function($q, $compile, olHelpers, olMapDefaults, olData) {
    return {
        restrict: 'EA',
        transclude: true,
        replace: true,
        scope: {
            center: '=olCenter',
            defaults: '=olDefaults',
            view: '=olView',
            events: '=olEvents',
            drawtype: '=olDrawType'
        },
        template: '<div class="angular-openlayers-map" ng-transclude></div>',
        controller: ["$scope", function($scope) {
            var _map = $q.defer();
            $scope.getMap = function() {
                return _map.promise;
            };

            $scope.setMap = function(map) {
                _map.resolve(map);
            };

            this.getOpenlayersScope = function() {
                return $scope;
            };
        }],
        link: function(scope, element, attrs) {
            var isDefined = olHelpers.isDefined;
            var createLayer = olHelpers.createLayer;
            var setMapEvents = olHelpers.setMapEvents;
            var setViewEvents = olHelpers.setViewEvents;
            var createView = olHelpers.createView;
            var createVectorLayerForDraw = olHelpers.createVectorLayerForDraw;
            var addDrawInteraction = olHelpers.addDrawInteraction;
            var addDrawModifyInteraction = olHelpers.addDrawModifyInteraction;
            var defaults = olMapDefaults.setDefaults(scope);

            // Set width and height if they are defined
            if (isDefined(attrs.width)) {
                if (isNaN(attrs.width)) {
                    element.css('width', attrs.width);
                } else {
                    element.css('width', attrs.width + 'px');
                }
            }

            if (isDefined(attrs.height)) {
                if (isNaN(attrs.height)) {
                    element.css('height', attrs.height);
                } else {
                    element.css('height', attrs.height + 'px');
                }
            }

            if (isDefined(attrs.lat)) {
                defaults.center.lat = parseFloat(attrs.lat);
            }

            if (isDefined(attrs.lon)) {
                defaults.center.lon = parseFloat(attrs.lon);
            }

            if (isDefined(attrs.zoom)) {
                defaults.center.zoom = parseFloat(attrs.zoom);
            }

            var controls = ol.control.defaults(defaults.controls);
            var interactions = ol.interaction.defaults(defaults.interactions);
            var view = createView(defaults.view);
            var features = new ol.Collection();
            var source = new ol.source.Vector({features: features});

            // Create the Openlayers Map Object with the options
            var map = new ol.Map({
                layers : [
                    new ol.layer.Tile({
                        source: new ol.source.OSM()
                    })
                ],
                renderer: defaults.renderer,
                target: element[0],
                view: view
            });

            // If no layer is defined, set the default tileLayer
            if (!attrs.customLayers) {
                var l = {
                    type: 'Tile',
                    source: {
                        type: 'OSM'
                    }
                };
                var layer = createVectorLayerForDraw(source);//createLayer(l, view.getProjection(), 'default');
                map.addLayer(layer);
                map.set('default', true);
            }

            if (!isDefined(attrs.olCenter)) {
                var c = ol.proj.transform([defaults.center.lon,
                        defaults.center.lat
                    ],
                    defaults.center.projection, view.getProjection()
                );
                view.setCenter(c);
                view.setZoom(defaults.center.zoom);
            }

            if(attrs.olDrawType != undefined){
                map.addInteraction(addDrawInteraction(source, attrs.olDrawType));
                map.addInteraction(addDrawModifyInteraction(features));
            }

            // Set the Default events for the map
            setMapEvents(defaults.events, map, scope);

            //Set the Default events for the map view
            setViewEvents(defaults.events, map, scope);

            source.on(['addfeature', 'changefeature'], function(evt){
                var feature = evt.feature;
                var coords = feature.getGeometry().getCoordinates();
                scope.$emit('pk.draw.coordinate', coords);
            });

            // Resolve the map object to the promises
            scope.setMap(map);
            olData.setMap(map, attrs.id);
        }
    };
}]);