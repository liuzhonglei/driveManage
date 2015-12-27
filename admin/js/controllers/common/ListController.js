MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        //init
        Metronic.initAjax();

        // init the table
        TableAjax.init('list',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController);
    });
}]);
