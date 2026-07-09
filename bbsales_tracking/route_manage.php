<?php
$page_id=631;$page_slug='route_page';
$ctable 	= "my_route";
$ctable1 	= "Sales Route Planning";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");
$sales_id  = isset($_REQUEST['sales_id'])?$_REQUEST['sales_id']:'';
$route_id  = isset($_REQUEST['route_id'])?$_REQUEST['route_id']:'';

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
				<div class="col-xl-12"> 
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				<div class="col-xl-12 ">            <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            </div>
                         </div>
                          <div class="portlet-body">
                            <div class="slimScrollDiv">
								<div class="row">
                     
	                              <!--   <div class="col-md-8 col-xs-8col-sm-8" style="margin-top:10px">
	                                	<form class="form-inline" role="form" onSubmit="return searchByName();">
	                                		<div class="form-group">
	                                			<label>Search By Name Or Email Or Phone: &nbsp;</label>
												<input type="text" placeholder="Search Here" class="form-control input-medium" name="searchName" id="searchName" value="" />
											</div>
										 	<div class="form-group">
												<input class="btn btn-danger btn-sm" type="submit" value="search">
											</div>
										 	<div class="form-group">
												<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
											</div>
										</form>
									</div> -->

	          <div class="col-lg-12" style="margin: 10px 0!important;">
		          <div class="row">

		       	         <div class="col-md-1 col-xs-1 col-sm-1">
								<?php
									// echo $db->getAddButton("route");
								?>		
                         </div>

	                     <!--  <div class="col-md-3 col-xs-3 col-sm-3 pull-left">
								<label>Filter By  Date</label>
								<br/>
								<div class="input-group">
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
						  </div> -->

	                        <div class="col-md-8 col-xs-8 col-sm-8 pull-right">
                                <div class="form-inline" role="form">
                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                       	<div class="form-group">

                                          <input type="text" style="width: 450px!important" placeholder="Search By Person Name / Company Name / Mobile No :  " class="form-control input-large" name="searchName" id="searchName" value="" />

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
													{ ?>
													<li>
														<a name="print" onClick="genSalesExecutivePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
													</li>

													<?php 
													} ?>
													<?php
													if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
													{ ?>
													<li>
														<a class="excel" name="excel" onClick="genSalesExecutiveExcel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
													</li>

													<?php
													}
													?>
												</ul>
											</div>
                                       	</div>
                                       	<div class="form-group">
                                          <a onclick="checkedDataDelete()" class="btn btn-success btn-sm" type="button" value="clear">Delete <span id="check-count"></span></a>
                                       	</div>
                                    </form>
                                </div>
                             </div>

	                      	</div>
		               	</div>

	                    </div>
	                </div>
					<div class="row">
						<div class="col-sm-6 col-sm-offset-3">
							<div class="alert alert-success alert-dismissable " id="alert-msg" style="display:none;margin-top:15px">
								<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
								<p></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>


                    <!-- END Portlet PORTLET-->
                </div>
				<!-- <div class="portlet light"> -->
					<!-- <div class="table-toolbar">
						<div class="row">
							<div class="col-md-6">
								<div class="btn-group">

									<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn sbold blue-ebonyclay dropdown-toggle"> 
										Add NEW<i class="fa fa-plus"></i>
									</button>

									<ul role="menu" class="dropdown-menu">
										<li>
											<a  href="sales_executive_crud.php?type=sales_manager&mode=add" title="Sales Manager"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Regional Sales Manager</span></a>
										
											<a  href="sales_executive_crud.php?type=area_sales_manager&mode=add" title="Area Sales Manager"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Business Development Manager</span></a>
											
											<a  href="sales_executive_crud.php?type=sales_officer&mode=add" title="Area Sales Manager"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Area Sales Manager</span></a>
											
											<a  href="sales_executive_crud.php?type=sales_executive&mode=add" title="Sales Excecutiv"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Sales Excecutive</span></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div> -->
					<div class="portlet-body">
						<div class="tabbable-line">
						 	<!-- <ul class="nav nav-tabs ">
								<li class="active">
									<a href="#result_sales_manager" data-toggle="tab" aria-expanded="false">Regional Sales Manager </a>
								</li>
								<li>
									<a href="#result_are_sales_manager" data-toggle="tab" aria-expanded="false">Business Development Manager</a>
								</li>
								<li>
									<a href="#result_sales_officer" data-toggle="tab" aria-expanded="false">Area Sales Manager</a>
								</li>
								<li>
									<a href="#result_sales_executive" data-toggle="tab" aria-expanded="false">Sales Officer</a>
								</li>
							</ul> -->
							<!-- <div class="tab-content"> -->
								<div class="tab-pane active" id="result_sales_manager">	
									<div class="row">
										<div class="col-sm-12">
											<div class="portlet light">
												<div class="col-md-6">
												</div>
												<div class="portlet-body">
													<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" >
													</div>
													<div id="results_sm"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="result_are_sales_manager">	
									<div class="row">
										<div class="col-sm-12">
											<!-- <div class="portlet light"> -->
												<div class="col-md-6">
												</div>
												<div class="portlet-body">
													<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;"> 
													</div>
													<div id="results_asm"></div>
												</div>
											<!-- </div> -->
										</div>
									</div>
								</div>
								<div class="tab-pane" id="result_sales_officer">	
									<div class="row">
										<div class="col-sm-12">
											<!-- <div class="portlet light"> -->
												<div class="col-md-6">
												</div>
												<div class="portlet-body">
													<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" >
													</div>
													<div id="results_so"></div>
												</div>
											<!-- </div> -->
										</div>
									</div>
								</div>
								<div class="tab-pane" id="result_sales_executive">	
									<div class="row">
										<div class="col-sm-12">
											<!-- <div class="portlet light"> -->
												<div class="col-md-6">
												</div>
												<div class="portlet-body">
													<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" >
												 	</div>
													<div id="results_se"></div>
												</div>
											<!-- </div> -->
										</div>
									</div>
								</div>
						<!-- 	</div> -->
						</div>
					</div>
				<!-- </div>
 -->			</div>
		</div>
	</div>
