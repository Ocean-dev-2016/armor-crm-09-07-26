<?php 
$PageConfig = array(
    "id" => 263,
    "navigation" => false
);
include("connect.php");
$tableName = "activity_log";
$register_name = "SYSTEM ACTIVITY LOG";
$tor = "
    { value: ' ' , label: ' ' },
    { value: 'Weekly' , label: 'Weekly' },
    { value: 'Monthly' , label: 'Monthly' },
    { value: 'Quarterly ' , label: 'Quarterly ' },
    { value: 'Half Yearly' , label: 'Half Yearly' },
    { value: 'Yearly ' , label: 'Yearly ' },
";
?>
<script type="text/javascript">
    var tableName = "<?=$tableName?>";
    var ajaxFile = "activity_log_get_ajax.php";
    
    var addToTableFile = "";
    var cust_id_key = "";

    var tor = [<?=$tor?>];
</script>
<?php
include("custom.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$register_name?></title>
    <?php include("include_css.php"); ?>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../jqwidgets/styles/jqx.base.css" type="text/css" />
    <link rel="stylesheet" href="../jqwidgets/styles/jqx.classic.css" type="text/css" />
    <link rel="stylesheet" href="../jqwidgets/styles/bootstrap.min.css" type="text/css" />
    <link href='../../assets/global/plugins/font-awesome/css/font-awesome.min.css' rel='stylesheet' media='screen'>
    <link rel="stylesheet" href="../jqwidgets/styles/custom.css" type="text/css" />
    <style type="text/css">
        body,h1,h2,h3,h4,h5,h6 {
            font-family: "Calibri", sans-serif
        }
        .icon-black i
        {
            color:#0f0f0ff2!important;
        }
        .font-black
        {
          color:#000!important;
        }
        .jqx-grid-column-header {
            background-color: #4e8d0c;
            color: #FFF;
            font-size:12px;
        }
        .jqx-grid-column-header-blue
        {
          background-color: #303e99!important;
        }
    </style>
    <script type="text/javascript" src="../jqwidgets/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="../jqwidgets/bootstrap.min.js"></script>
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
    <script type="text/javascript" src="../../assets/global/scripts/shortcut.js"></script>
    <script type="text/javascript" src="../jqwidgets/custom.js"></script>
    
    <script src='../../assets/global/plugins/jquery.blockui.min.js'></script>

    <script type="text/javascript">
        $(document).ready(function () {
            
            // jqx.credits="75CE8878-FCD1-4EC7-9249-BA0F153A5DE8";
            
            //global search
            $('#inputsearch').keyup(function(e) {

                if (e.keyCode == 13) {
                    var searchText = $("#inputsearch").val();
                    setGlobalFilter(searchText)
                }

                if ($('#inputsearch').val() == '') {
                    $("#jqxgrid").jqxGrid('clearfilters');

                }
            });

            function setGlobalFilter(filtervalue) {

                var columns = $("#jqxgrid").jqxGrid('columns');

                var filtergroup, filter;

                // the filtervalue must be aplied to all columns individually,
                //the column filters are combined using "OR" operator
                for (var i = 0; i < columns.records.length; i++) {
                    if (!columns.records[i].hidden && columns.records[i].filterable) {

                        // alert(columns.records[i].datafield);
                        filtergroup = new $.jqx.filter();
                        filtergroup.operator = 'or';
                        filter = filtergroup.createfilter('stringfilter', filtervalue, 'contains');
                        filtergroup.addfilter(1, filter);
                        $("#jqxgrid").jqxGrid('addfilter', columns.records[i].datafield, filtergroup);
                    }
                }
                $("#jqxgrid").jqxGrid('applyfilters');
            }
            //global search
           
            // prepare the data
            var data = {};
            var theme = 'energyblue';
            var source =
            {
                datatype: "json",
				datafields: [
                    { name: 'id', type: 'string' },
                    { name: 'user_id', type: 'string' },
                    { name: 'table_name', type: 'string' },
                    { name: 'ip', type: 'string' },
                    { name: 'ref_id', type: 'string' },
                    { name: 'activity_type', type: 'string' },
                    { name: 'before_description', type: 'string' },
                    { name: 'after_description', type: 'string' },
                    { name: 'created_date', type: 'string' },
                ],
				cache: false,
				id: 'id',
                url: ajaxFile,
                type: 'POST',
                root: 'Rows',
                filter: function()
                {
                    // update the grid and send a request to the server.
                    $("#jqxgrid").jqxGrid('updatebounddata', 'filter');
                },
                sort: function()
                {
                    // update the grid and send a request to the server.
                    $("#jqxgrid").jqxGrid('updatebounddata', 'sort');
                },
                beforeprocessing: function(data)
                {       
                    source.totalrecords = data[0].TotalRows;                    
                },
            };
			var dataadapter = new $.jqx.dataAdapter(source,{
                loadError: function(xhr, status, error)
                {
                    alert(error);
                }
            });
            

            /*var ArrayForCommentId = <?=$ArrayForCommentId?>;
            function CommentSection()
            {
                $.ajax({
                    dataType: 'json',
                    url: "comment_get_ajax.php",
                    data: {"tableName":tableName},
                    method:'POST',
                    async:false,
                    success: function (data) {
                        ArrayForCommentId = data;
                        $("#jqxgrid").jqxGrid('updatebounddata');
                    }
                });
            }*/
            $(document).on('keydown', function(e) {
                if ((e.altKey) && (String.fromCharCode(e.which).toLowerCase() === 'c'))
                {
                    var cell = $('#jqxgrid').jqxGrid('getselectedcell');
                    var rowData = $('#jqxgrid').jqxGrid('getrowdata', cell['rowindex']);
                    var value = rowData['id'];

                    if (value != "" && cell['datafield'] != "") {
                        $("#mod_id").val(value);
                        $("#mod_tablekey").val(cell['datafield']);
                        $("#CommentModel").modal('show');
                    } else {
                        $("#mod_id").val("");
                        $("#mod_tablekey").val("");
                    }
                } else if (!(e.ctrlKey) && (e.altKey) && (String.fromCharCode(e.which).toLowerCase() === 'x')) {
                    var cell = $('#jqxgrid').jqxGrid('getselectedcell');
                    var rowData = $('#jqxgrid').jqxGrid('getrowdata', cell['rowindex']);
                    var value = rowData['id'];

                    var Cr = confirm('Are You Sure You Want To Clear Comment?');
                    if (Cr) {
                        var f_year = $("#f_year").val();
                        $.ajax({
                            method: "POST",
                            url: "ajax_comment_data.php",
                            data: {
                                reference_id: value,
                                table_key: cell['datafield'],
                                f_year: f_year,
                                table_comment: "",
                                mode: "update",
                                table_name: tableName,
                            },
                            success: function(result) {
                                CommentSection();
                            }
                        });
                    }
                } else if ((e.metaKey || e.ctrlKey) && ( String.fromCharCode(e.which).toLowerCase() === 'm') ) {
                    $("#jqxgrid").jqxGrid('clearselection');
                    $('#inputsearch').focus();
                }else if ((e.ctrlKey ) && (e.altKey) && ( String.fromCharCode(e.which).toLowerCase() === 'f') ) {
                    $("#jqxgrid").jqxGrid('clearselection');
                    $('#jqxgrid').jqxGrid('selectcell', 0, 'id');
                    $("#jqxgrid").jqxGrid('focus');
                }
            });
            
            $('#CommentModel').on('hidden.bs.modal', function() {
                var id = $("#mod_id").val();
                var tableKey = $("#mod_tablekey").val();
                var table_comment = $("#message").val();
                var clas = "." + id + "_getval_" + tableKey;
                if (table_comment != "") {
                    $(clas).addClass("commentflag");
                }
                CommentSection();
                $("#jqxgrid").jqxGrid('focus');
            });

            var cellclassdis = function (row, columnfield, value) {
                var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
                /*var arr = ArrayForCommentId[columnfield];*/
                var arr = "";
                arr = JSON.stringify({arr});
                if(arr != undefined)
                {
                    if(arr.indexOf('"'+id+'"')>-1)
                    {
                        return ' common-cls commentflag disable '+id+'_getval_'+columnfield;
                    }
                    else
                        return ' common-cls disable '+id+'_getval_'+columnfield;
                }
                else
                    return ' common-cls disable '+id+'_getval_'+columnfield;
            }
            var coloredred = function (element) {
                $(element).parent().addClass('jqx-grid-column-header-blue');
            }
            
            // initialize jqxGrid
            var height = ($(window).height()-100);
            var editMode = false;
            $("#jqxgrid").jqxGrid(
            {
                width: "99%",
                height: height,
				selectionmode: 'multiplecellsadvanced',
                source: dataadapter,
                theme: theme,
				editable: false,
                editmode: 'selectedcell',
                virtualmode: true,
                sortable: true,
                filterable: true,
                columnsresize: true,
                columnsreorder: true,
                enabletooltips: true,
                rendergridrows: function(obj)
                {
                      return obj.data;     
                },
                ready: function()
                {
                    if(filterrequest=="true")
                    {
                        addfilter(cust_name_key,customer_name,days);
                    }
                    $('#jqxgrid').jqxGrid('selectcell', 0, 'id');
                    $("#jqxgrid").jqxGrid('focus');

                    var datainformations = $("#jqxgrid").jqxGrid("getdatainformation");
                    var rowscounts = datainformations.rowscount;
                    $(".totalrowspan").html(" - (TR : "+rowscounts+")");                    
                },
                columns: [
                        { 
                            text: 'Sr. No.',
                            datafield: 'id',
                            cellsAlign: 'center', 
                            width: "3%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            filterable: false,
                            rendered: coloredred,
                        },
                        { 
                            text: 'User Name',
                            datafield: 'user_id',
                            cellsAlign: 'center', 
                            width: "8%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                        { 
                            text: 'Database Table Name',
                            datafield: 'table_name',
                            cellsAlign: 'center', 
                            width: "9%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                        { 
                            text: 'IP Address',
                            datafield: 'ip',
                            cellsAlign: 'center', 
                            width: "7%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                        { 
                            text: 'Table Row Id',
                            datafield: 'ref_id',
                            cellsAlign: 'center', 
                            width: "5%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                        { 
                            text: 'Type Of Action',
                            datafield: 'activity_type',
                            cellsAlign: 'center', 
                            width: "6%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                        { 
                            text: 'Before Action Data',
                            datafield: 'before_description',
                            cellsAlign: 'center', 
                            width: "25%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                        { 
                            text: 'After Action Data',
                            datafield: 'after_description',
                            cellsAlign: 'center', 
                            width: "25%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                        { 
                            text: 'Activity Date',
                            datafield: 'created_date',
                            cellsAlign: 'center', 
                            width: "12%",
                            cellclassname: cellclassdis,
                            
                            editable: false, 
                            rendered: coloredred,
                        },
                  ]
            });
        });

        
    </script>
</head>
<body class='default'>
    <?php include("custom_js_serch_export.php"); ?>
	<!-- <div id='jqxWidget' style="padding-top: 65px;"> -->
    <div id='jqxWidget'>
        <div id="jqxgrid"></div>
    </div>
    <div class="tbl" style="display: none;">
        
    </div>
</body>
</html>
