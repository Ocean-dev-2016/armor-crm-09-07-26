<?php 
$page_id=583;$page_slug='page_followup';
include("connect.php");
$ctable 	= "visitor";
$ctable1 	= "visitor";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
if($_REQUEST['followup_type']=="today")
{
	$page_title = "Today's Followup";
}
if($_REQUEST['followup_type']=="future")
{
	$page_title = "Future  Followup";
}
if($_REQUEST['followup_type']=="pending")
{
	$page_title = "Pending  Followup";
}

function getYearAndMonthDates($year, $month = null) {
    $yearStartDate = date("Y-m-d", strtotime("$year-01-01"));
    $yearEndDate = date("Y-m-d", strtotime("$year-12-31"));
    
    $monthStartDate = null;
    $monthEndDate = null;
    
    if ($month !== null) {
        $monthStartDate = date("Y-m-d", strtotime("$year-$month-01"));
        $lastDayOfMonth = date("t", strtotime("$year-$month-01"));
        $monthEndDate = date("Y-m-d", strtotime("$year-$month-$lastDayOfMonth"));
    }
    
    return [
        "yearStartDate" => $yearStartDate,
        "yearEndDate" => $yearEndDate,
        "monthStartDate" => $monthStartDate,
        "monthEndDate" => $monthEndDate
    ];
}

if ($_REQUEST['followup_type'] == "") {
	if(isset($_REQUEST['followup_year']) && $_REQUEST['followup_year']!="" && $_REQUEST['followup_year']!=NULL && $_REQUEST['followup_year']!=undefined)
	{
		$yArr = getYearAndMonthDates($_REQUEST['followup_year']);
		$df1 = $yArr['yearStartDate']." to ".$yArr['yearEndDate'];
		// 01-09-2023 to 30-09-2023
	}

	if(isset($_REQUEST['followup_year']) && $_REQUEST['followup_year']!="" && $_REQUEST['followup_year']!=NULL && $_REQUEST['followup_year']!=undefined && isset($_REQUEST['followup_month']) && $_REQUEST['followup_month']!="" && $_REQUEST['followup_month']!=NULL && $_REQUEST['followup_month']!=undefined )
	{
		$yArr = getYearAndMonthDates($_REQUEST['followup_year'],$_REQUEST['followup_month']);
		$df1 = $yArr['monthStartDate']." to ".$yArr['monthEndDate'];
		// 01-09-2023 to 30-09-2023
	}
	
}

