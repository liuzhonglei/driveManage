/* Setup general page controller */
MetronicApp.controller('StudentDetailController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.metadata = {};
    $scope.selected = {};
    $scope.info = {};


    //$scope.action = Metronic.rootPath() + "/index.php?s=/addon/Student/Student/saveAdmin";

    // init the field
    $http({
        method: "post",
        headers: function ($httpProvider) {
            $httpProvider.defaults.withCredentials = true;
        },
        url: Metronic.rootPath() + "/index.php?s=/addon/Student/Student/getModelInfo"
    }).success(function (data) {
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
                    //$scope.info[data.fieldList[i][j].name] =
                }
            }
        }
    });

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
        } else {
            // init the field
            $http({
                method: "post",
                headers: function ($httpProvider) {
                    $httpProvider.defaults.withCredentials = true;
                },
                url: Metronic.rootPath() + "/index.php?s=/addon/Student/Student/getModelInfo"
            }).success(function (data) {
                // set the fieldGroup
                $scope.fieldGroup = {};
                var groups = data.model.field_group.split(";");
                for (var i in groups) {
                    var param = groups[i].split(":");
                    $scope.fieldGroup[param[0]] = param[1];
                }

                // set fieldList
                $scope.fieldList = data.fieldList;

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
                            $scope.info[data.fieldList[i][j].name] = data.fieldList[i][j].value;
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


    /**
     * upload the img
     */
    $scope.file = "";

    /**
     *  set the save method
     */
    $scope.save = function (modalName, tableName) {
        modalName = modalName || "form_info";
        tableName = tableName || "list";


        var params = "";
        for (var name in $scope.info) {

            // 转换特殊情况数据
            var field = getField(name);
            if (field) {
                if (field.type == "time" || field.type == "datetime" || field.type == "date") {
                    var timestamp = Date.parse(new Date($scope.info[name]));
                    timestamp = timestamp / 1000;
                    $scope.info[name] = timestamp;
                }
            }

            // 转换数据
            if ($scope.info[name] instanceof Array) {

                for (var index = 0; index < $scope.info[name].length; index++) {
                    if (params != "") {
                        params += "&";
                    }
                    params += name + "[]" + "=" + $scope.info[name][index] + "";
                }
            } else if ($scope.info[name] instanceof Object) {
                if (params != "") {
                    params += "&";
                }
                params += name + "[]" + "=" + $scope.info[name]["value"] + "";
            }
            else {
                if (params != "") {
                    params += "&";
                }
                if ($scope.info[name] == null) {
                    $scope.info[name] = "";
                }
                params += name + "=" + $scope.info[name];
            }
        }

        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/Student/Student/saveAdmin",
            data: params,
            headers: {
                'Accept': "application/json",
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function successCallback(response) {
            if (response.data.result == "1") {
                $scope.infoErrorShow = false;
                $scope.infoErrorMsg = "";
                $("#" + modalName).modal("hide");
                TableAjax.reload(tableName);
            } else {
                $scope.infoErrorShow = true;
                $scope.infoErrorMsg = response.msg;
            }
            $scope.info["id"] = null;
        }, function errorCallback(response) {
             //called asynchronously if an error occurs
             //or server returns response with an error status.
        });
    }
}
]);
