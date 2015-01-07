tmdtApp.controller('languageController', 
    ['$scope', '$timeout', 'ngTranslation', '$routeParams', '$rootScope', '$location', function (
                $scope, $timeout, ngTranslation, $routeParams, $rootScope, $location) {

            $scope.currentLanguage  = $rootScope.language;

            $scope.changeLanguage = function (language) {
                $scope.currentLanguage  = language;
                $location.path( "/"+language+"/index" );
            };
    }]
);
