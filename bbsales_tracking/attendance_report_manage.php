<?php
$page_id = 598;
$page_slug = 'attendance_report_page';
$ctable 	= "attendance";
$ctable1 	= "Attendance";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = $ctable1 . " Report";
$page_hierarchy = array(array("link" => "", "title" => "Report"), array("link" => $ctable . "_manage.php", "title" => $page_title));
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>

	<!-- zoom css -->
	<link rel="stylesheet" type="text/css" href="zoom-image/css/style.css" />
	<link rel="stylesheet" type="text/css" href="zoom-image/cloud-zoom/cloud-zoom.css" />
	<link rel="stylesheet" type="text/css" href="zoom-image/fancybox/jquery.fancybox-1.3.4.css" />
	<script src="zoom-image/js/cufon-yui.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="css/fSelect.css" />
	<!-- zoom css -->

	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.1/css/lightbox.css" /> -->

	<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" />
	<link rel="stylesheet" href="<?= ADMINSITEURL ?>css/lightbox.css" />
	<style type="text/css">
		.btn-sm,
		.btn-xs {
			line-height: 2;
		}
	</style>
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
					<div class="col-xl-12">
						<!-- BEGIN Portlet PORTLET-->
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
									<div class="row" style="height: 85px;">
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Search By Sales Person</label><br />
											<select class="form-control status" multiple="multiple" name="sales_executive" id="sales_executive">
												<?php
												if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
												?>
													<option value="">Select Sales Person</option>
												<?php
												}
												?>
												<?php
												if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {

													if ($rights['personal_flag'] == 1) {
														$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
														$ctable_where_se .= " AND id='" . $check_id . "' ";
													} else {
														if ($rights['chain_vise_flag'] == 1) {

															$check_id = $_SESSION[SITE_SESS . 'REFERANCE_ID'];

															$get_sales_type = $db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
															if ($get_sales_type == "sales_manager") {
																$sales_executive_type = "Regional Sales Manager";
																$key = "sm_id";
																$WhereCondition .= ' ' . $key . '=' . $check_id;
															} else if ($get_sales_type == "area_sales_manager") {
																$sales_executive_type = "National Sales Manager"; //Business Development Manager
																$key = "asm_id";
																$WhereCondition .= ' ' . $key . '=' . $check_id;
															} else if ($get_sales_type == "sales_officer") {
																$sales_executive_type = "Area Sales Manager"; //Area Sales Manager
																$key = "so_id";
																$WhereCondition .= ' ' . $key . '=' . $check_id;
															} else if ($get_sales_type == "sales_executive") {
																$sales_executive_type = "Sales Officer";
																$key = "se_id";
																$WhereCondition .= ' ' . $key . '=' . $check_id;
															} else {
																$WhereCondition .= ' type = "service_engineer"';
															}

															$data = $db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);

															$SALEID1 = array();
															if ($data) {
																while ($data_d = mysqli_fetch_assoc($data)) {
																	$SALEID1[] = $data_d['id'];
																}
															}
															if (!empty($SALEID1)) {
																$SALEID1 = implode(",", $SALEID1);

																$ctable_where_se .= "  AND id IN (" . $SALEID1 . ',' . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
															} else {
																$ctable_where_se .= "  AND id IN (" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . ")";
															}
														} else {
															$ctable_where_se = "";
														}
													}
												}
												$D_r = $db->rp_getData("sales_executive", "*", "1=1 AND isDelete=0 AND isActive=1 " . $ctable_where_se . " GROUP By name", "", 0);
												while ($D = mysqli_fetch_assoc($D_r)) {
												?>
													<option value="<?= $D['id'] ?>" <?= ($sid == $D['id']) ? "selected" : ""; ?>><?= $D['name'] ?></option>
												<?php
												}
												?>
											</select>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Search By In/Out</label><br />
											<select class="form-control status" multiple="multiple" name="io" id="io">
												<?php
												$io_r = $db->rp_getData("attendance", "*", "", "", 0);
												?>
												<option value="">select In/Out</option>
												<option value="In" <?= ($io_d['inout_status'] == "In") ? "selected" : ""; ?>>In</option>
												<option value="Out" <?= ($io_d['inout_status'] == "Out") ? "selected" : ""; ?>>Out</option>
											</select>
										</div>

										<div class="col-md-2 col-xs-2 col-sm-2 " style="margin-top:10px">
											<?php
											$months = array('01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July ', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December',);
											?>
											<label>Month</label><br />
											<select class="form-control" name="filter_month" id="filter_month">
												<?php
												$_REQUEST['filter_month'] = (isset($_REQUEST['filter_month']) && $_REQUEST['filter_month'] != "") ? $_REQUEST['filter_month'] : date('m');
												foreach ($months as $months_key => $months_value) {
												?>
													<option <?= ($months_key == $_REQUEST['filter_month']) ? "selected" : ""; ?> value="<?= $months_key ?>"><?= $months_value ?></option>
												<?php
												}
												?>
											</select>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2 " style="margin-top:10px">
											<?php
											$attendanceLessYear = $db->rp_getValue("attendance", "YEAR(date_time)", "YEAR(date_time)>1970 AND YEAR(date_time)!='' ORDER BY date_time ASC LIMIT 1", 0);
											$currentYear = date('Y');
											?>
											<label>Year</label>
											<select class="form-control" name="filter_year" id="filter_year">
												<?php
												$_REQUEST['filter_year'] = (isset($_REQUEST['filter_year']) && $_REQUEST['filter_year'] != "") ? $_REQUEST['filter_year'] : $currentYear;
												for ($i = $attendanceLessYear; $i <= $currentYear; $i++) {
												?>
													<option value="<?= $i ?>" <?= ($i == $_REQUEST['filter_year']) ? "selected" : ""; ?>><?= $i ?></option>
												<?php
												}
												?>
											</select>
										</div>
										<div class="col-md-4 col-xs-4 col-sm-4 pull-right" style="margin-top:10px;">
											<div class="form-inline" role="form">
												<label>Search By Name / Phone :</label>
												<form class="form-inline" role="form" onSubmit="return searchByName();">
													<div class="form-group">

														<input type="text" style="width:188px!important" placeholder="Search By Name / Phone :  " class="form-control input-small" name="searchName" id="searchName" value="" />
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
																if ($rights['print_flag'] == 1  || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
																?>
																	<li>
																		<!-- <a name="print" onClick="genexpensePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a> -->
																		<a name="print" onClick="genAttandancePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
																	</li>
																<?php
																}
																if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
																?>
																	<li>
																		<!-- <a class="excel" name="excel" onClick="genReportexcel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a> -->
																		<a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
																	</li>
																<?php
																}
																?>
															</ul>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
									<div class="row" style="height: 85px;">
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Search By Executive Type</label><br />
											<select class="form-control" name="sales_executive_type" id="sales_executive_type" onChange="SalesexecutiveType(this.value);" autofocus>
												<option value="">Select Sales Officer Type</option>
												<option value="sales_manager" <?= ($sales_executive_type == "sales_manager") ? "selected" : ""; ?>>Regional Sales Manager</option>
												<option value="area_sales_manager" <?= ($sales_executive_type == "area_sales_manager") ? "selected" : ""; ?>>Business Development Manager</option>
												<option value="sales_officer" <?= ($sales_executive_type == "sales_officer") ? "selected" : ""; ?>>Area Sales Manager</option>
												<option value="sales_executive" <?= ($sales_executive_type == "sales_executive") ? "selected" : ""; ?>>Sales Officer</option>
												<option value="service_executive" <?= ($sales_executive_type == "service_executive") ? "selected" : ""; ?>>Service Executive</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<!-- END Portlet PORTLET-->
						</div>
						<!-- <div class="col-md-12"> -->
						<div class="portlet light">
							<div class="portlet-body">
								<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;"> </div>
								<div id="results"></div>
							</div>
						</div>
						<!-- </div> -->
					</div>
				</div>
			</div>
		</div>

		<!-- Attandance In Modal -->
		<style type="text/css">
			@media (min-width: 768px) {
				.modal-dialog {
					width: 1000px !important;
				}
			}
		</style>
		<div id="InTimeData" class="modal fade" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Attandance Information
							</div>
							<div class="tools">

								<a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

								<a href="javascript:;" onclick="clearSearchByName();" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
							</div>
						</div>
						<div class="portlet-body portlet-empty" style="">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Attandance In Modal -->


		<!-- Attandance Out Modal -->
		<div id="OutTimeData" class="modal fade" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Attandance Information
							</div>
							<div class="tools">

								<a href="javascript:;" id="requesting_ajax1" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

								<a href="javascript:;" onclick="clearSearchByName();" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
							</div>
						</div>
						<div class="portlet-body portlet-empty" style="">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Attandance Out Modal -->
		<?php include("include_js.php"); ?>
		<script src="<?= ADMINSITEURL ?>js/lightbox.js"></script>
		<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
		<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
		<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
		<script type="text/javascript" src="js/fSelect.js"></script>

		<script type="text/javascript">
			$("#sales_executive").fSelect();
			$("#io").fSelect();
		</script>
		<script type="text/javascript">
			var searchName = "";
			var state = "";
			var city = "";
			var df1 = "";
			var sales_executive = "";
			var io = "";
			var customer_id = "";
			var sales_executive_type = "";
			var data_url = "attendance_report_get_ajax.php";
			// var data_url = "attendance_report_get_ajax_new.php";
			// var data_url = "index_demo.php";

			function searchByName() {
				displayRecords(100, 1);
				return false;
			}

			function clearSearchByName() {
				filter_month = '<?= date('m') ?>';
				filter_year = '<?= date('Y') ?>';
				searchName = '';
				io = '';
				sales_executive = '';
				sales_executive_type = '';


				$("#filter_month").select2('destroy');
				$("#filter_month").val(filter_month);
				$("#filter_month").select2();

				$("#filter_year").select2('destroy');
				$("#filter_year").val(filter_year);
				$("#filter_year").select2();

				$("#searchName").val("");

				$("#io option").prop("selected", false);
				$('#io').fSelect('destroy');
				$('#io').fSelect('create');

				$("#sales_executive option").prop("selected", false);
				$('#sales_executive').fSelect('destroy');
				$('#sales_executive').fSelect('create');

				$("#sales_executive_type").select2('destroy');
				$("#sales_executive_type").val(sales_executive_type);
				$("#sales_executive_type").select2();

				displayRecords(100, 1);
			}

			function loadDataTable() {
				$('#datatable_1').dataTable({
					"bPaginate": false,
					"bFilter": false,
					"bInfo": false,
					"bAutoWidth": false,
				});
			}

			function displayRecords(numRecords) {
				var filter_month = $("#filter_month").val();
				var filter_year = $("#filter_year").val();
				var io = $("#io").val();
				var sales_executive = $("#sales_executive").val();
				var sales_executive_type = $("#sales_executive_type").val();
				var searchName = $("#searchName").val();
				searchName = encodeURIComponent(searchName.trim());

				$("#results").html("");
				$("#results").load(data_url + "?show=" + numRecords + "&sales_executive=" + sales_executive + "&filter_month=" + filter_month + "&filter_year=" + filter_year + "&searchName=" + searchName + "&io=" + io + "&sales_executive_type=" + sales_executive_type, function() {
					loadDataTable();
				}); //load initial records
			}

			$(document).ready(function() {
				displayRecords(100, 1);
			});

			function del_conf(id) {
				var r = confirm("Are you sure you want to delete?");
				if (r) {
					window.location.href = '<?php echo $ctable; ?>_crud.php?mode=delete&id=' + id;
				}
			}
		</script>

		<script type="text/javascript">
			function genReport1(cid) {
				var rc = encodeURIComponent($("#print_info").html());
				$.ajax({
					type: "POST",
					url: "attandanceee_genreport_ajax.php",
					data: '&rc=' + rc,
					beforeSend: function() {
						$('.preloader').fadeIn('slow');
					},
					success: function(result) { //alert(result);
						setTimeout(function() {
							$('.preloader').fadeOut('slow');
							window.location.href = result;
						}, 1500);
					}
				});
			}

			function genReport() {
				var query = encodeURIComponent($(".tag_search_input").val());
				var searchName = encodeURIComponent($('#searchName').val());
				var month_id = encodeURIComponent($('#filter_month').val());
				var filter_year = encodeURIComponent($('#filter_year').val());
				var sales_executive = encodeURIComponent($('#sales_executive').val());
				var sales_executive_type = encodeURIComponent($('#sales_executive_type').val());
				var io = encodeURIComponent($('#io').val());
				var df1 = $("#material_request_filter_input").val();
				//var searchName = $("#searchName").val();
				//var sales_executive = $("#sales_executive").val();
				//alert(label_id);

				$.ajax({
					type: "POST",
					url: "attendance_report_genreport_excel.php",
					data: {
						searchName: searchName,
						sales_executive: sales_executive,
						sales_executive_type: sales_executive_type,
						df1: df1,
						io: io,
						month_id: month_id,
						filter_year: filter_year,
					},
					beforeSend: function() {
						$("#loading-modal").modal({
							backdrop: 'static',
							keyboard: false
						})
					},
					success: function(result) {
						setTimeout(function() {
							$("#loading-modal").modal('hide');
							window.open(result, '_blank');
						}, 1000);
					}
				});
			}

			function genAttandancePrint() {
				var searchName = $("#searchName").val();
				searchName = encodeURIComponent(searchName.trim());
				type = $("#type").val();
				var filter_month = encodeURIComponent($('#filter_month').val());
				var filter_year = encodeURIComponent($('#filter_year').val());
				class_id = $('#class_id').val();
				area = $("#area").val();
				status = $("#status").val();
				io = $("#io").val();
				var sales_executive = String($("#sales_executive").val());
				var sales_executive_type = String($("#sales_executive_type").val());
				type = $("#type").val();
				customer_type = $("#customer_type").val();
				sales_executive_id = $("#sales_executive_id").val();
				var myWindow = window.open('print_attandanceee_ajax.php?searchName=' + searchName + "&type=" + type + "&class_id=" + class_id + "&area=" + area + "&df=" + df1 + "&customer_type=" + customer_type + "&type=" + type + "&sales_executive_id=" + sales_executive_id + '&filter_month=' + filter_month + '&filter_year=' + filter_year + '&sales_executive=' + sales_executive + '&io=' + io + '&sales_executive_type=' + sales_executive_type, '', 'width=700,height=800');
				myWindow.print();
			}

			$('#InTimeData').on('show.bs.modal', function(event) {
				var button = $(event.relatedTarget) // Button that triggered the modal
				var requesting_id = button.data("id");
				$("#requesting_ajax").attr("data-url", "attandance_information_get_ajax_new.php?id=" + requesting_id);
				$("#requesting_ajax").click();
			})


			$('#OutTimeData').on('show.bs.modal', function(event) {
				var button = $(event.relatedTarget) // Button that triggered the modal
				var requesting_id = button.data("outid");
				$("#requesting_ajax1").attr("data-url", "attandance_out_information_get_ajax_new.php?id=" + requesting_id);
				$("#requesting_ajax1").click();
			})
		</script>
		<?php include("footer.php"); ?>

</body>

</html>