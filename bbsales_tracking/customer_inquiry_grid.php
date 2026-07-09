<?php
$page_id        = 605;
$page_slug      = 'customer_inquiry';
$ctable         = "customer_inquiry";
$ctable1        = "Customer Inquiry";
$main_page      = $ctable;
$page           = $ctable;
// $page_title     = "Inquiry";
$page_title     = "Lead";
$page_hierarchy = array(
    array(
        "link" => "",
        "title" => "Sales & Marketing"
    ),
    array(
        "link" => $ctable . "_manage.php",
        "title" => $page_title
    )
);
$FromDate       = "";
$ToDate         = "";
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
		<?php include("include_css.php"); ?>
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
		<style type="text/css">
			.btn.btn-sm {
				padding: 9px 14px 8px 14px!important;
			}
			.icon-black i
	        {
	            color:#0f0f0ff2!important;
	        }
	        .font-black
	        {
	          color:#000!important;
	        }
	        .jqx-grid-column-header{color:#fff;background-color:#999!important;font-size:12px}.jqx-grid-column-header-blue{color:#333;background-color:#eee!important}.jqx-grid-columngroup-header{background-color:#fff!important;color:#333!important}.dropdown-menu:not(.opensright){left:auto!important}.color_india_mart{background-color:red!important}.color_trade_india{background-color:#ff0!important}.overflow-view{overflow:visible!important;text-align:center}#jqxScrollAreaDownverticalScrollBarjqxgrid,#jqxScrollAreaUpverticalScrollBarjqxgrid,#jqxScrollBtnUpverticalScrollBarjqxgrid,#jqxScrollThumbverticalScrollBarjqxgrid,#jqxScrollWrapverticalScrollBarjqxgrid{z-index:100!important}#jqxScrollWrapverticalScrollBarjqxgrid{width:16px!important}
	        
	        .dropdown-menu:not(.opensright) {
	        	left: auto!important;
	        }
	        .color_india_mart
	        {
	        	background-color: red!important;
	        }
	        .color_trade_india
	        {
	        	background-color: yellow!important;
	        }
	        .overflow-view
	        {
	        	overflow: visible!important;
	        	text-align: center;
	        }
		</style>
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
		            <!-- <div class="col-md-12"> -->
		               <?php $db->printErrorMessage(); ?>
		               <?php $db->printSuccessMessage(); ?>
		               <div class="col-md-12" style="margin: 10px 0!important;padding: 0!important">
			               	<div class="row">
	                       	  <div class="col-md-4 col-xs-4 col-sm-4">
	                       	 	<a class="btn sbold blue-ebonyclay" href='<?php echo $ctable; ?>_crud.php?mode=add'> Add New
	                            <i class="fa fa-plus"></i>
	                            </a>										
	                          </div>
	                          <div class="col-md-8 col-xs-8 col-sm-8 pull-right">
	                                <div class="form-inline" role="form">
	                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
	                                       <div class="form-group">
	                                          <input type="text" style="width: 450px!important" placeholder="Search By Person / Company Name / Mobile Number/ Email:  " class="form-control input-large" name="searchName" id="searchName" value="" />
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
													<ul role="menu" class="dropdown-menu dropdown-menu-right">
														<li>
															<a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
														</li>
														<li>
															<a name="print" onClick="genInquiryPrint()"  title="Print Report"><i class="fa fa-print"></i>Print</a>
														</li>
														<li>
															<a class="excel" name="excel" onClick="genExcelReport()" id="excel"  title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
														</li>
													</ul>
												</div>
	                                       </div>
	                                    </form>
	                                 </div>
	                          </div>
	                      	</div>
		               </div>
		            <!-- </div> -->
		               <div class="row">
			               <div class="portlet light" style="padding: 0!important">
			                  <div class="portlet-body">
			                     <div class="loading-div" style="display:none;"> 
			                     	<img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;">
			                     </div>
			                     <div id="results" style="margin-left:1%;"></div>
			                  </div>
			               </div>
			           </div>
		         </div>
		      </div>
		   </div>
		</div>

<?php include("footer.php"); ?>


<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- sheet upload function -->
<link href="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
<script src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<div class="modal fade" id="uploadLeeds" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Upload Excel Data</h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-sm-12">
                  <div class="form-group">
                     <div class="fileinput fileinput-new" data-provides="fileinput">
                        <label class="control-label">Select XLS/XLSX File</label>	
                        <div class="input-group input-large">
                           <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                              <i class="fa fa-file fileinput-exists"></i>&nbsp;
                              <span class="fileinput-filename"> </span>
                           </div>
                           <span class="input-group-addon btn default btn-file">
                           <span class="fileinput-new"> Select file </span>
                           <span class="fileinput-exists"> Change </span>
                           <input type="file" name="excel_sheet" id="excel_sheet"> </span>
                           <a href="javascript:;" id="remove_file" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-sm-12">
                  <div id="result_upload">
                     <div class="row">
                        <div class="col-sm-12">
                           <h3 class="inserted_log">Updated:</h3>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="upload_sheet"><i class="fa fa-upload"></i> &nbsp;Start Upload</button>
         </div>
      </div>
   </div>
</div>
<!-- sheet upload function -->

<script type="text/javascript">
$('#ToDate').datepicker({
	datepicker: true,
	autoclose: true
});
$('#FromDate').datepicker({
	datepicker: true,
	autoclose: true
});

var ToDate = "";
var FromDate = "";
var type = "";
var assigned_to = "";
var searchName = "";
var status_id = "";
var c_type = "";
var country = "";
var city = "";
var country = ""
var state = "";
var df1 = "";
var data_url = "<?php echo $ctable ?>_get_ajax_new.php";

function searchByName() {
	searchName = $("#searchName").val();
	country = $("#country").val();
	state = $("#state").val();
	city = $("#city").val();
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	status_id = $("#status_id").val();
	c_type = $("#c_type").val();
	type = $("#type").val();
	assigned_to = $("#assigned_to").val();
	callAjax();

	return false;
}

function getByDate() {
	if ($("#FromDate").val() != '' && $("#ToDate").val() != '') {
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
	} else {
		alert("Please Select Date");
	}
}

function clearSearchByName() {
	searchName = "";
	c_type = "";
	type = "";
	assigned_to = "";
	ToDate = "";
	FromDate = "";
	country = "";
	state = "";
	city = "";
	status_id = "";
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#searchName").val("");
	$("#type").select2("val", "");
	$("#assigned_to").select2("val", "");
	$("#c_type").select2("val", "");
	$("#country").select2("val", "");
	$("#state").select2("val", "");
	$("#city").select2("val", "");
	$("#status_id").select2("val", "");
	callAjax();
}

$("#searchName").keyup(function (event) {
	if (event.keyCode == 13) {
		$("#searchByName").click();
	}
});

function callAjax(sales_id, date) {
	var df1=$("#material_request_filter_input").val();
	$.ajax({
		url: data_url,
		data: {
			numRecords: "100",
			searchName: searchName,
			type: type,
			assigned_to: assigned_to,
			status_id: status_id,
			c_type: c_type,
			country: country,
			state: state,
			city: city,
			df: df1,
		},
		beforeSend: function () {
			$("#results").html("<div class='row text-center'><div class='col-sm-12'><h2><i class='fa fa-refresh fa-spin'></i>&nbsp;Loading..</h2></div></div>");
		},
		success: function (result) {
			$("#results").html(result);
			if (country != "" && state != "") {
				filter_country(country, state, city);
			}
		}
	});
}

function filter_country(country_id, state = "", city = "") {
	$.ajax({
		type: "POST",
		url: "find_city.php",
		data: 'country_id=' + country_id + '&state=' + state,
		beforeSend: function () {
			// $("#loading-modal").modal('show');
			$('.preloader').fadeIn('slow');
		},
		success: function (data) {
			$("#state").select2("destroy");
			$("#state").html(data);
			$("#state").select2();
			// $("#loading-modal").modal('hide');
			$('.preloader').fadeOut('slow');
			if (state != "" ) {
				filter_state(state, city);
			}
		}
	});
}

function filter_state(state_id, city = "") {
	$.ajax({
		type: "POST",
		url: "find_city.php",
		data: 'state_id=' + state_id + "&city=" + city,
		beforeSend: function () {
		},
		success: function (data) {
			$("#city").select2("destroy");
			$("#city").html(data);
			$("#city").select2();
		}
	});
}

function loadDataTable() {

}

function displayRecords(numRecords) {
	var searchName = $("#searchName").val();
	searchName = encodeURIComponent(searchName.trim());
	state = encodeURIComponent(state.trim());
	city = encodeURIComponent(city.trim());
	$("#results").html("");
	$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status_id=" + status_id + "&c_type=" + c_type + "&country=" + country + "&state=" + state + "&city=" + city + "&df=" + df1, function () {
		loadDataTable();
	});

	$("#results").on("click", ".paging_simple_numbers a", function (e) {
		e.preventDefault();
		var numRecords = $("#numRecords").val();
		var c_type = $("#c_type").val();
		$(".loading-div").show();
		var page = $(this).attr("data-page");
		$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status_id=" + status_id + "&c_type=" + c_type + "&country=" + country + "&state=" + state + "&city=" + city + "&df=" + df1, {
			"page": page
		}, function () { 
			$(".loading-div").hide();
			loadDataTable();
		});

	});
}

