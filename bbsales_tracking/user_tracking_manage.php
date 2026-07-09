<?php
$page_id=529;$page_slug='page_user_tracking';
$etable 	= "employee";
$ctable 	= "user_tracking";
$ctable1 	= "User Tracking";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
include("connect.php");
if($rights['view_flag']!=1)
{
		$db->rp_location('access_denied.php?msg=insert_access_denied');
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
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
				<div class="col-sm-12">
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<form action="#" onSubmit="return searchByName();">
										<table class="table" style="margin-bottom:0;">
											<tr>
												<td>Search By Table Name : </td>
												<td>
													<input type="text" name="searchName" id="searchName" value="" />
												</td>
												<td>
													<input class="btn btn-danger btn-sm" type="submit" value="search">
													<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
												</td>
											</tr>
										</table>
									</form>
								</div>
								<div class="col-md-6">
									<div class="col-md-4">
										<select class="form-control" name="user_id" id="user_id" onChange="getSubCat(this.value);">
										<option>--Select User--</option>
											<?php 
												$user_d=$db->rp_getData('production_erp',"*","1=1","",0);
												while($user_r=mysqli_fetch_assoc($user_d))
												{
													?>
													<option <?php echo ($user_id==$user_r['id'])?"selected":"" ; ?> value="<?php echo $user_r['id']?>">
													<?php echo $user_r['name'];?>
													</option>
													<?php
												}
											?>
										</select>
									</div>
									<div class="col-md-5">
										<select class="form-control" name="user_type" id="user_type" onChange="getUserType(this.value);">
										<option>--Select User Type--</option>
											<?php 
												$user_type_d=$db->rp_getData('admin_type',"*","1=1","",0);
												while($user_type_r=mysqli_fetch_assoc($user_type_d))
												{
													?>
													<option <?php echo ($user_type==$user_type_r['id'])?"selected":"" ; ?> value="<?php echo $user_type_r['id']?>">
													<?php echo $user_type_r['name'];?>
													</option>
													<?php
												}
											?>
										</select>
									</div>
									<div class="col-md-3">
										<select class="form-control" name="activity_type" id="activity_type" onChange="getActivityType(this.value);">
										<option>--Select Activity--</option>
											<option value="insert">insert</option>
											<option value="update">update</option>
											<option value="delete">delete</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<table class="table" style="margin-bottom:0;">
											<tr>
												<td><input type="text"  name="FromDate" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
												</td>
												
												<td>
													<input type="text"  name="ToDate" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
												</td>											
												<td>
												<input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
												</td>
											</tr>
										</table>
																		
								</div>	
								<div class="col-md-6">
								<a class="btn btn-danger btn-sm pull-right" onClick="return delete_all();" name="delete_records" title="Delete All">Delete All</a>
								</div>
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
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">

var searchName="";
var ToDate="";
var FromDate="";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
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
var data_url = "<?php echo $ctable; ?>_get_ajax.php";
function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}
var user_id="";
var user_type1="";
var activity_type1="";
function getSubCat(cid){
		user_id=cid;
		displayRecords(100,1);
}

function getUserType(user_type){
		user_type1=user_type;
		displayRecords(100,1);
}
function getActivityType(activity_type){
		activity_type1=activity_type;
		//alert(activity_type1);
		displayRecords(100,1);
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
	displayRecords(100,1);
}
function delete_all(){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_get_ajax.php?mode=delete';
	}
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
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "10%","bSortable": false },
			  { "sWidth": "20%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&user_id=" + user_id+ "&user_type1=" + user_type1 + "&activity_type1=" + activity_type1  + "&ToDate=" + ToDate + "&FromDate=" + FromDate,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&user_id=" + user_id+ "&user_type1=" + user_type1 + "&activity_type1=" + activity_type1  + "&ToDate=" + ToDate + "&FromDate=" + FromDate,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&user_id=" + user_id+ "&user_type1=" + user_type1 + "&activity_type1=" + activity_type1  + "&ToDate=" + ToDate + "&FromDate=" + FromDate,{"page":page}, function(){ //get content from PHP page
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