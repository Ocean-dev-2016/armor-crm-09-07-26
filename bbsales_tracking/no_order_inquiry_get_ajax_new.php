<?php
// var_dump($_REQUEST);exit;
if($_REQUEST['inquiry_type']=="-1")
{
    $page_id=621;$page_slug='prospect_inquiry';
}
else if($_REQUEST['inquiry_type']=="0")
{
    $page_id        = 572;
    $page_slug      = 'no_order_inquiry';
}
else
{
    $page_id=620;$page_slug='lead_page';
} 
include("connect.php");

$tableName = "no_order_inquiry";
$register_name = "Inquiry";
$reqdata = json_encode($_REQUEST);
 // print_r($reqdata);exit;
// Note Dhaval  Remove for status_array (Interested,Hot,Warm,Wrong Call)
// $status_array = "
//     { value: ' ', label: ' '},
//     { value: 'Generate', label: 'Generate' },
//     { value: 'In Followup', label: 'In Followup' },
//     { value: 'Non Relavent', label: 'Non Relavent' },
//     { value: 'Not Interested', label: 'Not Interested' },
//     { value: 'Buy Later', label: 'Buy Later' },
//     { value: 'Lost', label: 'Lost' },
//     { value: 'Hot', label: 'Hot' },
//     { value: 'Cold', label: 'Cold' },
//     { value: 'Warm', label: 'Warm' },
//     { value: 'My Work', label: 'My Work' },
//     { value: 'Cancel', label: 'Cancel' },
// ";

$status_array = "
    { value: ' ', label: ' '},
    { value: 'Generate', label: 'Generate' },
    { value: 'Positive', label: 'Positive' },
    { value: 'In Followup', label: 'In Followup' },
    { value: 'Hot', label: 'Hot' },
    { value: 'Cold', label: 'Cold' },
    { value: 'Warm', label: 'Warm' },
    { value: 'My Work', label: 'My Work' },
    { value: 'Buy Later', label: 'Buy Later' },
    { value: 'Cancel', label: 'Cancel' },
    { value: 'Lost', label: 'Lost' },
";
?>
<script type="text/javascript">
    var tableName = "<?=$tableName?>";
    var ajaxFile = "<?=$tableName?>_get_ajax_grid.php";
    var requestData = <?=$reqdata?>;
    /*alert(JSON.stringify(requestData));*/
