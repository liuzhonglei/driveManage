MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;
    $scope.status = "WAIT";

    /**
     * 设置状态
     * @type {number}
     */
    $scope.setStatus = function (status) {
        $scope.status = status;
        $('#list-search-text').val('')
        $scope.loadTable();
    }

    /**
     * 重新加载表哥
     */
    $scope.loadTable = function () {
        $('#condition-modal').modal('hide');
        TableAjax.emptyModal($rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, {result_code: $scope.status});
        TableAjax.init('list', $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, {result_code: $scope.status});
    }

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        //init
        Metronic.initAjax();

        // init the table
        setTimeout(function () {
            // 等待DOM回来，黑科技，哈哈哈
            TableAjax.init('list', $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, {result_code: $scope.status}, null);

        }, 1);

    });
}]);
