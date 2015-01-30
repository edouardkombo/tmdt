tmdtApp.controller('homeController', 
    ['$scope', '$timeout', 'ngTranslation', '$routeParams', 'changeSiteLanguage', '$rootScope', function (
                $scope, $timeout, ngTranslation, $routeParams, changeSiteLanguage, $rootScope) {
            
            changeSiteLanguage.changeLang($scope, $routeParams, ngTranslation, $rootScope);
            $scope.currentLanguage = $rootScope.language;
    }]
);
