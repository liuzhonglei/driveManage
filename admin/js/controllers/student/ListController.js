MetronicApp.filter('propsFilter', function() {
    return function(items, props) {
        var out = [];

        if (angular.isArray(items)) {
            items.forEach(function(item) {
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


MetronicApp.controller('StudentListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;
    $scope.status = "-1"; // 默认为报名
    $scope.studentNum; // 学员数目

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        //init
        Metronic.initAjax();

        // init the table
        TableAjax.init('list', $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, {status: $scope.status});

        // 读取标签
        $url = Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/getStudentNum";
        $http.get($url).then(function successCallback(response) {
            $scope.studentNum = response.data.data;
        }, function errorCallback(response) {

        });

    });

    // 模块配置数据
    $scope.confData = new Array();

    /**
     * TODO  模块信息方法
     */
    $scope.confSave = function () {

    };

    /**
     * 同步信息
     */
    $scope.syncStudentInfo = function () {
        $http({
            method: 'GET',
            url: '/index.php?s=/addon/Student/Student/syncStudent.html'
        }).then(function successCallback(response) {
            TableAjax.reload('list-km0');
        }, function errorCallback(response) {
            alert(response);
        });
    };


    /**
     * 设置状态
     * @type {number}
     */
    $scope.setStatus = function (status) {
        $scope.status = status
        TableAjax.init('list', $rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController, {status: $scope.status});
    }
}]);
