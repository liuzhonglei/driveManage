'use strict';


/* Setup general page controller */
MetronicApp.controller('EditController', ['$rootScope', '$http', '$scope', 'dataTool', function ($rootScope, $http, $scope, dataTool) {
    $scope.$on('$viewContentLoaded', function () {
        Metronic.initAjax();
    });

    // init the param
    $scope.errorShow = false;
    $scope.successShow = false;


    $scope.metadata_sex = [{
        value: 'M',
        text: '男'
    }, {
        value: 'W',
        text: '女'
    }];
    //
    // set the submit method
    $scope.infoEdit = function () {
        dataTool.edit("student",$scope.info)
    };
}]);