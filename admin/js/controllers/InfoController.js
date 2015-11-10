/* Setup general page controller */
MetronicApp.controller('TableController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    
        // init the table
        TableAjax.init($rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController);
        $scope.TableAjax = TableAjax;


        // init the field
        $http({
            method: "post",
            headers: function ($httpProvider) {
                $httpProvider.defaults.withCredentials = true;
            },
            url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/getModelInfo"
        }).success(function (data) {
            // set searchTexg
            $scope.searchName = data.model.search_key;
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
}]);
