<?php
$page_id=565;$page_slug='page_order';
$ctable 	= "orders";
$ctable1 	= "Orders";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Order History";
if (isset($_REQUEST['type']) && $_REQUEST['type'] == "channel_partner") {
	$page_title = "Channel Partner Order";
} else if (isset($_REQUEST['type']) && $_REQUEST['type'] == "channel_partner_portal") {
	$page_title = "Channel Partner Portal Orders";
} else if (isset($_REQUEST['type']) && $_REQUEST['type'] == "pending_payment") {
	$page_title = "Pending Payment Orders";
} else if (isset($_REQUEST['type']) && $_REQUEST['type'] == "100") {
	$page_title = "All Orders";
}
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"dealer_orders_manage.php","title"=>$page_title));
include("connect.php");
// echo "<pre>";
// print_r($rights);die;
$FromDate='';
$ToDate='';
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
if($_REQUEST['type'] != "")
{
	$type=$_REQUEST['type'];
}
else
{
	$type="";
}
if(isset($_REQUEST['lr_add_submit'])){
	// echo "<pre>";
	// print_r($_FILES);
	// echo "<hr>";
	// print_r($_REQUEST);die;
	$order_id  			= $db->clean($_REQUEST['order_id']);
	$image_path   	= $_FILES['image_path'];
	$old_image_path = $db->clean($_REQUEST['old_image_path']);
	$lr_number  		= $db->clean($_REQUEST['lr_number']);

	if (isset($image_path)) 
	{
		// print_r($image_path);exit();
		// $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
		$temp = explode(".", $image_path["name"]);
	 	$extension = end($temp);
		// echo "Sdafds";die;
	 
			$fileName 	= $db->clean($image_path["name"]);	
				// echo "heelo";exit();
			if($fileName!="")
			{
				$fileSize 	= round($image_path["size"]); // BYTES									
				$adate 		= date('Y-m-d H:i:m');
				
				$extension	= end(explode(".", $fileName));		
				if(!in_array($extension,$allowedExts))
				{
					$file_error=true;
				}
									
				// print_r($image_path); exit;
				$image_path1	= 'lr_documents'.substr(sha1(time()), 0, 6).".".$extension;
				$filePath 	= LRCOPY_A.$image_path1;
				// $file['image_path']['tmp_name'];
				// echo $image_path['tmp_name'];die;
				// echo "<pre>";
				move_uploaded_file($image_path['tmp_name'], $filePath);
				$image_path=$image_path1;
				unset($old_image_path);
			}
			else{
				$image_path=$old_image_path;	
					unset($old_image_path);
			}
	}

	if($_REQUEST['order_id']!="")
	{
		// echo $image_path;die;
		$rows = array("lr_number" => $lr_number,"lr_image" => $image_path);
		$add_lr_image = $db->rp_update($ctable,$rows,"id = ".$order_id,0);
		// if($rights['insert_flag']!=1)
		// {
		// 	$db->rp_location('access_denied.php?msg=delete_access_denied');
		// }
		// // print_r($_FILES);exit;
		// $reply=$objLRDetail->InsertLR($detail,$_FILES);
		// if($reply['ack']==1)
		// {
		// 	$db->addSuccessMessage($reply['ack_msg']);
		// 	$db->rp_location("dispatch_manage.php?msg=inserted");
		// }
		// else
		// {
		// 	$db->addErrorMessage($reply['ack_msg']);
		// }
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
				<div class="col-xl-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
			</div>
	    <div class="row">

				<div class="col-md-12 "></div>
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
							<div class="row">
								<div class="col-md-5 col-xs-5 col-sm-5">
                  					<?php
									if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
									{
									}
									$cp_add_url = null;
									if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'channel_partner'
										&& function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db)) {
										$cp_add_url = 'channel_partner_order_simple.php?cp_mode=own';
									}
									if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'channel_partner_portal') {
										/* Admin portal list: no add button — orders come from CP portal */
										$cp_add_url = false;
									}
									if ($cp_add_url !== false) {
										echo $db->getAddButton($ctable, $_REQUEST['type'], $cp_add_url);
									}
									?>
									
								</div>
								<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
                  <div class="form-inline" role="form">
                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                      <div class="form-group">
                      	<input type="text" style="width: 450px!important" placeholder="Search By Person Name/Order No :  " class="form-control input-large" name="searchName" id="searchName" value="" />
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
																	<a name="print" onClick="genOrderPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
																</li>

														<?php
															}
														?>
														<?php
															// echo "<pre>";
															// print_r($rights);die;
															if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
															{
														?>
																<li>
																	<a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
																</li>
														<?php
															}
															if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE'] == 0)
															{
														?>
															<li>

																<a class="import" href="import_order_manage.php?mode=add" target="_blank" name="import"  id="import" ><i class="fa fa-download"></i>Import Order</a>
															</li>
														<?php
															}	
														?>
													</ul>
												</div>
												<a style="background-color: #20761fc7;" onclick="showStatics()" title="Show Statistics" type="button" class="btn btn-sm">
																		<i class="fa fa-eye"></i>
																	</a>
                      </div>
                     <!--  <a style="padding: 6px 14px 5px 14px!important;" class="btn btn-primary" href="view_order_pipeline.php" target="_blank"  title="track"><span class="text-success"><i style="color: black;" class="fa fa-eye"></i></span>
                      </a> -->
                    </form>
                  </div>
                </div>
              </div>
            </div>
					</div>
				</div>
				<!-- END Portlet PORTLET-->
			</div>
			<div class="portlet-body">
				
				<?php
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
				{
				?>
					 <!-- <ul class="nav nav-tabs ">
						<li class="active">
							<a href="#my_order_info" data-toggle="tab" aria-expanded="false"> My Order Information </a>
						</li>
						<li>
							<a href="#dealer_order_info" data-toggle="tab" aria-expanded="false">Outlet's Order Information </a>
						</li>
					</ul> -->
				<?php
				}
				?>
					<div class="tab-content">
						<div class="tab-pane active" id="my_order_info">	
							<div class="row">
								<div class="col-xl-12">
									<div class="portlet light">
										<!-- <div class="col-md-6">
											<?php
											if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
											{
											}
											echo $db->getAddButton($ctable);
											?>	
										</div> -->
										<div class="portlet-body">
											<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
											<div id="results"></div>
										</div>
									</div>
								</div>											
							</div>
						</div>
						<div class="tab-pane" id="dealer_order_info">	
							<div class="row">
								<div class="col-sm-12">
									<div class="portlet light">
										<div class="col-md-6">
										</div>
										<div class="portlet-body">
											<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
											<div id="results_outlets"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
	</div>	
