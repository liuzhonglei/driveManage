var TableAjax = function () {

    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            autoclose: true
        });
    }

    var initMenu = function(dataSrc){
        $.get(dataSrc, function(data){
            for(var i=0; i < data.fields.length; i++){
                var menu = $(".heading");
                menu.html(menu.html()+'<th class="sorting" tabindex="0" aria-controls="datatable_ajax" rowspan="1" colspan="1">'+data.fields[i]+'</th>');
            }
        });
    }

    var handleRecords = function (dataSrc) {

        var grid = new Datatable();
        grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error  
            },
            aoColumns:[
                { "sTitle": "Engine" },
                { "sTitle": "Browser" },
                { "sTitle": "Platform" },
                { "sTitle": "Version", "sClass": "center" },
                {
                    "sTitle": "Grade",
                    "sClass": "center",
                    "fnRender": function(obj) {
                        var sReturn = obj.aData[ obj.iDataColumn ];
                        if ( sReturn == "A" ) {
                            sReturn = "<b>A</b>";
                        }
                        return sReturn;
                    }
                }
            ],
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
                // So when dropdowns used the scrollable div should be removed. 
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": dataSrc, // ajax source
                },
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
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });
    }

    return {

        //main function to initiate the module
        init: function (model) {
            //initMenu(Metronic.rootPath()+"/index.php?s=/addon/School/School/getModelFields/model/"+model);
            initPickers();
            handleRecords(Metronic.rootPath()+"/index.php?s=/addon/School/School/getModelData/model/"+model+"/p/0/draw/1/token/gh_36a5c6958de0");
        }
    };
}();