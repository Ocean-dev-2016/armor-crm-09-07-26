<?php
$page_id=581;$page_slug='manage_complain';
$ctable 	= "complain";
$ctable1 	= "Service";
$page 		= $ctable."_manage";
//$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"manage_complain.php","title"=>"Manage ".$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));

include("connect.php");
include('../include/class.complain.php');
$objComplain=new Complain();
$complain_type			= "";
$complain_cat_id		= "";
$complain_subcat_id		= "";
$executive_type			= "";
$customer_id			= "";
$app_address			= "";
$contact_person			= "";
$state			        = "";
$city			        = "";
$zone			        = "";
$remark			        = "";
$complain_created_by	= "";
$complain_assign_to		= "";
$complain_date			= "";


if(isset($_REQUEST['submit'])){
	
	$detail['complain_id'] 	                   = $db->clean($_REQUEST['complain_id']);
	$detail['service_no'] 	                   = $db->clean($_REQUEST['service_no']);
	$detail['complain_date'] 	               = $db->clean($_REQUEST['complain_date']);
	$detail['customer_name'] 	               = $db->clean($_REQUEST['customer_name']);
	$detail['address'] 	                       = $db->clean($_REQUEST['address']);
	$detail['problem_report_date']             = $db->clean($_REQUEST['problem_report_date']);
	$detail['contact_person_name']             = $db->clean($_REQUEST['contact_person_name']);
	$detail['contact_no']                      = $db->clean($_REQUEST['contact_no']);
	$detail['designation']                     = $db->clean($_REQUEST['designation']);
	$detail['problem_report_by_client']        = $db->clean($_REQUEST['problem_report_by_client']);
	$detail['problem_report_observed_on_site'] = $db->clean($_REQUEST['problem_report_observed_on_site']);
	$detail['corrective_action_taken']         = $db->clean($_REQUEST['corrective_action_taken']);
	$detail['service_start_time']              = $db->clean($_REQUEST['service_start_time']);
	$detail['service_end_time']                = $db->clean($_REQUEST['service_end_time']);
	$detail['servicemen']                      = $db->clean($_REQUEST['servicemen']);
	$detail['remark']                          = $db->clean($_REQUEST['remark']);

	/*item array*/
	$product_id = $_REQUEST['product_id'];
	$make = $_REQUEST['make'];
	$sell_date = $_REQUEST['sell_date'];
	$warranty = $_REQUEST['warranty'];
	$pro_name = $_REQUEST['pro_name'];

	$size[]=sizeof($product_id);
	$size[]=sizeof($make);
	$size[]=sizeof($sell_date);
	$size[]=sizeof($warranty);
	$size[]=sizeof($pro_name);

	$value_check=sizeof($product_id);
	
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}

	if($isValidArray && !empty($product_id))
	{
		for($i=0;$i<sizeof($product_id);$i++)
		{
			$item[]=array("product_id"=>$product_id[$i],"make"=>$make[$i],"pro_name"=>$pro_name[$i],"sell_date"=>$sell_date[$i],"warranty"=>$warranty[$i],);
		}
	}
	/*item array*/
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objComplain->AddComplainService($detail,$item);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("manage_complain.php?msg=inserted");
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
}

//get outlet details
$GetOutlet_R = $db->rp_getData("complain","*","id='".$_REQUEST['complain_id']."' AND isDelete=0","",0);
$GetOutlet_D = mysqli_fetch_assoc($GetOutlet_R);

$customer_name = $db->rp_getValue("executive","cname","id='".$GetOutlet_D['customer_id']."'");
$contact_person = $db->rp_getValue("executive","company_name","id='".$GetOutlet_D['customer_id']."'");
$contact_no = $db->rp_getValue("executive","phone","id='".$GetOutlet_D['customer_id']."'");
$complain_assign_to  = $db->rp_getValue("complain","complain_assign_to","id='".$GetOutlet_D['id']."' AND isDelete=0",0);
$Servicemen  = $db->rp_getValue("sales_executive","GROUP_CONCAT(name)","id IN(".$complain_assign_to.") AND isDelete=0",0);
//get outlet details


/*get edit form*/
$GetService_R = $db->rp_getData("complain_service","*","complain_id='".$_REQUEST['complain_id']."' AND isDelete=0","",0);
$GetService_D = mysqli_fetch_assoc($GetService_R);
/*get edit form*/


?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>


