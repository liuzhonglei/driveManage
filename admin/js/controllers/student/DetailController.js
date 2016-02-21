/* Setup general page controller */
MetronicApp.controller('StudentDetailController', ['$rootScope', '$http', '$scope', 'infoTool', function ($rootScope, $http, $scope, infoTool) {
    // param
    $scope.selected = {};
    $scope.info = {id: null};
    $scope.infoDisabled = "true";


    /**
     * 更新信息
     */
    $scope.$watch('info["id"]', function () {
        infoTool.getInfoModel("Student", "Student").then(function (data) {
            // 扩展源数据
            if ($scope.info.id == null) {
                return;
            } else if (!$scope.metadata) {
                $.extend($scope, data);
            }

            // 取得信息
            if ($rootScope.$state.$current.data.extendClass) {
                $("div[name='form-info']").children(".modal-dialog").addClass($rootScope.$state.$current.data.extendClass);
            }
            if ($scope.info["id"] != null && $scope.info["id"].length > 0) {
                infoTool.getInfoData($scope, "Student", "Student", $scope.info["id"]).then(function (data) {
                    $scope.info = data;
                });
            } else {
                $.extend($scope.info, $scope.defaultInfo);
            }
        });

    });
}]);

/**
 * 显示微信绑定界面
 * @param id 对象id
 */
function studentDetailShow(id) {
    // 显示
     Metronic.startPageLoading({
            message: '读取中'
        });

    // 点击标签1
    var nav = $("a[name='student-detail-nav-1']");
    nav.click();

    // 修改数据
    $("#student_id").val(id);
    $("#student_id").trigger("change");

    // 刷新数据
    TableAjax.init('student-log', 'Student', 'Detail', {"info": "log", "student_id": id});
    TableAjax.init('student-money-log', 'Student', 'Detail', {"info": "moneyLog", "student_id": id, "operation": "0"});

    $("#student-detail").modal("show");
    Metronic.stopPageLoading();

}