</div>
	  
<div id="myModal" class="modal fade">
	<div class="modal-dialog">
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

<div id="ViewDispatchInfoModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Dispatch Information </div>
					<div class="tools">

						<a href="javascript:;" id="dispatch_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

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
<!-- lr attachment modal start -->
<div id="pdfattachment" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View PDF Attachment Information </div>
					<div class="tools">

						<a href="javascript:;" id="pdf_requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title="" ><i class="fa fa-reload"></i> </a>

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

<!-- Payment Received against Order (Tally-like: Party + Against Order) -->
<div id="paymentReceivedModal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#2f6f44;color:#fff;">
				<button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-money"></i> Payment Received</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="pr_order_id" value="">
				<div class="form-group">
					<label>Party / Customer Name</label>
					<input type="text" class="form-control" id="pr_party_name" readonly style="font-weight:600;background:#f7f7f7;">
				</div>
				<div class="form-group">
					<label>Against Order Number</label>
					<input type="text" class="form-control" id="pr_order_no" readonly>
				</div>
				<div class="form-group">
					<label>Order Amount</label>
					<input type="text" class="form-control" id="pr_order_amount" readonly>
				</div>
				<div class="form-group">
					<label>Payment Received Amount <code>*</code></label>
					<input type="number" step="0.01" min="0.01" class="form-control" id="pr_paid_amount" placeholder="Enter amount received">
				</div>
				<div class="form-group">
					<label>Payment Type <code>*</code></label>
					<select class="form-control" id="pr_payment_type">
						<option value="">--- Select Payment Type ---</option>
						<option value="1">By Cash</option>
						<option value="2">By Cheque</option>
						<option value="3">Online</option>
						<option value="4">Other</option>
					</select>
				</div>
				<div class="form-group">
					<label>Remark</label>
					<textarea class="form-control" id="pr_remark" rows="2" placeholder="Optional"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-success" id="pr_save_btn"><i class="fa fa-save"></i> Save Payment</button>
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
	$('#lrattachment').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var requesting_id=button.data("id");
		$("#lr_requesting_ajax").attr("data-url","lr_detail_ajx.php?id="+requesting_id);
		$("#lr_requesting_ajax").click();
	});
