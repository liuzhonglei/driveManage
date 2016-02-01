/* Setup general page controller */
MetronicApp.controller('InfoController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.metadata = {};
    $scope.selected = {};
    $scope.info = {};



    // init the field
    $http({
        method: "post",
        headers: function ($httpProvider) {
            $httpProvider.defaults.withCredentials = true;
        },
        url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/getModelInfo"
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
            //fieldList[i] = new Array();
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
                            result.push({"value": map[0], "text": map[1]});
                        }
                    }
                    $scope.metadata[data.fieldList[i][j].name] = result;
                }
            }
        }
    });

    // get edit info
    $scope.$watch('info["id"]', function () {
        // todo
        $(".fileinput").fileinput('clear');

        if ($scope.info["id"] != null && $scope.info["id"].length > 0) {
            $http({
                method: "get",
                url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/getDataById/id/" + $scope.info["id"]
            }).success(function (data) {
                $scope.info = data;
                for (var name in data) {
                    if ($scope.info[name] == "null") {
                        $scope.info[name] = "";
                    }

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
                }

            });
        } else {
            $scope.info = {photoUrl: "http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"};
            $scope.info["id"] = null;
        }
    });

    /**
     * upload the img
     */
    $scope.file = "";

    /**
     *  set the save method
     */
    $scope.save = function () {
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
}]);
