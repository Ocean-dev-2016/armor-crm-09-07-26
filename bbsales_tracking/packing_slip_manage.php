<?php
$page_id=612;$page_slug='packing_slip';
$ctable     = "packing_slip";
$page_title     = "Packing Slip";
$page         = $ctable . "_manage.php";
$page_hierarchy = array(array("link" => "", "title" => "Sales & Marketing"), array("link" => $page, "title" => $page_title));
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
        <?php include("include_css.php"); ?>
        <link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
    </head>

    <body class="page-md">
        <?php include("header.php"); ?>
        <div class="page-container">

            <div class="page-head bg-grey">
                <div class="container">
                    <div class="page-title">
                        <h1><a href="<?php echo "dashboard.php"; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12"><br />
                            <div class="portlet box blue">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-filter"></i>Filters
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>

                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="slimScrollDiv" style="position: relative; width: auto; height: auto;">
                                        <div class="row">
                                            <div class="col-md-5 col-xs-5 col-sm-5">
                                            <?php
                                                //echo $db->getAddButton($ctable);
                                            ?>  
                                            </div>
                                            <div class="col-md-7 col-xs-7 col-sm-7" style="margin-top:10px">
                                                <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                                    <div class="form-group"> 
                                                        <input type="text" class="form-control input-medium" style="width: 450px!important" name="searchName" id="searchName" value="" placeholder="Search By Packing Slip No/Dispatch No : " />
                                                    </div>
                                                    <div class="form-group">
                                                        <input class="btn btn-danger btn-sm" type="submit" value="search">
                                                    </div>
                                                    <div class="form-group">
                                                        <input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="btn-group">

                                                            <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                                                                <i class="fa fa-gear"></i>
                                                            </button>
                                                            
                                                            <ul role="menu" class="dropdown-menu dropdown-menu-right pull-right">
                                                                <?php
                                                                if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                                                                { 
                                                                    ?>
                                                                    <li>
                                                                        <a name="print" onClick="genPackingSlipPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
                                                                    </li>
                                                                    <?php
                                                                }
                                                                if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
                                                                { 
                                                                    ?>
                                                                    <li>
                                                                        <a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
                                                                    </li>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <?php $db->printSuccessMessage(); ?>
                            </div>

                            <div class="portlet light">
                                
                                <div class="portlet-body">
                                    <div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;"> </div>
                                    <div id="results"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php include("footer.php"); ?>
        <?php include("include_js.php"); ?>
        <script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
        <script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
        <script type="text/javascript">
            var searchName = "";
            var status = "";
            var df1 = "";
            var data_url = "<?php echo $ctable ?>_get_ajax.php";
            
            function searchByName() {
                searchName = $("#searchName").val();
                // searchName = encodeURIComponent(searchName);
                df1=$("#material_request_filter_input").val();
                df1 = encodeURI(df1);
                status = $("#status").val();
                displayRecords(100,1);
                return false;
            }

            function clearSearchByName() {
                searchName = "";
                df1 = "";
                status = "";
                $("#searchName").val("");
                $("#material_request_filter_input").val("");
                $("#status").val("");
                displayRecords(100,1);
            }
            
            function loadDataTable() {
                $('#datatable_1').dataTable({
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
                    "bAutoWidth": false,
                    
                    "order": [[1,'asc']], /* default order is index 1 */
                    'columnDefs': [ {
                        'targets': [0], /* column index */
                        'orderable': false, /* true or false */
                    }],

                    "oLanguage": {
                        "sEmptyTable": "Sorry No Data Available!!"
                    }
                });
            }

            function displayRecords(numRecords) {
                var searchName = $("#searchName").val();
                // searchName = encodeURIComponent(searchName.trim());
                // var df1 = $("#material_request_filter_input").val();
                var status = $("#status").val();
                $("#results").html("");
                $('.preloader').fadeIn('slow');
                $("#results" ).html("");
                $("#results").load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&df=" + df1,function(){
                    // $("#loading-modal").modal('hide');
                    $('.preloader').fadeOut('slow');
                    loadDataTable();
                }); 

                // $("#results").load(
                //     data_url,
                //     {searchName:searchName,df1:df1,status:status}, 
                //     function(data, status, jqXGR) {
                //         loadDataTable();
                //     }
                // ); //load initial records

                $("#results").on( "click", ".paging_simple_numbers a", function (e){
                    e.preventDefault();
                    var numRecords  = $("#numRecords").val();
                    $(".loading-div").show(); //show loading element
                    var page = $(this).attr("data-page"); //get page number from link
                    $("#results").load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&df=" + df1,{"page":page},function(){
                            // $("#loading-modal").modal('hide');
                            $(".loading-div").hide();
                            loadDataTable();
                        }); 
                });
                $("#results").on( "change", "#numRecords", function (e){
                    e.preventDefault();
                    var numRecords  = $("#numRecords").val();
                    $(".loading-div").show(); //show loading element
                    var page = $(this).attr("data-page"); //get page number from link
                    $("#results").load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&df=" + df1,{"page":page},function(){
                            // $("#loading-modal").modal('hide');
                            $('.preloader').fadeOut('slow');
                            $(".loading-div").hide();
                        }); 
                });
            }

            function del_conf(id) {
                var cnfrm = confirm("Are you sure you want to delete?");
                if (cnfrm) {
                    window.location.href = '<?php echo $ctable; ?>_crud_new.php?mode=delete&id=' + id;
                }
            }
            function genPackingSlipPrint()
            {
                var searchName  = $("#searchName").val();
                searchName      = encodeURIComponent(searchName.trim());
                var type        = $("#type").val();
                // var ToDate       = $("#ToDate").val();
                // var FromDate = $("#FromDate").val();
                var status      = $("#status").val();
                var sales_id    = $("#sales_id").val();
                var company_name    = $("#company_name").val();
                var df1     = $("#material_request_filter_input").val();
                // alert(type);
                var myWindow = window.open('print_packingslip_report.php?searchName='+searchName+ "&type=" + type + "&status=" + status + "&sales_id=" + sales_id + "&company_name=" + company_name + "&df1=" + df1,'','width=700,height=800');
                myWindow.print();
                // setTimeout(function () 
                // {
                //  myWindow.print();
                //  var ival = setInterval(function() 
                //  {
                //      myWindow.close();
                //      clearInterval(ival);
                //  }, 200);
                // }, 500);
            }
            function genReport()
            {
                var searchName = $("#searchName").val();
                searchName     = encodeURIComponent(searchName.trim());
                // ToDate = $("#ToDate").val();
                // FromDate =   $("#FromDate").val();
                df1 =   $("#material_request_filter_input").val();
                status = $("#status").val();
                type = $("#type").val();
                sales_id = $("#sales_id").val();
                company_name = $("#company_name").val();
                qid = $("#qid").val();

                $.ajax({
                    method: "POST",
                    url: "packingslip_info_genReport_ajax.php",
                    data: 'searchName=' + searchName + '&df1=' + df1 + '&status=' + status + '&type=' + type + '&sales_id=' + sales_id + '&company_name=' + company_name + '&qid=' + qid,
                    dataType : 'json',
                    beforeSend: function() {
                        // $("#loading-modal").modal('show');
                        $('.preloader').fadeIn('slow');
                    },
                    success: function(result){
                        // $("#loading-modal").modal('hide');
                        $('.preloader').fadeOut('slow');
                        window.location.href="<?=SITEURL?>"+result.file_path;
                    },
              //    error:function(result){
              //        var result = $.parseJSON(result);
              //        alert(JSON.stringify(result))
              //        alert(result.file_path);
                    //  window.location.href="<?=SITEURL?>"+result.file_path;
                    // }
                });
            }
            $(document).ready(function() {
                displayRecords(100,1);
            });

            function changeDisplayRowCount(numRecords) {
                displayRecords(numRecords, 1);
                // displayRecords_outlets(numRecords,1);
            }
             
        </script>
    </body>
</html>