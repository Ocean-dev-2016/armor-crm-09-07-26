<?php
$page_id=588;$page_slug='invoice_page';
$page_slug  = 'add_invoice';
$ctable     = "invoice_new";
$ctable1    = "Invoice";
$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"dealer_invoice_manage.php","title"=>$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
include("../include/class.invoice_new.php");
$InvoceNew = new InvoceNew();
$uid       = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$utype     = $_SESSION[SITE_SESS . '_ADMIN_TYPE'];
$invoice_date = date('d-m-Y');
$packing_charge = 0;
$transport_charge = 0;

if (isset($_REQUEST['submit'])) 
{
$isActive					            = 1;
$detail['isDelete']			            = 0;
$detail['remarks']			            = html_entity_decode($_REQUEST['remarks']);
$detail['chalan_no']	                = isset($_REQUEST['chalan_no']) ? $db->clean($_REQUEST['chalan_no']) : "";
$detail['po_no']		                = isset($_REQUEST['po_no']) ? $db->clean($_REQUEST['po_no']) : "";
$detail['po_date']		                = date('Y-m-d', strtotime($_REQUEST['po_date']));
$detail['terms_comdition']	            = isset($_REQUEST['terms_comdition'])?html_entity_decode($_REQUEST['terms_comdition']):"";
$detail['faithfully']	                = isset($_REQUEST['faithfully'])?html_entity_decode($_REQUEST['faithfully']):"";
$detail['vendor_code']	                = isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
$detail['tendor_code']	                = isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
$detail['uid'] 				            = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$detail['cash_discount']	            = isset($_REQUEST['cash_discount'])?$db->clean($_REQUEST['cash_discount']):"";
$detail['additional_discount']			= isset($_REQUEST['additional_discount'])?$db->clean($_REQUEST['additional_discount']):"";
$detail['cash_discount_amount']			= isset($_REQUEST['cash_discount_amount'])?$db->clean($_REQUEST['cash_discount_amount']):"";
$detail['additional_discount_amount']	= isset($_REQUEST['addtional_discount_amount'])?$db->clean($_REQUEST['addtional_discount_amount']):"";
$detail['gst_apply_flag']	            = isset($_REQUEST['gst_apply_flag'])?$db->clean($_REQUEST['gst_apply_flag']):"";
$detail['tcs_apply_flag']	            = isset($_REQUEST['tcs_apply_flag'])?$db->clean($_REQUEST['tcs_apply_flag']):"";
$detail['transport_charge_gst']	        = isset($_REQUEST['transport_charge_per'])?$db->clean($_REQUEST['transport_charge_per']):0;
$detail['packing_charge_gst']	        = isset($_REQUEST['packing_charge_per'])?$db->clean($_REQUEST['packing_charge_per']):0;
$detail['cd_gst']	                    = isset($_REQUEST['cd_gst'])?$db->clean($_REQUEST['cd_gst']):"";
$detail['ad_gst']	                    = isset($_REQUEST['ad_gst'])?$db->clean($_REQUEST['ad_gst']):"";
$detail['warehouse_id']                 = isset($_REQUEST['warehouse_id'])?$db->clean(implode(",", $_REQUEST['warehouse_id'])):"";
$detail['way_bill_no'] 					= (isset($_REQUEST['way_bill_no']) && $_REQUEST['way_bill_no']!="")?$_REQUEST['way_bill_no']:"";
$detail['lut_no']	                	= isset($_REQUEST['lut_no'])?trim($_REQUEST['lut_no']):"";
$detail['total_parcel']	                = isset($_REQUEST['total_parcel'])?trim($_REQUEST['total_parcel']):"";
$detail['total_weight']	                = isset($_REQUEST['total_weight'])?trim($_REQUEST['total_weight']):"";


// print_r($detail);exit();

$product_id                = $_REQUEST['product_id'];
$qty                       = $_REQUEST['qty'];
$original_price            = $_REQUEST['original_price'];
$price                     = $_REQUEST['price'];
$pro_name                  = $_REQUEST['pro_name'];
$weight_id                 = $_REQUEST['weight_id'];
$box_qty                   = $_REQUEST['box_qty'];
$bag                       = $_REQUEST['bag'];
$loose                     = $_REQUEST['loose'];
$brand_id                  = $_REQUEST['brand_id'];
$pro_description           = $_REQUEST['pro_description'];
$cd_discount               = $_REQUEST['cd_discount'];
$ad_discount               = $_REQUEST['ad_discount'];
$gst_amount_item           = $_REQUEST['gst_amount_item'];
$taxable_amount            = $_REQUEST['taxable_amount'];
$sub_total                 = $_REQUEST['sub_total'];
$other_charge              = $_REQUEST['other_charge'];
$fright_charge             = $_REQUEST['fright_charge'];
$discount                  = $_REQUEST['discount'];
$discount_amount           = $_REQUEST['discount_amount'];


$size[]       = sizeof($product_id);
$size[]       = sizeof($qty);
$size[]       = sizeof($price);
$size[]       = sizeof($pro_name);
$size[]       = sizeof($pro_description);
$brand_id[]   = sizeof($brand_id);
$weight_id[]  = sizeof($weight_id);
$box_qty[]    = sizeof($box_qty);
$loose[]      = sizeof($loose);

$value_check = sizeof($product_id);

if (in_array($value_check, $size)) 
{
$isValidArray = true;
} 
else 
{
$isValidArray = false;
}

if ($isValidArray && !empty($product_id)) 
{
for ($i = 0; $i < sizeof($product_id); $i++) 
{
$item[] = array("qty" => $qty[$i], "pid" => $product_id[$i], "original_price" => $original_price[$i], "price" => $price[$i], "pro_name" => $pro_name[$i], "weight_id" => $weight_id[$i], "cartoon_qty" => $box_qty[$i], "box_qty" => $bag[$i], "loose"=>$loose[$i], "brand_id" => $brand_id[$i] , "pro_description" =>$pro_description[$i],"cd_discount"=>$cd_discount[$i],"ad_discount"=>$ad_discount[$i],"gst_amount_item"=>$gst_amount_item[$i],"taxable_amount"=>$taxable_amount[$i],"sub_total"=>$sub_total[$i],"other_charge"=>$other_charge[$i],"fright_charge"=>$fright_charge[$i],"dispatch_item_type"=>1,"discount" => $discount[$i],"discount_amount"=>$discount_amount[$i]);
}
}
//print_r($item); exit;
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add") 
{
if($_REQUEST['packing_slip_id']!="")
{
$detail['dispatch_ids'] = $db->rp_getValue("packing_slip","dispatch_id","id='".$_REQUEST['packing_slip_id']."'");
}
else
{
$detail['dispatch_ids'] = 0;	
}
$detail['cid']                = $db->clean($_REQUEST['customer_id']);
$detail['customer_id']        = $db->clean($_REQUEST['customer_id']); 
$detail['invoice_date']       = date('Y-m-d', strtotime($_REQUEST['invoice_date']));
$detail['terms_comdition']	  = isset($_REQUEST['terms_comdition'])?html_entity_decode($_REQUEST['terms_comdition']):"";
$detail['faithfully']	      = isset($_REQUEST['faithfully'])?html_entity_decode($_REQUEST['faithfully']):"";
$detail['transport_name']	  = isset($_REQUEST['transport_name'])?trim($_REQUEST['transport_name']):"";
$detail['transport_through']  = isset($_REQUEST['transport_through'])?trim($_REQUEST['transport_through']):"";
$detail['transport_charge']	  = isset($_REQUEST['transport_charge'])?trim($_REQUEST['transport_charge']):"";
$detail['packing_charge']	  = isset($_REQUEST['packing_charge'])?$db->clean($_REQUEST['packing_charge']):"";
$detail['shipping_address']	  = isset($_REQUEST['shipping_address'])?trim($_REQUEST['shipping_address']):"";
$detail['billing_address']	  = isset($_REQUEST['billing_address'])?trim($_REQUEST['billing_address']):"";
$detail['name_gstin']	  	  = isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";
$detail['lut_no']	  	  	  = isset($_REQUEST['lut_no'])?trim($_REQUEST['lut_no']):"";
$detail['total_parcel']	  	  = isset($_REQUEST['total_parcel'])?trim($_REQUEST['total_parcel']):"";
$detail['total_weight']	  	  = isset($_REQUEST['total_weight'])?trim($_REQUEST['total_weight']):"";
$detail['sales_executive_id']	  	  = isset($_SESSION[SITE_SESS.'REFERANCE_ID'])?trim($_SESSION[SITE_SESS.'REFERANCE_ID']):"";
// $detail['sales_executive_id'] 			= $_SESSION[SITE_SESS.'REFERANCE_ID'];
if ($rights['insert_flag'] != 1) {
$db->rp_location('access_denied.php?msg=delete_access_denied');
}

$reply = $InvoceNew->AddInvocie($detail, $item);
if ($reply['ack'] == 1) 
{
$detail['cash_discount_flag'] 			= 0;
$detail['order_id'] 					= $reply['order_id'];
$detail['sales_executive_id'] 			= $_SESSION[SITE_SESS.'REFERANCE_ID'];
$detail['name_gstin']	  				= isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";
$detail['vendor_code']	      			= isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
$detail['tendor_code']	      			= isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
$detail['cash_discount']				= isset($_REQUEST['cash_discount'])?$db->clean($_REQUEST['cash_discount']):"";
$detail['additional_discount']			= isset($_REQUEST['additional_discount'])?$db->clean($_REQUEST['additional_discount']):"";
$detail['cash_discount_amount']			= isset($_REQUEST['cash_discount_amount'])?$db->clean($_REQUEST['cash_discount_amount']):"";
$detail['additional_discount_amount']	= isset($_REQUEST['addtional_discount_amount'])?$db->clean($_REQUEST['addtional_discount_amount']):"";
$detail['gst_apply_flag']				= isset($_REQUEST['gst_apply_flag'])?$db->clean($_REQUEST['gst_apply_flag']):""; 
$detail['tcs_apply_flag']				= isset($_REQUEST['tcs_apply_flag'])?$db->clean($_REQUEST['tcs_apply_flag']):"";
$detail['round_off']									= isset($_REQUEST['round_off'])?$db->clean($_REQUEST['round_off']):"";

$InvoceNew->PlaceInvociePanel($detail);

$customer_id = $db->rp_getValue("invoice_new","customer_id","id='".$reply['order_id']."'",0);
$dispatch_id = $db->rp_getValue("invoice_new","dispatch_ids","id='".$reply['order_id']."'",0);
$packing_slip_id = $db->rp_getValue("packing_slip","id","customer_id='".$customer_id."' AND dispatch_id='".$dispatch_id."' AND status=0",0);
$db->rp_update("packing_slip",array("status"=>1),"id='".$packing_slip_id."'",0);

$db->addSuccessMessage($reply['ack_msg']);
$db->rp_location("dealer_invoice_manage.php");
$db->rp_location("dealer_invoice_manage.php?msg=inserted");
} 
else 
{
$db->addErrorMessage($reply['ack_msg']);
}
}

else if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "edit") 
{
if ($rights['update_flag'] != 1) 
{
$db->rp_location('access_denied.php?msg=delete_access_denied');
}

$detail['customer_id']        = $db->clean($_REQUEST['edit_customer_id']);
$detail['sales_executive_id'] = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
$detail['cid']                = $db->clean($_REQUEST['edit_customer_id']);
$detail['order_id']           = $db->clean($_REQUEST['id']);
$detail['terms_comdition']    = html_entity_decode($_REQUEST['terms_comdition']);
$detail['faithfully']         = html_entity_decode($_REQUEST['faithfully']);
$detail['transport_name']	  = isset($_REQUEST['transport_name'])?trim($_REQUEST['transport_name']):"";
$detail['transport_through']  = isset($_REQUEST['transport_through'])?trim($_REQUEST['transport_through']):"";
$detail['transport_charge']   = isset($_REQUEST['transport_charge'])?trim($_REQUEST['transport_charge']):"";
$detail['packing_charge']	  = isset($_REQUEST['packing_charge'])?$db->clean($_REQUEST['packing_charge']):"";
$detail['shipping_address']	  = isset($_REQUEST['shipping_address'])?trim($_REQUEST['shipping_address']):"";
$detail['billing_address']	  = isset($_REQUEST['billing_address'])?trim($_REQUEST['billing_address']):"";
$detail['name_gstin']	      = isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";
$detail['vendor_code']	      = isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
$detail['tendor_code']	      = isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
$detail['lut_no']	  	  	  = isset($_REQUEST['lut_no'])?trim($_REQUEST['lut_no']):"";
$detail['round_off']		  = isset($_REQUEST['round_off'])?$db->clean($_REQUEST['round_off']):"";
$reply = $InvoceNew->UpdateInvoice($detail, $item);
if ($reply['ack'] == 1) 
{
$db->rp_update('quotation_detail', ['status' => 4], "id=".$quotation_id, $die=0);
unset($detail['order_date']);
$detail['cash_discount_flag'] = 0;
$detail['order_id'] = $reply['order_id'];
$detail['sales_executive_id'] = "";
$InvoceNew->PlaceInvociePanel($detail);

/*update in account*/
$invoice_id         = $db->clean($_REQUEST['id']);
require_once('../include/class.system.php');
$system = new System();

$customer_id = $db->rp_getValue("invoice_new","customer_id","id='".$invoice_id."'",0);
$status = $db->rp_getValue("invoice_new","status","id='".$invoice_id."'",0);
$AccountInfo=$db->rp_getData("account","*","cid='".$customer_id."'","",0);
$AccountInfo=mysqli_fetch_assoc($AccountInfo);
if($AccountInfo && $status==1)
{
$AccountID=$AccountInfo['id'];
$AccountNo=$AccountInfo['acc_no'];
$count = $db->rp_getTotalRecord("account_transaction","reference_id='".$invoice_id."' AND reference_table='invoice' AND isDelete=0");
if($count>=0)
{
$delete = $db->rp_update("account_transaction",array("isDelete"=>1),"reference_id='".$invoice_id."' AND reference_table='invoice'",0);

$debit="-".$db->rp_getValue("invoice_new","grand_total","id='".$invoice_id."'",0);
$grand_total=$db->rp_getValue("invoice_new","grand_total","id='".$invoice_id."'",0);
$payment_date=date('Y-m-d');
$payment_type = 0;
$remark = "Invoice Entry Of Invoice No. <a target='_blank' href='invoice_viewer.php?invoice_id=".$invoice_id."'>". $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'",0)."</a>";

$Columns=array("cid","account_id","account_no","type","debit","amount","reference_id","reference_table","description","payment_date");
$Values=array($customer_id,$AccountID,$AccountNo,$payment_type,$debit,$grand_total,$invoice_id,"invoice",$remark,$payment_date);
$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);	
}
}
/*update in account*/

// invoice remaining update		 
$invoice_tot_receipt_amt = $db->rp_getValue("account_transaction","SUM(credit)","invoice_id='".$invoice_id."' AND isDelete=0 AND reference_table='payment'");

$invoice_tot_receipt_amt = ($invoice_tot_receipt_amt)?$invoice_tot_receipt_amt:0;

$invoice_remaining_amt = $grand_total - $invoice_tot_receipt_amt;

$db->rp_update("invoice_new",array("receipt_amount"=>$invoice_tot_receipt_amt,"remaining_amount"=>$invoice_remaining_amt),"id='".$invoice_id."'",0);

$db->rp_update("account_transaction",array("remaining_amount"=>$invoice_remaining_amt),"reference_id='".$invoice_id."' AND reference_table='invoice' AND isDelete=0",0); 

$db->rp_update("account_transaction",array("cid"=>$customer_id,"account_id"=>$AccountID,"account_no"=>$AccountNo),"invoice_id='".$invoice_id."' AND reference_table='payment' AND isDelete=0",0); 

// invoice remaining update

$db->addSuccessMessage($reply['ack_msg']);
$type = $db->rp_getValue("orders", "customer_type", "id='" . $reply['type'] . "'");
$db->rp_location("dealer_invoice_manage.php");
} 
else 
{
$db->addErrorMessage($reply['ack_msg']);
$db->rp_location("invoice_crud_new.php?mode=edit&id=".$_REQUEST['id']);
}
}
}	

