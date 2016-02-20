/**
 * Created by wuhanchu on 16/2/5.
 */
MetronicApp.controller('PayInfoController', ['$rootScope', '$http', '$scope', 'infoTool', function ($rootScope, $http, $scope, infoTool) {
    // 信息
    $scope.info = {};
    $scope.idName = "pay-form-info-id";
    $scope.formName = "pay-form-info";

    $scope.module = "EO2OPayment";
    $scope.controller = "EO2OPayment";
    $scope.title = "划款流水";
    $scope.listName = "pay-list";
    $scope.ListExtendClass = "modal-full";


    /**
     * 设置对象信息
     */
    /**
     * 加载信息
     */
    setTimeout(function () {
        infoTool.getInfoModel($scope.module, $scope.controller).then(function (data) {
            $.extend($scope, data);
        });
    }, 50);


    /**
     * 更新信息
     */
    $scope.$watch('info["id"]', function () {
        $(".fileinput").fileinput('clear');

        // 查询数据
        if ($scope.info["id"] != null && $scope.info["id"].length > 0) {
            infoTool.getInfoData($scope, $scope.module, $scope.controller, $scope.info["id"]).then(function (data) {
                $scope.info = data;
            });
        } else {
            $.extend($scope.info, $scope.defaultInfo);
        }
    });

    /**
     *  set the save method
     */
    $scope.save = function (modalName, tableName) {
        // 取得学员ID
        $scope.info["student_id"] = TableAjax.tableMap["pay-list"].param["student_id"];

        // 保存
        tableName = tableName || "pay-list";
        infoTool.saveInfoData($scope.info, $scope.module, $scope.controller, "saveAdmin").then(function (response) {
            if (response.data.result == "1") {
                $scope.infoErrorShow = false;
                $scope.infoErrorMsg = "";
                $("div[name='pay-form-info']").modal("hide");
                TableAjax.reload(tableName);
            } else {
                $scope.infoErrorShow = true;
                $scope.infoErrorMsg = response.msg;
            }
            $scope.info["id"] = null;
        });
    }

    $scope.add = function(){
        TableAjax.add('pay-form-info');
    }
}]);


/**
 * 链接调用对象
 */
var payInfo = function () {
    /**
     * 返回
     */
    return {
        edit: function (id) {
            TableAjax.edit(id, "pay-form-info");
        },
        delete: function (id) {
            TableAjax.delete(id, "pay-list");
        }
    };
}();

/**
 * 显示划款信息界面
 * @param id 对象id
 */
function showStudentPayList(id) {
    var param = {};
    param["student_id"] = id;
    TableAjax.init('pay-list', "EO2OPayment", 'EO2OPayment', param);
    $("#pay-list").modal("show");
}