$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"followuplist_manage.php","title"=>$page_title));
$SEID=$db->rp_getvalue("dealer_distributor_network","sales_executive_id","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' ",0);
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
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
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

				<?php $db->printErrorMessage();

				 ?>
				<?php $db->printSuccessMessage(); ?>
				<!-- <div class="col-md-12">
				</div> -->
				<div class="col-xl-12 ">
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters 
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                            </div>
                        </div>

    <div class="portlet-body">
        <div class="slimScrollDiv" style="position: relative;width: auto; height: auto;">
	        <div class="row">
			    <div class="col-md-2 col-xs-2 col-sm-2"  style="margin-top:10px">
			   	    
                        <label>Search By FollowUp Type</label>	
							<select class="form-control" multiple="multiple" name="followup_type" id="followup_type">
							    	<option value="">Select Followup Type</option>
							    	<option value="all" <?= ($followup_type=="all" || $_REQUEST['followup_type'] == "all")?"selected":""; ?>>All</option>
							    	<option value="today" <?= ($followup_type=="today" || $_REQUEST['followup_type'] == "today")?"selected":""; ?>>Today Followup</option>
							    	<option value="future" <?= ($followup_type==
							    		"future" || $_REQUEST['followup_type'] == "future" )?"selected":""; ?>>Future Followup</option>
							    	<option value="pending" <?= ($followup_type=="pending" || $_REQUEST['followup_type'] == "pending")?"selected":""; ?>>Pending Followup</option>
							</select>	
								
                </div>
			 	<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
			 		<label>Search By  Mobile No :  </label>	</br>
                    <div class="form-inline" role="form">
                        <form class="form-inline " role="form" onSubmit="return searchByName();">
                            <div class="form-group">
                                <input type="text" style="width: 450px!important" placeholder="Search By  Mobile No :  " class="form-control input-small" name="searchName" id="searchName" value="" />
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
											<a name="print" onClick="genSalesExecutivePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
										</li>
											<?php
												}
												?>
												<?php
												if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{
													?>
										<li>
											<a class="excel" name="excel" onClick="genfollowupReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
<!-- 
									<div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
										
										<button type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genfollowupReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										
										<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genfollowuplistPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
									</div> -->
								</div>
                            </div>
                        </div>
                    </div> 
                    <!-- END Portlet PORTLET-->
                </div>



                <div class="col-xl-12">
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

<div class="modal" id="createFollowup" role="dialog" aria-labelledby="myModalLabel1" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
  <form role="form" action="" method="post" id="formLocation">
	<div class="modal-header">
	  <h4 class="modal-title" id="myModalLabel1">Create Followup</h4>
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">×</span>
	  </button>
	</div>
	<div class="modal-body">
	<fieldset class="form-group floating-label-form-group">
	  <label for="email">Description<span class="text-danger">*</span></label>
	  <textarea class="form-control" width="150px" id="description" name="description" placeholder="Enter Description" type="text"></textarea>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label for="email">Followup Through<span class="text-danger">*</span></label>
	  <select class="form-control through1234" id="through" name="through">
	  <option value="0">Select Followup Through</option>
	  <option value="1">Call</option>
	  <option value="2">Sms</option>
	  <option value="3">Email</option>
	  <!-- <option value="4">Whatsapp</option> -->
	  </select>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label>Followup Date <spatn class="text-danger">*</span></label>	
		 <div class="input-group input-medium date ">
			<input type="text" class="form-control datetime-picker" disabled name="followup_date" id="followup_date" placeholder="Followup Date">
			<span class="input-group-btn">
				<button class="btn default" type="button">
					<i class="fa fa-calendar"></i>
				</button>
			</span>
		</div>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label for="followup_status">Followup Status<span class="text-danger">*</span></label>
	  <select class="form-control" id="followup_status" name="followup_status">
	  <option value="">Select Followup Status</option>
	  	<option value="0">Generate</option> 
	  	<option value="2">Positive</option> 
			<option value="1">In Followup</option>
			<option value="4">Hot</option>
			<option value="5">Cold</option> 
			<option value="6">Warm</option> 
			<option value="-1">My Work</option>
			<option value="3">Buy Later</option>
			<option value="-2">Cancel</option> 
			<option value="11">Lost</option>
	  </select>
	</fieldset>
	</div>
	<div class="modal-footer">
		<input type="hidden" id="response_sales_id">
		<input type="hidden" id="visitor_id">
		<input type="hidden"  id="response_followup_flag">
	  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
		<button type="button" id="save_followup" class="btn btn-success">Save </button>
	</div>
	</form>
  </div>
</div>
</div>
<div class="modal" id="FollowupResponse" role="dialog" aria-labelledby="myModalLabel2" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
  <form role="form" action="" method="post" id="followuprespose">
	<div class="modal-header">
	  <h4 class="modal-title">Followup Response  <span id="response_followup_title"></span></h4>
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">×</span>
	  </button>
	</div>
	
	<div class="modal-body">
	<fieldset class="form-group floating-label-form-group">
	  <label for="response">Response<span class="text-danger">*</span></label>
	  <textarea class="form-control" value="" width="150px" id="response" name="response" placeholder="Enter Response" type="text"></textarea>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
	  <label for="followup_action">Followup Action<span class="text-danger">*</span></label>
	  <select class="form-control" id="followup_action" name="followup_action" onChange="showRelatedBlock(this)">
	  <option value="">Select Followup Action</option>
	  <option value="1">Next Followup</option>
	  <!-- <option value="2">In Future</option> -->
	  <option value="-1">End Followup</option>
	  </select>
	</fieldset>
	 
	<fieldset class="form-group floating-label-form-group followup_block_future" style="display:none">
	
	  <label>Followup Future Date <span class="text-danger">*</span></label>	
		 <div class="input-group input-medium date ">
			<input type="text" class="form-control datetime-picker1" name="followup_future_date" id="followup_future_date" placeholder="Followup Date">
		</div>
		
	</fieldset>
	</div>
	<div class="modal-footer">
		<input type="hidden" name="sales_id" id="sales_id">
		<input type="hidden" name="followup_flag" id="followup_flag">
	  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
		<button type="button" id="response_followup_btn" class="btn btn-success">Save </button>
	</div>
	</form>
  </div>
</div>
</div>
<div class="modal" id="EndFollowupResponse" role="dialog" aria-labelledby="myModalLabel2" >
<div class="modal-dialog" role="document">
  <div class="modal-content">
  <form role="form" action="" method="post" id="followuprespose">
	<div class="modal-header">
	  <h4 class="modal-title">Followup End Response  <span id="end_response_followup_title"></span></h4>
	  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">×</span>
	  </button>
	</div>
	
	<div class="modal-body">
	<fieldset class="form-group floating-label-form-group">
	  <label for="response">Response<span class="text-danger">*</span></label>
	  <input type="hidden" name="end_followup_id" id="end_followup_id">
	  <textarea class="form-control" readonly="" value="" width="150px" id="end_response" name="end_response" placeholder="Enter Response" type="text"></textarea>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
		<label for="email">Followup Reason<span class="text-danger">*</span></label>
		<select class="form-control" id="followup_reason_id" name="followup_reason_id">
			<option value="">Select Followup Reason</option>
			<?php
			$f_reason_r=$db->rp_getData("followup_reason","*","isDelete=0","",0);
			while($f_reason_d=mysqli_fetch_assoc($f_reason_r))
			{
			?>
			<option value="<?= $f_reason_d['id'] ?>"><?= $f_reason_d['name'] ?></option>
			<?php
			}
			?>
		</select>
	</fieldset>
	<fieldset class="form-group floating-label-form-group">
		<label for="email">Followup Status<span class="text-danger">*</span></label>
		<select class="form-control" id="followup_status_id" name="followup_status_id">
			<option value="">Select Followup Status</option> 
			<?php
			// $status1 = "";
				// $status1 = $db->rp_getValue("no_order_inquiry","status","id='".$_REQUEST['inquiry_id']."'","",0);
			?>
			<option <?=($status1==0)?"selecte":""; ?> value="0">Generate</option> 
			<option <?=($status1==2)?"selecte":""; ?> value="2">Positive</option> 
			<option <?=($status1==1)?"selecte":""; ?> value="1">In Followup</option>
			<option <?=($status1==4)?"selecte":""; ?> value="4">Hot</option>
			<option <?=($status1==5)?"selecte":""; ?> value="5">Cold</option> 
			<option <?=($status1==6)?"selecte":""; ?> value="6">Warm</option> 
			<option <?=($status1==-2)?"selecte":""; ?> value="-2">Cancel</option> 
			<option <?=($status1==-1)?"selecte":""; ?> value="-1">Working</option>
			<option <?=($status1==3)?"selecte":""; ?> value="3">Buy Later</option>
			<option <?=($status1==11)?"selecte":""; ?> value="11">Lost</option>
		</select>
	</fieldset>

	<!-- <fieldset class="form-group floating-label-form-group">
	  <label for="end_followup_action">Followup Action<span class="text-danger">*</span></label>
	  <select class="form-control" id="end_followup_action" name="end_followup_action" >
	  <option value="">Select Followup Action</option>
	  <option value="3">Cancel</option>
	  <option value="2">No Requirement</option>
	  <option value="-1">Others</option>
	  </select>
	</fieldset> -->	
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
		<button type="button" id="end_response_followup_btn" class="btn btn-success">Save </button>
	</div>
	</form>
  </div>
</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script> 
<script type="text/javascript" src="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>

<script type="text/javascript">
	$("#followup_type").fSelect();
</script>
<script type="text/javascript">

var data_url = "followuplist_get_ajax.php";
var searchName="";
var df="";
var df1="";
var df2="";
// var df1="<?php echo $df1; ?>";
// var df1 = encodeURI(df1);
var through="";
var reference_media_id="";
var executive="<?php echo $_REQUEST['customer_id']; ?>";
var sales_executive="<?php echo $_REQUEST['sales_id'] ?>";
var followup_type = "";
var isFillter=false;
var todate="<?= date('d-m-Y',strtotime($_REQUEST['todate'])); ?>";
var fromdate="<?=date('d-m-Y',strtotime($_REQUEST['fromdate'])); ?>";
if (todate != "" && fromdate != "" && todate !="01-01-1970" && fromdate!="01-01-1970") {
		df1 = todate+" to "+fromdate;
		fromdate = "";
		todate = "";
	
	}
	df1 = encodeURI(df1);


followup_type = '<?php  echo $_REQUEST['followup_type']?>';


function getmedia(id){
	reference_media_id = id;
	displayRecords(50,1);
	return false;
}

function GetFollowypType(val){
	followup_type = val;
	//displayRecords(50,1);
	return false;
}

function searchByName(){
	searchName = $("#searchName").val();
	through = $("#through").val();
	sales_executive = $("#sales_executive").val();	
	followup_type = $("#followup_type").val();
	//alert(followup_type);
	//df = $("#material_request_filter_input").val();
	df = $("#quick_stock_adjustment_filter_input").val();
	//df1 = $("#material_request_filter_input_1").val();
	df1 = $("#quick_stock_adjustment_filter_input").val();
	df2 = $("#quick_stock_adjustment_filter_input").val();
	isFillter=true;
	displayRecords(50,1);
	return false;
}

function searchByExecutive(id){
	executive = id;
	displayRecords(50,1);
	return false;
}

/*function searchBysalesExecutive(id){
	sales_executive = id;
	//displayRecords(50,1);
	return false;
}*/

// $(".datetimerange-picker-btn").on("click",function(){
// 		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
// 	});
// 	$(".customer-select").select2();
// 	//var StartFilterDate = moment().subtract(29, 'days');
//    // var EndFilterDate = moment();
// 	$(".datetimerange-picker-input").daterangepicker({"dateFormat":"dd-mm-yy ",timePicker:false,
// 	 startDate:  moment().subtract(29, 'days'),
//      endDate: moment(),
// 	 locale: {
// 		      format: 'DD-MM-YYYY'
// 		},
// 	ranges: {
//            'Today': [moment(), moment()],
//            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
//            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
//            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
//            'This Month': [moment().startOf('month'), moment().endOf('month')],
//            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
// }}, InitDatePickerSupport);

function InitDatePickerSupport(StartFilterDate, EndFilterDate) {
	$("#quick_stock_adjustment_filter_input").val(StartFilterDate.format('DD-MM-YYYY') + ' to ' + EndFilterDate.format('DD-MM-YYYY'));
	df=encodeURIComponent($("#quick_stock_adjustment_filter_input").val());
	displayRecords(50,1);
}
function InitDatePickerSupport(StartFilterDate, EndFilterDate) {
	$("#quick_stock_adjustment_filter_input_1").val(StartFilterDate.format('DD-MM-YYYY') + ' to ' + EndFilterDate.format('DD-MM-YYYY'));
	df1=encodeURIComponent($("#quick_stock_adjustment_filter_input_1").val());
	displayRecords(50,1);
}

function InitDatePickerSupport(StartFilterDate, EndFilterDate) {
	$("#quick_stock_adjustment_filter_input_2").val(StartFilterDate.format('DD-MM-YYYY') + ' to ' + EndFilterDate.format('DD-MM-YYYY'));
	df2=encodeURIComponent($("#quick_stock_adjustment_filter_input_2").val());
	displayRecords(50,1);
}

$(".clearBtnFilter").on('click',function(){
	df="";
	$("#quick_stock_adjustment_filter_input").val("");
	displayRecords(50,1);
});

$(".clearBtnFilter").on('click',function(){
	df1="";
	$("#quick_stock_adjustment_filter_input_1").val("");
	displayRecords(50,1);
});

$(".clearBtnFilter").on('click',function(){
	df2="";
	$("#quick_stock_adjustment_filter_input_2").val("");
	displayRecords(50,1);
});

function clearSearchByName(){
	searchName = "";
	reference_media_id="";
	executive="";
	sales_executive="";
	followup_type="";
	todate="";
	fromdate="";
	df="";
	df1="";
	df2 = "";
	through="";
	isFillter=false;
	$("#searchName").val("");
	$("#material_request_filter_input").val("");
	$("#material_request_filter_input_1").val("");
	$("#material_request_filter_input_2").val("");
	$("#reference_media_id").val("");
	$("#executive").select2("val","");
	$("#sales_executive").select2("val","");
	// $("#followup_type").select2("val","");
	// $("#followup_type").fSelect("destroy");
	// $("#followup_type").val("");
	// $("#followup_type").fSelect("create");
	$("#through").select2("val","");
	displayRecords(50,1);
}

$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});


