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
