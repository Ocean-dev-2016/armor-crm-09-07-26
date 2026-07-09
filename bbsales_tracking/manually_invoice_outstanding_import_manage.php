<?php
$page_id=666;$page_slug='manually_invoice_outstanding_import';
$ctable 	= "manually_invoice_outstanding_import";
$ctable1 	= "Manually A/c. Receivable Import";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"manually_invoice_outstanding_import_manage.php","title"=>$page_title));
include("connect.php"); 

$SEID = $db->rp_getvalue("dealer_distributor_network", "sales_executive_id", "id='" . $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "' ", 0);
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
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
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
				<div class="col-md-12 "><br/>
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
                            <div class="slimScrollDiv" style="position: relative; width: auto; height: auto;">
								
								<div class="row">
									<div class="col-md-5 col-xs-5 col-sm-5">
										<?php
										echo $db->getAddButton("manually_invoice_outstanding_import");
										?>
									</div>
                                 	<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
								  		<form class="form-inline pull-right" role="form">
								   			<div class="form-group"> 
												<input type="text" class="form-control input-medium" name="searchName" id="searchName" value="" placeholder="Search By Name:" />
											</div>
										 	<div class="form-group">
												<input class="btn btn-danger btn-sm" type="button" onClick="searchByName()" value="search">
											</div>
											 <div class="form-group">
												<input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
											</div> 
										</form>
								  	</div>
                                </div>
                            </div>
                        </div>
                
                	<!-- END Portlet PORTLET-->
                		</div>
					<div class="portlet light"> 
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

