<?php
$page_id = 577;
$page_slug = 'visit_page';
$ctable 	= "visit";
$ctable1 	= "Visit";
$main_page 	= $ctable;
$page 		= "manage_" . $ctable;
$page_title = "Customer Visit";
$page_hierarchy = array(array("link" => "", "title" => "Sales & Marketing"), array("link" => $ctable . "_manage.php", "title" => $page_title));
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" />
	<link rel="stylesheet" href="<?= ADMINSITEURL ?>css/lightbox.css" />
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
									<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
										<div class="form-inline" role="form">
											<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
												<div class="form-group">
													<input type="text" style="width: 450px!important" placeholder="Search By Mobile No :  " class="form-control input-large" name="searchName" id="searchName" value="" />
												</div>
												<div class="form-group">
													<input class="btn btn-danger btn-sm" type="submit" value="search">
												</div>
												<div class="form-group">
													<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
												</div>

												<div class="form-group">
													<div class="btn-group">
														<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"><i class="fa fa-gear"></i></button>

														<ul role="menu" class="dropdown-menu dropdown-menu-right pull-right">
															<?php
															if ($rights['print_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
															?>
																<li>
																	<a name="print" onClick="genVisitPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
																</li>
															<?php
															}
															if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
															?>
																<li>
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
											<div class="table-responsive" style="overflow-x: hidden;">
												<div id="results"></div>
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

	<!-- view image modal -->
	<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="margin-top: -41px">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel"><b>View Image</b></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 15px;top: 25px">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="portlet-body" id="requesting_ajax" style=""></div>
			</div>
		</div>
	</div>
	<!-- view image modal -->

	<?php include("footer.php"); ?>
	<?php include("include_js.php"); ?>

	<script src="<?= ADMINSITEURL ?>js/lightbox.js"></script>
	<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
	<script type="text/javascript">
		var searchName = "";
		var state = "";
		var city = "";
		var df1 = "<?= $_REQUEST['redirecttocustomer'] == 1 ? "" : date('d-m-Y') . " to " . date('d-m-Y') ?>";
		var sales_executive = "<?= $_REQUEST['sales_id'] ?>";
		var customer_id = "<?= $_REQUEST['customer_id'] ?>";
		var company_id = "";
		var visit_month = "<?= $_REQUEST['visit_month'] ?>";
		var visit_year = "<?= $_REQUEST['visit_year'] ?>";
		var data_url = "visit_get_ajax.php";
		var todate = "<?= $_REQUEST['todate'] && $_REQUEST['redirecttocustomer'] != 1 ?  date('d-m-Y', strtotime($_REQUEST['todate'])) : '' ?>";
		var fromdate = "<?= $_REQUEST['todate'] && $_REQUEST['redirecttocustomer'] != 1  ? date('d-m-Y', strtotime($_REQUEST['fromdate'])) : '' ?>";
		var visit_type = "";
		if (todate != "" && fromdate != "" && todate != "01-01-1970" && fromdate != "01-01-1970") {
			df1 = todate + " to " + fromdate;
			fromdate = "";
			todate = "";
			visit_year = "";
		}
		df1 = encodeURI(df1);
		if (visit_year != "") {
			df1 = "";
		}

		// var data_url = "index_demo.php";

		function searchByName() {
			searchName = $("#searchName").val();
			sales_executive = $("#sales_executive").val();
			customer_id = $("#customer_id").val();
			company_id = $("#company_id").val();
			visit_type = $("#visit_type").val();
			displayRecords(100, 1);
			return false;
		}

		function clearSearchByName() {
			searchName = "";
			sales_executive = "";
			customer_id = "";
			company_id = "";
			visit_type = "";
			df1 = "";
			$("#searchName").val("");
			$("#customer_id").val("");
			$("#company_id").val("");
			$("#sales_executive").val("");
			$("#visit_type").val("");
			$("#material_request_filter_input").val("");
			displayRecords(100, 1);
		}
		$("#searchName").keyup(function(event) {
			if (event.keyCode == 13) {
				$("#searchByName").click();
			}
		});

		function loadDataTable() {
			$('#datatable_1').dataTable({
				"bPaginate": false,
				"bFilter": false,
				"bInfo": false,
				"bAutoWidth": false,
				"aoColumns": [{
						"sWidth": "5%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%"
					},
					{
						"sWidth": "10%"
					},
					// { "sWidth": "10%" },
				]
			});
		}

		function displayRecords(numRecords) {
			city = encodeURIComponent(city.trim());

			var searchName = $("#searchName").val();
			// var sales_executive 	= $("#sales_executive").val();
			searchName = encodeURIComponent(searchName.trim());
			$("#results").html("");
			$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&company_id=" + company_id + "&df=" + df1 + "&visit_month=" + visit_month + "&visit_year=" + visit_year + "&todate=" + todate + "&fromdate=" + fromdate + "&visit_type=" + visit_type, function() {
				loadDataTable();
			}); //load initial records

			//executes code below when user click on pagination links
			$("#results").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&sales_executive=" + sales_executive + "&customer_id=" + customer_id + "&company_id=" + company_id + "&df=" + df1 + "&visit_month=" + visit_month + "&visit_year=" + visit_year + "&todate=" + todate + "&fromdate=" + fromdate + "&visit_type=" + visit_type, {
					"page": page
				}, function() { //get content from PHP page
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
		function genReport() {
			var searchName = $("#searchName").val();
			var sales_executive = $("#sales_executive").val();
			var customer_id = $("#customer_id").val();
			var company_id = $("#company_id").val();
			var visit_type = $("#visit_type").val();
			var df1 = $("#material_request_filter_input").val();
			searchName = encodeURIComponent(searchName.trim());
			// window.location.href='visit_genReport_ajax.php?searchName='+searchName+'&sales_executive='+sales_executive+'&customer_id='+customer_id+'&df='+df1;


			$.ajax({
				method: "POST",
				url: "visit_genReport_ajax.php",
				data: {
					searchName: searchName,
					sales_executive: sales_executive,
					customer_id: customer_id,
					company_id: company_id,
					visit_type: visit_type,
					df1: df1,
					todate: todate,
					fromdate: fromdate,
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

	<script type="text/javascript">
		function genVisitPrint() {
			var searchName = $("#searchName").val();
			var sales_executive = $("#sales_executive").val();
			df1 = $("#material_request_filter_input").val();
			searchName = encodeURIComponent(searchName.trim());
			var myWindow = window.open('print_visit_ajax.php?searchName=' + searchName + '&sales_executive=' + sales_executive + '&df=' + df1 + '&customer_id=' + customer_id + '&company_id=' + company_id + "&visit_type=" + visit_type, '', 'width=700,height=800');
			myWindow.print();
		}
	</script>

</body>

</html>