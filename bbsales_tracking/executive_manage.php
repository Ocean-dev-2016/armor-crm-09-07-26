<?php
error_reporting(0);
if (!isset($_REQUEST['flag']) && $_REQUEST['flag'] == "") {
	$page_id = 555;
	$page_slug = 'page_executive';
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
	$page_id = 616;
	$page_slug = 'prospect_customer';
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "channel_partner") {
	$page_id = 555;
	$page_slug = 'channel_partner_customer';
}
$ctable 	= "executive";
if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
	$ctable1 	= "Prospect Customer";
} else if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "channel_partner") {
	$ctable1 	= "Channel Partner";
} else {
	$ctable1 	= "Customer";
}
$main_page 	= $ctable;
if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "channel_partner") {
	$page = "channel_partner_customer";
} else if (!isset($_REQUEST['flag']) || $_REQUEST['flag'] == "") {
	$page = "executive_customer";
} else {
	$page = "manage_" . $ctable;
}
$page_title = "Manage " . $ctable1;
$page_hierarchy = array(array("link" => "", "title" => "Sales & Marketing"), array("link" => $ctable . "_manage.php", "title" => "Manage " . $ctable1));
$back_request_url = $_REQUEST['back_request_url'];
include("connect.php");
// echo ADMINSITEURL."task_info_print.php?id=".$cid."&type=".$type;
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/fSelect.css">

</head>

