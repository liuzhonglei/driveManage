/* Setup general page controller */
MetronicApp.controller('InfoController', ['$rootScope', '$http', '$scope', 'infoTool', function ($rootScope, $http, $scope, infoTool) {
    // 信息
    $scope.info = {};
    $scope.idName = "form-info-id";
    $scope.formName = "form-info";

    /**
     * 设置对象信息
     */

    infoTool.getInfoModel($rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController).then(function (data) {
        $.extend($scope, data);
        if($rootScope.$state.$current.data.extendClass){
                    $("div[name='form-info']").children(".modal-dialog").addClass($rootScope.$state.$current.data.extendClass);
        }
    });


    /**
     * 更新信息
     */
    $scope.$watch('info["id"]', function () {
       

        // 查询数据
        if ($scope.info["id"] != null && $scope.info["id"].length > 0) {
            infoTool.getInfoData($scope, $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, $scope.info["id"]).then(function (data) {
                $scope.info = data;
            });
        } else {
            $.extend($scope.info, $scope.defaultInfo);
        }
          // todo
//         $(".fileinput").fileinput('clear');
//         var navName = ;
       
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
