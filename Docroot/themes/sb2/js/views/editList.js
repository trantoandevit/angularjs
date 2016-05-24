sb2.controller('listCtrl', function ($scope, $http, $timeout) {
    $scope.fields = [{'sort': 1, 'dataType': 'string'}];

    $scope.addField = function () {
        $scope.fields.push({'sort': $scope.fields.length + 1, 'dataType': 'string'});
    };

    $scope.removeField = function (idx) {
        $scope.fields.splice(idx, 1);
        if ($scope.fields.length == 0)
            $scope.addField();
    };
});