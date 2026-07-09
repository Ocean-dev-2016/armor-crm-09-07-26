<?php
$page_id=560;$page_slug='page_order';
$ctable 	= "dispatch";
$ctable1 	= "Dispatch";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>$ctable1),array("link"=>"combine".$ctable."_manage.php","title"=>"Manage ".$ctable1));
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
                               
									 <div class="col-md-12" style="margin-top:20px">

								  <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
								   <div class="form-group">
										<label>Search By Name: &nbsp;</label>
										<input type="text" placeholder="Enter Name OR Company Name" class="form-control input-large" name="searchName" id="searchName" value="" />
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
				<div class="col-md-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-sm-12">
					<div class="portlet light">
							<div class="table-toolbar">
								<div class="row">
									<div class="col-md-1">
										<div class="btn-group">
											<a class="btn sbold blue-ebonyclay" href="<?php echo "dispatch_create.php"; ?>"> Add New
												<i class="fa fa-plus"></i>
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="portlet light">
								<div class="portlet-body">
									<div class="tabbable-custom nav-justified">
										<ul id="myTabs" class="nav nav-tabs ">
											<li class="active">
												<a href="#tab_today" data-toggle="tab" aria-expanded="false"> Today's & Urgent Dispatch </a>
											</li>
											
											<li>
												<a href="#tab_all" data-toggle="tab" aria-expanded="false"> All Dispatch </a>
											</li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active" id="tab_today">
												<div class="row">
													<div class="col-sm-12">
														<div class="row">
															<div class="col-md-12">
																<div id="results1">
																</div>
															</div>
														</div>
													</div>
												</div>
												
											</div>
											
											<div class="tab-pane" id="tab_all">
												<div class="row">
													<div class="col-sm-12">
														<div class="row">
															<div class="col-md-12">
																<div id="results3">
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
						</div>
				
				</div>
			</div>
		</div>
	</div>
	
</div>
<div id="dispatchModal" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="">
				<div class="modal-body portlet box blue">
					<div class="portlet-title">
						<div class="caption">
						View Dispatch Information </div>
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


</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">

	$('#dispatchModal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var requesting_id=button.data("id");
	//alert("sd"+requesting_id);
		$("#requesting_ajax").attr("data-url","dispatch_information_get_ajax.php?id="+requesting_id);
			$("#requesting_ajax").click();
	})
	var data_url = "combine_dispatch_all_get_ajax.php";
	var data_url_today = "combine_dispatch_get_ajax.php";
	
	
	
	
	function getSubCat(cid){
	status=cid;
	displayClassRecords(100,1);
	displayClassRecordsToday(100,1);
	}
	
	function loadDataTable(){
	$('#datatable1').dataTable({
	"bPaginate": false,
	"bFilter": false,
	"bInfo": false,
	"bAutoWidth": false,
	"aoColumns": [
	{ "sWidth": "5%" },
	{ "sWidth": "30%" },
	{ "sWidth": "20%" },
	{ "sWidth": "20%" },
	{ "sWidth": "20%" },
	{ "sWidth": "10%","bSortable": false }
	]
	});
	}
	function displayClassRecords(numRecords) {
	
	$("#results3" ).html("");
	$("#results3" ).load( data_url+"?show=" + numRecords + "&status=" + status,function(){
	loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results3").on( "click", ".paging_simple_numbers a", function (e){
	e.preventDefault();
	var numRecords  = $("#numRecords").val();
	$(".loading-div").show(); //show loading element
	var page = $(this).attr("data-page"); //get page number from link
	$("#results3").load(data_url+"?show=" + numRecords + "&status=" + status,{"page":page}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	loadDataTable();
	});
	
	});
	$("#results3").on( "change", "#numRecords", function (e){
	e.preventDefault();
	var numRecords  = $("#numRecords").val();
	$(".loading-div").show(); //show loading element
	var page = $(this).attr("data-page"); //get page number from link
	$("#results3").load(data_url+"?show=" + numRecords +  "&status=" + status,{"page":page}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	loadDataTable();
	});
	
	});
	}
	////// today recoedssssssss
	function loadDataTableTOday(){
	$('#datatable').dataTable({
	"bPaginate": false,
	"bFilter": false,
	"bInfo": false,
	"bAutoWidth": false,
	"aoColumns": [
	{ "sWidth": "5%" },
	{ "sWidth": "10%" },
	{ "sWidth": "30%" },
	{ "sWidth": "20%" },
	{ "sWidth": "20%" },
	{ "sWidth": "10%","bSortable": false }
	]
	});
	}
	function displayClassRecordsToday(numRecords) {
	
	$("#results1" ).html("");
	$("#results1" ).load( data_url_today+"?show=" + numRecords +  "&status=" + status,function(){
	loadDataTableTOday();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results1").on( "click", ".paging_simple_numbers a", function (e){
	e.preventDefault();
	var numRecords  = $("#numRecords").val();
	$(".loading-div").show(); //show loading element
	var page = $(this).attr("data-page"); //get page number from link
	$("#results1").load(data_url_today+"?show=" + numRecords + "&status=" + status,{"page":page}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	loadDataTableTOday();
	});
	
	});
	$("#results1").on( "change", "#numRecords", function (e){
	e.preventDefault();
	var numRecords  = $("#numRecords").val();
	$(".loading-div").show(); //show loading element
	var page = $(this).attr("data-page"); //get page number from link
	$("#results1").load(data_url_today+"?show=" + numRecords + "&status=" + status,{"page":page}, function(){ //get content from PHP page
	$(".loading-div").hide(); //once done, hide loading element
	loadDataTableTOday();
	});
	
	});
	}
	// used when user change row limit
	function changeDisplayRowCount(numRecords) {
	displayClassRecords(numRecords, 1);
	displayClassRecordsToday(numRecords, 1);
	}
	$(document).ready(function() {
	displayClassRecords(100,1);
	displayClassRecordsToday(100,1);
	});
	function del_conf(id,quotation_id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
	window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id +'&quotation_id='+quotation_id;
	}
	}
	function confirmChange(id)
	{
	var r=confirm("Are you sure to forward order to production department?");
	if(r)
	{
	window.location.href="orders_crud.php?mode=isActive&id="+id+"&status=1";
	}
	}
	function genReport(id){
	var rc = encodeURIComponent($("#print_info").html());
	
	$.ajax({
	type: "POST",
	url: "dispatch_ajax_genReport.php",
	data: 'id='+id,
	beforeSend: function() {
	$(".transCover").fadeIn(800);
	$('.preloader').fadeIn('slow');
	},
	success: function(result){ //alert(result);
	setTimeout(function(){
	$(".transCover").fadeOut(100);
	$('.preloader').fadeOut('slow');
	// $("#loading-modal").modal('hide');
	//window.location.href=result;
	window.open(result,'_blank');
	},1500);
	}
	});
	}
	function printPDF(id)
	{
	var printWindow = window.open('dispatch_format.php?id='+id+"&p=1",'','width=800,height=800')
	}
</script>
</body>
</html>