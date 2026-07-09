<?php
$page_id    = 612;
$page_slug  = 'packing_slip';
$ctable     = "packing_slip";

$ctable1 	= "Packing Slip"; 
$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"packing_slip_manage.php","title"=>$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));


include("connect.php");

$packing_slip_no       = $db->getLastInsertId($ctable);
$packing_slip_no       = PACKING_SLIP_NO . str_pad($packing_slip_no, 2, '0', STR_PAD_LEFT);
$packing_slip_date     = date('d-m-Y');

$uid              = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$utype            = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];


$packing_charge   = 0;
$transport_charge = 0;

if (isset($_REQUEST['submit'])) {
	/*echo "<pre>";
	print_r($_REQUEST);
	echo "</pre>";exit;*/
	if($_REQUEST['mode']=="add" && ($_REQUEST['customer_selectd_id']!="" || $_REQUEST['customer_id']!="") && (isset($_REQUEST['customer_selectd_id']) || isset($_REQUEST['customer_id']))) 
	{
		$packingSlipArray = array();
		$packingSlipArray['customer_type'] = $_REQUEST['customer_type'];
		
		if($_REQUEST['mode']=="add" && $_REQUEST['dispatch_id']!="")
		{
			$packingSlipArray['customer_id'] = $_REQUEST['customer_selectd_id'];
			$packingSlipArray['customer_id'] = $db->rp_getValue("dispatch_detail","customer_id","id='".$_REQUEST['dispatch_id']['0']."'",0);
			$packingSlipArray['customer_type'] = $db->rp_getValue("dispatch_detail","order_type","id='".$_REQUEST['dispatch_id']['0']."'",0);
		}
		else
		{
			$packingSlipArray['customer_id'] = $_REQUEST['customer_id'];
			//$packingSlipArray['customer_id'] = $db->rp_getValue("dispatch_detail","customer_id","id='".$_REQUEST['dispatch_id']['0']."'",0);
		}

		//echo $
		//$packingSlipArray['remark'] = $_REQUEST['remark'];
		$packingSlipArray['dispatch_id'] = implode(",", $_REQUEST['dispatch_id']);
		$packingSlipArray['packing_slip_no'] = $_REQUEST['packing_slip_no'];

		//print_r($packingSlipArray); exit;
		$packingSlipArray['packing_slip_date'] = date("Y-m-d",strtotime($_REQUEST['packing_slip_date']));


		/*log entry*/
		$module_name = "Packing Slip";
		$flag = "Web";
		$log_description = $module_name." ".$packing_slip_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/

		$PackingSlipId = $db->rp_insert("packing_slip",array_values($packingSlipArray),array_keys($packingSlipArray),0,$log_description,$flag,$module_name,"",$packingSlipArray['customer_id']);

		if($PackingSlipId)
		{
			foreach ($_REQUEST['main_carton_type_count'] as $main_carton_type_count_key => $main_carton_type_count_value)
			{
				$packingSlipItemArray = array();
				$packingSlipItemArray['packing_slip_id'] = $PackingSlipId;
				$packingSlipItemArray['main_carton_type'] = $_REQUEST['main_carton_type'][$main_carton_type_count_key];
				$packingSlipItemArray['main_carton_type_name'] = $_REQUEST['main_carton_type_name'][$main_carton_type_count_key];
				$packingSlipItemArray['main_carton_type_count'] = $main_carton_type_count_value;
				$packingSlipItemArray['main_carton_type_weight'] = $_REQUEST['main_carton_type_weight'][$main_carton_type_count_value][0];
				$packingSlipItemArray['main_carton_whole_actual_weight'] = $_REQUEST['main_carton_whole_actual_weight'][$main_carton_type_count_value][0];
				
				foreach ($_REQUEST['pro_name'][$main_carton_type_count_value] as $pro_name_key => $pro_name_value)
				{
					$packingSlipItemArray['pro_name'] = addslashes(html_entity_decode($pro_name_value));
					$packingSlipItemArray['pro_id'] = $_REQUEST['pro_id'][$main_carton_type_count_value][$pro_name_key];
					$packingSlipItemArray['weight_id'] = $_REQUEST['weight_id'][$main_carton_type_count_value][$pro_name_key];
					$packingSlipItemArray['pro_qty'] = $_REQUEST['pro_qty'][$main_carton_type_count_value][$pro_name_key];
					$packingSlipItemArray['pro_weight'] = $_REQUEST['pro_weight'][$main_carton_type_count_value][$pro_name_key];

					$db->rp_insert("packing_slip_item",array_values($packingSlipItemArray),array_keys($packingSlipItemArray),0);
				}
			}

			if($_REQUEST['dispatch_id'] != ""){

				$dispatch_id =  implode(",", $_REQUEST['dispatch_id']);
				$upadateid = $db->rp_update("dispatch_detail",array("status"=>2),"id IN (".$dispatch_id.")",0);
			}
		}
		/*For notification*/
		$id = $db->rp_getValue("dealer_distributor_network","sales_executive_id","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'",0);
		$type="packing_slip";
		$title="Packing Slip No ".$packing_slip_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME'];
		$body = "Packing Slip No ".$packing_slip_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
		$click_action="packing_slip_manage.php";

		$Data = [
		    'type' => $type,
            'title' => $title,
            'body' =>  $body,
            'description'    => $body,
            'user_id'        => $id,
            'reference_id'   => $PackingSlipId,
            'item_id'        => $PackingSlipId,
			'reference_type' => 'packing slip',
            'icon' => NOTIFICATIONICON,
            'image' => NOTIFICATIONIMAGE,
            'click_action'=> ADMINSITEURL.$click_action,
		];

		$ReferanceArray = [
            'reference_id' => 	$detail['quotation_id'],
            'reference_table' => "quotation_detail",
		];

		$id = $db->rp_getValue("dealer_distributor_network","sales_executive_id","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'",0);
		$id = $id;
    	if($id!="")
	    {
		    /*panel*/
		    $UPPERLEVEL1 = '1';
		    $UpperlevelAll = '1';
			$db->send_notificationpanel($Data,$id,$ReferanceArray,$Upperlevel1,$UpperlevelAll);
		    /*panel*/
		}
		/*For notification*/
		$db->rp_location("packing_slip_manage.php");
	}
	else if($_REQUEST['mode']=="edit")
	{
		$db->rp_location("packing_slip_manage.php");
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}	
	$status = $db->rp_getValue("packing_slip","status","id='".$id."' AND isDelete=0");
	if($status==0)
	{
		$id=$_REQUEST['id'];
		/*log entry*/
			$customer_id = $db->rp_getValue("packing_slip","customer_id","id='".$_REQUEST['id']."'");
			$packing_slip_no = $db->rp_getValue("packing_slip","packing_slip_no","id='".$_REQUEST['id']."'");
			$module_name = "Packing Slip";
			$flag = "Web";
			$log_description = $module_name." ".$packing_slip_no." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
		/*log entry*/
		$rows = array("isDelete"=>1);
		$db->rp_update("packing_slip",$rows,"id='".$id."'",0,$log_description,$flag,$module_name,"",$customer_id);
		$db->rp_update("packing_slip_item",array("isDelete"=>1),"packing_slip_id='".$id."'");

		$dispatch_id = $db->rp_getValue("packing_slip","dispatch_id","id='".$id."'",0);
		$db->rp_update("dispatch_detail",array("status"=>0),"id='".$dispatch_id."'");
		
		$db->addSuccessMessage("Packing Slip Delete Successfully");
		$db->rp_location($ctable."_manage.php");
	}
	else
	{
		$db->addErrorMessage("You Can Not Delete This Packing Slip Because It's Invoice Has Been Generated.");
		$db->rp_location($ctable."_manage.php");
	}
	
}

