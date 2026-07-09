<?php
$page_id    = 612;
$page_slug  = 'packing_slip';
$ctable     = "packing_slip";
$page_title = "Packing Slip";
$page_title = ucwords($_REQUEST['mode']) . " " . $page_title;

include("connect.php");

$packing_slip_no       = $db->getLastInsertId($ctable);
$packing_slip_no       = PACKING_SLIP_NO . str_pad($packing_slip_no, 2, '0', STR_PAD_LEFT);
$packing_slip_date     = date('d-m-Y');

$uid              = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$utype            = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];


$packing_charge   = 0;
$transport_charge = 0;

if (isset($_REQUEST['submit'])) {
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
		
		$packingSlipArray['remark'] = $_REQUEST['remark'];
		$packingSlipArray['dispatch_id'] = implode(",", $_REQUEST['dispatch_id']);
		$packingSlipArray['packing_slip_no'] = $_REQUEST['packing_slip_no'];

		//print_r($packingSlipArray); exit;
		$packingSlipArray['packing_slip_date'] = date("Y-m-d",strtotime($_REQUEST['packing_slip_date']));
		$PackingSlipId = $db->rp_insert("packing_slip",array_values($packingSlipArray),array_keys($packingSlipArray),0);

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
					$packingSlipItemArray['pro_name'] = $db->clean($pro_name_value);
					$packingSlipItemArray['pro_id'] = $_REQUEST['pro_id'][$main_carton_type_count_value][$pro_name_key];
					$packingSlipItemArray['weight_id'] = $_REQUEST['weight_id'][$main_carton_type_count_value][$pro_name_key];
					$packingSlipItemArray['pro_qty'] = $_REQUEST['pro_qty'][$main_carton_type_count_value][$pro_name_key];
					$packingSlipItemArray['pro_weight'] = $_REQUEST['pro_weight'][$main_carton_type_count_value][$pro_name_key];

					$packingslip_item_id = $db->rp_insert("packing_slip_item",array_values($packingSlipItemArray),array_keys($packingSlipItemArray),0);
					if($packingslip_item_id!=0)
					{
						$current_stock=$db->rp_getValue("product_weight_price","stock_qty","product_id='".$packingSlipItemArray['pro_id']."' AND weight_id='".$packingSlipItemArray['weight_id']."'");
						$remaining_stock_qty=$current_stock-$packingSlipItemArray['pro_qty'];
						$db->rp_update("product_weight_price",array("stock_qty"=>$remaining_stock_qty),"product_id='".$packingSlipItemArray['pro_id']."' AND weight_id='".$packingSlipItemArray['weight_id']."'",0);
					}
				}
			}
			if($_REQUEST['dispatch_id'] != ""){

				$dispatch_id =  implode(",", $_REQUEST['dispatch_id']);
				$upadateid = $db->rp_update("dispatch_detail",array("status"=>2),"id IN (".$dispatch_id.")",0);
			}
		}

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
		$Update_ids = $db->rp_update("packing_slip",array("isDelete"=>1),"id='".$id."'");

		/*UPDATE STOCK*/
		if($Update_ids)
		{
			$packing_d=$db->rp_getData("packing_slip_item","*","packing_slip_id='".$_REQUEST['id']."'","",0);
			while($packing_r=mysqli_fetch_array($packing_d))
			{
				$pro_id=$packing_r['pro_id'];
				$weight_id=$packing_r['weight_id'];
				$qty=$packing_r['pro_qty'];

				$all_order_items_with_pid=$db->rp_getData("product_weight_price","*","product_id='".$pro_id."' AND weight_id='".$weight_id."'","id ASC",0);
				$receive_qty=$qty;
				if($all_order_items_with_pid)
				{
					$current_order_item=mysqli_fetch_assoc($all_order_items_with_pid);
					$product_qty=$current_order_item['stock_qty'];
					if($receive_qty>0)
					{
						$new_receive_qty = $product_qty+$receive_qty;
						$row 	= array("stock_qty"=>$new_receive_qty);
						$update_dispatch_qty = $db->rp_update("product_weight_price",$row,"product_id='".$pro_id."' AND weight_id='".$weight_id."'",0);
					}
					else
					{
						break;
					}
				}
			}
		}
		/*UPDATE STOCK*/

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