</script>
<link rel="stylesheet" href="../jqwidgets/styles/jqx.base.css" type="text/css" />
<link rel="stylesheet" href="../jqwidgets/styles/jqx.classic.css" type="text/css" />
<link rel="stylesheet" href="../jqwidgets/styles/custom.css" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css" integrity="sha512-WEQNv9d3+sqyHjrqUZobDhFARZDko2wpWdfcpv44lsypsSuMO0kHGd3MQ8rrsBn/Qa39VojphdU6CMkpJUmDVw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style type="text/css">
    /*.no-quote
    {
        background-image: linear-gradient(180deg, #f2784b, #f2784b 0px, #f2875c66 7px, transparent)!important;
    }
    .with-quote
    {
        background-image: linear-gradient(180deg, #4e8d0c, #4e8d0c 0px, #4e8d0c54 7px, transparent)!important;
    }*/

    .pending-customer-details
    {
        background-image: linear-gradient(180deg, #f2784b, #f2784b 0px, #f2875c66 7px, transparent)!important;
    }
    .prospect-color-class
    {
        background-color: #4b9342 !important;
    }
    .inqiry-color-class
    {
        background-color: #7bd0a9 !important;
    }
    .lead-color-class
    {
        background-color: #c767dc !important;
    }
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
<script type="text/javascript" src="../jqwidgets/jqxwindow.js"></script>
<!-- <script src='../../assets/global/plugins/jquery.blockui.min.js'></script> -->



<script type="text/javascript">
    $(document).ready(function () {
        // jqx.credits="75CE8878-FCD1-4EC7-9249-BA0F153A5DE8";

        var status_array = [<?=$status_array?>];
        var statusSource =
        {
            datatype: "array",
            datafields: [
                { name: 'label', type: 'string' },
                { name: 'value', type: 'string' }
            ],
            localdata: status_array
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
                },
                {
                    name: 'id',
                    type: 'string'
                },
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
                    name: 'type_of_company',
                    type: 'string'
                },
                {
                    name: 'executive_type',
                    type: 'string'
                },
                {
                    name: 'inquiry_no',
                    type: 'string'
                },

                {
                    name: 'description',
                    type: 'string'
                },
                {
                    name: 'industry_type_id',
                    type: 'string'
                },

                {
                    name: 'company_name',
                    type: 'string'
                },
                /*{
                    name: 'dealer_id',
                    type: 'string'
                },*/
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
                    name: 'route',
                    type: 'string'
                },
                {
                    name: 'pincode',
                    type: 'string'
                },
                // {
                //     name: 'description',
                //     type: 'string'
                // },
                {
                    name: 'followup_reason_id',
                    type: 'string'
                },
                {
                    name: 'inquiry_date',
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
                // {
                //     name: 'assign_company',
                //     type: 'string'
                // },
                {
                    name: 'inquiry_lead_flag',
                    type: 'string'
                },
                {
                    name: 'followup_count',
                    type: 'string'
                },
                {
                    name: 'cancel_inq_remark',
                    type: 'string'
                },
                {
                    name: 'lost_reason',
                    type: 'string'
                },
                {
                    name: 'inquiry_type_color',
                    type: 'string'
                },
                {
                    name: 'inq_status',
                    type: 'string'
                },
                {
                    name: 'entry_flag',
                    type: 'string'
                },
                {
                    name: 'update_entry_flag',
                    type: 'string'
                },
                
                // {
                //     name: 'image_path',
                //     type: 'string'
                // },
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
                //alert(JSON.stringify(rowdata));
                var datafield = editedColumn;
                var mainvalue = rowdata[datafield];
                // synchronize with the server - send update command
                var data = {
                    "update": true,
                    "id": rowdata.id,
                    "field": datafield,
                    "inquiry_type": rowdata.inquiry_lead_flag,
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
               // alert(error);
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
                
                if(value=="IND-Inquiry" || value=="IND-Buyer Call")               
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

            var company_name = $('#jqxgrid').jqxGrid('getcellvalue', row, "company_name");
            // var company_name = $('#jqxgrid').jqxGrid('getcellvalue', row, "company_name");
            var mobile_number = $('#jqxgrid').jqxGrid('getcellvalue', row, "mobile_number");
            var state = $('#jqxgrid').jqxGrid('getcellvalue', row, "state");
            var city = $('#jqxgrid').jqxGrid('getcellvalue', row, "city");
            var route = $('#jqxgrid').jqxGrid('getcellvalue', row, "route");
            var inquiry_type_color = $('#jqxgrid').jqxGrid('getcellvalue', row, "inquiry_type_color");
            /*var followup_count = $('#jqxgrid').jqxGrid('getcellvalue', row, "followup_count");
            if(followup_count==0)
            {
                return ' common-cls no-quote normal-field ' + id + '_getval_' + columnfield + " change_color_" + id;
            } */

            if(company_name=="" || company_name==0 || mobile_number=="" || mobile_number==0 || state=="" || state==0 || city=="" || city==0)
            {
                return ' common-cls pending-customer-details normal-field ' + id + '_getval_' + columnfield + " change_color_" + id;
            }

             /*color code for inquiry*/
            if(inquiry_type_color=='-1')
            {
                return 'common-cls prospect-color-class normal-field ' + id + '_getval_' + columnfield + " change_color_" + id
            }
            if(inquiry_type_color=='0')
            {
                return 'common-cls inqiry-color-class normal-field ' + id + '_getval_' + columnfield + " change_color_" + id
            }
            if(inquiry_type_color=='1')
            {
                return 'common-cls lead-color-class normal-field ' + id + '_getval_' + columnfield + " change_color_" + id
            }
            /*color code for inquiry*/
        }
        var cellclassview = function (row, columnfield, value) {
            var id = $('#jqxgrid').jqxGrid('getcellvalue', row, "id");
            return ' common-cls disable classdisview overflow-view '+id+'_getval_'+columnfield + " change_color_" + id;
        }
        var coloredred = function (element) {
            $(element).parent().addClass('jqx-grid-column-header-blue');
        }

        // initialize jqxGrid
        //var height = ($(window).height() + 700);
        var height = ($(window).height() + 0);
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
                $("#route").select2();
                $("#c_type").select2();
                $("#company_type").select2();
                $("#industry_type").select2();
                $("#end_followup").select2();
                $("#type").select2();
                $("#assigned_to").select2();
                $("#status_id").select2();
                $("#source_id").select2();

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
                    columntype: 'button', 

                    cellclassname: cellclassview,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    editable: false,
                    filterable: false, 
                    sortable: false, 
                    rendered: coloredred,
                    enabletooltips : false,

                    cellsrenderer: function () {
                        return '⚙';
                    },
                    buttonclick: function (row,event) {
                        // open the popup window when the user clicks a button.
                        editrow = row;
                        var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', editrow);
                        // var offset = $("#jqxgrid").offset();

                        // $("#jqxgrid").jqxGrid('selectrow', event.args.rowindex);
                        var scrollTop = $(window).scrollTop();
                        var scrollLeft = $(window).scrollLeft();


                        $("#popupWindow").jqxWindow({ position: { x: parseInt(event.clientX) + 5 + scrollLeft, y: parseInt(event.clientY) + 5 + scrollTop } });


                        $(".edit-entry").unbind('click');
                        $(".delete-entry").unbind('click');
                        $(".generate-inquiry").unbind('click');
                        $(".generate-lead").unbind('click');
                        $(".quotation").unbind('click');
                        $(".timeline-view").unbind('click');
                        $(".cancel-prospect").unbind('click');
                        $(".cancel-inquiry").unbind('click');
                        $(".cancel-lead").unbind('click');
                        $(".assign_customer").unbind('click');
                        
                        
                        $(".edit-entry").on("click",function(e){
                            EditButtonClick(dataRecord.id);
                        });
                        $(".delete-entry").on("click",function(e){
                            DeleteButtonClick(dataRecord.id);
                        });
                        //  $(".generate-lead").on("click",function(e){
                        //     GenerateLead(dataRecord.id);
                        // });

                        if(dataRecord.inquiry_lead_flag=='-1')
                        {
                            $(".generate-inquiry").show();
                            $(".generate-lead").hide();
                            $(".quotation").hide();
                            $(".cancel-prospect").show();
                            $(".cancel-inquiry").hide();
                            $(".assign_customer").hide();
                            $(".cancel-lead").hide();
                            $(".generate-inquiry").on("click",function(e){
                                GenerateInquiry(dataRecord.id);
                            });

                            $(".cancel-prospect").on("click",function(e){
                                CancelInquiry(dataRecord.id);
                            });
                        }
                        else if(dataRecord.inquiry_lead_flag=='0')
                        {
                            $(".generate-inquiry").hide();
                            $(".generate-lead").show();
                            $(".quotation").hide();
                            $(".cancel-prospect").hide();
                            $(".cancel-inquiry").show();
                            $(".assign_customer").show();
                            $(".cancel-lead").hide();
                            $(".generate-lead").on("click",function(e){
                                GenerateLead(dataRecord.id);
                            });
                            $(".cancel-inquiry").on("click",function(e){
                                CancelInquiry(dataRecord.id);
                            });
                            $(".assign_customer").on("click",function(e){
                                Assigncustomer(dataRecord.id);
                            });
                        }
                        else
                        {
                            $(".generate-inquiry").hide();
                            $(".generate-lead").hide();
                            $(".quotation").show();
                            $(".cancel-prospect").hide();
                            $(".cancel-inquiry").hide();
                            $(".assign_customer").hide();
                            $(".cancel-lead").show();
                            $(".quotation").on("click",function(e){
                                QuotationButtonClick(dataRecord.id);
                            });
                            $(".cancel-lead").on("click",function(e){
                                CancelInquiry(dataRecord.id);
                            });
                        }

                        $(".timeline-view").on("click",function(e){
                            TimelineButtonClick(dataRecord.id);
                        });
                        

                        // get the clicked row's data and initialize the input fields.
                        /*var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', editrow);
                        $("#firstName").val(dataRecord.firstname);
                        $("#lastName").val(dataRecord.lastname);
                        $("#product").val(dataRecord.productname);
                        $("#quantity").jqxNumberInput({ decimal: dataRecord.quantity });
                        $("#price").jqxNumberInput({ decimal: dataRecord.price });*/

                        // show the popup window.
                        $("#popupWindow").jqxWindow('open');
                    }
                },
                // {
                //     text: ' ',
                //     datafield: 'action', 
                //     cellsAlign: 'left',
                //     width: 30,
                //     cellclassname: cellclassview,
                //     cellendedit: cellendedit,
                //     cellbeginedit:cellbeginedit,
                //     columntype: 'none',
                    
                //     editable: false,
                //     filterable: false, 
                //     sortable: false, 
                //     rendered: coloredred,
                //     enabletooltips : false,
                //     renderer: function (defaultText, alignment, height) {
                //          return '<div style="margin: 0px 0 0 3px;"> </div>';
                //     },
                //     cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {
                //         var status_data = rowdata.status;
                //         if(status_data!="Non Relavent Inquiry")
                //         {
                //             if(rowdata['inquiry_lead_flag']=='-1')
                //             {
                //                 var quotationhtml = '<li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="GenerateInquiry('+value+')" style="color:#797b00;background: none;outline: none;border: none;"> <i class="fa fa-file fa-lg fa-fw"> </i>Generate Inquiry</button> </li>';
                //             }
                //             else if(rowdata['inquiry_lead_flag']=='0')
                //             {
                //                var quotationhtml = '<li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="GenerateLead('+value+')" style="color:#797b00;background: none;outline: none;border: none;"> <i class="fa fa-file fa-lg fa-fw"> </i>Generate Lead</button> </li>';
                //             }
                //             else
                //             {
                //                 var quotationhtml = '<li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="QuotationButtonClick('+value+')" style="color:#797b00;background: none;outline: none;border: none;"> <i class="fa fa-file fa-lg fa-fw"> </i>Quotation</button> </li>'
                //             }

                //             /*var cancelhtml = '<li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="CancelButtonClick('+value+')" style="color:#b50000;background: none;outline: none;border: none;"> <i class="fa fa-file fa-lg fa-fw"> </i>Cancel</button> </li>';*/

                //             var followuphtml = '';
                //         }
                //         else
                //         {
                //             var quotationhtml = '';
                //             var cancelhtml = '';
                //             var followuphtml = '<li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="followupButtonClick('+value+')" style="color:#797b00;background: none;outline: none;border: none;"> <i class="fa fa-file fa-lg fa-fw"> </i>In Followup</button> </li>';
                //         }

                //         // return '<button type="button" aria-expanded="false" data-toggle="dropdown" class="view-guideline dropdown-toggle"  style="color:#808080;font-size: 20px;background: none;outline: none;border: none;">  <i class="fa fa-gear"></i> </button> <ul role="menu" class="dropdown-menu"> <li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="EditButtonClick('+value+')" style="color:#403500;background: none;outline: none;border: none;"> <i class="fa fa-pencil fa-lg fa-fw"> </i>Edit</button> </li><li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="DeleteButtonClick('+value+')" style="color:#b50000;background: none;outline: none;border: none;"> <i class="fa fa-trash fa-lg fa-fw"> </i>Delete</button> </li>'+quotationhtml+''+followuphtml+'</ul>';


                //          return '<button type="button" aria-expanded="false" data-toggle="dropdown" class="view-guideline dropdown-toggle"  style="color:#808080;font-size: 20px;background: none;outline: none;border: none;">  <i class="fa fa-gear"></i> </button> <ul role="menu" class="dropdown-menu"> <li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="EditButtonClick('+value+')" style="color:#403500;background: none;outline: none;border: none;"> <i class="fa fa-pencil fa-lg fa-fw"> </i>Edit</button> </li><li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="DeleteButtonClick('+value+')" style="color:#b50000;background: none;outline: none;border: none;"> <i class="fa fa-trash fa-lg fa-fw"> </i>Delete</button> </li>'+quotationhtml+''+followuphtml+'<li style="margin: 8px!important;"> <button type="button" class="view-guideline" onclick="TimelineButtonClick('+value+')" style="color:#403500;background: none;outline: none;border: none;"> <i class="fa fa-circle fa-lg fa-fw"> </i>Timeline View</button> </li></ul>';
                //     },
                //     columnGroup: "blank",
                // },
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
                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {
                        var status_data = rowdata.status;
                        if(status_data!="Non Relavent Inquiry")
                        {
                            return '<button type="button" class="view-guideline" onclick="ViewFollowUp('+value+')" style="color:#1d9407;font-size: 20px;background: none;outline: none;border: none;">\n\
                                    <i style="font-size: 20px!important;" class="fa fa-eye fa-lg fa-fw"> </i>\n\
                                </button>';
                        }    
                        else
                        {
                            return '';
                        }
                        
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
                    filteritems: status_array,
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
                    text: 'Inquiry Date',
                    datafield: 'inquiry_date' ,
                    cellsAlign: 'left',
                    width: 150,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "filter_date_group",
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
                // {
                //     text: 'Inquiry <br/>Assigned To Customer',
                //     datafield: 'assign_company' ,
                //     cellsAlign: 'left',
                //     width: 140,
                //     cellclassname: cellclassdis,
                //     cellendedit: cellendedit,
                //     cellbeginedit:cellbeginedit,

                //     editable: false,
                //     rendered: coloredred,

                //     columnGroup: "blank5",
                // },
                {
                    text: 'Type Of Company',
                    datafield: 'type_of_company' ,
                    cellsAlign: 'left',
                    width: 130,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "type_of_company",
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
                    text: 'Industry Type',
                    datafield: 'industry_type_id' ,
                    cellsAlign: 'left',
                    width: 130,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "industry",
                },
                {
                    text: 'Inquiry <br/>No.',
                    datafield: 'inquiry_no' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "blank1",
                },
                {
                    text: 'Description',
                    datafield: 'description' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    sortable: false,
                    filterable: false,
                    rendered: coloredred,

                    columnGroup: "blank1",
                },
                {
                    text: 'Firm <br/>Name',
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

                    cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties,rowdata) {


                        // return '<i class="fa fa-phone" style="margin-left:8px!important"></i>&nbsp;<a target="_blank" href="https://api.whatsapp.com/send?phone='+value+'&text=">'+value+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';

                        var country = rowdata.country;

                        if(country == "India")
                        {

                            if (value.indexOf('+91') > -1)
                            {

                                return '<i class="fa fa-phone" style="margin-left:8px!important"></i>&nbsp;<a target="_blank" href="https://api.whatsapp.com/send?phone='+value+'&text=">'+value+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
                            }
                            else
                            {

                                return '<i class="fa fa-phone" style="margin-left:8px!important"></i>&nbsp;<a target="_blank" href="https://api.whatsapp.com/send?phone=91'+value+'&text=">'+value+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
                                

                            }

                        }
                        else
                        {
                            
                            return '<i class="fa fa-phone" style="margin-left:8px!important"></i>&nbsp;<a target="_blank" href="https://api.whatsapp.com/send?phone='+value+'&text=">'+value+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';

                        }

                    },

                    columnGroup: "blank1",
                },
                {
                    text: 'Email Address',
                    datafield: 'email_address' ,
                    cellsAlign: 'left',
                    minwidth: 120,
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
                    text: 'Country',
                    datafield: 'country' ,
                    cellsAlign: 'left',
                    width: 100,
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
                    text: 'Route',
                    datafield: 'route' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "route_group",
                },
                {
                    text: 'Pincode',
                    datafield: 'pincode' ,
                    cellsAlign: 'left',
                    minwidth: 120,
                    width: "auto",
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,
                    columntype: 'none',

                    editable: false,
                    rendered: coloredred,

                    columnGroup: "pincode_group",
                },
                // {
                //     text: 'Description',
                //     datafield: 'description' ,
                //     cellsAlign: 'left',
                //     width: 100,
                //     cellclassname: cellclassdis,
                //     cellendedit: cellendedit,
                //     cellbeginedit:cellbeginedit,

                //     editable: false,
                //     sortable: false,
                //     filterable: false,
                //     rendered: coloredred,

                //     columnGroup: "blank2",
                // },
                {
                    text: 'End Followup <br/> Reason',
                    datafield: 'followup_reason_id' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    
                    rendered: coloredred,

                    columnGroup: "end_followup_group",
                },
                {
                    text: 'Lost <br/> Reason',
                    datafield: 'cancel_inq_remark' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    sortable: false,
                    filterable: false,
                    rendered: coloredred,

                    columnGroup: "blank2",
                },
                {
                    text: 'Quotation <br/> Lost Reason',
                    datafield: 'lost_reason' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    sortable: false,
                    filterable: false,
                    rendered: coloredred,

                    columnGroup: "blank2",
                },
                {
                    text: 'Inquiry Type',
                    datafield: 'inq_status' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    sortable: false,
                    filterable: false,
                    rendered: coloredred,

                    columnGroup: "blank2",
                },

                {
                    text: 'Entry Type',
                    datafield: 'entry_flag' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    sortable: false,
                    filterable: false,
                    rendered: coloredred,

                    columnGroup: "blank2",
                },

                {
                    text: 'Update Entry Type',
                    datafield: 'update_entry_flag' ,
                    cellsAlign: 'left',
                    width: 100,
                    cellclassname: cellclassdis,
                    cellendedit: cellendedit,
                    cellbeginedit:cellbeginedit,

                    editable: false,
                    sortable: false,
                    filterable: false,
                    rendered: coloredred,

                    columnGroup: "blank2",
                },
                
                // {
                //     text: 'images',
                //     datafield: 'image_path' ,
                //     cellsAlign: 'left',
                //     width: 50,
                //     cellclassname: cellclassview,
                //     cellendedit: cellendedit,
                //     cellbeginedit:cellbeginedit,
                //     columntype: 'image',
                    
                //     editable: false,
                //     filterable: false, 
                //     sortable: false, 
                //     rendered: coloredred,
                //     enabletooltips : false,
                //     renderer: function (defaultText, alignment, height) {
                //          return '<div style="margin: 0px 0 0 3px;">Image</div>';
                //     },
                //     cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {
                //         var status_data1 = rowdata;
                //         if(status_data1!="Non Relavent Inquiry")
                //         {
                //             return '<div class="popup-gallery"><a href="'+value+'" class="lightbox-cats" style="color:#1365f9;font-size: 20px;background: none;outline: none;border: none;"><i style="font-size: 20px!important;" class="fa fa-image fa-lg fa-fw"> </i></a></div>';
                //         }
                //     },
                //     columnGroup: "blank2",
                // },
            ],
            columnGroups:
            [
                { 
                    text: '<select class="form-control" id="c_type" name="c_type" style="width:100%!important;text-align:center;margin: 0!important;padding:0!important;height:100%!important"><option value="">Select Customer Type</option><?php $customer_type = $db->rp_getData("customer_type","*","isDelete=0"); if($customer_type){ while($customer_type_d = mysqli_fetch_assoc($customer_type)) { ?><option value="<?=$customer_type_d["id"]?>" <?=($_REQUEST["c_type"] == $customer_type_d["id"])?"selected":"";?>><?=$db->clean($customer_type_d["name"])?></option><?php }} ?></select>', 
                    name: "customer_type_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" id="company_type" name="company_type" style="width:100%!important;text-align:center;margin: 0!important;padding:0!important;height:100%!important"><option value="">Select Company Type</option><?php $company_type = $db->rp_getData("company_master","*","isDelete=0","",0); if($company_type){ while($company_type_d = mysqli_fetch_assoc($company_type)) { ?><option value="<?=$company_type_d["id"]?>" <?=($_REQUEST["company_type"] == $company_type_d["id"])?"selected":"";?>><?=$db->clean($company_type_d["name"])?></option><?php }} ?></select>', 
                    name: "type_of_company", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" id="industry_type" name="industry_type" style="width:100%!important;text-align:center;margin: 0!important;padding:0!important;height:100%!important"><option value="">Select Industry Type</option><?php $industry_type_r = $db->rp_getData("industry_type","*","isDelete=0"); if($industry_type_r){ while($industry_type_d = mysqli_fetch_assoc($industry_type_r)) { ?><option value="<?=$industry_type_d["id"]?>" <?=($_REQUEST["industry_type"] == $industry_type_d["id"])?"selected":"";?>><?=$db->clean($industry_type_d["name"])?></option><?php }} ?></select>', 
                    name: "industry", 
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
                    text: '<select class="form-control" name="city" id="city" onChange="filter_city(this.value)"><option value="">Select City</option></select>', 
                    name: "city_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" name="route" id="route"><option value="">Select Route</option></select>', 
                    name: "route_group", 
                    align: "center" 
                },
                { 
                    text: ' ', 
                    name: "pincode_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" id="end_followup" name="end_followup" style="width:100%!important;text-align:center;margin: 0!important;padding:0!important;height:100%!important"><option value="">Select Followup End Type</option><?php $followup_end_r = $db->rp_getData("followup_reason","*","isDelete=0"); if($followup_end_r){ while($followup_end_d = mysqli_fetch_assoc($followup_end_r)) { ?><option value="<?=$followup_end_d["id"]?>" <?=($_REQUEST["end_followup"] == $followup_end_d["id"])?"selected":"";?>><?=$db->clean($followup_end_d["name"])?></option><?php }} ?></select>', 
                    name: "end_followup_group", 
                    align: "center" 
                },
                { 
                    text: '<div class="input-group"><input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $_REQUEST['df']; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;"><span class="input-group-addon datetimerange-picker-btn"><i class="fa fa-calendar"></i></span><span class="input-group-btn"></span></div>', 
                    name: "filter_date_group", 
                    align: "center" 
                },
                { 
                    //onChange="getSalesExecutive(this.value);"
                    text: '<select class="form-control" name="type" id="type" ><option value="">Select Inquiry Taken By</option><?php $se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");if($se_r){ while($se_d=mysqli_fetch_assoc($se_r)){ ?><option value="<?php echo $se_d["id"];?>" <?=($_REQUEST["type"] == $se_d["id"])?"selected":"";?>><?php echo $db->clean($se_d["name"]); ?></option><?php } } ?></select>', 
                    name: "inquiry_taken_group", 
                    align: "center" 
                },
                { 
                    text: '<select class="form-control" name="assigned_to" id="assigned_to"><option value="">Select Inquiry Assigned By</option><?php $se_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1"); if($se_r){ while($se_d=mysqli_fetch_assoc($se_r)){ ?><option value="<?php echo $se_d["id"];?>" <?=($_REQUEST["assigned_to"] == $se_d["id"])?"selected":"";?>><?php echo $db->clean($se_d["name"]); ?></option><?php } } ?></select>', 
                    name: "inquiry_assigned_group", 
                    align: "center" 
                },
                // { 
                //     text: ' ', 
                //     name: "blank5", 
                //     align: "center" 
                // },
                {

                    // Note Dhaval  Remove for status_array (Interested,Hot,Warm,Wrong Call)
                    text: '<select class="form-control" id="status_id" name="status_id"> <option value="">Select Status</option> <option <?=($_REQUEST["status_id"]==0 && $_REQUEST["status_id"]!="")?"selected":""; ?> value="0">Generate</option><option <?=($_REQUEST["status_id"]==2 && $_REQUEST["status_id"]!="")?"selected":""; ?> value="2">Positive</option> <option <?=($_REQUEST["status_id"]==1)?"selected":""; ?> value="1">In Followup</option>  <option <?=($_REQUEST["status_id"]==4)?"selected":""; ?> value="4">Hot</option>  <option <?=($_REQUEST["status_id"]==5)?"selected":""; ?> value="5">Cold</option>  <option <?=($_REQUEST["status_id"]==6)?"selected":""; ?> value="6">Warm</option> <option <?=($_REQUEST["status_id"]==-2)?"selected":""; ?> value="-2">Cancel</option> <option <?=($_REQUEST["status_id"]==-1)?"selected":""; ?> value="-1">My Work</option> <option <?=($_REQUEST["status_id"]==3)?"selected":""; ?> value="3">Buy Later</option><option <?=($_REQUEST["status_id"]==11)?"selected":""; ?> value="11">Lost</option>select>', 
                    //text: '<select class="form-control" id="status_id" name="status_id"> <option value="">Select Status</option> <option <?=($_REQUEST["status_id"]==0 && $_REQUEST["status_id"]!="")?"selected":""; ?> value="0">Generate</option> <option <?=($_REQUEST["status_id"]==1)?"selected":""; ?> value="1">In Followup</option> <option <?=($_REQUEST["status_id"]==4)?"selected":""; ?> value="4">Hot</option> <option <?=($_REQUEST["status_id"]==5)?"selected":""; ?> value="5">Cold</option> <option <?=($_REQUEST["status_id"]==6)?"selected":""; ?> value="6">Warm</option> <option <?=($_REQUEST["status_id"]==-2)?"selected":""; ?> value="-2">Non Relavent</option> <option <?=($_REQUEST["status_id"]==-1)?"selected":""; ?> value="-1">Not Interested</option> <option <?=($_REQUEST["status_id"]==3)?"selected":""; ?> value="3">Buy Later</option><option <?=($_REQUEST["status_id"]==11)?"selected":""; ?> value="11">Lost</option>select>', 
                    //05-08-2021 by milan old
                    // text: '<select class="form-control" id="status_id" name="status_id"> <option value="">Select Status</option> <option <?=($_REQUEST["status_id"]==0 && $_REQUEST["status_id"]!="")?"selected":""; ?> value="0">Generate</option> <option <?=($_REQUEST["status_id"]==1)?"selected":""; ?> value="1">In Followup</option>  <option <?=($_REQUEST["status_id"]==-1)?"selected":""; ?> value="-1">Not Interested</option> <option <?=($_REQUEST["status_id"]==3)?"selected":""; ?> value="3">Working</option><option <?=($_REQUEST["status_id"]==-2)?"selected":""; ?> value="-2">Non Relavent Inquiry</option><option <?=($_REQUEST["status_id"]==5)?"selected":""; ?> value="5">Cold</option><option <?=($_REQUEST["status_id"]==8)?"selected":""; ?> value="8">Will Interested</option><option <?=($_REQUEST["status_id"]==9)?"selected":""; ?> value="9">Not Working</option><option <?=($_REQUEST["status_id"]==10)?"selected":""; ?> value="10">Not Doing Business</option><option <?=($_REQUEST["status_id"]==11)?"selected":""; ?> value="11">Lost</option></select>', 
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
                    name: "blank2", 
                    align: "center" 
                },
                // update code - sagar//
                { 
                    /*onChange="getSalesExecutive(this.value);"*/
                    text: '<select class="form-control" name="source_id" id="source_id" ><option value="">Select Source Medium By</option><?php $source_d=$db->rp_getData("source_of_inquiry","*"," isDelete=0 "); if($source_d){ while($source_r=mysqli_fetch_assoc($source_d)){ ?><option value="<?php echo $source_r["id"];?>" <?=($_REQUEST["source_id"] == $source_r["id"])?"selected":"";?>><?php echo $db->clean($source_r["name"]); ?></option><?php } } ?></select>', 
                    name: "blank3", 
                    align: "center" 
                },
                // update code - sagar//

            ]
        });
    });

