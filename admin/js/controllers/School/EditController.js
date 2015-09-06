
/* Setup general page controller */
MetronicApp.controller('ListsController', ['$rootScope','$http', '$scope', 'settings', function($rootScope,$http, $scope, settings) {
    $scope.$on('$viewContentLoaded', function() {
    	Metronic.initAjax();
    });
}]);