</script>
<script type="text/javascript">
	$('#pdfattachment').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var requesting_id=button.data("id");
	  var pdf_ids=button.data("pdfid");
	  // alert(pdf_ids)
		$("#pdf_requesting_ajax").attr("data-url","pdf_attachment_detail_ajax.php?id="+requesting_id+"&pdf_ids="+pdf_ids);
		$("#pdf_requesting_ajax").click();
		
	});
</script>
<script type="text/javascript">
var searchName="";
var data_url = "<?= (isset($_REQUEST['type']) && $_REQUEST['type'] == 'pending_payment') ? 'pending_payment_get_ajax.php' : 'orders_get_ajax.php'; ?>";
// var data_url_outlets = "outlet_orders_get_ajax.php";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
var ToDate="";
var order_type="";
var FromDate="";
var df1="<?= date('d-m-Y') ." to ". date('d-m-Y') ?>";
var status="<?= $_REQUEST['status'] ?>";
var dispatch_status="";
var type="<?= $_REQUEST['type'] ?>";
var qid="";
var sales_id="<?= $_REQUEST['sales_id'] ?>";
var flag="3";
var order_month="<?= $_REQUEST['order_month']?>";
var order_year="<?= $_REQUEST['order_year']?>";
var customer_id="<?=$_REQUEST['customer_id']?>";
var todate="<?= date('d-m-Y',strtotime($_REQUEST['todate'])) ?>";
var fromdate="<?=date('d-m-Y',strtotime($_REQUEST['fromdate'])) ?>";
var company_name="<?= $_REQUEST['company_name'] ?>";
if (todate != "" && fromdate != "" && todate !="01-01-1970" && fromdate != "01-01-1970") {
	df1 = todate+" to "+fromdate;
	fromdate = "";
	todate = "";
	order_year = "";
}
df1 = encodeURI(df1);
if (order_year != "") {
	df1 = "";
}
var type_of_company="";
var uid="<?php echo $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']?>";
$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
$('#ViewDispatchInfoModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var order_id=button.data("id");
	$("#dispatch_ajax").attr("data-url","alldispatch_information_get_ajax.php?id="+order_id);
	$("#dispatch_ajax").click();
})
function searchByName(){
	searchName = $("#searchName").val();
	type = '<?= $_REQUEST['type'] ?>';
	// type = $("#type").val();
	sales_id = $("#sales_id").val();
	status = $("#status").val();
	dispatch_status = $("#dispatch_status").val();
	qid=$("#qid").val();
	type_of_company=$("#type_of_company").val();
	company_name=$("#company_name").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1);
	displayRecords(50,1);
	// displayRecords_outlets(500,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	ToDate = "";
	FromDate = "";
	df1 = "";
	status = "";
	dispatch_status="";
	type = "<?= $_REQUEST['type'] ?>";
	sales_id = "";
	qid = "";
	company_name = "";
	$("#searchName").val("");
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#material_request_filter_input").val("");
	$("#status").val("");
	$("#dispatch_status").val("");
	// $("#type").val("");
	$("#sales_id").val("");
	$("#qid").val("");
	$("#type_of_company").val("");
	$("#company_name").val("");
	//$("#status").selec2("val","");
	displayRecords(50,1);
	// displayRecords_outlets(500,1);
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
// 		displayRecords(50,1);
// 		// displayRecords_outlets(500,1);
// 	}
// 	else
// 	{
// 		alert("Please Select Date");
// 	}

