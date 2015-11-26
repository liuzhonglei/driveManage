/* Setup general page controller */
MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;

    /**
     *  显示微信绑定
     */
    $scope.showBind = function () {
        for (var name in $scope.info) {
            if ($scope.metadata[name] && $scope.selected[name]) {
                $scope.info[name] = $scope.selected[name].value;
            }
        }

        var params = JSON.stringify($scope.info).replace("{", "").replace("}", "").replace(/\,/g, "&").replace(/\"/g, "").replace(/\:/g, "=");
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/saveAdmin",
            data: params,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function (data) {
            if (data.status == "1") {
                $scope.infoErrorShow = false;
                $scope.infoErrorMsg = "";
                TableAjax.reload();
                $("#form_info").modal("hide");
            } else {
                $scope.infoErrorShow = true;
                $scope.infoErrorMsg = data.msg;
            }
        });
    };

    /**
     * 绑定微信
     */
    $scope.bindWeixin = function(){

    }
}]);
