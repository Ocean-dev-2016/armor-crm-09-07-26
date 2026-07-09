<?php
$page_id = 668;
$page_slug = 'consultant_apptoval_process_report';
$ctable     = "sales_vs_consultant_approval_process";
$ctable1     = "sales_vs_consultant_approval_process";
$main_page     = $ctable;
$page         = $ctable;
$page_title = $ctable1 . " Report";
$page_hierarchy = array(array("link" => "", "title" => "Report"), array("link" => $ctable . "_report.php", "title" => $page_title));
include("connect.php");
  $FromDate="";
   $ToDate="";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
    <?php include("include_css.php"); ?>

    <!-- zoom css -->
    <link rel="stylesheet" type="text/css" href="zoom-image/css/style.css" />
    <link rel="stylesheet" type="text/css" href="zoom-image/cloud-zoom/cloud-zoom.css" />
    <link rel="stylesheet" type="text/css" href="zoom-image/fancybox/jquery.fancybox-1.3.4.css" />
    <script src="zoom-image/js/cufon-yui.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="css/fSelect.css" />
    <!-- zoom css -->

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.css" /> -->

    <link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" href="<?= ADMINSITEURL ?>css/lightbox.css" />
    <style type="text/css">
        .btn-sm,
        .btn-xs {
            line-height: 2;
        }
    </style>
</head>

