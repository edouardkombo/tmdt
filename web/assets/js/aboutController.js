tmdtApp.controller('aboutController', 
    ['$scope', '$http', '$timeout', 'ngTranslation', '$routeParams', 'changeSiteLanguage', '$rootScope', function (
                $scope, $http, $timeout, ngTranslation, $routeParams, changeSiteLanguage, $rootScope) {
            
            changeSiteLanguage.changeLang($scope, $routeParams, ngTranslation, $rootScope);
            $scope.htmlReady();
    }]
);