</div>
</div>

<!-- password model -->
<div id="changePasswordModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="changePasswordModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Change Password</h4>
			</div>
			<form action="#" id="changePasswordForm">
				<div class="modal-body">						
					<div class="form-body row">
						<div class="form-group col-sm-6">
							<label>New Password</label>
							<input type="password" name="nPassword" id="nPassword" class="form-control" value="" placeholder="New Password">								
							<p class="help-block text-danger"></p>
						</div>
						<div class="form-group col-sm-6">
							<label>Re-type new Password</label>
							<input type="password" name="nRPassword" id="nRPassword" class="form-control" value="" placeholder="Re-type New Password">
							<p class="help-block text-danger"></p>
							<input type="hidden" name="userId" id="userId" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input class="btn btn-success" type="submit" value="Update password">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- password model -->

<!-- Sales Excecutive Information model -->
<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Sales Excecutive Information </div>
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
<!-- Sales Excecutive Information model -->


<!-- user create model -->
<div id="usercreate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="usercreate">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Create System User</h4>
			</div>
			<form action="#" id="usercreateForm">
				<div class="modal-body">						
					<div class="form-body row">
						<div class="form-group col-sm-6">
							<label>Admin Type</label>
							<input type="hidden" name="salesId" id="salesId" class="form-control" value="">
							<select type="text" class="form-control" name="admin_type" id="admin_type" value="<?php echo $name; ?>" >
							<option value="">Select Admin Type</option>
							<?php 
								$ctable_data=$db->rp_getData("admin_type","*","isDelete=0","",0);
								while($a=mysqli_fetch_assoc($ctable_data))
								{
									?>
									<option <?php echo ($admin_type==$a['id'])?"selected":""; ?> value="<?php echo $a['id']; ?>"><?php echo $a['name']?></option>
									<?php
								}
							?>
							</select>								
							<p class="help-block text-danger"></p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input class="btn btn-success" type="submit" value="Create User">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- user create model -->



<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>



