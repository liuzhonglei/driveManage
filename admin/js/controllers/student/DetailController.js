/* Setup general page controller */
MetronicApp.controller('StudentDetailController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.metadata = {};
    $scope.selected = {};
    $scope.info = {};


    $scope.setInfoData = function () {
        var data = TableAjax.modelMap["Student_Student"];
        // set the fieldGroup
        $scope.fieldGroup = {};
        var groups = data.model.field_group.split(";");
        for (var i in groups) {
            var param = groups[i].split(":");
            $scope.fieldGroup[param[0]] = param[1];
        }

        // set fieldList
        $scope.fieldList = data.fieldList;

        //var fieldList = new Array();
        for (var i in data.fieldList) {
            for (var j in data.fieldList[i]) {
                if (data.fieldList[i][j] === null || data.fieldList[i][j].length < 1) {
                    continue;
                }
                var extra = data.fieldList[i][j].extra;
                if (extra !== undefined && extra !== null && extra.length > 0) {
                    var result = [];
                    var params = extra.split("\r\n");
                    for (var z in params) {
                        var map = params[z].split(":");
                        if (map.length == 2) {
                            result.push({
                                "value": map[0],
                                "text": map[1]
                            });
                        }
                    }
                    $scope.metadata[data.fieldList[i][j].name] = result;
                }
                $scope.info[data.fieldList[i][j].name] = data.fieldList[i][j].value;
            }
        }
    }

    // init the field
    if (TableAjax.modelMap["Student_Student"]) {
        $scope.setInfoData();
    } else {
        $http({
            method: "get",
            headers: function ($httpProvider) {
                $httpProvider.defaults.withCredentials = true;
            },
            url: Metronic.rootPath() + "/index.php?s=/addon/Student/Student/getModelInfo"
        }).success(function (data) {
            TableAjax.modelMap["Student_Student"] = data;
            $scope.setInfoData();
        });
    }

    // get edit info
    $scope.$watch('info["id"]', function () {
        // todo
        $(".fileinput").fileinput('clear');

        // 查询数据
        if ($scope.info["id"] != null && $scope.info["id"].length > 0) {
            $http({
                method: "get",
                url: Metronic.rootPath() + "/index.php?s=/addon/Student/Student/getModelDataById/id/" + $scope.info["id"]
            }).success(function (data) {
                $scope.info = data;
                for (var name in data) {
                    if ($scope.info[name] == null) {
                        $scope.info[name] = "";
                    }

                    // 设置当前选中项
                    if ($scope.metadata[name] != null) {
                        var currentMetaData = $scope.metadata[name]
                        for (var fieldMetadata in currentMetaData) {
                            if (currentMetaData[fieldMetadata]["value"] == $scope.info[name]) {
                                $scope.info[name] = currentMetaData[fieldMetadata];
                                $scope.selected[name] = currentMetaData[fieldMetadata];
                                break;
                            }
                        }
                    }

                    // 转换特殊情况数据
                    var field = getField(name);
                    if (field) {
                        if (field.type == "datetime") {
                            var newDate = new Date($scope.info[name] * 1000);
                            $scope.info[name] = newDate.pattern("yyyy-MM-dd hh:mm:ss")
                        }

                        if (field.type == "time" || field.type == "date") {
                            var newDate = new Date($scope.info[name] * 1000);
                            $scope.info[name] = newDate.pattern("yyyy-MM-dd")
                        }

                        if (field.type.indexOf("editor") > -1) {
                            var elem = $("[name='" + name + "']");
                            elem.code($scope.info[name]);

                        }
                    }
                }

            });
        }
    });

    /**
     * 根据字段名称取得字段信息
     * @param name 字段名称
     * @returns {*}
     */
    function getField(name) {
        for (var i in $scope.fieldList) {
            for (var j in $scope.fieldList[i]) {
                if ($scope.fieldList[i][j] && $scope.fieldList[i][j].name == name) {
                    return $scope.fieldList[i][j];
                }
            }
        }
    }
}
]);
