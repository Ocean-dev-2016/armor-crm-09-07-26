<?php
$page_id=560;$page_slug='page_order';
include("connect.php");
$page_title = 'Order Pipeline';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/task.css" />
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/custom.css" />
</head>
<body class="page-md">
	<div class="portlet-title">
        <div class="caption">
            <h2 class="caption-subject bold font-yellow-casablanca uppercase" style="color: #f2784b!important;margin-top: 15px!important;font-size: 16px;font-weight: bold;margin-left: 25px;"><a href="dashboard.php"><i class="fa fa-home" style="font-size:24px;"></i></a><span class="registername"> <?= $page_title ?></span>
            	<span style="float: right;padding-right: 20px;color: black;"><a target="_blank" href="dealer_orders_manage.php"><i class="fa  fa-arrow-circle-o-left" style="font-size:24px;"></i></a></span></h2>
        </div>
    </div>
   	<div class="container">
   		<!-- filter Section -->
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
				<div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
					<div class="row">
						<div class="col-md-6 col-xs-6 col-sm-6">
							<div class="col-md-3">
								<div class="form-group  pull-left">
                          	 		<label>Pagination Filter</label><br/>	
	                      	  		<select class="form-control input-small" id="page_id">
										<option value="">Select</option>
										<option value="10" selected="">Latest 10</option>
										<option value="50">Latest 50</option>
										<option value="100">Latest 100</option>
										<option value="1000">Latest 1000</option>
										<option value="">Latest All</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
	                            <div class="form-group">
	                          	 	<label>Order Type</label><br/>	
                          	  		<select class="form-control input-small" id="type">
										<option value="">Select Order Type</option>
										<?php
										$type_r = $db->rp_getData("customer_type", "id,name", "isDelete=0");
										if ($type_r) 
										{
											while ($type_d = mysqli_fetch_assoc($type_r)) 
											{
												?>
												<option value="<?= $type_d['id'] ?>" <?= ($type_d['id'] == $_REQUEST['type']) ? "selected" : ""; ?>><?= $type_d['name']; ?></option>
												<?php
											}
										}
										?>
									</select>
								</div>
	                        </div>
	                        <div class="col-md-3">
	                            <div class="form-group">
	                          	 	<label>Sales Officer</label><br/>	
                          	  		<select class="form-control input-small" id="sales_id">
										<option value="">Select Sales Officer</option>
										<?php
										$salesExe = $db->rp_getData("sales_executive", "*", "isDelete=0", "", 0);
										if ($salesExe) 
										{
											while ($salesD = mysqli_fetch_assoc($salesExe)) 
											{
												?>
												<option value="<?= $salesD['id'] ?>" <?= ($salesD['id'] == $_REQUEST['sales_id']) ? "selected" : ""; ?>><?= $salesD['name']; ?></option>
												<?php
											}
										}
										?>
									</select>
								</div>
                        	</div>
	                        <div class="col-md-3">
	                            <div class="form-group">
	                          	 	<label>Order Date</label><br/>	
                          	  		<div class="input-group">
										<input class="form-control datetimerange-picker-input" id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
										<span class="input-group-addon datetimerange-picker-btn">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
								</div>
	                        </div>
                        </div>

                        <div class="col-md-6 col-xs-6 col-sm-6">
                            <div class="form-inline" role="form">
                            	<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                            		<label>Search By Person Name/Order No</label><br/>	
                               		<div class="form-group">
                               			<input type="text" style="width: 450px!important" placeholder="Search By Person Name/Order No :  " class="form-control input-large" name="searchName" id="searchName" value="" />
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
		</div>
		<!-- filter Section -->
		<div class="portlet-body form">
			<div class="row">
				<div class="col-sm-12">
					<div id="label_container">
						<div class="task-container" style="min-height: 500px!important;max-height: 500px!important;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php //include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/bootstrap-ratingbar/jquery.barrating.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/order_pipeline.js"></script>
<script type="text/javascript">
    var CurrentTaskStatus = 0;
    var CurrentTaskID = 0;
    var TaskID = 0;
    var ShowHiddenTask = 0;
    var task_color_tag = "";
    var CurrentView = 0;
    var AdminView = 1;
    var searchName = "";
    var url = "order_pipeline_ajax_function.php";
    var CurrentDate = '<?php echo date('Y-m-d'); ?>';
    var EXPORT_URL = 'pipeline_report_generate.php';
</script>

<script type="text/javascript">
	$(".datetimerange-picker-btn").on("click", function() {
		$(".datetimerange-picker-input", $(this).closest(".date")).focus();
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
		$(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
	});
	function searchByName()
	{
		searchName = $("#searchName").val();
		page_id = $("#page_id").val();
		sales_id = $("#sales_id").val();
		type = $("#type").val();
		df1=$("#material_request_filter_input").val();
		df1 = encodeURI(df1);
		FetchTaskStatus();
		return false;
	}
	function clearSearchByName()
	{
		searchName = "";
		df1 = "";
		type = "";
		sales_id = "";
		$("#searchName").val("");
		$("#material_request_filter_input").val("");
		$("#type").val("");
		$("#sales_id").val("");
		FetchTaskStatus();
	}
</script>

<div class="modal fade" id="lostModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
    		<form method="post" name="lost_form" id="lost_form">
      			<div class="modal-header">
        			<h4 class="modal-title" id="exampleModalLabel">Reason for Lost</h4>
        			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          				<span aria-hidden="true">&times;</span>
        			</button>
      			</div>
		      	<div class="modal-body">
		      		<label>Reason for Lost</label>
		        	<textarea rows="4" name="lost_reason" id="lost_reason" style="width:100%;"></textarea>
		        	<input type="hidden" name="quotation_id" id="quotation_id" value="<?= $bid ?>">
		      	</div>
			    <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			        <button type="submit" name="submit" id="submit" class="btn btn-primary">Save changes</button>
			    </div>
    		</form>
    	</div>
  	</div>
</div>

<script type="text/javascript">
	function LostAdd(id)
	{
		var quotation_id = id;
		$("#lost_form").on("submit", function(e) 
		{
            e.preventDefault();
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = $("#lost_form").serialize();
            var lost_reason=$('#lost_reason').val();
            $.ajax(
            {
                url:"lost_reason_ajax.php",
                type:"POST",
                data: {
					quotation_id: quotation_id,
					lost_reason: lost_reason,
				},
                success:function(result)
                {
                    let jsonData = JSON.parse(result);  
                    if(jsonData.ack==1)
                    {                          
                        toastr.success("Lost this Order!");
                        $("#lost_form")[0].reset();
                        $("#lostModal").modal('hide');
                     	location.reload();   
                    }
                    else
                    {
                        toastr.error("Something went wrong...");
                    }
                }
            });
        });	
	}

	function GenerateOrder(qid) {
		var r = confirm("Are you sure to Generate Order???");
		if (r) {
			$.ajax({
				type: "post",
				url: "ajax_create_order.php",
				data: "qid=" + qid,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
					result = $.parseJSON(result);
					if (result.ack == 0) {
						toastr.error(result.ack_msg);
					} else {
						toastr.success(result.ack_msg);
						window.location.href = "orders_crud.php?mode=edit&id=" + result.order_id;
					}
				}
			})
		}
	}
	</script>

</body>
</html>