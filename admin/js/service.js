
/**
 * Created by wuhanchu on 15/9/11.
 * the background tool
 */
MetronicApp.factory('dataTool', ['$http', function ($http) {
    var saveData = function (model,info) {
        var params = JSON.stringify(info).replace("{", "").replace("}", "").replace(/\,/g, "&").replace(/\"/g, "").replace(/\:/g, "=");
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/School/School/modelEdit/model/" + model,
            data: params,
            headers: {
                'Access-Control-Allow-origin' : '*',
                'Access-Control-Allow-Headers' : 'X-Requested-With',
                'Access-Control-Allow-Methods' : 'POST',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function (data, status, headers, config) {
            return data;
        });
    };

    /**
     * get the table field list
     * @param model
     */
    var getFieldList = function(model,fieldList){
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/School/Admin/getFieldList/model/" + model
        }).success(function (data, status, headers, config) {
            fieldList = data;
        });
    }

    var getData = function (model,id){

    }

    var getDataByCon = function(model,condition){

    }

    var getDataList = function(model,id){

    }

    var getDataListByCon = function(model,condition){

    }

    // delete the data
    var dataDel = function(model,id){
        $http({
            method: "get",
            url: Metronic.rootPath() + "/index.php?s=/addon/School/School/modelDel/model/" + model+"id/"+id

        }).success(function (data) {
            return data;
        });
    }

    return {save: saveData,delete:dataDel,getFieldList:getFieldList};
}]);