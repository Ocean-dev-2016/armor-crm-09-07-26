<?php
$page_id=560;$page_slug='page_order';
$ctable 	= "inward_store";
$ctable1 	= "Inward Store";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Store"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");
$FromDate="";
$ToDate="";
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
									<div class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
										<div class="form-inline" role="form">
										   <div class="form-group">
												<label>Filter By Inward Date : &nbsp;</label>
												<input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
											</div>
											<div class="form-group">
												 <label>&nbsp;&nbsp;</label>
												<input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
											</div>
											<div class="form-group">
														<input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
											</div>
										</div>
									</div>
									<div class="col-md-6 col-xs-6 col-sm-6 " style="margin-top:10px">
										<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
											<div class="form-group">
												<label>Search By Vendor Name: &nbsp;</label>
												<input type="text" class="form-control input-small" name="searchName" id="searchName" value="" />
											</div>
											<div class="form-group">
												<input class="btn btn-danger btn-sm" type="submit" value="search">
											</div>
											<div class="form-group">
													<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
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
					<div class="btn-group">
										<a class="btn sbold blue-ebonyclay" href='inward_store_crud.php?mode=add'> Add New
										<i class="fa fa-plus"></i>
										</a>
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


<div id="inward_store_view" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-gift"></i>View Inward Store Information</div>
						<div class="tools">
							<a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i></a>
							<a href="javascript:;"  data-original-title="" title="" data-dismiss="modal" style="color:white;"><i class="fa fa-close"></i></a>
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
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
var searchName="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";

$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
var ToDate="";
var FromDate="";

var status="";
var type="";
var flag="1";
$('#inward_store_view').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","inward_store_info_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();	
})
function searchByName(){
	searchName = $("#searchName").val();
	displayRecords(100,1);
	return false;
}
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
function getByDate() {
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	if(FromDate<=ToDate)
		
	{
		displayRecords(100,1);
	}
	else
	{
		alert("From Date Should Be Less Than To Date");
	}
	
}
function getSubCat(cid){
		status=cid;
		displayRecords(100,1);
}
function getType(tid){
		type=tid;
		displayRecords(100,1);
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
			  { "sWidth": "20%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status=" + status + "&type=" + type+ "&flag=" + flag,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&ToDate=" + ToDate + "&FromDate=" + FromDate,{"page":page}, function(){ //get content from PHP page
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
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}

function confirmChange(id) 
{
	var r=confirm("Are you sure to forward order to production department?");
	if(r)
	{
		window.location.href="inward_store_crud.php?mode=isActive&id="+id+"&status=1";
	}
	
   
}

function genReport(inward_id){
	var rc = encodeURIComponent($("#print_info").html());
	$.ajax({
		type: "POST",
		url: "inward_store_ajax_genReport.php",
		data: 'inward_id='+inward_id+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					window.location.href=result;
				},1500);
			}
	});
}
function printPDF() 
{
	 var myWindow = window.open('','','width=800,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}
</script>
</body>
</html>