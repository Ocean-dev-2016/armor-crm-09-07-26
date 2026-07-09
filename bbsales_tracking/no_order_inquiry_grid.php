<?php
// print_r($rights);

if($_REQUEST['type']=="-1")
{
	$page_id=621;$page_slug='prospect_inquiry';
}
else if($_REQUEST['type']=="0")
{
	$page_id        = 572;
	$page_slug      = 'no_order_inquiry';
}
else
{
	$page_id=620;$page_slug='lead_page';
}
// $page_id        = 572;
$page_slug      = 'no_order_inquiry';
$ctable         = "no_order_inquiry";
$ctable1        = "Customer Inquiry";
$main_page      = $ctable;
$page           = $ctable;

if($_REQUEST['type']=="-1")
{
	$page_title     = "Raw Data";
}
else if($_REQUEST['type']=="0")
{
	$page_title     = "Inquiry";
}
else
{
	$page_title     = "Lead";
}
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
$SEID=$db->rp_getvalue("dealer_distributor_network","sales_executive_id","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' ",0);
	/*if($SEID==0)
	{
		$SEID=1;
	}*/
//print_r($_SESSION[SITE_SESS.'REFERANCE_ID']);exit;
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
	        #jqxgrid .jqx-button
{
    background: transparent;
    border:none;
    background-color: transparent;
    color: red;
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
	         		<?php $db->printErrorMessage(); ?>
	               <?php $db->printSuccessMessage(); ?>
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
										<div class="row">
											<div class="col-md-12" style="margin: 0px  0!important">
						               	<div class="row">
				                       	  	<div class="col-md-5 col-xs-5 col-sm-5">
				                       	  			<?php
				                       	  			if($_REQUEST['type']=="-1" && $rights['insert_flag']==1)
				                       	  			{
			                       	  				?>
			                       	  				<a class="btn sbold blue-ebonyclay" href='<?php echo $ctable; ?>_crud.php?mode=add&type=-1'> Add New <i class="fa fa-plus"></i></a>			
			                       	  				<?php
				                       	  			} 
				                       	  			else if($_REQUEST['type']=="0" && $rights['insert_flag']==1)
				                       	  			{
			                       	  				?>
			                       	  				<a class="btn sbold blue-ebonyclay" href='<?php echo $ctable; ?>_crud.php?mode=add&type=0'> Add New <i class="fa fa-plus"></i></a>
			                       	  				<?php
				                       	  			}
				                       	  			?>
				                       	 			<!-- <a class="btn sbold blue-ebonyclay" href='<?php echo $ctable; ?>_crud.php?mode=add'> Add New <i class="fa fa-plus"></i></a> -->										
				                          			<!-- cron button -->
			                          				<?php
			                          				if($_REQUEST['type']=="0" && $rights['insert_flag']==1) 
			                          				{
		                          					?>
			                       					<a class="btn btn-success" onclick="getindiamartinquiry();"> India Mart Inquiry</a> 
			                       					<?php 
				                       				} 
				                       				?>
					                       			<!-- cron button -->
				                          	</div>
				                          	  <div class="col-md-3 col-xs-3 col-sm-3 text-right">
					                        	<div class="form-check">
															<input type="checkbox" class="form-check-input" id="status_check" name="status_check" value="0">
															<label class="form-check-label" for="status_check">Not Interested, Non Relevant, Lost</label>
														</div>
					                        </div>
					                        <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
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
						                                 	<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"><i class="fa fa-gear"></i>
																		</button>
																		<ul role="menu" class="dropdown-menu dropdown-menu-right">
																			<li>
										<!-- 										<a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a> -->
																			<?php if ($_REQUEST['type'] == "-1"): ?>
																			<a href="import_inquiry_manage.php?mode=add" target="_blank"><i class="fa fa-upload"></i>Import</a>
																			<?php endif; ?>
																			</li>
																			<?php
																			if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																			{ 
																				// echo "kk";exit();
																				?>
																				<li>
																					<a name="print" onClick="genInquiryPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
																				</li>
																				<?php
																			}
																			if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																			{ 
																				?>
																			<li>
																				<a class="excel" name="excel" onClick="genExcelReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
																			</li>
																			<?php
																			}
																			?>
																		</ul>
																	</div>
				                                    </div>
				                                    <!-- <?php
				                                    if($_REQUEST['type']=="-1")
				                                    {
				                                    	?>
				                                    	<a style="padding: 6px 14px 10px 14px!important;" class="btn btn-primary" href="view_inquiry_pipeline.php?type=-1" target="_blank"  title="track"><span class="text-success"><i style="color: black;" class="fa fa-eye"></i></span>
																	</a>
				                                    	<?php
				                                    }
				                                    else if($_REQUEST['type']=="0")
																{
																	?>
																	<a style="padding: 6px 14px 10px 14px!important;" class="btn btn-primary" href="view_inquiry_pipeline.php?type=0" target="_blank"  title="track"><span class="text-success"><i style="color: black;" class="fa fa-eye"></i></span>
																	</a>
																	<?php
																}
																else
																{
																	?>
																	<a style="padding: 6px 14px 10px 14px!important;" class="btn btn-primary" href="view_inquiry_pipeline.php?type=1" target="_blank"  title="track"><span class="text-success"><i style="color: black;" class="fa fa-eye"></i></span>
																	</a>
																	<?php
																}
				                                    ?> -->
				                                 </form>
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
	         <div class="row">
	            <div class="col-sm-12">
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
</div>

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


<div class="modal fade" id="StatusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	    <form method="post" name="lost_form" id="lost_form">
	      <div class="modal-header">
	        <h4 class="modal-title" id="exampleModalLabel"><b>Add Status</b></h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      		<label>Select Status</label>
	        	<!-- <input type="text" name="quotation_id" id="quotation_id" value=""> -->
	        	<select class="form-control b-3" id="status" name="status">
                    <option value="" readonly>Select Status</option>
                    <!-- <option value="4">Hot</option>
                    <option value="5">Cold</option>
                    <option value="6">Warm</option> -->
                    <option value="1">In Followup</option>
                </select> 
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <input type="hidden" id="inquiry_id" value="">
	        <button type="submit" name="submit" id="submit" class="btn btn-primary">Save changes</button>
	      </div>
	    </form>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="StatusModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	    <form method="post" name="lost_inquiry" id="lost_inquiry">
	      <div class="modal-header">
	        <h4 class="modal-title" id="exampleModalLabel"><b>Cancel Reason</b></h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      		<label>Select Reason</label>
	      		<input type="hidden" id="inq_reason_id" value="">
	      		<select class="form-control b-3" id="reason_id" name="reason_id">
                  <option value="" readonly>Select Reason</option>
                  <?php
                  $ReasonR = $db->rp_getData("followup_reason","*","isDelete=0");
                  if($ReasonR)
                  {
                  	while($ReasonD = mysqli_fetch_assoc($ReasonR))
                  	{
                  		?>
                  			<option value="<?=$ReasonD['id']?>"><?=$ReasonD['name']?></option>
                  		<?php
                  	}
                  }
                  ?>
               </select> 
	      </div>
	      <div class="modal-body">
	      			<div class="form-group">
	      				<label>Remark</label>
		      			<textarea class="form-control" type="text" id="remark" name="remark" value=""></textarea>
	      			</div>
	      		</div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <input type="hidden" id="inquiry_id" value="">
	        <button type="submit" name="submit" id="submit" class="btn btn-primary">Save changes</button>
	      </div>
	    </form>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="assign-customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  	<div class="modal-dialog" role="document">
		    	<div class="modal-content">
			    	<form method="post" name="assign_customer" id="assign_customer">
				      <div class="modal-header">
				        <h4 class="modal-title" id="exampleModalLabel"><b>Assign Customer</b></h4>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				      		<?php
				      			// $no_order_inq_get_table_r = $db->rp_getData("no_order_inquiry","*","isDelete=0","",0);
				      			// $no_order_inq_get_table_d = mysqli_fetch_assoc($no_order_inq_get_table_r);

				      			// $company_id = $no_order_inq_get_table_d['type_of_company'];
				      			// $customer_type = $no_order_inq_get_table_d['executive_type'];

				      		?>
				      		<label>Select Company</label>
				      		<input type="hidden" id="inq_company_id" name="inq_company_id" value="">
				      		<select class="form-control b-3" id="company_id" name="company_id">
			                  <option value="" readonly>Select Company</option>
			                  <?php
			                  	$company_r = $db->rp_getData("company_master","*","isDelete=0","id DESC",0);
				                  if($company_r)
				                  {
				                  	while($company_d = mysqli_fetch_assoc($company_r))
				                  	{
	                  		?>
				                  			<option value="<?=$company_d['id']?>">
				                  				<?=$company_d['name']?>
				                  			</option>
	                  		<?php
				                  	}
				                  }
			                  ?>
			               </select> 
				      </div>
				      <div class="modal-body">
				      		<label>Select Customer Type</label>
				      		<input type="hidden" id="inq_company_type" name="inq_company_type" value="">
				      		<select class="form-control b-3" id="customer_type" name="customer_type" onclick="getCustomer(this.value)">
			                  <option value="" readonly>Select Customer Type</option>
			                  <?php
			                  	$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
				                  if($cust_R)
				                  {
				                  	while($C = mysqli_fetch_assoc($cust_R))
				                  	{
	                  		?>
				                  			<option value="<?=$C['id']?>">
				                  				<?=$C['name']?>
				                  			</option>
	                  		<?php
				                  	}
				                  }
			                  ?>
			               </select> 
				      </div>
				      <div class="modal-body">
				      		<label>Select Customer</label>
				      		<input type="hidden" id="inq_customer" name="inq_customer" value="">
				      		<select  class="form-control customer_id_s" name="customer_id" placeholder="Select Customer" id="customer_id"   type="text" >
									<option value="">Select Customer</option>
								</select>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				        <input type="hidden" id="inquiry_id" value="">
				        <button type="submit" name="submit" id="submit" class="btn btn-primary">Save changes</button>
				      </div>
			    	</form>
		    	</div>
		  	</div>
	</div>
<?php include("footer.php"); ?>
<script type="text/javascript">
$('#ToDate').datepicker({
	datepicker: true,
	autoclose: true
});
$('#FromDate').datepicker({
	datepicker: true,
	autoclose: true
});
$("#status_check").on('change', function() {
	callAjax("","",100);
});

var ToDate = "";
var FromDate = "";
var type = "<?= $_REQUEST['inquiry_taken_by'] ?>";
var assigned_to = "<?= $_REQUEST['inquiry_assign_by'] ?>";
var searchName = "";
var status_id = "<?= $_REQUEST['status'] ?>";
var inquiry_month="<?= $_REQUEST['inquiry_month'] ?>";
var inquiry_year="<?= $_REQUEST['inquiry_year'] ?>";
var lead_month="<?= $_REQUEST['lead_month'] ?>";
var lead_year="<?= $_REQUEST['lead_year'] ?>";
var todate="<?= date('d-m-Y',strtotime($_REQUEST['todate']));?>";

var fromdate="<?= date('d-m-Y',strtotime($_REQUEST['fromdate']));?>";

var lead_todate="<?=$_REQUEST['lead_todate']?>";
var lead_fromdate="<?=$_REQUEST['lead_fromdate']?>";

var c_type = "";
var company_type = "";
var industry_type = "";
var country = "";
var city = "";
var route = ""
var country = ""
var state = "";
var df1 = "";
// update code //
var source_id ="";
var industry_type ="";
var end_followup ="";

if (todate != "" && fromdate != "" && todate!="01-01-1970" && fromdate!="01-01-1970") {
	df1 = todate+" to "+fromdate;
	fromdate = "";
	todate = "";
	inquiry_year="";
}
//df1 = encodeURI(df1);

// update code //
var data_url = "<?php echo $ctable ?>_get_ajax_new.php";

function searchByName() {
	searchName = $("#searchName").val();
	country = $("#country").val();
	state = $("#state").val();
	city = $("#city").val();
	route = $("#route").val();
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	status_id = $("#status_id").val();
	c_type = $("#c_type").val();
	company_type = $("#company_type").val();
	type = $("#type").val();
	// update code //
	source_id= $("#source_id").val();
	// update code //
	assigned_to = $("#assigned_to").val();
	industry_type = $("#industry_type").val();
	end_followup = $("#end_followup").val();
	todate="<?= $_REQUEST['todate']?>";
    fromdate="<?= $_REQUEST['fromdate']?>";
    df1=$("#material_request_filter_input").val();

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
	company_type = "";
	type = "";
	assigned_to = "";
	ToDate = "";
	FromDate = "";
	country = "";
	source_id = "";
	state = "";
	status_id = "";
	industry_type = "";
	end_followup = "";
	city = "";
	route = "";
	status_id = "";
	df1="";
	df="";

	todate="";
	fromdate="";
	inquiry_year="";
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#searchName").val("");
	$("#searchName").val("");
	$("#material_request_filter_input").val("");

	$("#type").select2("val", "");
	$("#assigned_to").select2("val", "");
	$("#c_type").select2("val", "");
	$("#company_type").select2("val", "");
	$("#industry_type").select2("val", "");
	$("#end_followup").select2("val", "");
	$("#country").select2("val", "");
	$("#source_id").select2("val", "");
	$("#state").select2("val", "");
	$("#city").select2("val", "");
	$("#route").select2("val", "");
	$("#status_id").select2("val", "");
	callAjax();
}

$("#searchName").keyup(function (event) {
	if (event.keyCode == 13) {
		$("#searchByName").click();
	}
});

function callAjax(sales_id, date) {

	var inquiry_type = '<?= $_REQUEST['type'] ?>';
	var page = $(this).attr("data-page");
	
	// 
	/*if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,-1,11";
	}
	else
	{
		var status1="0,1,3,12";
	}*/
	if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,11";
	}
	else
	{
		//var status1="0,1,3,12";
		var status1="0,1,2,3,4,5,6,-1";
	}
	//alert(status1);
	$.ajax({
		url: data_url,
		data: {
			numRecords: "100",
			searchName: searchName,
			type: type,
			assigned_to: assigned_to,
			status_id: status_id,
			c_type: c_type,
			company_type: company_type,
			country: country,
			state: state,
			city: city,
			route: route,
			df: df1,
			// update code //
			source_id: source_id,
			// update code //
			inquiry_type: inquiry_type,
			industry_type: industry_type,
				end_followup: end_followup,
			inquiry_month:inquiry_month,
			inquiry_year:inquiry_year,
			lead_month:lead_month,
			lead_year:lead_year,
			todate:todate,
			fromdate:fromdate,
			lead_todate,
			lead_fromdate,
			status1:status1,
			page:page,


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

			if (city != "" ) {
				filter_city(city, route);
			}
		}
	});
}