<style type="text/css">
	.mainDiv, table{
	border: 1px solid #595959;
	border-collapse: collapse;
	font-size: 13px;
	width:250mm!important;
	background-color: #FFF;
	margin:auto;
  	padding:auto;
  	/*margin-top: 20px!important;
  	margin-bottom: 20px!important;*/
}
table , td, th {
	border: 1px solid #595959;
}
td, th {
	padding: 5px;
	height: 25px;
}

/*.select2
{
	width: 300px!important;
}*/
.no-border-left{
	border-left: hidden;
}
.no-border-right{
	border-right: hidden;
}
.no-border-bottom{
	border-bottom: hidden !important;
}
.no-border-top{
	border-top: hidden !important;
}
</style>

</head>
<body class="page-md">
<?php include("header.php"); ?>



<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "manage_complain.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					 <?php $db->printErrorMessage(); ?>
					 <?php $db->printSuccessMessage(); ?>		 
				</div>
			</div>
			<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
				<input type="hidden" name="mode" value="add">
				<div class="row">
					<div class="col-md-12">
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<h4><b>Service Report</b></h4>
									<hr/>
									<div class="mainDiv">
										<table style="width: 100%!important;">
											<tbody style="">
												<tr>
													<td colspan="6" class="no-border-right" style="text-align: center;">
														<img width="170" src="<?= SITEURL.VIEW_LOGO ?>">
													</td>
													<td colspan="10">
														<strong style="font-size: 30px;"><?php echo CLIENT_BRAND_NAME ?></strong><br>
														<?= CLIENT_ADDRESS ?><br>
														<?= OFFICE_PHONE ?>
													</td>
												</tr>
												<tr>
													<td colspan="4" class="no-border-right"></td>
													<td colspan="8" class="color no-border-right" align="center" style="background-color: #E5E5E5;height: 40px;"><strong>Service Report</strong></td>
													<td colspan="4"></td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Service No.</strong></td>
													<td colspan="4" class="color" align="center">
														<input readonly="" type="text" name="service_no" class="form-control" id="service_no" value="<?= $GetOutlet_D['complain_no'] ?>">
													</td>
													<td colspan="4" class="color" align="center"><strong>Date</strong></td>
													<td colspan="4" class="color" align="center">
														<input readonly="" type="text" name="complain_date" class="form-control" id="complain_date" value="<?php if($GetOutlet_D['complain_date']=="1970-01-01"){ echo "";}else{ echo date("d-m-Y h:i A",strtotime($GetOutlet_D['complain_date']));}?>">
													</td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Customer Name</strong></td>
													<td colspan="12" class="color" align="center">
														<input readonly="" type="text" name="customer_name" class="form-control" id="customer_name" value="<?= $customer_name ?>">
													</td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Address</strong></td>
													<td colspan="12" class="color" align="center">
														<input type="text" name="address" class="form-control" id="address" value="<?= $GetOutlet_D['app_address'] ?>">
													</td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Problem Report Date</strong></td>
													<td colspan="4" class="color" align="center">
														<input type="date" name="problem_report_date" class="form-control" id="problem_report_date" value="<?= $GetService_D['problem_report_date'] ?>">
													</td>
													<td colspan="8" class="no-border-left"></td>
												</tr>
												<tr>
													<td colspan="16" class="color" style="height:40px;"><strong>Contact Person (Name - Contact No. - Designation)</strong></td>
												</tr>
												<tr>
													<td colspan="8" class="color" align="center">
														<input type="text" name="contact_person_name" class="form-control" id="contact_person_name" value="<?= $contact_person ?>" placeholder="Name">
													</td>
													<td colspan="4" class="color" align="center">
														<input type="text" name="contact_no" class="form-control" id="contact_no" value="<?= $contact_no ?>" placeholder="Contact No">
													</td>
													<td colspan="4" class="color" align="center">
														<input type="text" name="designation" class="form-control" id="designation" value="<?= $GetService_D['designation'] ?>" placeholder="Designation">
													</td>
												</tr>

												<!-- product parts -->
												<tr>
													<td colspan="16" class="color" align="center" style="height: 40px;background-color: #E5E5E5;"><strong>Product Detail</strong></td>
												</tr>
											</tbody>
										</table>

										<!-- product parts -->
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<div class="form-group">
																<label class="test">Product</label>
																<select name="product_ids" class="form-control select2 pids" id="product_ids">
																	<option>Select Product</option>
																	<?php
																	$ProductR = $db->rp_getData("product","*","isDelete=0");
																	if($ProductR)
																	{
																		while($ProductD = mysqli_fetch_assoc($ProductR))
																		{
																			?>
																				<option data-name="<?= $ProductD['name'] ?>" data-pid="<?=$ProductD['id'] ?>" value="<?=$ProductD['id'] ?>"><?= $ProductD['name'] ?></option>
																			<?php
																		}
																	} 
																	?>
																</select>
																<p class="help-block"></p>
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label class="test">Make</label>
																<select name="make" class="form-control select2" id="make">
																	<option>Select Make</option>
																	<option data-make_name="CMK" value="1">CMK</option>
																	<option data-make_name="Other" value="2">Other</option>
																</select>
																<p class="help-block"></p>
															</div>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<label class="test">Sell Date</label>
																<input type="date" name="sell_date" class="form-control" id="sell_date" value="">
																<p class="help-block"></p>
															</div>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<label class="test">Warranty</label>
																<select name="warranty" class="form-control select2" id="warranty">
																	<option>Select Warranty</option>
																	<option data-warranty_name="Yes" value="1">Yes</option>
																	<option data-warranty_name="No" value="2">No</option>
																</select>
																<p class="help-block"></p>
															</div>
														</div>
														<div class="col-md-1">
															<div class="form-group" style="padding-top: 25px;">
																<button class="btn btn-primary" type="button" id="add">ADD</button>
																<p class="help-block"></p>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<table id="datatable_1" class="table table-striped table-bordered table-hover" style="width: 98%!important">
														<thead>
															<tr>
																<th class="text-center">Product</th>
																<th class="text-center">Make</th>
																<th class="text-center">Sell Date</th>
																<th class="text-center">Warranty</th>
															</tr>
														</thead>
														<tbody>
															<?php
															$get_item = $db->rp_getData("complain_service_item","*","complain_id='".$_REQUEST['complain_id']."' AND isDelete=0","id DESC");
															while($get_itemD = mysqli_fetch_assoc($get_item))
															{
																$makearray = array("1"=>"CMK","2"=>"Other");
																$warrantyarray = array("1"=>"Yes","2"=>"No");
																?>
																	<tr>
																		<td style="text-align: center;"><?= $get_itemD['pro_name'] ?></td>
																		<td style="text-align: center;"><?= $makearray[$get_itemD['make']] ?></td>
																		<td style="text-align: center;"><?= date('d-m-Y',strtotime($get_itemD['sell_date'])) ?></td>
																		<td style="text-align: center;"><?= $warrantyarray[$get_itemD['warranty']] ?></td>
																		<td style="text-align: center;"><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></a></td>
																	</tr>
																<?php
															}
															?>
														</tbody>
													</table>
												</div>
											</div>
										<!-- product parts -->
										<table>
											<tbody>
												<tr>
													<td colspan="16" class="color" align="center" style="height: 40px;background-color: #E5E5E5;"><strong>Issue / Problem</strong></td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Problem Reported By Client</strong></td>
													<td colspan="12" class="color" align="center"><input type="text" name="problem_report_by_client" class="form-control" id="problem_report_by_client" value="<?= $GetService_D['problem_report_by_client'] ?>"></td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Problem Observed On-Site</strong></td>
													<td colspan="12" class="color" align="center"><input type="text" name="problem_report_observed_on_site" class="form-control" id="problem_report_observed_on_site" value="<?= $GetService_D['problem_report_observed_on_site'] ?>"></td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Corrective Action Taken</strong></td>
													<td colspan="12" class="color" align="center"><input type="text" name="corrective_action_taken" class="form-control" id="corrective_action_taken" value="<?= $GetService_D['corrective_action_taken'] ?>"></td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Service Strat Time</strong></td>
													<td colspan="4" class="color" align="center">
														<input type="text" name="service_start_time" class="form-control" id="service_start_time" value="<?= $GetService_D['service_start_time'] ?>">
													</td>
													<td colspan="4" class="color" align="center"><strong>Service End Time</strong></td>
													<td colspan="4" class="color" align="center">
														<input type="text" name="service_end_time" class="form-control" id="service_end_time" value="<?= $GetService_D['service_end_time'] ?>">
													</td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Servicemen</strong></td>
													<td colspan="12" class="color" align="center">
														<input type="text" name="servicemen" class="form-control" id="servicemen" value="<?= $Servicemen ?>">
													</td>
												</tr>
												<tr>
													<td colspan="4" class="color" align="center"><strong>Remarks</strong></td>
													<td colspan="12" class="color" align="center">
														<input type="text" name="remark" class="form-control" id="remark" value="<?= $GetService_D['remark'] ?>">
													</td>
												</tr>
												<tr height="80px;">
													
													<td colspan="8" class="no-border-bottom">
														<?php
														if($GetService_D['serviceman_sign']!="")
														{
															?>
															<img style="height: 80px;width: 150px;margin-left: 150px;" src="<?= SITEURL."resource/complain_service/".$GetService_D['serviceman_sign'] ?>">
															<?php
															}
														?>
													</td>

													<td colspan="8" class="no-border-bottom" style="border-left:hidden;">
														<?php 
														if($GetService_D['customer_sign']!="")
														{
															?>
															<img style="height: 80px;width: 150px;margin-left: 150px;" src="<?= SITEURL."resource/complain_service/".$GetService_D['customer_sign'] ?>">
															<?php
														}
														?>
													</td>
												</tr>
												<tr height="40px;">
													<td colspan="8" class="color no-border-right" align="center"><strong>CMK Servicemen Sign</strong></td>
													<td colspan="8" class="color" align="center"><strong>Customer Sign</strong></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" name="submit" class="btn green">Submit</button>
							<button type="button" class="btn btn-default" onClick="window.location.href='manage_complain.php'">Back</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>



