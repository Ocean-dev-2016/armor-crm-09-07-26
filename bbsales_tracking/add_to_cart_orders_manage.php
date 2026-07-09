<?php
$page_id=590;$page_slug='add_to_cart_orders';
$ctable 	= "customer_orders";
$ctable1 	= "Orders";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Add To Cart ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");
include("../include/orders.class.php");
$objOrder= new Order();
$FromDate="";
$ToDate="";
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){


	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objOrder->DeleteOrder($detail);
	
	if($reply['ack']==1){

		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location('add_to_cart_orders_manage.php?msg=deleted');
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);
		$db->rp_location('add_to_cart_orders_manage.php?msg=Not deleted');
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
									<div class="col-md-8 col-xs-8 col-sm-8" style="margin-top:10px">
										<form class="form-inline" role="form" onSubmit="return searchByName();" >
                                    		<div class="form-group">
												<label>Search By Customer Name / Order No: &nbsp;</label>
												<input type="text" class="form-control input-medium" name="searchName" placeholder="Search Here" id="searchName" value="" />
											</div>
                                    		<div class="form-group">
												<input class="btn red-haze btn-sm" type="submit" value="search">
												<input class="btn green-haze btn-sm" type="button" value="clear" onClick="clearSearchByName();">
											</div>
                
                                    	</form>
									</div>
									<div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
										<button type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genCartPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
									</div>
								</div>
								<!-- <div class="row">
									<div class="col-md-9  col-xs-9  col-sm-9" style="margin-top:10px">
										<div class="form-inline" role="form">
										   <div class="form-group">
												<label>Filter By Order Date : &nbsp;</label><br/>
												<input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
												<input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
												<input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
											</div>
										</div>
									 </div>	                           
								</div> -->
								<div class="row hidden">
									<div class="col-md-3 col-xs-12 col-sm-12" style="margin-top:10px">
										<div class="form-group">
											<label>Search by Sales Person</label>
											
											<select class="form-control status" name="type" id="type"  autofocus onChange="searchBySales(this.value);">
												<option value=""> Select Sales Person </option>
												<?php 
													$department_list_d=$db->rp_getData('sales_executive',"*","isDelete=0","name ASC",0);
													while($department_list_r=mysqli_fetch_assoc($department_list_d))
													{
														?>
														<option  value="<?php echo $department_list_r['id']?>">
														<?php echo $department_list_r['name'];?>
														</option>
														<?php
													}
												?>
											</select>
										</div>
									</div>
									
								</div>
							</div>
						</div>
					</div>
					<!-- END Portlet PORTLET-->
				</div>
				<div class="col-sm-12">
					<div class="portlet light">
					
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
<div id="myModal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Order Information </div>
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

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var searchName="";
var sales_id="";
var o_type="";
var data_url = "add_to_cart_orders_get_ajax.php";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
var ToDate="";
var FromDate="";
var df1="";
var status="";
var flag="1";
$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
function searchByName(){
	searchName = $("#searchName").val();
	sales_id = $("#sales_id").val();
	o_type = $("#o_type").val();
	// df1 = $("#df1").val();
	
	displayRecords(500,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	sales_id = "";
	o_type = "";
	ToDate = "";
	FromDate = "";
	df1 = "";
	status = "";
	$("#searchName").val("");
	$("#sales_id").select2("val","");
	$("#o_type").select2("val","");
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#material_request_filter_input").val("");
	$("#status").select2("val","");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function getByDate() {

	var checkindatestr = $("#FromDate").val();
	var dateParts = checkindatestr.split("-");
	var checkindate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

	var checkindatestr1 = $("#ToDate").val();
	var dateParts1 = checkindatestr1.split("-");
	var now = new Date(dateParts1[2], dateParts1[1] - 1, dateParts1[0]);
	var difference = now - checkindate;
	var days = difference / (1000*60*60*24);
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	if(days>=0)
	{
		displayRecords(500,1);
	}
	else
	{
		toastr.error("From Date Should Be Less Than To Date");
	}
	// ToDate = $("#ToDate").val();
	// FromDate = $("#FromDate").val();
	// if(FromDate<=ToDate)
	// {
		
		
	// 	displayRecords(500,1);
	// }
	// else
	// {
	// 	alert("From Date Should Be Less Than To Date");
	// }

	
}
function searchBySales(sid){
		
		sales_executive_id=sid;
		displayRecords(500,1);
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
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "20%"},
			  { "sWidth": "5%" ,"bSortable": false },
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	o_type 	= encodeURIComponent(o_type.trim());
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&sales_id=" + sales_id + "&flag=" + flag + "&df=" + df1 + "&o_type=" + o_type ,function(){
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		var o_type = $("#o_type").val();
		var status = $("#status").val();
		var sales_id = $("#sales_id").val();
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&flag=" + flag + "&status=" + status + "&o_type=" + o_type + "&sales_id=" + sales_id + "&df=" + df1 ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	var type = $("#type").val();
	// 	var status = $("#status").val();
	// 	var sales_id = $("#sales_id").val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&flag=" + flag + "&type=" + type + "&sales_id=" + sales_id + "&df=" + df1,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });

	// $("#results").on( "change", "#sales_id", function (e){
	// 	var type = $("#type").val();
	// 	var sales_id = $(this).val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&flag=" + flag + "&type=" + type + "&sales_id=" + sales_id, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });
	// $("#results").on( "change", "#type", function (e){
	// 	var type = $(this).val();
	// 	var sales_id = $("#sales_id").val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&flag=" + flag + "&type=" + type + "&sales_id=" + sales_id, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(500,1);
});
function del_conf(id,quotation_id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='add_to_cart_orders_manage.php?mode=delete&id='+id +'&flag=1';
	}
}

