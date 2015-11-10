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
    list: new Array('../assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css',

        '../assets/global/plugins/datatables/all.min.js',
        '../assets/global/scripts/datatable.js',

        'js/scripts/weixin-table-ajax.js',
        'js/controllers/ListController.js'),
    info: new Array('../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css',
        '../assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css',
        '../assets/global/plugins/jquery-tags-input/jquery.tagsinput.css',
        '../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css',
        '../assets/global/plugins/typeahead/typeahead.css',
        '../assets/global/plugins/bootstrap-datepicker/css/datepicker3.css',
        '../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
        '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',

        '../assets/global/plugins/fuelux/js/spinner.min.js',
        '../assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js',
        '../assets/global/plugins/jquery.input-ip-address-control-1.0.min.js',
        '../assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js',
        '../assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
        '../assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js',
        '../assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js',
        '../assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
        '../assets/global/plugins/typeahead/handlebars.min.js',
        '../assets/global/plugins/typeahead/typeahead.bundle.min.js',
        '../assets/admin/pages/scripts/components-pickers.js',
        '../assets/admin/pages/scripts/components-form-tools.js',
        'js/controllers/InfoController.js')
};

/* Configure ocLazyLoader(refer: https://github.com/ocombe/ocLazyLoad) */
MetronicApp.config(['$ocLazyLoadProvider', function ($ocLazyLoadProvider) {
    $ocLazyLoadProvider.config({
        cssFilesInsertBefore: 'ng_load_plugins_before' // load the above css files before a LINK element with this ID. Dynamic CSS files must be loaded between core and theme css files
    });
}]);

/**
 * define metadata
 */
MetronicApp.constant('metadata',
    {
        "sex": [{"name": "男", value: "M"}, {"name": "女", value: "M"}],
        "intro_source": []
    });    //方法3定义全局变量


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
MetronicApp.controller('HeaderController', ['$scope', function ($scope) {
    $scope.$on('$includeContentLoaded', function () {
        Layout.initHeader(); // init header
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
            data: {pageTitle: 'Dashboard', pageSubTitle: 'statistics & reports'},
            controller: "DashboardController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name: 'MetronicApp',
                        insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                        files: [
                            '../assets/global/plugins/morris/morris.css',
                            '../assets/admin/pages/css/tasks.css',

                            '../assets/global/plugins/morris/morris.min.js',
                            '../assets/global/plugins/morris/raphael-min.js',
                            '../assets/global/plugins/jquery.sparkline.min.js',

                            '../assets/admin/pages/scripts/index3.js',
                            '../assets/admin/pages/scripts/tasks.js',

                            'js/controllers/DashboardController.js'
                        ]
                    });
                }]
            }
        })

        // student info
        .state("studentList", {
            url: "/student/list.html",
            templateUrl: "views/list.html",
            data: {
                pageTitle: "学员列表",
                module: "Student",
                handleController: "Student",
                modelName: "学员",
                action: "edit",
                info: true
            },
            controller: "ListController",
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
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.info, depFile.list)
                        }]);
                }]
            }
        })

        // student info
        .state("studentQuestionList", {
            url: "/student/questionList.html",
            templateUrl: "views/list.html",
            data: {pageTitle: "问题列表", module: "Student", handleController: "Question", action: "show"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: depFile.list
                        }]);
                }]
            }
        })

        // student info
        .state("studentBannerList", {
            url: "/student/bannerList.html",
            templateUrl: "views/list.html",
            data: {pageTitle: "学员锦旗", module: "Student", handleController: "Banner", action: "show"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: depFile.list
                        }]);
                }]
            }
        })

        // student info
        .state("studentNotificationList", {
            url: "/student/notificationList.html",
            templateUrl: "views/list.html",
            data: {pageTitle: "考试通知", module: "Student", handleController: "Notification", action: "show", conf: true},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: depFile.list
                        }]);
                }]
            }
        })

        // student info
        .state("studentAppriseList", {
            url: "/student/appriseList.html",
            templateUrl: "views/list.html",
            data: {pageTitle: "学员评级", module: "Student", handleController: "Appraise", action: "show"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: depFile.list
                        }]);
                }]
            }
        })


        //// school info
        //.state("schoolEdit", {
        //    url: "/school/edit.html",
        //    templateUrl: "views/school/edit.html",
        //    data: {pageTitle: "学员评级", module: "Teacher", handleController: "Teacher", action: "show"},
        //    controller: "EditController",
        //    resolve: {
        //        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
        //            return $ocLazyLoad.load([{
        //                name: 'angularFileUpload',
        //                files: [
        //                    '../assets/global/plugins/angularjs/plugins/angular-file-upload/angular-file-upload.min.js',
        //                ]
        //            }, {
        //                name: 'MetronicApp',
        //                insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
        //                files: [
        //                    '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css',
        //
        //                    '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
        //                    '../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
        //                    '../assets/global/plugins/angularjs/plugins/angular-file-upload/angular-file-upload.min.js',
        //                    'js/controllers/school/EditController.js'
        //                ]
        //            }]);
        //        }]
        //    }
        //})

        // school info
        .state("schoolList", {
            url: "/school/list.html",
            templateUrl: "views/list.html",
            data: {pageTitle: "驾校信息", module: "School", handleController: "School", action: "edit"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: depFile.list
                        }]);
                }]
            }
        })

        // school info
        .state("teacherList", {
            url: "/teacher/list.html",
            templateUrl: "views/list.html",
            data: {pageTitle: "教练信息", module: "Teacher", handleController: "Teacher", action: "edit"},
            controller: "ListController",
            resolve: {
                deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                    return $ocLazyLoad.load([
                        {
                            name: 'MetronicApp',
                            insertBefore: '#ng_load_plugins_before', // load the above css files before '#ng_load_plugins_before'
                            files: $.merge(depFile.info,depFile.list)
                        }]);
                }]
            }
        })
}]);

/* Init global settings and run the app */
MetronicApp.run(["$rootScope", "settings", "$state", function ($rootScope, settings, $state) {
    $rootScope.$state = $state; // state to be accessed from view
}]);

