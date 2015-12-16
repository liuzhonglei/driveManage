//MetronicApp.filter('propsFilter', function () {
//    return function (items, props) {
//        var out = [];
//
//        if (angular.isArray(items)) {
//            items.forEach(function (item) {
//                var itemMatches = false;
//                var keys = Object.keys(props);
//                for (var i = 0; i < keys.length; i++) {
//                    var prop = keys[i];
//                    var text = props[prop].toLowerCase();
//                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
//                        itemMatches = true;
//                        break;
//                    }
//                }
//
//                if (itemMatches) {
//                    out.push(item);
//                }
//            });
//        } else {
//            out = items;
//        }
//
//        return out;
//    };
//});

/* Setup general page controller */
MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
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
            url: Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/getModelInfo"
        }).success(function (data) {
            $scope.searchName = data.model.search_key;
        });
    });
}]);
