
storeApp.controller('newsController', 
    ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
        //Focus on element
        document.getElementById("mdm").focus();
        updateDatas($scope, $http);
        
        var countUp;
        countUp = function() {
            updateDatas($scope, $http);
            $timeout(countUp, 10000);
        };
        $timeout(countUp, 10000);
        
        // create a blank object to hold our form information
        // $scope will allow this to pass between controller and view
        $scope.formData     = {action: 'insert', resultStatus: false};
        
        // process the form
        $scope.processForm = function() {
            $scope.formData.thisForm = true;
            console.log($scope.formData.mdm);
            if (!$scope.formData.mdm) {
                $scope.formData.resultStatus    = false;                    
                $scope.result                   = "You must write a content in the above field first!";
                document.getElementById("mdm").focus();
            } else {
                $http({
                    method  : 'POST',
                    url     : 'process.php',
                    data    : $.param($scope.formData),
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
                })
                .success(function(data) {
                    if (!data.success) {
                        $scope.formData.resultStatus    = false;                    
                        $scope.result                   = data.error.message;
                    } else {
                        $scope.formData.mdm             = null;
                        $scope.formData.resultStatus    = true;                    
                        $scope.result                   = data.success.message; 
                    }

                }).error(function(data) {
                    console.log(data);
                });
            }
        };        
    }]
);



function updateDatas($scope, $http) {
    var datas = {action: 'update'};
    $http({
        method  : 'POST',
        url     : 'process.php',
        data    : $.param(datas),      
        headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
    })
    .success(function(data){
        if (!data.success) {
            $scope.data = data.success.datas;
        } else {
            $scope.data = data.success.datas;
        }
        $scope.result = null;
        $scope.formData.thisForm = false;
    })
    .error(function(data) {
        $scope.data = "error in fetching datas";
        $scope.result = null;
        $scope.formData.thisForm = false;
    });  
        
}


