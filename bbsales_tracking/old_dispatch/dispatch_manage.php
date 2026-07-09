<?php
$page_id=569;$page_slug='dispatch_pages';
$ctable 	= "dispatch_detail";
$ctable1 	= "Orders";
$main_page 	= $ctable;
$page 		= "dispatch_manage";
$page_title = "Manage Dispatch - ".$_REQUEST['order_no'];
include("connect.php");
$FromDate="";
$ToDate="";
$order_id=$_REQUEST['id'];
$flag=$_REQUEST['flag'];
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
			<?php
			if($flag==1)
			{
				$redirect_url="orders_manage.php";
			}
			else if($flag==2)
			{
				$redirect_url="ss_orders_manage.php";
			}
			else if($flag==3)
			{
				$redirect_url="dealer_orders_manage.php";
			}
			?>
				<h1><a href="<?php echo $redirect_url;?>" class="btn primary"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
		    <div class="row">
				<div class="col-md-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-md-12 ">
				
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
							<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
								<div class="row">
									<div class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
										<div class="form-inline" role="form">
										   <div class="form-group">
												<label>Filter By Dispatch Date : &nbsp;</label>
												<input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
											</div>
											<div class="form-group">
												 <label>&nbsp;&nbsp;</label>
												<input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
											</div>
											<div class="form-group">
														<input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
											</div>
										</div>
									</div>
									<div class="col-md-6 col-xs-6 col-sm-6 " style="margin-top:10px">
										<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
											<div class="form-group">
												<label>Search By Customer Name: &nbsp;</label>
												<input type="text" class="form-control input-small" name="searchName" id="searchName" value="" />
											</div>
											<div class="form-group">
												<input class="btn btn-danger btn-sm" type="submit" value="search">
											</div>
											<div class="form-group">
													<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
											</div>
										</form>
									 </div>                                        
								</div>
								<div class="row">
								</div>
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
					<?php 
					$status=$db->rp_getValue("orders","status","order_no='".$_REQUEST['order_no']."'","");
					if($status!=2){
					?>
						<div class="btn-group">
				
							<a class="btn sbold blue-ebonyclay" href='dispatch_crud.php?mode=add&id=<?php echo $order_id;?>&order_no=<?php echo $_REQUEST['order_no'];?>&flag=<?php echo $flag;?>'> Add New
							<i class="fa fa-plus"></i>
							</a>
						</div>
					<?php } ?>
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
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
var searchName="";
var data_url = "dispatch_get_ajax.php?id=<?php echo $order_id;?>";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
var ToDate="";
var FromDate="";
var status="";
var type="";
var flag="1";
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
function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	ToDate = "";
	FromDate = "";
	type = "";
	status = "";
	$("#searchName").val("");
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#type").select2("val","");
	$("#status").select2("val","");
	displayRecords(100,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function getByDate() {
	if($("#FromDate").val() != '' && $("#ToDate").val() != '' ){
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(100,1);
	}
	else
	{
		alert("Please Select Date");
	}

}
function getSubCat(cid){
		status=cid;
		displayRecords(100,1);
}
function getType(tid){
		type=tid;
		displayRecords(100,1);
}
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "5%" },
			  { "sWidth": "3%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "3%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "20%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName +  "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status=" + status + "&type=" + type+ "&flag=" + flag,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(100,1);
});
var order_id=<?php echo $_REQUEST['id'];?>;
function del_conf(id){
	
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='dispatch_crud.php?mode=delete&id='+id+ '&order_id=' +order_id;
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
					$(".transCover").fadeOut(100);
					window.location.href=result;
				},1500);
			}
	});
}
function genReport(cid){
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "dispatch_info_genReport_ajax.php",
		data: 'cid='+cid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					window.location.href=result;
				},1500);
			}
	});
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