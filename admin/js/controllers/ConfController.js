/* Setup general page controller */
MetronicApp.controller('ConfController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // init the config
    $http({
        method: "get",
        headers: function ($httpProvider) {
            $httpProvider.defaults.withCredentials = true;
        },
        url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/configAdmin"
    }).success(function (data) {
        for(var key in data.config){
           var confItem =  data.config[key];
            var tempOptions = new Array();
            for(var index in confItem.options){
                tempOptions.push({"value":index, "text": confItem.options[index]});
            }
            confItem.options = tempOptions;
        }
        $scope.addon = data;
    });

    /**
     *  set the save method
     */
    $scope.saveConf = function () {
        var config = {};
        for (var key in $scope.addon.config) {
            config[key] = $scope.addon.config[key].value;
        }
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/configAdmin/",
            data: config,
            headers: {
                'Content-Type': 'application/json'
            }
        }).success(function (data) {
            $scope.showMsg = data.msg;

            if (data.status == "0") {
                $scope.errorShow = true;
                $scope.successShow = false;
                if($scope.showMsg == null || $scope.showMsg.length < 1){
                    $scope.showMsg = "操作失败！";
                }
            } else {
                $scope.errorShow = false;
                $scope.successShow = true;
                if($scope.showMsg == null || $scope.showMsg.length < 1){
                    $scope.showMsg = "操作成功！";
                }
            }
        });
    };
}]);
