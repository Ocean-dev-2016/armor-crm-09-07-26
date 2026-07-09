<?php
$page_id=565;$page_slug='page_order';
$page_slug = 'page_order';
$page_slug = "manage_inward_store";
$ctable 	= "orders";
$ctable1 	= "Orders";
$main_page 	= $ctable;
$page 		= "add_" . $ctable;
$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
include("connect.php");
include('../include/product.class.php');
include("../include/orders.class.php");
$objOrder     = new Order();
$uid          = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$product      = new Product();
$isActive     = 0;
$count        = "";
$total_qty    = "";
$total_amount = "";
$grand_total  = "";
$total        = "";
$flag         = "";
$remarks      = "";
$item_info    = array();
$flag         = $_REQUEST['flag'];
$order_no     = $db->getLastInsertId("orders");

$order_date = date("d-m-Y");

// $invoice_no=INVOICE_NO.str_pad($order_no."/20-21", 8, '0', STR_PAD_LEFT);
$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
if (isset($_REQUEST['submit'])) {
	
	$quotation_id = $db->rp_getValue('orders', 'quotation_id', "id=".$_REQUEST['id']." AND customer_id=".$_REQUEST['edit_customer_id'], 0);
	$isActive					= 1;
	$detail['isDelete']			= 0;
	$detail['remarks']			= $db->clean($_REQUEST['remarks']);
	$detail['terms_comdition']	= isset($_REQUEST['terms_comdition'])?trim($db->clean($_REQUEST['terms_comdition'])):"";
	$detail['faithfully']	      = isset($_REQUEST['faithfully'])?trim($db->clean($_REQUEST['faithfully'])):"";
	$detail['vendor_code']	      = isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
	$detail['tendor_code']	      = isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
	$detail['uid'] 				  = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
	$detail['cash_discount']	  = isset($_REQUEST['cash_discount'])?$db->clean($_REQUEST['cash_discount']):"";
	$detail['additional_discount']	= isset($_REQUEST['additional_discount'])?$db->clean($_REQUEST['additional_discount']):"";
	$detail['cash_discount_amount']	= isset($_REQUEST['cash_discount_amount'])?$db->clean($_REQUEST['cash_discount_amount']):"";
	$detail['additional_discount_amount']	= isset($_REQUEST['addtional_discount_amount'])?$db->clean($_REQUEST['addtional_discount_amount']):"";
	$detail['tcs_apply_flag']	= isset($_REQUEST['tcs_apply_flag'])?$db->clean($_REQUEST['tcs_apply_flag']):"";

	//Insert Orders item
	// var_dump($_REQUEST);exit;
	$product_id      = $_REQUEST['product_id'];
	$qty             = $_REQUEST['qty'];
	$original_price  = $_REQUEST['original_price'];
	$price           = $_REQUEST['price'];
	$pro_name        = $_REQUEST['pro_name'];
	$weight_id       = $_REQUEST['weight_id'];
	$box_qty         = $_REQUEST['box_qty'];
	$bag             = $_REQUEST['bag'];
	$discount        = $_REQUEST['discount'];
	$brand_id        = $_REQUEST['brand_id'];
	$pro_description = $_REQUEST['pro_description'];
	$item_gst        = $_REQUEST['item_gst'];


	$size[] = sizeof($product_id);
	$size[] = sizeof($qty);
	$size[] = sizeof($price);
	$size[] = sizeof($pro_name);
	$size[] = sizeof($pro_description);

	$brand_id[] = sizeof($brand_id);

	$weight_id[] = sizeof($weight_id);
	$box_qty[] = sizeof($box_qty);
	$value_check = sizeof($product_id);


	if (in_array($value_check, $size)) {
		$isValidArray = true;
	} else {
		$isValidArray = false;
	}
	// print_r($product_id);exit;
	if ($isValidArray && !empty($product_id)) {
		for ($i = 0; $i < sizeof($product_id); $i++) {
			$item[] = array("qty" => $qty[$i], "pid" => $product_id[$i], "original_price" => $original_price[$i], "price" => $price[$i], "pro_name" => $pro_name[$i], "weight_id" => $weight_id[$i], "cartoon_qty" => 0, "box_qty" => 0, "brand_id" => $brand_id[$i] , "pro_description" =>$pro_description[$i],"item_gst"=>$item_gst[$i],"discount"=>$discount[$i]);
		}
	}
	/*check for duplicate */
	/*$new_array= array();
	foreach ($item as $p)
	{
		$new_array['pid']=$p['pid'];
		$new_array['weight_id']=$p['weight_id'];
		$new_array['pro_description']=$p['pro_description'];
		$new_array['pro_name']=$p['pro_name'];
	}

	$target=array();
	foreach ($item as $p1)
	{
		$target['pid']=$p1['pid'];
		$target['weight_id']=$p1['weight_id'];
		$target['pro_description']=$p1['pro_description'];
		$target['pro_name']=$p1['pro_name'];
	}
	print_r(array_intersect($item, $target));exit();
	if(count(array_intersect($new_array, $target)) == count($target))
	{
		$db->addErrorMessage("Something went wrong.");
		$db->rp_location("orders_crud.php?mode=add");
	}
	else
	{
		echo "11";
	}
	exit;*/
	/*check for duplicate */

	/*print_r($item); exit;*/
	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add") {
		$detail['cid']                = $db->clean($_REQUEST['customer_id']);
		$detail['customer_id']        = $db->clean($_REQUEST['customer_id']);
		$detail['sales_executive_id'] = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
		$detail['order_date']         = date('Y-m-d', strtotime($_REQUEST['order_date']));
		/*$detail['phone']	= $db->clean($_REQUEST['name_phone']);
		$detail['email']	= $db->clean($_REQUEST['name_email']);
		$detail['address']	= $db->clean($_REQUEST['name_address']);
		$detail['customer_po_no']	= $db->clean($_REQUEST['customer_po_no']);
		$detail['booking']	= isset($_REQUEST['booking'])?$db->clean($_REQUEST['booking']):"";
		$detail['transport']	= isset($_REQUEST['transport'])?$db->clean($_REQUEST['transport']):"";*/
		$detail['order_no']	= isset($_REQUEST['order_no']) ? $db->clean($_REQUEST['order_no']) : "";
		$detail['brand_id']	= isset($_REQUEST['brand_id']) ? $db->clean($_REQUEST['brand_id']) : "";


		$detail['chalan_no']	= isset($_REQUEST['chalan_no']) ? $db->clean($_REQUEST['chalan_no']) : "";
		$detail['po_no']	= isset($_REQUEST['po_no']) ? $db->clean($_REQUEST['po_no']) : "";
		$detail['po_date']	= date('Y-m-d', strtotime($_REQUEST['po_date']));

		$detail['terms_comdition']	  = isset($_REQUEST['terms_comdition'])?trim($_REQUEST['terms_comdition']):"";
		$detail['faithfully']	      = isset($_REQUEST['faithfully'])?trim($_REQUEST['faithfully']):"";

		$detail['transport_name']	  = isset($_REQUEST['transport_name'])?trim($_REQUEST['transport_name']):"";
		$detail['transport_through']  = isset($_REQUEST['transport_through'])?trim($_REQUEST['transport_through']):"";
		$detail['transport_charge']	  = isset($_REQUEST['transport_charge'])?trim($_REQUEST['transport_charge']):"";
		$detail['packing_charge']	  = isset($_REQUEST['packing_charge'])?$db->clean($_REQUEST['packing_charge']):"";
		$detail['shipping_address']	  = isset($_REQUEST['shipping_address'])?trim($_REQUEST['shipping_address']):"";
		$detail['billing_address']	  = isset($_REQUEST['billing_address'])?trim($_REQUEST['billing_address']):"";
		$detail['name_gstin']	  = isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";

		if ($rights['insert_flag'] != 1) {
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		// print_r($detail);exit;


		// $reply=$objOrder->InsertOrder($detail,$item);
		$reply = $objOrder->AddToCart($detail, $item);
		if ($reply['ack'] == 1) {
			// $db->rp_update('quotation_detail', ['status' => 4], );
			unset($detail['order_date']);
			$detail['cash_discount_flag'] = 0;
			$detail['order_id'] = $reply['order_id'];
			$detail['sales_executive_id'] = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
			$detail['name_gstin']	  = isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";
			$detail['vendor_code']	      = isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
			$detail['tendor_code']	      = isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
			$detail['cash_discount']	= isset($_REQUEST['cash_discount'])?$db->clean($_REQUEST['cash_discount']):"";
			$detail['additional_discount']	= isset($_REQUEST['additional_discount'])?$db->clean($_REQUEST['additional_discount']):"";
			$detail['cash_discount_amount']	= isset($_REQUEST['cash_discount_amount'])?$db->clean($_REQUEST['cash_discount_amount']):"";
			$detail['additional_discount_amount']	= isset($_REQUEST['addtional_discount_amount'])?$db->clean($_REQUEST['addtional_discount_amount']):"";
			$detail['tcs_apply_flag']	= isset($_REQUEST['tcs_apply_flag'])?$db->clean($_REQUEST['tcs_apply_flag']):"";

			$objOrder->PlaceOrderPanel($detail);
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("dealer_orders_manage.php");
			//for redirect to location after insert
			$db->rp_location("dealer_orders_manage.php?msg=inserted");
		} else {
			$db->addErrorMessage($reply['ack_msg']);
		}
	} else if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "edit") {
		if ($rights['update_flag'] != 1) {
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		// $reply=$objOrder->UpdateProductItem($detail,$item);
		$detail['customer_id']        = $db->clean($_REQUEST['edit_customer_id']);
		$detail['sales_executive_id'] = $_SESSION[SITE_SESS . 'REFERANCE_ID'];
		$detail['cid']                = $db->clean($_REQUEST['edit_customer_id']);
		$detail['order_id']           = $db->clean($_REQUEST['id']);
		
		$detail['chalan_no']          = isset($_REQUEST['chalan_no'])?($_REQUEST['chalan_no']):"";
		$detail['po_no']           	  = isset($_REQUEST['po_no'])?($_REQUEST['po_no']):"";
		$detail['po_date']            = $db->clean($_REQUEST['po_date']);
		$detail['terms_comdition']    = $db->clean($_REQUEST['terms_comdition']);
		$detail['faithfully']         = $db->clean($_REQUEST['faithfully']);
		$detail['transport_name']	  = isset($_REQUEST['transport_name'])?trim($_REQUEST['transport_name']):"";
		$detail['transport_through']  = isset($_REQUEST['transport_through'])?trim($_REQUEST['transport_through']):"";
		$detail['transport_charge']   = isset($_REQUEST['transport_charge'])?trim($_REQUEST['transport_charge']):"";
		$detail['packing_charge']	  = isset($_REQUEST['packing_charge'])?$db->clean($_REQUEST['packing_charge']):"";
		$detail['shipping_address']	  = isset($_REQUEST['shipping_address'])?trim($_REQUEST['shipping_address']):"";
		$detail['billing_address']	  = isset($_REQUEST['billing_address'])?trim($_REQUEST['billing_address']):"";
		$detail['name_gstin']	      = isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";
		$detail['vendor_code']	      = isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
		$detail['tendor_code']	      = isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
		// $detail['chalan_no']	= isset($_REQUEST['chalan_no']) ? $db->clean($_REQUEST['chalan_no']) : "";
		// $detail['po_no']	= isset($_REQUEST['po_no']) ? $db->clean($_REQUEST['po_no']) : "";
		// $detail['po_date']	= date('Y-m-d', strtotime($_REQUEST['po_date']));

		// $detail['terms_comdition']	= isset($_REQUEST['terms_comdition']) ? $db->clean($_REQUEST['terms_comdition']) : "";
		// $detail['faithfully']	= isset($_REQUEST['faithfully']) ? $db->clean($_REQUEST['faithfully']) : "";



		// $check_order = $db->rp_getTotalRecord('orders', "customer_id=".$detail['customer_id'], 0);

		// if($check_order == 0) {
		// 	$db->rp_update('executive', ['order_customer_flag' => 2], "id=". $detail['customer_id'], $die=0);
		// }
		
		$reply = $objOrder->UpdateOrder($detail, $item);
		// print_r($reply);exit;
		if ($reply['ack'] == 1) {
			$db->rp_update('quotation_detail', ['status' => 4], "id=".$quotation_id, $die=0);
			unset($detail['order_date']);
			$detail['cash_discount_flag'] = 0;
			$detail['order_id'] = $reply['order_id'];
			$detail['sales_executive_id'] = "";
			$objOrder->PlaceOrderPanel($detail);
			$db->addSuccessMessage($reply['ack_msg']);
			$type = $db->rp_getValue("orders", "customer_type", "id='" . $reply['type'] . "'");
			$db->rp_location("dealer_orders_manage.php");
		} else {
			$db->addErrorMessage($reply['ack_msg']);
			$db->rp_location("orders_crud.php?mode=edit&id=".$_REQUEST['id']);
		}
	}
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "edit") {
	/*$chck_otp_verify = $db->rp_getValue("orders","print_flag","id=".$_REQUEST['id']." AND isDelete=0",0);
	if($chck_otp_verify==1)
	{*/
	$generate_invoice_flag = $db->rp_getValue("orders", "generate_invoice_flag", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);

	if ($rights['update_flag'] != 1 || $generate_invoice_flag == 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id'] = $_REQUEST['id'];
	$OrderNo = $db->rp_getValue("orders", "order_no", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	$Order = $db->rp_getValue("orders", "customer_id", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	$customer_name = $db->rp_getValue("customer", "company_name", "id=" . $Order . " AND isDelete=0", 0);
	$page_title = ucwords($_REQUEST['mode']) . '&nbsp' . "Order" . "- " . ucwords($OrderNo) . '&nbsp';
	$reply = $objOrder->GetOrder($detail);
	$item_info = $objOrder->GetOrderItems($detail);

	if ($reply['ack'] == 1) {
		
		$id = $_REQUEST['id'];
		$result = $reply['result'];
		// echo "<pre>";
		// print_r($result); exit;
		extract($result);
		$invoice_date = date("d.m.Y", strtotime($invoice_date));
	}
	if ($item_info['ack'] == 1) {
		$store_inward_id = $_REQUEST['id'];
		$item_info = $item_info['result'];
	} else {
		$item_info = array();
	}
	/*}
	else
	{
		echo '<script>alert("Please First Verify Otp")</script>';
		echo '<script>close();</script>';
	}*/
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "delete") {
	if ($rights['delete_flag'] != 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id'] = $_REQUEST['id'];
	$reply = $objOrder->DeleteOrder($detail);
	if ($reply['ack'] == 1) {
		$db->addSuccessMessage($reply['ack_msg']);
		$type = $db->rp_getValue("orders", "customer_type", "id='" . $reply['type'] . "'");
		$quotation_id = $db->rp_getValue("orders", "quotation_id", "id='" . $_REQUEST['id'] . "'");
		if ($quotation_id != "" && $quotation_id != 0) {
			$update = $db->rp_update("quotation_detail", array("status" => 0), "id='" . $quotation_id . "'");
		}
		//for redirect to location after Delete
		$db->rp_location("dealer_orders_manage.php?msg=deleted");
	} else {
		$db->addErrorMessage($reply['ack_msg']);
	}
}
if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "isActive" && isset($_REQUEST['status'])  && $_REQUEST['status'] != "") {
	$status = $_REQUEST['status'];
	$rows 	= array(
		"isActive"	=> $status
	);
	$where	= "id='" . $_REQUEST['id'] . "'";
	$db->rp_update($ctable, $rows, $where);
	$db->rp_location("customer_orders_manage.php?msg=updated");
}
/*if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
		"isActive"	=> $status
	);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location("customer_orders_manage.php?msg=updated");
}*/
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8" />
	<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
	<?php include("include_css.php"); ?>
	<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css" />
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
					/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
						{
							$back="orders_manage.php";
						}*/
					/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 && $flag==2)
						{
							$back="ss_orders_manage.php";
						}
						else if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 && $flag==3)
						{
							$back="dealer_orders_manage.php";
						}
						else
						{
							if($type=='dealer' && $flag==3)
							{
								$back='dealer_orders_manage.php';
							}
							else if($type=='super_stockist' && $flag==2)
							{
								$back='ss_orders_manage.php';
							}
							else
							{
								$back='orders_manage.php';
							}
						}*/
					$back = 'dealer_orders_manage.php';
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
														<span class="caption-subject bold uppercase"> ORDER DETAIL</span>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-body">
														<div class="form-group">
															<div class="row">

																<div class="col-md-4 col-sm-4">
																	<div class="form-group">
																		<?php
																		if ($_REQUEST['mode'] == "edit") {
																			$disabled = "disabled";
																		} else {
																			$disabled = "";
																		}
																		?>
																		<label>Select Customer Type<code>*</code></label>
																		<select class="form-control" <?php echo $disabled; ?> id="customer_type" name="customer_type" onchange="getCustomer(this.value)">
																			<option value="">Select Customer Type</option>
																			<?php
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
																		<p class="help-block"></p>
																	</div>
																	<div class="form-group">
																		<label>Select Customer<code>*</code></label>
																		<?php
																		if ($_REQUEST['mode'] == "edit") {
																		?>
																			<input type="hidden" name="edit_customer_id" id="edit_customer_id" value="<?= $customer_id; ?>">
																		<?php
																		}
																		?>
																		<select class="form-control" name="customer_id" placeholder="Select Customer" id="customer_id" onchange="getProductList(this.value)" type="text" <?php echo $disabled; ?>>
																			<option value="">Select Customer</option>
																			<?php
																			if ($_REQUEST['mode'] == "edit") {

																				$customers = $db->rp_getData("executive", "*", "isDelete=0");
																				if ($customers) {
																					while ($customer = mysqli_fetch_assoc($customers)) {
																						if ($customer['price_list_id'] != 0) 
																						{
																							$price_list_name = $db->rp_getValue("price_list", "pricelist_name", "id='" . $customer['price_list_id'] . "'");
																						} 
																						else 
																						{
																							$price_list_name = "N/A";
																						}

																						/*for merchnt export*/
																						if($customer['type_of_executive']==7)
																				        {
																				           if(strtolower(CLIENT_STATE)==strtolower($customer['state']))
																				            {
																				                $gst_type="(CGST:0.05%,SGST:0.05%)";
																				            }
																				            else
																				            {
																				                $gst_type="(IGST:0.1%)";
																				            } 
																				        }
																				        /*for merchnt export*/
																				        else
																				        {
																							if (strtolower(CLIENT_STATE) == strtolower($customer['state'])) {
																								$gst_type = "(CGST:9%,SGST:9%)";
																							} else {
																								$gst_type = "(IGST:18%)";
																							}
																						}
																						$customer_type1 = $db->rp_getValue("customer_type", "name", "id='" . $customer['type_of_executive'] . "'");

																						?>
																						<option value="<?php echo $customer['id']; ?>" <?php if ($customer_id == $customer['id']) {echo "selected";
																							} ?> data-phone="<?php echo $customer['phone'] ?>" data-email="<?php echo $customer['email'] ?>" data-address="<?php echo $customer['address'] ?>" data-state="<?php echo $customer['state'] ?>" data-cname="<?= $customer['cname'] ?>" data-gstin="<?= $customer['gst'] ?>" data-price-list="<?= $price_list_name; ?>" data-cutomer-type="<?= $customer_type1; ?>" data-gst-type="<?= $gst_type ?>"><?php echo $customer['company_name']." - ".$customer['cname']; ?></option>
																			<?php
																					}
																				}
																			}
																			?>
																		</select>
																		<p class="help-block"></p>
																	</div>
																	<!-- CUSTOMER EDIT BUTTON -->
																	<?php
																	if($_REQUEST['mode']=="edit")
																	{
																		?>
																		<div class="form-group" style="text-align: right;margin-right: 64px;">
																			<?php
																			$Check_Dispatch = $db->rp_getTotalRecord("dispatch_detail","order_id='".$_REQUEST['id']."' AND isDelete=0");
																			if($Check_Dispatch==0)
																			{
																				?>
																				<a class="btn btn-primary" href='#CustomerChangeModel' data-title="Customer Details" data-customer_id="<?= $customer_id; ?>" data-mode="order" data-quotation_id="<?= $_REQUEST['id']; ?>"data-toggle='modal'><i class="fa fa-pencil"></i> Change Customer</a>
																				<?php
																			}
																			?>
																			<a class="btn btn-success" href='#CustomerEditModel' data-title="Customer Details" data-customer_id="<?= $customer_id; ?>" data-mode="order" data-quotation_id="<?= $_REQUEST['id']; ?>"data-toggle='modal'><i class="fa fa-edit"></i> Edit Customer</a>
																		</div>
																		<?php
																	}
																	?>
																	<!-- CUSTOMER EDIT BUTTON -->
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
																			<!-- <div class="col-md-7 value" name="name_gstin" id="name_gstin"><?php echo $customer_gstin; ?></div> -->
																			<div class="col-md-7">
																				<input class="form-control" type="text" name="name_gstin" id="name_gstin" value="<?php echo $name_gstin ?>">
																				<p class="help-block"></p>
																			</div>
																		</div>
																		<div class="row static-info address">
																			<div class="col-md-5 name"> Pricelist : </div>
																			<div class="col-md-7 value" name="name_pricelist" id="name_pricelist"><?php echo $customer_pricelist; ?></div>
																		</div>
																	</div>
																	<!-- <div class="row static-info customer_type">
																			<div class="col-md-5 name"> Type of Customer : </div>
																			<div class="col-md-7 value" name="customer_type" id="customer_type"><?php echo $customer_type; ?></div>
																		</div> -->

																										<!-- <div class="row static-info brand">
																			<div class="col-md-5 name"> Brand : </div>
																			<div class="col-md-7 value" id="brand_name"><?php echo $brand; ?></div>
																		</div> -->
																</div>
																<div class="col-md-4 col-sm-4">
																	<div class="form-group">
																		<label>Shipping Address<code>*</code></label>
																		<textarea class="form-control" id="shipping_address" name="shipping_address" value="<?php $shipping_address ?>" rows="6"><?php echo $shipping_address ?></textarea>
																		<p class="help-block"></p>
																	</div>
																	<div class="form-group">
																		<label>Billing Address<code>*</code></label>
																		<textarea class="form-control" id="billing_address" name="billing_address" value="<?php $billing_address ?>" rows="6"><?php echo $billing_address ?></textarea>
																		<p class="help-block"></p>
																	</div>
																	<!-- <div class="row static-info phone">
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
																	</div> -->
																</div>
																<div class="col-md-4 col-sm-4">
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Order No. <code>*</code></label>
																			<input type="text" readonly="" class="form-control" name="order_no" id="order_no" value="<?php echo $order_no; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Order Date <code>*</code></label>
																			<input type="text" readonly="" class="form-control" name="order_date" id="order_date" value="<?php echo $order_date; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>

																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Challan No.</label>
																			<input type="text" class="form-control" name="chalan_no" id="chalan_no" value="<?php echo $chalan_no; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>PO No. <code>*</code></label>
																			<input type="text" class="form-control" name="po_no" id="po_no" value="<?php echo $po_no; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>PO Date <code>*</code></label>
																			<input type="text" readonly="" class="form-control" name="po_date" id="po_date" value="<?php if($_REQUEST['mode']=="add"){ echo date('d-m-Y');} else{ echo $po_date;}?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Vendor Code</label>
																			<input style="resize: vertical;" type="text" class="form-control" name="vendor_code" id="vendor_code" value="<?php echo $vendor_code ?>">
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Tendor Code</label>
																			<input style="resize: vertical;" type="text" class="form-control" name="tendor_code" id="tendor_code" value="<?php echo $tendor_code ?>">
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Transport through <code>*</code></label>
																			<!-- update code - sagar  -->
																			<input type="hidden" id="transport_name_selected_id" value="<?=$transport_name ?>">
																			<!-- update code - sagar  -->
																			<select class="form-control" name="transport_through" id="transport_through" onchange="getTransportname(this.value);">
						                                                    	<option value="">Select Transport By</option>
						                                                        <?php
						                                                        $transport_by_r = $db->rp_getData("transport_by","*","isDelete=0");
						                                                        if(mysqli_num_rows($transport_by_r)>0)
						                                                        {
						                                                            while($transport_by_d = mysqli_fetch_array($transport_by_r))
						                                                            {
						                                                                ?>
						                                                                <option value="<?php echo $transport_by_d['id']; ?>" <?php if($transport_by_d['id']==$transport_through){?> selected <?php } ?>><?php echo $transport_by_d['name']; ?></option>
						                                                                <?php
						                                                            }
						                                                        }
						                                                        ?>
						                                                    </select>
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Transport Name <code>*</code></label>
																			<select class="form-control" name="transport_name" id="transport_name">
																				<option value="">Select Transport Name</option>
																			</select>
																			<p class="help-block"></p>
																		</div>
																	</div>
																	
																	<!-- <div class="col-md-6">
																		<div class="form-group">
																			<label>Shipping Address<code>*</code></label>
																			<textarea class="form-control" id="shipping_address" name="shipping_address" value="<?php $shipping_address ?>"><?php echo $shipping_address ?></textarea>
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Billing Address<code>*</code></label>
																			<textarea class="form-control" id="billing_address" name="billing_address" value="<?php $billing_address ?>"><?php echo $billing_address ?></textarea>
																			<p class="help-block"></p>
																		</div>
																	</div> -->
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
														<span class="caption-subject bold uppercase"> ORDER ITEM</span>
													</div>
												</div>
												<div class="portlet-body">
													<div class="row">
														<div class="col-sm-12">
															<div class="row">
																<div class="col-md-12">
																	<div class="form-body">
																		<div class="form-group">
																			<div class="row">
																				<div class="col-md-12">
																					<div class="row">
																						<!-- <div class="col-md-4">
																							<div class="form-group">
																								<label>Brand <code>*</code></label>
																								<select class="form-control" name="brand_id" id="brand_id">
																									<option value="">select Brand</option>
																									<?php
																									$get_bard_r=$db->rp_getData("brand","*","isDelete=0 AND isActive=1",1);
																									while($get_bard_d=mysqli_fetch_assoc($get_bard_r))
																									{
																									?>
																									<option value="<?= $get_bard_d['id'] ?>"><?= $get_bard_d['name'] ?></option>
																									<?php
																									}
																									?>
																								</select>
																								<p class="help-block"></p>
																							</div>
																						</div> -->
																						<div class="col-md-4">
																							<div class="form-group">
																								<label>Products <code>*</code></label>
																								<select class="form-control" name="product_id" id="product_id">
																									<option value="">Select Product</option>
																								</select>
																								<p class="help-block"></p>
																							</div>
																						</div>
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>Quantity<code>*</code></label>
																								<input type="text" class="form-control positive" name="qty" id="qty" value="" />
																								<p class="help-block"></p>
																							</div>
																						</div>
																						<div class="col-md-1">
																							<div class="form-group">
																								<br /><button class="btn btn-primary" type="button" id="add">ADD</button>
																								<p class="help-block"></p>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="row">
																				<div class="col-md-12">
																					<table border="1px" id="datatable_1" class="table table-striped table-bordered table-hover">
																						<thead>
																							<tr>
																								<!-- <th>No.</th> -->
																								<th class="text-center" width="200px;">Product Name</th>
																								<th class="text-center" width="50px;">Unit</th>
																								<th class="text-center">Stock</th>
																								<th class="" width="100px;">Product Description</th>
																								<th class="text-center" width="50px;">HSN Code</th>
																								<!-- <th  class="text-center">Avail Stock</th>
																								<th  class="text-center">Balance</th> -->
																								<!-- <th  class="text-center">Bag<br/><span class="f-10">(inner in NOS)</span> </th>
																								<th  class="text-center">Box<br/><span class="f-10">(outer in NOS)</span></th> -->
																								<th class="text-center">Qty</th>
																								<th class="text-center">Price<br />(in INR)</th>
																								<th class="text-center">Dis(%)</th>
																								<th class="text-center">GST(%)</th>
																								<th class="text-center">Net Rate<br />(in INR)</th>
																								<th class="text-center" width="150px;">Total</th>
																								<th class="text-center">Delete</th>
																							</tr>
																						</thead>
																						<tbody>
																							<?php
																							if (!empty($item_info)) {
																								$total_amount = 0;
																								$total_gst = $igst_amount;
																								foreach ($item_info as $i) {
																									//print_r($i); exit;
																									$box_qty += $i['box_qty'];
																									$qty_total += $i['qty'];
																									//echo $i['product_total'];
																									$total_amount += $i['product_total'];
																									$discount_amount = $i['discount_amount'];
																									$final_price = $total_amount;
																									$GST = $db->rp_getValue("product", "igst", "id='" . $i['product_id'] . "'", 0);

																									$total1 = $total_amount - $cash_discount_amount;
																									$total2 = $total1 - $additional_discount_amount;
																									$total = $total2 + $transport_charge + $packing_charge;;

																									
																									$unit_id = $db->rp_getValue("product","unit_id","id='". $i['product_id']."'");
																									$unit_name = $db->rp_getValue("unit","name","id='". $unit_id."'");
																									$total_discount = $i['gst_amount'];

																							?>
																									<tr>
																										<!-- <td><?php echo ++$count; ?><input type='hidden' name='count[]' value="<?php echo $count; ?>" class='count'></td> -->
																										<td style="width: 300px;text-align: center;">
																											<input type='hidden' name='product_id[]' class='product_id' value="<?php echo $i['product_id']; ?>">
																											<input class="pro_id" type='hidden' name='pro_id[]' value="<?php echo $i['product_id'] . "" . $i['weight_id']; ?>">


																											<input type='hidden' style="text-align:right" name='subtotal[]' value="">
																											<input type='hidden' style="text-align:right" name='total[]' value="">
																											<input type='hidden' style="text-align:right" name='item_name[]'>
																											<input type='hidden' name='pro_name[]' value="<?php echo $i['product_name']; ?>" id='pro_name'>
																											<input type='hidden' name='weight_id[]' value="<?php echo $i['weight_id']; ?>" id='weight_id'>
																											<?php echo $i['product_name']." - ".$i['cat_no']."- <br/>".$i['top_cat_name']." - ".$i['category_name']; ?>
																										</td>

																										<td><?= $unit_name ?></td>

																										<td class='text-center'><?php echo $i['stock']; ?></td>

																										<td>
																											<textarea rows="4" cols="10" id="pro_description" class="pro_desc" name="pro_description[]" style="margin: 0!important;"><?=$i['pro_description']?></textarea>
																										</td>

																										<td class='text-center'><?php echo $i['hsn_code']; ?></td>

																										
																										<td style="text-align:right">
																											<input type='text' style="text-align:right;width: 70px;" onChange='recalculateRow(this)' class='qty form-control' name='qty[]' value="<?php echo $i['qty']; ?>">
																										</td>

																										<td style="text-align:right">
																											<input name='original_price[]' class='original_price form-control' style="text-align:right;width: 70px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['original_price']; ?>">
																											<?php $i['original_price']; ?>
																										</td>

																										<td>
																											<input style="text-align:right;width: 70px;" type="text" name='discount[]' onChange='recalculateRow(this)' class="discount form-control" value="<?php echo $i['discount_per']; ?>">
																											<?php $i['discount_per']; ?>
																										</td>

																										<td style="text-align:right">
																											<input type="hidden" class="item_gst_amount" value="<?php echo $total_discount ?>">
																											<input type="text" style="text-align: right;width: 70px;" name="item_gst[]" class="form-control item_gst" onChange='recalculateRow(this)' value="<?php echo $i['item_gst']; ?>">
																										</td>

																										<td style="text-align:right">
																											<input name='price[]' class='price form-control' style="text-align:right;width: 70px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['product_price']; ?>">
																											<input class='old_price form-control' style="text-align:right;" type='hidden' value="<?php echo $i['product_price']; ?>">
																											<?php $i['product_price']; ?>
																										</td>


																										<td style="text-align:right">
																											<input type='text' style="text-align:right;" disabled class='total form-control' disabled onChange='recalculateRow(this)' name='subtotal[]' value="<?php echo $i['product_total']; ?>">
																										</td>
																										<td class="text-center">
																											<?php
																											$total_dispatch_record = $db->rp_getTotalRecord("dispatch_map_order", "order_id='" . $i['order_id'] . "' AND isDelete=0", 0);
																											if ($total_dispatch_record > 0) {
																											} else {
																											?>
																												<a class='delete btn btn-danger btn-sm' title='Delete'><i class='fa fa-times'></i></a>
																											<?php
																											}
																											?>
																										</td>
																									</tr>
																							<?php
																								}
																							}
																							?>
																							<!-- </thead>
																						<tbody> -->
																						</tbody>
																						<tfoot>
																							<tr>
																								<td colspan="5" align="right">Total</td>

																								<td style="text-align: right;">
																									<input type="text" style="text-align: right;" id="sum_qty" class="form-control" disabled value="<?php echo $qty_total; ?>">
																								</td>
																								<td colspan="4"></td>
																								<td style="text-align:right">
																									<input type='text' style="text-align:right;" id='finalTotal' align="right" class="form-control" disabled value="<?php echo $db->rp_number_format($total_amount); ?>" name='finalTotal[]'>
																								</td>


																							</tr>
																							<tr>
																								<td colspan="9" align="right">Cash Discount (%)</td>
																								<td style="text-align: right;">
																									<input type="text" style="text-align: right;" id="cash_discount" name="cash_discount"  onChange='recalculateRow(this)' class="form-control" value="<?php echo $cash_discount; ?>">
																								</td>
																								<td style="text-align: right;">
																									<input type="text" style="text-align: right;" id="cash_discount_amount" onChange='recalculateRow(this)' name="cash_discount_amount" class="form-control" value="<?php echo $cash_discount_amount; ?>">
																								</td>
																							</tr>
																							<tr>
																								<td colspan="9" align="right">Additonal Discount (%)</td>

																								<td style="text-align: right;">
																									<input type="text" style="text-align: right;" id="additional_discount" name="additional_discount"  onChange='recalculateRow(this)' class="form-control" value="<?php echo $additional_discount; ?>">
																								</td>
																								<td style="text-align: right;">
																									<input type="text" style="text-align: right;" id="addtional_discount_amount" onChange='recalculateRow(this)' name="addtional_discount_amount" class="form-control" value="<?php echo $additional_discount_amount; ?>">
																								</td>
																							</tr>
																							<tr>
																								<td colspan="10" align="right">Packing & Forwarding Charge</td>

																								<td style="text-align: right;">
																									<input type="text" style="text-align: right;" id="packing_charge" name="packing_charge"  onChange='recalculateRow(this)' class="form-control" value="<?php echo $packing_charge; ?>">
																								</td>
																							</tr>
																							<tr>
																								<td colspan="10" align="right">Transport</td>
																								<td style="text-align:right">
																									<input type="text" style="text-align: right;" class="form-control" name="transport_charge" id="transport_charge" onchange="recalculateRow(this)" value="<?php echo $transport_charge; ?>" />
																								</td>
																							</tr>
																							<tr>
																								<td colspan="10" align="right">Total</td>

																								<td style="text-align: right;">
																									<input type="text" disabled style="text-align: right;" id="total"  class="form-control" value="<?php echo $db->rp_number_format($total); ?>">
																								</td>
																							</tr>

																							<tr>
																								<td colspan='9' align="right">GST <span class="gst_type"></span><br/><b><span class='gst-amount-display'><?=$db->rp_number_format($gst_amount,2)?></span></b></td>
																								<td align="right">GST On Off Switch
																								
																								<input type="checkbox" 
																								<?php
																								if($igst_amount==0)
																								{
																									echo "";
																									$gst_apply_flag = 0;
																								} 
																								else
																								{
																									echo "checked";
																									$gst_apply_flag = 1;
																								}
																								?>
																								name="gst_apply" id="gst_apply">
																								<input type="hidden" name="gst_apply" id="gst_apply" value="<?= $gst_apply?>">
																								<input type="hidden" name="gst_apply_flag" id="gst_apply_flag" value="<?= $gst_apply_flag?>">
																								</td>
																								<td style="text-align:right">
																									<input readonly type='text' class="form-control gst_put" style="text-align:right;width:150px;" name='gst_amount' id='gst_amount' value="">
																								</td>
																								<td></td>
																							</tr>

																							<tr>
																								<td colspan='10' align="right">TCS On Off Switch
																								<input type="checkbox" 
																								<?php
																								if($tcs_amount==0)
																								{
																									echo "";
																									$tcs_apply_flag = 0;
																								} 
																								else
																								{
																									echo "checked";
																									$tcs_apply_flag = 1;
																								}
																								?>
																								name="tcs_apply" id="tcs_apply">
																								<input type="hidden" name="tcs_apply" id="tcs_apply" value="<?= $tcs_apply?>">
																								<input type="hidden" name="tcs_apply_flag" id="tcs_apply_flag" value="<?= $tcs_apply_flag?>">
																								</td>
																								
																								<td style="text-align:right">
																									<input readonly type='text' class="form-control tcs_put" style="text-align:right;width:150px;" name='tcs_amount' id='tcs_amount' value="<?php echo $tcs_amount; ?>">
																								</td>
																								<td></td>
																							</tr>

																							<?php



																							$whole = floor($total_gst);      // 1
																							$fraction = $total_gst - $whole;
																							$f1 =  $db->rp_number_format((float)$fraction, 2, '.', '');
																							?>
																							<tr>
																								<td colspan='10' align="right">Round Off</td>
																								<td style="text-align:right">
																									<input id="round_off" type='text' style="text-align:right;width:150px;" class="form-control" disabled value="<?php echo $db->rp_number_format($f1); ?>">
																								</td>
																								<td></td>
																							</tr>
																							<tr>
																								<td colspan='10' align="right"> Grand Total </td>
																								<?php
																								$gt = $total_gst - $f1;
																								
																								$gt = round($total_gst);
																								// $gt = round($total_gst+$transport_charge);
																								?>
																								<td style="text-align:right">
																									<input type='hidden' style="text-align:right" class="form-control" id='finalQty' disabled name='finalQty[]' value="<?php echo $total_qty; ?>">
																									<input type='text' style="text-align:right;width:150px;" id='finalgrandTotal' class="form-control" disabled value="<?php echo $db->rp_number_format($gt); ?>" name='finalgrandTotal[]'>
																								</td>
																								<td></td>
																							</tr>
																						</tfoot>
																					</table>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
															<div class="col-md-5">
																<div class="form-group">
																	<label>Terms & Conditions<code>*</code></label>
																	<textarea class="form-control" id="terms_comdition" rows="5" name="terms_comdition" style="resize: vertical;"><?php if($_REQUEST['mode']=="add"){ echo $db->custom_html_entity_decode2(DEFAULT_TERMS);} else { echo str_replace('rn','',$terms_comdition);}?></textarea>
																	<p class="help-block"></p>
																</div>
															</div>
															<div class="col-md-5">
																<div class="form-group">
																	<label>Regards<code>*</code></label>
																	<textarea class="form-control" id="faithfully" name="faithfully" style="resize: vertical;"><?php if($_REQUEST['mode']=="add"){ echo $db->custom_html_entity_decode2($_SESSION[SITE_SESS.'SESS_NAME']."<br/>".$db->rp_getValue("sales_executive","phone","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'")) ;} else { echo $faithfully;}?></textarea>
																	<p class="help-block"></p>
																</div>
															</div>
															<div class="col-md-5">
																<div class="form-group">
																	<label>Remarks </label>
																	<textarea class="form-control" id="remarks" name="remarks" style="resize: vertical;"><?= $remarks; ?></textarea>
																	<p class="help-block"></p>
																</div>
															</div>
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
	<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">

		$("#transport_charge").numeric();
		$("#packing_charge").numeric();
		CKEDITOR.replace('terms_comdition');
	    CKEDITOR.instances.terms_comdition.getData().replace(/(\r\n|\n|\r)/gm,"");

	    CKEDITOR.replace('faithfully');
	    CKEDITOR.instances.faithfully.getData().replace(/(\r\n|\n|\r)/gm,"");

		var mode = "<?= $_REQUEST['mode'] ?>";
		const format = (num = 0, decimals = 3) => num.toLocaleString('en-US', {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		});
		
		var gst_apply_flag = 0;
		var gstamount = 0;
		var tcs_apply_flag = 0;
		var tcsamount = 0;

		$("#qty").numeric();
		$(".qty").numeric();
		$('#order_date').datepicker({
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
		$("#customer_id").change(function() {

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
			//$("#place_of_supply").val(place_of_supply);
			$("#name_value").html(customer_name);
			$("#name_phone").html(phone);
			$("#name_address").html(address);
			$("#name_state").html(state);
			/*$("#name_gstin").html(gstin);*/
			$("#name_gstin").val(gstin);
			$("#name").html(cname);
			$("#name_pricelist").html(pricelist);
			$(".gst_type").html(gst_type);
			// $("#place_of_supply").val(place_of_supply.toUpperCase());
		})

		function getCustomer(ctype) {
			$('#customer_id').select2("val", "");
			$.ajax({
				type: "post",
				url: "ajax_get_customer.php",
				data: "customer_type=" + ctype,
				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					setTimeout(function() {
						$('#customer_id').html(result);
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
					});
				}

			})
			if (mode == "add") {
				var l = $("#datatable_1").find('tbody').find('tr').length;
				if (l > 0) {
					alert("You lost all added Product");
					$("#datatable_1").find('tbody').html("");
					recalculateRow();
					recalculateFinalValues();
				}
			}

		}

		function getProductList(cid) {
			$.ajax({
				type: "post",
				url: "ajax_get_product.php",
				data: "cid=" + cid,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					/*var cd=$("#customer_id").find("option:selected").data("cash-discount");
					$("#cash_discount").val(cd);
					var ad=$("#customer_id").find("option:selected").data("add-discount");
					$("#additional_discount").val(ad);*/
					setTimeout(function() {
						$('#product_id').html(result);
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
					});
				}

			})

			if (mode == "add") {
				var l = $("#datatable_1").find('tbody').find('tr').length;
				if (l > 0) {
					alert("You lost all added Product");
					$("#datatable_1").find('tbody').html("");
					recalculateRow();
					recalculateFinalValues();
				}
			}
		}

		//--------------Calculation for qty ,total------------------------//
		function recalculateRow(t) {

			var row = $(t).parent('td').parent('tr');
			var price = $(row).find("td").find("input.price").val();
			var original_price = $(row).find("td").find("input.original_price").val();
			var old_price = $(row).find("td").find("input.old_price").val();
			var discount = $(row).find("td").find("input.discount").val();
			var item_gst = $(row).find("td").find("input.item_gst").val();
			var qty = $(row).find("td").find("input.qty").val();
			var inner_size = $(row).find("td").find("input.inner_size").val();
			var outer_size = $(row).find("td").find("input.outer_size").val();
			var box_qty = $(row).find("td").find("input.box_qty").val();
			var stock = $(row).find("input.stock_value").val();
			//var final_qty = inner_size*box_qty;
			outer_size = (outer_size != "") ? parseFloat(outer_size) : 0;

			var box = Math.floor(qty / outer_size);
			if (price > original_price) {
				toastr.error("Rate should not be higher than Available Price");
				$(row).find("td").find("input.price").val(old_price);
				price = $(row).find("td").find("input.price").val();
			}
			if (box != 0) {
				var bagqty = qty - (outer_size * box);
				if (bagqty < 0) {
					bagqty = bagqty * -1;
				}
				bagqty = (bagqty != "") ? parseFloat(bagqty) : 0;
				var bag = (bagqty / inner_size);
				bag = format(bag, 3);

			} else {
				var bag = (qty / inner_size);
				bag = format(bag, 3);
			}

			if(isNaN(item_gst) || item_gst=="NaN")
			{
				item_gst=0
			}

			$(row).find("td").find("input.box_qty").val(box);
			$(row).find("td").find("input.bag").val(bag);
			var discount_amount = (original_price * discount) / 100;
			price = (original_price - discount_amount);
			price1 = (original_price - discount_amount);
			price = price + ((price*item_gst)/100);
			price = price.toFixed(2);
			var item_gst_amount1 = ((price1*item_gst)/100);
			var item_gst_amount1 = qty*item_gst_amount1;
			item_gst_amount1 = item_gst_amount1.toFixed(2);
			$(row).find("td").find("input.price").val(price);
			/*discount = format(discount, 3);
			$(row).find("td").find("input.discount").val(discount);*/
			var total = price * qty;
			// price=format(price,3);
			// total=format(total,3);
			total = total.toFixed(2);
			var total_balance = stock - qty;
			$(row).find("td").find("input.total").val(total);
			$(row).find("td").find("input.item_gst_amount").val(item_gst_amount1);
			$(row).find("td").find("input.final_balance").val(total_balance);
			recalculateFinalValues();
		}

		function recalculateFinalValues() {
			var customer_type = $("#customer_type").val();
			var sum = 0;
			var final_sum = 0;
			var qtytotal = 0;
			var sum_qty = 0;
			var grand_total = 0;
			var additional_discount_amount = 0;
			var cash_discount_amount = 0;
			if(customer_type==7)
			{
				var gst = 0.1;
			}
			else
			{
				var gst = 18;
			}
			var tcs_per = 0.1;
			var total = 0;
			var item_gst_amount2 = 0;


			$('.total').each(function() {

				total = parseFloat($(this).val());
				total = (total != "") ? parseFloat(total) : 0;
				sum += total;
				// this.value = this.value.replace(/\D/g, '');

			});

			/*$('.box_qty').each(function() {
				box_qty = parseFloat($(this).val());
				//box_qty=(qty!="")?parseInt(qty):0;
				if (isNaN(box_qty)) {
					box_qty = 0;
				} else {
					box_qty = parseFloat($(this).val());
				}
				qtytotal += box_qty;
				//only digit enter if i enter '-' or '.' value it replace and get alert//
				if (event.keyCode == 46 || event.keyCode == 8) {
					// let it happen, don't do anything
				}
			});*/

			$('.qty').each(function() {
				qty = parseFloat($(this).val());
				//box_qty=(qty!="")?parseInt(qty):0;
				if (isNaN(qty)) {
					qty = 0;
				} else {
					qty = parseFloat($(this).val());
				}
				sum_qty += qty;
				//only digit enter if i enter '-' or '.' value it replace and get alert//
				if (event.keyCode == 46 || event.keyCode == 8) {
					// let it happen, don't do anything
				}
			});

			$('.item_gst_amount').each(function() {
				item_gst_amount = parseFloat($(this).val());
				item_gst_amount = (item_gst_amount != "") ? parseFloat(item_gst_amount) : 0;
				item_gst_amount2 += item_gst_amount;
			});
			$(".gst_put").val(item_gst_amount2);

			sum = sum.toFixed(2);
			sum_qty = sum_qty.toFixed(2);
			
			$("#finalTotal").val('' + sum);
			$("#sum_qty").val('' + sum_qty);
			$("#total").val('' + sum);

			/*cash discount Calculation*/
			var cash_discount = $("#cash_discount").val();
			cash_discount=parseFloat(cash_discount);
			if((isNaN(cash_discount) || cash_discount=="NaN") || (cash_discount=="" || cash_discount==0))
			{
				var cash_discount_amount = $("#cash_discount_amount").val();
				cash_discount_amount=parseFloat(cash_discount_amount);
				if((isNaN(cash_discount_amount) || cash_discount_amount=="NaN") || (cash_discount_amount=="" || cash_discount_amount==0 || cash_discount==0))
				{
					cash_discount_amount = 0;
					cash_discount=0
					$("#cash_discount_amount").val(0);	
				}
				else
				{
					var sum = sum - cash_discount_amount;
					sum = sum.toFixed(2);
					$("#cash_discount_amount").val(parseFloat(cash_discount_amount));	
					$("#total").val('' + sum);		
				}
			}
			else
			{
				var cd = (sum * cash_discount) / 100;
				var sum = sum - parseFloat(cd);
				sum = sum.toFixed(2);
				$("#cash_discount_amount").val(parseFloat(cd));	
				$("#total").val('' + sum);
			}

			var additional_discount = $("#additional_discount").val();
			additional_discount=parseFloat(additional_discount);
			
			//if(isNaN(additional_discount) || additional_discount=="NaN" || additional_discount=="")

			
			if((isNaN(additional_discount) || additional_discount=="NaN") || (additional_discount=="" || additional_discount==0))
			{
				var additional_discount_amount = $("#addtional_discount_amount").val();
				additional_discount_amount=parseFloat(additional_discount_amount);
				if((isNaN(additional_discount_amount) || additional_discount_amount=="NaN") || ( additional_discount_amount=="" || additional_discount_amount==0 || additional_discount==0))
				{
					additional_discount_amount = 0;
					additional_discount=0;
					$("#addtional_discount_amount").val(0);
				}
				else
				{
					var sum = sum - additional_discount_amount;
					sum = sum.toFixed(2);
					$("#addtional_discount_amount").val(parseFloat(additional_discount_amount));	
					$("#total").val('' + sum);		
				}
			}
			else
			{
				var ad = (sum * additional_discount) / 100;
				var sum = sum - parseFloat(ad);
				sum = sum.toFixed(2);
				$("#addtional_discount_amount").val(ad);
				$("#total").val('' + sum)
			}
			
			var transport_charge = $("#transport_charge").val();
			var packing_charge = $("#packing_charge").val();

			transport_charge=parseFloat(transport_charge);
			packing_charge=parseFloat(packing_charge);

			if(isNaN(transport_charge) || transport_charge=="NaN")
			{
				transport_charge=0
			}

			if(isNaN(packing_charge) || packing_charge=="NaN")
			{
				packing_charge=0;
			}

			var total_charges = (transport_charge + packing_charge);
			var sum = parseFloat(sum) + parseFloat(total_charges);
			$("#total").val('' + sum);

			if(gst_apply_flag==0)
			{
				gst = 0
			}

			if(tcs_apply_flag==0)
			{
				tcs_per = 0
			}
			
			if(sum != "" && sum != "0.00")
			{
				if(gst!="0")
				{
					if(isNaN(item_gst_amount2) || item_gst_amount2=="NaN")
					{
						item_gst_amount2 = 0;
					}
					gst_amount = parseFloat(item_gst_amount2);
					gst_amount = gst_amount.toFixed(2);
					$("#gst_amount").val(parseFloat(gst_amount));
					var final_total = sum;
					var ft = Math.round(final_total);
					ft = ft.toFixed(2);
					var integr = Math.floor(final_total);
					var round_off = final_total - integr;
					round_off = round_off.toFixed(2);
					// round_off=format(round_off,3);
					$("#round_off").val('' + round_off);
					$("#finalgrandTotal").val('' + ft);	
				}
				else
				{
					$("#gst_amount").val(parseFloat(0));
					var final_total = sum;
					var ft = Math.round(final_total);
					ft = ft.toFixed(2);
					var integr = Math.floor(final_total);
					var round_off = final_total - integr;
					round_off = round_off.toFixed(2);
					// round_off=format(round_off,3);
					$("#round_off").val('' + round_off);
					$("#finalgrandTotal").val('' + ft);
				}
			}
			
			/*if (sum != "" && sum != "0.00") 
			{
				var gst_amount = (sum * gst) / 100;
				var gst_amount1 = (sum * gst) / 100;
				gst_amount = gst_amount.toFixed(2);
				gst_amount1 = gst_amount1.toFixed(2);
				$(".gst-amount-display").html(gst_amount);
				gst_amount = parseFloat(gst_amount) + parseFloat(sum);
				gst_amount = gst_amount.toFixed(2);
				$("#gst_amount").val(gst_amount1);
				var final_total = gst_amount;
				var ft = Math.round(final_total);
				ft = ft.toFixed(2);
				var integr = Math.floor(final_total);
				var round_off = final_total - integr;
				round_off = round_off.toFixed(2);
				// round_off=format(round_off,3);
				$("#round_off").val('' + round_off);
				$("#finalgrandTotal").val('' + ft);
			}*/
			/*else
			{
				if($("#cd").prop("checked") == true)
				{
					toastr.error("No such a Item Found to apply Cash Discount");
					$("#cd").prop("checked",false);
				}
			}*/

		}

		function hasValue(elem) {
			return $(elem).filter(function() {
				return $(this).val();
			}).length > 0;
		}

		$("#add").click(function() {
			var product_id = $("#product_id").val();
			var qty = $("#qty").val();
			var pro_description = $("#pro_description").val();
			count = 0;
			//var isProductAvailable=check_form();
			if (product_id == "") {
				toastr.error('Please Select product!!');
			} else if (qty == "" || qty == 0) {
				toastr.error('Please Enter At least one Quantity!!');
			}else if (pro_description == "") {
				toastr.error('Please Enter Product Description!!');
			} else {
				/*var stockcheck = $("#product_id").find('option:selected').data('stock_qty');
				if(stockcheck>=qty)
				{*/
				var count = $('.count').length;
				count = ++count;
				var product_id = $("#product_id").val();
				var price = $("#product_id").find('option:selected').data('pricelist');
				var p_name = $("#product_id").find('option:selected').data('name');
				var weight = $("#product_id").find('option:selected').data('weight');
				var weight_id = $("#product_id").find('option:selected').data('weight-id');
				var inner_size = $("#product_id").find('option:selected').data('inner_size');
				var outer_size = $("#product_id").find('option:selected').data('outer_size');
				var pro_id = $("#product_id").find('option:selected').data('pro_id');
				var original_price = $("#product_id").find('option:selected').data('original-price');
				var discountPer = $("#product_id").find('option:selected').data('discount');
				var stock = $("#product_id").find('option:selected').data('stock');
				var catno = $("#product_id").find('option:selected').data('catno');
				var unit_name = $("#product_id").find('option:selected').data('unit_name');
				var hsncode = $("#product_id").find('option:selected').data('hsncode');

				// alert(pro_id);
				var qty = $("#qty").val();

				qty = (qty != "") ? parseFloat(qty) : 0;
				outer_size = (outer_size != "") ? parseFloat(outer_size) : 0;

				var box = Math.floor(qty / outer_size);

				if (box != 0) {
					var bagqty = qty - (outer_size * box);
					if (bagqty < 0) {
						bagqty = bagqty * -1;
					}
					bagqty = (bagqty != "") ? parseFloat(bagqty) : 0;
					var bag = (bagqty / inner_size);
					bag = format(bag, 3);

				} else {
					var bag = (qty / inner_size);
					bag = format(bag, 3);
				}
				// $p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
				// $p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];

				var discount_amount = (original_price * 0) / 100;
				price = original_price - discount_amount;

				//var total=box*price;
				var total = qty * price;
				// price=format(price,3);
				// total=format(total,3);
				// total=total.replace(",","");
				price = price.toFixed(2);
				total = total.toFixed(2);
				var balance = stock - qty;
				original_price = original_price.toFixed(2);
				//discountPer = discountPer.toFixed(2);
				// original_price=format(original_price,3);
				// discountPer=format(discountPer,3);
				// var duplicate=$("input.pro_id[value='"+pro_id+weight_id+"']").length;
				var brand_id = $("#brand_id").val();
				// alert(duplicate);
				//var duplicate = hasValue($("input.pro_id[value='" + pro_id + weight_id + "']"));
				var duplicate = 0;
				if (duplicate == 0) {
					var new_row = "<tr><td width='300px;' class='text-center'><input type='hidden' class='pro_id' name='pro_id' id='pro_id' value='" + pro_id + weight_id + "'><input type='hidden' name='product_id[]' value='" + pro_id + "' class='product_id' id='product_id'/><input type='hidden' name='pro_name[]' value='" + p_name + "' id='pro_name'><input type='hidden' name='weight_id[]' value='" + weight_id + "' id='weight_id'><input type='hidden' name='brand_id[]' value='" + brand_id + "' id='brand_id'>" + p_name + "</td>" +

						"<td class='text-center'>"+unit_name+"</td>" +

						"<td class='text-center'>"+stock+"</td>" +

						"<td><textarea rows='3' cols='10' id='pro_description' name='pro_description[]' class='pro_desc' style='margin: 0!important;'></textarea></td>" +

						"<td class='text-center'>"+hsncode+"</td>" +
						/*"<td style='text-align:right'><input type='hidden' name='inner_size' class='inner_size' value='"+inner_size+"'><input readonly class='form-control bag' type='text' name='bag[]' style='text-align:right;width:100px;' value='"+bag+"''></td>"+

						"<td style='text-align:right'><input type='hidden' name='outer_size' class='outer_size' value='"+outer_size+"'><input readonly type='text' class='form-control box_qty' style='text-align:right;width:100px;' name='box_qty[]' class='box_qty positive' value='"+box+"'></td>"+*/

						"<td class='text-center'><input type='text' name='qty[]' class='form-control positive  qty' style='text-align:right;width: 70px;' value='" + qty + "' onChange='recalculateRow(this)'  id='qty'/></td>" +
						
						"<td style='text-align:right'><input type='text' name='original_price[]' class='form-control  original_price' style='text-align:right;width:70px;' value='" + original_price + "' onChange='recalculateRow(this)'  id='original_price'/</td>" +
						
						"<td style='text-align:right'><input style='text-align:right;width: 70px;' type='text' name='discount[]' class='discount form-control' value='" + discountPer + "' onChange='recalculateRow(this)'></td>" +
						
						"<td style='text-align:right'><input type='hidden' class='item_gst_amount'><input type='text' name='item_gst[]' style='text-align:right;width: 70px;' class='form-control item_gst' onChange='recalculateRow(this)' value=''></td>" +

						
						"<td class='price_val' ><input type='text' style='text-align:right;width: 70px;'  name='price[]' class='price form-control'  onChange='recalculateRow(this)' value='" + price + "'><input type='hidden' style='text-align:right;' class='old_price form-control' value='" + price + "'></td>" +

						"<td><input type='text' class='total form-control' disabled onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='subtotal[]' value='" + total + "' ></td>" +
						
						"<td  class='text-center'><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></td></tr>";
					$("#datatable_1").find('tbody').append(new_row);
					/*$("#datatable_1").find('tbody').prepend(new_row);*/
					recalculateRow();
					recalculateFinalValues();
				} else {
					$mainInputBox = $("input.pro_id[value='" + pro_id + weight_id + "']").parent().parent().find("td>input.qty");
					var old_va = $mainInputBox.val();
					var new_va = parseFloat(old_va) + parseFloat(qty);
					$mainInputBox.val(new_va);
					$mainInputBox.change();
					toastr.success("Product Qty Update Successfuly.");
					// toastr.error("Product already added!!");
				}
				/*}
				else
				{
					toastr.error('Entered Quantity Is Not Available In Stock!!');
				}*/
			}
			$("#qty").val("");
			$("#product_id").select2("val", "");
		})

		function check_form() {
			$(".form-body").children().removeClass("has-error");
			var isValid = true;
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
				if ($("#order_date").val() == "" || $("#order_date").val().split(" ").join("") == "") {
					vd = aj.error('order_date', "Please Enter Order Date.", "add_error");
					isValid = false;
				}
				if ($("#transport_name").val() == "" || $("#transport_name").val().split(" ").join("") == "") {
					vd = aj.error('transport_name', "Please Enter Order Date.", "add_error");
					isValid = false;
				}
				if ($("#transport_through").val() == "" || $("#transport_through").val().split(" ").join("") == "") {
					vd = aj.error('transport_through', "Please Enter Order Date.", "add_error");
					isValid = false;
				}
				if ($("#transport_charge").val() == "" || $("#transport_charge").val().split(" ").join("") == "") {
					vd = aj.error('transport_charge', "Please Enter Order Date.", "add_error");
					isValid = false;
				}

				

				

				// if ($("#remarks").val() == "" || $("#remarks").val().split(" ").join("") == "") {
				// 	vd = aj.error('remarks', "Please Enter Remarks", "add_error");
				// 	isValid = false;
				// }

			<?php } ?>

			if ($("#name_gstin").val() == "" || $("#name_gstin").val().split(" ").join("") == "") {
					vd = aj.error('name_gstin', "Please Enter GST Number", "add_error");
					isValid = false;
				}

				if ($("#shipping_address").val() == "" || $("#shipping_address").val().split(" ").join("") == "") {
					vd = aj.error('shipping_address', "Please Enter Shipping Address", "add_error");
					isValid = false;
				}

				if ($("#billing_address").val() == "" || $("#billing_address").val().split(" ").join("") == "") {
					vd = aj.error('billing_address', "Please Enter Billing Address", "add_error");
					isValid = false;
				}

			/*if($("#invoice_date").val()!=""){
				if(!isDate($("#invoice_date").val()).result)
				{
					aj.error('invoice_date','Please Select Invoice Date!!','add_error');
					isValid=false;
				}
			}*/
			var count_row = $("#datatable_1").find('tbody tr').length;
			// alert(count_row);
			if (count_row == 0) {
				toastr.error("Please Add At Least one Product!!");
				isValid = false;
			}
			$(".qty").each(function() {
				if ($(this).val() == "" || $(this).val() == 0) {
					toastr.error("Please Enter At Least one Quantity!!");
					$(".box_qty").focus();
					isValid = false;
				}
			});

			$(".pro_desc").each(function() {
				if ($(this).val() == "" || $(this).val() == 0) {
					toastr.error("Please Enter Product Description!!");
					$(".pro_desc").focus();
					isValid = false;
				}
			});

			if (isValid) 
			{
				var r = confirm("Are You sure want to Save this Order??");
				if (r) 
				{
					return true;
				}
				else 
				{
					return false;
				}
			} 
			else 
			{
				return false;
			}
		}

		$(".form-control").bind("keyup change", function() {
			if ($(this).parent().hasClass("has-error")) {
				$(this).parent().removeClass("has-error");
				$(this).parent().find('p.help-block').html("");
			}
		});

		$(document).ready(function() {
			$("#datatable_1").on('click', '.delete', function() {

				//var rows = document.getElementById("#datatable_1").getElementsByTagName("tr").length;
				//alert(rows);
				var r = confirm("Are you sure you want to delete?");
				if (r) {
					$(this).closest('tr').remove();
					recalculateFinalValues();
				}
			});
		});


		$(document).ready(function() {

			var mode = "<?= $_REQUEST['mode']; ?>";
			if (mode == "edit") {
				$("#customer_id").trigger("change");
				var cid = "<?= $customer_id; ?>";
				getProductList(cid);
				recalculateFinalValues();
				if ($("#cd").prop("checked") == true) {
					recalculateFinalValues();
				}
				if ($("#ad").prop("checked") == true) {
					
					recalculateFinalValues();
				}
			}
		})

		/*$("#cd").on("click", function() {
			if ($(this).prop("checked") == true) {
				var sum = $("#finalTotal").val();
				var additional_discount_amount = $("#additional_discount_amount").val();
				if (additional_discount_amount != "") {
					sum = additional_discount_amount;
				} else {
					sum = sum;
				}
				if (sum != "" && sum != "0.00") {
					setTimeout(function() {
						var cd_val = $("#cash_discount").val();
						$("#cash_discount_flag").val("1");
						// alert(cd_val);
						$(".cd_per").html("(" + cd_val + "%)");

						sum = sum.replace(",", "");
						sum = parseFloat(sum);
						cd_val = parseFloat(cd_val);
						var cash_discount = (sum * cd_val) / 100;
						var cash_discount_amount = sum - parseFloat(cash_discount);
						cash_discount_amount = cash_discount_amount.toFixed(2);
						$("#cash_discount_amount").val(cash_discount_amount);
						var gst = 18;
						var gst_amount = (cash_discount_amount * 18) / 100;

						gst_amount = gst_amount.toFixed(2);
						$(".gst-amount-display").html(gst_amount);

						gst_amount = parseFloat(gst_amount) + parseFloat(cash_discount_amount);
						// alert(gst_amount);
						gst_amount = gst_amount.toFixed(2);
						$("#gst_amount").val(gst_amount);
						// var round_off=
						var final_total = gst_amount;
						var ft = Math.round(final_total);
						ft = ft.toFixed(2);
						var integr = Math.floor(final_total);
						var round_off = final_total - integr;
						round_off = round_off.toFixed(2);

						$("#round_off").val('' + round_off);
						$("#finalgrandTotal").val('' + ft);
					}, 100);
				}

			} else {
				// alert("unchecked");	
				$(".cd_per").html("");
				$("#cash_discount_flag").val("0");
				$("#cash_discount_amount").val("0.00");
			}
			recalculateFinalValues();
		})

		$("#ad").on("click", function() {
			if ($(this).prop("checked") == true) {
				var sum = $("#finalTotal").val();
				if (sum != "" && sum != "0.00") {
					setTimeout(function() {
						var ad_val = $("#additional_discount").val();
						$("#additional_discount_flag").val("1");
						$(".ad_per").html("(" + ad_val + "%)");
						sum = sum.replace(",", "");
						sum = parseFloat(sum);
						ad_val = parseFloat(ad_val);
						var additional_discount = (sum * ad_val) / 100;
						var additional_discount_amount = sum - parseFloat(additional_discount);
						additiona_discount_amount = additional_discount_amount.toFixed(2);
						$("#additional_discount_amount").val(additional_discount_amount);
					}, 100);
				}
			} else {
				$("#additional_discount_flag").val("0");
				$("#additional_discount_amount").val("0.00");
			}
		})*/

		$(document).ready(function() {
			if (mode == "edit") {
				if ($("#gst_apply").prop("checked") == true) {
					gst_apply_flag = 1;
					recalculateFinalValues();
				}
			}
		})

		$("#gst_apply").on("click",function()
		{
			if ($('#gst_apply').is(":checked"))
			{
				gst_apply_flag = 1;
				$("#gst_apply_flag").val("1");
			}
			else
			{
				gst_apply_flag = 0;
				$("#gst_apply_flag").val("0");
			}
			recalculateFinalValues();
		})

		$(".positive").keyup(function(event) {

			if (event.keyCode == 46 || event.keyCode == 8) {
				// let it happen, don't do anything
			} else if (/\D/g.test(this.value)) {
				toastr.error("Only Digits Allowed");
				this.value = this.value.replace(/\D/g, '');
			}
		});

		function isDate(val) {

			var strng;
			strng = "Is time an illusion created within our minds? Or is it a dimension of the physical universe? Either way,time largely defines the human experience.";
			var isdash = false;
			var isdot = false;
			var position = val.search(/-/i);
			if (position < 0) {
				var isdash = false;
				var isdot = true;
			}
			var position1 = val.search(/\./i);
			if (position1 < 0) {
				var isdot = false;
				var isdash = true;
			}


			regexp = /^(0[1-9]|[12][0-9]|3[01])[\- \/.](?:(0[1-9]|1[012])[\- \/.](19|20)[0-9]{2})$/;
			regexp1 = /^([1-9]|[12][0-9]|3[01])[\- \/.](?:([1-9]|1[012])[\- \/.](19|20)[0-9]{2})$/;
			if (val.match(regexp) || val.match(regexp1)) {
				if (isdot) {
					var val = val.split(".");
					val = val[1] + "-" + val[0] + "-" + val[2];
					var d = new Date(val);
					return {
						result: !isNaN(d.valueOf()),
						message: "Enter Proper Date in dd.mm.yyyy format"
					}
				} else if (isdash) {
					var val = val.split("-");
					val = val[1] + "-" + val[0] + "-" + val[2];
					var d = new Date(val);
					return {
						result: !isNaN(d.valueOf()),
						message: "Enter Proper Date in dd-mm-yyyy format"
					}
				} else {
					return {
						result: false,
						message: "Enter Proper Date in dd-mm-yyyy format"
					}
				}
			} else {
				return {
					result: false,
					message: "Enter Proper Date in dd.mm.yyyy format"
				}
			}
		}

		// $("#transport_charge").on("change", function() {
		// 	let transport_charge_val = this.value;
		// 	let finalTotal = $("#finalTotal").val();
		// 	let gst_amount = $("#gst_amount").val();
		// 	let total_gst  = "<?= $total_gst ?>";

		// 	let trans_charge = 0;


			

		// 	if(finalTotal != 0) {
		// 		// alert(finalTotal);
		// 		let grand_total = (parseInt(total_gst) + parseInt(transport_charge_val));
				
		// 		$("#finalgrandTotal").val(grand_total.toFixed(2));

		// 	} else {
		// 		// alert('dfdfsdrf');
		// 	}
		// })

	</script>

	<script type="text/javascript">
		var mode = '<?= $_REQUEST['mode']; ?>';
		// update code -sagar 
		if(mode=="edit")
		{
			var id = $("#transport_through").val(); 
			var transport_name_selected_id = $("#transport_name_selected_id").val();
			getTransportname(id,transport_name_selected_id);	
		}

		var mode = '<?= $_REQUEST['mode']; ?>';
		var order_id = '<?= $_REQUEST['id']; ?>';
		if(mode=="add" && order_id!="")
		{
			var id = $("#transport_through").val(); 
			getTransportname(id);	
		}
		function getTransportname(id,transport_name_selected_id="")
		{	
			$.ajax({
				type: "post",
				url: "ajax_get_transport_detail.php",
				data: "id=" + id+"&selected_id="+transport_name_selected_id,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					setTimeout(function() {
						$("#transport_name").select2("destroy");
						$('#transport_name').html(result);
						$("#transport_name").select2();
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
					});
				}
			})
		}
		// update code -sagar 
	</script>
</body>

</html>