if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0 && $_REQUEST['mode'] == "edit") {
if ($rights['update_flag'] != 1 || $generate_invoice_flag == 1) 
{
$db->rp_location('access_denied.php?msg=delete_access_denied');
}

$detail['id'] = $_REQUEST['id'];
$OrderNo = $db->rp_getValue("invoice_new", "order_no", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
$Order = $db->rp_getValue("invoice_new", "customer_id", "id=" . $_REQUEST['id'] . " AND isDelete=0", 0);
$customer_name = $db->rp_getValue("customer", "company_name", "id=" . $Order . " AND isDelete=0", 0);
$page_title = ucwords($_REQUEST['mode']) . '&nbsp' . "Invoice";
$reply = $InvoceNew->GetOrder($detail);
$item_info = $InvoceNew->GetOrderItems($detail);
if ($reply['ack'] == 1) 
{
$id = $_REQUEST['id'];
$result = $reply['result'];
extract($result);
$invoice_date = date("d.m.Y", strtotime($invoice_date));
}

if ($item_info['ack'] == 1) 
{
$store_inward_id = $_REQUEST['id'];
$item_info = $item_info['result'];
} 
else 
{
$item_info = array();
}
}


if (isset($_REQUEST['packing_slip_id']) && $_REQUEST['packing_slip_id'] > 0 && $_REQUEST['mode'] == "add") 
{
$packing_slip_id = $_REQUEST['packing_slip_id'];
$detail['id'] = $_REQUEST['packing_slip_id'];

$reply = $InvoceNew->GetInvocenew($detail);
// print_r($reply);
$reply1=$InvoceNew->GetInquirynewItem($detail);
// print_r($reply1);exit;
if ($reply['ack'] == 1) 
{
$result = $reply['result'];
extract($result);
}

if($reply1['ack']==1)
{
$store_inward_id=$_REQUEST['id'];
$item_info=$reply1['result'];
}
else
{
$item_info=array();
}
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

/*delete Customer Leager */
$db->rp_update("account_transaction",array("isDelete"=>1),"reference_id='".$_REQUEST['id']."' AND reference_table='invoice'",0);
/*delete Customer Leager */

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
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
<link href="assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>

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
$back = 'dealer_invoice_manage.php';
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
					<span class="caption-subject bold uppercase"> Invoice DETAIL</span>
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
									if ($_REQUEST['mode'] == "edit" || $_REQUEST['mode'] == "add" && (isset($_REQUEST['packing_slip_id'])) ) {
										$disabled = "disabled";
									} else {
										$disabled = "";
									}

									if($_REQUEST['mode']=="add" && $_REQUEST['packing_slip_id']!="")
									{
										$customer_type = $db->rp_getValue("packing_slip","customer_type","id='".$_REQUEST['packing_slip_id']."'");
										$customer_id = $db->rp_getValue("packing_slip","customer_id","id='".$_REQUEST['packing_slip_id']."'");
										
										$packingslipid = $db->rp_getValue("packing_slip","id","id='".$_REQUEST['packing_slip_id']."'");

										$dispatchid = $db->rp_getValue("packing_slip","dispatch_id","id='".$_REQUEST['packing_slip_id']."'");
									}
									?>
									<label>Select Customer Type<code>*</code></label>
									<?php
									if ((isset($_REQUEST['packing_slip_id']) && $_REQUEST['mode'] == "add")) 
									{
										?>
										<input type="hidden" name="customer_id" id="customer_id" value="<?= $customer_id; ?>" class="customer_id">
										<input type="hidden" name="customer_type" id="customer_type" value="<?= $customer_type; ?>">
										<?php
									} 
									?>
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
										<input type="hidden" name="edit_customer_id" id="edit_customer_id" value="<?= $customer_id; ?>" class="customer_id_s">
									<?php
									}
									?>
									<select class="form-control customer_id_s" name="customer_id" placeholder="Select Customer" id="customer_id"  type="text" <?php echo $disabled; ?>>
										<option value="">Select Customer</option>
										<?php
										if ($_REQUEST['mode'] == "edit" || $_REQUEST['mode'] == "add" || (isset($_REQUEST['packing_slip_id'])))
										{
											$customers = $db->rp_getData("executive", "*", "isDelete=0");
											if ($customers) 
											{
												while ($customer = mysqli_fetch_assoc($customers)) 
												{
													if ($customer['price_list_id'] != 0) 
													{
														$price_list_name = $db->rp_getValue("price_list", "pricelist_name", "id='" . $customer['price_list_id'] . "'");
													} 
													else 
													{
														$price_list_name = "N/A";
													}

													/*for merchnt export*/
													// if($customer['type_of_executive']==7)
													if($customer['type_of_executive']==8)
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
											        	if (strtolower(CLIENT_STATE) == strtolower($customer['state'])) 
											        	{
															$gst_type = "(CGST:9%,SGST:9%)";
														} 
														else
														{
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
								if($_REQUEST['mode']=="edit" && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0)
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
								<?php
								if(isset($_REQUEST['packing_slip_id']) && $_REQUEST['mode'] == "add")
								{
									$gst = $db->rp_getValue("executive","gst","id='".$customer_id."'");

									$pricelistid =  $db->rp_getValue("executive","price_list_id","id='".$customer_id."'"); 
									$price_list_name=$db->rp_getValue("price_list","pricelist_name","id='".$customer_id."'");
									?>
									<div class="form-group">
										<div class="row static-info phone">
											<div class="col-md-5 name"> Name : </div>
											<div class="col-md-7 value" name="name" id="name"><?php echo $db->rp_getValue("executive","cname","id='".$customer_id."'"); ?> </div>
										</div>
										<div class="row static-info phone">
											<div class="col-md-5 name"> Phone : </div>
											<div class="col-md-7 value" name="name_phone" id="name_phone"><?php echo $db->rp_getValue("executive","phone","id='".$customer_id."'"); ?> </div>
										</div>
										<div class="row static-info address">
											<div class="col-md-5 name"> Address : </div>
											<div class="col-md-7 value" name="name_address" id="name_address"><?php echo $db->rp_getValue("executive","address","id='".$customer_id."'"); ?></div>
										</div>
										<div class="row static-info address">
											<div class="col-md-5 name"> State : </div>
											<div class="col-md-7 value" name="name_state" id="name_state"><?php echo $db->rp_getValue("executive","state","id='".$customer_id."'"); ?></div>
										</div>
										<div class="row static-info address">
											<div class="col-md-5 name"> GSTIN : </div>
											<div class="col-md-7">
												<input class="form-control" type="text" name="name_gstin" id="name_gstin" value="<?php echo $gst ?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="row static-info address">
											<div class="col-md-5 name"> Pricelist : </div>
											<div class="col-md-7 value" name="name_pricelist" id="name_pricelist"><?php if($price_list_name!=""){ echo $price_list_name;} else {echo "N/A"; } ?></div>
										</div>
									</div>
									<?php
								}
								else
								{
									?>
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
												<input class="form-control" type="text" name="name_gstin" id="name_gstin" value="<?php echo $name_gstin ?>" maxlength="15">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="row static-info address">
											<div class="col-md-5 name"> Pricelist : </div>
											<div class="col-md-7 value" name="name_pricelist" id="name_pricelist"><?php echo $customer_pricelist; ?></div>
										</div>
									</div>
									<?php
								}
								?>
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

									<a class="btn-sm btn-success" id='customer_id_c' href='#CustomerChangeShippingAddressModel' data-title="Customer Details" data-customer_id="<?= $dealer_id; ?>" data-mode="quotation_change_shipping" data-quotation_id="<?= $_REQUEST['id']; ?>"data-toggle='modal'><i class="fa fa-edit"></i> Change Shipping </a>

									<textarea class="form-control" id="shipping_address" name="shipping_address" value="<?php $shipping_address ?>" rows="6"><?php echo $shipping_address ?></textarea>
									<p class="help-block"></p>
								</div>
								<div class="form-group">
									<label>Billing Address<code>*</code></label>
									<textarea class="form-control" id="billing_address" name="billing_address" value="<?php $billing_address ?>" rows="6"><?php echo $billing_address ?></textarea>
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

								<div class="col-md-6">
									<div class="form-group">
										<label>Challan No.</label>
										<input type="text" class="form-control" name="chalan_no" id="chalan_no" value="<?php echo $chalan_no; ?>" />
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>PO No. </label>
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
										<label>Transport By</label>
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
										<label>Transporter Detail </label>
										<select class="form-control" name="transport_name" id="transport_name">
											<option value="">Select Transporter Detail</option>
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
										<label>LUT No.</label>
										<input style="resize: vertical;" type="text" class="form-control" name="lut_no" id="lut_no" value="<?php echo $lut_no ?>">
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label>Total Parcel</label>
										<input style="resize: vertical;" type="text" class="form-control" name="total_parcel" id="total_parcel" value="<?php echo $total_parcel ?>">
										<p class="help-block"></p>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label>Total Weight</label>
										<input style="resize: vertical;" type="text" class="form-control" name="total_weight" id="total_weight" value="<?php echo $total_weight ?>">
										<p class="help-block"></p>
									</div>
								</div>

								<!-- <div class="col-md-6">
									<div class="form-group">
										<label>E-Way Bill No</label>
										<input type="text" class="form-control" name="way_bill_no" id="way_bill_no" value="<?php echo $way_bill_no; ?>" />
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
					<span class="caption-subject bold uppercase"> Invoice ITEM</span>
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
													<div class="col-md-3">
														<div class="form-group">
															<label>Category <code>*</code></label>
															<select class="form-control" name="category_id" id="category_id" >
																<option value="">select Category</option>
																<?php
																$cat_r=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",1);
																while($cat_d=mysqli_fetch_assoc($cat_r))
																{
																?>
																<option value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option>
																<?php
																}
																?>
															</select>
															<p class="help-block"></p>
														</div>
													</div> 
													<div class="col-md-3">
														<div class="form-group">
															<label>Products <code>*</code></label>
															<select class="form-control" name="product_id" id="product_id">
																<option value="">Select Product</option>
															</select>
															<p class="help-block"></p>
														</div>
													</div>

													<div class="col-md-2">
														<div class="form-group">
															<label>Select Order Unit <code>*</code></label>
															<select class="form-control" name="bag_box_id" id="bag_box_id">
																<option value="">Select Order Unit</option>
																<option value="2">Inner Pack</option>
																<option value="3">Master Pack</option>
																<option value="1">Qty</option>
															</select>
															<p class="help-block"></p>
														</div>
													</div>

													<div class="col-md-1">
														<div class="form-group">
															<label>Quantity<code>*</code></label>
															<input type="text" class="form-control positive" name="qty" id="qty" value="" />
															<p class="help-block"></p>
														</div>
													</div>

													<div class="col-md-1" >
														<div class="form-group" style="margin-top:21px;">
															<label><b>Inner <br>Pack : </b> </label>
															<span class='inner_size'></span>
															<br/><label><b>Master <br>Pack : </b></label>
															<span class='outer_size'></span>
															<br/><label><b>Qty : </b></label>
															<span class='qty'></span>
															<input type="hidden" id="final_qty" value="">
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
											<div class="col-md-12" style="overflow:scroll;">
												<table border="1px" id="datatable_1" class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<!-- <th>No.</th> -->
															<th class="text-center">Delete</th>
															<th style="padding: 1px!important;" class="text-center" width="200px;">Product Name</th>
															<th style="padding: 1px!important;" class="text-center" width="50px;">Unit</th>

															<th style="padding: 1px!important;"  class="text-center">Inner Pack<br/>(NOS)</th>

															<th style="padding: 1px!important;"  class="text-center">Master Pack<br/>(in NOS)</th>

															<th style="padding: 1px!important;"  class="text-center">Loose Qty<br/><span class="f-10">(in NOS)</span></th>

															<th style="padding: 1px!important;" class="text-center">Stock</th>
															<th style="padding: 1px!important;" class="" width="100px;">Product Description</th>
															<th style="padding: 1px!important;" class="text-center" width="50px;">HSN Code</th>
															<!-- <th  class="text-center">Avail Stock</th>
															<th  class="text-center">Balance</th> -->
															<!-- <th  class="text-center">Bag<br/><span class="f-10">(inner in NOS)</span> </th>
															<th  class="text-center">Box<br/><span class="f-10">(outer in NOS)</span></th> -->
															<th style="padding: 1px!important;" class="text-center">Qty<br /><span class="f-10"></span></th>
															<th style="padding: 1px!important;" class="text-center">Price<br /><span class="f-10">(in INR)</span></th>
															<th style="padding: 1px!important;" class="text-center">Dis(Flat)</th>
															<th style="padding: 1px!important;" class="text-center">Dis(%)</th>
															<th style="padding: 1px!important;" class="text-center">Rate<br /><span class="f-10">(in INR)</span></th>
															<th style="padding: 1px!important;" class="text-center" width="150px;">Amount</th>
															<!-- <th style="padding: 1px!important;" class="text-center" width="150px;">CD DIS</th> -->
															<!-- <th style="padding: 1px!important;" class="text-center" width="150px;">AD DIS</th> -->
															<!-- <th style="padding: 1px!important;" class="text-center" width="150px;">Other Charges</th> -->
															<!-- <th style="padding: 1px!important;" class="text-center" width="150px;">Fright Charges</th> -->
															<th style="padding: 1px!important;" class="text-center" width="150px;">Taxable Amount</th>
															<th style="padding: 1px!important;" class="text-center" width="150px;">GST Amount</th>
															<th style="padding: 1px!important;min-width: 41px;" class="text-center" width="150px;"></th>
															<th class="text-center" width="150px;">Sub Total</th>
															
														</tr>
													</thead>
													<tbody>
														<?php
														if (!empty($item_info)) {
															$total_amount = 0;
															$total_gst = $igst_amount;
															foreach ($item_info as $i) {
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

																$top_cat_id = $db->rp_getValue("product","tcid","id='". $i['product_id']."'");
																
																$unit_id = $db->rp_getValue("product","unit_id","id='". $i['product_id']."'");
																$unit_name = $db->rp_getValue("unit","name","id='". $unit_id."'");

																$outer_size = $db->rp_getValue("product_weight_price","outer_size","product_id='". $i['product_id']."' AND weight_id='". $i['weight_id']."'",0);

																$inner_size = $db->rp_getValue("product_weight_price","inner_size","product_id='". $i['product_id']."' AND weight_id='". $i['weight_id']."'");


																?>
																<tr>

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
																	
																	<!-- <td><?php echo ++$count; ?><input type='hidden' name='count[]' value="<?php echo $count; ?>" class='count'></td> -->
																	<td style="min-width: 187px;padding: 1px!important;">
																		<input type='hidden' name='product_id[]' class='product_id' value="<?php echo $i['product_id']; ?>">
																		<input class="pro_id" type='hidden' name='pro_id[]' value="<?php echo $i['product_id'] . "" . $i['weight_id']; ?>">


																		<input type='hidden' style="text-align:right" name='subtotal[]' value="">
																		<input type='hidden' style="text-align:right" name='total[]' value="">
																		<input type='hidden' style="text-align:right" name='item_name[]'>
																		<input type='hidden' name='pro_name[]' value="<?php echo $i['product_name']; ?>" id='pro_name'>
																		<input type='hidden' name='weight_id[]' value="<?php echo $i['weight_id']; ?>" id='weight_id'>
																		<?php echo $i['product_name']." - ".$i['cat_no']." - <br/>".$i['top_cat_name']." - ".$i['category_name'];; ?>
																	</td>

																	<td  style="padding: 1px!important;"><?= $unit_name ?></td>


																	<td style="text-align:right;padding: 1px!important;">
																		<input class="inner_size" type='hidden' name='inner_size[]' value="<?php echo $inner_size; ?>">
																		<input readonly name='bag[]' class='form-control bag positive' style="text-align:right;width:70px;"  type='text' value="<?php echo $i['bag']; ?>">
																	</td>
																	
																	<td style='text-align:right;padding: 1px!important'><input type='hidden' name='outer_size' class='outer_size' value="<?php echo $outer_size; ?>"><input readonly type='text' class='form-control box_qty' style='text-align:right;width:70px;' name='box_qty[]' class='box_qty positive' value="<?php echo $i['box']; ?>"></td>

																	<td style='text-align:right;padding: 1px!important'><input type='hidden' name='loose_qty' class='loose_qty' value="<?php echo $i['loose']; ?>"><input readonly type='text' class='form-control loose' style='text-align:right;width:70px;' name='loose[]' class='loose positive' value="<?php echo $i['loose']; ?>"></td>

																	<td style="padding: 1px!important;" class='text-center'><?php echo $i['stock']; ?></td>

																	<td style="padding: 1px!important">
																		<textarea rows="2" cols="10" id="pro_description" class="pro_desc" name="pro_description[]" style="margin: 0!important;"><?=$i['pro_description']?></textarea>
																	</td>

																	<td style="padding: 1px!important" class='text-center'><?php echo $i['hsn_code']; ?></td>

																	<td style="text-align:right;padding: 1px!important">
																		<input type='text' style="text-align:right;width:80px;" onChange='recalculateRow(this)' class='qty form-control' name='qty[]' value="<?php echo $i['qty']; ?>">
																	</td>

																	<td style="text-align:right;padding: 1px!important">
																		<input name='original_price[]' class='original_price form-control' style="text-align:right;width:80px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['original_price']; ?>">
																		<?php $i['original_price']; ?>
																	</td>

																	<td style='text-align:right;padding: 1px!important;'>
																		<?php
																		if($i['discount_per']!=0)
																		{
																			$discount_amt = 0;	
																		} 
																		else
																		{
																			$discount_amt = $i['discount_amount'];
																		}
																		?>
																		<input type='text' name='discount_amount[]' style='text-align:right;width: 80px;' class='form-control discount_amount' onChange='recalculateRow(this,1)' value='<?php echo $discount_amt; ?>'>
																	</td>

																	<td style="padding: 1px!important">
																		<input style="text-align:right;width:80px;" type="text" name='discount[]' onChange='recalculateRow(this,2)' class="discount form-control" value="<?php echo $i['discount_per']; ?>">
																		<?php $i['discount_per']; ?>
																	</td>

																	<td style="text-align:right;padding: 1px!important">
																		<input name='price[]' class='price form-control' style="text-align:right;width:80px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['product_price']; ?>">
																		<input class='old_price form-control' style="text-align:right;" type='hidden' value="<?php echo $i['product_price']; ?>">
																		<?php $i['product_price']; ?>
																	</td>


																	<td style="text-align:right;padding: 1px!important">
																		<input type='text' style="text-align:right;width: 100px;" disabled class='total form-control' disabled onChange='recalculateRow(this)' name='subtotal[]' value="<?php echo $i['product_total']; ?>">
																	</td>

																	<td class="hidden" style="padding: 1px!important;">
																		<input style="width: 150px;" readonly type='text' class='cd_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='cd_discount[]' value="<?php echo $i['cd_amount']; ?>">
																	</td>

																	<td  class="hidden" style="padding: 1px!important;"><input style="width: 150px;" readonly type='text' class='ad_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='ad_discount[]' value="<?php echo $i['ad_amount']; ?>"></td>

																	<td  class="hidden" style='padding: 1px!important;';><input readonly type='text' class='other_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='other_charge[]' value=<?php echo $i['other_charge']; ?>></td>

																	<td class="hidden" style='padding: 1px!important;';><input readonly type='text' class='fright_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='fright_charge[]' value=<?php echo $i['fright_charge']; ?>></td>

																	<td style="padding: 1px!important;"><input readonly type='text' class='taxable_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='taxable_amount[]' value="<?php echo $i['taxable_amount'] ?>"><input class='new_taxable' type='hidden' value='<?php echo $i['taxable_amount'] ?>' id='taxable_amount'></td>

																	<td style="padding: 1px!important;"><input readonly  type='text' class='gst_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='gst_amount_item[]' value="<?php echo $i['gst_amount_item'] ?>"></td>


																	<?php
																	if($customer_type==8)
															        {
															        	$gst =  "0.1";
																	} 
																	else
																	{
																		$gst = $i['gst'];
																	}
															        ?>

																	<td style="text-align: center;padding:1px;"><input type='hidden' style='text-align:right;width: 80px;' id='gst_tax'  class='gst_tax form-control' value='<?php echo $gst ?>'><?php echo $gst ?>%</td>


																	<td style="padding: 1px!important;"><input readonly type='text' class='sub_total form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='sub_total[]' value="<?php echo $i['sub_total'] ?>"></td>

																	
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
															<td colspan="9" align="right">Total</td>

															<td style="text-align: right;">
																<input type="text" style="text-align: right;width:80px;" id="sum_qty" class="form-control" disabled value="<?php echo $qty_total; ?>">
															</td>

															<td></td>
															<td></td>
															<td></td>
															<td></td>
															
															<td style="text-align:right">
																<input type='text' style="text-align:right;" id='finalTotal' align="right" class="form-control" disabled value="<?php echo $db->rp_number_format($total_amount); ?>" name='finalTotal[]'>
															</td>

															<td class="hidden" style="text-align:right">
																<input type='text' style="text-align:right;" id='cd_total_sum' align="right" class="form-control" disabled value="" name='cd_total_sum[]'>
															</td>
															
															<td class="hidden" style="text-align:right">
																<input type='text' style="text-align:right;" id='ad_total_sum' align="right" class="form-control" disabled value="" name='ad_total_sum[]'>
															</td>

															<td class="hidden" colspan="1"></td>

															<td  style="text-align:right">
																<input type='text' style="text-align:right;" id='taxable_total_sum' align="right" class="form-control" disabled value="" name='taxable_total_sum[]'>
															</td>
															<td style="text-align:right">
																<input type='text' style="text-align:right;" id='gst_total_sum' align="right" class="form-control" disabled value="" name='gst_total_sum[]'>
															</td>

															<td></td>

															<td style="text-align:right">
																<input type='text' style="text-align:right;" id='sub_total_sum' align="right" class="form-control" disabled value="" name='sub_total_sum[]'>
															</td>
														</tr>
														<tr>
															<td colspan="11" align="right">Cash Discount (%)</td>
															<td style="text-align: right;">
																<input type="text" style="text-align: right;" id="cash_discount" name="cash_discount"  onChange='recalculateRow(this)' class="form-control" value="<?php echo ($cash_discount>0)?$cash_discount:""; ?>">
															</td>
															<td style="text-align: right;">
																<input type="text" style="text-align: right;" id="cash_discount_amount" onChange='recalculateRow(this)' name="cash_discount_amount" class="form-control cd_calculate" value="<?php echo $cash_discount_amount; ?>">
															</td> 
														</tr>
														<tr>
															<td colspan="11" align="right">Additonal Discount (%)</td>

															<td style="text-align: right;">
																<input type="text" style="text-align: right;" id="additional_discount" name="additional_discount"  onChange='recalculateRow(this)' class="form-control" value="<?php echo ($additional_discount>0)?$additional_discount:""; ?>">
															</td>
															<td style="text-align: right;">
																<input type="text" style="text-align: right;" id="addtional_discount_amount" onChange='recalculateRow(this)' name="addtional_discount_amount" class="form-control ad_calculate" value="<?php echo $additional_discount_amount; ?>">
															</td>
														</tr>
														<tr>
															<td colspan="12" align="right">Packing & Forwarding Charge</td>

															<td style="text-align: right;">
																<input type="text" style="text-align: right;" id="packing_charge" name="packing_charge"  onChange='recalculateRow(this)' class="form-control packing_calculate" value="<?php echo $packing_charge; ?>">
															</td> 
														</tr>
														<tr>
															<td colspan="12" align="right">Transport</td>
															<td style="text-align:right">
																<input type="text" style="text-align: right;" class="form-control transport_calculate" name="transport_charge" id="transport_charge" onchange="recalculateRow(this)" value="<?php echo $transport_charge; ?>" />
															</td>

															<!-- <td style="text-align: right;">
																<button class="btn btn-primary" type="button" id="transport_calculate">Transport On Item</button>
															<p class="help-block"></p>
															</td> -->

															<!-- <td style="text-align: right;">
																<input type="text" style="text-align: right;" id="transport_charge_per" name="transport_charge_per"  onChange='recalculateRow(this)' class="form-control" value="<?php echo $transport_charge_per; ?>">
																<label style="font-size: 12px;">Transport GST (%)</label>
															</td> -->
														</tr>

														<tr>
															<td colspan="12" align="right">Total Taxable</td>

															<td style="text-align: right;">
																<input type="text" disabled style="text-align: right;" id="total1"  class="form-control" value="">
															</td>
														</tr>

														
														<tr>
															<?php
															if($igst_amount!=0)
															{
																if($customer_type==7)
																{
																	$GST = 18;
																}
																else
																{
																	$GST = 18;
																}
															}
															else
															{	
																$GST = 0;
															}
															$t_charge = $total;
															$gst_amount = ($t_charge * $GST) / 100;
															// $gst_amount = ($total_amount * $GST) / 100;
															// $total_gst = $gst_amount + $total_amount;
															$total_gst = $gst_amount + $t_charge;
															?>
															<!-- <td colspan='16' align="right">GST <span class="gst_type"></span><br/><b><span class='gst-amount-display'><?=$db->rp_number_format($gst_amount,2)?></span></b></td> -->

															<td colspan='11' align="right"></td>

															<td align="right">GST On Off Switch
															
															<input type="checkbox" 
															<?php
															if($_REQUEST['mode']=="add")
															{
																echo "checked";
																$gst_apply_flag = 1;
															}
															else if($gst_amount==0)
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
																<input type="hidden" class="item_gst_amount" value="">
																<input type='text' style="text-align:right;width:150px;" id='gst_amount' align="right" class="form-control" disabled value="<?= $db->rp_number_format($gst_amount) ?>" name='gst_amount'>
															</td>
														</tr>
<!-- <?=  $tcs_amount;?> -->
														<tr>
															<td colspan='12' align="right">TCS On Off Switch
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
															<td colspan='12' align="right">Round Off</td>
															<td style="text-align:right">
																<input id="round_off" name="round_off" type='text' style="text-align:right;width:150px;" class="form-control" readonly value="<?php echo $db->rp_number_format($f1); ?>">
															</td>
															<td></td>
														</tr>
														<tr>
															<td colspan='12' align="right"> Grand Total </td>
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
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script src="assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>

<script type="text/javascript" src="assets/global/plugins/ckeditor/ckeditor.js"></script>

<script type="text/javascript" src="js/fSelect.js"></script> 

<script type="text/javascript">
// $("#category_id").fSelect({
//     numDisplayed: 1,
// });
$(".discount_amount").numeric();
$(".discount").numeric();
$(".original_price").numeric();
$(".price").numeric();

</script>

<script type="text/javascript">
/*Customer Chnage Shipping address*/ 
$('#CustomerChangeShippingAddressModel').on('show.bs.modal', function (event) {
var button = $(event.relatedTarget) // Button that triggered the modal

var customer_id = $('#customer_id').val();

var quotation_id=button.data("quotation_id");
var mode=button.data("mode");
$("#requesting_ajax_change_shipping").attr("data-url","customer_change_shipping_get_ajax.php?customer_id="+customer_id+"&id="+quotation_id+"&mode="+mode);
$("#requesting_ajax_change_shipping").click();
})
/*Customer Chnage Shipping address*/ 
</script>

<script type="text/javascript">
$(document).ready(function() {
var admintype = '<?=$_SESSION[SITE_SESS.'_ADMIN_TYPE'] ?>';
if(admintype!="0")
{
CKEDITOR.config.readOnly = true;
}
else
{
CKEDITOR.config.readOnly = false;	
}
});
</script>

<script type="text/javascript">


$("#transport_charge").numeric();
$("#packing_charge").numeric();
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

var gst_apply_flag = 0;

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
// var pricelist = $("#customer_id").find('option:selected').data("price-list");

var pricelist_new = $(".customer_id_s").find('option:selected').data("price-list");

var cname = $("#customer_id").find('option:selected').data("cname");
var gst_type = $("#customer_id").find('option:selected').data("gst-type");


var cname = $("#customer_id").find('option:selected').data("cname");
var shipping_add = $("#customer_id").find('option:selected').data("shipping-add");
var billing_add = $("#customer_id").find('option:selected').data("billing-add");


var c_id = $("#customer_id").find('option:selected').data("c_id");

$('#customer_id_c').attr("data-customer_id",c_id); //setter
var c1 =  $('.customer_id_s').val();


$("#shipping_address").html(shipping_add);
$("#billing_address").html(billing_add);

//$("#place_of_supply").val(place_of_supply);
$("#name_value").html(customer_name);
$("#name_phone").html(phone);
$("#name_address").html(address);
$("#name_state").html(state);
/*$("#name_gstin").html(gstin);*/

var packing_slip_id = '<?=$_REQUEST['packing_slip_id'] ?>';
var mode = '<?=$_REQUEST['mode'] ?>';	
if(mode!="add" && packing_slip_id!="")
{
$("#name_gstin").val(gstin);
}

$("#name").html(cname);
$("#name_pricelist").html(pricelist_new);
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
// alert("You lost all added Product");
// $("#datatable_1").find('tbody').html("");
recalculateRow();
recalculateFinalValues();
}
}

}

$("#category_id").on('change', function() {
var tcid = $("#category_id").val();
getProductList(tcid);
});

function getProductList(tcid) {

// var tcid = $("#category_id").val();
var cid = $("#customer_id").val();

$.ajax({
type: "post",
url: "ajax_get_product.php",

data: "cid=" + cid+"&tcid="+tcid,
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


$("#product_id").change(function() {
var inner_size = $("#product_id").find('option:selected').data('inner_size');
$(".inner_size").html(inner_size);
var outer_size = $("#product_id").find('option:selected').data('outer_size');
$(".outer_size").html(outer_size);
})

$("#qty").change(function(){
var bagids = $("#bag_box_id").val();
var qtys = $("#qty").val();
var inner_size = $("#product_id").find('option:selected').data('inner_size');
var outer_size = $("#product_id").find('option:selected').data('outer_size');
if(bagids==2)
{
var new_qty_bag = inner_size*qtys;
$(".qty").html(new_qty_bag);
}
else if(bagids==3)
{
var new_qty_box = outer_size*qtys;
$(".qty").html(new_qty_box);
}
else if(bagids==1)
{
$(".qty").html(qtys);
}
})

if (mode == "add") {
var l = $("#datatable_1").find('tbody').find('tr').length;
if (l > 0) {
// alert("You lost all added Product");
// $("#datatable_1").find('tbody').html("");
recalculateRow();
recalculateFinalValues();
}
}
}

//--------------Calculation for qty ,total------------------------//
function recalculateRow(t,discount_type="") {
var row = $(t).parent('td').parent('tr');
if(discount_type==1)
{
//$(".discount").val("");
$(row).find("td").find("input.discount").val("");
}
else if(discount_type==2)
{
//$(".discount_amount").val("");
$(row).find("td").find("input.discount_amount").val("");
}
else
{

}

var price = $(row).find("td").find("input.price").val();
var original_price = $(row).find("td").find("input.original_price").val();
var old_price = $(row).find("td").find("input.old_price").val();
var discount = $(row).find("td").find("input.discount").val();
var qty1 = $(row).find("td").find("input.qty").val();
var qty = $(row).find("td").find("input.qty").val();
var inner_size = $(row).find("td").find("input.inner_size").val();
var outer_size = $(row).find("td").find("input.outer_size").val();
var box_qty = $(row).find("td").find("input.box_qty").val();
var stock = $(row).find("input.stock_value").val();
var cd_discount = $(row).find("input.cd_discount").val();
var ad_discount = $(row).find("input.ad_discount").val();
var other_charge = $(row).find("input.other_charge").val();
var fright_charge = $(row).find("input.fright_charge").val();
var gst_tax = $(row).find("input.gst_tax").val();
var discount_amount_new = $(row).find("td").find("input.discount_amount").val();


if(isNaN(cd_discount) || cd_discount=="NaN" || cd_discount=="")
{
cd_discount=0;
}

if(isNaN(ad_discount) || ad_discount=="NaN" || ad_discount=="")
{
ad_discount=0;
}

if(isNaN(other_charge) || other_charge=="NaN" || other_charge=="")
{
other_charge=0;
}

if(isNaN(fright_charge) || fright_charge=="NaN" || fright_charge=="")
{
fright_charge=0;
}

var bag_box_id = $("#bag_box_id").val();


if(bag_box_id==""){
var bag_box_id = 3;	
}
else{
var bag_box_id = bag_box_id;
}
 
var customer_type = $("#customer_type").val();
if(customer_type==8)
{
var gst = 0.1;
}
else
{
var gst = $("#product_id").find('option:selected').data('gst');
}

if(bag_box_id==2)
{
var new_qty_bag = inner_size*qty;
$("#final_qty").val(new_qty_bag);
var bag = Math.floor(qty / inner_size);
bag = Math.floor(bag);
var box = 0;
var loose = 0;
}
else if(bag_box_id==3)
{
var new_qty_box = outer_size*qty;
$("#final_qty").val(new_qty_bag);
//var qty = new_qty_box;
var box = Math.floor(qty / outer_size)
var bag = 0;
var loose = 0;

}
else if(bag_box_id==1)
{
$("#final_qty").val(qty);
var qty = qty;
var box = Math.floor(qty / outer_size);
if (box != 0) 
{
var bagqty = qty - (outer_size * box);
if (bagqty < 0)
{
bagqty = bagqty * -1;
}
bagqty = (bagqty != "") ? parseFloat(bagqty) : 0;
var bag = (bagqty / inner_size);
//bag = format(bag, 3);
bag = Math.floor(bag);
} 
else 
{
var bag = (qty / inner_size);
//bag = format(bag, 3);
bag = Math.floor(bag);
}
 }

/*loose qty calculation*/
var total_bag = bag*inner_size;
var total_box = box*outer_size;
var totalsum = total_bag+total_box;
var loose = Math.floor(qty-totalsum);
/*loose qty calculation*/

$(row).find("td").find("input.box_qty").val(box);
$(row).find("td").find("input.bag").val(bag);
$(row).find("td").find("input.loose").val(loose);

if (parseFloat(price) > parseFloat(original_price)) {
// toastr.error("Rate should not be higher than Available Price");
$(row).find("td").find("input.price").val(old_price);
price = $(row).find("td").find("input.price").val();
}

if (parseFloat(discount_amount_new) > parseFloat(original_price)) {
toastr.error("You cant add Discount More Than Price");
$(row).find("td").find("input.discount_amount").val(0);
discount_amount_new=0;
}

/*if(parseFloat(discount_amount_new) > 100)
{
toastr.error("You cant add Discount More Than 100");
$(row).find("td").find("input.discount_amount").val(0);
}
*/
if(parseFloat(discount) > 100)
{
toastr.error("You cant add Discount More Than 100");
$(row).find("td").find("input.discount").val(0);
discount=0;
}

var customer_type = $("#customer_type").val();
if(customer_type==8)
{
var item_gst = 0.1;
}
else
{
item_gst=gst_tax;
}

//item_gst=gst_tax;

if(discount_amount_new!="" && discount_amount_new!=0)
{
var discount_amount = (parseFloat(original_price) - parseFloat(discount_amount_new));
price = discount_amount;
price1 = discount_amount;
}	
else
{
var discount_amount = (original_price * discount) / 100;	
price = (original_price - discount_amount);
price1 = (original_price - discount_amount);
}

price1 = Number(price1).toFixed(2);
price = price + ((price*item_gst)/100);
price = Number(price).toFixed(2);
$(row).find("td").find("input.price").val(price1);
var total = qty1 * price;
var total1 = price1 * qty;
// price=format(price,3);
// total=format(total,3);
total = total.toFixed(2);
total1 = total1.toFixed(2);
var total_balance = stock - qty;
var taxable_amount = parseFloat(total1) - parseFloat(cd_discount) - parseFloat(ad_discount) + parseFloat(other_charge) + parseFloat(fright_charge);
// alert("taxable_amount"+taxable_amount);
var item_gst_amount1 = ((taxable_amount*item_gst)/100);
item_gst_amount1 = item_gst_amount1.toFixed(2);
var taxable_amount = taxable_amount.toFixed(2);
var sub_total = (parseFloat(total1) + parseFloat(item_gst_amount1)).toFixed(2);
$(row).find("td").find("input.total").val(total1);
$(row).find("td").find("input.final_balance").val(total_balance);
$(row).find("td").find("input.gst_amount").val(item_gst_amount1);
$(row).find("td").find("input.final_balance").val(total_balance);
$(row).find("td").find("input.sub_total").val(sub_total);
$(row).find("td").find("input.taxable_amount").val(taxable_amount);
$(row).find("td").find("input.new_taxable").val(taxable_amount);
cdadCalculate();
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
/*if(customer_type==7)
{
var gst = 0.1;
}
else
{*/
var gst = 18;
//}
var tcs_per ='<?= TCS_CHARGE_IN_PER; ?>';
var total = 0; 
var item_gst_amount2 = 0;
var total1 = 0;
var taxable_amount = 0;
var gst_total_sum = 0;
var item_gst_amount1 = 0;
var sub_total = 0;
var sum1 = 0;
var sum2 = 0;
// alert(tcs_apply_flag);

$('.total').each(function() {

total = parseFloat($(this).val());
total = (total != "") ? parseFloat(total) : 0;
sum += total;
}); 

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
// if (event.keyCode == 46 || event.keyCode == 8) {
// 	// let it happen, don't do anything
// }
});

$('.taxable_amount').each(function() {
total1 = parseFloat($(this).val());
total1 = (total1 != "") ? parseFloat(total1) : 0;
taxable_amount += total1;
// this.value = this.value.replace(/\D/g, '');
});

$('.gst_amount').each(function() {

item_gst_amount1 = parseFloat($(this).val());
item_gst_amount1 = (item_gst_amount1 != "") ? parseFloat(item_gst_amount1) : 0;
// alert(item_gst_amount1);
gst_total_sum += item_gst_amount1;
// this.value = this.value.replace(/\D/g, '');
});


$('.cd_discount').each(function() {

cd_discount = parseFloat($(this).val());
cd_discount = (cd_discount != "") ? parseFloat(cd_discount) : 0;
cash_discount_amount += cd_discount;
// this.value = this.value.replace(/\D/g, '');
});

$('.ad_discount').each(function() {

ad_discount = parseFloat($(this).val());
ad_discount = (ad_discount != "") ? parseFloat(ad_discount) : 0;
additional_discount_amount += ad_discount;
// this.value = this.value.replace(/\D/g, '');
});

$('.sub_total').each(function() {

total = parseFloat($(this).val());
sub_total = (total != "") ? parseFloat(total) : 0;
sum1 += sub_total;
sum2 += sub_total;
// this.value = this.value.replace(/\D/g, '');
});

sum = sum.toFixed(2);
sum_qty = sum_qty.toFixed(2);
gst_total_sum = gst_total_sum.toFixed(2);
sum1 = sum1.toFixed(2);
sum2 = sum2.toFixed(2);
taxable_amount = taxable_amount.toFixed(2);
cash_discount_amount = cash_discount_amount.toFixed(2);
additional_discount_amount = additional_discount_amount.toFixed(2);

$("#finalTotal").val('' + sum);
$("#sum_qty").val('' + sum_qty);
//$("#total1").val('' + sum1);
$("#total1").val('' + taxable_amount);
$("#taxable_total_sum").val('' + taxable_amount);
$("#gst_total_sum").val('' + gst_total_sum);
$("#sub_total_sum").val('' + sum2);
$("#cd_total_sum").val('' + cash_discount_amount);
$("#ad_total_sum").val('' + additional_discount_amount);
$("#finalTotal").val('' + sum);
 
var transport_charge = $("#transport_charge").val();
var packing_charge = $("#packing_charge").val();
var transport_charge_per = $("#transport_charge_per").val();
var packing_charge_per = $("#packing_charge_per").val();
var cd_gst = $("#cd_gst").val();
var ad_gst = $("#ad_gst").val();

if(gst_apply_flag==0)
{
gst = 0
gst_total_sum = 0
packing_charge_per = 0
transport_charge_per = 0
gst_amount = 0
cd_gst = 0
ad_gst = 0
}

if(tcs_apply_flag==0)
{
tcs_per = 0
}

var sum1 = $("#total1").val();
if (sum1 != "" && sum1 != "0.00") 
{
 
var gst_amount = parseFloat(gst_total_sum);
gst_amount = gst_amount.toFixed(2);

$("#gst_amount").val(parseFloat(gst_amount));

var final_total = (parseFloat(sum1)+parseFloat(gst_amount));
var ft = Math.round(final_total);
ft = ft.toFixed(2);
var integr = Math.floor(final_total);
var round_off = final_total - integr;
round_off = round_off.toFixed(2);
// round_off=format(round_off,3);
$("#round_off").val('' + round_off);
$("#finalgrandTotal").val('' + ft);	 
}

//var sum1 = $("#total1").val();
var sum1 = $("#finalgrandTotal").val();
if(tcs_per!="0")
{
var tcs_amount1 = (sum1 * tcs_per) / 100;
tcs_amount1 = tcs_amount1.toFixed(2);
$("#tcs_amount").val(parseFloat(tcs_amount1));
$(".tcs_put").val(parseFloat(tcs_amount1));
//final_sum = parseFloat(ft) + parseFloat(tcs_amount1);
//	alert(final_total);
final_total=parseFloat(final_total)+parseFloat(tcs_amount1);
//$("#finalgrandTotal").val('' + final_sum);
}
else
{
$("#tcs_amount").val("0");
$(".tcs_put").val(0);
$("#finalgrandTotal").val('' + ft);
}
var ft = Math.round(final_total);
ft = ft.toFixed(2);
var integr = Math.floor(final_total);
var round_off = final_total - integr;
round_off = round_off.toFixed(2);
// round_off=format(round_off,3);
$("#round_off").val('' + round_off);
$("#finalgrandTotal").val('' + ft);
}

function hasValue(elem) {
return $(elem).filter(function() {
return $(this).val();
}).length > 0;
}

$("#add").click(function() {
var product_id = $("#product_id").val();
var qty = $("#qty").val();
var bag_box_id = $("#bag_box_id").val();
var pro_description = $("#pro_description").val();
count = 0;
//var isProductAvailable=check_form();
if (product_id == "") {
toastr.error('Please Select product!!');
} else if (qty == "" || qty == 0) {
toastr.error('Please Enter At least one Quantity!!');
}
// else if (pro_description == "") {
// 	toastr.error('Please Enter Product Description!!');
// }
else if (bag_box_id==0) {
toastr.error('Please Select Order Unit!!');
}
else {
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
var gst = $("#product_id").find('option:selected').data('gst');
// alert(pro_id);
var qty = $("#qty").val();
var customer_type = $("#customer_type").val();
if(customer_type==8)
{
var gst = 0.1;
}
else
{
var gst = $("#product_id").find('option:selected').data('gst');
}

if(bag_box_id==2)
{
var new_qty_bag = inner_size*qty;
$("#final_qty").val(new_qty_bag);
var qty = new_qty_bag;
var bag = (qty / inner_size);
bag = Math.floor(bag);
var box = 0;
var loose = 0;
}
else if(bag_box_id==3)
{
var new_qty_box = outer_size*qty;
$("#final_qty").val(new_qty_bag);
var qty = new_qty_box;
var box = Math.floor(qty / outer_size)
var bag = 0;
var loose = 0;
}
else if(bag_box_id==1)
{
$("#final_qty").val(qty);
var qty = qty;
var box = Math.floor(qty / outer_size);
if (box != 0) 
{
var bagqty = qty - (outer_size * box);
if (bagqty < 0)
{
bagqty = bagqty * -1;
}
bagqty = (bagqty != "") ? parseFloat(bagqty) : 0;
var bag = (bagqty / inner_size);
//bag = format(bag, 3);
bag = Math.floor(bag);
} 
else 
{
var bag = (qty / inner_size);
//bag = format(bag, 3);
bag = Math.floor(bag);
}
/*loose qty calculation*/
var total_bag = bag*inner_size;
var total_box = box*outer_size;
var totalsum = total_bag+total_box;
var loose =  Math.floor(qty-totalsum);
/*loose qty calculation*/
}

qty = (qty != "") ? parseFloat(qty) : 0;
outer_size = (outer_size != "") ? parseFloat(outer_size) : 0;

/*loose qty calculation*/
var total_bag = bag*inner_size;
var total_box = box*outer_size;
var totalsum = total_bag+total_box;
var loose =  Math.floor(qty-totalsum);

/*loose qty calculation*/

// $p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
// $p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];
//var item_gst=gst;

var customer_type = $("#customer_type").val();
if(customer_type==8)
{
var item_gst = 0.1;
}
else
{
item_gst=gst;
}

var cd_discount = 0;
var ad_discount = 0;
var discount_amount = (original_price * discountPer) / 100;
price = original_price - discount_amount;
price1 = (original_price - discount_amount);

//var total=box*price;
var total = qty * price;
// price=format(price,3);
// total=format(total,3);
// total=total.replace(",","");
price = price.toFixed(2);
total = total.toFixed(2);
var balance = stock - qty;
var total1 = price1 * qty;
var taxable_amount = (total1 - cd_discount - ad_discount);
var item_gst_amount1 = ((taxable_amount*item_gst)/100);
var new_total = (parseFloat(total1) + parseFloat(item_gst_amount1)).toFixed(2);
item_gst_amount1 = item_gst_amount1.toFixed(2);
var taxable_amount = taxable_amount.toFixed(2);
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
var new_row = "<tr><td  class='text-center'><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></td><td style='padding: 1px!important;min-width: 187px;' width='300px;' class='text-center'><input type='hidden' class='pro_id' name='pro_id' id='pro_id' value='" + pro_id + weight_id + "'><input type='hidden' name='product_id[]' value='" + pro_id + "' class='product_id' id='product_id'/><input type='hidden' name='pro_name[]' value='" + p_name + "' id='pro_name'><input type='hidden' name='weight_id[]' value='" + weight_id + "' id='weight_id'><input type='hidden' name='brand_id[]' value='" + brand_id + "' id='brand_id'>" + p_name + "</td>" +

"<td style='padding: 1px!important;'; class='text-center'>"+unit_name+"</td>" +


"<td style='text-align:right;padding: 1px!important;'><input type='hidden' name='inner_size' class='inner_size' value='"+inner_size+"'><input readonly class='form-control bag' type='text' name='bag[]' style='text-align:right;width:70px;' value='"+bag+"''></td>"+

"<td style='text-align:right;padding: 1px!important;'><input type='hidden' name='outer_size' class='outer_size' value='"+outer_size+"'><input readonly type='text' class='form-control box_qty' style='text-align:right;width:70px;' name='box_qty[]' class='box_qty positive' value='"+box+"'></td>"+

"<td style='text-align:right;padding: 1px!important;'><input type='hidden' name='loose_qty' class='loose_qty' value='"+outer_size+"'><input readonly type='text' class='form-control loose' style='text-align:right;width:70px;' name='loose[]' class='loose positive' value='"+loose+"'></td>"+


"<td style='padding: 1px!important;' class='text-center'>"+stock+"</td>" +

"<td style='padding: 1px!important;'><textarea rows='2' cols='10' id='pro_description' name='pro_description[]' class='pro_desc' style='margin: 0!important;'></textarea></td>" +

"<td style='padding: 1px!important;' class='text-center'>"+hsncode+"</td>" +
/*"<td style='text-align:right'><input type='hidden' name='inner_size' class='inner_size' value='"+inner_size+"'><input readonly class='form-control bag' type='text' name='bag[]' style='text-align:right;width:100px;' value='"+bag+"''></td>"+

"<td style='text-align:right'><input type='hidden' name='outer_size' class='outer_size' value='"+outer_size+"'><input readonly type='text' class='form-control box_qty' style='text-align:right;width:100px;' name='box_qty[]' class='box_qty positive' value='"+box+"'></td>"+*/

"<td style='padding: 1px!important;' class='text-center'><input type='text' name='qty[]' class='form-control positive  qty' style='text-align:right;width:80px;' value='" + qty + "' onChange='recalculateRow(this)'  id='qty'/><input class='new_qty' type='hidden' value='" + qty + "' id='qty'></td>" +

"<td style='text-align:right;padding: 1px!important;'><input type='text' name='original_price[]' class='form-control  original_price' style='text-align:right;width:80px;' value='" + original_price + "' onChange='recalculateRow(this)'  id='original_price'/</td>" +

"<td style='text-align:right;padding: 1px!important;'><input type='text' name='discount_amount[]' style='text-align:right;width: 80px;' class='form-control discount_amount' onChange='recalculateRow(this,1)' value=''></td>" +

"<td style='text-align:right;padding: 1px!important;'><input style='text-align:right;width:80px;' type='text' name='discount[]' class='discount form-control' value='" + discountPer + "' onChange='recalculateRow(this,2)'></td>" +

"<td style='padding: 1px!important;' class='price_val' ><input type='text' style='text-align:right;width:80px;'  name='price[]' class='price form-control'  onChange='recalculateRow(this)' value='" + price + "'><input type='hidden' style='text-align:right;' class='old_price form-control' value='" + price + "'></td>" +

"<td style='padding: 1px!important;'><input type='text' class='total form-control' disabled onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='subtotal[]' value='" + total + "' ></td>" +

"<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='cd_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='cd_discount[]' value='' ></td>" +

"<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='ad_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='ad_discount[]' value='' ></td>" +

"<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='other_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='other_charge[]' value='' ></td>" +

"<td class='hidden' style='padding: 1px!important;';><input readonly type='text' class='fright_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='fright_charge[]' value='' ></td>" +

"<td style='padding: 1px!important;';><input readonly type='text' class='taxable_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='taxable_amount[]' value='"+ taxable_amount +"' ><input class='new_taxable' type='hidden' value='" + taxable_amount + "' id='taxable_amount'></td>" +

"<td style='padding: 1px!important;';><input readonly  type='text' class='gst_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='gst_amount_item[]' value='" + item_gst_amount1 + "' ></td>" +

"<td style='padding: 1px!important;' class='text-center'>"+gst+"%<input type='hidden' style='text-align:right;width: 80px;' id='gst_tax'  class='gst_tax form-control' value='" + gst + "'></td>" +

"<td style='padding: 1px!important;';><input readonly type='text' class='sub_total form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='sub_total[]' value='" + new_total + "' ></td>" +


"</tr>";
$("#datatable_1").find('tbody').append(new_row);
/*$("#datatable_1").find('tbody').prepend(new_row);*/

$('#category_id').prop('disabled',true);
recalculateRow(); 
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

$(".inner_size").html("");
$(".outer_size").html("");
$(".qty").html("");
})

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
/*if ($("#invoice_date").val() == "" || $("#invoice_date").val().split(" ").join("") == "") 
{
vd = aj.error('invoice_date', "Please Enter Invoice Date.", "add_error");
isValid = false;
}*/
if ($("#billing_address").val() == "" || $("#billing_address").val().split(" ").join("") == "") 
{
vd = aj.error('billing_address', "Please Enter Billing Address.", "add_error");
isValid = false;
}
if ($("#shipping_address").val() == "" || $("#shipping_address").val().split(" ").join("") == "") 
{
vd = aj.error('shipping_address', "Please Enter Shipping Address.", "add_error");
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
$(".form-control").bind("keyup change", function() {
if ($(this).parent().hasClass("has-error")) {
$(this).parent().removeClass("has-error");
$(this).parent().find('p.help-block').html("");
}
});

$(document).ready(function() {
recalculateRow();
recalculateFinalValues();
$("#datatable_1").on('click', '.delete', function() {

//var rows = document.getElementById("#datatable_1").getElementsByTagName("tr").length;
//alert(rows);
var rows = $("#datatable_1").find("tbody").find("tr").length;

var r = confirm("Are you sure you want to delete?");
if (r) {
$(this).closest('tr').remove();
cdadCalculate();
if(rows==1){
$('#category_id').prop('disabled',false);
}
}
});
});


$(document).ready(function() {

var mode = "<?= $_REQUEST['mode']; ?>";
var packing_slip_id = "<?= $_REQUEST['packing_slip_id']; ?>";
if (mode == "add" && packing_slip_id!="") {
$("#customer_id").trigger("change");
}

if (mode == "edit") {
$("#customer_id").trigger("change");
var cid = "<?= $customer_id; ?>";

var top_cat_id = "<?= $top_cat_id; ?>";

$("#category_id").select2("destroy");
$('#category_id option[value='+top_cat_id+']').attr('selected', 'selected');
$("#category_id").select2();
$('#category_id').prop('disabled',true);

getProductList(top_cat_id);
if ($("#cd").prop("checked") == true) {
recalculateFinalValues();
}
if ($("#ad").prop("checked") == true) {
recalculateFinalValues();
}
}
})
$("#cd").on("click", function() {
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
})

$(".positive").keyup(function(event) {

if (event.keyCode == 46 || event.keyCode == 8) {
// let it happen, don't do anything
} else if (/\D/g.test(this.value)) {
toastr.error("Only Digits Allowed");
this.value = this.value.replace(/\D/g, '');
}
});

$(document).ready(function() {
if (mode == "edit") {

if ($("#gst_apply").prop("checked") == true) {
gst_apply_flag = 1;
recalculateFinalValues();
}

if ($("#tcs_apply").prop("checked") == true) {
tcs_apply_flag = 1;
recalculateFinalValues();
}
}

if (mode == "add") {
if ($("#gst_apply").prop("checked") == true) { 
gst_apply_flag = 1;
$("#gst_apply").prop("checked");
recalculateFinalValues();
}
if ($("#tcs_apply").prop("checked") == true) { 
tcs_apply_flag = 1;
$("#tcs_apply").prop("checked");
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

$("#tcs_apply").on("click",function()
{	
if ($('#tcs_apply').is(":checked"))
{
tcs_apply_flag = 1;
$("#tcs_apply_flag").val("1");
}
else
{
tcs_apply_flag = 0;
$("#tcs_apply_flag").val("0");
}
recalculateFinalValues();
})

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
</script>

<script type="text/javascript">
var mode = '<?= $_REQUEST['mode']; ?>';
if(mode=="edit" )
{
var id = $("#transport_through").val(); 
var transport_name_selected_id = $("#transport_name_selected_id").val();
getTransportname(id,transport_name_selected_id);
}


var mode = '<?= $_REQUEST['mode']; ?>';
var packing_slip_id = '<?= $_REQUEST['packing_slip_id']; ?>';
if(mode=="add" && packing_slip_id!="")
{
var id = $("#transport_through").val();
var transport_name_selected_id = $("#transport_name_selected_id").val(); 
getTransportname(id,transport_name_selected_id);	

var top_cat_id = "<?= $top_cat_id; ?>";

$("#category_id").select2("destroy");
$('#category_id option[value='+top_cat_id+']').attr('selected', 'selected');
$("#category_id").select2();
$('#category_id').prop('disabled',true);

getProductList(top_cat_id);
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
</script>

<script type="text/javascript">            
$("#cash_discount").change(function(){
cdadCalculate(); 
})           
$("#additional_discount").change(function(){ 
   cdadCalculate(); 
})
function cdadCalculate()
{
    var additional_discount = $("#additional_discount").val();
    var cash_discount = $("#cash_discount").val();
    var addtional_discount_amount = $("#addtional_discount_amount").val();
    var cash_discount_amount = $("#cash_discount_amount").val();
    
    var packing_charge = $("#packing_charge").val();
    var transport_charge = $("#transport_charge").val(); 

    var sum1=0;
    $('.total').each(function() {
       total1 = parseFloat($(this).val()); 
       total1 = (total1 != "") ? parseFloat(total1) : 0;
       sum1 += total1;  
   }); 
    packing_charge = (packing_charge)?packing_charge:0;
    transport_charge = (transport_charge)?transport_charge:0;
    
    // cash_discount = (cash_discount)?cash_discount:0;
    // additional_discount = (additional_discount)?additional_discount:0;

    if(parseFloat(cash_discount) > 100)
    {
        toastr.error("You cant add Cash Discount More Than 100"); 
        cash_discount=0;
        $("#cash_discount").val(0);
    } 
    if(parseFloat(additional_discount) > 100)
    {
        toastr.error("You cant add Additional Discount More Than 100"); 
        additional_discount=0;
        $("#additional_discount").val(0);
    }  

    var cd_value;
    var ad_value;

    if(cash_discount=="")
    { 
        cd_value = cash_discount_amount;
    }
    else if(cash_discount>=0)
    {
        cd_value = (sum1 * 0) / 100;
        cd_value = (sum1 * cash_discount) / 100;
        cd_value = Math.floor(cd_value* 100) / 100;
    }    
    $("#cash_discount_amount").val(cd_value);

    if(additional_discount=="")
    {
        ad_value = addtional_discount_amount;
    }
    else if(additional_discount>=0)
    {
        var adsum = sum1-cd_value;
        ad_value = (adsum * 0) / 100;
        ad_value = (adsum * additional_discount) / 100;
        ad_value = Math.floor(ad_value* 100) / 100;
    }
    $("#addtional_discount_amount").val(ad_value); 

    cd_value = (cd_value)?cd_value:0;
    ad_value = (ad_value)?ad_value:0;
    var totltaxabaleSum =  parseFloat(sum1) - parseFloat(cd_value) - parseFloat(ad_value);
    totltaxabaleSum=totltaxabaleSum.toFixed(2);

    var cash_discount_amount = $("#cash_discount_amount").val();
    var additional_discount_amount = $("#addtional_discount_amount").val();
    // $("#addtional_discount_amount").val(parseFloat(ad).toFixed(2));   
    $('.new_taxable').each(function(){
       var row = $(this).parent('td').parent('tr');
       var total = $(row).find("input.total").val(); 
       // var other_charge_item = $(row).find("input.other_charge_item").val(); 
       // var fright_charge_item = $(row).find("input.fright_charge").val();  

        // var cditem = (total*cash_discount)/100;
        var cditem = ((cash_discount_amount*total)/sum1);
        var cditem_amt = total-cditem;

        var aditem = ((additional_discount_amount*cditem_amt)/(sum1-cash_discount_amount));
        // var aditem = (cditem_amt*additional_discount)/100;
        var aditem_amt = cditem_amt-aditem;

        cditem = (cditem)?cditem:0;
        aditem = (aditem)?aditem:0;
        cditem_amt = (cditem_amt)?cditem_amt:0;
        aditem_amt = (aditem_amt)?aditem_amt:0;
        cditem = cditem.toFixed(2);
        aditem = aditem.toFixed(2);

        var itemWithCDAD_for_packing_value = parseFloat(total) - parseFloat(cditem) - parseFloat(aditem); 
        itemWithCDAD_for_packing_value = itemWithCDAD_for_packing_value.toFixed(2);
        var packing_value = ((parseFloat(packing_charge)*parseFloat(itemWithCDAD_for_packing_value))/parseFloat(totltaxabaleSum));
        packing_value = (packing_value)?packing_value:0;
        packing_value = packing_value.toFixed(2);

        var itemWithCDAD_for_transport = parseFloat(itemWithCDAD_for_packing_value) + parseFloat(packing_value); 
        itemWithCDAD_for_transport = itemWithCDAD_for_transport.toFixed(2);
        /*  alert("transport_charge-"+transport_charge);
        alert("itemWithCDAD_for_transport-"+itemWithCDAD_for_transport);
        alert("totltaxabaleSum-"+(totltaxabaleSum+packing_value));*/
        var transport_value = ((parseFloat(transport_charge)*parseFloat(itemWithCDAD_for_transport))/(parseFloat(totltaxabaleSum)+parseFloat(packing_charge)));

        transport_value = (transport_value>0)?transport_value:0;
        transport_value = transport_value.toFixed(2);
        // alert(packing_value);
        // alert(transport_value);
       
        itemFinalTotal = parseFloat(total) - parseFloat(cditem) - parseFloat(aditem) + parseFloat(packing_value) +parseFloat(transport_value);
        itemFinalTotal = itemFinalTotal.toFixed(2);

        // for gst
        gst_tax  = $(row).find("input.gst_tax").val();
        gst_amount  = (parseFloat(itemFinalTotal) * gst_tax)/100;
        gst_amount = gst_amount.toFixed(2);
        // for gst

        $(row).find("input.cd_discount").val(cditem);
        $(row).find("input.ad_discount").val(aditem);
        $(row).find("input.other_charge").val(packing_value);
        $(row).find("input.fright_charge").val(transport_value);
        $(row).find("td").find("input.taxable_amount").val(itemFinalTotal);
        $(row).find("td").find("input.new_taxable").val(itemFinalTotal)
        $(row).find("td").find("input.gst_amount").val(gst_amount)

        recalculateFinalValues();

    });
}
</script>

<script type="text/javascript">
$(".packing_calculate").change(function(){
cdadCalculate();
}) 

$(".transport_calculate").change(function(){
cdadCalculate();
}) 
</script>
<script type="text/javascript">  
$(document).ready(function() {
var packing_slip_id = '<?=$_REQUEST['packing_slip_id'] ?>';
var mode = '<?=$_REQUEST['mode'] ?>';	
if(mode=="add" && packing_slip_id!="")
{
// $(".cd_calculate").trigger("change");
// $(".ad_calculate").trigger("change");
// $(".packing_calculate").trigger("change");
// $(".transport_calculate").trigger("change");	
	cdadCalculate();
//$(".discount").trigger('change');

}
});
</script>

</body>

</html>

