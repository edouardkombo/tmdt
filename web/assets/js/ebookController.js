tmdtApp.controller('ebookController', 
    ['$scope', '$http', '$timeout', 'changeSiteLanguage', 'ngTranslation', '$routeParams', '$rootScope', function (
                $scope, $http, $timeout, changeSiteLanguage, ngTranslation, $routeParams, $rootScope) {
            changeSiteLanguage.changeLang($scope, $routeParams, ngTranslation, $rootScope);
            $scope.htmlReady();
    }]
);
