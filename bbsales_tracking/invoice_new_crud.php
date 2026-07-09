<?php
$page_id=588;$page_slug='invoice_page';
$page_slug  = 'add_invoice';
$ctable     = "invoice_new";
$ctable1    = "Invoice";
$main_page  = $ctable;
$page       = "add_" . $ctable;
$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
include("connect.php");
$uid          = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$utype        = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];
/*$invoice_no     = $db->getLastInsertId("invoice_new");
$invoice_no     = OUTLETS_INVOICE_NO . str_pad($invoice_no, 2, '0', STR_PAD_LEFT);*/
$invoice_date = date('d-m-Y');
$packing_charge = 0;
$transport_charge = 0;
if (isset($_REQUEST['submit'])) {

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add")
	{
		//print_r($_REQUEST); exit;
		$invoice_date = $_REQUEST['invoice_date'];
		$dispatch_id_imploded = implode(",",$_REQUEST['dispatch_id']);
		$warehouse_id = isset($_REQUEST['warehouse_id'])?$db->clean(implode(",", $_REQUEST['warehouse_id'])):"";
		$packing_charge = (isset($_REQUEST['packingcharge']) && $_REQUEST['packingcharge']!="")?$_REQUEST['packingcharge']:0;
		$transport_charge = (isset($_REQUEST['transportcharge']) && $_REQUEST['transportcharge']!="")?$_REQUEST['transportcharge']:0;

		$cash_discount = (isset($_REQUEST['cd_discount']) && $_REQUEST['cd_discount']!="")?$_REQUEST['cd_discount']:0;
		$cash_discount_amount = (isset($_REQUEST['cd_amount']) && $_REQUEST['cd_amount']!="")?$_REQUEST['cd_amount']:0;
		
		$additional_discount = (isset($_REQUEST['ad_discount']) && $_REQUEST['ad_discount']!="")?$_REQUEST['ad_discount']:0;
		$additional_discount_amount = (isset($_REQUEST['ad_amount']) && $_REQUEST['ad_amount']!="")?$_REQUEST['ad_amount']:0;

		$vendor_code = (isset($_REQUEST['vendor_code']) && $_REQUEST['vendor_code']!="")?$_REQUEST['vendor_code']:"";
		$tendor_code = (isset($_REQUEST['tendor_code']) && $_REQUEST['tendor_code']!="")?$_REQUEST['tendor_code']:"";
		$way_bill_no = (isset($_REQUEST['way_bill_no']) && $_REQUEST['way_bill_no']!="")?$_REQUEST['way_bill_no']:"";

		if(sizeof($_REQUEST['dispatch_id'])>0)
		{
			$dispatch_detailR = $db->rp_getData("dispatch_detail","*","id IN (".$dispatch_id_imploded.")","1",0);
			$dispatch_detail = mysqli_fetch_assoc($dispatch_detailR);
		}
		$ordersR = $db->rp_getData("orders","*","id IN (".$dispatch_detail['order_id'].")","1",0);
		$orders = mysqli_fetch_assoc($ordersR);
		foreach ($orders as $key => $value) {
			$orders[$key] = (isset($value) && $value!="")?$value:"";
		}
		$mainArrayToinsertInInvoice = array (
			"dispatch_ids" => $dispatch_id_imploded,
			"customer_id" => $orders['customer_id'],
			"company_name" => $orders['company_name'],
			"dealer_id" => $orders['dealer_id'],
			"super_stockist_id" => $orders['super_stockist_id'],
			"sales_id" => $orders['sales_id'],
			"sales_type" => $orders['sales_type'],
			"customer_name" => $orders['customer_name'],
			"customer_type" => $orders['customer_type'],
			"contact_number" => $orders['contact_number'],
			"address" => htmlentities($orders['address']),
			"city" => $orders['city'],
			"state" => $orders['state'],
			"country" => $orders['country'],
			"email" => $orders['email'],
			"status" => 0,
			"adate" => date("Y-m-d H:i:s"),
			"isDelete" => 0,
			"isActive" => 1,
			"remarks" => $orders['remarks'],
			"item_total_price" => 0,
			"total_qty" => 0,
			"taxable" => 0,
			"discount" => 0,
			"discount_amount" => 0,
			"subtotal" => 0,
			"total_amount" => 0,
			"cgst_amount" => 0,
			"sgst_amount" => 0,
			"igst_amount" => 0,
			"grand_total" => 0,
			"roundoff" => 0,
			"total_taxamount" => 0,
			"grand_total_rounded" => 0,
			"local_id" => $orders['local_id'],
			"taxable_amount" => 0,
			"discount_type" => 0,
			"modify_date" => date("Y-m-d H:i:s"),
			"gst" => 0,
			"booking" => 0,
			"transport" => $orders['transport'],
			"entry_flag" => $orders['entry_flag'],
			"terms_comdition" =>INVOICE_DEFAULT_TERMS,// $db->clean($orders['terms_comdition']),
			"faithfully" => $db->clean($orders['faithfully']),
			"transport_name" => $orders['transport_name'],
			"transport_through" => $orders['transport_through'],
			"transport_charge" => $transport_charge,
			"packing_charge" => $packing_charge,
			"shipping_address" => $db->clean($orders['shipping_address']),
			"billing_address" => $db->clean($orders['billing_address']),
			"vendor_code" => $orders['vendor_code'],
			"tendor_code" => $orders['tendor_code'],
			"warehouse_id" => $warehouse_id,
			"way_bill_no" => $way_bill_no,
			"cash_discount" => $cash_discount,
			"cash_discount_amount" => $cash_discount_amount,
			"additional_discount" => $additional_discount,
			"additional_discount_amount" => $additional_discount_amount,
			
		);
		/*log entry*/
		$module_name = "Invoice";
		$flag = "Web";
		$log_description = $module_name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/
		$invoice_insert_id = $db->rp_insert("invoice_new",array_values($mainArrayToinsertInInvoice),array_keys($mainArrayToinsertInInvoice),0,$log_description,$flag,$module_name,"",$orders['customer_id']);
		$dispatch_itemR = $db->rp_getData("dispatch_item","*","dispatch_id IN (".$dispatch_id_imploded.")","",0);

		
		$pro_qty = 0;
		$unitprice = 0;
		$totalprice = 0;
		$taxable = 0;
		$subtotal = 0;
		$grand_total = 0;
		
		while ($dispatch_item = mysqli_fetch_assoc($dispatch_itemR))
		{
			foreach ($dispatch_item as $key => $value) {
				$dispatch_item[$key] = (isset($value) && $value!="")?$value:"";
			}

			$insertItemArray = array (
				"invoice_id" => $invoice_insert_id,
				"pro_id" => $dispatch_item['pro_id'],
				"weight_id" => $dispatch_item['weight_id'],
				"pro_name" => $db->clean($dispatch_item['pro_name']),
				"pro_description" => $dispatch_item['pro_description'],
				"pro_qty" => $dispatch_item['qty'],
				"unitprice" => $dispatch_item['unitprice'],
				"original_price" => $dispatch_item['original_price'],
				"totalprice" => $dispatch_item['totalprice'],
				"discount" => $dispatch_item['discount'],
				"discount_amount" => $dispatch_item['discount_amount'],
				"taxable" => $dispatch_item['taxable'],
				"subtotal" => $dispatch_item['subtotal'],
				"grandtotal" => $dispatch_item['grand_total'],
				"adate" => date("Y-m-d H:i:s"),
				"isActive" => 1,
				"isDelete" => 0,
				"status" => 0,
				"modify_date" => date("Y-m-d H:i:s"),
				"dispatch_item_type" => $dispatch_item['dispatch_item_type'],
			);

			$pro_qty = $dispatch_item['qty'];
			$unitprice = $dispatch_item['unitprice'];
			$totalprice = $dispatch_item['totalprice'];
			$taxable = $dispatch_item['taxable'];
			$subtotal = $dispatch_item['subtotal'];
			$grand_total = $dispatch_item['grand_total'];
			$proqtysum += $pro_qty;
			$unitpricesum += $unitprice;
			$totalpricesum += $totalprice;
			$taxablesum += $taxable;
			$subtotalsum += $subtotal;
			$grand_totalsum += $grand_total;

			$invoice_item_insert = $db->rp_insert("invoice_new_product_item",array_values($insertItemArray),array_keys($insertItemArray),0);
		}
		
		
		$db->rp_update("invoice_new",array("item_total_price"=>$unitpricesum,"total_qty"=>$proqtysum,"taxable"=>$taxablesum,"subtotal"=>$_REQUEST['total_amount'],"total_amount"=>$_REQUEST['total_amount'],"grand_total"=>$_REQUEST['grand_total'],"grand_total_rounded"=>$_REQUEST['grand_total'],"igst_amount"=>$_REQUEST['total_gst'],"cash_discount"=>$cash_discount,"cash_discount_amount"=>$cash_discount_amount,"additional_discount"=>$additional_discount,"additional_discount_amount"=>$additional_discount_amount,"transport_charge"=>$transport_charge,"packing_charge"=>$packing_charge),"id='".$invoice_insert_id."'",0);

		/*for notification*/
		$id = $db->rp_getValue("dealer_distributor_network","sales_executive_id","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'",0);
		$type="invoice";
		$title="Invoice Created By ".$_SESSION[SITE_SESS.'SESS_NAME'];
		$body = "Invoice Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
		$click_action="dealer_invoice_manage.php";

		$Data = [
		    'type' => $type,
            'title' => $title,
            'body' =>  $body,
            'description'    => $body,
            'user_id'        => $id,
            'reference_id'   => $invoice_insert_id,
            'item_id'        => $invoice_insert_id,
			'reference_type' => 'invoice',
            'icon' => NOTIFICATIONICON,
            'image' => NOTIFICATIONIMAGE,
            'click_action'=> ADMINSITEURL.$click_action,
		];

		$ReferanceArray = [
            'reference_id' => 	$invoice_insert_id,
            'reference_table' => "invoice_new",
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
		/*for notification*/

		// update packing slip status
		$customer_id = $db->rp_getValue("invoice_new","customer_id","id='".$invoice_insert_id."'",0);
		$dispatch_id = $db->rp_getValue("invoice_new","dispatch_ids","id='".$invoice_insert_id."'",0);
		$packing_slip_id = $db->rp_getValue("packing_slip","id","customer_id='".$customer_id."' AND dispatch_id='".$dispatch_id."' AND status=0",0);
		$db->rp_update("packing_slip",array("status"=>1),"id='".$packing_slip_id."'",0);
		// $db->rp_update("dispatch_detail",array("status"=>1),"id='".$dispatch_id."'",0);
		$db->addSuccessMessage("insert successfully!!");
		$db->rp_location("dealer_invoice_manage.php");
	}

	else if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "edit") 
	{
		if ($rights['update_flag'] != 1) 
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$transport_name	    = isset($_REQUEST['transport_name'])?trim($_REQUEST['transport_name']):"";
		$transport_through  = isset($_REQUEST['transport_through'])?trim($_REQUEST['transport_through']):"";
		$shipping_address	= isset($_REQUEST['shipping_address'])?trim($_REQUEST['shipping_address']):"";
		$billing_address    = isset($_REQUEST['billing_address'])?trim($_REQUEST['billing_address']):"";
		$vendor_code	    = isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
		$tendor_code	    = isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
		$warehouse_id       = isset($_REQUEST['warehouse_id'])?$db->clean(implode(",", $_REQUEST['warehouse_id'])):"";
		$way_bill_no        = (isset($_REQUEST['way_bill_no']) && $_REQUEST['way_bill_no']!="")?$_REQUEST['way_bill_no']:"";
		$invoice_id         = $db->clean($_REQUEST['id']);
		$transportcharge    = (isset($_REQUEST['transportcharge']) && $_REQUEST['transportcharge']!="")?$_REQUEST['transportcharge']:"";
		$packingcharge      = (isset($_REQUEST['packingcharge']) && $_REQUEST['packingcharge']!="")?$_REQUEST['packingcharge']:"";
		$terms_comdition    = html_entity_decode($_REQUEST['terms_comdition']);
		$faithfully        	= html_entity_decode($_REQUEST['faithfully']);

		$cash_discount			= isset($_REQUEST['cd_discount'])?$db->clean($_REQUEST['cd_discount']):"";
		$cash_discount_amount	= isset($_REQUEST['cd_amount'])?$db->clean($_REQUEST['cd_amount']):"";
		$additional_discount	= isset($_REQUEST['ad_discount'])?$db->clean($_REQUEST['ad_discount']):"";
		$additional_discount_amount	= isset($_REQUEST['ad_amount'])?$db->clean($_REQUEST['ad_amount']):"";
		$sub_total 					=  $_REQUEST['total_amount'];
		$grand_total 				=  $_REQUEST['grand_total'];
		$gst_amount 				=  $_REQUEST['total_gst'];

		$isUpdated=$db->rp_update("invoice_new",array("transport_name" => $transport_name, "transport_through" => $transport_through, "billing_address"=>$db->clean($billing_address),"shipping_address"=>$db->clean($shipping_address),"vendor_code"=>$vendor_code,"tendor_code"=>$tendor_code,"warehouse_id"=>$warehouse_id,"way_bill_no"=>$way_bill_no,"transport_charge"=>$transportcharge,"packing_charge"=>$packingcharge,"terms_comdition"=>$terms_comdition,"faithfully"=>$faithfully,"cash_discount"=>$cash_discount,"cash_discount_amount"=>$cash_discount_amount,"additional_discount"=>$additional_discount,"additional_discount_amount"=>$additional_discount_amount,"total_amount"=>$sub_total,"grand_total"=>$grand_total,"igst_amount"=>$gst_amount,"grand_total_rounded"=>$grand_total),"id='".$invoice_id."'",0);
		
		if($isUpdated)
		{
			/*update in account*/
			require_once('../include/class.system.php');
			$system = new System();
			
			$customer_id = $db->rp_getValue("invoice_new","customer_id","id='".$invoice_id."'",0);
			$AccountInfo=$db->rp_getData("account","*","cid='".$customer_id."'","",0);
			$AccountInfo=mysqli_fetch_assoc($AccountInfo);
			if($AccountInfo)
			{
				$AccountID=$AccountInfo['id'];
				$AccountNo=$AccountInfo['acc_no'];
				$count = $db->rp_getTotalRecord("account_transaction","reference_id='".$invoice_id."' AND reference_table='invoice' AND isDelete=0");
				if($count>=0)
				{
					$delete = $db->rp_update("account_transaction",array("isDelete"=>1),"reference_id='".$invoice_id."' AND reference_table='invoice'");
					$Columns=array("cid","account_id","account_no","type","debit","amount","reference_id","reference_table","description","payment_date");
					$debit="-".$db->rp_getValue("invoice_new","grand_total_rounded","id='".$invoice_id."'",0);
					$grand_total=$db->rp_getValue("invoice_new","grand_total_rounded","id='".$invoice_id."'",0);
					$payment_date=date('Y-m-d');
					$payment_type = 0;
					$remark = "Invoice Entry Of Invoice No. <a target='_blank' href='invoice_viewer.php?invoice_id=".$invoice_id."'>". $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'",0)."</a>";
					$Values=array($customer_id,$AccountID,$AccountNo,$payment_type,$debit,$grand_total,$invoice_id,"invoice",$remark,$payment_date);
					$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);	
				}
			}
			/*update in account*/
			$db->addSuccessMessage('Invoice Update Successfully.');
			$db->rp_location("dealer_invoice_manage.php");
		}
		else 
		{
			$db->addErrorMessage('Invoice Update Failed.');
			$db->rp_location("invoice_new_crud_new.php?mode=edit&id=".$_REQUEST['id']);
		}
	}
}

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "edit") {
	if ($rights['update_flag'] != 1 || $generate_invoice_flag == 1) {
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$page_title = ucwords($_REQUEST['mode']) . '&nbsp' . "Invoice";
	$detail['id'] = $_REQUEST['id'];
	$invoice_no = $db->rp_getValue("invoice_new","invoice_no","id='".$_REQUEST['id']."'");
	$customer_id = $db->rp_getValue("invoice_new", "customer_id", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	$customer_type = $db->rp_getValue("invoice_new", "customer_type", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	$customer_name = $db->rp_getValue("invoice_new", "company_name", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	$dispatch_ids = $db->rp_getValue("invoice_new", "dispatch_ids", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	//$packing_slip_id = $db->rp_getValue("packing_slip","id","customer_id = '".$customer_id."' AND isDelete=0");
	$packing_slip_id = $db->rp_getValue("packing_slip","id","dispatch_id = '".$dispatch_ids."' AND isDelete=0",0);
	$terms_comdition = $db->rp_getValue("invoice_new", "terms_comdition", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	$faithfully = $db->rp_getValue("invoice_new", "faithfully", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
	$remarks = $db->rp_getValue("invoice_new", "remarks", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
}


if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "delete") 
{
	if ($rights['delete_flag'] != 1) 
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	// $_REQUEST['id'];

	/*log entry*/
		$customer_id = $db->rp_getValue("invoice_new","customer_id","id='".$_REQUEST['id']."'");
		$invoice_no = $db->rp_getValue("invoice_new","invoice_no","id='".$_REQUEST['id']."'");
		$module_name = "Invoice";
		$flag = "Web";
		$log_description = $module_name." ".$invoice_no." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
	/*log entry*/
	$rows = array("isDelete"=>1);
	$db->rp_update("invoice_new",$rows,"id='".$_REQUEST['id']."'",0,$log_description,$flag,$module_name,"",$customer_id);
	$db->rp_update("invoice_new_product_item",array("isDelete"=>1),"invoice_id='".$_REQUEST['id']."'");
	/*update packingslip status*/
	$db->rp_update("packing_slip",array("packing_slip.status"=>0),"dispatch_id IN ((SELECT invoice_new.dispatch_ids FROM invoice_new WHERE invoice_new.id='".$_REQUEST['id']."' ))",0);
	/*update packingslip status*/

	/*update dispatch status*/
	$db->rp_update("dispatch_detail",array("dispatch_detail.status"=>0),"id IN ((SELECT invoice_new.dispatch_ids FROM invoice_new WHERE invoice_new.id='".$_REQUEST['id']."' ))",0);
	/*update dispatch status*/
	$reply['ack'] = 1;
	$reply['ack_msg'] = "Invoice Delete successfully..";
	if ($reply['ack'] == 1) 
	{
		$db->addSuccessMessage($reply['ack_msg']);
		//for redirect to location after Delete
		$db->rp_location("dealer_invoice_manage.php");
	} 
	else 
	{
		$db->addErrorMessage($reply['ack_msg']);
		$db->rp_location("dealer_invoice_manage.php");
	}
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
					$back = 'dealer_orders_manage.php';
					?>
					<h1><a href="<?php echo  $back; ?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
				</div>
			</div>
		</div>
		<div class="page-content">
			<div class="container">
				<div class="row">
					<div class="col-xl-12">
						<?php $db->printErrorMessage(); ?>
						<?php $db->printSuccessMessage(); ?>
					</div>
				</div>
				<!-- Employee ID-->
				<form role="form" action="" method="post" onSubmit="return check_form();">
					<div class="row">
						<div class="col-xl-12">
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
																		if($_REQUEST['mode']=="add" && $_REQUEST['packing_slip_id']!="")
																		{
																			$customer_type = $db->rp_getValue("packing_slip","customer_type","id='".$_REQUEST['packing_slip_id']."'");
																			$customer_id = $db->rp_getValue("packing_slip","customer_id","id='".$_REQUEST['packing_slip_id']."'");
																			$packingslipid = $db->rp_getValue("packing_slip","id","id='".$_REQUEST['packing_slip_id']."'");

																			$dispatchid = $db->rp_getValue("packing_slip","dispatch_id","id='".$_REQUEST['packing_slip_id']."'");
																		}
																		
																		if($_REQUEST['mode']=="edit" && $_REQUEST['id']!="")
																		{
																			$customer_id = $db->rp_getValue("invoice_new", "customer_id", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
																			$dispatchid = $db->rp_getValue("invoice_new", "dispatch_ids", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
																			$packingslipid = $db->rp_getValue("packing_slip","id","dispatch_id = '".$dispatchid."' AND isDelete=0");
																		}

																		if ($_REQUEST['mode'] == "edit") {
																			$disabled = "disabled";
																		} else {
																			$disabled = "";
																		}
																		?>
																		<label>Select Customer Type<code>*</code></label>
																		
																		<input type="hidden" name="customer_selectd_id" id="customer_selectd_id" value="<?php echo $customer_id; ?>">
																		
																		<input type="hidden" name="packingslip_selectd_id" id="packingslip_selectd_id" value="<?php echo $packingslipid; ?>">

																		<input type="hidden" name="dispatch_selectd_id" id="dispatch_selectd_id" value="<?php echo $dispatchid; ?>">

																		<select  class="form-control"  id="customer_type" name="customer_type" onchange="getCustomer(this.value)" <?php echo $disabled; ?>>
																			<option value="">Select Customer Type</option>
																			<?php
																			$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
																			if ($cust_R) {
																				while ($C = mysqli_fetch_assoc($cust_R)) {
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
																		<!-- onchange="getDispatchList(this.value)" -->
																		<label>Select Customer<code>*</code></label>
																		<select class="form-control" name="customer_id" placeholder="Select Customer" id="customer_id" onchange="getPackingSlipList(this.value)" <?php echo $disabled; ?>>
																			<option value="">Select Customer</option>
																		</select>
																		<p class="help-block"></p>
																	</div>
																	<div class="form-group">
																		<label>Select Packing Slip<code>*</code></label>
																		<select class="form-control" name="packing_slip[]" placeholder="Select Packing Slip" id="packing_slip" onchange="getDispatchList(this.value)" multiple="">
																			<option value="">Select Packing Slip</option>
																		</select>
																		<p class="help-block"></p>
																	</div>
																	<div class="form-group">
																		<label>Select Dispatch<code>*</code></label>
																		<select class="form-control" name="dispatch_id[]" placeholder="Select Dispatch" id="dispatch_id" onchange="getDispatchView()" multiple="">
																			<option value="">Select Dispatch</option>
																		</select>
																		<p class="help-block"></p>
																	</div>

																	<!-- CUSTOMER EDIT BUTTON -->
																	<?php
																	if($_REQUEST['mode']=="edit")
																	{
																		?>
																		<div class="form-group" style="text-align: right;margin-right: 64px;">
																			<a class="btn btn-success" href='#CustomerEditModel' data-title="Customer Details" data-customer_id="<?= $customer_id; ?>" data-mode="invoice" data-quotation_id="<?= $_REQUEST['id']; ?>"data-toggle='modal'><i class="fa fa-edit"></i> Edit Customer</a>

																			<a class="btn btn-primary" href='#CustomerChangeModel' data-title="Customer Details" data-customer_id="<?= $customer_id; ?>" data-mode="invoice" data-quotation_id="<?= $_REQUEST['id']; ?>"data-toggle='modal'><i class="fa fa-pencil"></i> Change Customer</a>
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
																			<div class="col-md-7 value" name="name_gstin" id="name_gstin"><?php echo $customer_gstin; ?></div>
																		</div>
																		<div class="row static-info address">
																			<div class="col-md-5 name"> Pricelist : </div>
																			<div class="col-md-7 value" name="name_pricelist" id="name_pricelist"><?php echo $customer_pricelist; ?></div>
																		</div>
																	</div>
																</div>
																<div class="col-md-4 col-sm-4">
																	<div class="form-group">
																		<label>Shipping Address<code>*</code></label>
																		<textarea class="form-control" id="shipping_address" name="shipping_address" value="<?php $shipping_address ?>" rows="7"><?php echo $shipping_address ?></textarea>
																		<p class="help-block"></p>
																	</div>
																	<div class="form-group">
																		<label>Billing Address<code>*</code></label>
																		<textarea class="form-control" id="billing_address" name="billing_address" value="<?php $billing_address ?>" rows="7"><?php echo $billing_address ?></textarea>
																		<p class="help-block"></p>
																	</div>
																</div>
																<div class="col-md-4 col-sm-4">
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Invoice No. <code>*</code></label>
																			<input type="text" readonly="" class="form-control" name="invoice_no" id="invoice_no" value="<?php echo $invoice_no; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Invoice Date <code>*</code></label>
																			<input type="text" readonly="" class="form-control" name="invoice_date" id="invoice_date" value="<?php echo $invoice_date; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
																</div>
																<div class="col-md-4 col-sm-4">
																	<!-- <div class="col-md-6">
																		<div class="form-group">
																			<label>Packing & Forwarding Charge</label>
																			<input type="text" class="form-control" name="packing_charge" id="packing_charge" value="<?php echo $packing_charge; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div> -->
																	<!-- <div class="col-md-6">
																		<div class="form-group">
																			<label>Transport</label>
																			<input type="text" class="form-control" name="transport_charge" id="transport_charge" value="<?php echo $transport_charge; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div> -->

																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Vendor Code</label>
																			<input type="text" class="form-control" name="vendor_code" id="vendor_code" value="<?php echo $vendor_code; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>

																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Tendor Code</label>
																			<input type="text" class="form-control" name="tendor_code" id="tendor_code" value="<?php echo $tendor_code; ?>" />
																			<p class="help-block"></p>
																		</div>	
																	</div>

																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Transport By</label>
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
																			<label>Transporter Deatil</label>
																			<select class="form-control" name="transport_name" id="transport_name">
																				<option value="">Select Transporter Datail</option>
																			</select>
																			<p class="help-block"></p>
																		</div>
																	</div>

																	<div class="col-md-6">
																		<div class="form-group">
																			<label>Select Warehouse</label>
																			<select class="form-control" name="warehouse_id[]" id="warehouse_id" multiple="">
																				<option value="">Select Warehouse</option>
																				<?php
																					$WarehouseR=$db->rp_getData('warehouse',"*","isDelete=0","",0);
																					while($WarehouseD=mysqli_fetch_assoc($WarehouseR))
																					{
																						?>
																						<option <?=(in_array($WarehouseD['id'], $warehouse_id))?"selected":"";?> value="<?php echo $WarehouseD['id']; ?>">
																						<?php echo $WarehouseD['name']; ?>
																						</option>
																						<?php
																					}
																					?>
																			</select>
																			<p class="help-block"></p>
																		</div>
																	</div>

																	<div class="col-md-6">
																		<div class="form-group">
																			<label>E-Way Bill No</label>
																			<input type="text" class="form-control" name="way_bill_no" id="way_bill_no" value="<?php echo $way_bill_no; ?>" />
																			<p class="help-block"></p>
																		</div>
																	</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row" style="margin-right: -1px; margin-left: -1px;">
										<div class="col-md-12 col-sm-12">
											<div class="portlet grey-cascade box" style="margin-bottom: 0px;">
												<div class="portlet-title">
													<div class="caption">
														<i class="fa fa-user"></i>
														<span class="caption-subject bold uppercase"> INVOICE ITEM</span>
													</div>
												</div>

												<div class="portlet-body">
													<div class="row">
														<div class="col-sm-12 col-lg-12 col-xs-12 invoice-item-data">
														</div>

														<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
															<div class="col-md-5">
																<div class="form-group">
																	<label>Terms & Conditions<code>*</code></label>
																	<textarea class="form-control" id="terms_comdition" rows="5" name="terms_comdition" style="resize: vertical;"><?php if($_REQUEST['mode']=="add"){ echo $db->custom_html_entity_decode2(INVOICE_DEFAULT_TERMS);} else { echo str_replace('rn','',$terms_comdition);}?></textarea>
																	<p class="help-block"></p>
																</div>
															</div>
															<div class="col-md-5">
																<div class="form-group">
																	<label>Regards<code>*</code></label>
																	<textarea class="form-control" id="faithfully" name="faithfully" style="resize: vertical;">
																		<?php 
																			if($_REQUEST['mode']=="add")
																			{ 
																				echo $db->custom_html_entity_decode2($_SESSION[SITE_SESS.'SESS_NAME']."<br/>".$db->rp_getValue("sales_executive","phone","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'"));
																			} 
																			else 
																			{ 
																				echo str_replace('rn','',$faithfully);
																			}
																		?>
																			
																		</textarea>
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
	<script type="text/javascript" src="js/fSelect.js"></script>
	<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
	<script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		var loadersetunset = 0;
		$("#packing_charge").numeric();
		$("#transport_charge").numeric();
		$("#packing_slip").fSelect();
		$("#dispatch_id").fSelect();
		$("#warehouse_id").fSelect();

		CKEDITOR.replace('terms_comdition');
	    CKEDITOR.instances.terms_comdition.getData().replace(/(\r\n|\n|\r)/gm,"");

	    CKEDITOR.replace('faithfully');
	    CKEDITOR.instances.faithfully.getData().replace(/(\r\n|\n|\r)/gm,"");

		var mode = "<?= $_REQUEST['mode'] ?>";
		const format = (num = 0, decimals = 3) => num.toLocaleString('en-US', {
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
		});
		$('#invoice_date').datepicker({
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
			$("#name_gstin").html(gstin);
			$("#name").html(cname);
			$("#name_pricelist").html(pricelist);
			$(".gst_type").html(gst_type);
			// $("#place_of_supply").val(place_of_supply.toUpperCase());
		})


		var mode = '<?= $_REQUEST['mode']; ?>';
		var packing_slip_id = '<?= $_REQUEST['packing_slip_id']; ?>';

		if(mode=="add" && packing_slip_id!="")
		{
			var loadersetunset = 1;
			var ctype = $("#customer_type").val();
			var selected_value = $("#customer_selectd_id").val();
			var packingslip_selectd_id = $("#packingslip_selectd_id").val();
			var dispatch_selectd_id = $("#dispatch_selectd_id").val();
			getCustomer(ctype,selected_value);
			getPackingSlipList(selected_value,packingslip_selectd_id);
			getDispatchList(packingslip_selectd_id,dispatch_selectd_id);
			getDispatchView(dispatch_selectd_id);
			getCustomerList(selected_value);
		}

		function getCustomer(ctype,selected_value="") {
			// $('#customer_id').select2("val", "");
			$.ajax({
				type: "post",
				url: "ajax_get_customer.php",
				data: "customer_type=" + ctype+"&selected_value=" + selected_value,
				beforeSend: function() {
				},
				success: function(result) {
					setTimeout(function() {
						$('#customer_id').select2("destroy");
						$('#customer_id').html(result);
						$('#customer_id').select2();
					});
				}
			})
		}

		function getDispatchList(id) {
			var mode = "<?=$_REQUEST['mode']?>"; 
			var dispatch_selectd_id = $("#dispatch_selectd_id").val();
			$.ajax({
				type: "post",
				url: "ajax_dispatchlist_from_packing_slip.php",
				data: "packing_slip_id=" + id + "&selected_value=" + dispatch_selectd_id+"&mode=" + mode,
				beforeSend: function() {
				},
				success: function(result) {
					setTimeout(function() {
						$("#dispatch_id").fSelect("destroy");
						$("#dispatch_id").html(result);
						$("#dispatch_id").fSelect("create");
					});
				}
			})
		}

		function getPackingSlipList(cid,packingslip_selectd_id="") {
			var mode = "<?=$_REQUEST['mode']?>";
			$.ajax({
				type: "post",
				url: "ajax_packingslip_from_customer.php",
				data: "cid=" + cid +"&selected_value="+packingslip_selectd_id+"&mode=" + mode,
				beforeSend: function() {
				},
				success: function(result) {
					setTimeout(function() {
						$("#packing_slip").fSelect("destroy");
						$("#packing_slip").html(result);
						$("#packing_slip").fSelect("create");
					});
				}
			})
		}

		function getDispatchView() {
			var dispatch_id = $("#dispatch_id").val();
			var invoice_id = '<?= $_REQUEST['id'] ?>';
			if(dispatch_id==null) {
				dispatch_id = 0;
			}
			var mode = '<?=$_REQUEST['mode'] ?>';
			$.ajax({
				type: "post",
				url: "ajax_dispatchdetail_from_order.php",
				data: "dispatch_id=" + dispatch_id+"&invoice_id="+invoice_id+"&mode="+mode,
				beforeSend: function() {
					$(".invoice-item-data").html("");
				},
				success: function(result) {
					setTimeout(function() {
						$(".invoice-item-data").html(result);
						var dispatchids = $("#dispatch_id").val();
						getorderAddress(dispatchids);
					});
				}
			})
		}


		function getorderAddress() {
			var dispatch_id = $("#dispatch_id").val();
			if(dispatch_id==null) {
				dispatch_id = 0;
			}
			var mode = "<?=$_REQUEST['mode']?>"; 
			var invoice_id = "<?=$_REQUEST['id']?>"; 
			$.ajax({
				type: "post",
				url: "ajax_get_shipping_from_order.php",
				data: "dispatch_id=" + dispatch_id+"&id="+invoice_id+"&mode="+mode,
				beforeSend: function() {
				},
				success: function(result) {
					setTimeout(function() {
						var data =$.parseJSON(result);
						$("#billing_address").val(data.billing_address);
						$("#shipping_address").val(data.shipping_address);
						$("#transport_through").select2("val",data.transport_through);
						$("#transport_name").val(data.transport_name);
						getTransportname(data.transport_through,data.transport_name)
						$("#warehouse_id").fSelect("destroy");
						$("#warehouse_id").val(data.warehouse_id);
			        	$("#warehouse_id").fSelect('create');
			        	$("#warehouse_id").attr("readonly","readonly");
						$("#vendor_code").val(data.vendor_code);
						$("#tendor_code").val(data.tendor_code);
						$("#way_bill_no").val(data.way_bill_no);
					});
				}
			})
		}

		function getCustomerList(cust_id) 
		{
			$.ajax({
				type: "post",
				url: "ajax_get_invoice_customer.php",
				data: "selected_value=" + cust_id,
				beforeSend: function() {
				},
				success: function(result) 
				{
					setTimeout(function() 
					{
						var data =$.parseJSON(result);
						$("#name_value").html(data.cname);
						$("#name_phone").html(data.phone);
						$("#name_address").html(data.address);
						$("#name_state").html(data.state);
						$("#name_gstin").html(data.gst);
						$("#name").html(data.cname);
						$("#name_pricelist").html(data.price_list_name);
						$(".gst_type").html(data.gst_type);
					});
				}
			})
		}

		function check_form() 
		{
			$(".form-body").children().removeClass("has-error");
			var isValid = true;
			<?php if ($_REQUEST['mode'] == "add") 
			{
				?>
				if ($("#customer_type").val() == "" || $("#customer_type").val().split(" ").join("") == "") 
				{
					vd = aj.error('customer_type', "Please Select Customer Type", "add_error");
					isValid = false;
				}
				if ($("#customer_id").val() == "" || $("#customer_id").val().split(" ").join("") == "") 
				{
					vd = aj.error('customer_id', "Please Select Customer.", "add_error");
					isValid = false;
				}
				if ($("#invoice_date").val() == "" || $("#invoice_date").val().split(" ").join("") == "") 
				{
					vd = aj.error('invoice_date', "Please Enter Invoice Date.", "add_error");
					isValid = false;
				}
				<?php 
			} 
			?>
			if (isValid) 
			{
				var r = confirm("Are You sure want to Save this Invoice??");
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
		$(".form-control").bind("keyup change", function() 
		{
			if ($(this).parent().hasClass("has-error")) 
			{
				$(this).parent().removeClass("has-error");
				$(this).parent().find('p.help-block').html("");
			}
		});
		setTimeout(function() {
			$("#dispatch_id").select2("destroy");
			$("#dispatch_id").fSelect();
		},500);
	</script>

	<script type="text/javascript">
		var mode = '<?= $_REQUEST['mode']; ?>';
		if(mode=="edit")
		{
			var ctype = $("#customer_type").val();
			var selected_value = $("#customer_selectd_id").val();
			var packingslip_selectd_id = $("#packingslip_selectd_id").val();
			var dispatch_selectd_id = $("#dispatch_selectd_id").val();
			var invoice_id = '<?= $_REQUEST['id'] ?>';
			getCustomer(ctype,selected_value);
			getPackingSlipList(selected_value,packingslip_selectd_id);
			getDispatchList(packingslip_selectd_id,dispatch_selectd_id);
			getDispatchView(dispatch_selectd_id,invoice_id);
			getCustomerList(selected_value);
			getorderAddress(dispatch_selectd_id);
		}

		function getTransportname(id,transport_name_selected_id="")
		{	
			$.ajax({
				type: "post",
				url: "ajax_get_transport_detail.php",
				data: "id=" + id+"&selected_id="+transport_name_selected_id,
				beforeSend: function() {
				},
				success: function(result) 
				{
					setTimeout(function() 
					{
						$("#transport_name").select2("destroy");
						$('#transport_name').html(result);
						$("#transport_name").select2();
					});
				}
			})
		}
	</script>
</body>
</html>