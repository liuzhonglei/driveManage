MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', 'dataTool', function ($rootScope, $http, $scope, dataTool) {
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


    /**
     * 打印信息
     */
    $scope.print = function () {
        // 读取标签
        param = {id: TableAjax.tableMap['list'].grid.getSelectedRows()};

        dataTool.getPostResponse("/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/payPrint", param, function (responese) {
            if (responese.result == 1) {
                browerPrint('pay-print-area', responese.data);
            }
        });
    }

    function browerPrint(divName, content) {
        var jqueryName = "div#" + divName;
        $(jqueryName).html(content)
        $(jqueryName).print({
            addGlobalStyles: true,
            stylesheet: null,
            rejectWindow: true,
            noPrintSelector: ".no-print",
            iframe: true,
            append: null,
            prepend: null
        });

        $(jqueryName).html("");
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
}])
;