<body class="page-md">
    <?php include("header.php"); ?>
    <div class="page-container">

        <div class="page-head bg-grey">
            <div class="container">
                <div class="page-title">
                    <h1><a href="<?php echo "dashboard.php"; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
                </div>
            </div>
        </div>

        <div class="page-content">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <?php $db->printErrorMessage(); ?>
                        <?php $db->printSuccessMessage(); ?>
                    </div>
                    <div class="col-xl-12">
                        <!-- BEGIN Portlet PORTLET-->
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
                                <div class="slimScrollDiv">
                                    <div class="row" style="height: 85px;">
                                        <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
                                            <label>Select Sales Person</label>
                                            <div class="form-group" role="form">
                                                <select class="form-control" name="sales_executive" id="sales_executive">
                                                    <option value="">--- Select Sales Person ---</option>

                                                    <?php
                                                    $whereCustom = " isDelete=0 AND isActive=1 ";
                                                    if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {

                                                        if ($rights['personal_flag'] == 1) {
                                                            $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
                                                            $whereCustom .= " AND id='" . $check_id . "' ";
                                                        } else {
                                                            if ($rights['chain_vise_flag'] == 1) {

                                                                $check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];

                                                                $get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
                                                                if ($get_sales_type == "sales_manager") {
                                                                    $sales_executive_type = "Regional Sales Manager";
                                                                    $key = "sm_id";
                                                                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                                                                } else if ($get_sales_type == "area_sales_manager") {
                                                                    $sales_executive_type = "National Sales Manager"; //Business Development Manager
                                                                    $key = "asm_id";
                                                                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                                                                } else if ($get_sales_type == "sales_officer") {
                                                                    $sales_executive_type = "Area Sales Manager"; //Area Sales Manager
                                                                    $key = "so_id";
                                                                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                                                                } else if ($get_sales_type == "sales_executive") {
                                                                    $sales_executive_type = "Sales Officer";
                                                                    $key = "se_id";
                                                                    $WhereCondition .= ' ' . $key . '=' . $check_id;
                                                                } else {
                                                                    $WhereCondition .= ' type = "service_engineer"';
                                                                }

                                                                $data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);

                                                                $SALEID1 = array();
                                                                if ($data) {
                                                                    while ($data_d = mysqli_fetch_assoc($data)) {
                                                                        $SALEID1[] = $data_d['id'];
                                                                    }
                                                                }
                                                                if (!empty($SALEID1)) {
                                                                    $SALEID1 = implode(",", $SALEID1);

                                                                    $whereCustom .= "  AND id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
                                                                } else {
                                                                    $whereCustom .= "  AND id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
                                                                }
                                                            } else {
                                                                $whereCustom = "";
                                                            }
                                                        }
                                                    }
                                                    $sales_executive_r = $db->rp_getData('sales_executive', "*", $whereCustom, "", 0);
                                                    while ($sales_executive_d = mysqli_fetch_assoc($sales_executive_r)) {
                                                    ?>
                                                        <option <?= ($sales_executive_d['id'] == $_SESSION[SITE_SESS . 'REFERANCE_ID']) ? "selected" : ""; ?> <?php echo ($sales_executive_d == $sales_executive_d['id']) ? "selected" : ""; ?> value="<?php echo $sales_executive_d['id'] ?>"><?php echo $sales_executive_d['name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                          <!-- <div class="col-md-4  col-xs-4  col-sm-4" style="margin-top:10px">
                                          <div class="form-inline" role="form">
                                             <div class="form-group">
                                                <label>Filter By Purchase Date : &nbsp;</label></br>
                                                <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
                                             </div>
                                             <div class="form-group">
                                                <label>&nbsp;&nbsp;</label></br>
                                                <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
                                             </div>
                                             <div class="form-group">
                                                <label>&nbsp;&nbsp;</label></br>
                                                <input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
                                             </div>
                                          </div>
                                       </div> -->
                                        <div class="col-md-4 col-xs-4 col-sm-4 pull-right" style="margin-top:10px;">
                                            <div class="form-inline" role="form">
                                                <label>Search By Name / Phone :</label>
                                                <form class="form-inline" role="form" onSubmit="return searchByName();">
                                                    <div class="form-group">

                                                        <input type="text" style="width:188px!important" placeholder="Search By Name / Phone :  " class="form-control input-small" name="searchName" id="searchName" value="" />
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
                                                                <!-- <li>
																<a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
															</li> -->
                                                                <?php
                                                                if ($rights['print_flag'] == 1  || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
                                                                ?>
                                                                    <li>
                                                                        <!-- <a name="print" onClick="genexpensePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a> -->
                                                                        <a name="print" onClick="genSalesPlanPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
                                                                    </li>
                                                                    <!--  <?php
                                                                        }
                                                                        if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
                                                                            ?>
                                                                    <li>
                                                                        <a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
                                                                    </li>
                                                                <?php
                                                                        }
                                                                ?> -->
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END Portlet PORTLET-->
                        </div>
                        <!-- <div class="col-md-12"> -->
                        <div class="portlet light">
                            <div class="portlet-body">
                                <div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;"> </div>
                                <div id="results"></div>
                            </div>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>

        <?php include("include_js.php"); ?>
        <script src="<?= ADMINSITEURL ?>js/lightbox.js"></script>
        <script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
        <script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
        <script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="js/fSelect.js"></script>

        <script type="text/javascript">
            // $("#sales_executive").Select2();
        </script>
        <script type="text/javascript">
            var searchName = "";
            var state = "";
            var city = "";
            var df1 = "";
            var sales_executive = "";
            var customer_id = "";
                $('#ToDate').datepicker({  datepicker: true, autoclose: true });
               $('#FromDate').datepicker({  datepicker: true, autoclose: true });
               var ToDate="";
               var FromDate="";
            var data_url = "consultant_approval_process_get_ajax.php";

            function searchByName() {
                displayRecords(100, 1);
                return false;
            }

            function clearSearchByName() {
               
                searchName = '';
                sales_executive = '';
                FromDate = "";
                ToDate = "";
                $("#searchName").val("");

                $("#sales_executive").select2('destroy');
                $("#sales_executive").val();
                $("#sales_executive").select2();
                 
                  $("#ToDate").val("");
                  $("#FromDate").val("");

                displayRecords(100, 1);
            }

            function loadDataTable() {
                $('#datatable_1').dataTable({
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
                    "bAutoWidth": false,
                });
            }
         function getByDate() 
               {
                  if(FromDate<=ToDate)
                  {
                     ToDate = $("#ToDate").val();
                     FromDate = $("#FromDate").val();
                     displayRecords(100,1);
                  }
                  else
                  {
                     alert("From Date Should Be Less Than To Date");
                  }
               }
            function displayRecords(numRecords) {
                var sales_executive = $("#sales_executive").val();
                var searchName = $("#searchName").val();
                searchName = encodeURIComponent(searchName.trim());

                $("#results").html("");
                $("#results").load(data_url + "?show=" + numRecords + "&sales_executive=" + sales_executive + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&searchName=" + searchName, function() {
                    loadDataTable();
                }); //load initial records
            }

            $(document).ready(function() {
                displayRecords(100, 1);
            });

         function del_conf(id) {
    var r = confirm("Are you sure you want to delete?");
    if (r) {
        $.ajax({
            type: "POST",
            url: "consultant_common_ajax.php",
            data: {
                id: id,
                mode: "delete"
            },
            success: function(response) {
                response = $.parseJSON(response);
                if (response.ack == 1) {
                    toastr.success("Deleted Successfully!", "Success");
                    displayRecords(); 
                } else {
                    toastr.error("Delete Failed!", "Error");
                }
            },
            error: function() {
                toastr.error("Server Error!", "Error");
            }
        });
    }
}


         function status_conf(id){
            var r = confirm("Are You Sure Update The Status");
            if (r) {
                $.ajax({
                    type:'POST',
                   url: "consultant_common_ajax.php",
                    data:{
                      id:id,
                      mode: "status"
                    },
                    success: function (response) {
                    response = $.parseJSON(response);
                    if(response.ack == 1){
                        toastr.success("Status Update Successfully")
                    }else{
                        toastr.error("Update Failed",)
                    }
                    },
                    error: function () {
                        toastr.error("Server Error!","Error")
                    }
                });
            }
         }

         
        </script>

        <script type="text/javascript">
            // function genReport1(cid) {
            //     var rc = encodeURIComponent($("#print_info").html());
            //     $.ajax({
            //         type: "POST",
            //         url: "attandanceee_genreport_ajax.php",
            //         data: '&rc=' + rc,
            //         beforeSend: function() {
            //             $('.preloader').fadeIn('slow');
            //         },
            //         success: function(result) { //alert(result);
            //             setTimeout(function() {
            //                 $('.preloader').fadeOut('slow');
            //                 window.location.href = result;
            //             }, 1500);
            //         }
            //     });
            // }

            function genReport() {
                var query = encodeURIComponent($(".tag_search_input").val());
                var searchName = encodeURIComponent($('#searchName').val());
                var sales_executive = encodeURIComponent($('#sales_executive').val());
                var df1 = $("#material_request_filter_input").val();
                //var searchName = $("#searchName").val();
                //var sales_executive = $("#sales_executive").val();
                //alert(label_id);

                $.ajax({
                    type: "POST",
                    url: "attendance_report_genreport_excel.php",
                    data: {
                        searchName: searchName,
                        sales_executive: sales_executive,
                        df1: df1,
                        ToDate:ToDate,
                        FromDate:FromDate,
                    },
                    beforeSend: function() {
                        $("#loading-modal").modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    },
                    success: function(result) {
                        setTimeout(function() {
                            $("#loading-modal").modal('hide');
                            window.open(result, '_blank');
                        }, 1000);
                    }
                });
            }

            function genSalesPlanPrint() {
                var searchName = $("#searchName").val();
                searchName = encodeURIComponent(searchName.trim());
                var sales_executive = String($("#sales_executive").val());
                // type = $("#type").val();
                var myWindow = window.open('consultant_approval_process_print.php?searchName=' + searchName + '&sales_executive=' + sales_executive +'&ToDate='+ToDate+'&FromDate='+FromDate, '', 'width=700,height=800');
                myWindow.print();
            }
        </script>
        <?php include("footer.php"); ?>

</body>

</html>