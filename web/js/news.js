
storeApp.controller('newsController', 
    ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
        updateDatas($scope, $http);
        
        var countUp;
        countUp = function() {
            updateDatas($scope, $http);
            $timeout(countUp, 10000);
        };
        $timeout(countUp, 10000);
        
        // create a blank object to hold our form information
        // $scope will allow this to pass between controller and view
        $scope.formData = {};

        // process the form
        $scope.processForm = function() {
            $http({
                method  : 'POST',
                url     : 'process.php',
                data    : $.param($scope.formData),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
            .success(function(data) {
                var result = eval('('+data+')');
                console.log(result);
                if (!result.success) {
                    $scope.errorName = result.errors.name;
                } else {
                    $scope.message = result.message;
                    $scope.formData.mdm = null;
                }
            });
        };        
    }]
);

function updateDatas($scope, $http) {
    $http.get("update.php")
    .success(function(data){
        $scope.data = data;
    })
    .error(function() {
        $scope.data = "error in fetching data";
    });    
}


