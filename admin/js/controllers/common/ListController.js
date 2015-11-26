/* Setup general page controller */
MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        //init
        Metronic.initAjax();

        // init the table
        TableAjax.init('list',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController);

        // init the field
        //$http({
        //    method: "post",
        //    headers: function ($httpProvider) {
        //        $httpProvider.defaults.withCredentials = true;
        //    },
        //    url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/getModelInfo"
        //}).success(function (data) {
        //    $scope.searchName = data.model.search_key;
        //});
    });
}]);
