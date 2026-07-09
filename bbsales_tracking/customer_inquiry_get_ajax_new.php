<?php
$page_id=605;$page_slug='customer_inquiry';
include("connect.php");

$tableName = "customer_inquiry";
$register_name = "Inquiry";
$reqdata = json_encode($_REQUEST);
$status = "
    { value: ' ', label: ' '},
    { value: 'Generate', label: 'Generate' },
    { value: 'In Followup', label: 'In Followup' },
    { value: 'Interested', label: 'Interested' },
    { value: 'Not Interested', label: 'Not Interested' },
    { value: 'Working', label: 'Working' },
";
?>
<script type="text/javascript">
    var tableName = "<?=$tableName?>";
    var ajaxFile = "<?=$tableName?>_get_ajax_grid.php";
    var requestData = <?=$reqdata?>;
</script>
<link rel="stylesheet" href="../jqwidgets/styles/jqx.base.css" type="text/css" />
<link rel="stylesheet" href="../jqwidgets/styles/jqx.classic.css" type="text/css" />
<link rel="stylesheet" href="../jqwidgets/styles/custom.css" type="text/css" />
<style type="text/css">
    /*.no-quote
    {
        background-image: linear-gradient(180deg, #f2784b, #f2784b 0px, #f2875c66 7px, transparent)!important;
    }
    .with-quote
    {
        background-image: linear-gradient(180deg, #4e8d0c, #4e8d0c 0px, #4e8d0c54 7px, transparent)!important;
    }*/
</style>
<script type="text/javascript" src="../jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxcheckbox.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.selection.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.filter.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.sort.js"></script>  
<script type="text/javascript" src="../jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.pager.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.edit.js"></script> 
<script type="text/javascript" src="../jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.columnsreorder.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.columnsresize.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxcalendar.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxnumberinput.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxdatetimeinput.js"></script>
<script type="text/javascript" src="../jqwidgets/globalization/globalize.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxcombobox.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxdata.export.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxgrid.export.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxpanel.js"></script>  
<script type="text/javascript" src="../jqwidgets/custom.js"></script>
<!-- <script src='../../assets/global/plugins/jquery.blockui.min.js'></script> -->



