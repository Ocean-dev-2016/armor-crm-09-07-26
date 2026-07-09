<?php
$page_id=569;$page_slug='dispatch_pages';
$ctable 	= "dispatch_detail";
$ctable1 	= "Orders";
$main_page 	= $ctable;
$page 		= "dispatch_manage";
$page_title = "Dispatch Order";
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"dispatch_manage.php","title"=>"Dispatch Order"));
include("connect.php");
require_once("../include/class.lr.php");
$objLRDetail= new LRDetail();


if(isset($_REQUEST['submit'])){
	$detail['dispatch_id']   	= $db->clean($_REQUEST['dispatch_id']);
	$detail['image_path']   	= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']   = $db->clean($_REQUEST['old_image_path']);
	$detail['isDelete']		= 0;

	if(isset($_REQUEST['dispatch_id']) && $_REQUEST['dispatch_id']!="")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		// print_r($_FILES);exit;
		$reply=$objLRDetail->InsertLR($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("dispatch_manage.php?msg=inserted");
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<!-- <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/> -->
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>

</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "orders_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
				<div class="col-xl-12 ">				
					<!-- BEGIN Portlet PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-filter"></i>Filters </div>
							 <div class="tools">
								<a href="javascript:;" class="collapse" data-original-title="" title=""> </a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
								<div class="row filter_list">
										<?php
            				if($rights['insert_flag']==1) 
            				{
            				?>
								<!-- <div class="col-md-5 col-xs-5 col-sm-5">
               						<a class="btn sbold blue-ebonyclay" href='dispatch_crud.php?mode=add'> Add New<i class="fa fa-plus"></i></a>
								</div> -->
							<?php 
							}
							?>

									  <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
	                                <div class="form-inline" role="form">
	                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
	                                       	<div class="form-group">

	                                          <input type="text" style="width: 450px!important" placeholder="Search By  Dispatch No/Order No :  " class="form-control input-large" name="searchName" id="searchName" value="" />

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
																			if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																			{ 
																				?>
														<li>
															<a name="print" onClick="genComplainPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
								<!-- <div class="row filter_list">
									<div class="col-md-2 col-xs-2 col-sm-2  pull-right" style="margin-top:10px">
										<button type="button" class="btn green-haze excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<button type="button" class="btn print" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genComplainPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
									</div>
								</div> -->
							</div>
						</div>
					</div>
					<!-- END Portlet PORTLET-->
				</div>
				<div class="row">
				<div class="col-md-12">
									
				</div>
				<div class="col-sm-12">
					<div class="portlet light">
					
						<!-- <div class="btn-group">
				
							<a class="btn sbold blue-ebonyclay" href='dispatch_crud.php?mode=add'> Add New
							<i class="fa fa-plus"></i>
							</a>
						</div> -->
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results"></div>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="myDispatchModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Dispatch Information </div>
					<div class="tools">

						<a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>

		</div>
	</div>
</div>
<div id="DispatchPaymentModal" class="modal fade ">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue" style="width:730px;">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Dispatch | Payment Information </div>
					<div class="tools">

						<a href="javascript:;" id="payment_requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>

		</div>
	</div>
</div>
<!-- lr attachment modal start -->
<div id="lrattachment" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View LR Information </div>
					<div class="tools">

						<a href="javascript:;" id="lr_requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>

		</div>
	</div>
</div>
<!-- lr attachment modal end -->

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<!-- <script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script> -->
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
        <script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var searchName="";
var data_url = "dispatch_get_ajax.php";
// $('#ToDate').datepicker({  datepicker: true, autoclose: true });
// $('#FromDate').datepicker({  datepicker: true, autoclose: true });
// var ToDate="";
// var FromDate="";
var status="<?= $_REQUEST['status']?>";
var type="";
var sales_id="<?= $_REQUEST['sales_id'] ?>";
var company_name="";
var df1="";
var dispatch_month="<?= $_REQUEST['dispatch_month']?>";
var dispatch_year="<?= $_REQUEST['dispatch_year']?>";
var customer_id="<?=$_REQUEST['customer_id']?>";
var todate="<?=$_REQUEST['todate'] ?>";
var fromdate="<?=$_REQUEST['fromdate'] ?>";
$('#myDispatchModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","dispatch_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
$('#DispatchPaymentModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#payment_requesting_ajax").attr("data-url","payment_dispatch_get_ajax.php?id="+requesting_id);
	$("#payment_requesting_ajax").click();
})

$('#lrattachment').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#lr_requesting_ajax").attr("data-url","lr_detail_ajx.php?id="+requesting_id);
	$("#lr_requesting_ajax").click();
})

function searchByName(){
	searchName = $("#searchName").val();
	// alert(searchName); 
	sales_id = $("#sales_id").val();
	status = $("#status").val();
	company_name = $("#company_name").val();
	df1 = $("#material_request_filter_input").val();
	displayRecords(500,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	// ToDate = "";
	// FromDate = "";
	type = "";
	status = "";
	sales_id = "";
	company_name = "";
	df1 = "";
	$("#searchName").val("");
	// $("#ToDate").val("");
	// $("#FromDate").val("");
	$("#type").select2("val","");
	$("#status").select2("val","");
	$("#sales_id").val("");
	$("#company_name").val("");
	$("#material_request_filter_input").val("");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});

// function getByDate() {
// 	if($("#FromDate").val() != '' && $("#ToDate").val() != '' ){
// 		ToDate = $("#ToDate").val();
// 		FromDate = $("#FromDate").val();
// 		displayRecords(500,1);
// 	}
// 	else
// 	{
// 		alert("Please Select Date");
// 	}
// }

function getType(tid){
	type=tid;
	displayRecords(500,1);
}
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		// "aoColumns": [
		// 	  { "sWidth": "5%" }, 
		// 	  { "sWidth": "5%" },
		// 	  { "sWidth": "3%" },
		// 	  { "sWidth": "5%" },
		// 	  { "sWidth": "5%" },
		// 	  { "sWidth": "3%" },
		// 	  { "sWidth": "10%" },
		// 	  { "sWidth": "5%" },
		// 	  { "sWidth": "5%" },
		// 	  { "sWidth": "20%","bSortable": false }
		// 	]
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
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	// alert(searchName);
	sales_id 	= encodeURIComponent(sales_id.trim());
	// $("#loading-modal").modal('show');
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&type=" + type + "&sales_id=" + sales_id + "&company_name=" + company_name + "&df1=" + df1+ "&dispatch_month=" + dispatch_month + "&dispatch_year=" + dispatch_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate,function(){
		// $("#loading-modal").modal('hide');
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&sales_id=" + sales_id + "&company_name=" + company_name + "&df1=" + df1+ "&dispatch_month=" + dispatch_month + "&dispatch_year=" + dispatch_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type+ "&dispatch_month=" + dispatch_month + "&dispatch_year=" + dispatch_year+ "&customer_id=" + customer_id,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(500,1);
});
function del_conf(id){
	
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='dispatch_crud.php?mode=delete&id='+id;
	}
}
function cash_payment(id){
	
	var r = confirm("Are you sure you want to make manual close?");
	if(r){
		window.location.href='dispatch_crud.php?mode=cash_payment_flag&id='+id;
	}
}

