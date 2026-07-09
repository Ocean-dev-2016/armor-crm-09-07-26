<?php
$page_id=5909;$page_slug='manage_notification';
include("connect.php");

$ctable 	= "notification";
$ctable1 	= "Notification";
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;

$sales_executive="";
if(isset($_REQUEST['submit'])){
	$disp_count = $_REQUEST['disp_count'];
	for($i=1;$i<=$disp_count;$i++){
		$p_id 			= $_REQUEST['p_id'.$i];
		$display_order	= $_REQUEST['disp'.$i];
		if($p_id>0){
			$rows 	= array("display_order"=>$display_order);
			$where	= "id=".$p_id;
			$db->rp_update($ctable,$rows,$where);
		}
	}
	$db->rp_location("manage_".$ctable.".php?msg=updated");
}
if($_REQUEST['mode']=="all"){
	$status = 0;
	$rows 	= array(
				"isActive"	=> 0,
			);
	$where	= "isActive='1'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=updated");
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="s"){
	$status = 0;
	$rows 	= array(
				"isActive"	=> 0,
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=updated");
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $page_title; ?> | <?php echo ADMINTITLE; ?></title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<?php include("include_css.php"); ?>
<link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
<style>
input[type=number]::-webkit-outer-spin-button,  input[type=number]::-webkit-inner-spin-button {
 -webkit-appearance: none;
 margin: 0;
}
input[type=number] {
	-moz-appearance:textfield;
}
</style>
</head>
<body class="skin-black">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo ADMINSITEURL;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="portlet box blue">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cog"></i>Notification</div>
                                        <div class="tools">
                                            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
										<div class="table-toolbar">
												<div class="row">
													<div class="col-md-4">
														<?php
															echo $db->getAddButton($ctable);
														?>	
													</div>
													
													
												<?php	if($rights['update_flag']==1)
													{ ?>

													<div class="col-md-4 pull-right">
														<label for="sales_executive">Search</label>
														<input onkeydown="return saleex();" class="form-control" type="text" name="searchName" id="searchName" placeholder="Search By Description">
													</div>

													<div class="col-md-4 pull-right">
														<label for="sales_executive">Sales Officer</label>
															<select name="sales_executive" id="sales_executive" onchange="return saleex();" class="form-control">	
																<option value="">--Select Sales Officer--</option>

																<?php
																$sales_r = $db->rp_getData("sales_executive","*","isdelete=0 AND isactive=1");
																if(mysqli_num_rows($sales_r)>0){
																while($sales_d = mysqli_fetch_array($sales_r)){
																?>
																<option value="<?php echo $sales_d['id']; ?>" <?php if($sales_d['id']==$sales_executive){?> selected <?php } ?>><?php echo $sales_d['name']; ?></option>
															<?php
															}
															}
															?>

															</select>
													</div>
													<?php } ?>
												</div>
											</div>
										<div class="tabbable-custom ">
										<?php
							if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="1"){
							?>
							<div class="alert alert-success">
								<button class="close" data-close="alert"></button>
								<span>Notification Add Successfully!!. </span>
							</div>
							<?php
							}else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="2"){
							?>
								<div class="alert alert-success">
									<button class="close" data-close="alert"></button>
									<span>Notification Added Failed!! Try again. </span>
								</div>
							<?php
							}else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="3"){
							?>
								<div class="alert alert-success">
									<button class="close" data-close="alert"></button>
									<span>Notification Updated Successfully. </span>
								</div>    
							<?php
							}
							else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="4"){
							?>
								<div class="alert alert-success">
									<button class="close" data-close="alert"></button>
									<span>Notification Updated Failed!! Try Again. </span>
								</div>    
							<?php
							}
							else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="5"){
							?>
								<div class="alert alert-success">
									<button class="close" data-close="alert"></button>
									<span>Notification Deleted Successfully. </span>
								</div>    
							<?php
							}
							else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="6"){
							?>
								<div class="alert alert-success">
									<button class="close" data-close="alert"></button>
									<span>Notification Delete Failed!! Try Again. </span>
								</div>    
							<?php
							}
							else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="7"){
							?>
								<div class="alert alert-success">
									<button class="close" data-close="alert"></button>
									<span>Notification Resend Successfully!!. </span>
								</div>    
							<?php
							}
							else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="8"){
							?>
								<div class="alert alert-success">
									<button class="close" data-close="alert"></button>
									<span>Notification Resend Failed!! Try Again. </span>
								</div>    
							<?php
							}
							?>
                                            <ul class="nav nav-tabs ">
                                                <li class="active">
                                                    <a href="#tab_0" data-toggle="tab"> All </a>
                                                </li>
                                                <li>
													<a  href="#tab_1" data-toggle="tab" aria-expanded="false"> Today</a>                                                    
                                                </li>
                                                <li >
                                                    <a  href="#tab_2" data-toggle="tab" aria-expanded="false"> Pending </a>
                                                </li>
												
											</ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_0">
                                                  
												  <div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
												<div id="results"></div>
												   
                                                </div>
												
                                                <div class="tab-pane" id="tab_1">
                                                 
												  <div class="loading-div-today" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
												  <div id="results_today"></div> 
												   
                                                </div>
												
												
                                                <div class="tab-pane" id="tab_2">
                                                  
												  <div class="loading-div-pending" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
												  <div id="results_pending"></div> 
												   
                                                </div>
											</div>
                                        </div>
									</div>
                </div>
		</div>
	</div>
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo ADMINSITEURL;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> </h1>
			</div>
		</div>
	</div>
