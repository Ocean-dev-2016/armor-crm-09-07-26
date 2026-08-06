<?php
$page_id = 565;
$page_slug = 'page_order';
include("connect.php");
$order_id	= $_REQUEST['order_id'];
$status	= $_REQUEST['status'];

// $get_approve_by_name = $db->rp_getValue("dealer_distributor_network","name","id='".$get_approve_by_id."'");

$customer_id = $db->rp_getValue("orders", "customer_id", "id='" . $order_id . "'");
$order_no = $db->rp_getValue("orders", "order_no", "id='" . $order_id . "'");
$update = false;
$txt = "Updated";

if ($status == 1) {
	$txt = "Approved";
	/*log entry*/
	$ctable = "orders";
	$last_id = $order_id;
	$flag = "Web";
	$module_name = "Order";
	$log_description = $module_name . " " . $order_no . " Status Change To " . $txt . " By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
	$db->insertLog($ctable, $last_id, "status_change", "", $insert, 0, $log_description, $flag, $module_name, $user_id, $customer_id);
	$get_approve_by_id = ($_SESSION[SITE_SESS . '_ADMIN_SESS_ID']);
	$update = $db->rp_update("orders", array("status" => $status, "approve_by_id" => $get_approve_by_id), "id='" . $order_id . "'", 0);
	/*add transaction*/
	require_once('../include/class.system.php');
	$system = new System();

	$AccountInfo = $db->rp_getData("account", "*", "cid='" . $customer_id . "'", "", 0);
	$AccountInfo = mysqli_fetch_assoc($AccountInfo);
	// $AccountInfo=$system->GetAccountInfo($invoice_data_d['customer_id']);
	if ($AccountInfo) {
		$AccountID = $AccountInfo['id'];
		$AccountNo = $AccountInfo['acc_no'];
		$Columns = array("cid", "account_id", "account_no", "type", "debit", "amount", "reference_id", "reference_table", "description", "payment_date");
		$debit = "-" . $db->rp_num($db->rp_getValue("orders", "grand_total_rounded", "id='" . $order_id . "'", 0));
		$grand_total = $db->rp_getValue("orders", "grand_total", "id='" . $order_id . "'", 0);
		$payment_date = date('Y-m-d');
		$payment_type = 0;
		$remark = "Order Entry Of Order No. <a target='_blank' href='order_viewer.php?order_id=" . $order_id . "'>" . $db->rp_getValue("orders", "order_no", "id='" . $order_id . "'", 0) . "</a>";
		$Values = array($customer_id, $AccountID, $AccountNo, $payment_type, $debit, $grand_total, $order_id, "orders", $remark, $payment_date);
		/*entry account transaction*/

		$TransctionID = $db->rp_insert("account_transaction", $Values, $Columns, 0);
	}
	/*add transaction*/


	/*log entry*/
} else if ($status == -2) {
	$txt = "Disapproved";
	$ctable = "orders";
	$last_id = $order_id;
	$flag = "Web";
	$module_name = "Order";
	$log_description = $module_name . " " . $order_no . " Status Change To " . $txt . " By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
	$db->insertLog($ctable, $last_id, "status_change", "", $insert, 0, $log_description, $flag, $module_name, $user_id, $customer_id);
	$update = $db->rp_update("orders", array("status" => $status), "id='" . $order_id . "'", 0);
} else if ($status == 3) {
	$txt = "Cancelled";
	$ctable = "orders";
	$last_id = $order_id;
	$flag = "Web";
	$module_name = "Order";
	$log_description = $module_name . " " . $order_no . " Status Change To " . $txt . " By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
	$db->insertLog($ctable, $last_id, "status_change", "", $insert, 0, $log_description, $flag, $module_name, $user_id, $customer_id);
	$update = $db->rp_update("orders", array("status" => $status), "id='" . $order_id . "'", 0);
} else if ($status == 4) {
	// Gear menu / Order Viewer → Account Approve (CP supply stays at 4; Dispatch is a separate Action step)
	$txt = "Account Approved";
	$ctable = "orders";
	$last_id = $order_id;
	$flag = "Web";
	$module_name = "Order";
	$log_description = $module_name . " " . $order_no . " Status Change To " . $txt . " By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
	$db->insertLog($ctable, $last_id, "status_change", "", $insert, 0, $log_description, $flag, $module_name, $user_id, $customer_id);
	$update = $db->rp_update("orders", array("status" => $status), "id='" . $order_id . "'", 0);
} else if ($status == 5) {
	$txt = "Dispatch";
	/* CP supply order: Account Approval required before Dispatch */
	$cpFlag = (int) $db->rp_getValue("orders", "channel_partner_order_flag", "id='" . (int) $order_id . "'", 0);
	$cpMode = $db->rp_getValue("orders", "cp_order_mode", "id='" . (int) $order_id . "'", 0);
	$cpEnd = (int) $db->rp_getValue("orders", "channel_partner_customer_id", "id='" . (int) $order_id . "'", 0);
	$curStatus = (int) $db->rp_getValue("orders", "status", "id='" . (int) $order_id . "'", 0);
	$isCpSupply = ($cpFlag === 1 && $cpMode !== 'customer' && $cpEnd <= 0);
	if ($isCpSupply && $curStatus < 4) {
		echo json_encode(array(
			"ack" => 0,
			"ack_msg" => "Account Approval required before Dispatch for Channel Partner supply order."
		));
		require_once 'disconnect.php';
		exit;
	}
	$update = $db->rp_update("orders", array("status" => $status), "id='" . $order_id . "'", 0);
	if ($update && $isCpSupply) {
		require_once dirname(__FILE__) . '/../include/class.channel_partner_stock.php';
		$stockObj = new ChannelPartnerStock($db);
		$stockObj->creditFromOrder((int) $order_id);
	}
} else if ($status == 6) {
	$txt = "Order Complete";
	$update = $db->rp_update("orders", array("status" => $status), "id='" . $order_id . "'", 0);
} else if ($status == 7) {
	$txt = "LR Pending";
	$update = $db->rp_update("orders", array("status" => $status), "id='" . $order_id . "'", 0);
}
if ($update) {

	/*$order_data = $db->rp_getData("orders","*","id='".$order_id."' AND isDelete=0 AND status=1");
	$order_data_d=mysqli_fetch_assoc($order_data);

	$grand_total = $db->rp_num(round($order_data_d['grand_total']));

	$value=$db->getlastInsertId("invoice");
	$invoice_no=INVOICE_NO.str_pad($value, 3, '0', STR_PAD_LEFT);
	
	$inser_row = array("invoice_no","customer_id","sales_executive_id","amount","invoice_date","remark","isDelete","isActive");
	$insert_value = array($invoice_no,$order_data_d['customer_id'],$order_data_d['sales_id'],$grand_total,date('Y-m-d'),"Invoice Generated",0,1);

	$insert = $db->rp_insert("invoice",$insert_value,$inser_row,0);

	require_once('../include/class.system.php');
	$system = new System();
	$AccountInfo=$system->GetAccountInfo($order_data_d['customer_id']);
	if($AccountInfo)
	{
		$AccountID=$AccountInfo['id'];
		$AccountNo=$AccountInfo['acc_no'];
		$cid = $db->rp_getValue("account","cid","isDelete=0 AND id='".$AccountID."'");
		$Columns=array("cid","account_id","account_no","type","debit","amount","reference_id","reference_table","description");
		$debit="-".$grand_total;
		$payment_type = 0;
		$remark = "Invoice Payment";
		$Values=array($cid,$AccountID,$AccountNo,$payment_type,$debit,$grand_total,$insert,"invoice",$remark);
		$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);
	}*/

	require_once("../include/push_notification.class.php");
	$objPushNotification = new PushNotification();

	if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 0) {
		if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) //sales executive 
		{
			$status_change_name = $db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
		}
		if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) //customer  
		{
			$status_change_name = $db->rp_getValue("executive", "cname", "isDelete=0 AND id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
		}
	} else {
		$status_change_name = "Admin";
	}
	$notification_description = "Order No " . $order_no . " has been " . $txt . " by " . $status_change_name;

	// send sales executive notification added by shivani     
	$sales_id = $db->rp_getValue("orders", "sales_id", "id='" . $order_id . "'");
	$objPushNotification->commonNotification($sales_id, $order_id, "orders", "Order Status Change", $notification_description, "sales_executive", "orders");
	// send sales executive notification added by shivani 

	// send customer notification added by shivani
	$customer_id = $db->rp_getValue("orders", "customer_id", "id='" . $order_id . "'");
	$objPushNotification->commonNotification($customer_id, $order_id, "orders", "Order Status Change", $notification_description, "customer", "orders");
	// send customer notification added by shivani 

	// send customer upper chanel notification added by shivani 
	$customer_type  = $db->rp_getValue("orders", "customer_type", "id='" . $order_id . "'");
	if ($customer_type == 2)  //distributor
	{
		$upper_chanel_id = $db->rp_getValue("executive", "super_stockist_id", "id='" . $customer_id . "'");
	} else if ($customer_type == 3) //retailer 
	{
		$upper_chanel_id = $db->rp_getValue("executive", "dealer_distributor_id", "id='" . $customer_id . "'");
	}
	$objPushNotification->commonNotification($upper_chanel_id, $order_id, "orders", "Order Status Change", $notification_description, "customer", "orders");
	// send customer upper chanel notification added by shivani 

	$reply = array("ack" => 1, "ack_msg" => "Order " . $txt . " Successfully");
} else {
	$reply = array("ack" => 0, "ack_msg" => "Order " . $txt . " Updated Failed");
}

echo json_encode($reply);
