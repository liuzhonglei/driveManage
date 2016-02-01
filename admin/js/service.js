
/**
 * Created by wuhanchu on 15/9/11.
 * the background tool
 */
MetronicApp.factory('dataTool', ['$http', function ($http) {
    /**
     * 保存数据
     * @param model
     * @param info
     */
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

    /**
     * 根据参数查询数据
     * @param model 模型名称
     * @param param 参数名称
     * @param callBack 回调函数
     */
    var getDataByParam = function (model,param,callBack){
        var url = Metronic.rootPath() + "/index.php?s=/addon/School/School/getDataByParam/model/" + model+"/param/"+param;
        $http({
            method: "get",
            url: url
        }).success(function (data, status, headers, config) {
            callBack(data);
        });
    }


    // 返回
    return {save: saveData,getDataByParam:getDataByParam,getFieldList:getFieldList};
}]);