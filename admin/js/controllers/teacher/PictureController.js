/**
 * Created by wuhanchu on 16/2/5.
 */
MetronicApp.controller('PictureController', ['$rootScope', '$http', '$scope', 'infoTool', function ($rootScope, $http, $scope, infoTool) {
    // 信息
    $scope.module = "Teacher";
    $scope.controller = "TeacherPhoto";
    $scope.title = "照片列表";

    $scope.listName = "picture-list";

    $scope.info = {};
    $scope.idName = "picture-form-info-id";
    $scope.formName = "picture-form-info";

    /**
     * 设置对象信息
     */
    infoTool.getInfoModel($scope.module, $scope.controller).then(function (data) {
        $.extend($scope, data);
    });


    /**
     * 更新信息
     */
    $scope.$watch('info["id"]', function () {
        // todo
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
        $scope.info["student_id"] = TableAjax.tableMap["picture-list"].param["student_id"];

        // 保存
        tableName = tableName || $scope.listName;
        infoTool.saveInfoData($scope.info, $scope.module, $scope.controller, "saveAdmin").then(function (response) {
            if (response.data.result == "1") {
                $scope.infoErrorShow = false;
                $scope.infoErrorMsg = "";
                $("div[name='picture-form-info']").modal("hide");
                TableAjax.reload(tableName);
            } else {
                $scope.infoErrorShow = true;
                $scope.infoErrorMsg = response.msg;
            }
            $scope.info["id"] = null;
        });
    }

    /**
     *  set the save method
     */
    $scope.add = function (modalName, tableName) {
        TableAjax.add("picture-form-info");
    }
}]);


/**
 * 链接调用对象
 */
var pictureInfo = function () {
    /**
     * 返回
     */
    return {
        edit: function (id) {
            TableAjax.edit(id, "picture-form-info");
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
function showPictureList(id) {
    var param = {};
    param["type"] = "1";
    param["object_id"] = id;
    param["model"] = "teacher";


    TableAjax.init('picture-list', "Teacher", 'TeacherPhoto', param);
    $("#pay-list").modal("show");
}