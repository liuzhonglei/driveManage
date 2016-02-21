var TableAjax = function () {
    // 存储表格
    var tableMap = {};

    // 模型数据
    var modelMap = {};

    /**
     * 清空模型信息
     * @param module
     * @param controller
     */
    var removeModelInfo = function (module, controller) {
        modelMap[module + "_" + controller] = null;
    }

    /**
     * 清空模型数据
     * @param module
     * @param controller
     * @param param
     */
    var emptyModal = function (module, controller, param) {
        modelMap[module + "_" + controller + param] = null;
    }

    /**
     * init the table
     * @param module
     * @param handleController
     */
    var init = function (name, module, controller, param, extendClass, successCallback) {
        // 管理表格
        tableMap[name] = {};
        tableMap[name].param = param;
        tableMap[name].module = module;
        tableMap[name].columns = new Array();
        tableMap[name].controler = controller;

        // 查询表格信息
        if (!modelMap[tableMap[name].module + "_" + tableMap[name].controler + tableMap[name].param]) {
            var url = createUrl(name, "getModelInfo", tableMap[name].param);
            $.get(url, function (data) {
                // 判断是否登录
                if (data.result == "-1") {
                    window.location = "/admin/login.html"
                }

                // 设置全局模型变量
                modelMap[tableMap[name].module + "_" + tableMap[name].controler + param] = data;

                // 加载模型
                loadModel(name);

                //查询数据
                createTable(name, createUrl(name, "listsAdmin"), successCallback);
            });
        } else {
            // 加载模型
            loadModel(name);

            //查询数据
            createTable(name, createUrl(name, "listsAdmin"), successCallback);
        }
    }

    /**
     * 加载模型信息
     * @param name 模型名称
     */
    var loadModel = function (name) {
        // 取得数据
        var data = modelMap[tableMap[name].module + "_" + tableMap[name].controler + tableMap[name].param];

        // set search name
        var searchEle = $("#" + name + "-search-name");
        if (searchEle) {
            searchEle.val(data.model.search_key);
        }

        // init column
        for (var i = 0; i < data.list_data.list_grids.length; i++) {
            // 字段名
            if (!data.list_data.list_grids[i].title) {
                continue;
            }
            var column = {
                "sTitle": data.list_data.list_grids[i].title,
                "aTargets": [i],
                "bSortable": true,
                "sWidth": data.list_data.list_grids[i].width
            };

            // 字段名
            if (data.list_data.list_grids[i].field) {
                column.sName = data.list_data.list_grids[i].field[0];
            }

            // 排序
            if (!data.list_data.list_grids[i].field || data.list_data.list_grids[i].title == "操作" || data.list_data.list_grids[i].order == "0") {
                column.bSortable = false;
                column.sWidth = 10;
            }

            // 增加字段
            tableMap[name].columns.push(column);
        }
    }

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
    var createTable = function (name, url, successCallback) {
        // 清除已存在
        var tableName = "#" + name + "-table";
        if ($.fn.dataTable.isDataTable(tableName)) {
            var table = $(tableName);
            if (table.DataTable()) {
                // 删除数据
                table.DataTable().destroy();
                table.empty();
            }
        }

        // 创建
        tableMap[name].grid = new Datatable();

        // 初始化
        tableMap[name].grid.init({
            src: $("#" + name + "-table"),
            loadingMessage: '读取中...',
            dataTable: {
                aoColumnDefs: tableMap[name].columns,
                //"bStateSave": true,
                "lengthMenu": [
                    [10, 20, 50, 100, 150],
                    [10, 20, 50, 100, 150]
                ],
                "pageLength": 10,
                "ajax": {
                    "url": url
                }
            },
            onSuccess: function (grid) {
                console.log(grid.getTable());
                var info = $(grid.getTable()).find("#list-table_info")
                info.text();
                if (successCallback) {
                    successCallback(grid);
                }
            },
            onError: function (grid) {
                //alert("错误", grid);
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

    /**
     * 返回
     */
    return {
        init: function (name, module, handleController, param, successCallback) {
            init(name, module, handleController, param, successCallback);
        },
        reload: function (name, param) {
            reload(name, param);
        },
        get: function (name) {
            return tableMap[name];
        },

        delete: function (id, tableName) {
            tableName = tableName || 'list';
            bootbox.confirm("确认删除选中数据?", function (result) {
                if (result) {
                    Metronic.startPageLoading({
                        message: '读取中'
                    });
                    var grid = tableMap[tableName].grid;
                    grid.setAjaxParam("customActionType", "action");
                    grid.setAjaxParam("customActionName", "delete");
                    grid.setAjaxParam("id", id);
                    grid.getDataTable().ajax.reload();
                    Metronic.stopPageLoading();
                }
            });
        },
        add: function (formName) {
            Metronic.startPageLoading({
                message: '读取中'
            });
            formName = formName || 'form-info';
            $("input[name='" + formName + "-id']").val("-1");
            $("input[name='" + formName + "-id']").trigger("change");
            $("input[name='" + formName + "-id']").val("");
            $("input[name='" + formName + "-id']").trigger("change");

            $("div[name='" + formName + "']").modal("show");
            var nav = $("a[name='" + formName + "-nav-1']");
            nav.click();
            Metronic.stopPageLoading();
        },
        edit: function (id, formName) {
            Metronic.startPageLoading({
                message: '读取中'
            });
            formName = formName || 'form-info';

            $("input[name='" + formName + "-id']").val(id);
            $("input[name='" + formName + "-id']").trigger("change");
            $("div[name='" + formName + "']").modal("show");
            var nav = $("a[name='" + formName + "-nav-1']");
            nav.click();
            Metronic.stopPageLoading();
        },
        emptyModal: emptyModal,
        modelMap: modelMap,
        tableMap: tableMap,
        removeModelInfo: removeModelInfo
    };
}();