<script>


	$(function(){
		$('#changePasswordModal').on('shown.bs.modal', function (event) {
		   var button = $(event.relatedTarget) // Button that triggered the modal
		  var userId = button.data('id') 	
		  var modal = $(this)
		  modal.find('input[type=hidden][name=userId]').val(userId);
		});

		$('#changePasswordModal').on('hidden.bs.modal', function (event) {			   
		var modal = $(this)
		modal.find('input[type=hidden][name=userId]').val("");
		  modal.find('input[name=nRPassword]').val("");
		  modal.find('input[name=nPassword]').val("");
		  modal.find('p.help-block').html("");
		});
			
		$('#changePasswordForm').on('submit',function(e)
		{
			var error=0;
			e.preventDefault();
			if($('#nPassword').val()=="" )
			{
				// error=1;
				// $('#nPassword').parent('div.form-group').find('p.help-block').html("*Please Enter Password");
			}else{
				error=0;
				$('#nPassword').parent('div.form-group').find('p.help-block').html("");
			}
			if($('#nRPassword').val()=="" || $('#nPassword').val()!=$('#nRPassword').val())
			{
				error=1;
				$('#nRPassword').parent('div.form-group').find('p.help-block').html("*It Must be match with password field !!");
			}
			else{
				error=0;
				$('#nRPassword').parent('div.form-group').find('p.help-block').html("");
			}
			if($('#userId').val()=="")
			{
				error=1;
				alert('Internal Error Please Try Again !!');
				$('#changePasswordModal').modal('hide');
			}
			if(error==0)
			{
				var nPassword=$('#nPassword').val();
				var userId=$('#userId').val();					
				$.ajax({
				  type: "POST",
				  url: "change_password_sales_manager.php",
				  data: {nPassword:nPassword,userId:userId},						
				  success: function(data){
					  var json_obj=$.parseJSON(data);
					  if(json_obj['data']['ack']==1)
					  {
						
						$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
						$('#alert-msg').show();								
						$('#changePasswordModal').modal('hide');
						// displayRecords();
						// displayRecords(10,1);
						callAjax();
					  }
					  else
					  {
						$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
						$('#alert-msg').show();								
														 
						 $('#changePasswordModal').modal('hide');
						
					  }
				  }						 
				});
			}
		});
	});
</script>


<!-- usre create script -->
<script>
	$(function(){
		$('#usercreate').on('shown.bs.modal', function (event) 
		{
		   	var button = $(event.relatedTarget) // Button that triggered the modal
		 	var salesId = button.data('id') 	
		  	var modal = $(this)
		  	modal.find('input[type=hidden][name=salesId]').val(salesId);
		});

		$('#usercreate').on('hidden.bs.modal', function (event) 
		{			   
			var modal = $(this)
			modal.find('input[type=hidden][name=salesId]').val("");
		  	modal.find('p.help-block').html("");
		});
			
		$('#usercreateForm').on('submit',function(e)
		{
			var error=0;
			e.preventDefault();
			if($('#admin_type').val()=="" || $('#admin_type').val()!=$('#admin_type').val())
			{
				error=1;
				$('#admin_type').parent('div.form-group').find('p.help-block').html("*It Must be match with password field !!");
			}
			else{
				error=0;
				$('#admin_type').parent('div.form-group').find('p.help-block').html("");
			}
			
			if($('#salesId').val()=="")
			{
				error=1;
				alert('Internal Error Please Try Again !!');
				$('#usercreate').modal('hide');
			}
			
			if(error==0)
			{
				var admin_type=$('#admin_type').val();
				var salesId=$('#salesId').val();					
				$.ajax({
				 	type: "POST",
				  	url: "create_user_ajax_function.php",
				  	data: {admin_type:admin_type,salesId:salesId},						
				  	success: function(data)
				  	{
						var json_obj=$.parseJSON(data);
					  	if(json_obj['data']['ack']==1)
					 	{
					 		$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
							$('#alert-msg').show();								
							$('#usercreate').modal('hide');
							callAjax();
					  	}
					  	else
					  	{
							$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
							$('#alert-msg').show();								
							$('#usercreate').modal('hide');
						}
				  	}						 
				});
			}
		});
	});
</script>
<!-- usre create script -->
		
