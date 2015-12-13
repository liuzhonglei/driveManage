/* Setup general page controller */
MetronicApp.controller('EditController', ['$rootScope', '$http', '$scope', 'settings', 'FileUploader', function ($rootScope, $http, $scope, settings, FileUploader) {
    $scope.$on('$viewContentLoaded', function () {
        Metronic.initAjax();
    });

    // init the param
    $scope.errorShow = false;
    $scope.successShow = false;

    // init the page
    $scope.recruit_place_list = [{
        "id": "1",
        "value": "思明区"
    }, {
        "id": "2",
        "value": "湖里区"
    }, {
        "id": "3",
        "value": "集美区"
    }, {
        "id": "4",
        "value": "同安区"
    }, {
        "id": "5",
        "value": "海沧区"
    }, {
        "id": "6",
        "value": "翔安区"
    }];
    $scope.array = [1, 2];
    $scope.photo = "http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image";


    // get the model data
    $http.get(Metronic.rootPath() + "/index.php?s=/addon/School/School/modelEdit/model/school/id/9").success(function (data, status, headers, config) {
        $scope.model = data;
        if ($scope.model.recruit_place !== null) {
            $scope.array = $scope.model.recruit_place.split(",");
        } else {
            $scope.array = [];
        }
    }).error(function (data, status, headers, config) {
        alert("error");
    });

    // photo
    var photo = $scope.photo = new FileUploader({
        url: 'http://localhost:8891/index.php?s=/home/File/uploadPicture'
    });

    // images
    var uploader = $scope.uploader = new FileUploader({
        url: 'http://localhost:8891/index.php?s=/home/File/uploadPicture'
    });

    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function (item /*{File|FileLikeObject}*/, options) {
            return this.queue.length < 10;
        }
    });

    // CALLBACKS
    uploader.onWhenAddingFileFailed = function (item /*{File|FileLikeObject}*/, filter, options) {
        console.info('onWhenAddingFileFailed', item, filter, options);
    };

    uploader.onAfterAddingFile = function (fileItem) {
        if (this.queue.length > 1) {
            this.queue.shift();
        }
        console.info('onAfterAddingFile', fileItem);
    };
    uploader.onAfterAddingAll = function (addedFileItems) {
        console.info('onAfterAddingAll', addedFileItems);
    };
    uploader.onBeforeUploadItem = function (item) {
        console.info('onBeforeUploadItem', item);
    };
    uploader.onProgressItem = function (fileItem, progress) {
        console.info('onProgressItem', fileItem, progress);
    };
    uploader.onProgressAll = function (progress) {
        console.info('onProgressAll', progress);
    };
    uploader.onSuccessItem = function (fileItem, response, status, headers) {
        console.info('onSuccessItem', fileItem, response, status, headers);
    };
    uploader.onErrorItem = function (fileItem, response, status, headers) {
        console.info('onErrorItem', fileItem, response, status, headers);
    };
    uploader.onCancelItem = function (fileItem, response, status, headers) {
        console.info('onCancelItem', fileItem, response, status, headers);
    };
    uploader.onCompleteItem = function (fileItem, response, status, headers) {
        console.info('onCompleteItem', fileItem, response, status, headers);
    };
    uploader.onCompleteAll = function () {
        console.info('onCompleteAll');
    };


    // set the submit method
    $scope.schoonInfoEdit = function () {
        var params = JSON.stringify($scope.$schoonInfo).replace("{", "").replace("}", "").replace(/\,/g, "&").replace(/\"/g, "").replace(/\:/g, "=");
        $http({
            method: "post",
            url: Metronic.rootPath() + "/index.php?s=/addon/School/School/modelEdit/model/school",
            data: params,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function (data, status, headers, config) {
            if (data.code == "0") {
                $scope.errorShow = false;
                $scope.successShow = true;
            } else {
                $scope.errorShow = true;
                $scope.successShow = false;
            }
            $scope.returnMsg = data.msg;
        }).error(function (data, status, headers, config) {
            alert(data);
        });
    };

}]);