function filter_city(city,route=""){
    $.ajax({
        type: "POST",
        url: "find_city.php",
        data:'main_city='+city+"&city="+route,
        beforeSend:function(){ 
        },
        success: function(data){
            $("#route").select2("destroy"); 
        		$("#route").html(data);
       		$("#route").select2(); 
        }
    });
} 

function loadDataTable() { 
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"scrollY": 400,
	   "scrollX": true,
		"order": []          
	});
}

function displayRecords(numRecords) {
	var searchName = $("#searchName").val();
	searchName = encodeURIComponent(searchName.trim());
	state = encodeURIComponent(state.trim());
	city = encodeURIComponent(city.trim());
	$("#results").html("");
	$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status_id=" + status_id + "&c_type=" + c_type + "&country=" + country + "&state=" + state + "&city=" + city + "&df=" + df1+"&status1=" + status1 + "&company_type=" + company_type, function () {
		loadDataTable();
	});

	/*if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,-1,11";
	}
	else
	{
		var status1="0,1,3,12";
	}*/ 

	if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,11";
	}
	else
	{
		var status1="0,1,2,3,4,5,6,-1";
	} 
   // alert(status1);
	$("#results").on("click", ".paging_simple_numbers a", function (e) {
		e.preventDefault();
		var numRecords = $("#numRecords").val();
		var c_type = $("#c_type").val();
		var company_type = $("#company_type").val();
		$(".loading-div").show();
		var page = $(this).attr("data-page");
		$("#results").load(data_url + "?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status_id=" + status_id + "&c_type=" + c_type + "&country=" + country + "&state=" + state + "&city=" + city + "&df=" + df1+"&status1=" + status1 + "&company_type=" + company_type, {
			"page": page
		}, function () { 
			$(".loading-div").hide();
			loadDataTable();
		});

	});
}

