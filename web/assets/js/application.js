'use strict';
var userLang            = navigator.language || navigator.userLanguage;
var availableLanguages  = ['fr', 'en'];
var paginationStep      = 10;
var phpUrl              = 'http://backend.themilliondollartalk.com';

var tmdtApp = angular.module('TheMillionDollarTalk', ['ng-translation','ngRoute','monospaced.elastic', 'seo'])
.config(['ngTranslationProvider', '$routeProvider','$locationProvider', '$httpProvider', function(ngTranslationProvider, $routeProvider, $locationProvider, $httpProvider) {
    ngTranslationProvider.
        setDirectory('assets/static').
        setFilesSuffix('.json').
        langsFiles({
            en: 'lang.en',
            fr: 'lang.fr'
        }).
        fallbackLanguage('en');

        $httpProvider.defaults.useXDomain = true;
        $httpProvider.defaults.withCredentials = true;
        delete $httpProvider.defaults.headers.common["X-Requested-With"];
        $httpProvider.defaults.headers.common["Accept"] = "application/json";
        $httpProvider.defaults.headers.common["Content-Type"] = "application/json";        
        $locationProvider.hashPrefix('!');

       
    $routeProvider.
        when('/:language/index', {
            templateUrl: '/partials/home.html',
            controller: 'homeController' 
        }).                
        when('/:language/aboutus', {
            templateUrl: '/partials/aboutus.html',
            controller: 'aboutController'
        }).                
        when('/:language/crowdwriting', {
            templateUrl: '/partials/crowdwriting.html',
            controller: 'writeController' 
        }).              
        when('/:language/ebooks', {
            templateUrl: '/partials/ebooks.html',
            controller: 'ebookController'
        }).
        when('/:language/contact', {
            templateUrl: '/partials/contact.html',
            controller: 'contactController'
        }).
        otherwise({
            redirectTo: '/'+userLang+'/index'
        });       
}])
.service('changeSiteLanguage', function () {
    return {
        changeLang: function($scope, $routeParams, ngTranslation, $rootScope) {
            var userLanguage        = $routeParams.language;
            $rootScope.language     = userLanguage;
            ngTranslation.use(userLanguage);
            
            var appElement = document.querySelector('[ng-app=TheMillionDollarTalk]');
            var appScope = angular.element(appElement).scope();
            var controllerScope = appScope.$$childHead;            
            controllerScope.currentLanguage = $rootScope.language;
        }
    };     
})
;

tmdtApp.run(function(ngTranslation, $location, $rootScope) {  
    var language        = $location.url().split('/');
    var trueLanguage    = '';

    if (availableLanguages.indexOf(language[1]) >= 0) {
        trueLanguage = language[1];
    } else {
        trueLanguage = "en";
    }
    
    $rootScope.language = trueLanguage;
    console.log($rootScope.language + ' => first');
    ngTranslation.use(trueLanguage); 
});