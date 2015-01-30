tmdtApp.directive('scroller', function () {
    return {
        restrict: 'A',
        scope: {
            loadingMethod: "&"
        },
        link: function (scope, elem, attrs) {
            var rawElement = elem[0];
            if (rawElement) {
                window.addEventListener("scroll", function() {
                    var customScrollTop = (document.documentElement.scrollTop||document.body.scrollTop);
                    if (document.body.scrollHeight === 
                            (customScrollTop + window.innerHeight)) 
                    {
                        console.log('yiii');
                        scope.$apply(scope.loadingMethod);
                    }                
                });
            }
        }
    };
});

tmdtApp.controller('writeController', 
    ['$scope', '$http', 'ngTranslation', '$timeout', '$routeParams', 'changeSiteLanguage', '$rootScope', function ($scope, $http, ngTranslation, $timeout, $routeParams, changeSiteLanguage, $rootScope) {
        
        changeSiteLanguage.changeLang($scope, $routeParams, ngTranslation, $rootScope);
        
        //Focus on element
        document.getElementById("mdm").focus();
        
        $scope.formData     = {action: 'insert', resultStatus: false};
        
        $scope.items        = [];
        $scope.counter      = 0;
        $scope.loaded       = true;
        $scope.loadMore     = function(){
            $scope.loaded = false;
            getContributions($scope, $http, lasts = false);
            $scope.counter += paginationStep;
        };
        if ($scope.loaded === true) {
            $scope.loadMore();
        }
        
        //Get new messages every 3 seconds
        var getNewEntries = function() {
            getContributions($scope, $http, lasts = 3);
            $timeout(getNewEntries, 3000); 
        }.bind(this);
        $timeout(getNewEntries, 3000);        
         
        $scope.formData     = {action: 'insert', resultStatus: null, lang: userLang};
        $scope.processForm  = function() {
            $scope.formData.thisForm = true;

            if (!$scope.formData.mdm) {
                $scope.formData.resultStatus    = false;                    
                $scope.result                   = ngTranslation.get(userLang).crowdwriting.emptyError;
                document.getElementById("mdm").focus();
            } else {
                $http({
                    method  : 'POST',
                    url     : phpUrl,
                    data    : $.param($scope.formData),
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                .success(function(data) {
            
                    if (!data.success) {
                        $scope.formData.resultStatus    = false;                    
                        $scope.result                   = ngTranslation.get(userLang).crowdwriting.error;
                    } else {                    
                        $scope.formData.mdm             = null;
                        $scope.formData.resultStatus    = true;                    
                        $scope.result                   = ngTranslation.get(userLang).crowdwriting.success; 
                    }
                    
                    var resetValues = function() {
                        $scope.formData.resultStatus    = null;                      
                        $scope.result                   = '';
                        $scope.formData.thisForm        = false; 
                        if (data.success) {
                            $scope.formData.mdm         = null;
                        }
                    }.bind(this);
                    $timeout(resetValues, 3000);                                       

                }).error(function(data) {
                    $scope.formData.resultStatus    = false;                    
                    $scope.result                   = ngTranslation.get(userLang).crowdwriting.serverError;
                });
            }
        };
        
        $scope.htmlReady();
    }]
);

function getContributions($scope, $http, lastsValue) {
    var datas = {action: 'update', range: paginationStep, limit: $scope.counter, lang: userLang, lasts:lastsValue};
    $http({
        method  : 'POST',
        url     : phpUrl,
        data    : $.param(datas),      
        headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
    })
    .success(function(data){
        var result = data.success.datas;

        if (lastsValue !== false) {
            
            if (result[0] !== undefined) {
                for (var key in loop = result){
                    $scope.items.unshift(loop[key]);
                }               
            }
        } else {
            for (var key in loop = result){
                $scope.items.push(loop[key]);
            }
        }
        
        $scope.loaded = true;
        $scope.result = null;
        $scope.formData.thisForm = false;
    })
    .error(function(data) {
        $scope.loaded = false;
        $scope.items = "error in fetching datas";
        $scope.result = null;
        $scope.formData.thisForm = false;
    });      
}
