<?php 
$page_id = 555;$page_slug = 'page_executive';
$ctable 	= "executive";
$ctable1 	= "Executive";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"executive_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");
if(isset($_REQUEST['id']) && $_REQUEST['id']>0)
{
	$ledgerData = $db->rp_getData("executive","*","isDelete=0 AND id='".$_REQUEST['id']."'","",0);
	$result = mysqli_fetch_assoc($ledgerData);
	extract($result); 
}

if(isset($_REQUEST['flag']) && $_REQUEST['flag']!="")
{
	$customer_id = $db->rp_getValue("no_order_inquiry","dealer_id","isDelete=0 AND id='".$_REQUEST['id']."'",0);	
	$ledgerData = $db->rp_getData("executive","*","isDelete=0 AND id='".$customer_id."'","",0);
	$result = mysqli_fetch_assoc($ledgerData);
	extract($result); 
}

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
<style type="text/css">
	.member-list{float:left;list-style:none;margin-top:-3px;padding:0;width:190px;z-index: 999}
	.member-list li{padding: 10px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;}
	.member-list li:hover{background:#ece3d2;cursor: pointer;}
</style>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "executive_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
	                               <?= $db->rp_getValue("executive","company_name","id='".$_REQUEST['id']."' AND isDelete=0"); ?>
	                            </div>
	                        </div>
	                    </div>
                    	<!-- END Portlet PORTLET-->
                	</div>
					<div class="portlet light">
						<div class="portlet-body">
							<ul class="nav nav-tabs">
								<?php
								if($_REQUEST['flag']=="prospect")
								{
									?>
									<li class="active"><a data-toggle="tab" href="#menu">Customer Details</a></li>
									<li><a data-toggle="tab" class="GetProspect" href="#menu6" data-id='<?= $_REQUEST['id'] ?>'>Raw Data</a></li>
									<li><a data-toggle="tab" class="Getfollowup" href="#menu5" data-id='<?= $_REQUEST['id'] ?>'>Followup</a></li>
							    	<li><a data-toggle="tab" class="Gettimeline" href="#menu7" data-id='<?= $_REQUEST['id'] ?>'>Timeline</a></li>
								    <?php
								}
								else if($_REQUEST['flag']=="inquiry")
								{
									?>
										<li class="active"><a data-toggle="tab" href="#menu">Customer Details</a></li>
										<li><a data-toggle="tab" class="GetProspect" href="#menu6" data-id='<?= $_REQUEST['id'] ?>'>Raw Data</a></li>
										<li><a data-toggle="tab" class="Getinquiry" href="#menu2" data-id='<?= $_REQUEST['id'] ?>'>Inquiry</a></li>
										<li><a data-toggle="tab" class="Getfollowup" href="#menu5" data-id='<?= $_REQUEST['id'] ?>'>Followup</a></li>
								    	<li><a data-toggle="tab" class="Gettimeline" href="#menu7" data-id='<?= $_REQUEST['id'] ?>'>Timeline</a></li>
								    <?php
								}
								else
								{
									?>
								    <li class="active"><a data-toggle="tab" href="#menu">Customer Details</a></li>
								    <li><a data-toggle="tab" class="GetProspect" href="#menu6" data-id='<?= $_REQUEST['id'] ?>'>Raw Data</a></li>
								    <li><a data-toggle="tab" class="Getinquiry" href="#menu2" data-id='<?= $_REQUEST['id'] ?>'>Inquiry</a></li>
								    <li><a data-toggle="tab" class="Getlead" href="#menu1" data-id='<?= $_REQUEST['id'] ?>'>Leads</a></li>
								    <li><a data-toggle="tab" class="Getquotation" href="#menu3" data-id='<?= $id ?>'>Quotation</a></li>
								    <li><a data-toggle="tab" class="Getorder" href="#menu4" data-id='<?= $_REQUEST['id'] ?>'>Orders</a></li>
								    <li><a data-toggle="tab" class="Getinvoice" href="#menu8" data-id='<?= $_REQUEST['id'] ?>'>Invoice</a></li>
								    <li><a data-toggle="tab" class="Getfollowup" href="#menu5" data-id='<?= $_REQUEST['id'] ?>'>Followup</a></li>
								    <li><a data-toggle="tab" class="Gettimeline" href="#menu7" data-id='<?= $_REQUEST['id'] ?>'>Timeline</a></li>
							    	<?php
								}
								?>
							</ul>

							<?php
							if($_REQUEST['flag']=="prospect")
							{
								$DataR = $db->rp_getData("no_order_inquiry","*","id='".$_REQUEST['id']."' AND isDelete=0","",0);
								$result1 = mysqli_fetch_assoc($DataR);
								extract($result1);
								?>
								<div class="tab-content" style="padding: 5px;">
									<div id="menu" class="tab-pane fade in active">
							       		<div class="portlet-title row">  
										  	<div class="caption col-md-8">
										        <i class="fa fa-file font-red"></i>
										        <span class="caption-subject font-red sbold uppercase">CUSTOMER Detail</span>
										    </div>
										</div> 
									    <div class="portlet-body" style="margin-top: 20px;font-size: 12px!important"> 
							            	<div id="company_info"> 
							            		<div class="row">
							            			<div class="col-md-5">
							            				<div class="form-group row">
							            					<label for="" class="col-sm-5"><b>Company Name</b></label>
														    <label for="" class="col-sm-1"><b>:</b></label>
														   	<div class="col-sm-6">
															   	<div class="form-group">
															     	<?php echo $company_name; ?> 
															     </div>
														    </div>
														</div>
														<div class="form-group row">
															<label for="" class="col-sm-5"><b>Customer Name</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
														    <div class="col-sm-6">
														    	<div class="form-group">
														      		<?php echo $person_name; ?> 
														      	</div>
														    </div>
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>Mobile No</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $mobile_number; ?> 
																</div>
															</div> 
														</div>

														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $address; ?> 
														      	</div>
													      	</div>
														</div>
														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Email Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $email_address; ?> 
														      	</div>
													      	</div>
														</div>
														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Billing Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $billing_address; ?> 
														      	</div>
													      	</div>
														</div>
														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Shipping  Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $shipping_address; ?> 
														      	</div>
													      	</div>
														</div>
													</div>
							            			<div class="col-md-2"><div class="vl"  style="border-left: 2px solid #000;height: 280px;"></div> </div>
							            			<div class="col-md-5">
							            				<div class="form-group row">
															<label class="col-sm-5"><b>Country</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $country; ?> 
																</div>
															</div> 
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>State</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $state; ?> 
																</div>
															</div> 
														</div> 
														<div class="form-group row">
															<label class="col-sm-5"><b>City</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $city; ?> 
																</div>
															</div> 
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>GST No</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $gst_no; ?> 
																</div>
															</div> 
														</div>
														<?php
														if($image_path)
														{
															?>
															<!-- <div class="form-group row">
																<label class="col-sm-5"><b>Attachment</b></label>
																<label for="" class="col-sm-1"><b>:</b></label>
																<div class="col-sm-6">
																	<div class="form-group">
																		<a target="_blank" href="<?php echo INQUIRY_IMAGE .$image_path ?>">
														      		<i class="fa fa-eye" aria-hidden="true"></i></a> 
																	</div>
																</div> 
															</div> -->
															<?php
														}
														?>
							            			</div>
							            		</div>  
											</div>
										</div>
								    </div> 
									<div id="menu6" class="tab-pane fade in active">
					     				<div class="prospect"></div>  
									</div>
									<div id="menu5" class="tab-pane fade in">
				     					<div class="followup"></div>  
									</div>
									<div id="menu7" class="tab-pane fade in">
					     				<div class="timeline_view"></div>  
									</div>
								</div>
								<?php
							}
							else if($_REQUEST['flag']=="inquiry")
							{
								$DataR1 = $db->rp_getData("no_order_inquiry","*","id='".$_REQUEST['id']."' AND isDelete=0","",0);
								$result2 = mysqli_fetch_assoc($DataR1);
								extract($result2);
								?>
								<div class="tab-content" style="padding: 5px;">
									<div id="menu" class="tab-pane fade in active">
							       		<div class="portlet-title row">  
										  	<div class="caption col-md-8">
										        <i class="fa fa-file font-red"></i>
										        <span class="caption-subject font-red sbold uppercase">CUSTOMER Detail</span>
										    </div>
										</div> 
									    <div class="portlet-body" style="margin-top: 20px;font-size: 12px!important"> 
							            	<div id="company_info"> 
							            		<div class="row">
							            			<div class="col-md-5">
							            				<div class="form-group row">
							            					<label for="" class="col-sm-5"><b>Company Name</b></label>
														    <label for="" class="col-sm-1"><b>:</b></label>
														   	<div class="col-sm-6">
															   	<div class="form-group">
															     	<?php echo $company_name; ?> 
															     </div>
														    </div>
														</div>
														<div class="form-group row">
															<label for="" class="col-sm-5"><b>Customer Name</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
														    <div class="col-sm-6">
														    	<div class="form-group">
														      		<?php echo $person_name; ?> 
														      	</div>
														    </div>
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>Mobile No</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $mobile_number; ?> 
																</div>
															</div> 
														</div>

														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $address; ?> 
														      	</div>
													      	</div>
														</div>
														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Email Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $email_address; ?> 
														      	</div>
													      	</div>
														</div>
														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Billing Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $billing_address; ?> 
														      	</div>
													      	</div>
														</div>
														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Shipping  Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $shipping_address; ?> 
														      	</div>
													      	</div>
														</div>
													</div>
							            			<div class="col-md-2"><div class="vl"  style="border-left: 2px solid #000;height: 280px;"></div> </div>
							            			<div class="col-md-5">
							            				<div class="form-group row">
															<label class="col-sm-5"><b>Country</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $country; ?> 
																</div>
															</div> 
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>State</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $state; ?> 
																</div>
															</div> 
														</div> 
														<div class="form-group row">
															<label class="col-sm-5"><b>City</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $city; ?> 
																</div>
															</div> 
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>GST No</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $gst_no; ?> 
																</div>
															</div> 
														</div>
														<?php
														if($image_path)
														{
															?>
															<!-- <div class="form-group row">
																<label class="col-sm-5"><b>Attachment</b></label>
																<label for="" class="col-sm-1"><b>:</b></label>
																<div class="col-sm-6">
																	<div class="form-group">
																		<a target="_blank" href="<?php echo INQUIRY_IMAGE .$image_path ?>">
														      		<i class="fa fa-eye" aria-hidden="true"></i></a> 
																	</div>
																</div> 
															</div> -->
															<?php
														}
														?>
							            			</div>
							            		</div>  
											</div>
										</div>
								    </div> 
									<div id="menu6" class="tab-pane fade in active">
					     				<div class="prospect"></div>  
									</div>
									<div id="menu2" class="tab-pane fade in">
				     					<div class="inquiry"></div>  
									</div>
									<div id="menu5" class="tab-pane fade in">
				     					<div class="followup"></div>  
									</div>
									<div id="menu7" class="tab-pane fade in">
					     				<div class="timeline_view"></div>  
									</div>
								</div>
								<?php
							}
							else
							{
								?>
								<div class="tab-content" style="padding: 5px;">
								    <div id="menu" class="tab-pane fade in active">
							       		<div class="portlet-title row">  
										  	<div class="caption col-md-8">
										        <i class="fa fa-file font-red"></i>
										        <span class="caption-subject font-red sbold uppercase">CUSTOMER Detail</span>
										    </div>
										</div> 
									    <div class="portlet-body" style="margin-top: 20px;font-size: 12px!important"> 
							            	<div id="company_info"> 
							            		<div class="row">
							            			<div class="col-md-5">
							            				<div class="form-group row">
							            					<label for="" class="col-sm-5"><b>Company Name</b></label>
														    <label for="" class="col-sm-1"><b>:</b></label>
														   	<div class="col-sm-6">
															   	<div class="form-group">
															     	<?php echo $company_name; ?> 
															     </div>
														    </div>
														</div>
														<div class="form-group row">
															<label for="" class="col-sm-5"><b>Customer Name</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
														    <div class="col-sm-6">
														    	<div class="form-group">
														      		<?php echo $cname; ?> 
														      	</div>
														    </div>
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>Mobile No</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $phone; ?> 
																</div>
															</div> 
														</div>

														<div class="form-group row"> 
														    <label for="" class="col-sm-5"><b>Address</b></label>
													      	<label for="" class="col-sm-1"><b>:</b></label>
													      	<div class="col-sm-6">
														      	<div class="form-group">
														      		<?php echo $address; ?> 
														      	</div>
													      	</div>
														</div>
													</div>
							            			<div class="col-md-2"><div class="vl"  style="border-left: 2px solid #000;height: 280px;"></div> </div>
							            			<div class="col-md-5">
							            				<div class="form-group row">
															<label class="col-sm-5"><b>Country</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $country; ?> 
																</div>
															</div> 
														</div>
														<div class="form-group row">
															<label class="col-sm-5"><b>State</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $state; ?> 
																</div>
															</div> 
														</div> 
														<div class="form-group row">
															<label class="col-sm-5"><b>City</b></label>
															<label for="" class="col-sm-1"><b>:</b></label>
															<div class="col-sm-6">
																<div class="form-group">
																	<?php echo $city; ?> 
																</div>
															</div> 
														</div>
														<?php
														if($image_path)
														{
															?>
															<div class="form-group row">
																<label class="col-sm-5"><b>Attachment</b></label>
																<label for="" class="col-sm-1"><b>:</b></label>
																<div class="col-sm-6">
																	<div class="form-group">
																		<a target="_blank" href="<?php echo SUPER_STOCKIST_A .$image_path ?>">
														      		<i class="fa fa-eye" aria-hidden="true"></i></a> 
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
								    <div id="menu6" class="tab-pane fade in">
					     				<div class="prospect"></div>  
									</div>
								    <div id="menu1" class="tab-pane fade in">
					     				<div class="leads"></div>  
									</div>
									<div id="menu2" class="tab-pane fade in">
					     				<div class="inquiry"></div>  
									</div>
									<div id="menu3" class="tab-pane fade in">
					     				<div class="quotation"></div>  
									</div>
									<div id="menu4" class="tab-pane fade in">
					     				<div class="order"></div>  
									</div>
									<div id="menu8" class="tab-pane fade in">
					     				<div class="invoice"></div>  
									</div>
									<div id="menu5" class="tab-pane fade in">
					     				<div class="followup"></div>  
									</div>
									<div id="menu7" class="tab-pane fade in">
					     				<div class="timeline_view"></div>  
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


<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">

$( document ).ready(function() {
    var flag = '<?= $_REQUEST['flag'] ?>';
    if(flag=="prospect")
    {
    	$(".GetProspect").trigger('click');
    }
    else if(flag=="inquiry")
    {
    	$(".Getinquiry").trigger('click');
    }

});

$(document).on('click', '.Getlead', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_leads',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".leads").html(result);
		}
	});
});

$(document).on('click', '.GetProspect', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_prospect',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".prospect").html(result);
		}
	});
});

$(document).on('click', '.Getinquiry', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_inquiry',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".inquiry").html(result);
		}
	});
});


$(document).on('click', '.Getquotation', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_quotation',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".quotation").html(result);
		}
	});
});

$(document).on('click', '.Getorder', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_order',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".order").html(result);
		}
	});
});

$(document).on('click', '.Getinvoice', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_invoice',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".invoice").html(result);
		}
	});
});

$(document).on('click', '.Getfollowup', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_followup',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".followup").html(result);
		}
	});
});

$(document).on('click', '.Gettimeline', function(event) {
	var customer_id = $(this).data("id");
	$.ajax({
		type: "POST",
		url: "customer_data_ajax_function.php",
		data: {
			customer_id:customer_id,
			mode:'get_timeline',
		},
		cache: false,
		beforeSend: function() {
		},
		success: function(result)
		{
			$(".timeline_view").html(result);
		}
	});
})


</script>
</body>
</html>