</div>

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script type="text/javascript">
//var searchName="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";
var data_url_today = "<?php echo $ctable ?>_today_get_ajax.php";
var data_url_pending = "<?php echo $ctable ?>_pending_get_ajax.php";

function saleex()
	{	
		console.log("sdf");
		var sales=$("#sales_executive").val();
		var numRecords  = $("#numRecords").val();
		var numRecords1  = $("#numRecords1").val();
		var numRecords2  = $("#numRecords2").val();
		var searchName 	= encodeURIComponent($("#searchName").val());
		var page = $(this).attr("data-page");
		$("#results").load(data_url+"?show=" + numRecords + "&sales="+ sales + "&searchName="+ searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		$("#results_today").load(data_url_today+"?show=" + numRecords1 + "&sales="+ sales + "&searchName="+ searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div-today").hide(); //once done, hide loading element
			loadDataTable();
		});
		$("#results_pending").load(data_url_pending+"?show=" + numRecords2 + "&sales="+ sales + "&searchName="+ searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div-pending").hide(); //once done, hide loading element
			loadDataTable();
		});
	}
function loadDataTable(){
	
	// $('#example1').DataTable({
	// 	"bPaginate": false,
	// 	"bFilter": false,
	// 	"bInfo": false,
	// 	"bAutoWidth": false, 
	// 	"aaSorting": [[ 0, "desc" ]],
	// 	"aoColumns": [
	// 		  { "sWidth": "10%" }, 
	// 		  { "sWidth": "35%" },
	// 		  { "sWidth": "40%" },
	// 		  { "sWidth": "30%" },
	// 		  { "sWidth": "20","bSortable": false },
	// 		  { "sWidth": "5%","bSortable": false }
	// 		]
	// });
}

function displayRecords(numRecords) {
			//var searchName	= $("#searchName").val();
			//searchName 	= encodeURIComponent(searchName.trim());
			var numRecords  = $("#numRecords").val();
			$("#results" ).html("");
			$("#results" ).load( "notification_get_ajax.php?show=" + numRecords ); //load initial records
			
			setTimeout(function(){
				$('#selectAll').click(function(event) {  //on click
					$(".checkbox1").prop('checked', $(this).prop("checked"));
					
				});
				$('.checkbox1').click(function(){
					if($('#selectAll').is(':checked')){
						$("#selectAll").prop('checked', $(this).prop("checked"));
					}
				});
			},500);
			
			//executes code below when user click on pagination links
			$("#results").on( "click", ".paging_simple_numbers a", function (e){
				e.preventDefault();
				var numRecords  = $("#numRecords").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load("notification_get_ajax.php?show=" + numRecords ,{"page":page}, function(){ //get content from PHP page
					$(".loading-div").hide(); //once done, hide loading element
					
				});
				// $('#example1').DataTable({
				// 	"bPaginate": false,
				// 	"bFilter": false,
				// 	"bInfo": false
				// });
				setTimeout(function(){
					$('#selectAll').click(function(event) {  //on click
						$(".checkbox1").prop('checked', $(this).prop("checked"));
						
					});
					$('.checkbox1').click(function(){
						if($('#selectAll').is(':checked')){
							$("#selectAll").prop('checked', $(this).prop("checked"));
						}
					});
				},500);
				
			});
			$("#results").on( "change", "#numRecords", function (e){
				e.preventDefault();
				var numRecords  = $("#numRecords").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load("ajax_get_pricelist.php?show=" + numRecords + "&searchEmail=" + searchEmail,{"page":page}, function(){ //get content from PHP page
					$(".loading-div").hide(); //once done, hide loading element
					
				});
				// $('#example1').DataTable({
				// 	"bPaginate": false,
				// 	"bFilter": false,
				// 	"bInfo": false
				// });
				setTimeout(function(){
					$('#selectAll').click(function(event) {  //on click
						$(".checkbox1").prop('checked', $(this).prop("checked"));
						
					});
					$('.checkbox1').click(function(){
						if($('#selectAll').is(':checked')){
							$("#selectAll").prop('checked', $(this).prop("checked"));
						}
					});
				},500);
			});
		}
		
		// used when user change row limit
		function changeDisplayRowCount(numRecords) {
			displayRecords(numRecords);
		}
		
		$(document).ready(function() {
			displayRecords(25);
			
		});
		/*function udpateAll_conf(){
			$('#frm').trigger('submit');
		}*/
		function delAll_conf(){
			var r = confirm("Are you sure want to delete selected <?php echo $ctable; ?>?");
			if(r){
				$('#del_pro').trigger('click');
			}
		}
		function del_conf(id){
			var r = confirm("Are you sure you want to delete?");
			if(r){
				window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
			}
		}