// function changeDisplayRowCount(numRecords) {
// 	displayRecords(numRecords, 1);
// }
function changeDisplayRowCount(numRecords) {
// alert(status1);  
	callAjax("","",numRecords);
}

$(document).ready(function () {
	//alert(status1);
	callAjax("","",100); 
});

function del_conf(id) {
	var r = confirm("Are you sure you want to delete?");
	if (r) {
		// var type = "<?=$_REQUEST['type']?>";
		var inquiry_type = '<?= $_REQUEST['type'] ?>';
		window.location.href = '<?php echo $ctable; ?>_crud.php?mode=delete&type='+inquiry_type+'&id=' + id;
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
	var route = $("#route").val();
	var company_type = $("#company_type").val();
	var industry_type = $("#industry_type").val();
	var end_followup = $("#end_followup").val();
	var type = $("#type").val();
	var assigned_to = $("#assigned_to").val();
	var inquiry_type = '<?= $_REQUEST['type'] ?>';
	var status_id = $("#status_id").val();
	// update code
	var source_id = $("#source_id").val();
	// update code
   /*if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,-1,11";
	}
	else
	{
		var status1="0,1,3,12";
	} */
	if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,11";
	}
	else
	{
		var status1="0,1,2,3,4,5,6,-1";
	} 

	var assigned_to = $("#assigned_to").val();
	df1 = $("#material_request_filter_input").val();	
	df1 = encodeURI(df1)
	// alert(inquiry_type);

	var myWindow = window.open('print_inquiry_ajax.php?searchName=' + searchName + "&type=" + type + "&assigned_to=" + assigned_to + "&c_type=" + c_type + "&country=" + country + "&state=" + state + "&city=" + city+ "&route=" + route +"&df=" + df1 +"&inquiry_type=" + inquiry_type +"&status_id=" + status_id + "&source_id=" + source_id + "&industry_type=" + industry_type  + "&end_followup=" + end_followup+ "&status1=" + status1 + "&company_type=" + company_type, '', 'width=700,height=800');
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
	var route = $("#route").val();
	var company_type = $("#company_type").val();
	var status_id = $("#status_id").val();
	var industry_type = $("#industry_type").val();
	var end_followup = $("#end_followup").val();
	var type = $("#type").val();
	var assigned_to = $("#assigned_to").val();
	var inquiry_type = '<?= $_REQUEST['type'] ?>';
	// update code
	var source_id =$("#source_id").val();
	// update code
   /*if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,-1,11";
	}
	else
	{
		var status1="0,1,3,12";
	} */
	if($("#status_check").is(':checked') == true)
	{
		var status1 = "-2,11";
	}
	else
	{
		var status1="0,1,2,3,4,5,6,-1";
	} 
	// alert(assigned_to); 

	sales_executive = $("#sales_executive").val();
	customer_id = $("#customer_id").val();
	df1 = $("#material_request_filter_input").val();
	df1 = encodeURI(df1)

	$.ajax({
		method: "POST",
		url: "inquiry_report_excel.php",
		data: {
			searchName: searchName,
			country: country,
			state: state,
			city: city,
			route: route,
			c_type: c_type,
			company_type: company_type,
			type: type,
			assigned_to: assigned_to,
			inquiry_type:inquiry_type,
			industry_type:industry_type,
			end_followup:end_followup,
			df:df1,
			status_id:status_id,
			// update_code
			source_id:source_id,
			status1:status1,
			// update code
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
	company_type = $("#company_type").val();
	type = $("#type").val();
	assigned_to = $("#assigned_to").val();
	callAjax();
})


function EditButtonClick(id)
{
	var type = '<?= $_REQUEST['type'] ?>';
	/*window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id='+id;*/
	window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&type='+type+'&id='+id;
}
function DeleteButtonClick(id)
{
	var r = confirm("Are you sure you want to delete?");
	if(r){
		var inquiry_type = '<?= $_REQUEST['type'] ?>';
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id+'&type='+inquiry_type;
	}
}

function QuotationButtonClick(id)
{
	// alert();
	$.ajax({
        type: "POST",
        url: "customer_ajax_function.php",
        data:'inquiry_id='+id+'&m=check_customer',
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
        },
      	success: function(data)
       	{	
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

function TimelineButtonClick(id)
{
	var slug = '<?php echo $_REQUEST['type']; ?>';
	if(slug=="-1")
	{
		window.location.href='customer_view.php?id='+id+'&flag=prospect';	
	}
	else if(slug=="0")
	{
		window.location.href='customer_view.php?id='+id+'&flag=inquiry';
	}
	else
	{
		window.location.href='customer_view.php?id='+id+'&flag=leads';
	}
}


function GenerateInquiry(id)
{
	$("#jqxgrid").jqxGrid('updatebounddata');
	window.open("no_order_inquiry_crud.php?m=change_to_inquiry&mode=edit&type=0&id="+id,"_blank");
	// alert("sdf");
	// $.ajax({
   //      type: "POST",
   //      url: "customer_ajax_function.php",
   //      data:'inquiry_id='+id+'&m=change_to_inquiry',
   //      beforeSend:function(){
   //          // $("#loading-modal").modal('show');  
   //          $('.preloader').fadeIn('slow');
   //      },
   //     	success: function(data)
   //     	{
   //     		// $("#loading-modal").modal('hide');  
   //     		$('.preloader').fadeOut('slow');
   //     		$("#jqxgrid").jqxGrid('updatebounddata');
   //     		// window.open("no_order_inquiry_grid.php?type=0","_blank");
   //     		// $db->rp_location(");
   //     		window.open("no_order_inquiry_crud.php?m=change_to_inquiry&mode=edit&type=0&id="+id,"_blank");
   //     	}
   //  });
}

function CancelInquiry(id)
{
	$("#StatusModal1").modal('show');
	$("#inq_reason_id").val(id);
}
function Assigncustomer(id)
{
	$("#assign-customer").modal('show');
	$("#inq_company_id").val(id);
}

function GenerateLead(id)
{

	$.ajax({
        type: "POST",
        url: "customer_ajax_function.php",
        data:'inquiry_id='+id+'&m=change_to_lead',
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
            $('.preloader').fadeIn('slow');
        },
       	success: function(data)
       	{
       		data=$.parseJSON(data);
       		if(data['ack']==1)
       		{
       			// $("#loading-modal").modal('hide');  
       			$('.preloader').fadeOut('slow');
       			$("#jqxgrid").jqxGrid('updatebounddata');
       			window.open("no_order_inquiry_grid.php?type=1","_blank");
       		}
       		else if(data['ack']==2){
       			
       			$('.preloader').fadeOut('slow');
       			$("#StatusModal").modal('show');
       			$("#inquiry_id").val(id);
       		}
       		else
       		{
       			$("#loading-modal").modal('hide'); 
       			toastr.error(data['ack_msg']);
       		}
       	}
    });
}

function CancelButtonClick(id)
{
	var r = confirm("Are you sure you want to Cancel?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=cancel&id='+id;
	}
}

function followupButtonClick(id)
{
	var r = confirm("Are you sure you want to Transfer Inquiry In Followup?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=followup&id='+id;
	}
}

function createCustomer(inq_id)
{
	$.ajax({
        type: "POST",
        url: "customer_ajax_function.php",
        data:'inquiry_id='+inq_id+'&m=create_customer',
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

function ViewFollowUp(id,sales_executive_id)
{
	// alert("hello");
	var sales_executive_id = '<?= $SEID ?>';
	window.open("followup.php?mode=inquiry_followup&inquiry_id="+id+"&sales_id="+sales_executive_id,"_blank")
	// window.location.href = "followup.php?mode=leads_followup&inquiry_id="+id+"&sales_id="+sales_executive_id;
}

function getindiamartinquiry()
{
    $('.preloader').fadeIn('slow');
	/*$.ajax({
        type: "POST",
        url: "../service/india_mart_leads.php",
        data:'',
       	success: function(data) {
       		// $('.preloader').fadeOut('slow');
        }
    });*/
	$.ajax({
        type: "POST",
        url: "../service/india_mart_leads_another.php",
        data:'',
       	success: function(data) {
       		$('.preloader').fadeOut('slow');
       		window.location.reload();
        }
    });
}

function gettradeindiainquiry()
{
	$.ajax({
        type: "POST",
        url: "../service/trade_india_leads.php",
        data:'',
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
            $('.preloader').fadeIn('slow');
        },
       	success: function(data)
       	{
       		// $("#loading-modal").modal('hide');
       		$('.preloader').fadeOut('slow');
       		window.location.reload();
        }
    });
}

</script>

<script type="text/javascript">
	$("#lost_form").on("submit", function(e) {
            e.preventDefault();
            
            var inquiry_id=$('#inquiry_id').val();
            var status = $('#status').val();

            if(status == ""){
            	toastr.error("Please Select Status...");
            }
            else{
            
                $.ajax(
                {
                    url:"update_inquiry_status_ajax.php",
                    type:"POST",
                    data:'inquiry_id='+inquiry_id+'&status='+status+'',
                    
                    beforeSend:function()
                    {
                       // $("#loading-modal").modal({backdrop: 'static', keyboard: false});
                       // $("#loading-modal").modal('show');
                    },
                    success:function(result)
                    {
                      
                        let jsonData = JSON.parse(result);  
                        if(jsonData.ack==1)
                        {                          
							$("#StatusModal").modal('hide');
							GenerateLead(inquiry_id);
                        }
                        else
                        {
                            // toastr.error("Something went wrong...");
                            // $("#fail-show").text("Something went wrong");                        
                        }
                    }
                });
            }
            
        });

			$("#lost_inquiry").on("submit", function(e) 
			{
            e.preventDefault();
            
            var inq_reason_id=$('#inq_reason_id').val();
            var reason_id = $('#reason_id').val();
             var remark = $('#remark').val();
            if(reason_id == ""){
            	toastr.error("Please Select Reason...");
            }
            else
            {
            	$.ajax(
               {
                  url:"inquiry_cancel_status_ajax.php",
                  type:"POST",
                  data:'inq_reason_id='+inq_reason_id+'&reason_id='+reason_id+'&remark='+remark,
                  beforeSend:function()
                  {
                     // $("#loading-modal").modal({backdrop: 'static', keyboard: false});
                     // $("#loading-modal").modal('show');
                  },
                  success:function(result)
                  {
                  	let jsonData = JSON.parse(result);  
                     if(jsonData.ack==1)
                     {                          
								$("#StatusModal1").modal('hide');
								location.reload();
							}
                     else
                     {
                        // toastr.error("Something went wrong...");
                         // $("#fail-show").text("Something went wrong");                        
                     }
                  }
               });
            }
            
        });

			$("#assign_customer").on("submit", function(e) 
			{
            e.preventDefault();
            
            var inq_company_id=$('#inq_company_id').val();
            var company_id = $('#company_id').val();
          	var customer_type = $('#customer_type').val();
          	var customer_id = $('#customer_id').val();
          	// alert(customer_id);
            if(company_id == ""){
            	toastr.error("Please Select Company");
            }
            if(customer_type == ""){
            	toastr.error("Please Select Customer Type");
            }
            if(customer_id == ""){
            	toastr.error("Please Select Customer");
            }
            else
            {
            	$.ajax(
               {
                  url:"inquiry_customer_assign_ajax.php",
                  type:"POST",
                  data:'inq_company_id='+inq_company_id+'&company_id='+company_id+'&customer_type='+customer_type+'&customer_id='+customer_id,
                  beforeSend:function()
                  {
                     // $("#loading-modal").modal({backdrop: 'static', keyboard: false});
                     // $("#loading-modal").modal('show');
                  },
                  success:function(result)
                  {
                  	let jsonData = JSON.parse(result);  
                     if(jsonData.ack==1)
                     {                          
								$("#assign-customer").modal('hide');
								location.reload();
							}
                     else
                     {
                        // toastr.error("Something went wrong...");
                         // $("#fail-show").text("Something went wrong");                        
                     }
                  }
               });
            }
            
        });

function getCustomer(ctype) 
{
	$('#customer_id').select2("val", "");
	var cus_id = '<?= $customer_id ?>';
	var mode = 'assign_customer';

	$.ajax({
	type: "post",
	url: "ajax_get_customer.php",
	data: "customer_type=" + ctype+"&mode=" + mode,
	beforeSend: function() {
		// $(".transCover").fadeIn(800);
		// $("#loading-modal").modal('show');
		$('.preloader').fadeIn('slow');
	},
	success: function(result) 
	{
	setTimeout(function() {
		$('#customer_id').html(result);
		$('#customer_id').select2('destroy');
		$("#customer_id").val(cus_id);
		$('#customer_id').select2();
		$("#customer_id").trigger('change');
		// $("#loading-modal").modal('hide');
		$('.preloader').fadeOut('slow');
		});
	}

	})
}
</script>

</body>
</html>