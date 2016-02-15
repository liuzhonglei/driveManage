/***
 Metronic AngularJS App Main Script
 ***/

/* Metronic App */
var MetronicApp = angular.module("MetronicApp", [
    "ui.router",
    "ui.bootstrap",
    "oc.lazyLoad",
    "ngSanitize"
]);


var depFile = {
    chart: new Array(
        "js/controllers/common/ChartController.js"),
    list: new Array(
        //'../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css',
        //'../assets/global/plugins/datatables/all.min.js',
        //'../assets/global/scripts/datatable.js',
        'js/controllers/common/Filter.js',
        'js/controllers/common/ListController.js'),

    info: new Array(
        //'../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css',
        //'../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css',
        //'../assets/global/plugins/jquery-tags-input/jquery.tagsinput.css',
        //'../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css',
        //'../assets/global/plugins/typeahead/typeahead.css',
        //
        //'../assets/global/plugins/bootstrap-datepicker/css/datepicker3.css',
        //
        //'../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',

        //'../assets/global/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.zh-CN.js',
        //'../assets/global/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js',
        //
        //'../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
        //'../assets/global/plugins/fuelux/js/spinner.min.js',
        //'../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js',
        //'../assets/global/plugins/jquery.input-ip-address-control-1.0.min.js',
        //'../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js',
        //'../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
        //'../assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js',
        //'../assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
        //'../assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
        //'../assets/global/plugins/typeahead/handlebars.min.js',
        //'../assets/global/plugins/typeahead/typeahead.bundle.min.js',
        //'../assets/admin/pages/scripts/components-pickers.js',
        //'../assets/admin/pages/scripts/components-form-tools.js',
        'js/controllers/common/InfoController.js'),
    conf: new Array('js/controllers/ConfController.js')
};

/* Configure ocLazyLoader(refer: https://github.com/ocombe/ocLazyLoad) */
MetronicApp.config(['$ocLazyLoadProvider', function ($ocLazyLoadProvider) {
    $ocLazyLoadProvider.config({
        cssFilesInsertBefore: 'ng_load_plugins_before' // load the above css files before a LINK element with this ID. Dynamic CSS files must be loaded between core and theme css files
    });
}]);


/********************************************
 BEGIN: BREAKING CHANGE in AngularJS v1.3.x:
 *********************************************/
/**
 `$controller` will no longer look for controllers on `window`.
 The old behavior of looking on `window` for controllers was originally intended
 for use in examples, demos, and toy apps. We found that allowing global controller
 functions encouraged poor practices, so we resolved to disable this behavior by
 default.

 To migrate, register your controllers with modules rather than exposing them
 as globals:

 Before:

 ```javascript
 function MyController() {
  // ...
}
 ```

 After:

 ```javascript
 angular.module('myApp', []).controller('MyController', [function() {
  // ...
}]);

 Although it's not recommended, you can re-enable the old behavior like this:

 ```javascript
 angular.module('myModule').config(['$controllerProvider', function($controllerProvider) {
  // this option might be handy for migrating old apps, but please don't use it
  // in new ones!
  $controllerProvider.allowGlobals();
}]);
 **/

//AngularJS v1.3.x workaround for old style controller declarition in HTML
MetronicApp.config(['$controllerProvider', function ($controllerProvider) {
    // this option might be handy for migrating old apps, but please don't use it
    // in new ones!
    $controllerProvider.allowGlobals();
}]);

/********************************************
 END: BREAKING CHANGE in AngularJS v1.3.x:
 *********************************************/

/* Setup global settings */
MetronicApp.factory('settings', ['$rootScope', function ($rootScope) {
    // supported languages
    var settings = {
        layout: {
            pageSidebarClosed: false, // sidebar state
            pageAutoScrollOnLoad: 1000 // auto scroll to top on page load
        },
        layoutImgPath: Metronic.getAssetsPath() + 'admin/layout/img/',
        layoutCssPath: Metronic.getAssetsPath() + 'admin/layout/css/'
    };

    $rootScope.settings = settings;

    return settings;
}]);

/* Setup App Main Controller */
MetronicApp.controller('AppController', ['$scope', '$rootScope', function ($scope, $rootScope) {
    $scope.$on('$viewContentLoaded', function () {
        Metronic.initComponents(); // init core components
        //Layout.init(); //  Init entire layout(header, footer, sidebar, etc) on page load if the partials included in server side instead of loading with ng-include directive 
    });
}]);

/***
 Layout Partials.
 By default the partials are loaded through AngularJS ng-include directive. In case they loaded in server side(e.g: PHP include function) then below partial
 initialization can be disabled and Layout.init() should be called on page load complete as explained above.
 ***/

