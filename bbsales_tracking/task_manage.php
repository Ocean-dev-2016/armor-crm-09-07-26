<?php
$page_id=582;$page_slug='task_manage';
$ctable 	= "executive";
$ctable1 	= "executive";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Today's Task";
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
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
										<div class="col-md-4" hidden>
										<br/>
											<div class="input-group input-large pull-left">
												<input class="form-control datetimerange-picker-input" id="quick_stock_adjustment_filter_input" value="" placeholder="Select Date Range" type="text">
												<span class="input-group-addon datetimerange-picker-btn">
													<i class="fa fa-calendar"></i>
												</span>
												<span class="input-group-btn">
													<button class="btn blue filterBtn" type="button">Filter</button>
												</span>
												<span class="input-group-btn">
													<button class="btn red clearBtnFilter" type="button">Clear</button>
												</span>
											</div>									
										</div>

										<div class="col-md-2">
										<div class="form-group">
										<label>Search By Refrence Media</label>	
											<select class="form-control" name="reference_media_id" id="reference_media_id"  onchange="getmedia(this.value)">
												<p class="help-block"></p>
												<option value="">Select Refrence Media</option>
												<?php
													$projects=$db->rp_getData("reference_media","*","isDelete=0 AND isActive=1");
													if($projects){
														while ($project=mysqli_fetch_assoc($projects)) {
														?>
														<option value="<?php echo $project['id']; ?>" <?php if($project['id']==$reference_media_id){ echo "selected"; } ?>><?php echo $project['name']; ?></option>
														<?php
														}
													}
													?>
											</select>
										</div>
										</div>

										<?php if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0){
										?>
										<div class="col-md-3">
											<div class="form-group">
											<label>Search By Executive</label>	
												<select class="form-control" name="executive" id="executive"  onchange="searchByExecutive(this.value);" >
													<p class="help-block"></p>
														<option value="">Select Executive</option>
														<?php 
														$executives = $db->rp_getData("executive","*","isDelete=0","",0);
														if($executives){
														while($executive = mysqli_fetch_array($executives)){
														?>
														<option value="<?php echo $executive['id']; ?>"><?php echo $executive['cname']; ?></option>
														<?php
															}
														}
														?>
												</select>
											</div>
										</div>
										<?php } ?>

										<div class="col-md-4 pull-right">
											<form action="#" onSubmit="return searchByName();">
												<label> Search</label>
												<div class="input-group">
													<input type="text" class="form-control" name="searchName" id="searchName" value="" placeholder="Enter Name Or Phone"/>
													<span class="input-group-btn">
														<input class="btn btn-success" type="submit" value="search">
													</span>
													<span class="input-group-btn">
														<input class="btn btn-danger" type="button" value="clear" onClick="clearSearchByName();">
													</span>
												</div>
											</form>
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
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var data_url = "task_get_ajax.php";
var searchName="";
var df="";
var reference_media_id="";
var executive_id="";

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
	executive_id = id;
	displayRecords(100,1);
	return false;
}

$(".datetimerange-picker-btn").on("click",function(){
		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
	});
	$(".customer-select").select2();
	//var StartFilterDate = moment().subtract(29, 'days');
   // var EndFilterDate = moment();
	$(".datetimerange-picker-input").daterangepicker({"dateFormat":"dd-mm-yy ",timePicker:false,
	 startDate:  moment().subtract(29, 'days'),
     endDate: moment(),
	 locale: {
		      format: 'DD-MM-YYYY'
		},
	ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}}, InitDatePickerSupport);

function InitDatePickerSupport(StartFilterDate, EndFilterDate) {
	$("#quick_stock_adjustment_filter_input").val(StartFilterDate.format('DD-MM-YYYY') + ' to ' + EndFilterDate.format('DD-MM-YYYY'));
	df=encodeURIComponent($("#quick_stock_adjustment_filter_input").val());
	displayRecords(100,1);
}

$(".clearBtnFilter").on('click',function(){
			df="";
			$("#quick_stock_adjustment_filter_input").val("");
			displayRecords(100,1);
		});

function clearSearchByName(){
	searchName = "";
	reference_media_id="";
	executive_id="";
	$("#searchName").val("");
	$("#reference_media_id").val("");
	$("#executive_id").val("");
	displayRecords(100,1);
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
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&ToDate=" + df + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive_id=" + executive_id ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&ToDate=" + df + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive_id=" + executive_id ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&ToDate=" + df + "&reference_media_id=" + reference_media_id + "&searchName=" + searchName + "&executive_id=" + executive_id,{"page":page}, function(){ //get content from PHP page
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
</body>
</html>
</html>