<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>


<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>

<script type="text/javascript">
	$('#complain_date').datepicker({ datepicker: true, autoclose: true, format: 'yyyy-mm-dd'});
</script>

<script type="text/javascript">

	function Getcustomer(customer_type)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_customer.php",
        	data:'customer_type='+customer_type,
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#customer_id").select2("destroy");
	            $("#customer_id").html(data);
	            $("#customer_id").select2();
       		}
   	 	});
	}

	function Getsubcategory(category_id)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_subcategory.php",
        	data:'category_id='+category_id,
        	beforeSend:function(){
            },
        	success: function(data){
	            $("#complain_subcat_id").select2("destroy");
	            $("#complain_subcat_id").html(data);
	            $("#complain_subcat_id").select2();
       		}
   	 	});
	}

	function GetcustomerInfo(customer_id)
	{
		$.ajax({
        	type: "POST",
        	url: "ajax_get_customer_info.php",
        	data:'customer_id='+customer_id,
        	beforeSend:function(){
            },
        	success: function(data){
        		var data =$.parseJSON(data);
        		$("#address").html(data.address);
        		$("#contact_person").val(data.cname);
        		$("#state").val(data.state);
        		$("#city").val(data.city);
        		$("#zone").val(data.zip);
        	}
   	 	});
	}