/* Setup Layout Part - Header */
MetronicApp.controller('HeaderController', ['$scope', 'infoTool', function ($scope, infoTool) {
     $scope.info = {
        "old": "",
        "password": "",
        "repassword": ""
    };

    $scope.$on('$includeContentLoaded', function () {
        Layout.initHeader(); // init header

        // 判断是否已经等路
        $.get(Metronic.rootPath() + "/index.php?s=/addon/School/School/isLogin", function (data) {
            // 判断是否登录
            if (data.result == "-1") {
                window.location = "./login.html"
            } else {
                $scope.userInfo = data.data;
            }
        });
    });

   
    /**
     * 显示界面
     */
    $scope.showPassword = function () {
        $scope.info.old = "";
        $scope.info.password = "";
        $scope.info.repassword = "";
        $("#alter-password").modal("show");       
    }

    /**
     * 保存界面
     */
    $scope.savePassword = function () {
        var deffer = infoTool.saveInfoData($scope.info, "User", "profile", null, "Home");
        deffer.then(function () {
            $("#alter-password").modal("hide");
        })
    }
}]);

/* Setup Layout Part - Sidebar */
MetronicApp.controller('SidebarController', ['$scope', function ($scope) {
    $scope.$on('$includeContentLoaded', function () {
        // 配置权限

        // 初始化
        Layout.initSidebar(); // init sidebar
    });
}]);

/* Setup Layout Part - Sidebar */
MetronicApp.controller('PageHeadController', ['$scope', function ($scope) {
    $scope.$on('$includeContentLoaded', function () {
        Demo.init(); // init theme panel
    });
}]);

/* Setup Layout Part - Footer */
MetronicApp.controller('FooterController', ['$scope', function ($scope) {
    $scope.$on('$includeContentLoaded', function () {
        Layout.initFooter(); // init footer
    });
}]);

