<?php
$page_id=592;$page_slug='expense_page';
$ctable 	= "expense";
$ctable1 	= "Expense";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$id=isset($_REQUEST['id'])?$_REQUEST['id']:"";
$date_filter_query = "";
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");

if(isset($_REQUEST['today']) && $_REQUEST['today']!="" && $_REQUEST['today']==1)
{
	$date_filter_query = date("Y-m-d")." to ".date("Y-m-d");
}
else
{
	$date_filter_query = date("Y-m-01")." to ".date("Y-m-t");	
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
<style type="text/css">
	.modal-dialog
	{
	    width: 800px;
	    margin: 30px auto;
	}
	 #wrapper
	{
	    width:190mm;
	    margin:0 50mm;
	}
</style>
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
	                            <div class="slimScrollDiv" style="position: relative; /*overflow: hidden;*/ width: auto; height: auto;">
									<div class="row">
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										 	<?php echo $db->getAddButton($ctable);?>	
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										   	<div class="form-group" role="form">
												<div class="form-group">
	                                               <label>Filter By Sales Person</label> 
	                                               <select class="form-control" name="sales_executive_id" id="sales_executive_id" onChange="return searchByName(this.value);">
													<option value="">select Sales Person</option>
													<option value="0">All</option>
													<?php
													// $whereCustom = "";
													// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14)
													// {
													// 	$whereCustom = " AND id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
													// }
													if ($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=14) 
	                				{
		                				$whereCustom = "";

			                			if($rights['personal_flag']==1)
														{
														 	$whereCustom .= " AND id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
														}
														else 
														{
															if($rights['chain_vise_flag'] == 1)
														 	{	
																$exeType = $db->rp_getValue("sales_executive","type","id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."'");
																// echo $exeType;
																if($exeType=='sales_manager')
																{ 
																	$whereCustom .= " AND (sm_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."')";
																}
																else if($exeType=='area_sales_manager' || $exeType=='dispatch_sales_manager')
																{ 
																	$whereCustom .= " AND (asm_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."')";
																}
																else if($exeType=='sales_officer')
																{ 
																	$whereCustom .= " AND (so_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."')";
																}
																else
																{
																	$whereCustom .= " AND id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
																}
															}
														}
													}

													$product_list_d=$db->rp_getData('sales_executive',"*","isDelete=0 AND isActive=1 AND type!='service_engineer'".$whereCustom,"",0);
													while($product_list_r=mysqli_fetch_assoc($product_list_d))
													{
													?>
													<option <?php echo $product_list_r['name']?> value="<?php echo $product_list_r['id'];?>"><?php echo $product_list_r['name']?>
													</option>
													<?php
													}
													?>
													</select>
	                                            </div>
											</div>
									    </div>
	                                    <div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
									        <label>Filter By Date</label>
								  			<div class="input-group pull-left">
								  				<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
												<span class="input-group-addon datetimerange-picker-btn">
													<i class="fa fa-calendar"></i>
												</span>
												<span class="input-group-btn">
								          			<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        		</span>
								        	</div>
									    </div>
									    <div class="col-md-5 col-xs-5 col-sm-5 " style="margin-top:10px" >
							 				<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
												<div class="form-group" >
													<input type="text" style="width: 450px!important; display: none;" placeholder="Search By Name :  " class="form-control input-large" name="searchName" id="searchName" value="" />
												</div>

		                                      	<div class="form-group">
		                                          <input class="btn btn-danger btn-sm" type="submit" value="search">
		                                      	</div>

		                                      	<div class="form-group">
		                                          <input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
		                                       </div>

	                                       		<div class="form-group">
	                                                <div class="btn-group">
	                                                	<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"><i class="fa fa-gear"></i>
	                                                    </button>
	                                                    <ul role="menu" class="dropdown-menu dropdown-menu-right pull-right">
	                                                        <?php
															if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
															{ 
																?>
	                                                        	<li>
	                                                            	<a name="print" onClick="genExpensePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
	                                                        	</li>
	                                                       	 	<?php
															}
															if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
															{ 
																?>
	                                                        	<li>
	                                                            	<a class="excel" name="excel" onClick="genReport1()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
	                    <!-- END Portlet PORTLET-->
	                </div>
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<!-- <div class="col-md-12">
									<?php
										echo $db->getAddButton("document_list");
									?>
									
								</div>							 -->	
							</div>
						</div>
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
 <div id="myExpenseModal" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div style="width: 970px;margin-left: -56px;" class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Expense Information </div>
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
<script src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<script type="text/javascript">
var searchName="";
var sales_executive_id="<?= $_REQUEST['sales_id'] ?>";

var expense_month="<?= $_REQUEST['expense_month']?>";
var expense_year="<?= $_REQUEST['expense_year']?>";


var expense_status="<?= $_REQUEST['status'] ?>";

var data_url = "<?php echo $ctable ?>_get_ajax.php";
var todate="<?= date('d-m-Y',strtotime($_REQUEST['todate'])); ?>";
var fromdate="<?=date('d-m-Y',strtotime($_REQUEST['fromdate'])); ?>";

var df1="<?=$date_filter_query?>";

if (todate != "" && fromdate != "" && todate != "01-01-1970" && fromdate != "01-01-1970" ) {
	
	df1 = todate+" to "+fromdate;
	fromdate = "";
	todate = "";
	expense_year = "";
	
}

df1 = encodeURI(df1);


$('#myExpenseModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","expense_information_get_ajax_new.php?id="+requesting_id);
	$("#requesting_ajax").click();
})

function searchByName(){
	sales_executive_id = $("#sales_executive_id").val();
	displayRecords(100,1);
	return false;
}
// searchByName();
$(".filterBtn").on("click",function()
{
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	displayRecords(100,1);
})

function clearSearchByName(){
	df1 = "";
	sales_executive_id = "";
	// ToDate = "";
	FromDate = "";
	$("#material_request_filter_input").val("");
	//$("#sales_executive_id").val("");
	// $('#sales_executive_id').html('<option value="">Select Sales Officer</option>');
	$('#sales_executive_id').select2("val","");
	// $('#FromDate').datepicker("setDate", new Date(date.getFullYear(), date.getMonth(), 1));
	// $('#ToDate').datepicker("setDate", new Date(date.getFullYear(), date.getMonth() + 1, 1));
	displayRecords(100,1);
}

function loadDataTable(){
	$.fn.dataTable.ext.errMode = 'none';
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "23%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	// df1 = "<?=$date_filter_query?>";
	searchName 	= encodeURIComponent(searchName.trim());
	// $('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&df=" + df1  + "&sales_executive_id=" + sales_executive_id+ "&expense_month=" + expense_month+ "&expense_year=" + expense_year+ "&expense_status=" + expense_status +"&todate="+todate+"&fromdate="+fromdate ,function(){
		loadDataTable();
		// $('.preloader').fadeOut('slow');
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&df=" + df1 + "&sales_executive_id=" + sales_executive_id+ "&expense_month=" + expense_month+ "&expense_year=" + expense_year+ "&expense_status=" + expense_status+ "&todate=" + todate+ "&fromdate=" + fromdate,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});

	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + "&df=" + df1 + "&sales_executive_id=" + sales_executive_id+ "&expense_month=" + expense_month+ "&expense_year=" + expense_year+ "&expense_status=" + expense_status+ "&todate=" + todate+ "&fromdate=" + fromdate,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

function genReport1(){
	var rc = encodeURIComponent($("#print_info1").html());
	$.ajax({
		type: "POST",
		url: "ExpenseReport_gen_ajax.php",
		data: '&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			$("#myModal").modal('show');
		},
		success: function(result){ //alert(result);
				// setTimeout(function(){
					$(".transCover").fadeOut(100);
					// alert("Report file generated!!");
					$("#myModal").modal('hide');
					window.location.href=result;
					
				// },1500);
			}
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(100,1);
});
</script>
<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}

function genExpensePrint(){
		// alert("heloo");
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
    var	ToDate = $("#ToDate").val();
		var	FromDate = 	$("#FromDate").val();
		var df1 = $("#material_request_filter_input").val();
		// df1 = encodeURI(df1);
		// alert(df1);
		// alert()
		var sales_executive_id = $("#sales_executive_id").val();
		// alert(sales_executive_id);
		var class_id=$('#class_id').val();
		var area=$("#area").val();
     	var myWindow = window.open('print_expense_ajax.php?searchName='+searchName + "&sales_executive_id=" + sales_executive_id + "&df="+df1,'width=700,height=800');
     	myWindow.print();
    }
function genReport(cid){
	// alert(cid);
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "expense_info_genReport_ajax.php",
		data: 'cid='+cid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
		// alert(JSON.stringify(result));
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					window.location.href=result;
				},1500);
			}
	});
}

