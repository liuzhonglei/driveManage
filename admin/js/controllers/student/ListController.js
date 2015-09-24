
/* Setup general page controller */
MetronicApp.controller('ListsController', ['$rootScope', '$scope', 'settings','dataTool', function($rootScope, $scope, settings,dataTool) {
    $scope.$on('$viewContentLoaded', function() {   
    	// initialize core components
    	Metronic.initAjax();
    });

    $scope.delete = function () {
        var ids = $("[name='id[]'][checked]");
        dataTool.delete('student', ids);
    };
}]);