// Load Data For Today Notification
function loadDataTable_today(){
	// $('#datatable_today').dataTable({
	// 	"bPaginate": false,
	// 	"bFilter": false,
	// 	"bInfo": false,
	// 	"bAutoWidth": false, 
	// 	"aoColumns": [
	// 		  { "sWidth": "5%" }, 
	// 		  { "sWidth": "30%" }, 
	// 		  { "sWidth": "30%" },
	// 		  { "sWidth": "10%" },
	// 		  { "sWidth": "5%" },
	// 		  { "sWidth": "20%","bSortable": false }
	// 		]
	// });
}
function displayRecords_today(numRecords1) {
	
	
	$("#results_today" ).html("");
	$("#results_today" ).load(data_url_today+"?show=" + numRecords1,function(){
		loadDataTable_today();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results_today").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords1  = $("#numRecords1").val();
		$(".loading-div-today").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results_today").load(data_url_today+"?show=" + numRecords1,{"page":page}, function(){ //get content from PHP page
			$(".loading-div-today").hide(); //once done, hide loading element
			loadDataTable_today();
		});
		
	});
	$("#results_today").on( "change", "#numRecords1", function (e){
		e.preventDefault();
		var numRecords1  = $("#numRecords1").val();
		$(".loading-div-followup").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results_today").load(data_url_today+"?show=" + numRecords1,{"page":page}, function(){ //get content from PHP page
			$(".loading-div-today").hide(); //once done, hide loading element
			loadDataTable_today();
		});
		
	});
	
}
function changeDisplayRowCount1(numRecords1) {
	displayRecords_today(numRecords1);
	
}

$(document).ready(function() {
	displayRecords_today(25);
	
});

// Load Data For Pending Notification

function loadDataTable_pending(){
	// $('#datatable_pending').dataTable({
	// 	"bPaginate": false,
	// 	"bFilter": false,
	// 	"bInfo": false,
	// 	"bAutoWidth": false, 
	// 	"aoColumns": [
	// 		  { "sWidth": "5%" }, 
	// 		  { "sWidth": "30%" }, 
	// 		  { "sWidth": "30%" },
	// 		  { "sWidth": "%" },
	// 		  { "sWidth": "5%" },
	// 		  { "sWidth": "20%","bSortable": false }
	// 		]
	// });
}
function displayRecords_pending(numRecords2) {
	
	var numRecords2  = $("#numRecords2").val();
	$("#results_pending" ).html("");
	$("#results_pending" ).load(data_url_pending+"?show=" + numRecords2,function(){
		loadDataTable_pending();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results_pending").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords2  = $("#numRecords2").val();
		$(".loading-div-pending").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results_pending").load(data_url_pending+"?show=" + numRecords2,{"page":page}, function(){ //get content from PHP page
			$(".loading-div-pending").hide(); //once done, hide loading element
			loadDataTable_pending();
		});
		
	});
	$("#results_pending").on( "change", "#numRecords2", function (e){
		e.preventDefault();
		var numRecords2  = $("#numRecords2").val();
		$(".loading-div-pending").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results_pending").load(data_url_pending+"?show=" + numRecords2,{"page":page}, function(){ //get content from PHP page
			$(".loading-div-pending").hide(); //once done, hide loading element
			loadDataTable_pending();
		});
		
	});
	
}
function changeDisplayRowCount2(numRecords2) {
	displayRecords_pending(numRecords2);
	
}

$(document).ready(function() {
	displayRecords_pending(25);
	
});
</script>

</body>
</html>
