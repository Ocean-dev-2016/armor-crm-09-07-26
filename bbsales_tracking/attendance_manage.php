<?php
$page_id=593;$page_slug='attendance_page';
$ctable 	= "attendance";
$ctable1 	= "Attendance";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
$se_id=isset($_REQUEST['id'])?$_REQUEST['id']:"";
if(!isset($_REQUEST['id']))
{
	$redirect="dashboard.php";
}
else
{
	$redirect="sales_executive_manage.php";
}
include("connect.php");

function checkVar($var) {
    if (
        isset($var) &&
        !empty($var) &&
        $var != undefined &&
        $var != null &&
        $var != NULL &&
        $var != "" &&
        $var != 'undefined' &&
        $var != 'NULL'
    ) {
        return true;
    } else {
        return false;
    }
}

//variable defination
$date_filter_query="";
$fillter_redirect="";
$dashboard_month = "";
$dashboard_year = "";

$fillter_redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : '';

if ($fillter_redirect == 'dashboard') {
	if (checkVar($_REQUEST['todate']) && checkVar($_REQUEST['fromdate'])) {
		$date_filter_query = date("d-m-Y",strtotime($_REQUEST['todate']))." to ".date("d-m-Y",strtotime($_REQUEST['fromdate']));
	}
	$dashboard_month = checkVar($_REQUEST['attendance_month']) ? $_REQUEST['attendance_month'] : '';
	$dashboard_year = checkVar($_REQUEST['attendance_year']) ? $_REQUEST['attendance_year'] : '';
	

} else {
	$date_filter_query = date("d-m-Y")." to ".date("d-m-Y");
}
// if(isset($_REQUEST['today']) && $_REQUEST['today']!="" && $_REQUEST['today']==1)
// {
//}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>

<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $redirect;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
                                <i class="fa fa-filter"></i>Filters </div>
                             <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
							</div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv">
								<div class="row">
									<!-- <div class="col-md-4 col-xs-4 col-sm-4" style="margin-top:10px">
								  		<div class="input-group pull-left">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
												<i class="fa fa-calendar"></i>
											</span>
										
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
								        </div> -->
					 <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
                               
                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                       	<div class="form-group">

                                          <input type="text" style="width: 450px!important" placeholder="Search By Name /  Phone :  " class="form-control input-large" name="searchName" id="searchName" value="" />

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
														<a name="print" onClick="genAttandancePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
								       <!--  <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
											<button type="button" class="btn green-haze btn-sm excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>

											<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genAttandancePrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
										</div> -->
                                </div> 
                        	</div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
				<div class="col-xl-12">
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<?php
										//echo $db->getAddButton($ctable);
									?>	
								</div>
							</div>
						</div>
						<div class="portlet-body">
							
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div class="">
								<div id="results"></div>
                			</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Attandance In Modal -->
<style type="text/css">
	@media (min-width: 768px){
		.modal-dialog {
			width:1000px!important;
		}
	}
</style>
<div id="InTimeData" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>Attandance Information </div>
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
<script src="<?=ADMINSITEURL?>js/lightbox.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var se_id="<?= $se_id; ?>";
var searchName="";
var state="";
var city="";
// var df1="";
var df1="<?= $date_filter_query; ?>";

df1 = encodeURI(df1);
var io="<?= $_REQUEST['status'] ?>";
var sales_executive="";
var customer_id="";
var attendance_month="<?= $dashboard_month?>";
var attendance_year="<?= $dashboard_year?>";
var todate="";
var fromdate="";
var attendance_type="";
var data_url = "attendance_get_ajax_new.php";

// var data_url = "index_demo.php";

function searchByName(){
	sales_executive = $("#sales_executive").val();
	searchName = $("#searchName").val();
	io = $("#io").val();io
	attendance_type = $("#attendance_type").val();
	state = $("#state").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1);
	displayRecords(50,1);
	return false;
}