// initialize the popup window and buttons.
$("#popupWindow").jqxWindow({
    width: 150, 
    height: 130, 
    resizable: true,  
    isModal: true, 
    autoOpen: false, 
    modalOpacity: 0.01,
    animationType: 'fade',
});




$("#jqxgrid").on('rowclick', function (event) {
    if (event.args.rightclick) {
        // console.log(event.args.columnindex);
        $("#jqxgrid").jqxGrid('selectrow', event.args.rowindex);
        // console.log(event.args);
        // $('#jqxgrid').jqxGrid('clearselection');
        // $('#jqxgrid').jqxGrid('selectcell', event.args.rowindex, 'action');
        
        var dataRecord = $("#jqxgrid").jqxGrid('getrowdata', event.args.rowindex);

        if(dataRecord.description!="")
        {
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();
            contextMenu.jqxMenu('open', parseInt(event.args.originalEvent.clientX) + 5 + scrollLeft, parseInt(event.args.originalEvent.clientY) + 5 + scrollTop);


            $(".description-class").html("");
            // console.log(dataRecord);
            $(".description-class").html(dataRecord.description);
        }
        return false;
    }
});


var contextMenu = $("#Menu").jqxMenu({ width: 400, autoOpenPopup: false, mode: 'popup'});

$("#jqxgrid").on('contextmenu', function () {
    return false;
});

