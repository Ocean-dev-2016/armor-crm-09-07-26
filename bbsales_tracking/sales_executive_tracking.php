<?php
$page_id=562;$page_slug='page_category';
$ctable 	= "salesexecutive_tracking";
$ctable1 	= "Sales Officer Tracking";
$main_page 	= $ctable;
$page 		= "dispatch_manage";
include("connect.php");
$sales_name=$db->rp_getValue("sales_executive","name","id=".$_REQUEST['id']."",0);
$page_title = "Manage Sales Officer Tracking - ".$sales_name;
$FromDate="";
$ToDate="";
$reg_year=date("Y",strtotime($admin_details['date']));
$curr_year=date("Y");
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
			
				$redirect_url="sales_executive_manage.php";
			
			?>
				<h1><a href="<?php echo $redirect_url;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
									<div class="col-md-12">
													<table cellpadding="2">
														<tr>
															<td>Search By Month & Year Name : &nbsp;&nbsp; &nbsp;&nbsp; 
															</td>
															<td>
																<select style="width:230px !important;" class="form-control" name="month_id" id="month_id" onChange="getMonths(this.value);">
																<option value="">Select Month</option>
																<?php 
																	$months = array("January","February","March","April","May","June","july","August","September","October","November","December");
																	
																	foreach ($months as $key=>$month) {
																		?>
																		<option <?php echo ($month==date("F"))?"selected":"" ; ?> value="<?php echo $key+1;?>"   ><?php echo $month;?></option>
																		<?php
																		
																	}
																	
																?>
																</select>
															</td>
															
															<td>
																<select  style="width:230px !important;margin-left:20px;" class="form-control" name="year_id" id="year_id" onChange="getYears(this.value);">
																<option value="">Select Year</option>
																<?php 
																	
																	for ($i=$curr_year-$reg_year; $i>=0;$i--) {
																		?>
																		<option <?php echo ($i==$curr_year-$reg_year)?"selected":"" ;?>  value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
																		<?php
																		
																	}
																?>
																</select>
															</td>
															<td>
																	<input type="hidden" name="searchName" id="searchName" value="" />
																</td>
														</tr>
													</table>
												</div>
												</div>
												<div class="col-md-6">
												<table class="table" style="margin-bottom:0;">
												<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
														<tr>
															<td><input type="hidden"  name="FromDate" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
															</td>
															
															<td>
																<input type="hidden"  name="ToDate" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
															</td>											
															<td>
															<input class="btn btn-danger btn-sm" type="hidden" value="Filter" onClick="getByDate();">
															</td>
														</tr>
													</table>
																					
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
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">

var month_id="";
var year_id="";
var sales_id="<?php echo $_REQUEST['id'];?>";
var data_url = "salesexecutive_tracking_get_ajax.php";
function getMonths(cid){
		month_id=cid;
		displayRecords(100,1);
}
function getYears(yid){
		year_id=yid;
		displayRecords(100,1);
}
$("#FromDate").click(function (e) {
   $("#year_id").val($("#year_id").children('option:first').val());
   $("#month_id").val($("#month_id").children('option:first').val());
});
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

function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
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
	$("#results" ).load( data_url+"?show=" + numRecords +  "&month_id=" + month_id + "&year_id=" + year_id + "&sales_id=" + sales_id  ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords+ "&month_id=" + month_id + "&year_id=" + year_id  + "&sales_id=" + sales_id,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&month_id=" + month_id + "&year_id=" + year_id  + "&sales_id=" + sales_id,{"page":page}, function(){ //get content from PHP page
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