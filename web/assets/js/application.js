'use strict';

var tmdtApp = angular.module('TheMillionDollarTalk', ['pascalprecht.translate']).
    config(['$routeProvider', function($routeProvider) {
    $routeProvider.
        when('/', {
            templateUrl: 'partials/home.html',
            controller: 'homeController' 
        }).
        when('/aboutus', {
            templateUrl: 'partials/aboutus.html',
            controller: 'aboutController'
        }).                
        when('/crowdwriting', {
            templateUrl: 'partials/crowdwriting.html',
            controller: 'writeController' 
        }).              
        when('/ebooks', {
            templateUrl: 'partials/ebooks.html',
            controller: 'ebookController'
        }).
        when('/contact', {
            templateUrl: 'partials/contact.html',
            controller: 'contactController'
        }).
        otherwise({
            redirectTo: '/'
        });
    }]);