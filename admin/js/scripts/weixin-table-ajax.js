var TableAjax = function () {
    // 存储表格
    var tableMap = {};

    /**
     * init the table
     * @param module
     * @param handleController
     */
    var init = function (name, module, controller, param, successCallback) {

            // 表格是否已经创建
            if (tableMap[name] && tableMap[name].grid) {
                var exitDataTable = tableMap[name].grid.getDataTable();
                if (exitDataTable) {
                    // 删除数据
                    exitDataTable.destroy();

                    // 清空表头字段
                    var head = $("#" + name + "-table");
                    head.empty();
                }
            }

            // 管理表格
            tableMap[name] = {};
            tableMap[name].param = param;
            tableMap[name].module = module;
            tableMap[name].columns = new Array();
            tableMap[name].controler = controller;

            // 查询表格信息
            var url = createUrl(name, "getModelInfo");
            $.get(url, function (data) {
                // 判断是否登录
                if (data.result == "-1") {
                    window.location = "/admin/login.html"
                }

                // set search name
                var searchEle = $("#" + name + "-search-name");
                if (searchEle) {
                    searchEle.val(data.model.search_key);
                }

                // init column
                for (var i = 0; i < data.list_data.list_grids.length; i++) {
                    // 排序
                    var idOrder = true;
                    if (data.list_data.list_grids[i].title.indexOf('checkbox') > 0 || data.list_data.list_grids[i].title == "操作" || data.list_data.list_grids[i].order == "0") {
                        idOrder = false;
                    }

                    var columnName = "name";
                    if(data.list_data.list_grids[i].field){
                        columnName = data.list_data.list_grids[i].field[0];
                    }
                    tableMap[name].columns.push({
                        "sTitle": data.list_data.list_grids[i].title,
                        "sName": columnName,
                        "aTargets": [i],
                        "bSortable": idOrder
                    });
                }

                //查询数据
                initData(name, createUrl(name, "listsAdmin"), successCallback);
            });
        }
        ;

    /**
     * reload the table
     * @param searchName
     * @param serachText
     */
    var reload = function (name, param) {
        var grid = tableMap[name].grid;
        if (param) {
            tableMap[name].param = param;
        }
        grid.getDataTable().ajax.url(createUrl(name, "listsAdmin")).load();
    }

    /**
     * create the  data url
     * @returns {string}
     */
    var createUrl = function (name, method) {
        // get search info
        var searchName = $("#" + name + "-search-name").val();
        var searchText = $("#" + name + "-search-text").val();

        var url = Metronic.rootPath() + "/index.php?s=/addon/" + tableMap[name].module + "/" + tableMap[name].controler + "/" + method

        if (tableMap[name].param) {
            for (var key in tableMap[name].param) {
                url += "/" + key + "/" + tableMap[name].param[key];
            }
        }

        if (searchName != null && searchText != null && searchName.length > 0 && searchText.length > 0) {
            url += "&" + searchName + "=" + searchText;
        }

        // return
        return url;
    }

    /**
     * get the data and set
     * @param url
     */
    var initData = function (name, url,successCallback) {
        // 表格对象已经初始化过一次
        if (!tableMap[name].grid) {
            tableMap[name].grid = new Datatable();
        }

        // 还未初始化表格对象
        tableMap[name].grid.init({
            src: $("#" + name + "-table"),
            onSuccess: function (grid) {
                if(successCallback){
                    successCallback(grid);
                }
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            loadingMessage: '读取中...',
            dataTable: {
                aoColumnDefs: tableMap[name].columns,
                "bStateSave": true,
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"]
                ],
                "pageLength": 10,
                "ajax": {
                    "url": url
                }
            }
        });

        // handle group action submit button click
        tableMap[name].grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var grid = tableMap[name].grid;
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
        init: function (name, module, handleController, param,successCallback) {
            init(name, module, handleController, param,successCallback);
        },
        reload: function (name, param) {
            reload(name, param);
        },

        get: function (name) {
            return tableMap[name];
        },
        delete: function (id) {
            var grid = tableMap['list'].grid;
            grid.setAjaxParam("customActionType", "action");
            grid.setAjaxParam("customActionName", "delete");
            grid.setAjaxParam("id", id);
            grid.getDataTable().ajax.reload();
        },
        add: function () {
            $("#form_info_id").val("");
            $("#form_info_id").trigger("change");
            ComponentsFormTools.init();
            $("#form_info").modal("show");
        },
        edit: function (id) {
            $("#form_info_id").val(id);
            $("#form_info_id").trigger("change");
            ComponentsFormTools.init();
            $("#form_info").modal("show");
        }
    };
}();