// function Viewimage(value){
//     $(".modal-img").prop("src",src);  
// }

// $(function(){
//   $("#image img").on("click",function(){
//      var src = $(this).attr("src");
//      $(".modal-img").prop("src",src);
//   })
// })

</script>
<!-- <span class="totalrowspan"></span> -->
<div id='jqxWidget'>
    <div id="jqxgrid"></div>
</div>

<div class="modal fade " id="imagemodal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <img class="modal-img" />
        </div>
    </div>
</div>


<div id="popupWindow">
    <div>Settings</div>
    <div style="overflow: hidden;text-align: left;">
        <table>
            <?php
            if($rights['update_flag']==1)
            { 
            ?>
            <tr>
                <td class="text-center">
                    <button type="button" class="edit-entry" style="color:#403500;background: none;outline: none;border: none;">
                        <i class="fa fa-pencil fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="edit-entry" style="color:#403500;background: none;outline: none;border: none;"> Edit</button>
                </td>
            </tr>
            <?php
            }
            if($rights['delete_flag']==1)
            { 
            ?>
            <tr>
                <td class="text-center">
                    <button type="button" class="delete-entry" style="color:#b50000;background: none;outline: none;border: none;">
                        <i class="fa fa-trash fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="delete-entry" style="color:#b50000;background: none;outline: none;border: none;"> Delete</button>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <td class="text-center">
                    <button type="button" class="generate-inquiry" style="color:#797b00;background: none;outline: none;border: none;">
                        <i class="fa fa-file fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="generate-inquiry" style="color:#797b00;background: none;outline: none;border: none;font-size: 12px!important;"> Generate Inquiry</button>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <button type="button" class="generate-lead" style="color:#797b00;background: none;outline: none;border: none;">
                        <i class="fa fa-file fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="generate-lead" style="color:#797b00;background: none;outline: none;border: none;font-size: 12px!important;"> Generate Lead</button>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <button type="button" class="quotation" style="color:#797b00;background: none;outline: none;border: none;">
                        <i class="fa fa-file fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="quotation" style="color:#797b00;background: none;outline: none;border: none;font-size: 12px!important;"> Quotation</button>
                </td>
            </tr>

            <!-- <tr>
                <td class="text-center">
                    <button type="button" class="timeline-view" style="color:#403500;background: none;outline: none;border: none;">
                        <i class="fa fa-circle fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="timeline-view" style="color:#403500;background: none;outline: none;border: none;font-size: 12px!important;"> Timeline View</button>
                </td>
            </tr> -->

            <tr>
                <td class="text-center">
                    <button type="button" class="cancel-prospect" style="color:#403500;background: none;outline: none;border: none;">
                        <i class="fa fa-circle fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="cancel-prospect" style="color:#403500;background: none;outline: none;border: none;font-size: 12px!important;"> Lost Raw Data</button>
                </td>
            </tr>

            <tr>
                <td class="text-center">
                    <button type="button" class="cancel-inquiry" style="color:#403500;background: none;outline: none;border: none;">
                        <i class="fa fa-circle fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="cancel-inquiry" style="color:#403500;background: none;outline: none;border: none;font-size: 12px!important;"> Lost Inquiry</button>
                </td>
            </tr>
            <!-- <tr>
                <td class="text-center">
                    <button type="button" class="assign_customer" style="color:#403500;background: none;outline: none;border: none;">
                        <i class="fa fa-user fa-lg fa-fw"></i>
                    </button>
                </td>
                <td>
                    <button type="button" class="assign_customer" style="color:#403500;background: none;outline: none;border: none;font-size: 12px!important;"> Assign Customer </button>
                </td>
            </tr> -->

            <tr>
                <td class="text-center">
                    <button type="button" class="cancel-lead" style="color:#403500;background: none;outline: none;border: none;">
                        <i class="fa fa-circle fa-lg fa-fw"> </i>
                    </button>
                </td>
                <td>
                    <button type="button" class="cancel-lead" style="color:#403500;background: none;outline: none;border: none;font-size: 12px!important;"> Lost Lead</button>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="tbl" style="display: none;">
    
</div>


<div id='Menu'>
    <div class="description-class" style="padding: 10px;background: #e0efcd;"></div>
</div>


<?php require_once "disconnect.php"; ?>

<!-- <script src="js/jquery.magnific-popup.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js" integrity="sha512-IsNh5E3eYy3tr/JiX2Yx4vsCujtkhwl7SLqgnwLNgf04Hrt9BT9SXlLlZlWx+OK4ndzAoALhsMNcCmkggjZB1w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- <script>
       $(document).ready(function() {
          $('.lightbox-cats').magnificPopup({
             type: 'image',
             gallery:{enabled:true}
             // removalDelay: 300,       // Delay in milliseconds before popup is removed
             // mainClass: 'mfp-fade'   // Class that is added to popup wrapper and background

             // other options
          });
       });

    </script>
 -->
