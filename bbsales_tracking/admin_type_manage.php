<?php
$page_id=404;$page_slug='manage_admin_type';
$ctable 	= "admin_type";
$ctable1 	= "Admin Type";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
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
			<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']!=""){ ?>
				<div class="alert alert-success alert-dismissable"> <i class="fa fa-check"></i>
					<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
					<strong>Success! </strong>
					<?php
						if($_REQUEST['msg']=="inserted"){
							echo $ctable1." detail Added successfully.";
						}else if($_REQUEST['msg']=="updated"){
							echo $ctable1." detail Updated successfully.";
						}else if($_REQUEST['msg']=="deleted"){
							echo $ctable1." Deleted successfully.";
						}
					?>
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-12">
					<div class="portlet light">
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $db->getAddButton($ctable);
										echo $db->getAddApplicationPageButton();
									?>
									<!-- <a class="btn btn-danger btn-sm" href="user_tracking_manage.php" title="User Tracking">User Tracking</a> -->

								</div>
								<div class="col-md-6" hidden="">
									<form action="#" onSubmit="return searchByName();">
										<table class="table" style="margin-bottom:0;">
											<tr>
												<td class="pull-right">Search By Name:
													<input type="text" name="searchName" id="searchName" value="" />
													<input class="btn btn-danger btn-sm" type="submit" value="search">
													<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
												</td>
											</tr>
										</table>
									</form>
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
<script type="text/javascript">
var searchName="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";
function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(50,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	$("#searchName").val("");
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
			  { "sWidth": "5%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "23%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(50,1);
});
</script>
<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
<script>
$(document).ready(function() {       
   $('#datatable_1').dataTable();
});
</script>
</body>
</html>