function genReport1(){
	var rc = encodeURIComponent($("#print_info1").html());
	$.ajax({
		type: "POST",
		url: "ExpenseReport_gen_ajax.php",
		data: '&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			$("#myModal").modal('show');
		},
		success: function(result){ //alert(result);
				// setTimeout(function(){
					$(".transCover").fadeOut(100);
					// alert("Report file generated!!");
					$("#myModal").modal('hide');
					window.location.href=result;
					
				// },1500);
			}
	});
}
// function printPDF() 
// {
// 	 var myWindow = window.open('','','width=700,height=800')
//     myWindow.document.write("<style>th,tr,td{border:1px solid #000;border-collapse:collapse padding:10px;}</style>"+$("#print_info").html());
//     myWindow.print();
   
// }
function printPDF(id) 
{	
	var myWindow =  window.open('expense_view.php?id='+id+"&p=1",'','width=500,height=800');
	  myWindow.print();
}
function del_conf(id){
	var r = confirm("Are you sure you want to Reject This expense?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
function genReportPdf(id){
	var rc = encodeURIComponent($("#print_info1").html());
	$.ajax({
		type: "POST",
		url: "expense_info_genReport_ajax.php",
		data: 'id='+id+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
			setTimeout(function(){
				$(".transCover").fadeOut(100);
				// alert()
				window.open('expense_view.php?id='+id+"&p=1",'','width=500,height=800'); 
				// window.location.href=result;
			},1500);
		}
	});
}
</script>
<script>
$(document).ready(function() {       
   $('#datatable_1').dataTable();

});
</script>
<!-- <script type="text/javascript">
	function genExpensePrint(){
		alert("hello")
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	// ToDate = $("#ToDate").val();
		// FromDate = 	$("#FromDate").val();
		df1 = $("#material_request_filter_input").val();
		// df1 = encodeURI(df1);
		// alert(df1);
		// alert()
		sales_executive_id = $("#sales_executive_id").val();
		class_id=$('#class_id').val();
		area=$("#area").val();
     	var myWindow = window.open('print_expense_ajax.php?searchName='+searchName + "&sales_executive_id=" + sales_executive_id + "&df="+df1,'width=700,height=800');
     	myWindow.print();
    }
</script> -->

<script type="text/javascript">
	$(".datetimerange-picker-btn").on("click",function(){
		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
	});
	$(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
	$('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 $(".datetimerange-picker-input").val(picker.startDate.format('YYYY-MM-DD')+" to "+picker.endDate.format('YYYY-MM-DD'));
});
</script>

</body>
</html>