function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function () {
	callAjax();
});

function del_conf(id) {
	var r = confirm("Are you sure you want to delete?");
	if (r) {
		window.location.href = '<?php echo $ctable; ?>_crud.php?mode=delete&id=' + id;
	}
}

function editStatus(id) {
	$("#inquiry_status" + id).removeAttr("disabled");
	$("#editStatus_" + id).hide(100);
	$("#editStatus2_" + id).show(400);
}

function cancelEditStatus(id) {
	$("#editStatus2_" + id).hide(100);
	$("#editStatus_" + id).show(400);
	$("#inquiry_status" + id).attr("disabled", "disabled");
}

function saveEditStatus(id) {
	var newinquiry_status = $("#inquiry_status" + id).val();

	$.ajax({
		type: "POST",
		url: "ajax_update_status_request.php",
		data: "id=" + id + "&status=" + newinquiry_status + '&table=customer_inquiry',
		cache: false,
		beforeSend: function () {

		},
		success: function (html) {

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

function genInquiryPrint() {
	var searchName = $("#searchName").val();
	searchName = encodeURIComponent(searchName.trim());
	var c_type = $("#c_type").val();
	var country = $("#country").val();
	var state = $("#state").val();
	var city = $("#city").val();
	var c_type = $("#c_type").val();
	var type = $("#type").val();
	var assigned_to = $("#assigned_to").val();

	var myWindow = window.open('print_customer_inquiry_ajax.php?searchName=' + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&c_type=" + c_type + "&country=" + country + "&state=" + state + "&city=" + city, '', 'width=700,height=800');
	myWindow.print();
}


function genExcelReport() {
	var searchName = $("#searchName").val();
	searchName = encodeURIComponent(searchName.trim());
	var ToDate = $("#ToDate").val();
	var FromDate = $("#FromDate").val();
	var c_type = $("#c_type").val();
	var country = $("#country").val();
	var state = $("#state").val();
	var city = $("#city").val();
	var c_type = $("#c_type").val();
	var status_id = $("#status_id").val();
	var type = $("#type").val();
	var assigned_to = $("#assigned_to").val();

	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1 = $("#material_request_filter_input").val();
	df1 = encodeURI(df1)

	$.ajax({
		method: "POST",
		url: "customer_inquiry_report_excel.php",
		data: {
			searchName: searchName,
			country: country,
			state: state,
			city: city,
			c_type: c_type,
			type: type,
			assigned_to: assigned_to,
		},
		dataType: 'json',
		beforeSend: function () {
			// $("#loading-modal").modal('show');
			$('.preloader').fadeIn('slow');
		},
		success: function (result) {
			// $("#loading-modal").modal('hide');
			$('.preloader').fadeOut('slow');
			window.location.href = "<?=SITEURL?>" + result.file_path;
		},
	});
}
$("#upload_sheet").on("click", function () {
	if ($("#excel_sheet").val() != "") {
		if (confirm("Don't press refresh or back button while uploading. This can't be undone. Are you sure to continue this?")) {
			var data = new FormData();
			data.append('mode', "upload_discount");
			$.each($('#excel_sheet')[0].files, function (i, file) {
				data.append('discount_sheet', file);
			});
			var lst_text = $(this).html();
			$.ajax({
				type: "POST",
				url: "customer_inquiry_upload_excel_function.php",
				data: data,
				cache: false,
				processData: false,
				contentType: false,
				beforeSend: function () {
					$(this).attr("disabled", "disabled");
					$(this).attr("disabled", "disabled");
					$(this).html("Uploading");
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function (data) {
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');

					$(this).removeAttr("disabled");
					$(this).html(lst_text);
					var json_obj = $.parseJSON(data);
					if (json_obj.ack == 1) {
						toastr.success("" + json_obj.ack_msg);
						$("#excel_sheet").val("");
						$("#remove_file").click();

						$("#result_upload").find("h3.inserted_log").html("Updated:" + json_obj.log.updated);
					} else {
						toastr.error("" + json_obj.ack_msg);
					}

				},
				error: function () {
					$("#loading-modal").modal('hide');
					$(this).removeAttr("disabled");
					$(this).html(lst_text);
					toastr.error('Connection Error Try Again Later');
					$("#uploadDiscount").modal("hide");
				},
				xhr: function () {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							console.log(percentComplete);
							$(this).html("Uploading " + percentComplete + "%");
							$('div.progressbar').css({
								width: percentComplete * 100 + '%'
							});
							if (percentComplete === 1) {
								$(this).html(lst_text)
							}
						}
					}, false);
					xhr.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							console.log(percentComplete);
							$(this).html("Uploading " + percentComplete + "%");
							$('div.progressbar').css({
								width: percentComplete * 100 + '%'
							});
							if (percentComplete === 1) {
								$(this).html(lst_text)
							}
						}
					}, false);
					return xhr;
				}
			});
		}
	} else {
		toastr.error("Select Excel File!!");
	}

})