<div class="modal" id="FollowupResponse" role="dialog" aria-labelledby="myModalLabel2">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form role="form" action="" method="post" id="followuprespose">
					<div class="modal-header">
						<h4 class="modal-title">Followup Response <span id="response_followup_title"></span></h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<fieldset class="form-group floating-label-form-group">
							<label for="response">Response<span class="text-danger">*</span></label>
							<textarea class="form-control" value="" width="150px" id="response" name="response" placeholder="Enter Response" type="text"></textarea>
						</fieldset>
						<fieldset class="form-group floating-label-form-group">
							<label for="followup_action">Followup Action<span class="text-danger">*</span></label>
							<select class="form-control" id="followup_action" name="followup_action" onChange="showRelatedBlock(this)">
								<option value="">Select Followup Action</option>
								<option value="1">Next Followup</option>
								<!-- <option value="2">In Future</option> -->
								<option value="-1">End Followup</option>
							</select>
						</fieldset>
						<fieldset class="form-group floating-label-form-group followup_block_future" style="display:none">
							<label>Followup Future Date <span class="text-danger">*</span></label>
							<div class="input-group input-medium date ">
								<input type="text" class="form-control datetime-picker1" name="followup_future_date" id="followup_future_date" placeholder="Followup Date">
							</div>
						</fieldset>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
						<input type="hidden" name="followup_id" id="followup_id" value="">
						<button type="button" id="response_followup_btn" class="btn btn-success">Save </button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal" id="EndFollowupResponse" role="dialog" aria-labelledby="myModalLabel2">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form role="form" action="" method="post" id="followuprespose">
					<div class="modal-header">
						<h4 class="modal-title">Followup End Response <span id="end_response_followup_title"></span></h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<fieldset class="form-group floating-label-form-group">
							<label for="response">Response<span class="text-danger">*</span></label>
							<input type="hidden" name="end_followup_id" id="end_followup_id">
							<textarea class="form-control" readonly="" value="" width="150px" id="end_response" name="end_response" placeholder="Enter Response" type="text"></textarea>
						</fieldset>
						<fieldset class="form-group floating-label-form-group">
							<label for="email">Followup Reason<span class="text-danger">*</span></label>
							<select class="form-control" id="followup_reason_id" name="followup_reason_id">
								<option value="">Select Followup Reason</option>
								<?php
								$f_reason_r=$db->rp_getData("followup_reason","*","isDelete=0","",0);
								while($f_reason_d=mysqli_fetch_assoc($f_reason_r))
								{
								?>
								<option value="<?= $f_reason_d['id'] ?>"><?= $f_reason_d['name'] ?></option>
								<?php
								}
								?>
							</select>
						</fieldset>
						<fieldset class="form-group floating-label-form-group">
							<label for="email">Followup Status<span class="text-danger">*</span></label>
							<select class="form-control" id="followup_status_id" name="followup_status_id">
								<option value="">Select Followup Status</option> 
								<?php
								// $status1 = "";
									// $status1 = $db->rp_getValue("no_order_inquiry","status","id='".$_REQUEST['inquiry_id']."'","",0);
								?>
								<option <?=($status1==0)?"selecte":""; ?> value="0">Generate</option> 
								<option <?=($status1==2)?"selecte":""; ?> value="2">Positive</option> 
								<option <?=($status1==1)?"selecte":""; ?> value="1">In Followup</option>
								<option <?=($status1==4)?"selecte":""; ?> value="4">Hot</option>
								<option <?=($status1==5)?"selecte":""; ?> value="5">Cold</option> 
								<option <?=($status1==6)?"selecte":""; ?> value="6">Warm</option> 
								<option <?=($status1==-2)?"selecte":""; ?> value="-2">Cancel</option> 
								<option <?=($status1==-1)?"selecte":""; ?> value="-1">Working</option>
								<option <?=($status1==3)?"selecte":""; ?> value="3">Buy Later</option>
								<option <?=($status1==11)?"selecte":""; ?> value="11">Lost</option>
							</select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
						<button type="button" id="end_response_followup_btn" class="btn btn-success">Save </button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal" id="createFollowup" role="dialog" aria-labelledby="myModalLabel1">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form role="form" action="" method="post" id="formLocation">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel1">Create Followup</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body"> 
						<fieldset class="form-group floating-label-form-group">
							<label for="email">Description<span class="text-danger">*</span></label>
							<textarea class="form-control" width="150px" id="description" name="description" placeholder="Enter Description" type="text"></textarea>
						</fieldset>
						<fieldset class="form-group floating-label-form-group">
							<label for="email">Followup Through<span class="text-danger">*</span></label>
							<select class="form-control" id="through" name="through">
								<option value="0">Select Followup Through</option>
								<option value="1">Call</option>
								<option value="2">Sms</option>
								<option value="3">Email</option>
								<!-- <option value="4">Whatsapp</option> -->
							</select>
						</fieldset>
						<fieldset class="form-group floating-label-form-group">
							<label>Followup Date <span class="text-danger">*</span></label>
							<div class="input-group input-medium date ">
								<input type="text" class="form-control datetime-picker" disabled name="followup_date" id="followup_date" placeholder="Followup Date" value="<?= date("Y/m/d H:i") ?>">
								<span class="input-group-btn">
									<button class="btn default" type="button">
										<i class="fa fa-calendar"></i>
									</button>
								</span>
							</div>
						</fieldset>
						<fieldset class="form-group floating-label-form-group">
							<label for="followup_status">Followup Status<span class="text-danger">*</span></label>
							<select class="form-control" id="followup_status" name="followup_status">
								<option value="">Select Followup Status</option>
								<option value="0">Generate</option> 
							  	<option value="2">Positive</option> 
								<option value="1">In Followup</option>
								<option value="4">Hot</option>
								<option value="5">Cold</option> 
								<option value="6">Warm</option> 
								<option value="-1">My Work</option>
								<option value="3">Buy Later</option>
								<option value="-2">Cancel</option> 
								<option value="11">Lost</option> 
							</select>
						</fieldset> 
					</div>
					<div class="modal-footer">

						<input type="hidden" name="visitor_id" id="visitor_id" value="">
						<input type="hidden" name="invoice_id" id="invoice_id" value="">
						<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
						<button type="button" id="save_followup" class="btn btn-success">Save </button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>
<script type="text/javascript">
var sales_id="";
var customer_id="";
var searchName="";
var data_url = "manually_invoice_outstanding_import_get_ajax.php";