// }

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
		displayRecords(50,1);
	}
	else
	{
		toastr.error("From Date Should Be Less Than To Date");
	}
	
}
function getSubCat(cid){
		status=cid;
		displayRecords(50,1);
		// displayRecords_outlets(500,1);
}
function loadDataTable(){
	var $table = $('#datatable_1');
	if (!$table.length) {
		return;
	}
	if ($.fn.dataTable && $.fn.dataTable.isDataTable('#datatable_1')) {
		$table.dataTable().fnDestroy();
	}
	var colCount = $table.find('thead th').length;
	var canInit = colCount > 0;
	$table.find('tbody tr').each(function () {
		var tdCount = $(this).children('td').length;
		var colspanCount = $(this).children('td[colspan]').length;
		if (colspanCount > 0 || (tdCount > 0 && tdCount !== colCount)) {
			canInit = false;
			return false;
		}
	});
	if (!canInit) {
		return;
	}
	var aoColumns = [];
	for (var i = 0; i < colCount; i++) {
		aoColumns.push({ "bSortable": false });
	}
	$table.dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"aoColumns": aoColumns
	});
}
function displayRecords(numRecords) {
	 
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	// $("#loading-modal").modal('show');
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status=" + status + "&dispatch_status=" + dispatch_status + "&order_type=" + order_type  + "&uid=" + uid + "&flag=" + flag + "&type=" + type + "&sales_id=" + sales_id + "&df=" + df1+ "&qid=" + qid+ "&order_month=" + order_month + "&order_year=" + order_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&type_of_company=" + type_of_company + "&company_name="+company_name,function(){
		// $("#loading-modal").modal('hide');
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		// var type = $("#type").val();
		var type='<?= $_REQUEST['type'] ?>';
		var status = $("#status").val();
		var sales_id = $("#sales_id").val();
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName+ "&order_type=" + order_type + "&uid=" + uid + "&flag=" + flag + "&status=" + status + "&dispatch_status=" + dispatch_status + "&type=" + type + "&sales_id=" + sales_id + "&df=" + df1+ "&qid=" + qid+ "&order_month=" + order_month + "&order_year=" + order_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&type_of_company=" + type_of_company + "&company_name="+company_name,{"page":page}, function(){ //get content from PHP page
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
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&order_type=" + order_type + "&uid=" + uid + "&flag=" + flag + "&status=" + status + "&type=" + type + "&sales_id=" + sales_id, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });

	// $("#results").on( "change", "#sales_id", function (e){
	// 	var type = $("#type").val();
	// 	var status = $("#status").val();
	// 	var sales_id = $(this).val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&order_type=" + order_type + "&uid=" + uid + "&flag=" + flag + "&status=" + status + "&type=" + type + "&sales_id=" + sales_id, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });
	// $("#results").on( "change", "#type", function (e){
	// 	var type = $(this).val();
	// 	var status = $("#status").val();
	// 	var sales_id = $("#sales_id").val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&order_type=" + order_type + "&uid=" + uid + "&flag=" + flag + "&status=" + status + "&type=" + type + "&sales_id=" + sales_id, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });
	// $("#results").on( "change", "#status", function (e){
	// 	var status = $(this).val();
	// 	var type = $("#type").val();
	// 	var sales_id = $("#sales_id").val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&order_type=" + order_type + "&uid=" + uid + "&flag=" + flag + "&status=" + status + "&type=" + type + "&sales_id=" + sales_id, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });
}
function loadDataTable_outlets(){
	$('#datatable_outlets').dataTable({
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
			  { "sWidth": "20%","bSortable": false }
			]
	});
}
// function displayRecords_outlets(numRecords) {
// 	var searchName 	= $("#searchName").val();
// 	searchName 	= encodeURIComponent(searchName.trim());
// 	$("#results_outlets" ).html("");
// 	$("#results_outlets" ).load( data_url_outlets+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status=" + status + "&type=" + type + "&uid=" + uid + "&df=" + df1+ "&df=" + df1,function(){
// 		loadDataTable_outlets();
// 	});
	 //load initial records
	
	//executes code below when user click on pagination links
	// $("#results_outlets").on( "click", ".paging_simple_numbers a", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); 
	// 	var page = $(this).attr("data-page");
	// 	$("#results_outlets").load(data_url_outlets+"?show=" + numRecords + "&searchName=" + searchName +  "&ToDate=" + ToDate +  "&uid=" + uid + "&df=" + df1+ "&df=" + df1,{"page":page}, function(){
	// 		$(".loading-div").hide(); 
	// 		loadDataTable_outlets();
	// 	});
		
	// });
	// $("#results_outlets").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results_outlets").load(data_url_outlets+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&uid=" + uid,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable_outlets();
	// 	});
		
	// });

	
// }
// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
	// displayRecords_outlets(numRecords,1);
}

$(document).ready(function() {
	displayRecords(100,1);
});
$(document).ready(function() {
	// displayRecords_outlets(500,1);
});

function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}

$('#paymentReceivedModal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var orderId = button.data('order-id') || '';
	var orderNo = button.data('order-no') || '';
	var grandTotal = button.data('grand-total') || 0;
	var partyName = button.data('party-name') || '';
	$('#pr_order_id').val(orderId);
	$('#pr_order_no').val(orderNo);
	$('#pr_order_amount').val(grandTotal);
	$('#pr_party_name').val(partyName);
	$('#pr_paid_amount').val(grandTotal > 0 ? grandTotal : '');
	$('#pr_payment_type').val('');
	$('#pr_remark').val(partyName ? ('Payment received from ' + partyName + ' against Order ' + orderNo) : '');
});

