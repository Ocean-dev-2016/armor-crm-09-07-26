<?php
$page_id = 604;
$page_slug = 'active_deactive_customer_report_page';
$ctable 	= "executive";
$ctable1 	= "Active/Deactive Customer";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = $ctable1 . " Report";
$page_hierarchy = array(array("link" => "", "title" => "Report"), array("link" => "manage_" . $ctable . ".php", "title" => $page_title));
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
	<link rel="stylesheet" type="text/css" href="css/fSelect.css" />
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
					<div class="col-md-12">
						<?php $db->printErrorMessage(); ?>
						<?php $db->printSuccessMessage(); ?>
					</div>
					<div class="col-md-12">
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
								<div class="slimScrollDiv" style="position: relative;width: auto; height: auto;">
									<div class="row filter_list">
										<!-- <div class="col-md-3 col-xs-3 col-sm-3 " style="margin-top:10px">
								  		<label>Filter By Date:</label>
								  		<div class="input-group">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
												<i class="fa fa-calendar"></i>
											</span>
										
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
								        </div>
                                    </div> -->
										<div class="col-md-2 col-xs-2 col-sm-2 " style="margin-top:10px">
											<label>Customer Status<code>*</code></label><br />
											<select class="form-control" id="customer_status" name="customer_status">
												<option value="">Select Status</option>
												<option value="1">Active</option>
												<option value="0">Deactive</option>
											</select>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Search By Customer Type</label><br />
											<div class="form-group" role="form">
												<select class="form-control" multiple="multiple" name="customer_type" id="customer_type">
													<option value="">Customer Type</option>
													<?php
													$customer_type_r = $db->rp_getData("customer_type", "*", "isDelete=0", 0);
													if (mysqli_num_rows($customer_type_r) > 0) {
														while ($customer_type_d = mysqli_fetch_array($customer_type_r)) {
													?>
															<option value="<?php echo $customer_type_d['id']; ?>" <?= ($customer_type == $customer_type_d['id']) ? "selected" : ""; ?>><?php echo $customer_type_d['name']; ?></option>
													<?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Search By Customer Name</label><br />
											<div class="form-group" role="form">
												<select class="form-control" name="customer_id" multiple="multiple" id="customer_id">
													<option value="">Customer Name</option>
													<?php
													$E_r = $db->rp_getData("executive", "id,cname,company_name,client_code", "", "cname ASC", 0);
													while ($E = mysqli_fetch_assoc($E_r)) {
														$customer_flag_text = "";
														if ($E['customer_flag'] == 1) {
															$customer_flag_text = " - P";
														} else if ($E['customer_flag'] == 0) {
															$customer_flag_text = " - C";
														}
													?>
														<option value="<?= $E['id'] ?>" <?= ($cid == $E['id']) ? "selected" : ""; ?>><?= $E['company_name'] . "-" . $E['cname'] . "-" . $E['client_code'] . $customer_flag_text ?></option>
													<?php
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2 hidden" style="margin-top:10px">
											<label>Search By Month</label><br />
											<select class="form-control" id="month" name="month" onchange="getMonth(this.value)">
												<option value="">Select Month</option>
												<option value="3">3 Months</option>
												<option value="6">6 Months</option>
												<option value="9">9 Months</option>
												<option value="12">12 Months</option>
											</select>
										</div>

										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<div class="form-group" role="form">
												<label>Search by State: </label>
												<select class="form-control" multiple="multiple" name="state" id="state">
													<option value="">Select State</option>
													<?php
													$cid_r = $db->rp_getData("class", "*", 0);
													if (mysqli_num_rows($cid_r) > 0) {
														while ($cid_d = mysqli_fetch_array($cid_r)) {
													?>
															<option value="<?php echo $cid_d['name']; ?>"><?php echo $cid_d['name']; ?></option>

													<?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<div class="form-group">
												<label>Search by City: </label>
												<select class="form-control" multiple="multiple" name="city" id="city" autofocus>
													<option value="">Select City</option>
												</select>
											</div>
										</div>

										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<div class="form-group">
												<label>Search by Route: </label>
												<select class="form-control" multiple="multiple" name="route" id="route" autofocus>
													<option value="">Select Route</option>
												</select>
											</div>
										</div>

									</div>
									<div class="row">
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										</div>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<label>Days: <code>*</code></label>
											<input type="text" name="days" id="days" class="form-control">
										</div>
										<!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
								  		<div class="form-group" role="form">
											<label>Search by Company: </label>
                                        	<select class="form-control" multiple="multiple" name="type_of_company" id="type_of_company">
                                        		<option value="">Select Comapny</option>
												<?php
												$company_r = $db->rp_getData("brand", "*", "isDelete=0 AND isActive=1", 0);
												if ($company_r > 0) {
													while ($company_d = mysqli_fetch_array($company_r)) {
												?>
												<option value="<?php echo $company_d['id']; ?>"><?php echo $company_d['name']; ?></option>

												<?php
													}
												}
												?>
                                        	</select>
										</div>
                                    </div> -->
										<div class="col-md-6 col-xs-6 col-sm-6" style="margin-top:10px">
											<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
												<div class="form-group">
													<input type="text" class="form-control input-medium" name="searchName" id="searchName" placeholder="Search By Company / Person / Phone:" value="" style="width: 300px!important" />
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
																	<a name="print" onClick="genCustomerPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
										<!-- <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">

								   		<?php
											if ($rights['print_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
											?>

								   		<button type="button" class="btn print btn-sm" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genCustomerPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>

								   		<?php
											}
											if ($rights['export_excel_flag'] == 1 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
											?>
										<button type="button" class="btn green-haze excel btn-sm" name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<?php
											}
										?>
										
									</div> -->

									</div>

								</div>
								<div class="row">

								</div>
							</div>
						</div>
						<!-- END Portlet PORTLET-->
					</div>
					<div class="col-md-12">
						<div class="portlet light">
							<div class="portlet-body">

								<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;"> </div>
								<div class="">
									<div id="results"></div>
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

	<script type="text/javascript" src="js/fSelect.js"></script>

	<script type="text/javascript">
		$("#customer_id").fSelect();
		$("#customer_type").fSelect();
		$("#customer_status").select2();
		$("#state").fSelect();
		$("#city").fSelect();
		$("#route").fSelect();
		$("#type_of_company").fSelect();
	</script>

	<script type="text/javascript">
		var searchName = "";
		var state = "";
		var city = "";
		var route = "";
		var df1 = "";
		var customer_type = "";
		var customer_id = "";
		var month = "";
		var customer_status = "";
		var type_of_company = "";
		var days = "";
		var isFillter = false;
		var data_url = "active_deactive_customer_get_ajax.php";
		// var data_url = "index_demo.php";

		function searchByName() {

			searchName = $("#searchName").val();
			// city = $("#city").val();
			city = encodeURIComponent($("#city").val());
			state = encodeURIComponent($("#state").val());
			route = encodeURIComponent($("#route").val());
			customer_type = $("#customer_type").val();
			customer_id = $("#customer_id").val();
			isFillter = true;
			month = $("#month").val();
			days = $("#days").val()

			customer_status = $("#customer_status").val();
			// alert(customer_status);
			if (customer_status != "") {
				if (days == "") {
					toastr.error("Please select Days");
				} else {
					displayRecords(100, 1);
				}

			} else if (days != "") {
				if (customer_status == "") {
					toastr.error("Please select status");
				} else {
					displayRecords(100, 1);
				}

			}

			// a	
			else {
				displayRecords(100, 1);
			}
			// alert()

			/*if(state!="")
			{
				getCity(state);
			}*/
			return false;
		}
		$(".filterBtn").on("click", function() {
			df1 = $("#material_request_filter_input").val();
			df1 = encodeURI(df1)
			displayRecords(100, 1);
		})

		/*$("#month").on("change",function()
		{
			var month=$(this).val();
			alert(month);
		});*/

		$("#state").on('change', function() {
			var state = $("#state").val();
			getCity(state, "");
		});

		$("#city").on('change', function() {
			var city = $("#city").val();
			getRoute(city, "");
		});

		function getCity(state) {
			$.ajax({
				type: "POST",
				url: "ajax_get_main_city.php",
				data: 'sid=' + state,
				beforeSend: function() {
					$('.preloader').fadeIn('slow');
				},
				success: function(data) {
					$("#city").select2("destroy");
					$("#city").fSelect("destroy");
					$("#city").html(data);
					$("#city").fSelect('create');
					$('.preloader').fadeOut('slow');
				}
			});
		}

		function getRoute(city) {
			$.ajax({
				type: "POST",
				url: "ajax_get_city.php",
				data: 'sid=' + city,
				beforeSend: function() {
					$('.preloader').fadeIn('slow');
				},
				success: function(data) {
					$("#route").select2("destroy");
					$("#route").fSelect("destroy");
					$("#route").html(data);
					$("#route").fSelect('create');
					$('.preloader').fadeOut('slow');
				}
			});
		}

		function getMonth(s) {
			month = s;
		}

		function getStatus(s) {
			customer_status = s;
			displayRecords(100, 1);
		}

		function clearSearchByName() {
			searchName = "";
			state = "";
			city = "";
			route = "";
			customer_type = "";
			month = "";
			customer_id = "";
			df1 = "";
			customer_status = "";
			type_of_company = "";
			days = "";
			isFillter = false;
			$("#searchName").val("");
			$("#days").val("");

			$("#state").fSelect("destroy");
			$("#state").val("");
			$("#state").fSelect("create");

			$("#city").fSelect("destroy");
			$("#city").val("");
			$("#city").fSelect("create");

			$("#route").fSelect("destroy");
			$("#route").val("");
			$("#route").fSelect("create");

			$("#customer_type").fSelect("destroy");
			$("#customer_type").val("");
			$("#customer_type").fSelect("create");

			$("#customer_id").fSelect("destroy");
			$("#customer_id").val("");
			$("#customer_id").fSelect("create");

			$("#customer_status").fSelect("destroy");
			$("#customer_status").val("");
			$("#customer_status").fSelect("create");

			$("#type_of_company").fSelect("destroy");
			$("#type_of_company").val("");
			$("#type_of_company").fSelect("create");

			// $("#state").select2("val","");
			// $("#city").select2("val","");
			// $("#customer_type").val("");
			$("#month").val("");
			// $("#customer_id").val("");
			$("#customer_status").select2("val", "1");
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
				]
			});
		}

		function displayRecords(numRecords) {
			var searchName = $("#searchName").val();
			var days = $("#days").val();
			var type_of_company = $("#type_of_company").val();
			searchName = encodeURIComponent(searchName.trim());
			$('.preloader').fadeIn('slow');
			$("#results").html("");
			$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&customer_type=" + customer_type + "&customer_id=" + customer_id + "&df=" + df1 + "&month=" + month + "&state=" + state + "&city=" + city + "&route=" + route + "&customer_status=" + customer_status + "&type_of_company=" + type_of_company + "&days=" + days + "&isFillter=" + isFillter, function() {
				$('.preloader').fadeOut('slow');
				loadDataTable();
			}); //load initial records

			//executes code below when user click on pagination links
			$("#results").on("click", ".paging_simple_numbers a", function(e) {
				e.preventDefault();
				var numRecords = $("#numRecords").val();
				$(".loading-div").show(); //show loading element
				var page = $(this).attr("data-page"); //get page number from link
				$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&customer_type=" + customer_type + "&customer_id=" + customer_id + "&df=" + df1 + "&month=" + month + "&state=" + state + "&city=" + city + "&route=" + route + "&customer_status=" + customer_status + "&type_of_company=" + type_of_company + "&days=" + days + "&isFillter=" + true, {
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
			// displayRecords(100,1);
			$("#results").html("<h2 class='text-center' style='margin:0;padding:0'>Select Filter To See Result</h2>");
		});

		function del_conf(id) {
			var r = confirm("Are you sure you want to delete?");
			if (r) {
				window.location.href = '<?php echo $ctable; ?>_crud.php?mode=delete&id=' + id;
			}
		}
	</script>
	<script type="text/javascript">
		$(".datetimerange-picker-btn").on("click", function() {
			$(".datetimerange-picker-input", $(this).closest(".input-group")).focus();
		});
		$(".datetimerange-picker-input").daterangepicker({
			"format": "dd-mm-yy ",
			autoUpdateInput: false,
			timePicker: false,
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		});
		$('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
			$(".datetimerange-picker-input").val(picker.startDate.format('YYYY-MM-DD') + " to " + picker.endDate.format('YYYY-MM-DD'));
		});
	</script>

	<script type="text/javascript">
		function genReport() {
			var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());

			var customer_type = String($("#customer_type").val());
			var customer_id = String($("#customer_id").val());
			var state = String($("#state").val());
			var city = String($("#city").val());
			var route = String($("#route").val());
			// var customer_status = String($("#customer_status").val());

			// var customer_type = $("#customer_type").val();
			// var customer_id = $("#customer_id").val();
			// var df1 = $("#material_request_filter_input").val();
			// var state = $('#state').val();
			// var city = $("#city").val();
			var customer_status = String($("#customer_status").val());
			var month = $("#month").val();
			var days = $("#days").val();
			var df1 = $("#material_request_filter_input").val();
			if (customer_status == "") {
				toastr.error("Please select status");
			} else if (days == "") {
				toastr.error("Please select days");
			} else {

				// alert(month);
				/*window.location.href='complain_genReport_ajax.php?searchName='+searchName+'&customer_type='+customer_type+'&customer_id='+customer_id+'&df='+df1+"&state="+state+"&city="+city;*/
				$.ajax({
					method: "POST",
					url: "active_deactive_customer_genReport_ajax.php",
					data: {
						searchName: searchName,
						customer_type: customer_type,
						customer_id: customer_id,
						df1: df1,
						customer_status: customer_status,
						state: state,
						city: city,
						route: route,
						month: month,
						days: days
					},
					dataType: 'json',
					beforeSend: function() {

					},
					success: function(result) {
						// alert(result);
						window.location.href = "<?= SITEURL ?>" + result.file_path;
					},
					/*error:function(result){
						window.location.href="<?= SITEURL ?>"+result.file_path;
					}*/
				});

			}
		}
	</script>

	<script type="text/javascript">
		function genCustomerPrint() {
			var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			var customer_type = $("#customer_type").val();
			var customer_id = $("#customer_id").val();
			var df1 = $("#material_request_filter_input").val();
			var state = $('#state').val();
			var city = $("#city").val();
			var route = $("#route").val();
			var customer_status = String($("#customer_status").val());
			var month = $("#month").val();
			var days = $("#days").val();
			if (customer_status == "") {
				toastr.error("Please select status");
			} else if (days == "") {
				toastr.error("Please select days");
			} else {
				var myWindow = window.open('print_active_deactive_customer.php?searchName=' + searchName + '&customer_type=' + customer_type + '&df=' + df1 + '&customer_id=' + customer_id + "&state=" + state + "&city=" + city + "&route=" + route + "&month=" + month + "&customer_status=" + customer_status + "&days=" + days, '', 'width=700,height=800');
				myWindow.print();
			}
		}


		function editStatus(id) {
			$("#complain_status" + id).removeAttr("disabled");
			$("#editStatus_" + id).hide(500);
			$("#editStatus2_" + id).show(400);
		}

		function cancelEditStatus(id) {
			$("#editStatus2_" + id).hide(500);
			$("#editStatus_" + id).show(400);
			$("#complain_status" + id).attr("disabled", "disabled");
		}

		function saveEditStatus(id) {
			var newcomplain_status = $("#complain_status" + id).val();
			// alert(newcomplain_status);
			$.ajax({
				type: "POST",
				url: "ajax_update_status.php",
				data: "id=" + id + "&status=" + newcomplain_status + '&table=complain',
				cache: false,
				beforeSend: function() {

				},
				success: function(html) {

					var result = $.parseJSON(html);
					if (result.ack == 1) {
						toastr.success(result.ack_msg);
						cancelEditStatus(id);
					} else {
						toastr.error(result.ack_msg);
					}
					if (html == 1) {

						toastr.success("Status Updated Successfully");
					}
				}
			});

		}
	</script>



</body>

</html>