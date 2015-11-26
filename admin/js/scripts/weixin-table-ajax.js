var TableAjax = function () {
    // 存储表格
    var tableMap = {};

    /**
     * init the table
     * @param module
     * @param handleController
     */
    var init = function (name, module, controller, param) {
        // 表格是否已经创建
        if (tableMap[name] && tableMap[name].grid) {
            var exitDataTable = tableMap[name].grid.getDataTable();
            if(exitDataTable){
                exitDataTable.destroy();
            }
        }

        // 管理表格
        tableMap[name] = {};
        tableMap[name].param = param;
        tableMap[name].module = module;
        tableMap[name].columns = new Array();
        tableMap[name].controler = controller;

        // 查询表格信息
        var url = createUrl(name,"getModelInfo");
        $.get(url, function (data) {
            // set search name
            $("#"+name+"-search-name").val(data.model.search_key);

            // init column
            for (var i = 0; i < data.list_data.fields.length; i++) {
                if (data.list_data.list_grids[i].title.indexOf('checkbox') > 0 || data.list_data.list_grids[i].title == "操作") {
                    tableMap[name].columns.push({
                        "sTitle": data.list_data.list_grids[i].title,
                        "aTargets": [i],
                        "bSortable": false
                    });
                }
                tableMap[name].columns.push({"sTitle": data.list_data.list_grids[i].title, "aTargets": [i]});
            }

            //查询数据
            initData(name, createUrl(name,"listsAdmin"));
        });
    };

    /**
     * reload the table
     * @param searchName
     * @param serachText
     */
    var reload = function (name) {
        var grid = tableMap[name].grid;
        grid.getDataTable().ajax.url(createUrl(name)).load();
    }

    /**
     * create the  data url
     * @returns {string}
     */
    var createUrl = function (name,method) {
        // get search info
        var searchName = $("#"+name+"-search-name").val();
        var searchText = $("#"+name+"-search-text").val();

        // TODO set module
        var url = Metronic.rootPath() + "/index.php?s=/addon/" + tableMap[name].module + "/" + tableMap[name].controler + "/"+method+"/token/gh_36a5c6958de0"
        if (searchName != null && searchText != null && searchName.length > 0 && searchText.length > 0) {
            url += "/" + searchName + "/" + searchText;
        }
        if(tableMap[name].param){
            for(var key in tableMap[name].param){
                url += "/" + key + "/" + tableMap[name].param[key];
            }
        }

        // return
        return url;
    }

    /**
     * get the data and set
     * @param url
     */
    var initData = function (name, url) {
        tableMap[name].grid = new Datatable();
        tableMap[name].grid.init({
            src: $("#"+name+"-table"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            loadingMessage: '读取中...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                aoColumnDefs: tableMap[name].columns,
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

        // handle group action submit button click
        tableMap[name].grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var grid =  tableMap[name].grid;
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
        init: function (name,module, handleController,param) {
            init(name,module, handleController,param);
        },
        delete: function (id) {
            var grid = tableMap['list'].grid;
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
        reload: function (name) {
            reload(name);
        }

    };
}();