if($_REQUEST['mode']=="edit" && isset($_REQUEST['id']) && $_REQUEST['id']!="")
{
	$getPackingSplipDataR = $db->rp_getData("packing_slip","*","isDelete = 0 AND isActive = 1 AND id = '".$_REQUEST['id']."'");
	$getPackingSplipData = mysqli_fetch_assoc($getPackingSplipDataR);

	$customer_type = $getPackingSplipData['customer_type'];
	$customer_id = $getPackingSplipData['customer_id'];
	$dispatch_id = $getPackingSplipData['dispatch_id'];
	$packing_slip_no = $getPackingSplipData['packing_slip_no'];
	$packing_slip_date = $getPackingSplipData['packing_slip_date'];
	$packing_slip_date = date("d-m-Y",strtotime($packing_slip_date));


	$getPackingSplipItemDataR = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['id']."' GROUP BY main_carton_type_count");
	$getPackingSplipItemData = array();
	while($getPackingSplipItemDataComp = mysqli_fetch_assoc($getPackingSplipItemDataR))
	{
		$getPackingSplipItemData[] = $getPackingSplipItemDataComp;
	}
	// echo "<pre>";
	// print_r($getPackingSplipItemData);exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css" />
	<link rel="stylesheet" type="text/css" href="css/fSelect.css" />
	<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
	<style type="text/css">
		tbody td,
		th {
			border-left: none !important;
			border-right: none !important;
		}

		tfoot td {
			border: none !important;
		}

		.f-10 {
			font-size: 13px;
		}
		.select2-disabled>div:hover {
			cursor: not-allowed;
			/*background: #eee!important;*/
    		/*border: 1px dotted!important;*/
		}
	</style>

</head>

