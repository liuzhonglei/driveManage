MetronicApp.filter('propsFilter', function () {
    return function (items, props) {
        var out = [];

        if (angular.isArray(items)) {
            items.forEach(function (item) {
                var itemMatches = false;

                var keys = Object.keys(props);
                for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                        itemMatches = true;
                        break;
                    }
                }

                if (itemMatches) {
                    out.push(item);
                }
            });
        } else {
            // Let the output be the input untouched
            out = items;
        }

        return out;
    };
});

/* Setup general page controller */
MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', '$timeout', 'metadata', function ($rootScope, $http, $scope, $timeout, metadata) {
    // param
    $scope.model = $rootScope.$state.$current.data.model;
    $scope.metadata = {};
    $scope.selected = {};
    $scope.fieldList = {};
    $scope.info = {};
    $scope.infoId = null;

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        // init
        //(function($){
        //    $.fn.datepicker.dates['zh-CN'] = {
        //        days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
        //        daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
        //        daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
        //        months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        //        monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        //        today: "今日",
        //        format: "yyyy年mm月dd日",
        //        weekStart: 1
        //    };
        //}(jQuery));

        //init
        Metronic.initAjax();

        // init the table
        TableAjax.init($rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController);
        $scope.TableAjax = TableAjax;

        // init the field
        $http({
            method: "post",
            headers: function ($httpProvider) {
                $httpProvider.defaults.withCredentials = true;
            },
            url: Metronic.rootPath() + "/index.php?s=/addon/"+$rootScope.$state.$current.data.module+"/"+$rootScope.$state.$current.data.handleController+"/getModelInfo"
        }).success(function (data) {
            // set searchTexg
            $scope.searchName = data.model.search_key;

            // init the metadata
            for (var i in data.fieldList) {
                for (var j in data[i]) {
                    if (data[i][j] !== null) {
                        var extra = data[i][j].extra;
                        if (extra !== undefined && extra !== null && extra.length > 0) {
                            var result = [];
                            var params = extra.split("\r\n");
                            for (var z in params) {
                                var map = params[z].split(":");
                                if (map.length == 2) {
                                    result.push({"value": map[0], "text": map[1]});
                                }
                            }
                            $scope.metadata[data[i][j].name] = result;
                        }
                    }
                }
            }

            // set  the fieldList
            $scope.fieldList = data[1];
            $scope.search_field = data[1];


        });
    });

    // get edit info
    $scope.$watch('infoId', function () {
        // todo
        //$(".fileinput").fileinput('clear');

        if ($scope.infoId != null && $scope.infoId != "") {
            $http({
                method: "get",
                url: Metronic.rootPath() + "/index.php?s=/addon/School/Admin/getDataById/model/" + $scope.model + "/id/" + $scope.infoId
            }).success(function (data) {
                $scope.info = data;
                for (var name in data) {
                    if($scope.info[name] == "null"){
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
        }else{
            $scope.info = {photoUrl:"http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"};
            $scope.infoId = null;
        }

        ComponentsPickers.init();
        //ComponentsFormTools.init();
    });

    /**
     *  set the save method
     */
    $scope.save = function () {
        for (var name in $scope.info) {
            if(metadata[name] != null){
                $scope.info[name] = $scope.selected[name].value;
            }
        }

        var params = JSON.stringify($scope.info).replace("{", "").replace("}", "").replace(/\,/g, "&").replace(/\"/g, "").replace(/\:/g, "=");
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/School/Admin/saveModel/model/" + $scope.model,
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
     * upload the img
     */
    $scope.file;
    $scope.uploadPic = function (ele) {
        var xhr = new XMLHttpRequest();
        var files = ele.files;
        var formData = new FormData();
        formData.append('filename', files[0]); // index 为第 n 个文件的索引
        xhr.open('post', Metronic.rootPath() + "/index.php?s=/home/File/uploadPicture.html"); // url 为提交的后台地址
        xhr.addEventListener("load", uploadComplete, false); // 处理上传完成
        // 改变t2的函数
        function uploadComplete() {
            var returnData = JSON.parse(xhr.responseText);
            $scope.info[this.id] = returnData.id;
        }

        xhr.send(formData);
    }
}]);
