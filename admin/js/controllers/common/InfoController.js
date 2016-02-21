/* Setup general page controller */
MetronicApp.controller('InfoController', ['$rootScope', '$http', '$scope', 'infoTool', function ($rootScope, $http, $scope, infoTool) {
    /**
     * 信息
     */
    $scope.info = {id:null};
    $scope.idName = "form-info-id";
    $scope.formName = "form-info";
    $scope.extendClass = $rootScope.$state.$current.data.extendClass;


    /**
     * 更新信息
     */
    $scope.$watch('info["id"]', function () {
        Metronic.startPageLoading({
            message: '读取中'
        });
        // 查询数据
        infoTool.getInfoModel($rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController).then(function (data) {
            if ($scope.info.id == null) {
                Metronic.stopPageLoading();
                return;
            } else if (!$scope.metadata) {
                $.extend($scope, data);
            }
            if ($scope.info["id"] != null && $scope.info["id"].length > 0) {
                infoTool.getInfoData($scope, $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, $scope.info["id"]).then(function (data) {
                    $scope.info = data;
                });
            } else {
                $.extend($scope.info, $scope.defaultInfo);
            }
            Metronic.stopPageLoading();

        });
    });

    /**
     *  set the save method
     */
    $scope.save = function (modalName, tableName) {
        tableName = tableName || "list";

        infoTool.saveInfoData($scope.info, $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController).then(function (response) {
            if (response.data.result == "1") {
                $scope.infoErrorShow = false;
                $scope.infoErrorMsg = "";
                $("div[name='form-info']").modal("hide");

                TableAjax.reload(tableName);
            } else {
                $scope.infoErrorShow = true;
                $scope.infoErrorMsg = response.msg;
            }
            $scope.info["id"] = null;
        });
    }
}]);
