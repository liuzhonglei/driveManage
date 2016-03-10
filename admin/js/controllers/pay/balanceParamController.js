/**
 * Created by wuhanchu on 16/2/1.
 */
/* 先·Setup general page controller */
MetronicApp.controller('BalanceParamController', ['$rootScope', '$http', '$scope', 'dataTool', function ($rootScope, $http, $scope, dataTool) {
    // 下拉框数据查询
    $scope.schoolPlaceList = new Array();
    dataTool.getDataByParam("school_place", "", function (data) {
        $scope.schoolPlaceList = data.data;
    });



    console.log("type", $scope.payTypeList);

    // 页面加载完
    $scope.$on('$viewContentLoaded', function () {
        $scope.loadData();
    });

    // 重新加载搜索条件
    $scope.loadData = function () {
        $('#condition-modal').modal('hide')
        var param = "";
         var payType = false;
        $("#search_form").serializeArray().forEach(function (item, index) {
            if (param) {
                param += ",";
            }

            if (!item.value) {
                param += "null";
            }else if(item.name == "payType"){
                if(!payType){
                                    param +=  "\"(";

                }
                                 payType = true;

                param +=  item.value ;
            }else {
                param += "'" + item.value + "'";
            }
        });
        if(payType){
            param += ")\"";
        }else{
            param += ",null";
        }

        // 组装URL
        var url = Metronic.rootPath() + "/index.php?s=/addon/" + $rootScope.$state.$current.data.module + "/" + $rootScope.$state.$current.data.handleController + "/procedureQuery/procedure_name/" + $rootScope.$state.$current.data.action + "/param/" + param;

        // 查询
        $http({
            method: "get",
            headers: function ($httpProvider) {
                $httpProvider.defaults.withCredentials = true;
            },
            url: url
        }).success(function (data) {
            if (!$scope.chart) {
                $scope.chart = $scope.createChart(data.data);
            } else {
                $scope.chart.dataProvider = data.data;
                $scope.chart.validateNow();
                $scope.chart.validateData();

            }
        });

    }

    /**
     * 创建基础柱状图
     */
    $scope.createChart = function createComplexPayChart(data) {
        chart = AmCharts.makeChart("chart_1", {
            "type": "serial",
            "theme": "light",
            "angle": 30,
            "legend": {
                "horizontalGap": 10,
                "useGraphSettings": true,
                "markerSize": 10
            },
            "dataProvider": data,
            "valueAxes": [{
                "stackType": "regular",
                "axisAlpha": 0,
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
                "valueField": "sign_fee"
            }, {
                "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                "fillAlphas": 0.8,
                "labelText": "[[value]]",
                "lineAlpha": 0.3,
                "title": "锦旗",
                "type": "column",
                "color": "#000000",
                "valueField": "banner_fee"
            }, {
                "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                "fillAlphas": 0.8,
                "labelText": "[[value]]",
                "lineAlpha": 0.3,
                "title": "补考费",
                "type": "column",
                //"newStack": true,
                "color": "#000000",
                "valueField": "supplementary_fee"
            }, {
                "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                "fillAlphas": 0.8,
                "labelText": "[[value]]",
                "lineAlpha": 0.3,
                "title": "活动费",
                "type": "column",
                "color": "#000000",
                "valueField": "activity_fee"
            }, {
                "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                "fillAlphas": 0.8,
                "labelText": "[[value]]",
                "lineAlpha": 0.3,
                "title": "工资",
                "newStack": true,
                "type": "column",
                "color": "#000000",
                "valueField": "wage_fee"
            }, {
                "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                "fillAlphas": 0.8,
                "labelText": "[[value]]",
                "lineAlpha": 0.3,
                "title": "奖励",
                "type": "column",
                "color": "#000000",
                "valueField": "reward_fee"
            }, {
                "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
                "fillAlphas": 0.8,
                "labelText": "[[value]]",
                "lineAlpha": 0.3,
                "title": "车辆损耗",
                "type": "column",
                "color": "#000000",
                "valueField": "car_fee"
            }],
            "categoryField": "time",
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
        return chart;
    }
}
])
;