<body class="page-md">
	<?php include("header.php"); ?>
	<div class="page-container">
		<div class="page-head bg-grey">
			<div class="container">
				<div class="page-title">
					<?php 
					$back = 'packing_slip_manage.php';
					?>
					<h1><a href="<?php echo  $back; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1> 
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
				<!-- Employee ID-->
				<form role="form" action="" method="post" onSubmit="return check_form();">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet box blue">
								<div class="portlet-body form">
									<div class="row">
										<div class="col-sm-12">
											<div class="col-md-12 col-sm-12 col-xs-12 portlet box grey-cascade box">
												<div class="portlet-title">
													<div class="caption">
														<i class="fa fa-user"></i>
														<span class="caption-subject bold uppercase"> PACKING SLIP DETAIL</span>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-body">
														<div class="form-group">
															<div class="row">

																<div class="col-md-4 col-sm-4">
																	<div class="col-md-12">
																		<div class="form-group">
																			<?php
																			$customer_id = $db->rp_getValue("dispatch_detail","customer_id","id='".$_REQUEST['dispatch_id']."'",0);
																			?>
																			<label>Select Customer Type<code>*</code></label>
																			<input type="hidden" name="customer_selectd_id" id="customer_selectd_id" value="<?php echo $customer_id; ?>">
																			<select <?=($_REQUEST['mode']=="edit") || ($_REQUEST['mode']=="add" && $_REQUEST['dispatch_id']!="")?"disabled":"";?> class="form-control"  id="customer_type" name="customer_type" onchange="getCustomer(this.value)">
																				<option value="">Select Customer Type</option>
																				<?php
																				if($_REQUEST['mode']=="add" && $_REQUEST['dispatch_id'])
																				{
																					$customer_type = $db->rp_getValue("dispatch_detail","order_type","id='".$_REQUEST['dispatch_id']."'",0);
																				}
																				$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
																				if ($cust_R) 
																				{
																					while ($C = mysqli_fetch_assoc($cust_R)) 
																					{
																						?>
																						<option <?= ($customer_type == $C['id']) ? "selected" : ""; ?> value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
																						<?php
																					}
																				}
																				?>

																			</select>
																			<input type="hidden" name="customer_type" id="customer_type" value="<?= $customer_type ?>">
																			<p class="help-block"></p>
																		</div>
																		<div class="form-group">
																			<label>Select Customer<code>*</code></label>
																			<select <?=($_REQUEST['mode']=="edit") || ($_REQUEST['mode']=="add" && $_REQUEST['dispatch_id']!="")?"disabled":"";?> class="form-control" name="customer_id" placeholder="Select Customer" id="customer_id" onchange="getDispatchList(this.value)">
																				<option value="">Select Customer</option>
																			</select>
																			<p class="help-block"></p>
																		</div>
																		<div class="form-group">
																			<!-- onchange="getDispatchView()" -->
																			<label>Select Dispatch<code>*</code></label>
																			<select class="form-control" name="dispatch_id[]" placeholder="Select Dispatch" id="dispatch_id"  multiple="">
																				<option value="">Select Dispatch</option>
																			</select>
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Packing Slip No. <code>*</code></label>
																			<input type="text" readonly="" class="form-control" name="packing_slip_no" id="packing_slip_no" value="<?php echo $packing_slip_no; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Packing Slip Date <code>*</code></label>
																			<input type="text" readonly="" class="form-control" name="packing_slip_date" id="packing_slip_date" value="<?php echo $packing_slip_date; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																</div>
																<div class="col-md-3 col-sm-3">
																	<div class="form-group">
																		<div class="row static-info phone">
																			<div class="col-md-5 name"> Name : </div>
																			<div class="col-md-7 value" name="name" id="name"><?php echo $customer_name; ?> </div>
																		</div>
																		<div class="row static-info phone">
																			<div class="col-md-5 name"> Phone : </div>
																			<div class="col-md-7 value" name="name_phone" id="name_phone"><?php echo $contact_number; ?> </div>
																		</div>
																		<div class="row static-info address">
																			<div class="col-md-5 name"> Address : </div>
																			<div class="col-md-7 value" name="name_address" id="name_address"><?php echo $address; ?></div>
																		</div>
																		<div class="row static-info address">
																			<div class="col-md-5 name"> State : </div>
																			<div class="col-md-7 value" name="name_state" id="name_state"><?php echo $customer_state; ?></div>
																		</div>
																		<div class="row static-info address">
																			<div class="col-md-5 name"> GSTIN : </div>
																			<div class="col-md-7 value" name="name_gstin" id="name_gstin"><?php echo $customer_gstin; ?></div>
																		</div>
																		<div class="row static-info address">
																			<div class="col-md-5 name"> Pricelist : </div>
																			<div class="col-md-7 value" name="name_pricelist" id="name_pricelist"><?php echo $customer_pricelist; ?></div>
																		</div>
																	</div>
																</div>
																<div class="col-md-5 col-sm-5">
																	<div class="table-responsive invoice-item-data" style="max-height: 300px!important">
																		
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
							<div class="row">
								<div class="col-md-12 col-sm-12">
									<div class="portlet grey-cascade box">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-user"></i>
												<span class="caption-subject bold uppercase"> PACKING SLIP ITEM</span>
											</div>
										</div>
										<div class="portlet-body">
											<div class="row">
												<div class="col-sm-12 col-lg-12 col-xs-12">
													<div class="row">
														<div class="col-md-4">
															<div class="form-group">
																<label for="packing_type">Packing Type<code>*</code></label>
																<select class="form-control" id="packing_type" name="packing_type" <?=($_REQUEST['mode']=="edit")?"disabled":"";?>>
																	<option data-name="" data-weight="0" value="">Select Packing Type</option>
																	<?php
																		$PackingTypeData = $db->rp_getData("packing_type","name,weight,id","isDelete = 0 AND isActive = 1");
																		while ($PackingType = mysqli_fetch_assoc($PackingTypeData))
																		{
																			?>
																			<option data-name="<?=$PackingType['name']?>" data-weight="<?=$PackingType['weight']?>" value="<?=$PackingType['id']?>"><?=$PackingType['name']." - ".$PackingType['weight']." KG"?></option>
																			<?php
																		}
																	?>
																</select>
															</div>
														</div>
														<div class="col-md-4">
															<button type="button" <?=($_REQUEST['mode']=="edit")?"disabled":"";?> class="btn btn-success" id="add-another-carton" style="margin-top: 24px"><i class="fa fa-plus"></i> Add</button>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<table class="table table-borderd" border="1">
																<thead>
																	<tr>
																		<th class="text-center" colspan="5">Client Name: <span class="set-client-name"></span></th>
																	</tr>
																	<tr>
																		<th style="width: 10%">Sr. No.</th>
																		<th>Item</th>
																		<th style="width: 25%">Requested Qty</th>
																		<th style="width: 25%">Packing Qty</th>
																		<th style="width: 25%">KGs</th>
																	</tr>
																</thead>
																<tbody class="main-dipatch-details-body">
																	<?php
																		$MAINTOTALQTY = 0;
																		$MAINTOTALWEIGHT = 0;
																		if($_REQUEST['mode']=="edit")
																		{
																			$main_carton_type_count = 0;
																			$MAINTOTALQTY = 0;
																			$MAINTOTALWEIGHT = 0;
																			foreach ($getPackingSplipItemData as $item_key => $item_value) {
																				?>
																				<tr>
																					<td colspan="4">
																						<b><?=$item_value['main_carton_type_name']?> No : </b><?=$item_value['main_carton_type_count']?>
																						<input data-total-packing="<?=$item_value['main_carton_type_count']?>" type="hidden" name="main_carton_type[]" class="form-control main-carton-array" value="<?=$item_value['main_carton_type']?>">
																						<input data-total-packing="<?=$item_value['main_carton_type_count']?>" type="hidden" name="main_carton_type_name[]" class="form-control" value="<?=$item_value['main_carton_type_name']?>">
																						<input data-total-packing="<?=$item_value['main_carton_type_count']?>" type="hidden" name="main_carton_type_count[]" class="form-control" value="<?=$item_value['main_carton_type_count']?>">
																					</td>
																				</tr>
																				<?php
																				$getPackingSplipItemDataFullR = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['id']."' AND main_carton_type_count='".$item_value['main_carton_type_count']."'");
																				$getPackingSplipItemDataFull = array();
																				while($getPackingSplipItemDataFullComp = mysqli_fetch_assoc($getPackingSplipItemDataFullR))
																				{
																					$getPackingSplipItemDataFull[] = $getPackingSplipItemDataFullComp;
																				}
																				$count = 1;
																				$TOTALQTY = 0;
																				$TOTALWEIGHT = 0;
																				foreach ($getPackingSplipItemDataFull as $item_full_key => $item_full_value) {
																						$TOTALQTY += $item_full_value['pro_qty'];
																						$TOTALWEIGHT += $item_full_value['pro_weight'];
																					?>
																					<tr>
																						<td style="width: 10%">
																							<button data-total-packing="<?=$item_full_value['main_carton_type_count']?>" type="button" class="remove-this btn btn-danger btn-sm" style="padding: 5px 10px 5px 10px">
																								<i class="fa fa-trash"></i> 
																								<b style="font-size: 14px!important" class="counter-plus-for-item" data-total-packing="<?=$item_full_value['main_carton_type_count']?>">
																									<?=$count++;?>
																								</b>
																							</button>
																						</td>
																						<td>
																							<?=$item_full_value['pro_name'];?>
																							<input data-total-packing="<?=$item_full_value['main_carton_type_count']?>" type="hidden" name="pro_name[<?=$item_full_value['main_carton_type_count']?>][]" class="form-control" value="<?=$item_full_value['pro_name'];?>">
																							<input data-total-packing="<?=$item_full_value['main_carton_type_count']?>" type="hidden" name="pro_id[<?=$item_full_value['main_carton_type_count']?>][]" class="form-control" value="<?=$item_full_value['pro_id'];?>">
																							<input data-total-packing="<?=$item_full_value['main_carton_type_count']?>" type="hidden" name="weight_id[<?=$item_full_value['main_carton_type_count']?>][]" class="form-control" value="<?=$item_full_value['weight_id'];?>">
																						</td>
																						<td style="width: 25%">
																							<input data-total-packing="<?=$item_full_value['main_carton_type_count']?>" data-pro-id="<?=$item_full_value['pro_id'];?>" data-pro-weight="<?=$item_full_value['weight_id'];?>" data-change-poduct-weight-id="<?=$item_full_value['pro_id'];?>##<?=$item_full_value['weight_id'];?>" type="text" name="pro_qty[<?=$item_full_value['main_carton_type_count']?>][]" class="form-control product-qty" value="<?=$item_full_value['pro_qty']?>">
																						</td>
																						<td style="width: 25%" class="text-right">
																							<input data-total-packing="<?=$item_full_value['main_carton_type_count']?>" data-pro-id="<?=$item_full_value['pro_id'];?>" data-pro-weight="<?=$item_full_value['weight_id'];?>" type="text" name="pro_weight[<?=$item_full_value['main_carton_type_count']?>][]" class="form-control product-weight" value="<?=$item_full_value['pro_weight'];?>">
																						</td>
																					</tr>
																					<?php
																				}
																				$TOTALQTY += 0;
																				$TOTALWEIGHT += $item_full_value['main_carton_type_weight'];

																				$MAINTOTALQTY += $TOTALQTY;
																				$MAINTOTALWEIGHT += $TOTALWEIGHT;
																				?>
																				<tr style="background: #eee!important">
																					<td colspan="2" class="text-right"><b><?=$item_value['main_carton_type_name']?> Weight</b></td>
																					<td class="text-center">&nbsp;</td>
																					<td class="text-right">
																						<input data-total-packing="<?=$item_value['main_carton_type_count']?>" type="text" readonly="" name="main_carton_type_weight[<?=$item_value['main_carton_type_count']?>][]" class="form-control product-weight" value="<?=$item_value['main_carton_type_weight']?>">
																					</td>
																				</tr>

																				<tr style="background: #eee!important">
																					<td colspan="2" class="text-right"><b>Total</b></td>
																					<td class="text-center">
																						<input data-total-packing="<?=$item_value['main_carton_type_count']?>" type="text" readonly="" class="form-control product-all-total-qty" value="<?=$TOTALQTY?>">
																					</td>
																					<td class="text-right">
																						<input data-total-packing="<?=$item_value['main_carton_type_count']?>" type="text" readonly="" class="form-control product-all-total-weight" value="<?=$TOTALWEIGHT?>">
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2" class="text-right"><b>Actual Weight</b></td>
																					<td class="text-center">&nbsp;</td>
																					<td class="text-right">
																						<input data-total-packing="<?=$item_value['main_carton_type_count']?>" name="main_carton_whole_actual_weight[<?=$item_value['main_carton_type_count']?>][]" type="text" class="form-control product-all-total-weight-actual" value="<?=$item_value['main_carton_whole_actual_weight']?>">
																					</td>
																				</tr>
																				<?php
																			}
																		}
																	?>
																	<tr class="remove-this-before-save-click" style="background: #eee!important">
																		<td colspan="3" class="text-right">
																			<b>Grand Total</b>
																		</td>
																		<td class="text-center">
																			<input readonly="" class="form-control mainChanged-Qty" value="<?=$MAINTOTALQTY?>">
																		</td>
																		<td class="text-right">
																			<input readonly="" class="form-control" value="<?=$MAINTOTALWEIGHT?>">
																		</td>
																	</tr>
																</tbody>
															</table>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="row">
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Remarks</label>
																		<textarea class="form-control" name="remark" value="<?php echo $remark; ?>"></textarea>
																		<p class="help-block"></p>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
													<div class="col-md-5" style="margin-top: 25px;">
														<button type="submit" name="submit" class="btn green">Submit</button>
														<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $back; ?>'">Back</button>
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
			</form>
		</div>
	</div>