function clearSearchByName(){
	searchName = "";
	sales_executive = "";
	df1 = "";
	io = "";
	state = "";
	attendance_type = "";
	$("#searchName").val("");
	$("#sales_executive").val("");
	$("#io").val("");
	$("#state").val("");
	$("#attendance_type").val("");
	$("#material_request_filter_input").val("");
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
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "1%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "3%" },
			  { "sWidth": "21%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			]
	});
}
function displayRecords(numRecords) {
	city=encodeURIComponent(city.trim());

	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&se_id=" + se_id + "&io=" + io+ "&attendance_month=" + attendance_month + "&attendance_year=" + attendance_year+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&attendance_type=" + attendance_type + "&state=" + state,function(){
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&se_id=" + se_id  + "&io=" + io+ "&attendance_month=" + attendance_month + "&attendance_year=" + attendance_year+ "&todate=" + todate+ "&fromdate=" + fromdate + "&attendance_type=" + attendance_type + "&state=" + state,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&se_id=" + se_id ,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });

	// $("#results").on( "change", "#sales_executive", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	var sales_executive = $("#sales_executive").val();
	// 	var customer_id = $("#customer_id").val();
	// 	// alert(sales_executive);
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&se_id=" + se_id ,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });

	// $("#results").on( "change", "#customer_id", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	var sales_executive = $("#sales_executive").val();
	// 	var customer_id = $("#customer_id").val();
	// 	// alert(sales_executive);
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&df=" + df1+ "&se_id=" + se_id ,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
	// });
}

// $("#fnt").on("click",function()
// {
// 	df1=$("#material_request_filter_input").val();
// 	sales_executive = $("#sales_executive").val();
// 	alert(sales_executive);
// 	io = $("#io").val();
// 	attendance_type = $("#attendance_type").val();
// 	df1 = encodeURI(df1)
// 	displayRecords(50,1);
// })
// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {

	displayRecords(50,1);

});
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
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

	$('#InTimeData').on('show.bs.modal', function (event) {
  		var button = $(event.relatedTarget) // Button that triggered the modal
  		var requesting_id=button.data("id");
		$("#requesting_ajax").attr("data-url","attandance_information_get_ajax_new.php?id="+requesting_id);
		$("#requesting_ajax").click();
	})
</script>

<script type="text/javascript">
	function genReport(){
		var searchName     = $("#searchName").val();
      	searchName     = searchName.trim();
      	// searchName     = encodeURIComponent(searchName.trim());
      	sales_executive = $("#sales_executive").val();
      	df1=$("#material_request_filter_input").val();
      	io=$("#io").val();
      	attendance_type=$("#attendance_type").val();
      	state=$("#state").val();
      	// alert(sales_executive);
      	$.ajax({
			method: "POST",
			url: "attandance_genReport_ajax.php",
			data:{
        		searchName:searchName,
        		sales_executive:sales_executive,
        		df1:df1,
        		io:io,
        		attendance_type:attendance_type,
        		state:state,
			},
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
</script>


<script type="text/javascript">
	function genAttandancePrint(){
		var searchName     = $("#searchName").val();
      	var sales_executive = $("#sales_executive").val();
      	df1=$("#material_request_filter_input").val();
      	io = $("#io").val();
      	attendance_type = $("#attendance_type").val();
      	state = $("#state").val();
      	searchName     = encodeURIComponent(searchName.trim());
     	var myWindow = window.open('print_attandance_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&df='+df1  + '&io=' + io+ '&attendance_type=' + attendance_type + '&state=' + state,'','width=700,height=800');
     		myWindow.print();
     	// setTimeout(function () 
		// {
		// 	myWindow.print();
		// 	var ival = setInterval(function() 
		// 	{
		// 	    myWindow.close();
		// 	    clearInterval(ival);
		// 	},1000);
		// }, 500);
    }
</script>

</body>
</html>