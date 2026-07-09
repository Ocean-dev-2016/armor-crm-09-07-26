<?php 
$page_id=583;$page_slug='future_followup_manage';
$ctable 	= "followup";
$ctable1 	= "visitor";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = "Followup Pending Report";
$page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>"followup_pending_report_manage.php","title"=>$page_title));
include("connect.php");

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
									<div class="row filter_list">
										<?php if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0){ ?>
											<div class="col-md-3 col-xs-3 col-sm-3">
								  		<label>Filter By Date:</label>
								  		<div class="input-group">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
												<i class="fa fa-calendar"></i>
											</span>
										
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
								        </div>
                                    </div>

										<div class="col-md-2">
									       <div class="form-group">
									        <label>Search By Customer Name</label>	
											    <select class="form-control" name="executive" id="executive"  onchange="searchByExecutive(this.value);" >
												    <option value="">Select Customer</option>
													<?php 
		
														$executive_r = $db->rp_getData("executive","*","isDelete=0 GROUP By cname","",0);
														if($executive_r){
														while($executive_d = mysqli_fetch_array($executive_r)){
														?>
													        <option value="<?php echo $executive_d['id']; ?>"><?php echo $executive_d['cname']; ?></option>
													    <?php
															}
														}
														?>
												</select>
											</div>
										</div>
										<?php } ?>

										<?php if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0){ ?>
										<div class="col-md-3">
									       <div class="form-group">
									        <label>Search By Sales Officer Name</label>	
											    <select class="form-control" name="sales_executive" id="sales_executive"  onchange="searchBysalesExecutive(this.value);" >
												    <option value="">Select Sales Officer</option>
													<?php 
		
														$executives_r = $db->rp_getData("sales_executive","*","isDelete=0","",0);
														if($executives_r){
														while($executive_d = mysqli_fetch_array($executives_r)){
														?>
													        <option value="<?php echo $executive_d['id']; ?>"><?php echo $executive_d['name']; ?></option>
													    <?php
															}
														}
														?>
												</select>
											</div>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">
											<button type="button" class="btn green-haze excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
											<button type="button" class="btn print" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genfollowuppendingPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
										</div>
										<?php } ?>
										</div>
										<div class="row">
										<div class="col-md-6" style="margin-top:10px">
											<form>
												<div class="form-group">
												<label>Search By Mobile : &nbsp;</label>
													<input type="text" class="form-control input-medium" name="searchName" id="searchName" value="" placeholder="Search Here"/>
													<!-- <span class="form-group-btn">
														<input class="btn btn-success" type="submit" value="search">
													</span>
													<span class="form-group-btn">
														<input class="btn btn-danger" type="button" value="clear" onClick="clearSearchByName();">
													</span> -->
												</div>
											</form>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top: 10px;">
										<form class="form-inline" role="form" onSubmit="return searchByName();">
												<div class="form-group">
													<input class="btn red-haze btn-sm" style="padding:10px 20px 10px 20px ;" type="submit" value="search">
													<input class="btn green-haze btn-sm" style="padding:10px 20px 10px 30px ;" type="button" value="clear" onClick="clearSearchByName();">
												</div>
										</form>	
								    </div>      
										<!-- <div class="col-md-3 col-xs-3 col-sm-3" style="margin-top:10px">
								  		<label>Filter By Date:</label>
								  		<div class="input-group">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
												<i class="fa fa-calendar"></i>
											</span>
										
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
								        </div>
                                    </div>
 -->
										<!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<button type="button" class="btn green-haze excel" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
											<button type="button" class="btn print" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genComplainPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
										</div> -->
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
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">

var data_url = "followup_pending_get_ajax.php";
var searchName="";
var df="";
var reference_media_id="";
var executive="";
var sales_executive="";

function getmedia(id){
	reference_media_id = id;
	displayRecords(100,1);
	return false;
}

function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}

function searchByExecutive(id){
	executive = id;
	displayRecords(100,1);
	return false;
}
function searchBysalesExecutive(id){
	sales_executive = id;
	displayRecords(100,1);
	return false;
}

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

// function InitDatePickerSupport(StartFilterDate, EndFilterDate) {
// 	$("#quick_stock_adjustment_filter_input").val(StartFilterDate.format('DD-MM-YYYY') + ' to ' + EndFilterDate.format('DD-MM-YYYY'));
// 	df=encodeURIComponent($("#quick_stock_adjustment_filter_input").val());
// 	displayRecords(100,1);
// }

$(".clearBtnFilter").on('click',function(){
			df="";
			$("#quick_stock_adjustment_filter_input").val("");
			displayRecords(100,1);
		});

function clearSearchByName(){
	searchName = "";
	reference_media_id="";
	executive="";
	sales_executive="";
	df ="";
	$("#searchName").val("");
	$("#reference_media_id").val("");
	$("#executive").select2("val","");
	$("#sales_executive").select2("val","");
	displayRecords(100,1);
}
$(".filterBtn").on("click",function()
{
	df=$("#material_request_filter_input").val();
	df = encodeURI(df)
	displayRecords(100,1);
})

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
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords  + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive=" + executive + "&sales_executive=" + sales_executive  + "&df=" + df ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive=" + executive + "&sales_executive=" + sales_executive  + "&df=" + df ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive=" + executive + "&sales_executive=" + sales_executive  + "&df=" + df ,{"page":page}, function(){ //get content from PHP page
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
 $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
});
</script>
<script type="text/javascript">
	function genfollowuppendingPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
		executive=$("#executive").val();
		sales_executive=$("#sales_executive").val();
     	var myWindow = window.open('print_followup_pending_report_ajax.php?searchName='+searchName+ "&ToDate=" + df + "&executive=" + executive + "&sales_executive=" + sales_executive,'','width=700,height=800');
     	myWindow.print();
    }
</script>
</body>
</html>
</html>