tmdtApp.controller('languageController', 
    ['$scope', '$timeout', 'ngTranslation', function ($scope, $timeout, ngTranslation) {
            $scope.currentLanguage = userLang;
            
            $scope.changeLanguage = function(language) {
                ngTranslation.use(language);
                userLang = language;
                $scope.currentLanguage = language;
            };
    }]
);
