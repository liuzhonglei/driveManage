var TableAjax = function () {
    var columns = new Array();
    var module = null;
    var handleController = null;
    var grid = "";
    //
    //var initPickers = function () {
    //    //init date pickers
    //    $('.date-picker').datepicker({
    //        rtl: Metronic.isRTL(),
    //        autoclose: true
    //    });
    //};

    /**
     * init the table
     * @param module
     * @param handleController
     */
    var init = function (module1, handleController1) {
        if (grid != "" && grid != null) {
            grid.getDataTable().destroy();
        }

        if (columns.length > 0) {
            columns = new Array();
        }
        module = module1;
        handleController = handleController1;
        $.get(Metronic.rootPath() + "/index.php?s=/addon/" + module + "/" + handleController + "/getModelFields", function (data) {
            // init column
            for (var i = 0; i < data.fields.length; i++) {
                if( data.list_grids[i].title.indexOf('checkbox') > 0 ||  data.list_grids[i].title == "操作"){
                    columns.push({"sTitle": data.list_grids[i].title, "aTargets": [i],"bSortable": false});
                }
                columns.push({"sTitle": data.list_grids[i].title, "aTargets": [i]});
            }

            //initPickers();
            handleRecords(createUrl());
        });
    };

    /**
     * reload the table
     * @param searchName
     * @param serachText
     */
    var reload = function () {
        grid.getDataTable().ajax.url(createUrl()).load();
        //init(module, handleController);
    }

    /**
     * create the  data url
     * @returns {string}
     */
    var createUrl = function () {
        var searchName = $("#search-name").val();
        var searchText = $("#search-text").val();
        var url = Metronic.rootPath() + "/index.php?s=/addon/" + module + "/" + handleController + "/listsAdmin/token/gh_36a5c6958de0"
        if (searchName != null && searchText != null && searchName.length > 0 && searchText.length > 0) {
            url += "/" + searchName + "/" + searchText;
        }
        return url;
    }

    /**
     * get the data and set
     * @param url
     */
    var handleRecords = function (url) {
        grid = new Datatable();
        grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            loadingMessage: '读取中...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                aoColumnDefs: columns,
                    //"oLanguage": {
                    //    "sInfoEmpty": "没有数据"
                    //},
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": url// ajax source
                },
                //scrollY: true,
                "order": [
                    [1, "asc"]
                ] // set first column as a default sort by asc
            }
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: '请选择操作',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: '没有数据被选中',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });
    }

    return {
        //main function to initiate the module
        init: function (module, handleController) {
            init(module, handleController);
        },
        delete: function (id) {
            grid.setAjaxParam("customActionType", "action");
            grid.setAjaxParam("customActionName", "delete");
            grid.setAjaxParam("id", id);
            grid.getDataTable().ajax.reload();
        },
        add: function () {
            $("#form_info_id").val(null);
            $("#form_info_id").trigger("change");
            ComponentsPickers.init();
            ComponentsFormTools.init();
            $("#form_info").modal("show");
        },
        edit: function (id) {
            $("#form_info_id").val(id);
            $("#form_info_id").trigger("change");
            ComponentsPickers.init();
            ComponentsFormTools.init();
            $("#form_info").modal("show");
        },
        reload: function () {
            reload();
        }

    };
}();