function confirmChange(id) 
{
	var r=confirm("Are you sure to forward order to production department?");
	if(r)
	{
		window.location.href="orders_crud.php?mode=isActive&id="+id+"&status=1";
	}
	
   
}
function genReport(){
	var searchName = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());
	
	sales_id = $("#sales_id").val();
	o_type = $("#o_type").val();
	df1 = 	$("#df").val();

	$.ajax({
		method: "POST",
		url: "add_to_cart_orders_genReport_ajax.php",
		data: 'searchName=' +searchName + '&sales_id=' + sales_id + '&o_type=' + o_type + '&flag=' + flag + '&df=' + df1,
		dataType : 'json',
		beforeSend: function() {
			$('.preloader').fadeIn('slow');
		},
		success: function(result){
			$('.preloader').fadeOut('slow');
			window.location.href="<?=SITEURL?>"+result.file_path;
    	},
	});
}
function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000;border-collapse:collapse padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}

function genReportDispatch(cid){
	var rc = encodeURIComponent($("#print_info_dispatch").html());
	$.ajax({
		type: "POST",
		url: "alldispatch_info_genReport_ajax.php",
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
function printPDFDispatch() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000;border-collapse:collapse padding:10px;}</style>"+$("#print_info_dispatch").html());
    myWindow.print();
   
}
</script>
<script type="text/javascript">
	function genCartPrint(){
		var searchName     = $("#searchName").val();
		var sales_id     = $("#sales_id").val();
		var o_type     = $("#o_type").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	ToDate = $("#ToDate").val();
		FromDate = 	$("#FromDate").val();
		df1 = 	$("#df").val();
		status = $("#status").val();
		// alert(df1);
     	var myWindow = window.open('print_add_to_cart_ajax.php?searchName='+searchName  + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&sales_id=" + sales_id + "&flag=" + flag  + "&o_type=" + o_type  + "&df=" + df1,'','width=700,height=800');
     	// myWindow.print();
     	setTimeout(function () 
		{
			myWindow.print();
			var ival = setInterval(function() 
			{
			    myWindow.close();
			    clearInterval(ival);
			}, 200);
		}, 500);
    }
</script>
</body>
</html>