/***
GLobal Directives
***/

// Route State Load Spinner(used on page or content load)
MetronicApp.directive('ngSpinnerBar', ['$rootScope',
    function($rootScope) {
        return {
            link: function(scope, element, attrs) {
                // by defult hide the spinner bar
                element.addClass('hide'); // hide spinner bar by default

                // display the spinner bar whenever the route changes(the content part started loading)
                $rootScope.$on('$stateChangeStart', function() {
                    element.removeClass('hide'); // show spinner bar
                });

                // hide the spinner bar on rounte change success(after the content loaded)
                $rootScope.$on('$stateChangeSuccess', function() {
                    element.addClass('hide'); // hide spinner bar
                    $('body').removeClass('page-on-load'); // remove page loading indicator
                    Layout.setSidebarMenuActiveLink('match'); // activate selected link in the sidebar menu

                    // auto scorll to page top
                    setTimeout(function () {
                        Metronic.scrollTop(); // scroll to the top on content load
                    }, $rootScope.settings.layout.pageAutoScrollOnLoad);
                });

                // handle errors
                $rootScope.$on('$stateNotFound', function() {
                    element.addClass('hide'); // hide spinner bar
                });

                // handle errors
                $rootScope.$on('$stateChangeError', function() {
                    element.addClass('hide'); // hide spinner bar
                });
            }
        };
    }
])

// Handle global LINK click
MetronicApp.directive('a',
    function() {
        return {
            restrict: 'E',
            link: function(scope, elem, attrs) {
                if (attrs.ngClick || attrs.href === '' || attrs.href === '#') {
                    elem.on('click', function(e) {
                        e.preventDefault(); // prevent link click for above criteria
                    });
                }
            }
        };
    });

// Handle Dropdown Hover Plugin Integration
MetronicApp.directive('dropdownMenuHover', function () {
  return {
    link: function (scope, elem) {
      elem.dropdownHover();
    }
  };  
});


/**
 * 编辑器
 */
MetronicApp.directive('summernote', function () {
    return {
        link: function (scope, elem, attrs) {
            elem.summernote({height: 300,onChange: function(contents, $editable) {
                scope.info[attrs.name] = contents;
            }});
        }
    };
});

/**
 * 日期选择器
 */
MetronicApp.directive('datePicker', function () {
    return {
        restrict: "C",
        link: function (scope, elem, attrs) {
            elem.datepicker({
                rtl: Metronic.isRTL(),
                language:"zh-CN",
                orientation: "left",
                autoclose: true,
                initialDate : new Date()
            });
        }
    };
});


/**
 * 时间选择器
 */
MetronicApp.directive('dataTimePicker', function () {
    return {
        restrict: "C",
        link: function (scope, elem, attrs) {
            elem.datetimepicker({
                autoclose: true,
                language: "zh-CN",
                isRTL: Metronic.isRTL(),
                format: "yyyy-mm-dd hh:ii",
                pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
                initialDate : new Date()
            });
        }
    };
});


MetronicApp.directive('ngThumb', ['$window', function($window) {
        var helper = {
            support: !!($window.FileReader && $window.CanvasRenderingContext2D),
            isFile: function(item) {
                return angular.isObject(item) && item instanceof $window.File;
            },
            isImage: function(file) {
                var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        };

        return {
            restrict: 'A',
            template: '<canvas/>',
            link: function(scope, element, attributes) {
                if (!helper.support) return;

                var params = scope.$eval(attributes.ngThumb);

                if (!helper.isFile(params.file)) return;
                if (!helper.isImage(params.file)) return;

                var canvas = element.find('canvas');
                var reader = new FileReader();

                reader.onload = onLoadFile;
                reader.readAsDataURL(params.file);

                function onLoadFile(event) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = event.target.result;
                }

                function onLoadImage() {
                    var width = params.width || this.width / this.height * params.height;
                    var height = params.height || this.height / this.width * params.width;
                    canvas.attr({ width: width, height: height });
                    canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
                }
            }
        };
    }]);

/**
 * checkboxGroup
 */
MetronicApp.directive("checkboxGroup", function () {
        return {
            restrict: "A",
            link: function (scope, elem, attrs) {
                // Determine initial checked boxes
                scope.$watch('array', function(newValue, oldValue){
                    if (newValue.indexOf(scope.item.id) !== -1) {
                        elem[0].checked = true;
                    }else{
                        elem[0].checked = false;
                    }

                });

                // Update array on click
                elem.bind('click', function () {
                    var index = scope.array.indexOf(scope.item.id);
                    // Add if checked
                    if (elem[0].checked) {
                        if (index === -1) scope.array.push(scope.item.id);
                    }
                    // Remove if unchecked
                    else {
                        if (index !== -1) scope.array.splice(index, 1);
                    }
                    // Sort and update DOM display
                    scope.$apply(scope.array.sort(function (a, b) {
                        return a - b;
                    }));
                });
            }
        };
    }
);

MetronicApp.directive("fileread", [function () {
    return {
        scope: {
            info: "="
        },
        link: function (scope, element, attributes,ngModel) {
            element.bind("change", function (event) {
                var xhr = new XMLHttpRequest();
                var files = event.target.files;
                var formData = new FormData();
                formData.append('filename', files[0]); // index 为第 n 个文件的索引
                xhr.open('post',Metronic.rootPath()+"/index.php?s=/home/File/uploadPicture.html"); // url 为提交的后台地址
                xhr.addEventListener("load", uploadComplete, false); // 处理上传完成
                // 改变t2的函数
                function uploadComplete() {
                    var returnData = JSON.parse(xhr.responseText);
                    var fieldName = attributes["name"]
                    scope.info[fieldName] = returnData.id;
                }
                xhr.send(formData);
            });
        }
    }
}]);