function searchByName(){ 
	displayRecords(500,1); 
}
function clearSearchByName(){
	searchName = "";
	customer_id = "";
	sales_id = "";
	$("#searchName").val("");
	$("#customer_id").select2("val","");
	$("#sales_id").select2("val","");
	
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
}); 
function loadDataTable(){
	
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			  { "sWidth": "10%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	var customer_id 	= $("#customer_id").val();
	var sales_id 	= $("#sales_id").val();
	searchName 	= encodeURIComponent(searchName.trim()); 
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_id=" + sales_id + "&customer_id=" + customer_id,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_id=" + sales_id + "&customer_id=" + customer_id,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(500,1);
}); 

		function showRelatedBlock(spn) {
			$("fieldset.followup_block_future").hide(10);
			if ($(spn).val() == 1) {
				// $("#createFollowup").modal('show');
				// $("#FollowupResponse").modal('hide');
			} else if ($(spn).val() == 2) {
				$("fieldset.followup_block_future").show(100);
			} else if ($(spn).val() == -1) {
				var response1 = $("#response").val();
				if (response1 == "") {
					toastr.error("Please Enter first Response");
					$("#followup_action").select2("val", "");
				} else {
					var fid = $("#response_followup_btn").data("id");
					var title = $("#response_followup_title").html();
					$("#FollowupResponse").modal('hide');
					$("#EndFollowupResponse").modal('show');
					$("#end_response").html(response1);
					$("#end_response_followup_title").html(title);
					$("#end_followup_id").val(fid);
				}
			}
		}
		$('#createFollowup').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget) // Button that triggered the modal
			// invoice_id = button.data("id"); 
			// visitor_id = button.data("visitor_id");  
			invoice_id = $("#openCreateFollowupModal").data("id");
	        visitor_id =$("#openCreateFollowupModal").data("visitor_id");

			$("#invoice_id").val(invoice_id);
			$("#visitor_id").val(visitor_id);
		});

		$('#FollowupResponse').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget) // Button that triggered the modal
			var followup_id = button.data("id");
			var title = button.data("date");
			var next_action = button.data("next_action");
			var mode = button.data("mode");
			if (mode == "edit") {
				$("#followup_action").attr("disabled", "disabled");
			} else {
				$("#followup_action").removeAttr("disabled", "disabled");
			}
			var response = button.data("response");
			$("#response_followup_btn").attr("data-id", followup_id);
			$("#followup_id").val(followup_id);
			$("#response_followup_title").html(title);
			$("#response").val(response);
			$("#followup_action").val(next_action);
			$("#followup_action").select2("destroy");
			$("#followup_action").select2();
		});
		$('.datetime-picker').datetimepicker({
			formatTime: 'H:i',
			formatDate: 'd.m.Y',
			minDate: '0',
			timepickerScrollbar: false,
			container: '#modal_followup modal-body'
		});
		$('.datetime-picker').parent("div.input-group").find("span.input-group-btn").on("click", function() {
			$('.datetime-picker').removeAttr("disabled");
			$('.datetime-picker').datetimepicker("show");
		})
		$('.date-picker').datetimepicker({
			format: 'Y/m/d',
			minDate: '0',
			timepicker: false,
			container: '#createFollowup modal-body'
		});
		$('.date-picker').parent("div.input-group").find("span.input-group-btn").on("click", function() {
			$('.date-picker').removeAttr("disabled");
			$('.date-picker').datetimepicker("show");
		})

		$(function() {
			$("#save_followup").on('click', function() {
				// var invoice_id = "";
				var followupdate = new Date($('#followup_date').val());
				var day_followup = followupdate.getDate();
				var month_followup = followupdate.getMonth() + 1;
				var year_followup = followupdate.getFullYear();
				var followup_date_format = `${year_followup}/${month_followup}/${day_followup}`;
				var date = new Date();
				let day = date.getDate();
				let month = date.getMonth() + 1;
				let year = date.getFullYear();
				let currentDate = `${year}/${month}/${day}`;
				var date1 = new Date(followup_date_format);
				var date2 = new Date(currentDate);
				if (date2 > date1) {
					toastr.error("Please Select Valid Followup Date!!");
				} else {
					CreateFollowup();
				}
			});
		})


		function CreateFollowup() {
			var isValid = true;
			var sales_id = '<?php echo $SEID ?>';
			var followup_flag = "manual_invoice_import"; 
 
			var invoice_id = $("#invoice_id").val();
			var visitor_id = $("#visitor_id").val();
			var description = $('#description').val();
			var through = $('#through').val();
			var followup_date = $('#followup_date').val();
			var followup_status = $('#followup_status').val();
			// alert(visitor_id);
			// alert(invoice_id);
			if (description != "") {
				if (through != "" && through != 0) {
					if (followup_date != "") {
						$.ajax({
							type: "GET",
							url: "followup_ajax_function.php",
							data: {
								m: "save_followup",
								description: description,
								through: through,
								followup_date: followup_date,
								visitor_id: visitor_id,
								invoice_id: invoice_id,
								followup_flag: followup_flag,
								sales_id: sales_id,
								followup_status: followup_status,  
							},
							success: function(result) {

								var result = $.parseJSON(result);

								if (result.a == 1) {
									//alert(result.invoice_id);
									$("#createFollowup").modal('hide'); 
									toastr.success(result.mg);
									// location.reload();
									$("#description").val("");
									$("#through").select2("val","");
									$("#followup_status").select2("val","");
									$("#followup_date").val(""); 

									followup_grid_load(invoice_id, "manual_invoice_import");

								} else {
									toastr.error(result.mg);
								}
							}
						})
					} else {
						toastr.error("Followup Date Required!!");
					}
				} else {
					toastr.error("Followup Through Required!!");
				}
			} else {
				toastr.error("Description Required!!");
			}
		} 

		$("#end_response_followup_btn").on('click', function() {

			var isValid = true;
			var followup_future_date = "";
			var response = $("#end_response").val();
			var invoice_id = $("#invoice_id").val();

			var quotation_id = "<?= $_REQUEST['quotation_id'] ?>";
			var followup_action = -1;
			var status = $("#end_followup_action").val();
			var followup_id = $("#end_followup_id").val();
			var followup_reason_id_response = $("#followup_reason_id_response").val();
			var followup_status_id = $("#followup_status_id").val();
			if (response == "") {
				isValid = false;
				toastr.error("Enter response!!", "Error");
			}
			if (status == "") {
				isValid = false;
				toastr.error("Select Next Action!!", "Error");
			}
			// if(followup_reason_id=="")
			// {
			// 	isValid=false;
			// 	toastr.error("Select Followup Reason!!","Error");
			// }
			if (isValid) {
				$.ajax({
					type: "GET",
					url: "followup_ajax_function.php",
					data: {
						response: response,
						followup_id: followup_id,
						followup_action: followup_action,
						followup_future_date: followup_future_date,
						invoice_id: invoice_id,
						quotation_id: quotation_id,
						status: status,
						followup_reason_id_response: followup_reason_id_response,
						followup_status_id: followup_status_id,
						m: "end_followup"
					},
					success: function(json) {
						json = $.parseJSON(json);
						msg = json.mg;
						if (json.a == 1) {
							toastr.success(msg, "Success!!");
							$("#EndFollowupResponse").modal('hide');
							followup_grid_load(json.reference_id, "manual_invoice_import");
 
						} else {
							toastr.error(msg, 'Error!!')
						}
					}
				});
			}
		});

		$("#response_followup_btn").on('click', function() {

			var isValid = true;
			var followup_future_date = "";
			var response = $("#response").val();
			var followup_reason_id = $("#followup_reason_id").val();
			var followup_action = $("#followup_action").val();
			followup_future_date = $("#followup_future_date").val();
			/*var followup_id=$(this).data("id");*/
			var followup_id = $("#followup_id").val();
			if (response == "") {
				isValid = false;
				toastr.error("Enter response!!", "Error");
			}
			if (followup_action == "") {
				isValid = false;
				toastr.error("Select Next Action!!", "Error");
			}
			if (followup_action == 2) {
				if (followup_future_date == "") {
					isValid = false;
					toastr.error("Select Next Action!!", "Error");
				}
			}
			$.ajax({
				type: "GET",
				url: "followup_ajax_function.php",
				data: {
					response: response,
					followup_id: followup_id,
					followup_reason_id: followup_reason_id,
					followup_action: followup_action,
					followup_future_date: followup_future_date,
					m: "add_response"
				},
				success: function(json) {
					json = $.parseJSON(json);
					msg = json.mg;
					if (json.a == 1) {
						toastr.success(msg, "Success!!");
						$("#FollowupResponse").modal("hide");
						if (followup_action == 1) {

							$("#createFollowup").modal('show');
						}
						$("response_followup_title").val("");
						$("#followup-ajax-result-container-1").empty();
						//ChannelAjax.init();
						//location.reload();
						$("#response").val("");
						$("#followup_reason_id").val("");
						$("#followup_action").val("");
						$("#followup_future_date").val("");
						invoice_id = json.invoice_id;
						followup_grid_load(json.reference_id, "manual_invoice_import");
						//location.reload();
					} else {
						toastr.error(msg, 'Error!!')
					}
				}
			});
		});

		function followup_grid_load(id, followup_flag, sales_id = "", visitor_id = "") {
			//alert(id);
			$("#EndFollowupResponse").modal('hide');
			//alert(id);
			var invoice_id = id;
			var sales_id = sales_id;
			var visitor_id = visitor_id;
			$.ajax({
				url: "followup_data_ajax.php",
				data: {
					reference_id: invoice_id,
					followup_flag: "manual_invoice_import",
					sales_id: sales_id,
					visitor_id: visitor_id,
				},
				beforeSend: function() {
					$("#followup_data").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
				},
				success: function(result) {
					$("#followup_data").html(result);
				}
			});
		}

		function sendWhatsappMsg(id)
		{
			var r = confirm("Are you sure you want to send sms to whatsapp?");
			if(r) {
				$.ajax({
					type: "POST",
					url: "manually_invoice_outstanding_import_ajax_function.php",
					data: {
						id: id, 
					},
					success: function(json) {
						json = $.parseJSON(json);
						msg = json.mg;
						if (json.ack == 1) {
							toastr.success(msg, "Success!!"); 
						} else {
							toastr.error(msg, 'Error!!')
						}
					}
				});
			}
		}
</script>
</body>

</html>