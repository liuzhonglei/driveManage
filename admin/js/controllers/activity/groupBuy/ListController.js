MetronicApp.controller('GroupBuyListController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {
    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        //init
        Metronic.initAjax();

        // init the table
        TableAjax.init('group-buy-info', "GroupBuy", "GroupBuy");
        TableAjax.init('group-buy-party', "GroupBuy", "GroupBuyParty");
        TableAjax.init('group-buy-type', "GroupBuy", "GroupBuyType");

    });
}
]);
