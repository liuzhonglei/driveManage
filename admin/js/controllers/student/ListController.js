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
    }
});


MetronicApp.controller('StudentListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;
    $scope.status = "-1";


    // 默认为报名
    $scope.studentNum = {};
    // 学员数目

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        //init
        Metronic.initAjax();

        // init the table
        $scope.setStatus("-1");
    });

    /**
     * 同步信息
     */
    $scope.syncStudentInfo = function () {

        Metronic.startPageLoading({
            message: '同步中'
        });

        $http({
            method: 'GET',
            url: Metronic.rootPath() + '/index.php?s=/addon/Student/Student/syncStudent/status/' + $scope.status
        }).then(function successCallback(response) {
            Metronic.stopPageLoading();
            $scope.loadTable();
        }, function errorCallback(response) {
            Metronic.stopPageLoading();
            alert(response);
        });
    }


    /**
     * 设置状态
     * @type {number}
     */
    $scope.setStatus = function (status) {
        $scope.status = status;
        $scope.loadTable();
    }

    /**
     * 重新加载表哥
     */
    $scope.loadTable = function(){
        TableAjax.modelMap[$rootScope.$state.$current.data.module + "_" + $rootScope.$state.$current.data.handleController] = null;
        $scope.loadStudentNum();
        TableAjax.init('list', $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, getSearchParam());
    }


    /**
     * 获取查询参数
     * @returns {{}}
     */
    function getSearchParam() {
        var param = {};
        $('#search_form').serializeArray().forEach(function (element, index, array) {
            if (element.value != "") {
                param[element.name] = element.value;
                if (param[element.name] && element.name == "sign_begin_date") {
                    var timestamp = Date.parse(param[element.name] + " 00:00:00");
                    timestamp = timestamp / 1000;
                    param[element.name] = timestamp;
                } else if (param[element.name] && element.name == "sign_end_date") {
                    var timestamp = Date.parse(param[element.name] + " 23:59:59");
                    timestamp = timestamp / 1000;
                    param[element.name] = timestamp;
                }
            }

        });
        param.status = $scope.status;
        return param;
    }


    /**
     * 取得学员数目
     */
    $scope.loadStudentNum = function () {
        // 读取标签
        $scope.studentNum = "";
        var param = getSearchParam();
        var url = Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/getStudentNum"
        for (var key in param) {
            url += "/" + key + "/" + param[key];
        }

        $http({
            method: 'GET',
            url:url
        }).then(function successCallback(response) {
            $scope.studentNum = response.data.data;
            if (!$scope.studentNum) {
                $scope.studentNum = 0;
            }
        });
    }

    /**
     * 保存字段配置
     */
    $scope.saveFieldConf = function () {
        var tree = $.jstree.reference('#tree_field-conf');
        var conf = tree.get_selected();

        // 保存
        $http({
            method: 'GET',
            url: Metronic.rootPath() + '/index.php?s=/addon/Student/Student/saveFieldConf/status/' + $scope.status + '/value/' + conf.join() + '.html'
        }).then(function successCallback(response) {
            TableAjax.modelMap[$rootScope.$state.$current.data.module + "_" + $rootScope.$state.$current.data.handleController] = null;
            TableAjax.init('list', $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, {
                status: $scope.status
            });
            $("#model-field-conf").modal("hide");
        });

    }

    /**
     * 显示字段配置
     */
    $scope.showFieldConf = function () {
        Metronic.startPageLoading({
            message: '读取中'
        });

        $http({
            method: 'GET',
            url: Metronic.rootPath() + '/index.php?s=/addon/Student/Student/getFieldConf/status/' + $scope.status + '.html'
        }).then(function successCallback(response) {
            var treeData = {
                "text": "显示字段",
                "children": []
            };

            for (var x in response.data.data) {
                var selected = true;
                if (response.data.data[x].show == false) {
                    selected = false;
                }
                treeData.children.push({
                    "id": response.data.data[x].field[0].split("|")[0],
                    "text": response.data.data[x].title,
                    "state": {
                        "selected": selected
                    }
                });
            }
            var tree = $.jstree.reference('#tree_field-conf');
            if (tree) {
                try {
                    tree.destroy();
                } catch (err) {

                }
            }
            $('#tree_field-conf').jstree({
                'plugins': ["wholerow", "checkbox", "types"],
                'core': {
                    "themes": {
                        "responsive": false
                    },
                    'data': treeData
                },
                "types": {
                    "default": {
                        "icon": "fa fa-folder icon-state-warning icon-lg"
                    },
                    "file": {
                        "icon": "fa fa-file icon-state-warning icon-lg"
                    }
                }
            });


            Metronic.stopPageLoading();
            $("#model-field-conf").modal("show");
        });
    }
}
]);