<script type="text/javascript">
var status="";
var searchName="";
var city="";
var df1="";
var state="";
var type = "";
var sales_id = <?= $sales_id ?>;
var route_id = <?= $route_id ?>;

var data_url_sm = "route_get_ajax.php";



$('#myModal').on('show.bs.modal', function (event) {
  	var button = $(event.relatedTarget) // Button that triggered the modal
  	var requesting_id=button.data("id");
 	$("#requesting_ajax").attr("data-url","sales_executive_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})

// function SalesexecutiveType(se_type){
	// type=se_type;
    // displayRecords(500,1);
    // callAjax();
// }

function searchByName(){
	searchName = $("#searchName").val();
	state = $("#state").val();
	city = $("#city").val();
	type = $("#sales_executive_type").val();
	sales_id = $("#sales_id").val();
	df1=$("#quick_stock_adjustment_filter_input").val();
	// displayRecords_sm(500,1);
	callAjax();
	return false;
}
function clearSearchByName(){
	searchName = "";
	city = "";
	state = "";
	type = "";
	sales_id = "";
	df1 = "";
	$("#searchName").val("");
	$("#city").select2("val","");
	$("#state").select2("val","");
	$("#sales_executive_type").select2("val","");
	$("#sales_id").select2("val","");
	$("#quick_stock_adjustment_filter_input").val("");
	// displayRecords_sm(500,1);
	callAjax();
	
}

$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});



// sm---------
function loadDataTable_sm(){
	
	$('#datatable_sm').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			  { "sWidth": "1%" }, 
			  { "sWidth": "3%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  { "sWidth": "10%" },
			  // { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});
}


		


function callAjax(){
	$.ajax({
	    url: data_url_sm,
	    data: {
	    	numRecords: "500",
	        searchName: searchName,
	        status:status,
	        state:state,
	        city:city,
	        type:type,
	        sales_id:sales_id,
	        df1:df1,
	        route_id:route_id,
	    },
	    beforeSend: function() {
	        $("#results_sm").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
	    },
	    success: function(result) {
	        $("#results_sm").html(result);
	        if(state!="" && city!="")
			{
				filter_state(state,city);
			}
		}
	});	
}


// used when user change row limit
function changeDisplayRowCount(numRecords) {
	// displayRecords_sm(numRecords, 1);
	callAjax();
	
}

$(document).ready(function() {
	// displayRecords_sm(500,1);
	callAjax();
});

function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='route_crud.php?mode=delete&id='+id;
	}
}

function genSalesExecutiveExcel(){
    	var searchName     = $("#searchName").val();
      	searchName     	   = searchName.trim();
      	// searchName     	   = encodeURIComponent(searchName.trim());
      	var sales_id = $("#sales_id").val();
	    var df1=$("#quick_stock_adjustment_filter_input").val();
      	var type          = $("#sales_executive_type").val();
      	$.ajax({
	        method: "POST",
	        url: "sales_route_report_excel.php",
	        data:{
        		searchName:searchName,
				sales_id:sales_id,
				df1:df1,
				type:type
			},	
			dataType : 'json',
			beforeSend: function()
			{
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
        	success: function(result){
        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
			/*error:function(result){
				window.location.href="<?=SITEURL?>"+result.file_path;
			}*/
    	});
    }

    function genSalesExecutivePrint() {

    	var searchName     = $("#searchName").val();
      	searchName     	   = encodeURIComponent(searchName.trim());
      	var sales_id = $("#sales_id").val();
	    var df1=$("#quick_stock_adjustment_filter_input").val();
      	var type          = $("#sales_executive_type").val();

    	var myWindow = window.open('print_sales_route_ajax.php?searchName='+searchName+ "&type=" + type + "&sales_id=" + sales_id + "&df1=" + df1 ,'','width=700,height=800');
     	myWindow.print();
  //    	setTimeout(function () 
		// {
		// 	myWindow.print();
		// 	var ival = setInterval(function() 
		// 	{
		// 	    myWindow.close();
		// 	    clearInterval(ival);
		// 	}, 200);
		// }, 500);
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

	
</script>

</body>
</html>