</div>


<!-- Modal -->
<div id="addCartonMoadal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title carton-modal-title"></h4>
      </div>
      <div class="modal-body">
      	<table class="table table-borderd" border="1">
      		<tbody class="carton-modal-body">
      			
      		</tbody>
      	</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success carton-modal-save">Save</button>
      </div>
    </div>

  </div>
</div>



	<?php include("footer.php"); ?>
	<?php include("include_js.php"); ?>
	<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
	<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="js/fSelect.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
	<!-- <script type="text/javascript" src="js/repeater.js"></script> -->
	<script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		/*$("#packing_charge").numeric();
		$("#transport_charge").numeric();*/
		var mode = "<?= $_REQUEST['mode'] ?>";
		const format = (num = 0, decimals = 3) => num.toLocaleString('en-US', {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		});
		$('#packing_slip_date').datepicker({
			datepicker: true,
			autoclose: true,
			dateFormat: 'dd-mm-yy',
			maxDate: 0
		});
		$('#po_date').datepicker({
			datepicker: true,
			autoclose: true,
			dateFormat: 'dd-mm-yy',
			maxDate: 0
		});
		
		function setCustomerData()
		{
			var customer_name = $("#customer_id").find('option:selected').data("name");
			var phone = $("#customer_id").find('option:selected').data("phone");
			var address = $("#customer_id").find('option:selected').data("address");
			var place_of_supply = $("#customer_id").find('option:selected').data("state");
			var cust_type = $("#customer_id").find('option:selected').data("cutomer-type");
			var state = $("#customer_id").find('option:selected').data("state");
			var gstin = $("#customer_id").find('option:selected').data("gstin");
			var pricelist = $("#customer_id").find('option:selected').data("price-list");
			var cname = $("#customer_id").find('option:selected').data("cname");
			var gst_type = $("#customer_id").find('option:selected').data("gst-type");
			$("#name_value").html(customer_name);
			$("#name_phone").html(phone);
			$("#name_address").html(address);
			$("#name_state").html(state);
			$("#name_gstin").html(gstin);
			$("#name").html(cname);
			$("#name_pricelist").html(pricelist);
			$(".gst_type").html(gst_type);
		}


		var mode = '<?php echo $_REQUEST['mode']; ?>';
		var dispatch_id = '<?php echo $_REQUEST['dispatch_id'];?>';
		if(mode=="add" && dispatch_id!="")
		{
			var ctype = $("#customer_type").val();
			var selected_value = $("#customer_selectd_id").val();
			getCustomer(ctype,selected_value);						
			getDispatchList(selected_value,dispatch_id);						
		}

		function getCustomer(ctype,selected_value='') {
			$.ajax({
				type: "post",
				url: "ajax_get_customer.php",
				data: "customer_type=" + ctype+"&selected_value=" + selected_value,
				beforeSend: function() {
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					setTimeout(function() {
						$('#customer_id').select2("destroy");
						$('#customer_id').html(result);
						$('#customer_id').select2();
						$('.preloader').fadeOut('slow');
					});
				}
			})
		}

		function getDispatchList(cid,selected_value='') {

			var mode = "<?=$_REQUEST['mode']?>";

			$.ajax({
				type: "post",
				url: "ajax_dispatchlist_from_customer.php",
				data: "cid=" + cid+"&selected_value=" + selected_value+"&mode=" + mode,
				beforeSend: function() {
					$(".set-client-name").html("");
					$(".transCover").fadeIn(800);
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					setTimeout(function() {
						setCustomerData();
						$("#dispatch_id").fSelect("destroy");
						$("#dispatch_id").html(result);
						$("#dispatch_id").fSelect("create");
						$('.preloader').fadeOut('slow');
						
						$(".set-client-name").html($("#customer_id").find("option:selected").data('cname'));

						if(mode=="edit")
						{
							// $("#customer_id").change();
							$("#dispatch_id").find("option").attr("disabled","disabled");
							$(".fs-option").addClass("disabled");
						}
						
					},500);
				}

			})
		}

		$("#dispatch_id").on("change",function(e){
			var mode = "<?=$_REQUEST['mode']?>";
			var selected = $(this).val();
			var Dataselected = $.data(this, 'current');
			if(selected!=null)
			{
				selected = selected.join(",");
			}
			if(Dataselected!=null)
			{
				Dataselected = Dataselected.join(",");
			}

			var total_package = $( ".main-carton-array" ).length;
			if(total_package==0)
			{
				var isValid = true;
			}
			else
			{
				if(selected!=Dataselected && mode!="edit")
				{
					var isValid = confirm("Are you sure you want to reset packing slip item?");
				}
				else
				{
					var isValid = false;
				}
			}

			if(isValid)
			{
				$.data(this, 'current', $(this).val());
				getDispatchView(isValid);
			}
			else
			{
				// alert(selected)
				// alert($.data(this, 'current'))
				if(selected!=Dataselected && mode!="edit")
				{
					$(this).fSelect("destroy");
					$(this).val($.data(this, 'current'));
					$(this).fSelect("create");
				}

				// alert($(this).val());
				// e.preventDefault();
				// alert($(this).val());
			}
			if(mode=="edit")
			{
				getDispatchView(true);
			}
		})

		function getDispatchView(isValid) {
			// alert(isValid);
			if(isValid)
			{
				var mode = "<?=$_REQUEST['mode']?>";
				if(mode!="edit")
				{
					$(".main-dipatch-details-body").html('<tr class="remove-this-before-save-click" style="background: #eee!important"><td colspan="3" class="text-right"><b>Grand Total</b></td><td class="text-center"><input readonly="" class="form-control mainChanged-Qty" value="0"></td><td class="text-right"><input readonly="" class="form-control" value="0"></td></tr>');
				}


				var dispatch_id = $("#dispatch_id").val();
				if(dispatch_id==null) {
					dispatch_id = 0;
				}

				if(dispatch_id!=0)
				{
					$("#packing_type").select2("destroy");
					if(mode!="edit")
					{
						$("#add-another-carton").removeAttr("disabled","disabled");
						$("#packing_type option").removeAttr("disabled");
					}
					$("#packing_type").select2();
				}
				else
				{
					$("#packing_type").select2("destroy");
					$("#packing_type").val("");
					$("#add-another-carton").attr("disabled","disabled");
					$("#packing_type option[value!='']").attr("disabled","disabled");
					$("#packing_type").select2();
				}

				$.ajax({
					type: "post",
					url: "ajax_packing_slip_detail_from_dispatch.php",
					data: "dispatch_id=" + dispatch_id,
					beforeSend: function() {
						$(".transCover").fadeIn(800);
						$('.preloader').fadeIn('slow');
						$(".invoice-item-data").html("");
					},
					success: function(result) {
						setTimeout(function() {
							$('.preloader').fadeOut('slow');
							$(".invoice-item-data").html(result);
						});
					}
				})
			}
		}
		// $(".main-dipatch-details-body")

		$("#add-another-carton").on("click",function(e){
			// alert();
			var packing_type = $("#packing_type").val();
			if(packing_type!="")
			{
				var total_package = $( ".main-carton-array" ).length;
				var dispatch_id = $("#dispatch_id").val();
				if(dispatch_id==null) {
					dispatch_id = 0;
				}

				var packing_type_name = $("#packing_type option:selected").data("name");
				var packing_type_weight = $("#packing_type option:selected").data("weight");

				var newProductArray = [];

				/*$(".qty-class").each( function(index) {
					var proid = $(this).data("pro-id");
					var proweight = $(this).data("pro-weight");
					var proqty = $(this).data("pro-qty");
					newProductArray[proid+"#"+proweight]
				});*/

				$(".carton-modal-title").html("<b>"+packing_type_name+' No : </b>'+(parseFloat(total_package)+1));
				$.ajax({
					url: "ajax_get_packing_slip_modal_data.php",
					method: "POST",
					data: {
						packing_type:packing_type,
						total_package:(parseFloat(total_package)+1),
						packing_type_name:packing_type_name,
						packing_type_weight:packing_type_weight,
						dispatch_id:dispatch_id
					},
					success: function(data){
						$(".carton-modal-body").html(data);
						$(".product-qty").numeric();
						$(".product-weight").numeric();
						$(".product-all-total-weight-actual").numeric();
						$(".carton-modal-save").data("total-packing",(parseFloat(total_package)+1));
						$("#addCartonMoadal").modal("show");
					},
				});
			}
			else
			{
				toastr.error("Please select packing type first!!");
			}
		})

		$("#addCartonMoadal").on('hidden.bs.modal', function(){
			$(".carton-modal-body").html("");
			$(".carton-modal-save").data("total-packing",0);
			GenerateFinalAllTotal();
		});

		$(".carton-modal-save").on("click",function(e){
			var trueFalse = true;
			var totalpacking = $(this).data("total-packing");
			$(".product-qty[data-total-packing='"+totalpacking+"']").each( function(index) {
				var thisValue = $(this).val();
				var thisValue = parseFloat(thisValue);
				if(isNaN(thisValue) || thisValue=="" || thisValue==0 || thisValue=="0.000" || thisValue<=0)
				{
					trueFalse = false;
				}
			});
			$(".product-weight[data-total-packing='"+totalpacking+"']").each( function(index) {
				var thisValue = $(this).val();
				var thisValue = parseFloat(thisValue);
				if(isNaN(thisValue) || thisValue=="" || thisValue==0 || thisValue=="0.000" || thisValue<=0)
				{
					trueFalse = false;
				}
			});
			
			if(trueFalse)
			{
				$(".remove-this-before-save-click").remove();

				$(".carton-modal-body>tr").clone().appendTo(".main-dipatch-details-body");

				// $(".main-dipatch-details-body").append($(".carton-modal-body").html());
				$("#addCartonMoadal").modal("hide");
			}
			else
			{
				toastr.error("Input must be fulfill with value grater then zero ( 0 )");
			}
		});

		$(document).on('click','.remove-this', function() {
			var whichpacking = $(this).data('total-packing');
			var anotherItemAvailableCheck = $( ".remove-this[data-total-packing='"+whichpacking+"']" ).length;
		    // alert(anotherItemAvailableCheck);
		    if(anotherItemAvailableCheck>1)
		    {
		    	$(this).parent().parent().remove();
		    	toastr.success("remove successfully..");
		    }
		    else
		    {
		    	toastr.error("Can't remove single item!!");
		    }

		    functionToSetTotalQtyAndWeight(whichpacking);
		});

		$(document).on('keyup','.product-qty', function(e) {

			/*var thisqty = $(this).val();
			var thisqty = parseFloat(thisqty);
			if(isNaN(thisqty))
			{
				var thisqty = 0;
			}

			var thisproid = $(this).data("pro-id");
			var thisproweigt = $(this).data("pro-weight");*/

			var poductweightid = $(this).data("change-poduct-weight-id");

			var userQtyTotal = 0;
			var maxQtyTotalLimit = $(".qty-class[data-final-poduct-weight-id='"+poductweightid+"']").data("pro-qty");

			$(".product-qty[data-change-poduct-weight-id='"+poductweightid+"']").each(function(index){
				var thisqty = $(this).val();
				var thisqty = parseFloat(thisqty);
				if(isNaN(thisqty))
				{
					var thisqty = 0;
				}
				userQtyTotal = userQtyTotal+thisqty;
			});

			var maxQtyTotalLimit = parseFloat(maxQtyTotalLimit);
			if(isNaN(maxQtyTotalLimit))
			{
				var maxQtyTotalLimit = 0;
			}

			if(maxQtyTotalLimit<userQtyTotal)
			{
				$(this).val(0);
				toastr.error("Value can not be grater then dispatch qty!!");
			}


			var whichpacking = $(this).data('total-packing');
		    functionToSetTotalQtyAndWeight(whichpacking);
		});
		$(document).on('keyup','.product-weight', function() {
			var whichpacking = $(this).data('total-packing');
		    functionToSetTotalQtyAndWeight(whichpacking);
		});

		/*$(document).on('click','.remove-this', function() {
		});*/

		function GenerateFinalAllTotal()
		{
			$(".remove-this-before-save-click").remove();

			var totalQTY = 0;
			var totalWEIGHT = 0;
			
			$(".product-qty").each(function(index){

				var thisqty = $(this).val();
				var thisqty = parseFloat(thisqty);
				if(isNaN(thisqty))
				{
					var thisqty = 0;
				}

				totalQTY = thisqty+totalQTY;
			});
			$(".product-weight").each(function(index){

				var thisweight = $(this).val();
				var thisweight = parseFloat(thisweight);
				if(isNaN(thisweight))
				{
					var thisweight = 0;
				}

				totalWEIGHT = thisweight+totalWEIGHT;
			});
			
			var htmltoBeappend = '<tr class="remove-this-before-save-click" style="background: #eee!important">';
			htmltoBeappend += '<td colspan="3" class="text-right"><b>Grand Total</b></td>';
			htmltoBeappend += '<td class="text-center"><input readonly class="form-control mainChanged-Qty" value="'+totalQTY+'"></td>';
			htmltoBeappend += '<td class="text-right"><input readonly class="form-control" value="'+totalWEIGHT+'"></td>';
			htmltoBeappend += '</tr>';


			$(".main-dipatch-details-body").append(htmltoBeappend);

		}

		function functionToSetTotalQtyAndWeight(count_id)
		{
			var totalcounterplus = 0;
			var total_all_product_qty = 0;
			var total_all_product_weight = 0;

			$(".product-qty[data-total-packing='"+count_id+"']").each(function(index){
				var thisqty = $(this).val();
				var thisqty = parseFloat(thisqty);
				if(isNaN(thisqty))
				{
					var thisqty = 0;
				}
				total_all_product_qty = total_all_product_qty+thisqty;
			});
			$(".product-weight[data-total-packing='"+count_id+"']").each(function(index){
				var thisweight = $(this).val();
				var thisweight = parseFloat(thisweight);
				if(isNaN(thisweight))
				{
					var thisweight = 0;
				}
				total_all_product_weight = total_all_product_weight+thisweight;
			});
			$(".counter-plus-for-item[data-total-packing='"+count_id+"']").each(function(index){
				$(this).html(++totalcounterplus);
			});

			var total_all_product_qty = total_all_product_qty.toFixed(3);
			var total_all_product_weight = total_all_product_weight.toFixed(3);
			$(".product-all-total-qty[data-total-packing='"+count_id+"']").val(total_all_product_qty);
			$(".product-all-total-weight[data-total-packing='"+count_id+"']").val(total_all_product_weight);
			$(".product-all-total-weight-actual[data-total-packing='"+count_id+"']").val(total_all_product_weight);
		}

		function check_form() {
			$(".form-body").children().removeClass("has-error");
			var isValid = true;

			var totalPackingTobe = 0;
			$(".qty-class").each(function(index){
				totalPackingTobe = parseFloat(totalPackingTobe)+parseFloat($(this).data("pro-qty"));
			});

			var totalPackingPacked = $(".mainChanged-Qty").val();
			totalPackingPacked = parseFloat(totalPackingPacked);
			if(isNaN(totalPackingPacked))
			{
				totalPackingPacked = 0;
			}

			// alert(totalPackingTobe);
			// alert(totalPackingPacked);
			if(totalPackingTobe!=totalPackingPacked)
			{
				toastr.error("Please make sure all item is packed per selected dispatch");
				isValid = false;
			}

			<?php if ($_REQUEST['mode'] == "add") {
			?> 
				if ($("#customer_type").val() == "" || $("#customer_type").val().split(" ").join("") == "") {
					vd = aj.error('customer_type', "Please Select Customer Type", "add_error");
					isValid = false;
				}
				if ($("#customer_id").val() == "" || $("#customer_id").val().split(" ").join("") == "") {
					vd = aj.error('customer_id', "Please Select Customer.", "add_error");
					isValid = false;
				}
				if ($("#dispatch_id").val() == null) { 
					vd = aj.error('dispatch_id', "Please Select Dispatch.", "add_error");
					toastr.error("Please Select Dispatch");
					isValid = false;
				}
				if ($("#packing_slip_date").val() == "" || $("#packing_slip_date").val().split(" ").join("") == "") {
					vd = aj.error('packing_slip_date', "Please Enter Packing Slip Date.", "add_error");
					isValid = false;
				}

			<?php } ?>
			if (isValid) {
				var r = confirm("Are You sure want to Save this Packing Slip??");
				if (r) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		$(".form-control").bind("keyup change", function() {
			if ($(this).parent().hasClass("has-error")) {
				$(this).parent().removeClass("has-error");
				$(this).parent().find('p.help-block').html("");
			}
		});
		setTimeout(function() {
			$("#dispatch_id").select2("destroy");
			$("#dispatch_id").fSelect();
		},500);

		$(document).ready(function (){
			var mode = "<?=$_REQUEST['mode']?>";
			if(mode=="edit")
			{
				var customer_type = '<?=$customer_type?>';
				var customer_id = '<?=$customer_id?>';
				var dispatch_id = '<?=$dispatch_id?>';
				getCustomer(customer_type,customer_id);
				getDispatchList(customer_id,dispatch_id);
			}
		});
	</script>
</body>
</html>