$(".filterBtn").on("click", function () {
	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1 = $("#material_request_filter_input").val();
	df1 = encodeURI(df1)

	searchName = $("#searchName").val();
	country = $("#country").val();
	state = $("#state").val();
	city = $("#city").val();
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	status_id = $("#status_id").val();
	c_type = $("#c_type").val();
	type = $("#type").val();
	assigned_to = $("#assigned_to").val();

	callAjax();
})


function EditButtonClick(id)
{
	window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id='+id;
}
function DeleteButtonClick(id)
{
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
function ViewFollowUp(id,sales_executive_id)
{
	window.open("followup.php?mode=leads_followup&inquiry_id="+id+"&sales_id="+sales_executive_id,"_blank")
	// window.location.href = "followup.php?mode=leads_followup&inquiry_id="+id+"&sales_id="+sales_executive_id;
}

function QuotationButtonClick(id)
{
	$.ajax({
        type: "POST",
        url: "customer_ajax_function.php",
        data:'inquiry_id='+id+'&m=check_customer',
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
        },
       success: function(data)
       {
		   console.log(data);
		  data=$.parseJSON(data);
          if(data['ack']==1)
          {
			window.open('quotation_crud.php?mode=add&inquiry_id='+id,"_blank")
          }
          else
          {
          	alert(data['ack_msg']);
          	var r=confirm(data['ask']);
          	if(r)
          	{
          		createCustomer(id);
          	}
          }
          // alert(data['ack_msg']);
       }
    });
}


function createCustomer(inq_id)
{
	$.ajax({
        type: "POST",
        url: "customer_ajax_function.php",
        data:'inquiry_id='+inq_id+'&m=create_customer_inquiry',
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
            $('.preloader').fadeIn('slow');
        },
       	success: function(data)
       	{
       		// $("#loading-modal").modal('hide');  
       		$('.preloader').fadeOut('slow');
          	data=$.parseJSON(data);
          	if(data['ack']==1)
          	{
				window.open('quotation_crud.php?mode=add&inquiry_id='+inq_id,"_blank");
          	}
          	else
          	{
          		toastr.error(data['ack_msg']);          	
          	}
       	}
    });
}

</script>

</body>
</html>