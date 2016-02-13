/**
 * Created by wuhanchu on 15/9/11.
 * the background tool
 */
MetronicApp.factory('dataTool', ['$http', '$q', function ($http, $q) {
    /**
     * 保存数据
     * @param model
     * @param info
     */
    var saveData = function (model, info) {
        var params = JSON.stringify(info).replace("{", "").replace("}", "").replace(/\,/g, "&").replace(/\"/g, "").replace(/\:/g, "=");
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/School/School/modelEdit/model/" + model,
            data: params,
            headers: {
                'Access-Control-Allow-origin': '*',
                'Access-Control-Allow-Headers': 'X-Requested-With',
                'Access-Control-Allow-Methods': 'POST',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function (data, status, headers, config) {
            return data;
        });
    };

    /**
     * get the table field list
     * @param model
     */
    var getFieldList = function (model, fieldList) {
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/School/Admin/getFieldList/model/" + model
        }).success(function (data, status, headers, config) {
            fieldList = data;
        });
    }

    /**
     * 取得模型信息
     * @returns {*}
     */
    var getModelInfo = function (module, controller) {
        var deferred = $q.defer();
        if (!TableAjax.modelMap[module + "_" + controller]) {
            $http({
                method: "get",
                headers: function ($httpProvider) {
                    $httpProvider.defaults.withCredentials = true;
                },
                url: Metronic.rootPath() + "/index.php?s=/addon/" + module + "/" + controller + "/getModelInfo"
            }).success(function (data) {
                TableAjax.modelMap[module + "_" + controller] = data;
                deferred.resolve(data);
            });
        } else {
            deferred.resolve(TableAjax.modelMap[module + "_" + controller]);
        }

        // 返回
        return deferred.promise;
    }

    /**
     * 根据参数查询数据
     * @param model 模型名称
     * @param param 参数名称
     * @param callBack 回调函数
     */
    var getDataByParam = function (model, param, callBack) {
        var url = Metronic.rootPath() + "/index.php?s=/addon/School/School/getDataByParam/model/" + model + "/param/" + param;
        $http({
            method: "get",
            url: url
        }).success(function (data, status, headers, config) {
            callBack(data);
        });
    }


    // 返回
    return {save: saveData, getDataByParam: getDataByParam, getFieldList: getFieldList, getModelInfo: getModelInfo};
}]);


/**
 * 信息工具
 * Created by wuhanchu on 15/9/11.
 * the background tool
 */
MetronicApp.factory('infoTool', ['$http', 'dataTool', '$q', function ($http, dataTool, $q) {
    /**
     * 设置对象信息
     */
    var getInfoModel = function (module, controller) {
        // 查询map
        var deferred = $q.defer();
        var modelInfoDefer = dataTool.getModelInfo(module, controller);
        modelInfoDefer.then(function (data) {
            // 返回数据
            var infoData = {
                metadata: {},
                selected: {},
                defaultInfo: {}
            };


            infoData.fieldGroup = {};
            var groups = data.model.field_group.split(";");
            for (var i in groups) {
                var param = groups[i].split(":");
                infoData.fieldGroup[param[0]] = param[1];
            }

            // set fieldList
            infoData.fieldList = data.fieldList;

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
                        infoData.metadata[data.fieldList[i][j].name] = result;
                    }
                    infoData.defaultInfo[data.fieldList[i][j].name] = data.fieldList[i][j].value;
                }
            }

            deferred.resolve(infoData);

        })

        // 返回
        return deferred.promise;
    }

    /**
     * 取得对象信息
     * @param module 模块
     * @param controller 操作器
     * @param modelInfo 模型信息
     */
    var getInfoData = function ($scope, module, controller, id) {
        var deferred = $q.defer();
        $http({
            method: "get",
            url: Metronic.rootPath() + "/index.php?s=/addon/" + module + "/" + controller + "/getModelDataById/id/" + id
        }).success(function (data) {
            var info = data;
            for (var name in data) {
                if (info[name] == null) {
                    info[name] = "";
                }

                // 设置当前选中项
                if ($scope.metadata[name] != null) {
                    var currentMetaData = $scope.metadata[name]
                    for (var fieldMetadata in currentMetaData) {
                        if (currentMetaData[fieldMetadata]["value"] == info[name]) {
                            info[name] = currentMetaData[fieldMetadata];
                            break;
                        }
                    }
                }

                // 转换特殊情况数据
                var field = getField($scope, name);
                if (field) {
                    if (field.type == "datetime") {
                        var newDate = new Date(info[name] * 1000);
                        info[name] = newDate.pattern("yyyy-MM-dd hh:mm:ss")
                    }

                    if (field.type == "time" || field.type == "date") {
                        var newDate = new Date(info[name] * 1000);
                        info[name] = newDate.pattern("yyyy-MM-dd")
                    }

                    if (field.type.indexOf("editor") > -1) {
                        var elem = $("[name='" + name + "']");
                        elem.code(info[name]);
                    }
                }
            }
            deferred.resolve(info);
        });

        // 返回
        return deferred.promise;
    }


    /**
     * 保存信息
     * @param module 模块
     * @param controller 操作器
     * @param data 信息
     */
    var saveInfoData = function (info, module, controller, method,area) {
        var deferred = $q.defer();
        method = method || "saveAdmin";
        area = area || "addon";
        // 转换参数
        var params = "";
        for (var name in info) {

            // 转换数据
            if (info[name] instanceof Array) {
                for (var index = 0; index < info[name].length; index++) {
                    if (params != "") {
                        params += "&";
                    }
                    params += name + "[]" + "=" + info[name][index] + "";
                }
            } else if (info[name] instanceof Object) {
                if (params != "") {
                    params += "&";
                }
                params += name + "[]" + "=" + info[name]["value"] + "";
            }
            else {
                if (params != "") {
                    params += "&";
                }
                if (info[name] == null) {
                    info[name] = "";
                }
                params += name + "=" + info[name];
            }
        }


        // 发送信息
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/"+area+"/" + module + "/" + controller + "/" + method,
            data: params,
            headers: {
                'Accept': "application/json",
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function successCallback(response) {
            if(response.data.result == "1"){
                deferred.resolve(response); 
            }else{
                bootbox.alert(response.data.message);
                deferred.reject(response.data.message);
            }
        }, function errorCallback(response) {
            //called asynchronously if an error occurs
            //or server returns response with an error status.
        });

        // 返回
        return deferred.promise;
    }

    /**
     * 根据字段名称取得字段信息
     * @param name 字段名称
     * @returns {*}
     */
    function getField($scope, name) {
        for (var i in $scope.fieldList) {
            for (var j in $scope.fieldList[i]) {
                if ($scope.fieldList[i][j] && $scope.fieldList[i][j].name == name) {
                    return $scope.fieldList[i][j];
                }
            }
        }
    }


    // 返回
    return {getInfoModel: getInfoModel, getInfoData: getInfoData, saveInfoData: saveInfoData};
}]);