</script>

<script type="text/javascript">
	function hasValue(elem) {
    	return $(elem).filter(function() { return $(this).val(); }).length > 0;
	}

	$("#add").click(function()
	{
		var check_product=$("#product_ids").val();
		var make=$("#make").val();
		var sell_date=$("#sell_date").val();
		var warranty=$("#warranty").val();
		
		if(check_product=="" || check_product=="Select Product")
		{
			toastr.error('Please Select product!!');
		}
		else
		{	
			var product_id=$(".pids"+check_product).data('pid');
			var p_name=$("#product_ids").find('option:selected').data('name');
			var duplicate = hasValue($("input.product_id[value='"+product_id+"']"));
			var makeflagname = $("#make").find('option:selected').data('make_name');
			var warrantyflagname = $("#warranty").find('option:selected').data('warranty_name');
			if(duplicate==0)
			{
				var new_row="<tr><td class='text-center'><input type='hidden' name='product_id[]' class='form-control' style='text-align:right;' value='"+check_product+"' id='product_id'/><input type='hidden' name='pro_name[]' value='"+p_name+"' id='pro_name'>"+p_name+"</td>" +

				"<td class='text-center'><input type='hidden' name='make[]' class='form-control positive  quantity' style='text-align:right;' value='"+make+"' id='make'/>"+makeflagname+"</td>" +

				"<td class='text-center'><input type='hidden' name='sell_date[]' class='form-control' style='text-align:right;' value='"+sell_date+"' id='sell_date'/>"+sell_date+"</td>" +

				"<td class='text-center'><input type='hidden' name='warranty[]' class='warrantyble-single-amount form-control' style='text-align:right;' value='"+warranty+"' id='warranty'/>"+warrantyflagname+"</td>" +

				"<td  class='text-center'><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></tr>" ;

				$("#datatable_1").find('tbody').append(new_row);
			}
			else
			{
				toastr.error("Product already added!!");
			}
		}
	})

	$(document).ready(function(){
		$("#datatable_1").on('click','.delete',function(){
		var r = confirm("Are you sure you want to delete?");
			if(r){
				$(this).closest('tr').remove();
				recalculateFinalValues();
			}
		});
	});
</script>
</body>
</html>