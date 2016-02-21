'use strict';
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


MetronicApp.filter('conditionFilter', function() {
    return function(items, param) {
        var out = [];
       

        if (angular.isArray(items)) {
            items.forEach(function(item) {
                var itemMatches = true;
                if(item.condition && param.info[item.condition] !== item.conditionValue){
                    itemMatches = false;
                }
                if(item.conditionValue &&  param.value.selected && param.value.selected.value && param.value.selected.conditionValue  != param.info[item.condition]){
                       param.value.selected.value  = null;
                       param.value.selected.text  = null;

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