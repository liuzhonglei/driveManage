MetronicApp.controller('DashboardController', ['$rootScope', '$scope', '$http', function ($rootScope, $scope, $http) {

    // 收费统计数据
    $scope.payData = {};
    $scope.payData.payType = "year";
    $scope.payData.total = 0;
    $scope.payData.payNum = {sign: 0, supplementary: 0, activity: 0, total: 0};
    $scope.payData.payRecord = {};


    //$scope.payData.graph = null;

    //$scope.toDoTimer;

    /**
     * 同步信息
     */
    $scope.$on('$viewContentLoaded', function () {
        // initialize core components
        $scope.payData.payType.payType='year';
        getPayStatics();
        getSignStatics();

        // 刷新待办事项
        //$scope.getDoList();
        //setTimeout($scope.getDoList(), 1000);
    });

    /**
     * 获取待办事项
     */
    $scope.getDoList = function () {
        // 清空计时器
        if ($scope.toDoTimer) {
            window.clearTimeout($scope.toDoTimer);
        }

        // 查询

        // 1分钟后 ,进行下一次查询
        $scope.toDoTimer = setTimeout($scope.getDoList, 60000);
    }

    /**
     * 获取图形信息
     */
    getPayStatics = function () {
        // 统计数据
        $http({
            method: 'GET',
            url: Metronic.rootPath() + '/index.php?s=/addon/EO2OPayment/EO2OPayment/procedureQuery/procedure_name/statics_type_pay/param/' + "'" + $scope.payData.payType + "'" + '.html'
        }).then(function successCallback(response) {
            $scope.payData.payNum.sign = 0;
            $scope.payData.payNum.supplementary = 0;
            $scope.payData.payNum.activity = 0;
            response.data.data.forEach(function (currentValue, index, array) {
                if (currentValue["paytype"]) {
                    $scope.payData.payNum[currentValue["paytype"]] = parseFloat(currentValue["total_fee"]);
                }
            });

            $scope.payData.payNum.total = $scope.payData.payNum.sign + $scope.payData.payNum.supplementary + $scope.payData.payNum.activity;
        }, function errorCallback(response) {
            console.log("error", response);
        });



        // 统计流水
        $http({
            method: 'GET',
            url: Metronic.rootPath() + '/index.php?s=/addon/EO2OPayment/EO2OPayment/procedureQuery/procedure_name/statics_date_pay/param/' + "'" + $scope.payData.payType + "'" + '.html'
        }).then(function successCallback(response) {
            var data = new Array();
            response.data.data.forEach(function (currentValue) {
                if (currentValue["time"]) {
                    data.push({
                        period: currentValue["time"],
                        in_fee: currentValue["in_fee"],
                        out_fee: currentValue["out_fee"]
                    })
                }
            });

            if (!$scope.payData.graph) {
                $("#sales_statistics").empty();
                $scope.payData.graph = Morris.Area({
                    element: 'sales_statistics',
                    padding: 0,
                    behaveLikeLine: false,
                    gridEnabled: false,
                    gridLineColor: false,
                    axes: false,
                    fillOpacity: 1,
                    data: data,
                    lineColors: ['#399a8c', '#92e9dc'],
                    xkey: 'period',
                    ykeys: ['in_fee', 'out_fee'],
                    labels: ['收入', '支出'],
                    pointSize: 0,
                    lineWidth: 0,
                    hideHover: 'auto',
                    resize: true
                });
                console.log("init");
            } else {
                $scope.payData.graph.setData(data);
            }


        }, function errorCallback(response) {
            console.log("error", response);
        });


    }


    // 学员统计数据
    $scope.signData = {};
    $scope.signData.timeType = "day";
    $scope.signData.payRecord = {};
    /**
     * 报名数据
     */
    getSignStatics = function () {
        // 学员报名统计
        $http({
            method: 'GET',
            url: Metronic.rootPath() + '/index.php?s=/addon/EO2OPayment/EO2OPayment/procedureQuery/procedure_name/statics_date_student_sign/param/' + "'" + $scope.signData.timeType + "'" + '.html'
        }).then(function successCallback(response) {
            var data = new Array();
            response.data.data.forEach(function (currentValue) {
                data.push({
                    period: currentValue["time"],
                    count: currentValue["count"]
                })

            });

            if (!$scope.signData.graph) {
                $("#sign_statistics").empty();
                $scope.signData.graph = Morris.Bar({
                    element: 'sign_statistics',
                    data: data,
                    xkey: 'period',
                    ykeys: ['count'],
                    labels: ['数量'],
                    hideHover: "auto"
                });
            } else {
                $scope.signData.graph.setData(data);
            }
        }, function errorCallback(response) {
            console.log("error", response);
        });
    }
}]);