<script type="text/javascript">
    $(document).ready(function () {
        // jqx.credits="75CE8878-FCD1-4EC7-9249-BA0F153A5DE8";

        var status = [<?=$status?>];
        var statusSource =
        {
             datatype: "array",
             datafields: [
                 { name: 'label', type: 'string' },
                 { name: 'value', type: 'string' }
             ],
             localdata: status
        };

        var statusAdapter = new $.jqx.dataAdapter(statusSource, {
            autoBind: true
        });


        // prepare the data
        var data = {};
        var theme = 'energyblue';
        var source = {
            datatype: "json",
            datafields: [{
                    name: 'action',
                    type: 'string'
                    // delete
                },
                /*{
                    name: 'action1',
                    type: 'string'
                    // edit
                },*/
                {
                    name: 'count',
                    type: 'string'
                },

                {
                    name: 'action2',
                    type: 'string'
                    // view
                },
                {
                    name: 'action3',
                    type: 'string'
                    // view
                },
                {
                    name: 'status',
                    type: 'string',
                    value: 'status',
                    values: { 
                        source: statusAdapter.records, 
                        value: 'value', 
                        name: 'label' 
                    },
                    // adasdas
                },
                {
                    name: 'source_of_inquiry',
                    type: 'string'
                },
                {
                    name: 'executive_type',
                    type: 'string'
                },
                {
                    name: 'company_name',
                    type: 'string'
                },
                {
                    name: 'person_name',
                    type: 'string'
                },
                {
                    name: 'mobile_number',
                    type: 'string'
                },
                {
                    name: 'email_address',
                    type: 'string'
                },
                {
                    name: 'sales_executive_id',
                    type: 'string'
                },
                {
                    name: 'inquiry_assign_to',
                    type: 'string'
                },
                {
                    name: 'country',
                    type: 'string'
                },
                {
                    name: 'state',
                    type: 'string'
                },
                {
                    name: 'city',
                    type: 'string'
                },
                {
                    name: 'date_of_call',
                    type: 'string'
                },
                {
                    name: 'followup_count',
                    type: 'string'
                },
                
                /*{
                    name: 'image_path',
                    type: 'string'
                },*/
            ],
            cache: false,
            id: 'id',
            url: ajaxFile,
            type: 'POST',
            root: 'Rows',
            data: requestData,
            sort: function()
            {
                // update the grid and send a request to the server.
                $("#jqxgrid").jqxGrid('updatebounddata', 'sort');
            },
            beforeprocessing: function (data) {
                source.totalrecords = data[0].TotalRows;
            },
            updaterow: function (rowid, rowdata, commit) {
                var datafield = editedColumn;
                var mainvalue = rowdata[datafield];
                // synchronize with the server - send update command
                var data = {
                    "update": true,
                    "id": rowdata.id,
                    "field": datafield,
                    [datafield]: mainvalue,
                }

                $.ajax({
                    dataType: 'json',
                    url: ajaxFile,
                    data: data,
                    method: 'POST',
                    success: function (data, status, xhr) {
                        commit(true);
                        // LastUpdatedBy();
                    }
                });
            },

        };

        var dataadapter = new $.jqx.dataAdapter(source, {
            loadError: function (xhr, status, error) {
                alert(error);
            }
        });
        var editedColumn;

        var cellendedit = function (row, datafield, columntype, oldvalue, newvalue) {
            editMode = false;
            editedColumn = datafield;
        };
        var cellbeginedit = function (row, datafield, columntype, oldvalue, newvalue) {
            editMode = true;
        };

        $(document).on('keydown', function (e) {
            if ((e.ctrlKey) && (e.altKey) && (String.fromCharCode(e.which).toLowerCase() === 'x')) {
                var cell = $('#jqxgrid').jqxGrid('getselectedcell');
                var rowData = $('#jqxgrid').jqxGrid('getrowdata', cell['rowindex']);
                var value = rowData['id'];

                var r = confirm('Are You Sure You Want To Delete?');
                if (r) {
                    $.ajax({
                        method: 'POST',
                        url: "ajax_delete_table_row.php",
                        data: {
                            table: tableName,
                            id: value
                        },
                        success: function (result) {
                            $("#jqxgrid").jqxGrid('updatebounddata');
                            setTimeout(function () {
                                var datainformations = $("#jqxgrid").jqxGrid("getdatainformation");
                                var rowscounts = datainformations.rowscount;
                                // $(".totalrowspan").html(" - (TR : " + rowscounts + ")");
                                $(".classdisdel").not(":has(.test)").append('<span style="color:#c11a1a;" class="test"><i class="fa fa-trash fa-lg fa-fw" style="padding-top: 40%"> </i></span>');
                            }, 1000);
                        }
                    });
                }
            } else if ((e.metaKey || e.ctrlKey) && (String.fromCharCode(e.which).toLowerCase() === 'm')) {
                $("#jqxgrid").jqxGrid('clearselection');
            } else if ((e.ctrlKey) && (e.altKey) && (String.fromCharCode(e.which).toLowerCase() === 'f')) {
                $("#jqxgrid").jqxGrid('clearselection');
                $('#jqxgrid').jqxGrid('selectcell', 0, 'id');
                $("#jqxgrid").jqxGrid('focus');
            }
        });
        
        var cellclass = function (row, columnfield, value) {
            var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
            return ' common-cls normal-field ' + id + '_getval_' + columnfield + " change_color_" + id;
        }
        var cellclassdis = function (row, columnfield, value) {
            var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
            return ' common-cls disable ' + id + '_getval_' + columnfield + " change_color_" + id;
        }
        var cellclassdismedium = function (row, columnfield, value) {
            var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
            
            if(columnfield == "source_of_inquiry")
            {
                var color_india_mart = "";
                var color_trade_india = "";
                
                if(value=="India Mart")
                {
                    color_india_mart = "color_india_mart";
                }
                if(value=="Trade India")
                {
                    color_trade_india = "color_trade_india";
                }
            }

            return ' common-cls disable ' + id + '_getval_' + columnfield + " change_color_" + id + " " +color_india_mart + " " + color_trade_india;
        }

        var cellclassdisdel = function (row, columnfield, value) {
            var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
            return ' common-cls disable classdisdel ' + id + '_getval_' + columnfield + " change_color_" + id;
        }
        var cellclasswithFollowupcount = function (row, columnfield, value) {
            var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
            var followup_count = $('#jqxgrid').jqxGrid('getcellvalue', row, "followup_count");
            if(followup_count==0)
            {
                return ' common-cls no-quote normal-field ' + id + '_getval_' + columnfield + " change_color_" + id;
            } 
        }
        var cellclassview = function (row, columnfield, value) {
            var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
            return ' common-cls disable classdisview overflow-view '+id+'_getval_'+columnfield + " change_color_" + id;
        }
        var coloredred = function (element) {
            $(element).parent().addClass('jqx-grid-column-header-blue');
        }

        // initialize jqxGrid
        var height = ($(window).height() - 150);
        var editMode = false;
        $("#jqxgrid").jqxGrid({
            width: "99%",
            height: height,
            selectionmode: 'multiplecellsadvanced',
            source: dataadapter,
            theme: theme,
            editable: true,
            editmode: 'selectedcell',
            virtualmode: true,
            sortable: true,
            filterable: false,
            columnsresize: true,
            columnsreorder: true,
            enabletooltips: true,
            rendergridrows: function (obj) {
                return obj.data;
            },
            ready: function () {
                $('#jqxgrid').jqxGrid('selectcell', 0, 'id');
                $("#jqxgrid").jqxGrid('focus');

                $(".datetimerange-picker-btn").on("click", function () {
                    $(".datetimerange-picker-input", $(this).closest(".date")).focus();
                });
                $(".datetimerange-picker-input").daterangepicker({
                    "format": "dd-mm-yy ",
                    autoUpdateInput: false,
                    timePicker: false,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });
                $('.datetimerange-picker-input').on('apply.daterangepicker', function (ev, picker) {
                    $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
                });
                $("#country").select2();
                $("#state").select2();
                $("#city").select2();
                $("#c_type").select2();
                $("#type").select2();
                $("#assigned_to").select2();
                $("#status_id").select2();

                setTimeout(function() { 
                    var width_theme = $("#contentjqxgrid").width();
                    $("#contentjqxgrid").css("width","0");
                    $("#contentjqxgrid").css("width",(width_theme*3));
                }, 600);
                // var datainformations = $("#jqxgrid").jqxGrid("getdatainformation");
                // var rowscounts = datainformations.rowscount;
                // $(".totalrowspan").html(" - (TR : " + rowscounts + ")");
                // $(".classdisdel").not(":has(.test)").append('<span style="color:#c11a1a;" class="test"><i class="fa fa-trash fa-lg fa-fw" style="padding-top: 40%"> </i></span>');
            },
            columns: [
                {
                    text: ' ',
                    datafield: 'action', 
                    cellsAlign: 'left',
                    width: 30,
                    cellclassname: cellclassview,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    columntype: 'none',
                    
                    editable: false,
                    filterable: false, 
                    sortable: false, 
                    rendered: coloredred,
                    enabletooltips : false,
                    renderer: function (defaultText, alignment, height) {
                         return '<div style="margin: 0px 0 0 3px;"> </div>';
                    },
                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties) {

                        var quotationhtml = '<li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="QuotationButtonClick('+value+')" style="color:#797b00;background: none;outline: none;border: none;"> <i class="fa fa-file fa-lg fa-fw"> </i>Quotation</button> </li>';

                        return '<button type="button" aria-expanded="false" data-toggle="dropdown" class="view-guideline dropdown-toggle"  style="color:#808080;font-size: 20px;background: none;outline: none;border: none;">  <i class="fa fa-gear"></i> </button> <ul role="menu" class="dropdown-menu"> <li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="EditButtonClick('+value+')" style="color:#403500;background: none;outline: none;border: none;"> <i class="fa fa-pencil fa-lg fa-fw"> </i>Edit</button> </li><li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="DeleteButtonClick('+value+')" style="color:#b50000;background: none;outline: none;border: none;"> <i class="fa fa-trash fa-lg fa-fw"> </i>Delete</button> </li>'+quotationhtml+'</ul>';
                    },
                    columnGroup: "blank",
                },
                /*{
                    text: ' ',
                    datafield: 'action1', 
                    cellsAlign: 'left',
                    width: 30,
                    cellclassname: cellclassview,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    columntype: 'none',
                    
                    editable: false,
                    filterable: false, 
                    sortable: false, 
                    rendered: coloredred,
                    enabletooltips : false,
                    renderer: function (defaultText, alignment, height) {
                         return '<div style="margin: 0px 0 0 3px;"> </div>';
                    },
                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties) {
                        return '<button type="button" class="view-guideline" onclick="DeleteButtonClick('+value+')" style="color:#b50000;background: none;outline: none;border: none;">\n\
                                    <i class="fa fa-trash fa-lg fa-fw"> </i>\n\
                                </button>';
                    },
                    columnGroup: "blank",
                },*/
                {
                    text: 'Sr. No.',
                    datafield: 'count',
                    cellsAlign: 'center',
                    width: 50,
                    cellclassname: cellclasswithFollowupcount,
                    cellendedit: cellendedit,
                    cellbeginedit: cellbeginedit,

                    editable: false,
                    filterable: false,
                    sortable: false,
                    rendered: coloredred,

                    columnGroup: "blank",
                },
                {
                    text: 'Followup  ',
                    datafield: 'action2', 
                    cellsAlign: 'left',
                    width: 50,
                    cellclassname: cellclassview,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    columntype: 'none',
                    
                    editable: false,
                    filterable: false, 
                    sortable: false, 
                    rendered: coloredred,
                    enabletooltips : false,
                    renderer: function (defaultText, alignment, height) {
                         return '<div style="margin: 0px 0 0 3px;">Follow-<br/>up</div>';
                    },
                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties) {
                        return '<button type="button" class="view-guideline" onclick="ViewFollowUp('+value+')" style="color:#1d9407;font-size: 20px;background: none;outline: none;border: none;">\n\
                                    <i style="font-size: 20px!important;" class="fa fa-eye fa-lg fa-fw"> </i>\n\
                                </button>';
                    },
                    columnGroup: "blank",
                },
                {
                    text: 'Quotation',
                    datafield: 'action3', 
                    cellsAlign: 'left',
                    width: 50,
                    cellclassname: cellclassview,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    columntype: 'none',
                    
                    editable: false,
                    filterable: false, 
                    sortable: false, 
                    rendered: coloredred,
                    enabletooltips : false,
                    renderer: function (defaultText, alignment, height) {
                         return '<div style="margin: 0px 0 0 3px;">Quotation</div>';
                    },
                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties) {
                        return '<button type="button" class="view-guideline" onclick="QuotationButtonClick('+value+')" style="color:#1d9407;font-size: 20px;background: none;outline: none;border: none;">\n\
                                    <i style="font-size: 20px!important;" class="fa fa-book fa-lg fa-fw"> </i>\n\
                                </button>';
                    },
                    columnGroup: "blank",
                },
                {
                    text: 'Status',
                    datafield: 'status' ,
                    cellsAlign: 'left',
                    width: 80,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    // rendered: coloredred,

                    filtertype: 'checkedlist',
                    filteritems: status,
                    columntype: 'combobox',
                    createeditor: function (row, cellvalue, editor, cellText, width, height) {
                        editor.jqxComboBox({
                            source: statusAdapter,
                            displayMember: 'label',
                            valueMember: 'value',
                            autoComplete: true,
                            enableSelection:true,
                            autoOpen:true,
                            enableBrowserBoundsDetection:true,
                        });
                    },
                    geteditorvalue: function(row, cellvalue, editor) {
                        return editor.jqxComboBox('getSelectedItem').label;
                    },
                    columnGroup: "status_group",
                },
                {
                    text: 'Source <br/>Medium',
                    datafield: 'source_of_inquiry' ,
                    cellsAlign: 'left',
                    width: 80,
                    cellclassname: cellclassdismedium,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "blank3",
                },
                {
                    text: 'Customer <br/>Type',
                    datafield: 'executive_type' ,
                    cellsAlign: 'left',
                    width: 130,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "customer_type_group",
                },
                {
                    text: 'Company <br/>Name',
                    datafield: 'company_name' ,
                    cellsAlign: 'left',
                    width: 120,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "blank1",
                },
                {
                    text: 'Person <br/>Name',
                    datafield: 'person_name' ,
                    cellsAlign: 'left',
                    minwidth: 100,
                    width: "auto",
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "blank1",
                },
                {
                    text: 'Mobile Number',
                    datafield: 'mobile_number' ,
                    cellsAlign: 'left',
                    minwidth: 130,
                    width: "auto",
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    columntype: 'none',

                    editable: false,
                    rendered: coloredred,

                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties) {
                        return '<i class="fa fa-phone" style="margin-left:8px!important"></i>&nbsp;<a target="_blank" href="https://api.whatsapp.com/send?phone='+value+'&text=">'+value+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
                    },

                    columnGroup: "blank1",
                },
                {
                    text: 'Email Address',
                    datafield: 'email_address' ,
                    cellsAlign: 'left',
                    minwidth: 90,
                    width: "auto",
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    columntype: 'none',

                    editable: false,
                    rendered: coloredred,

                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties) {
                        return '<i class="fa fa-envelope" style="margin-left:8px!important"></i>&nbsp;<a target="_blank" href="mailto:'+value+'">'+value+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
                    },

                    columnGroup: "blank1",
                },
                {
                    text: 'Inquiry <br/>Taken By',
                    datafield: 'sales_executive_id' ,
                    cellsAlign: 'left',
                    width: 120,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "inquiry_taken_group",
                },
                {
                    text: 'Inquiry <br/>Assigned To',
                    datafield: 'inquiry_assign_to' ,
                    cellsAlign: 'left',
                    width: 120,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,
                    
                    columnGroup: "inquiry_assigned_group",
                },
                {
                    text: 'Country',
                    datafield: 'country' ,
                    cellsAlign: 'left',
                    width: 120,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "country_group",
                },
                {
                    text: 'State',
                    datafield: 'state' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "state_group",
                },
                {
                    text: 'City',
                    datafield: 'city' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "city_group",
                },
                {
                    text: 'Date Of Call',
                    datafield: 'date_of_call' ,
                    cellsAlign: 'left',
                    width: 150,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "filter_date_group",
                },
                /*{
                    text: 'Image',
                    datafield: 'image_path' ,
                    cellsAlign: 'left',
                    width: "10%",
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,
                },*/
            ],
            columnGroups:
            [
                { 
                    text: '<select class="form-control" id="c_type" name="c_type" style="width:100%!important;text-align:center;margin: 0!important;padding:0!important;height:100%!important"><option value="">Select Customer Type</option><?php $customer_type = $db->rp_getData("customer_type","*","isDelete=0"); if($customer_type){ while($customer_type_d = mysqli_fetch_assoc($customer_type)) { ?><option value="<?=$customer_type_d["id"]?>" <?=($_REQUEST["c_type"] == $customer_type_d["id"])?"selected":"";?>><?=$db->clean($customer_type_d["name"])?></option><?php }} ?></select>', 
                    name: "customer_type_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" name="country" id="country" onChange="filter_country(this.value);"><option value="">Select Country</option><?php $country_r = $db->rp_getData("country","*",0); if(mysqli_num_rows($country_r)>0) { while($country_d = mysqli_fetch_array($country_r)) { ?><option value="<?php echo $db->clean($country_d["name"]); ?>" <?=($_REQUEST["country"] == $db->clean($country_d["name"]))?"selected":"";?>><?php echo $db->clean($country_d["name"]); ?></option><?php }}?></select>', 
                    name: "country_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" name="state" id="state" autofocus onChange="filter_state(this.value);"><option value="">Select State</option></select>', 
                    name: "state_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" name="city" id="city"><option value="">Select City</option></select>', 
                    name: "city_group", 
                    align: "center" 
                },
                { 
                    text: '<div class="input-group"><input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $_REQUEST['df']; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;"><span class="input-group-addon datetimerange-picker-btn"><i class="fa fa-calendar"></i></span><span class="input-group-btn"></span></div>', 
                    name: "filter_date_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" name="type" id="type" onChange="getSalesExecutive(this.value);"><option value="">Select Inquiry Taken By</option><?php $se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");if($se_r){ while($se_d=mysqli_fetch_assoc($se_r)){ ?><option value="<?php echo $se_d["id"];?>" <?=($_REQUEST["type"] == $se_d["id"])?"selected":"";?>><?php echo $db->clean($se_d["name"]); ?></option><?php } } ?></select>', 
                    name: "inquiry_taken_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" name="assigned_to" id="assigned_to" onChange="getSalesExecutive(this.value);"><option value="">Select Inquiry Assigned By</option><?php $se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1"); if($se_r){ while($se_d=mysqli_fetch_assoc($se_r)){ ?><option value="<?php echo $se_d["id"];?>" <?=($_REQUEST["type"] == $se_d["id"])?"selected":"";?>><?php echo $db->clean($se_d["name"]); ?></option><?php } } ?></select>', 
                    name: "inquiry_assigned_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" id="status_id" name="status_id"> <option value="">Select Status</option> <option <?=($_REQUEST["status_id"]==0 && $_REQUEST["status_id"]!="")?"selected":""; ?> value="0">Generate</option> <option <?=($_REQUEST["status_id"]==1)?"selected":""; ?> value="1">In Followup</option> <option <?=($_REQUEST["status_id"]==2)?"selected":""; ?> value="2">Interested</option> <option <?=($_REQUEST["status_id"]==-1)?"selected":""; ?> value="-1">Not Interested</option> <option <?=($_REQUEST["status_id"]==3)?"selected":""; ?> value="-1">Working</option> </select>', 
                    name: "status_group", 
                    align: "center" 
                },
                { 
                    text: ' ', 
                    name: "blank", 
                    align: "center" 
                },
                { 
                    text: ' ', 
                    name: "blank1", 
                    align: "center" 
                },
                { 
                    text: ' ', 
                    name: "blank3", 
                    align: "center" 
                },
            ]
        });
    });
</script>
<!-- <span class="totalrowspan"></span> -->
<div id='jqxWidget'>
    <div id="jqxgrid"></div>
</div>
<div class="tbl" style="display: none;">
    
</div>
<?php require_once "disconnect.php"; ?>