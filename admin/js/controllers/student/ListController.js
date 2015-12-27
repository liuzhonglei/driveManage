MetronicApp.controller('ListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // param
    $scope.rootData = $rootScope.$state.$current.data;

    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        //init
        Metronic.initAjax();

        // init the table
        TableAjax.init('list-km-2',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"-2"});
        TableAjax.init('list-km-1',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"-1"});
        TableAjax.init('list-km0',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"0"});
        TableAjax.init('list-km1',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"1"});
        TableAjax.init('list-km2',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"2"});
        TableAjax.init('list-km3',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"3"});
        TableAjax.init('list-km4',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"4"});
        TableAjax.init('list-km99',$rootScope.$state.$current.data.module, $rootScope.$state.$current.data.handleController,{status:"99"});

    });
}]);
