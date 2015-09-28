var TableAjax = function () {
    var columns = new  Array();
    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            autoclose: true
        });
    };

    var init = function (model) {
        columns = new  Array();
        $.get(Metronic.rootPath() + "/index.php?s=/addon/School/Admin/getModelFields/model/" + model + "/token/gh_36a5c6958de0", function (data) {
            columns.push({"sTitle": '<input type="checkbox" class="group-checkable">', "aTargets": [0],"bSortable": false});
            for (var i = 0; i < data.fields.length; i++) {
                columns.push({"sTitle": data.list_grids[i].title, "aTargets": [i+1]});
            }
            initPickers();
            handleRecords(Metronic.rootPath() + "/index.php?s=/addon/School/Admin/getDataList/model/" + model + "/token/gh_36a5c6958de0/search/");
        });
    };

    var grid = "";

    var handleRecords = function (dataSrc) {
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

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
                // So when dropdowns used the scrollable div should be removed. 
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                 aoColumnDefs: columns,
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": dataSrc, // ajax source
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
        init: function (model) {
            init(model);

        },
        delete: function (id) {
            grid.setAjaxParam("customActionType", "action");
            grid.setAjaxParam("customActionName", "delete");
            grid.setAjaxParam("id", id);
            grid.getDataTable().ajax.reload();
        },
        add : function(){
            $("#form_info_id").val(null);
            $("#form_info_id").trigger("change");
            $("#form_info").modal("show");
        },
        edit: function(id){
            $("#form_info_id").val(id);
            $("#form_info_id").trigger("change");
            $("#form_info").modal("show");
        },
        reload: function(){
            grid.getDataTable().ajax.reload();
        }
    };
}();