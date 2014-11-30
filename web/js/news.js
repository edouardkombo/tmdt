
storeApp.controller('newsController', 
    ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
        updateDatas($scope, $http);
        
        var countUp;
        countUp = function() {
            updateDatas($scope, $http);
            $timeout(countUp, 10000);
        };
        $timeout(countUp, 10000);        
    }]
);

function updateDatas($scope, $http) {
    $http.get("update.php")
    .success(function(data){
        
        for (var key in data) {
            for (var k in data[key]) {
                if (k === 'picture') {
                    if (data[key][k].match(/\.(jpeg|jpg|gif|png)$/) === null) {
                        data[key][k] = "";
                    }
                }
                //console.log(data[key][k]);
            }
        }
        $scope.data = data;
    })
    .error(function() {
        $scope.data = "error in fetching data";
    });    
}