function paymentGenReport(cid){
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "payment_info_genReport_ajax.php",
		data: 'cid='+cid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
				setTimeout(function(){
					$(".transCover").fadeOut(500);
					window.location.href=result;
				},1500);
			}
	});
}

function genReport()
{
	var searchName = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());
	// ToDate = $("#ToDate").val();
	// FromDate = 	$("#FromDate").val();
	df1 = 	$("#material_request_filter_input").val();
	status = $("#status").val();
	type = $("#type").val();
	sales_id = $("#sales_id").val();
	company_name = $("#company_name").val();
	qid = $("#qid").val();

	$.ajax({
		method: "POST",
		url: "dispatchh_info_genReport_ajax.php",
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
  //   	error:function(result){
  //   		var result = $.parseJSON(result);
  //   		alert(JSON.stringify(result))
  //   		alert(result.file_path);
		// 	window.location.href="<?=SITEURL?>"+result.file_path;
		// }
	});
}

function genComplainPrint()
	{
		var searchName 	= $("#searchName").val();
      	searchName     	= encodeURIComponent(searchName.trim());
		var type     	= $("#type").val();
		// var ToDate		= $("#ToDate").val();
		// var FromDate	= $("#FromDate").val();
		var status  	= $("#status").val();
		var sales_id  	= $("#sales_id").val();
		var company_name  	= $("#company_name").val();
		var df1  	= $("#material_request_filter_input").val();
		// alert(type);
     	var myWindow = window.open('print_dispatch_report.php?searchName='+searchName+ "&type=" + type + "&status=" + status + "&sales_id=" + sales_id + "&company_name=" + company_name + "&df1=" + df1,'','width=700,height=800');
     	myWindow.print();
  //    	setTimeout(function () 
		// {
		// 	myWindow.print();
		// 	var ival = setInterval(function() 
		// 	{
		// 	    myWindow.close();
		// 	    clearInterval(ival);
		// 	}, 200);
		// }, 500);
    }

function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000;border-collapse:collapse padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}
function paymentPrintPDF() 
{
	var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>table{margin-bottom:20px;}th,tr,td{border:1px solid #000;border-collapse:collapse padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}
</script>
</body>
</html>