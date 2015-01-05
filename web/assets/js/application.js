'use strict';
var userLang        = navigator.language || navigator.userLanguage;
var paginationStep  = 10;
var phpUrl          = 'http://backend.themilliondollartalk.com/';

var tmdtApp = angular.module('TheMillionDollarTalk', ['ng-translation','ngRoute','monospaced.elastic'])
.config(['ngTranslationProvider', function(ngTranslationProvider) {
    ngTranslationProvider.
        setDirectory('assets/static').
        setFilesSuffix('.json').
        langsFiles({
            en: 'lang.en',
            fr: 'lang.fr'
        }).
        fallbackLanguage(userLang);
}])
.config(['$locationProvider', function($locationProvider) {
        $locationProvider.hashPrefix('!');
    }
])
.config(['$routeProvider', function($routeProvider) {                  
    $routeProvider.
        when('/index', {
            templateUrl: '/partials/home.html',
            controller: 'homeController' 
        }).
        when('/aboutus', {
            templateUrl: '/partials/aboutus.html',
            controller: 'aboutController'
        }).                
        when('/crowdwriting', {
            templateUrl: '/partials/crowdwriting.html',
            controller: 'writeController' 
        }).              
        when('/ebooks', {
            templateUrl: '/partials/ebooks.html',
            controller: 'ebookController'
        }).
        when('/contact', {
            templateUrl: '/partials/contact.html',
            controller: 'contactController'
        }).
        otherwise({
            redirectTo: '/index'
        });
}]);

tmdtApp.run(function(ngTranslation, $location) {
    ngTranslation.use(userLang);
});