/* Setup Rounting For All Pages */
MetronicApp.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
    // Redirect any unmatched url
    $urlRouterProvider.otherwise("/dashboard.html");

    $stateProvider

    // Dashboard
        .state('dashboard', {
            url: "/dashboard.html",
            templateUrl: "views/dashboard.html",
            data: {pageTitle: '监控台', pageSubTitle: '各种关键信息'},
            controller: "DashboardController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/morris/morris.css',
                            //'../assets/admin/pages/css/tasks.css',

                            '../assets/global/plugins/morris/morris.min.js',
                            '../assets/global/plugins/morris/raphael-min.js',
                            '../assets/global/plugins/jquery.sparkline.min.js',
                            //
                            //'../assets/admin/pages/scripts/index3.js',
                            //'../assets/admin/pages/scripts/tasks.js',

                            'js/controllers/DashboardController.js'
                        ]
                    });
                }]
            }
        })

        // school info
        .state("teacherList", {
            url: "/teacher/list.html",
            templateUrl: "views/teacher/list.html",
            data: {pageTitle: "教练信息", module: "Teacher", handleController: "Teacher", action: "edit", info: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.info, depFile.list).concat(new Array("js/controllers/teacher/PictureController.js"))
                        }]);
                }]
            }
        })


        // 划款模块
        .state("payList", {
            url: "/pay/list.html",
            templateUrl: "views/pay/list.html",
            data: {
                pageTitle: "划款信息",
                module: "EO2OPayment",
                handleController: "EO2OPayment",
                //action: "edit",
                info: true
            },
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(new Array("js/controllers/student/DetailController.js"), depFile.list)
                        }]);
                }]
            }
        })


        // student info
        .state("studentList", {
            url: "/student/list.html",
            templateUrl: "views/student/list.html",
            data: {
                pageTitle: "学员列表",
                module: "Student",
                handleController: "Student",
                modelName: "学员",
                action: "edit",
                info: true
            },
            controller: "StudentListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'ui.select',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: [
                                '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                                '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                            ]
                        }, {
                            name: 'MetronicApp',
                            files: $.merge(depFile.info, new Array('js/controllers/student/ListController.js', 'js/controllers/student/PayInfoController.js',"js/controllers/student/DetailController.js")).concat(depFile.conf)
                        }]);
                }]
            }
        })

        // school info
        .state("schoolList", {
            url: "/school/list.html",
            templateUrl: "views/school/list.html",
            data: {pageTitle: "驾校信息", module: "School", handleController: "School", info: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before',
                            files: $.merge(depFile.info, depFile.list).concat(new Array("js/controllers/school/PictureController.js"))
                        }]);
                }]
            }
        })
        // 相关信息
        .state("schoolInfoList", {
            url: "/schoolInfo/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "相关资料", module: "School", handleController: "Info", action: "edit", info: true, extendClass: "modal-lg"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before',
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })

        // 课程信息
        .state("schoolCourseList", {
            url: "/schoolCourse/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "课程信息", module: "School", handleController: "Course", action: "edit", info: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before',
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })

        // 驾校场地信息
        .state("schoolPlaceList", {
            url: "/schoolPlace/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "驾校场地", module: "School", handleController: "Place", action: "edit", info: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before',
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })


        // 驾校优惠
        .state("schoolPrivilegeList", {
            url: "/schoolPrivilege/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "驾校优惠", module: "School", handleController: "Privilege", action: "edit", info: true, extendClass: "modal-lg"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before',
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })

        // 付款项目信息
        .state("payitemList", {
            url: "/payitem/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "相关资料", module: "School", handleController: "Payitem", action: "edit", info: true, extendClass: "modal-lg"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before',
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })


        // student info
        .state("messagetQuestionList", {
            url: "/message/questionList.html",
            templateUrl: "views/common/list.html",
            data: {
                pageTitle: "问题列表",
                module: "Message",
                handleController: "Message",
                action: "show",
                info: true,
                conf: true
            },
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge($.merge(depFile.info, depFile.list), depFile.conf)
                        }]);
                }]
            }
        })

        // student info
        .state("studentBannerList", {
            url: "/student/bannerList.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "学员锦旗", module: "Student", handleController: "Banner", action: "show", info: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })

        // student info
        .state("studentNotificationList", {
            url: "/student/notificationList.html",
            templateUrl: "views/common/list.html",
            data: {
                pageTitle: "考试通知",
                module: "Student",
                handleController: "Notification",
                action: "edit",
                info: true,
                conf: true
            },
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge($.merge(depFile.info, depFile.list), depFile.conf)
                        }]);
                }]
            }
        })

        // student info
        .state("studentAppriseList", {
            url: "/student/appriseList.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "学员评级", module: "Student", handleController: "Appraise", action: "edit", info: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })




        // school info
        .state("userCenterList", {
            url: "/userCener/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "粉丝信息", module: "UserCenter", handleController: "UserCenter", action: "show"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge($.merge(depFile.info, depFile.list), depFile.conf)
                        }]);
                }]
            }
        })



        // 微信自动回复
        .state("customReplyList", {
            url: "/customReply/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "自动回复", module: "CustomReply", handleController: "CustomReply"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })

        // 砍价活动
        .state("cutPriceList", {
            url: "/cutPrice/list.html",
            templateUrl: "views/common/list.html",
            data: {pageTitle: "砍价活动", module: "CutPrice", handleController: "CutPrice", action: "show", conf: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge($.merge(depFile.info, depFile.list), depFile.conf)
                        }]);
                }]
            }
        })
        //  团购活动
        .state("groupBuyList", {
            url: "/groupBuy/list.html",
            templateUrl: "views/activity/groupBuy/list.html",
            data: {pageTitle: "自动回复", module: "GroupBuy", handleController: "GroupBuyType"},
            controller: "GroupBuyListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.info, depFile.list).concat(new Array('js/controllers/activity/groupBuy/ListController.js'))
                        }]);
                }]
            }
        })
        //// 教练统计
        //.state("teacherTop", {
        //    url: "/statistics/teacherTop.html",
        //    templateUrl: "views/chart/teacherTop.html",
        //    data: {
        //        pageTitle: "教练统计",
        //        module: "Teacher",
        //        handleController: "Teacher",
        //        action: "teacher_rank",
        //        type: "BarChart"
        //    },
        //    controller: "ChartController",
        //    resolve: {
        //        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
        //            return $ocLazyLoad.load([{
        //                name: 'ui.select',
        //                insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
        //                files: [
        //                    '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
        //                    '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
        //                ]
        //            },
        //                {
        //                    name: 'MetronicApp',
        //                    insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
        //                    files: depFile.chart
        //                }]);
        //        }]
        //    }
        //})


        // 资金流入统计
        .state("balanceStatistics", {
            url: "/statistics/balanceStatistics.html",
            templateUrl: "views/chart/balanceStatistics.html",
            data: {
                pageTitle: "资金统计",
                module: "EO2OPayment",
                handleController: "EO2OPayment",
                action: "statics_complex_type_pay",
                param: "'month',null,null,null",
                type: "ComplexPayChart"
            },
            controller: "BalanceParamController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([{
                        name: 'ui.select',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.css',
                            '../assets/global/plugins/angularjs/plugins/ui-select/select.min.js'
                        ]
                    },
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.chart, new Array('js/controllers/pay/BalanceParamController.js'))
                        }]);
                }]
            }
        })
}]);

/* Init global settings and run the app */
MetronicApp.run(["$rootScope", "settings", "$state", function ($rootScope, settings, $state) {
    $rootScope.$state = $state; // state to be accessed from view
}]);