$('#pr_save_btn').on('click', function () {
	var orderId = $('#pr_order_id').val();
	var paidAmount = $.trim($('#pr_paid_amount').val());
	var paymentType = $('#pr_payment_type').val();
	var remark = $.trim($('#pr_remark').val());
	var orderNo = $('#pr_order_no').val();

	if (!orderId) {
		alert('Invalid order.');
		return;
	}
	if (paidAmount === '' || isNaN(paidAmount) || parseFloat(paidAmount) <= 0) {
		alert('Please enter Payment Received Amount.');
		$('#pr_paid_amount').focus();
		return;
	}
	if (!paymentType) {
		alert('Please select Payment Type.');
		$('#pr_payment_type').focus();
		return;
	}
	if (!confirm('Save Payment Received for Order ' + orderNo + '?')) {
		return;
	}

	var $btn = $(this);
	$btn.prop('disabled', true);
	$.ajax({
		type: 'POST',
		url: 'order_payment_received_ajax.php',
		data: {
			order_id: orderId,
			paid_amount: paidAmount,
			payment_type: paymentType,
			remark: remark
		},
		dataType: 'json',
		success: function (result) {
			$btn.prop('disabled', false);
			if (result && result.ack == 1) {
				$('#paymentReceivedModal').modal('hide');
				if (typeof toastr !== 'undefined') {
					toastr.success(result.ack_msg);
				} else {
					alert(result.ack_msg);
				}
				displayRecords(typeof numRecords !== 'undefined' ? numRecords : 100, 1);
			} else {
				var msg = (result && result.ack_msg) ? result.ack_msg : 'Failed to save payment';
				if (typeof toastr !== 'undefined') {
					toastr.error(msg);
				} else {
					alert(msg);
				}
			}
		},
		error: function () {
			$btn.prop('disabled', false);
			alert('Payment Received request failed');
		}
	});
});