if($_REQUEST['mode']=="edit" && isset($_REQUEST['packing_id']) && $_REQUEST['packing_id']!="")
{
	$getPackingSplipDataR = $db->rp_getData("packing_slip","*","isDelete = 0 AND isActive = 1 AND id = '".$_REQUEST['packing_id']."'","",0);
	$getPackingSplipData = mysqli_fetch_assoc($getPackingSplipDataR);

	$customer_type = $getPackingSplipData['customer_type'];
	$customer_id = $getPackingSplipData['customer_id'];
	$dispatch_id = $getPackingSplipData['dispatch_id'];
	$packing_slip_no = $getPackingSplipData['packing_slip_no'];
	$packing_slip_date = $getPackingSplipData['packing_slip_date'];
	$packing_slip_date = date("d-m-Y",strtotime($packing_slip_date));
	$remark = $getPackingSplipData['remark'];


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
					$type = $db->rp_getValue("executive", "type_of_executive", "id=" . $uid . "", 0);
					$back = 'packing_slip_manage.php';
					?>
					<h1><a href="<?php echo  $back; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
																			<?php
																			if($_REQUEST['mode']=="add" && $_REQUEST['dispatch_id'])
																			{
																				$customer_type = $db->rp_getValue("dispatch_detail","order_type","id='".$_REQUEST['dispatch_id']."'",0);

																				$customer_id = $db->rp_getValue("dispatch_detail","customer_id","id='".$_REQUEST['dispatch_id']."'",0);
																			} 
																			?>
																			<label>Select Customer<code>*</code></label>
																			<input type="hidden" name="customer_type" id="customer_type" value="<?= $customer_type ?>">
																			<input type="hidden" name="customer_selectd_id" id="customer_selectd_id" value="<?php echo $customer_id; ?>">
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
														<div class="col-md-2">
															<div class="form-group">
																<label for="packing_type">Packing Type<code>*</code></label>
																<select class="form-control" id="packing_type" name="packing_type">
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
														<div class="col-md-2">
															<div class="form-group">
																<label for="cartoon_no">Cartoon No<code>*</code></label>
																<input type="text" name="cartoon_no" id="cartoon_no" class="form-control" onchange="getProduct()">
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="product_id">Select Product<code>*</code></label>
																<select class="form-control" id="product_id" name="product_id">
																	<option>Select Product</option>
																</select>
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="product_id">Qty<code>*</code></label>
																<input type="text" name="qty" id="qty" class="form-control add_qty" onchange="checkQtyValidation(this.value)">
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="product_id">Weight<code>*</code></label>
																<input type="text" name="weight" id="weight" class="form-control">
															</div>
														</div>
														<div class="col-md-2">
															<button type="button" class="btn btn-success" id="add_cartoon" style="margin-top: 24px"><i class="fa fa-plus"></i> Add</button>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="main-dipatch-details-body"></div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="row">
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Remarks</label>
																		<textarea class="form-control" name="remark" id="remark" value="<?php echo $remark; ?>"><?php echo $remark; ?></textarea>
																		<p class="help-block"></p>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
													<div class="col-md-5" style="margin-top: 25px;">
														<!-- <button type="submit" name="submit" class="btn green">Submit</button> -->
														<button type="submit" name="submit" class="btn green" onClick="window.location.href='packing_slip_manage.php'">Submit</button>
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
		var newArrayForRemainig = [];
		$( document ).ready(function() {
			var mode = '<?php echo $_REQUEST['mode']; ?>';
			var dispatch_id = '<?php echo $_REQUEST['dispatch_id'];?>';
			if(mode=="add" && (typeof dispatch_id === 'undefined' || dispatch_id === null || dispatch_id === ''))
			{
				var packing_slip_module = 'packing_slip';
    			getCustomer('all','',packing_slip_module);
    		}
		});

		/*$("#packing_charge").numeric();
		$("#transport_charge").numeric();*/
		var mode = "<?= $_REQUEST['mode'] ?>";
		const format = (num = 0, decimals = 3) => num.toLocaleString('en-US', {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		});

		$("#cartoon_no").numeric();
		$("#qty").numeric();
		
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
			var client_code = $("#customer_id").find('option:selected').data("client_code");
			var phone = $("#customer_id").find('option:selected').data("phone");
			var address = $("#customer_id").find('option:selected').data("address");
			var place_of_supply = $("#customer_id").find('option:selected').data("state");
			var cust_type = $("#customer_id").find('option:selected').data("cutomer-type");
			var state = $("#customer_id").find('option:selected').data("state");
			var gstin = $("#customer_id").find('option:selected').data("gstin");
			var pricelist = $("#customer_id").find('option:selected').data("price-list");
			var cname = $("#customer_id").find('option:selected').data("cname");
			var gst_type = $("#customer_id").find('option:selected').data("gst-type");
			var client_code = $("#customer_id").find('option:selected').data("client_code");
			$("#name_value").html(customer_name);
			$("#name_phone").html(phone);
			$("#name_address").html(address);
			$("#name_state").html(state);
			$("#name_gstin").html(gstin);
			$("#name").html(cname);
			//$("#name").html(client_code);
			$("#name_pricelist").html(pricelist);
			$(".gst_type").html(gst_type);
		}


		var mode = '<?php echo $_REQUEST['mode']; ?>';
		var dispatch_id = '<?php echo $_REQUEST['dispatch_id'];?>';
		if(mode=="add" && dispatch_id!="")
		{
			var ctype = $("#customer_type").val();
			var selected_value = $("#customer_selectd_id").val();
			var packing_slip_module = 'packing_slip';
			getCustomer(ctype,selected_value,packing_slip_module);						
			getDispatchList(selected_value,dispatch_id);						
		}
		// var packing_slip=1;
		function getCustomer(ctype,selected_value='',packing_slip_module) {
			$.ajax({
				type: "post",
				url: "ajax_get_customer.php",
				data: "customer_type=" + ctype+"&selected_value=" + selected_value+"&packing_slip_module=" + packing_slip_module,
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

						/*if(mode=="edit")
						{
							// $("#customer_id").change();
							$("#dispatch_id").find("option").attr("disabled","disabled");
							$(".fs-option").addClass("disabled");
						}*/
						
					},500);
				}

			})
		}

		function getDispatchView(isValid) {
			// alert(isValid);
			if(isValid)
			{
				var mode = "<?=$_REQUEST['mode']?>";
				if(mode!="edit")
				{
					$(".main-dipatch-details-body").html('<tr class="remove-this-before-save-click" style="background: #eee!important"><td colspan="4" class="text-right"><b>Grand Total</b></td><td class="text-center"><input readonly="" class="form-control mainChanged-Qty" value="0"></td><td class="text-right"><input readonly="" class="form-control" value="0"></td></tr>');
				}


				if(dispatch_id==null) {
					dispatch_id = 0;
				}

				if(dispatch_id!=0)
				{
					$("#packing_type").select2("destroy");
					if(mode!="edit")
					{
						//$("#add-another-carton").removeAttr("disabled","disabled");
						$("#packing_type option").removeAttr("disabled");
					}
					$("#packing_type").select2();
				}
				else
				{
					$("#packing_type").select2("destroy");
					$("#packing_type").val("");
					//$("#add-another-carton").attr("disabled","disabled");
					//$("#packing_type option[value!='']").attr("disabled","disabled");
					$("#packing_type").select2();
				}

				var packing_id = '<?= $_REQUEST['packing_id'] ?>';
				$.ajax({
					type: "post",
					url: "ajax_packing_slip_detail_from_dispatch.php",
					data: "dispatch_id=" + dispatch_id+"&packing_id="+packing_id,
					beforeSend: function() {
						$(".transCover").fadeIn(800);
						$('.preloader').fadeIn('slow');
						$(".invoice-item-data").html("");
					},
					success: function(result) {
						setTimeout(function() {
							$("#product_id").select2('val',"");
							getProduct(dispatch_id);
							$('.preloader').fadeOut('slow');
							$(".invoice-item-data").html(result);
						});
					}
				})
			}
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
				if(selected!=Dataselected && mode!="edit")
				{
					$(this).fSelect("destroy");
					$(this).val($.data(this, 'current'));
					$(this).fSelect("create");
				}
			}
			
			if(mode=="edit")
			{
				getDispatchView(true);
			}
		})

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

			if(totalPackingTobe!=totalPackingPacked)
			{
				toastr.error("Please make sure all item is packed per selected dispatch");
				isValid = false;
			}

			<?php if ($_REQUEST['mode'] == "add") {
			?>
				/*if ($("#customer_type").val() == "" || $("#customer_type").val().split(" ").join("") == "") {
					vd = aj.error('customer_type', "Please Select Customer Type", "add_error");
					isValid = false;
				}*/
				if ($("#customer_id").val() == "" || $("#customer_id").val().split(" ").join("") == "") {
					vd = aj.error('customer_id', "Please Select Customer.", "add_error");
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
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			GetPackingdata(packing_id);
		});

		function checkQtyValidation(qty)
		{
			var dispatch_qty = $("#product_id option:selected").data("dispatch_qty");
			var pro_weight = $("#product_id option:selected").data("pro-weight");
			var remainig_qty = $("#product_id option:selected").data("remainig_qty");
			final_qty=parseFloat(qty);
			pro_weight=parseFloat(pro_weight);

			if(isNaN(qty) || qty=="NaN")
			{
				qty = 0;
			}
			if (isNaN(pro_weight) || pro_weight=="NaN")
			{
				pro_weight = 0;
			}
			// var sum = parseFloat(qty) * (parseFloat(pro_weight)/1000);
			var sum = parseFloat(qty) * (parseFloat(pro_weight));
			sum=sum.toFixed(3);
			$("#weight").val(sum);

			if(qty>dispatch_qty || qty>remainig_qty)
			{
				toastr.error("You can't Enter More Than Dispatch Qty");
				$("#qty").val("");
				$("#weight").val("");
			}
		}

		$("#add_cartoon").on("click",function()
		{
			var packing_type=$("#packing_type").val();
			var product_id=$("#product_id").val();
			var qty=$("#qty").val();
			var weight=$("#weight").val();
			var cartoon_no=$("#cartoon_no").val();
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			//var dispatch_id = '<?= $_REQUEST['dispatch_id'] ?>';
			var dispatch_id = $("#dispatch_id").val();
			var product_name = $("#product_id option:selected").data("name");
			var weight_id = $("#product_id option:selected").data("weight_id");
			var StockQty = $("#product_id option:selected").data("stockqty");
			var customer_id = '<?=$customer_id?>';
			if(qty<=StockQty)
			{
				if(packing_type!="" && product_id!="" && qty!="" && weight!="")
				{
					$.ajax({
						type: "POST",
						url: "ajax_add_packingslip_data.php",
						data: {
							packing_type:packing_type,
							product_id:product_id,
							qty:qty,
							weight:weight,
							packing_id:packing_id,
							product_name:product_name,
							weight_id:weight_id,
							dispatch_id:dispatch_id,
							cartoon_no:cartoon_no,
							mode:"add_packing_slip",
						},
						cache: false,
						beforeSend: function() {
						},
						success: function(json)
						{
							json=$.parseJSON(json);
							msg=json.ack_msg;
							if(json.ack==1)
							{						
								toastr.success(msg,"Success!!");
								$("#product_id").select2('val',"");
								$("#qty").val(""); 
								// $("#packing_type").val(""); 
								$("#cartoon_no").val(""); 
								$("#weight").val(""); 
								//location.reload();
								getProduct(dispatch_id);
								getDispatchList(customer_id,dispatch_id);
								GetPackingdata(packing_id);
								$('#product_id').select2('open');	
							}
							else
							{
								toastr.error(msg, 'Error!!')
							}
						}
					})
				}
				else
				{
					toastr.error("Please Select Packing Type Product Qty AND weight");
				}	
			}
			else
			{
				toastr.error("You can't Add More Than Stock Qty...");
			}
		});
	</script>

	<script type="text/javascript">
		function del_conf(id)
		{
			var customer_id = '<?=$customer_id?>';
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			var r = confirm("Are you sure you want to delete?");
			if(r)
			{
				$.ajax({
					type: "POST",
					url: "ajax_add_packingslip_data.php",
					data: {
						id:id,
						mode:"delete_packing_Slip",
					},
					cache: false,
					beforeSend: function() {
						
					},
					success: function(json)
					{
						json=$.parseJSON(json);
						msg=json.ack_msg;
						if(json.ack==1)
						{						
							toastr.success(msg,"Success!!");
							//location.reload();
							getDispatchList(customer_id,dispatch_id);
							GetPackingdata(packing_id);
							getProduct(dispatch_id);
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					}
				});
			}
		}

		$("textarea").change(function(){
		    var remark = $("#remark").val();
		    var packing_id = '<?= $_REQUEST['packing_id'] ?>';
		    $.ajax({
				type: "POST",
				url: "ajax_add_packingslip_data.php",
				data: {
					remark:remark,
					packing_id:packing_id,
					mode:"add_packing_remark",
				},
				cache: false,
				beforeSend: function() {
					
				},
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						location.reload();
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		});

		function GetPackingdata(id)
		{
			$.ajax({
				type: "POST",
				url: "ajax_add_packingslip_data.php",
				data: {
					packing_id:id,
					mode:"get_packing_item",
				},
				cache: false,
				beforeSend: function() {
					
				},
				success: function(result)
				{
					$(".main-dipatch-details-body").html(result);
				}
			});
		}

		function getProduct()
		{
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			var dispatch_id = $("#dispatch_id").val();
			$.ajax({
				type: "POST",
				url: "ajax_add_packingslip_data.php",
				data: {
					dispatch_id: String(dispatch_id),
					packing_id: packing_id,
					mode:"get_packing_product",
				},
				cache: false,
				beforeSend: function() {
					
				},
				success: function(result)
				{
					$("#product_id").select2('val',"");
					$("#product_id").html(result);
				}
			});
		}

		function UpdateActualWeight(actualweight,packing_item_id)
		{
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			var dispatch_id = '<?= $_REQUEST['dispatch_id'] ?>';
			var customer_id = '<?=$customer_id?>';
			
			$.ajax({
				type: "POST",
				url: "ajax_add_packingslip_data.php",
				data: {
					packing_item_id:packing_item_id,
					actualweight:actualweight,
					mode:"update_actual_weight",
				},
				cache: false,
				beforeSend: function() {
					
				},
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						//location.reload();
						getDispatchList(customer_id,dispatch_id);
						GetPackingdata(packing_id);
						getProduct(dispatch_id);
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}

		function UpdateProductWeight(pro_weight,packing_item_id)
		{
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			var dispatch_id = '<?= $_REQUEST['dispatch_id'] ?>';
			var customer_id = '<?=$customer_id?>';
			
			$.ajax({
				type: "POST",
				url: "ajax_add_packingslip_data.php",
				data: {
					packing_item_id:packing_item_id,
					pro_weight:pro_weight,
					mode:"update_pro_weight",
				},
				cache: false, 
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						//location.reload();
						getDispatchList(customer_id,dispatch_id);
						GetPackingdata(packing_id);
						getProduct(dispatch_id);
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}

		function UpdateProductSizeCFT(sizecft,packing_item_id)
		{
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			var dispatch_id = '<?= $_REQUEST['dispatch_id'] ?>';
			var customer_id = '<?=$customer_id?>';
			
			$.ajax({
				type: "POST",
				url: "ajax_add_packingslip_data.php",
				data: {
					packing_item_id:packing_item_id,
					sizecft:sizecft,
					mode:"update_pro_size_cft",
				},
				cache: false,
				beforeSend: function() {
					
				},
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						//location.reload();
						getDispatchList(customer_id,dispatch_id);
						GetPackingdata(packing_id);
						getProduct(dispatch_id);
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
		function UpdateCFT(value_cft,packing_item_id)
		{
			var packing_id = '<?= $_REQUEST['packing_id'] ?>';
			var dispatch_id = '<?= $_REQUEST['dispatch_id'] ?>';
			var customer_id = '<?=$customer_id?>';
			
			$.ajax({
				type: "POST",
				url: "ajax_add_packingslip_data.php",
				data: {
					packing_item_id:packing_item_id,
					value_cft:value_cft,
					mode:"update_cft",
				},
				cache: false,
				beforeSend: function() {
					
				},
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						//location.reload();
						getDispatchList(customer_id,dispatch_id);
						GetPackingdata(packing_id);
						getProduct(dispatch_id);
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}

	</script>
</body>
</html>