function loadDataTable(){
	
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			  { "sWidth": "2%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&ToDate=" + df + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive=" + executive + "&sales_executive=" + sales_executive + "&followup_type=" + followup_type + "&df=" + df+ "&df1=" + df1 + "&df2=" + df2 + "&through=" + through+ "&todate=" + todate+ "&fromdate=" + fromdate + "&isFillter="+isFillter,function(){
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		// alert("hello");
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&ToDate=" + df + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive=" + executive + "&sales_executive=" + sales_executive + "&df=" + df+ "&df1=" + df1 + "&df2=" + df2 +"&through=" + through+ "&todate=" + todate+ "&fromdate=" + fromdate + "&isFillter=" + true ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
	});
	
	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&ToDate=" + df + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive=" + executive + "&sales_executive=" + sales_executive + "&df=" + df,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(50,1);
});

</script>
<script type="text/javascript">
	function genSalesExecutivePrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
		sales_executive=$("#sales_executive").val();
		followup_type=$("#followup_type").val();
		through = $("#through").val();
		df = 	$("#material_request_filter_input").val();
		df1 = 	$("#material_request_filter_input_1").val();
		df2 = 	$("#material_request_filter_input_2").val();
     	var myWindow = window.open('print_followuplist_ajax.php?searchName='+searchName + "&sales_executive=" + sales_executive + "&followup_type=" + followup_type + "&df=" + df+ "&df1=" + df1 +"&df2=" + df2 + "&through=" + through + "&todate=" + todate+ "&fromdate=" + fromdate + "&reference_media_id=" + reference_media_id + "&executive=" + executive,'','width=700,height=800');
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

    function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	var followup_type = "<?php echo $_REQUEST['followup_type'] ?>";
	if(r){
		window.location.href='followuplist_crud.php?mode=delete&id='+id+'&followup_type='+followup_type;
	}
}

    function genfollowupReport()
    {
    	var searchName = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
		sales_executive = $("#sales_executive").val();
		followup_type = String($("#followup_type").val());
		// through = $("#through").val();
		// df = 	$("#material_request_filter_input").val();
		// df1 = 	$("#material_request_filter_input_1").val();
    	$.ajax({
	        method: "POST",
	        url: "followup_report_excel.php",
	        data:{
        		searchName:searchName,
				sales_executive:sales_executive,
				followup_type:followup_type,
				through:through,
				df:df,
				df1:df1,
				df2:df2,
				todate:todate,
				fromdate:fromdate,
				reference_media_id:reference_media_id,
				executive:executive,
			},	
			dataType : 'json',
			beforeSend: function()
			{
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
        	success: function(result){
        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    	});
    }
</script>



<script type="text/javascript">
	 
function showRelatedBlock(spn)
{	
	$("fieldset.followup_block_future").hide(10);
	if($(spn).val()==1)
	{
		// $("#createFollowup").modal('show');
		// $("#FollowupResponse").modal('hide');
	}
	else if($(spn).val()==2)
	{
		$("fieldset.followup_block_future").show(500);
	}
	else if($(spn).val()==-1)
	{
		var response1=$("#response").val();	
		if(response1=="")	
		{
			toastr.error("Please Enter first Response");
			$("#followup_action").select2("val","");
		}
		else
		{
			var fid=$("#response_followup_btn").data("id");
			var title=$("#response_followup_title").html();
			$("#FollowupResponse").modal('hide');
			$("#EndFollowupResponse").modal('show');
			$("#end_response").html(response1);
			$("#end_response_followup_title").html(title);
			$("#end_followup_id").val(fid);
		}
	}
}

$('#FollowupResponse').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var followup_id=button.data("id");
		  var ref_id=button.data("ref_id");
		  var title=button.data("date");
		  var next_action=button.data("next_action");
		  var mode=button.data("mode");
		  var sales_id=button.data("sales-id");
		  var followup_flag=button.data("followup-flag");
		  var visitor_id=button.data("visitor_id");

		  if(mode=="edit")
		  {
			  $("#followup_action").attr("disabled","disabled");
		  }
		  else
		  {
			  $("#followup_action").removeAttr("disabled","disabled");
		  }
		  var response=button.data("response");
		  $("#response_followup_btn").attr("data-id",followup_id);
		  $("#response_followup_btn").attr("data-ref_id",ref_id);
		  $("#response_followup_title").html(title);
		  $("#response").val(response);
		  $("#sales_id").val(sales_id);
		  $("#followup_flag").val(followup_flag);
		  $("#response_sales_id").val(sales_id);
		  $("#response_followup_flag").val(followup_flag);
		  $("#followup_action").val(next_action);
		  $("#followup_action").select2("destroy");
		  $("#followup_action").select2();
		  $("#visitor_id").val(visitor_id);
			
	});
		$('.datetime-picker').datetimepicker({
			formatTime:'H:i',
			formatDate:'d.m.Y',
			minDate:'0',
			timepickerScrollbar:false,
			container: '#modal_followup modal-body'
		});
		$('.datetime-picker').parent("div.input-group").find("span.input-group-btn").on("click",function(){
			$('.datetime-picker').removeAttr("disabled");
			$('.datetime-picker').datetimepicker("show");
		})
		$('.date-picker').datetimepicker({
			format:'Y/m/d',
			minDate:'0',
			timepicker:false,
			container: '#createFollowup modal-body'
		});
		$('.date-picker').parent("div.input-group").find("span.input-group-btn").on("click",function(){
			$('.date-picker').removeAttr("disabled");
			$('.date-picker').datetimepicker("show");
		})
		// add followup Response
		$('.datetime-picker1').datetimepicker({
			formatTime:'H:i',
			formatDate:'d.m.Y',
			minDate:'0',
			timepickerScrollbar:false,
			container: '#modal_followup modal-body'
		});
		$('.datetime-picker1').find("div.input-group").on("click",function(){
			$('.datetime-picker1').removeAttr("disabled");
			$('.datetime-picker1').datetimepicker("show");
		})
		$('.date-picker1').datetimepicker({
			format:'Y/m/d',
			minDate:'0',
			timepicker:false,
			container: '#createFollowup modal-body'
		});
		$('.date-picker1').find("div.input-group").on("click",function(){
			$('.date-picker1').removeAttr("disabled");
			$('.date-picker1').datetimepicker("show");
		})
   	
	$(function(){
   		$("#save_followup").on('click',function(){
   			CreateFollowup();
   		});
   		
   	})
   	
   	function CreateFollowup()
   	{ 
		var isValid =true;
		var sales_id = '<?= $SEID ?>';
		// var sales_id = $("#sales_id").val(); comment by shivani
   		var followup_flag=$("#response_followup_flag").val();

   		if(followup_flag=="inquiry_followup" || followup_flag=="leads_followup")
   		{
   			var inquiry_id=$("#response_followup_btn").data("ref_id");
   		}
   		if(followup_flag=="quotation_followup" || followup_flag=="quotation_followup")
   		{
   			var quotation_id=$("#response_followup_btn").data("ref_id");
   		}
   		if(followup_flag=="manual_invoice_import")
   		{
   			var invoice_id=$("#response_followup_btn").data("ref_id");
   		}
   		if(followup_flag=="customer_followup" || followup_flag=="customer_followup")
   		{
   			var executive_id=$("#response_followup_btn").data("ref_id");
   		}

   		/*if(followup_flag=="inquiry_followup" || followup_flag=="leads_followup")
   		{
   			var inquiry_id=$("#response_followup_btn").data("ref_id");
   			// alert(inquiry_id);
   		}
   		else
   		{
   		}*/
   			var visitor_id=$("#visitor_id").val();
   		var description=$('#description').val();
   		//var through=$('#through').val();
   		var through=$('select.through1234').val();
   		var followup_date=$('#followup_date').val();
   		var followup_status=$('#followup_status').val();
   		
   		if(description!="")
   		{
   			if(through!="" && through!=0)
	   		{
	   			if(followup_date!="")
	   			{
		   			$.ajax({
						url:"followup_ajax_function.php",
						data:{
							m:"save_followup",
							description:description,   						
							followup_status:followup_status,   						
							through:through,   						
							followup_date:followup_date,
							visitor_id:visitor_id,
							invoice_id:invoice_id,
							inquiry_id:inquiry_id,
							quotation_id:quotation_id,
							executive_id:executive_id,
							followup_flag:followup_flag,
							sales_id:sales_id
						},
						success:function(result){
							var result=$.parseJSON(result);
							if(result.a==1)
							{
								$("#createFollowup").modal('hide');
								$("#followup-ajax-result-container-1").empty();
								displayRecords(500, 1);
								toastr.success(result.mg);
								location.reload();
								$("#description").val("");
								$("#through").val("");
								$("#followup_date").val("");
							}
							else
							{
								toastr.error(result.mg);
							}
						}
					})
	   			}
	   			else
	   			{
	   				toastr.error("Followup Date Required!!");
	   			}
			}
	   		else
	   		{
	   			toastr.error("Followup Through Required!!");
	   		}
	   	}
	   	else
	   	{
	   		toastr.error("Description Required!!");
	   	}
   	}
	$("#response_followup_btn").on('click',function(){
			var isValid=true;
			var followup_future_date="";
			var response=$("#response").val();
			var followup_action=$("#followup_action").val();
			followup_future_date=$("#followup_future_date").val();
			var followup_id=$(this).data("id");
			if(response=="")
			{
				isValid=false;
				toastr.error("Enter response!!","Error");
			}
			if(followup_action=="")
			{
				isValid=false;
				toastr.error("Select Next Action!!","Error");
			}
			if(followup_action==2)
			{
				if(followup_future_date=="")
				{
					isValid=false;
					toastr.error("Select Next Action!!","Error");
				}
			}
			$.ajax({
				type: "GET",
				url:"followup_ajax_function.php",
				data:{
					response:response,
					followup_id:followup_id,
					followup_action:followup_action,
					followup_future_date:followup_future_date,
					m:"add_response"
				},
				success: function(json) {
					json=$.parseJSON(json);
					msg=json.mg;
					if(json.a==1)
					{
						toastr.success(msg,"Success!!");
						$("#FollowupResponse").modal("hide");
						if(followup_action==1){
							$("#createFollowup").modal('show');
						}
						$("response_followup_title").val("");
						$("#followup-ajax-result-container-1").empty();
						displayRecords(500, 1);
						//location.reload();
						$("#response").val("");
						$("#followup_action").val("");
						$("#followup_future_date").val("");
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});			
		});
	$("#end_response_followup_btn").on('click',function(){
			var isValid=true;
			var followup_future_date="";
			var response=$("#end_response").val();
			var inquiry_id="<?= $_REQUEST['inquiry_id']?>";
			if(inquiry_id=="")
			{
				var inquiry_id=$("#end_response_followup_btn").data("id");
			}
			var followup_action=-1;
			var status=$("#end_followup_action").val();
			var followup_id=$("#end_followup_id").val();
			var followup_reason_id=$("#followup_reason_id").val();
			var followup_status_id=$("#followup_status_id").val(); 
			if(response=="")
			{
				isValid=false;
				toastr.error("Enter response!!","Error");
			}
			if(status=="")
			{
				isValid=false;
				toastr.error("Select Next Action!!","Error");
			}
			if(followup_reason_id=="")
			{
				isValid=false;
				toastr.error("Select Followup Reason!!","Error");
			}
			if(followup_status_id=="")
			{
				isValid=false;
				toastr.error("Select Followup Status!!","Error");
			}
			if(isValid)
			{				
				$.ajax({
					type: "GET",
					url:"followup_ajax_function.php",
					data:{
						response:response,
						followup_id:followup_id,
						followup_action:followup_action,
						followup_future_date:followup_future_date,
						inquiry_id:inquiry_id,
						status:status,
						followup_reason_id:followup_reason_id,
						followup_status_id:followup_status_id,
						m:"end_followup"
					},
					success: function(json) {
						json=$.parseJSON(json);
						msg=json.mg;
						if(json.a==1)
						{
							toastr.success(msg,"Success!!");						
							$("#EndFollowupResponse").modal('hide');
							$("#end_response").html("");
							$("#end_response_followup_title").html("");
							$("#end_followup_id").val("");
							$("#followup-ajax-result-container-1").empty();
							displayRecords(500, 1);
							location.reload()
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					}
				});			
			}
		});
			
	</script>
</body>
</html>
</html>