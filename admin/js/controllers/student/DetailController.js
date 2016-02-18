/* Setup general page controller */
MetronicApp.controller('StudentDetailController', ['$rootScope', '$http', '$scope', 'infoTool', function ($rootScope, $http, $scope, infoTool) {
    // param
    $scope.metadata = {};
    $scope.selected = {};
    $scope.info = {id: null};
    $scope.infoDisabled = "true";

    /**
     * 加载信息
     */
    setTimeout(function () {
        infoTool.getInfoModel("Student", "Student").then(function (data) {
            $.extend($scope, data);
            if ($rootScope.$state.$current.data.extendClass) {
                $("div[name='form-info']").children(".modal-dialog").addClass($rootScope.$state.$current.data.extendClass);
            }
        });
    }, 500);

    /**
     * 更新信息
     */
    $scope.$watch('info["id"]', function () {
        // 查询数据
        if ($scope.info["id"] != null && $scope.info["id"].length > 0) {
            infoTool.getInfoData($scope, "Student", "Student", $scope.info["id"]).then(function (data) {
                $scope.info = data;
            });
        } else {
            $.extend($scope.info, $scope.defaultInfo);
        }
    });
}]);

/**
 * 显示微信绑定界面
 * @param id 对象id
 */
function studentDetailShow(id) {
    // 点击标签1
    var nav = $("a[name='student-detail-nav-1']");
    nav.click();

    // 修改数据
    $("#student_id").val(id);
    $("#student_id").trigger("change");

    // 刷新数据
    TableAjax.init('student-log', 'Student', 'Detail', {"info": "log", "student_id": id});
    TableAjax.init('student-money-log', 'Student', 'Detail', {"info": "moneyLog", "student_id": id});
    $("#student-detail").modal("show");
}