<body class="page-md">
	<?php include("header.php"); ?>
	<div class="page-container">
		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">
					<h1><a href="<?php echo "dashboard.php"; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy); ?> </h1>
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
				</div>
				<div class="row">
					<div class="col-md-12 "></div>
					<!-- BEGIN Portlet PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption"> <i class="fa fa-filter"></i>Filters </div>
							<div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a></div>
						</div>
						<div class="portlet-body">
							<div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
								<div class="row">
									<?php
									// if($rights['insert_flag']==1) 
									// {
									?>
									<div class="col-md-1 col-xs-1 col-sm-1">
										<div class="btn-group">
											<?php
											if ($_REQUEST['flag'] == "") {
											?>
												<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn sbold blue-ebonyclay dropdown-toggle">
													Add NEW<i class="fa fa-plus"></i>
												</button>
											<?php
											}
											?>

											<ul role="menu" class="dropdown-menu">
												<li>
													<?php
													if (isset($_REQUEST['flag']) && $_REQUEST['flag'] == "prospect") {
														$addUrl = "mode=add&flag=prospect";
													} else {
														$addUrl = "mode=add";
													}
													?>
													<!-- <a href="executive_crud.php?type=1&<?= $addUrl; ?>" title="CP"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add CP </span></a>
													<a href="executive_crud.php?type=2&<?= $addUrl; ?>" title="Dealer"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Dealer </span></a>
													<a href="executive_crud.php?type=3&<?= $addUrl; ?>" title="Sub Dealer"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add  Sub Dealer  </span></a> -->
													<a href="executive_crud.php?type=4&<?= $addUrl; ?>" title="Government Office"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Government Office</span></a>
													<!-- <a href="executive_crud.php?type=6&<?= $addUrl; ?>" title="Trader"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Trader</span></a> -->
													<a href="executive_crud.php?type=7&<?= $addUrl; ?>" title=""><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Customer</span></a>
													<a href="executive_crud.php?type=9&<?= $addUrl; ?>" title=""><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add MEP Consultant</span></a>
													<a href="executive_crud.php?type=10&<?= $addUrl; ?>" title=""><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Builder</span></a>
													<a href="executive_crud.php?type=11&<?= $addUrl; ?>" title=""><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Brand Approval Visit</span></a>
													<!-- <a href="executive_crud.php?type=7&<?= $addUrl; ?>" title="Promotional Customer"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Promotional Customer</span></a> -->
													<!-- <a href="executive_crud.php?type=8&<?= $addUrl; ?>" title="Merchant Exports"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Merchant Exports</span></a> -->
													<!-- <a href="executive_crud.php?type=9&<?= $addUrl; ?>" title="Corporate Customer"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Add Corporate Customer</span></a> -->
												</li>
											</ul>
										</div>
									</div>
									<?php
									//}
									?>

									<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
										<div class="form-inline" role="form">
											<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
												<div class="form-group">
													<input type="text" style="width: 450px!important" placeholder="Search By Person / Company / Mobile /  Gst / Client Code / Pincode:  " class="form-control input-large" name="searchName" id="searchName" value="" />
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
															<?php
															if ($rights['print_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
															?>
																<li>
																	<a name="print" onClick="genExecutivePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
																</li>
															<?php
															}
															?>
															<?php
															if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
															?>
																<li>
																	<a class="excel" name="excel" onClick="genExecutiveExcelReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
																</li>
															<?php
															}
															?>
															<?php
															if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
															?>
																<li>
																	<a class="import" href="import_customer_manage.php?mode=add&flag=<?= $_REQUEST['flag'] ?>" target="_blank" name="import" id="import"><i class="fa fa-download"></i>Import</a>
																</li>
															<?php
															}
															?>
														</ul>
													</div>
													<a style="background-color: #20761fc7;" onclick="showStatics()" title="Show Statistics" type="button" class="btn btn-sm">
														<i class="fa fa-eye"></i>
													</a>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END Portlet PORTLET-->
				</div>

				<div class="portlet-body">
					<div class="tab-content">
						<div class="tab-pane active" id="my_order_info">
							<div class="row">
								<div class="col-xl-12">
									<div class="portlet light">
										<div class="portlet-body">
											<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;"> </div>
											<?php
											if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
											?>
												<div class="portlet-body">
													<div class="tabbable-line">
														<!-- <ul class="nav nav-tabs ">
														<li class="active">
															<a href="#result_super" data-toggle="tab" aria-expanded="false"> Super Stockist </a>
														</li>
														<li>
															<a href="#result_dealer" data-toggle="tab" aria-expanded="false">Distributor</a>
														</li>
														<li>
															<a href="#result_outlet" data-toggle="tab" aria-expanded="false">Dealer</a>
														</li>
													</ul> -->
														<div class="tab-content">
															<div class="tab-pane active" id="result_super">
																<div class="row">
																	<div class="col-sm-12">
																		<div class="portlet light">
																			<div class="col-md-6">
																			</div>
																			<div class="portlet-body">
																				<div class="loading-div" style="display:none;">
																					<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;">
																				</div>
																				<div id="results_ss"></div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>

															<div class="tab-pane" id="result_dealer">
																<div class="row">
																	<div class="col-sm-12">
																		<div class="portlet light">
																			<div class="col-md-6">
																			</div>
																			<div class="portlet-body">
																				<div class="loading-div" style="display:none;">
																					<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;">
																				</div>
																				<div id="results_dd"></div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>

															<div class="tab-pane" id="result_outlet">
																<div class="row">
																	<div class="col-sm-12">
																		<div class="portlet light">
																			<div class="col-md-6">
																			</div>
																			<div class="portlet-body">
																				<div class="loading-div" style="display:none;">
																					<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;">
																				</div>
																				<div id="results_out"></div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											<?php
											} else {
											?>
												<div class="portlet-body">
													<div class="row">
														<div id="results">
														</div>
													</div>
												</div>
											<?php
											}
											?>
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


	<!---Model Customer-->
	<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document" style="width: 50%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="myModalLabel">Customer Details</h3>
				</div>
				<div class="modal-body">
					<div class="row" id="get_data"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<!-- <button type="button" class="btn btn-primary">Save changes</button> -->
				</div>
			</div>
		</div>
	</div>

	<div id="myModal" class="modal fade" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							View Customer Information </div>
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

	<!---First model change password-->
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

	<div id="MyCustomerInfo" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-gift"></i>My Customer Information
						</div>
						<div class="tools">

							<a href="javascript:;" id="requesting_ajax_my_customer" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

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
	<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
	<script src="js/fSelect.js" type="text/javascript"></script>

	<script>
		function getCustomerModuleCount(id) {
			//console.log(id)
			$.ajax({
				type: "POST",
				url: "get_customer_module_count.php",
				data: {
					id: id
				},
				beforeSend: function() {
					//alert();
					$("#get_data").html("");
				},
				success: function(result) {
					$("#get_data").html(result);
				}
			})
		}
	</script>
	<script>
		$(function() {
			//alert("passios");
			$('#changePasswordModal').on('shown.bs.modal', function(event) {
				var button = $(event.relatedTarget) // Button that triggered the modal
				var userId = button.data('id')
				var modal = $(this)
				modal.find('input[type=hidden][name=userId]').val(userId);
			});
			$('#changePasswordModal').on('hidden.bs.modal', function(event) {
				var modal = $(this)
				modal.find('input[type=hidden][name=userId]').val("");
				modal.find('input[name=nRPassword]').val("");
				modal.find('input[name=nPassword]').val("");
				modal.find('p.help-block').html("");
			});
			$('#changePasswordForm').on('submit', function(e) {

				var error = 0;
				e.preventDefault();
				if ($('#nPassword').val() == "") {
					// error=1;
					// $('#nPassword').parent('div.form-group').find('p.help-block').html("*Minimum 6 Character required");
				} else {
					error = 0;
					$('#nPassword').parent('div.form-group').find('p.help-block').html("");
				}
				if ($('#nRPassword').val() == "" || $('#nPassword').val() != $('#nRPassword').val()) {
					error = 1;
					$('#nRPassword').parent('div.form-group').find('p.help-block').html("*It Must be match with password field !!");
				} else {
					error = 0;
					$('#nRPassword').parent('div.form-group').find('p.help-block').html("");
				}
				if ($('#userId').val() == "") {
					error = 1;
					alert('Internal Error Please Try Again !!');
					$('#changePasswordModal').modal('hide');
				}
				if (error == 0) {
					var nPassword = $('#nPassword').val();
					var userId = $('#userId').val();
					$.ajax({
						type: "POST",
						url: "change_password_customer.php",
						data: {
							nPassword: nPassword,
							userId: userId
						},
						success: function(data) {
							var json_obj = $.parseJSON(data);
							if (json_obj['data']['ack'] == 1) {

								$('#alert-msg').find('p').html(json_obj['data']['ack_msg']);
								$('#alert-msg').show();
								$('#changePasswordModal').modal('hide');
								// displayRecords_super();
								displayRecords_super(50, 1);
							} else {
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
	<script type="text/javascript">
		function CustomerUserCreate(id) {

			$.ajax({
				type: "POST",
				url: "customer_create_user.php",
				data: {
					id: id
				},
				success: function(data) {
					var json_obj = JSON.parse(data);
					if (json_obj['data']['ack'] == 1) {
						toastr.success(json_obj['data']['ack_msg'] || "System User Create Successfully");
					} else {
						toastr.error(json_obj['data']['ack_msg'] || "System User Creation Failed");
					}
					displayRecords_super(50, 1);
				}
			});
		}
		// }
	</script>


	<script type="text/javascript">
		var status = "";
		var searchName = "<?= $back_request_url; ?>";
		var main_city = "";
		var city = "";
		var state = "";
		var class_name = "";
		var area = "";
		var customer_type = "";
		var price_list = "";
		var zone = "";
		var category_id = "";
		var top_category_id = "";
		var type_of_company = "";
		var seid = "";
		var isFillter = false;

		var data_url = "<?php echo $ctable ?>_super_get_ajax.php";
		var data_url_super = "<?php echo $ctable ?>_super_get_ajax.php";
		/*var data_url_dealer = "<?php echo $ctable ?>_dealer_get_ajax.php";
		var data_url_outlet = "<?php echo $ctable ?>_outlet_get_ajax.php";*/
		// echo $_REQUEST['type'];
		var type = "";


		$('#myModal').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget) // Button that triggered the modal
			var requesting_id = button.data("id");
			var type = button.data("type");
			$("#requesting_ajax").attr("data-url", "executive_information_get_ajax.php?id=" + requesting_id + "&type=" + type);
			$("#requesting_ajax").click();
		})

		$('#MyCustomerInfo').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget) // Button that triggered the modal
			var requesting_id = button.data("id");
			var type = button.data("type");
			$("#requesting_ajax_my_customer").attr("data-url", "my_customer_information_get_ajax.php?id=" + requesting_id + "&type=" + type);
			$("#requesting_ajax_my_customer").click();
			//$("#myModal").hide();
		})

		function getExecutive(type) {
			status = type;
			displayRecords(50, 1);
			displayRecords_super(50, 1);
			/*displayRecords_dealer(500,1);
			displayRecords_outlet(500,1);*/
		}

		function CustomerType(type) {
			customer_type = type;
			/*displayRecords(500,1);
			displayRecords_super(500,1);*/
			/*displayRecords_dealer(500,1);
			displayRecords_outlet(500,1);*/
		}

		function price_list(id) {
			price_list = id;
		}

		function searchByName() {
			searchName = $("#searchName").val();
			customer_type = $("#customer_type").val();
			state = $("#state").val();
			city = $("#city").val();
			main_city = $("#main_city").val();
			price_list = $("#price_list").val();
			seid = $("#seid").val();
			zone = $("#zone").val();
			category_id = $("#category_id").val();
			top_category_id = $("#top_category_id").val();

			type_of_company = $("#type_of_company").val();
			isFillter = true;
			displayRecords(50, 1);
			displayRecords_super(50, 1);
			if (state != "" && city != "") {
				filter_state(state, city);
				filter_city(city, main_city)
			}
			/*displayRecords_dealer(500,1);
			displayRecords_outlet(500,1);*/
			return false;
		}

		function clearSearchByName() {
			searchName = "";
			status = "";
			city = "";
			main_city = "";
			state = "";
			class_name = "";
			area = "";
			customer_type = "";
			price_list = "";
			seid = "";
			zone = "";
			category_id = "";
			top_category_id = "";
			type_of_company = "";
			isFillter = false;

			$("#searchName").val("");
			$("#status").select2("val", "");
			$("#city").select2("val", "");
			$("#main_city").select2("val", "");
			$("#state").select2("val", "");
			$("#class_name").select2("val", "");
			$("#area").select2("val", "");
			$("#customer_type").select2("val", "");
			$("#price_list").select2("val", "");
			$("#seid").select2("val", "");
			$("#zone").select2("val", "");
			$("#category_id").fSelect("destroy");
			$("#category_id").val("");
			$("#category_id").fSelect("create");

			$("#top_category_id").fSelect("destroy");
			$("#top_category_id").val("");
			$("#top_category_id").fSelect("create");

			$("#type_of_company").select2("val", "");
			displayRecords(50, 1);
			displayRecords_super(50, 1);
			displayRecords_dealer(50, 1);
			displayRecords_outlet(50, 1);
		}
		$("#searchName").keyup(function(event) {
			if (event.keyCode == 13) {
				$("#searchByName").click();
			}
		});

		function getCity(val) {

			state = val;
			displayRecords(50, 1);
			displayRecords_super(50, 1);
			displayRecords_dealer(50, 1);
			displayRecords_outlet(50, 1);
			$.ajax({
				type: "POST",
				url: "find_city.php",
				data: 'state_id=' + val,

				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#city").html(data);
					$('#city').select2("val", "");
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
				}
			});
		}

		function getCityName(cid) {
			state = $('#state').val();
			//$('#state').select2("val","");
			//alert(cid);
			city = cid;
			city = encodeURIComponent(city.trim());

			displayRecords(50, 1);
			displayRecords_super(50, 1);
			displayRecords_dealer(50, 1);
			displayRecords_outlet(50, 1);
		}

		function getAreaList(val) {
			class_name = val;
			displayRecords(50, 1);
			displayRecords_super(50, 1);
			displayRecords_dealer(50, 1);
			displayRecords_outlet(50, 1);
			$.ajax({
				type: "POST",
				url: "find_area.php",
				data: 'class_id=' + val,
				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');

				},
				success: function(data) {
					$("#area").html(data);
					$('#area').select2("val", "");
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
				}
			});
		}

		function getAreaName(aid) {
			class_name = $('#class_name').val();
			area = aid;
			displayRecords(50, 1);
			displayRecords_super(50, 1);
			displayRecords_dealer(50, 1);
			displayRecords_outlet(50, 1);
		}

		function loadDataTable() {
			$.fn.dataTable.ext.errMode = 'none';
			$('#datatable_super').dataTable({
				"bPaginate": false,
				"order": ['desc'],
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false,
				// "aoColumns": [{
				// 		"sWidth": "10%"
				// 	},
				// 	{
				// 		"sWidth": "30%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%",
				// 		"bSortable": false
				// 	}
				// ],
				// "oLanguage": {
				// 	"sEmptyTable": "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"
				// },
				"order": [
					[1, 'asc']
				],
				/* default order is index 1 */
				'columnDefs': [{
					'targets': [0],
					/* column index */
					'orderable': false,
					/* true or false */
				}],

				"oLanguage": {
					"sEmptyTable": "Sorry No Data Available!!"
				}
			});
		}

		function displayRecords(numRecords) {
			var flag = '<?= $_REQUEST['flag'] ?>';
			var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			state = encodeURIComponent(state.trim());
			city = encodeURIComponent(city.trim());
			main_city = encodeURIComponent(main_city.trim());
			// $("#loading-modal").modal('show');
			$('.preloader').fadeIn('slow');
			$("#results").html("");
			$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&customer_type=" + customer_type + "&price_list=" + price_list + "&seid=" + seid + "&flag=" + flag + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company + "&isFillter=" + isFillter, function() {
				// $("#loading-modal").modal('hide');
				$('.preloader').fadeOut('slow');
				loadDataTable();
			}); //load initial records

			//executes code below when user click on pagination links
			$("#results").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				// $(".loading-div").show(); //show loading element
				$('.preloader').fadeIn('slow');
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&customer_type=" + customer_type + "&price_list=" + price_list + "&seid=" + seid + "&flag=" + flag + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company, {
					"page": page
				}, function() { //get content from PHP page
					// $(".loading-div").hide(); //once done, hide loading element
					$('.preloader').fadeOut('slow');
					loadDataTable();
				});

			});
			$("#results").on("change", "#numRecords", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				// $(".loading-div").show(); //show loading element
				$('.preloader').fadeIn('slow');
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&customer_type=" + customer_type + "&price_list=" + price_list + "&seid=" + seid + "&flag=" + flag + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company, {
					"page": page
				}, function() { //get content from PHP page
					// $(".loading-div").hide(); //once done, hide loading element
					$('.preloader').fadeOut('slow');
					loadDataTable();
				});

			});
		}

		function loadDataTable_super() {

			$('#datatable_super').dataTable({
				"bPaginate": false,
				"order": ['desc'],
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false,
				"aoColumns": [{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					{
						"sWidth": "5%"
					},
					// { "sWidth": "5%" }, 
					// { "sWidth": "5%" }, 
					// { "sWidth": "5%" }, 
				],
				// "oLanguage": {
				// 	"sEmptyTable": "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"
				// },
				// "order": ['asc'], /* default order is index 1 */
				'columnDefs': [{
					'targets': [0],
					/* column index */
					'orderable': false,
					/* true or false */
				}],


				"oLanguage": {
					"sEmptyTable": "Sorry No Data Available!!"
				}
			});
		}

		function displayRecords_super(numRecords) {
			var flag = '<?= $_REQUEST['flag'] ?>';
			var dashboard_customer_type = '<?= $_REQUEST['customer_type'] ?>';
			// var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			// $("#loading-modal").modal('show');
			$('.preloader').fadeIn('slow');
			$("#results_ss").html("");
			$("#results_ss").load(data_url_super + "?show=" + numRecords + "&searchName=" + searchName + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&customer_type=" + customer_type + "&price_list=" + price_list + "&seid=" + seid + "&flag=" + flag + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&dashboard_customer_type=" + dashboard_customer_type + "&type_of_company=" + type_of_company + "&isFillter=" + isFillter, function() {
				// $("#loading-modal").modal('hide');
				$('.preloader').fadeOut('slow');
				loadDataTable_super();
			}); //load initial records

			//executes code below when user click on pagination links
			$("#results_ss").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				// $(".loading-div").show(); //show loading element
				$('.preloader').fadeIn('slow');
				var page = $(this).attr("data-page"); //get page number from link
				$("#results_ss").load(data_url_super + "?show=" + numRecords + "&searchName=" + searchName + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&customer_type=" + customer_type + "&price_list=" + price_list + "&seid=" + seid + "&flag=" + flag + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company, {
					"page": page
				}, function() { //get content from PHP page
					// $(".loading-div").hide(); //once done, hide loading element
					$('.preloader').fadeOut('slow');
					loadDataTable_super();
				});

			});
			// $("#results_ss").on( "change", "#numRecords", function (e){
			// 	e.preventDefault();
			// 	var numRecords  = $("#numRecords").val();
			// 	$(".loading-div").show(); //show loading element
			// 	var page = $(this).attr("data-page"); //get page number from link
			// 	$("#results_ss").load(data_url_super+"?show=" + numRecords + "&searchName=" + searchName +"&state=" + state +"&city=" + city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&customer_type=" + customer_type,{"page":page}, function(){ //get content from PHP page
			// 		$(".loading-div").hide(); //once done, hide loading element
			// 		loadDataTable_super();
			// 	});

			// });
		}
		// get data for dealer----------------------//
		function loadDataTable_dealer() {

			$('#datatable_dealer').dataTable({
				"bPaginate": false,
				"order": ['desc'],
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false,
				// "aoColumns": [{
				// 		"sWidth": "10%"
				// 	},
				// 	{
				// 		"sWidth": "30%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%",
				// 		"bSortable": false
				// 	}
				// ],
				// "oLanguage": {
				// 	"sEmptyTable": "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"
				// },
				"order": [
					[1, 'asc']
				],
				/* default order is index 1 */
				'columnDefs': [{
					'targets': [0],
					/* column index */
					'orderable': false,
					/* true or false */
				}],

				"oLanguage": {
					"sEmptyTable": "Sorry No Data Available!!"
				}

			});
		}

		function displayRecords_dealer(numRecords) {
			var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			$("#results_dd").html("");
			$('.preloader').fadeIn('slow');
			$("#results_dd").load(data_url_dealer + "?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&price_list=" + price_list + "&seid=" + seid + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company + "&isFillter=" + isFillter, function() {
				$('.preloader').fadeOut('slow');
				loadDataTable_dealer();
			}); //load initial records

			//executes code below when user click on pagination links
			$("#results_dd").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				// $(".loading-div").show(); //show loading element
				$('.preloader').fadeIn('slow');
				var page = $(this).attr("data-page"); //get page number from link
				$("#results_dd").load(data_url_dealer + "?show=" + numRecords + "&searchName=" + searchName + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&price_list=" + price_list + "&seid=" + seid + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company, {
					"page": page
				}, function() { //get content from PHP page
					// $(".loading-div").hide(); //once done, hide loading element
					$('.preloader').fadeOut('slow');
					loadDataTable_dealer();
				});

			});
			// $("#results_dd").on( "change", "#numRecords", function (e){
			// 	e.preventDefault();
			// 	var numRecords  = $("#numRecords").val();
			// 	$(".loading-div").show(); //show loading element
			// 	var page = $(this).attr("data-page"); //get page number from link
			// 	$("#results_dd").load(data_url_dealer+"?show=" + numRecords + "&searchName=" + searchName +"&state=" + state +"&city=" + city + "&type=" + type + "&class_name=" + class_name + "&area=" + area  + "&price_list=" + price_list,{"page":page}, function(){ //get content from PHP page
			// 		$(".loading-div").hide(); //once done, hide loading element
			// 		loadDataTable_dealer();
			// 	});

			// });
		}

		function loadDataTable_outlet() {

			$('#datatable_outlet').dataTable({
				"bPaginate": false,
				"order": ['desc'],
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false,
				// "aoColumns": [{
				// 		"sWidth": "10%"
				// 	},
				// 	{
				// 		"sWidth": "30%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%"
				// 	},
				// 	{
				// 		"sWidth": "20%",
				// 		"bSortable": false
				// 	}
				// ],
				// "oLanguage": {
				// 	"sEmptyTable": "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"
				// },
				"order": [
					[1, 'asc']
				],
				/* default order is index 1 */
				'columnDefs': [{
					'targets': [0],
					/* column index */
					'orderable': false,
					/* true or false */
				}],

				"oLanguage": {
					"sEmptyTable": "Sorry No Data Available!!"
				}
			});
		}
		//get Data for  Outlet-------------------------//
		function displayRecords_outlet(numRecords) {

			var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			$("#results_out").html("");
			$('.preloader').fadeIn('slow');
			$("#results_out").load(data_url_outlet + "?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company, function() {
				$('.preloader').fadeOut('slow');
				loadDataTable_outlet();
			}); //load initial records

			//executes code below when user click on pagination links
			$("#results_out").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				// $(".loading-div").show(); //show loading element
				$('.preloader').fadeIn('slow');
				var page = $(this).attr("data-page"); //get page number from link
				$("#results_out").load(data_url_outlet + "?show=" + numRecords + "&searchName=" + searchName + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&type=" + type + "&class_name=" + class_name + "&area=" + area + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company, {
					"page": page
				}, function() { //get content from PHP page
					// $(".loading-div").hide(); //once done, hide loading element
					$('.preloader').fadeOut('slow');
					loadDataTable_outlet();
				});

			});
			// $("#results_out").on( "change", "#numRecords", function (e){
			// 	e.preventDefault();
			// 	var numRecords  = $("#numRecords").val();
			// 	$(".loading-div").show(); //show loading element
			// 	var page = $(this).attr("data-page"); //get page number from link
			// 	$("#results_out").load(data_url_outlet+"?show=" + numRecords + "&searchName=" + searchName +"&state=" + state +"&city=" + city + "&type=" + type + "&class_name=" + class_name + "&area=" + area,{"page":page}, function(){ //get content from PHP page
			// 		$(".loading-div").hide(); //once done, hide loading element
			// 		loadDataTable_outlet();
			// 	});

			// });
		}
		// used when user change row limit
		function changeDisplayRowCount(numRecords) {
			displayRecords(numRecords, 1);
			displayRecords_super(numRecords, 1);
			displayRecords_dealer(numRecords, 1);
			displayRecords_outlet(numRecords, 1);
		}

		$(document).ready(function() {
			displayRecords(50, 1);
			displayRecords_super(50, 1);
			/*displayRecords_dealer(500,1);
			displayRecords_outlet(500,1);*/
		});

		function del_conf(id) {
			var r = confirm("Are you sure you want to delete?");
			if (r) {
				window.location.href = '<?php echo $ctable; ?>_crud.php?mode=delete&id=' + id;
			}
		}

		function change_to_customer(id) {
			var r = confirm("Are you sure you want to convert this prospect customer into customer?");
			if (r) {
				window.location.href = '<?php echo $ctable; ?>_crud.php?mode=change_to_customer&id=' + id;
			}
		}

		function genExecutivePrint() {

			var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			var customer_type = $("#customer_type").val();
			var state = $("#state").val();
			var city = $("#city").val();
			var main_city = $("#main_city").val();
			var zone = $("#zone").val();
			var category_id = $("#category_id").val();
			var top_category_id = $("#top_category_id").val();
			var type_of_company = $("#type_of_company").val();
			var price_list = $("#price_list").val();
			var seid = $("#seid").val();
			var flag = '<?= $_REQUEST['flag'] ?>';

			var myWindow = window.open('print_executive_ajax.php?searchName=' + searchName + "&customer_type=" + customer_type + "&state=" + state + "&city=" + city + "&main_city=" + main_city + "&price_list=" + price_list + "&seid=" + seid + "&flag=" + flag + "&zone=" + zone + "&category_id=" + category_id + "&top_category_id=" + top_category_id + "&type_of_company=" + type_of_company, '', 'width=700,height=800');
			myWindow.print();
			// setTimeout(function () 
			// {
			// 	myWindow.print();
			// 	var ival = setInterval(function() 
			// 	{
			// 	    myWindow.close();
			// 	    clearInterval(ival);
			// 	}, 200);
			// }, 500);
		}

		function genExecutiveExcelReport() {
			var searchName = $("#searchName").val();
			// searchName = encodeURIComponent(searchName.trim());
			var customer_type = $("#customer_type").val();
			var state = $("#state").val();
			var city = $("#city").val();
			var main_city = $("#main_city").val();
			var zone = $("#zone").val();
			var category_id = $("#category_id").val();
			var top_category_id = $("#top_category_id").val();
			var type_of_company = $("#type_of_company").val();
			var price_list = $("#price_list").val();
			var seid = $("#seid").val();
			var flag = '<?= $_REQUEST['flag'] ?>';
			$.ajax({
				method: "POST",
				url: "executive_report_excel.php",
				data: {
					searchName: searchName,
					customer_type: customer_type,
					price_list: price_list,
					seid: seid,
					state: state,
					city: city,
					main_city: main_city,
					zone: zone,
					category_id: category_id,
					top_category_id: top_category_id,
					type_of_company: type_of_company,
					flag: flag
				},
				dataType: 'json',
				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
					window.location.href = "<?= SITEURL ?>" + result.file_path;
				},
				/*error:function(result){
					window.location.href="<?= SITEURL ?>"+result.file_path;
				}*/
			});
		}
	</script>

</body>

</html>