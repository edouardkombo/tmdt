tmdtApp.controller('contactController', 
    ['$scope', '$http', '$timeout', 'changeSiteLanguage', '$routeParams', 'ngTranslation', '$rootScope', function (
                $scope, $http, $timeout, changeSiteLanguage, $routeParams, ngTranslation, $rootScope) { 
            
            changeSiteLanguage.changeLang($scope, $routeParams, ngTranslation, $rootScope);
            $scope.htmlReady();
    }]
);