function confirmChange(id) 
{
	var r=confirm("Are you sure to forward order to production department?");
	if(r)
	{
		window.location.href="orders_crud.php?mode=isActive&id="+id+"&status=1";
	}
	
   
}
function genReport()
{
	var searchName = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());
	// ToDate = $("#ToDate").val();
	// FromDate = 	$("#FromDate").val();
	df1 = 	$("#material_request_filter_input").val();
	status = $("#status").val();
	dispatch_status = $("#dispatch_status").val();
	var type='<?= $_REQUEST['type'] ?>';
	sales_id = $("#sales_id").val();
	qid = $("#qid").val();
	type_of_company = $("#type_of_company").val();
	company_name = $("#company_name").val();

	$.ajax({
		method: "POST",
		url: "orders_info_genReport_ajax.php",
		data: 'searchName=' + searchName + '&ToDate=' + ToDate + '&FromDate=' + FromDate + '&df=' + df1 + '&status=' + status + '&type=' + type + '&sales_id=' + sales_id + '&qid=' + qid + '&dispatch_status=' + dispatch_status+ '&type_of_company=' + type_of_company+'&company_name='+company_name,
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
// function printPDF() 
// {
// 	 var myWindow = window.open('','','width=700,height=800')
//     myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
//     myWindow.print();
   
// }
// function genReportDispatch(cid){
// 	var rc = encodeURIComponent($("#print_info_dispatch").html());
// 	$.ajax({
// 		type: "POST",
// 		url: "alldispatch_info_genReport_ajax.php",
// 		data: 'cid='+cid+'&rc='+rc,
// 		beforeSend: function() {
// 			$(".transCover").fadeIn(800);
// 		},
// 		success: function(result){ 
// 				setTimeout(function(){
// 					$(".transCover").fadeOut(500);
// 					window.location.href=result;
// 				},1500);
// 			}
// 	});
// }
// function printPDFDispatch() 
// {
// 	 var myWindow = window.open('','','width=700,height=800')
//     myWindow.document.write("<style>th,tr,td{border:1px solid #000;border-collapse:collapse padding:10px;}</style>"+$("#print_info_dispatch").html());
//     myWindow.print();
   
// }
</script>
<script type="text/javascript">
	function genOrderPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	/*ToDate = $("#ToDate").val();
		FromDate = 	$("#FromDate").val();*/
		df1 = 	$("#material_request_filter_input").val();
		qid = 	$("#qid").val();
		// alert(qid);
		status = $("#status").val();
		dispatch_status = $("#dispatch_status").val();
		var type='<?= $_REQUEST['type'] ?>';
		sales_id = $("#sales_id").val();
		type_of_company = $("#type_of_company").val();
		company_name = $("#company_name").val();
		// alert(df1);
     	var myWindow = window.open('print_dealer_order_ajax.php?searchName='+searchName+"&uid=" + uid + "&status=" + status + "&sales_id=" + sales_id + "&type=" + type + "&df=" + df1+ "&qid=" + qid+ "&dispatch_status=" + dispatch_status+ "&type_of_company=" + type_of_company + "&company_name="+company_name,'','width=700,height=800');
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


    function editStatus(id){
	$("#dispatch_status"+id).removeAttr("disabled");
	$("#editStatus_"+id).hide(100);
	$("#editStatus2_"+id).show(400);
}

function cancelEditStatus(id){
	$("#editStatus2_"+id).hide(100);
	$("#editStatus_"+id).show(400);
	$("#dispatch_status"+id).attr("disabled","disabled");
}

function saveEditStatus(id){
var newdispatch_status = $("#dispatch_status"+id).val();

	$.ajax({
		type: "POST",
		url: "ajax_update_Dispatch_status.php",
		data: "id=" + id + "&status=" + newdispatch_status+'&table=orders',
		cache: false,
		beforeSend: function() {
			
		},
		success: function(html) {		

			var result=$.parseJSON(html);
			if(result.ack==1)
			{
				toastr.success(result.ack_msg);
				cancelEditStatus(id);
			}
			else
			{
				toastr.error(result.ack_msg);
			}
			if(html==1){
				
				toastr.success("Status Updated Successfully");
			}			
		}
	});
		
}



function ConvertCpPortalOrder(oid) {
	var r = confirm("Convert this Portal Order to regular Channel Partner Order with pricing?\n\nIt will open in Armor regular order format.");
	if (!r) {
		return;
	}
	$.ajax({
		type: "POST",
		url: "ajax_convert_cp_portal_order.php",
		data: { order_id: oid },
		dataType: "json",
		beforeSend: function () {
			$(".transCover").fadeIn(800);
		},
		success: function (result) {
			$(".transCover").fadeOut(100);
			if (result && result.ack == 1) {
				toastr.success(result.ack_msg);
				if (result.redirect) {
					setTimeout(function () {
						window.location.href = result.redirect;
					}, 800);
				} else if (typeof displayRecords === "function") {
					displayRecords(typeof numRecords !== "undefined" ? numRecords : 100, 1);
				}
			} else {
				toastr.error((result && result.ack_msg) ? result.ack_msg : "Convert failed");
			}
		},
		error: function () {
			$(".transCover").fadeOut(100);
			toastr.error("Convert request failed");
		}
	});
}

function CreditCpStock(oid) {
	if (!confirm("Account Approved order — Dispatch and credit quantity to Channel Partner stock?")) {
		return;
	}
	$.ajax({
		type: "POST",
		url: "ajax_cp_credit_stock.php",
		data: { order_id: oid, mark_dispatch: 1 },
		dataType: "json",
		success: function (result) {
			if (result && result.ack == 1) {
				toastr.success(result.ack_msg);
				if (typeof displayRecords === "function") {
					displayRecords(typeof numRecords !== "undefined" ? numRecords : 100, 1);
				}
			} else {
				toastr.error((result && result.ack_msg) ? result.ack_msg : "Credit failed");
			}
		},
		error: function () {
			toastr.error("Credit stock request failed");
		}
	});
}

function CpDispatchOrder(oid) {
	if (!confirm("Mark this customer order as Dispatched?")) {
		return;
	}
	$.ajax({
		type: "POST",
		url: "ajax_cp_dispatch_order.php",
		data: { order_id: oid, dispatch_status: 1 },
		dataType: "json",
		success: function (result) {
			if (result && result.ack == 1) {
				toastr.success(result.ack_msg);
				if (typeof displayRecords === "function") {
					displayRecords(typeof numRecords !== "undefined" ? numRecords : 100, 1);
				}
			} else {
				toastr.error((result && result.ack_msg) ? result.ack_msg : "Dispatch failed");
			}
		},
		error: function () {
			toastr.error("Dispatch request failed");
		}
	});
}
</script>
</body>
</html>