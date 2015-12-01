/* 先·Setup general page controller */
MetronicApp.controller('ChartController', ['$rootScope', '$http', '$scope', function ($rootScope, $http, $scope) {

    var url = Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/" + $rootScope.$state.$current.data.action + "/openid/gh_36a5c6958de0/groupField/pay_day";
    // init ajax
    $scope.$on('$viewContentLoaded', function () {
        $http({
            method: "get",
            headers: function ($httpProvider) {
                $httpProvider.defaults.withCredentials = true;
            },
            url: url
        }).success(function (data) {
            //init
            window["create" + $rootScope.$state.$current.data.type](data);
        });
    });
}]);

/**
 * 创建基础柱状图
 */
function createBarChart(data) {
    var chart = AmCharts.makeChart("chart_1", {
        "type": "serial",
        "theme": "light",
        "dataProvider": data,
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "[[category]]: <b>[[value]]</b>",
            "fillAlphas": 0.8,
            "lineAlpha": 0.2,
            "type": "column",
            "valueField": "suc_rate"
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "name",
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "tickPosition": "start",
            "tickLength": 20
        },
        "export": {
            "enabled": true
        }
    });
}

/**
 * 流水组成
 * @param data
 */
function createComposeBarChart(data){
    var chart = AmCharts.makeChart("chart_1", {
        "type": "serial",
        "theme": "light",
        "legend": {
            "horizontalGap": 10,
            "maxColumns": 1,
            "position": "right",
            "useGraphSettings": true,
            "markerSize": 10
        },
        "dataProvider": data,
        "valueAxes": [{
            "stackType": "regular",
            "axisAlpha": 0.3,
            "gridAlpha": 0
        }],
        "graphs": [{
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "fillAlphas": 0.8,
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "报名费",
            "type": "column",
            "color": "#000000",
            "valueField": "sign"
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "fillAlphas": 0.8,
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "补考费",
            "type": "column",
            "color": "#000000",
            "valueField": "supplementary"
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "fillAlphas": 0.8,
            "labelText": "[[value]]",
            "lineAlpha": 0.3,
            "title": "锦旗费用",
            "type": "column",
            "color": "#000000",
            "valueField": "banner"
        }, {
            "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
            "fillAlphas": 0.8,
            "labelText": "[[activity]]",
            "lineAlpha": 0.3,
            "title": "活动费用",
            "type": "column",
            "color": "#000000",
            "valueField": "banner"
        }],
        "categoryField": "pay_day",
        "categoryAxis": {
            "gridPosition": "start",
            "axisAlpha": 0,
            "gridAlpha": 0,
            "position": "left"
        },
        "export": {
            "enabled": true
        }

    });
}
