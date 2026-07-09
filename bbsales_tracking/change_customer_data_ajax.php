<?php
$page_id = 400;
$page_slug = 'dashboard';
include("connect.php");
$mode = $_REQUEST['mode'];
if ($mode == "quotation") {
	$Check_Order = $db->rp_getTotalRecord("orders", "quotation_id='" . $_REQUEST['edit_id'] . "' AND isDelete=0");
	if ($Check_Order == 0) {
		$old_customer = $db->rp_getValue("quotation_detail", "customer_name", "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0);
		$old_customer_phone = $db->rp_getValue("quotation_detail", "contact_number", "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0);
		$CustomerR = $db->rp_getData("executive", "*", "id='" . $_REQUEST['customer_id'] . "' AND isDelete=0 AND isActive=1");
		$CustomerD 	= mysqli_fetch_assoc($CustomerR);
		if ($CustomerD) {
			$update_Rows = array(
				"customer_id"      => $CustomerD['id'],
				"company_name"     => $CustomerD['company_name'],
				"customer_name"    => $CustomerD['cname'],
				"client_code"    => $CustomerD['client_code'],
				"customer_type"    => $CustomerD['type_of_executive'],
				"contact_number"   => $CustomerD['phone'],
				"address"          => $CustomerD['address'],
				"city"             => $CustomerD['city'],
				"state"            => $CustomerD['state'],
				"country"          => $CustomerD['country'],
				"email"            => $CustomerD['email'],
				"gst"              => $CustomerD['gst'],
				"shipping_address" => $CustomerD['shipping_address'],
				"billing_address"  => $CustomerD['billing_address'],
			);
			/*log entry*/
			$quotation_no = $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $_REQUEST['edit_id'] . "'");
			$module_name = "Quotation";
			$flag = "Web";
			$log_description = "Quotation " . $quotation_no . " Customer Changed " . $old_customer . "-" . $old_customer_phone . " To " . $CustomerD['cname'] . "-" . $CustomerD['phone'] . " Done By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");

			/*log entry*/
			$update = $db->rp_update("quotation_detail", $update_Rows, "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0, $log_description, $flag, $module_name, "", "");

			if ($update) {
				$reply = array("ack" => 1, "ack_msg" => "Data Updated Successfully");
			} else {
				$reply = array("ack" => 0, "ack_msg" => "Data Updated Failed");
			}
		} else {
			$reply = array("ack" => 0, "ack_msg" => "This Customer Is Delete Or Deactivated.Please Check..");
		}
	} else {
		$reply = array("ack" => 0, "ack_msg" => "You Can Not Change Customer For This Quotation Because Already Order Generated For This Quotation.");
	}
	echo json_encode($reply);
}

if ($mode == "order") {
	$Check_Dispatch = $db->rp_getTotalRecord("dispatch_detail", "order_id='" . $_REQUEST['edit_id'] . "' AND isDelete=0");
	if ($Check_Dispatch == 0) {
		$old_customer = $db->rp_getValue("orders", "customer_name", "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0);
		$old_customer_phone = $db->rp_getValue("orders", "contact_number", "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0);
		$CustomerR = $db->rp_getData("executive", "*", "id='" . $_REQUEST['customer_id'] . "' AND isDelete=0 AND isActive=1");
		$CustomerD 	= mysqli_fetch_assoc($CustomerR);
		if ($CustomerD) {
			$update_Rows = array(
				"customer_id"      => $CustomerD['id'],
				"company_name"     => $CustomerD['company_name'],
				"customer_name"    => $CustomerD['cname'],
				"customer_type"    => $CustomerD['type_of_executive'],
				"contact_number"   => $CustomerD['phone'],
				"address"          => $CustomerD['address'],
				"city"             => $CustomerD['city'],
				"state"            => $CustomerD['state'],
				"country"          => $CustomerD['country'],
				"email"            => $CustomerD['email'],
				"gst"              => $CustomerD['gst'],
				"shipping_address" => $CustomerD['shipping_address'],
				"billing_address"  => $CustomerD['billing_address'],
			);
			/*log entry*/
			$order_no = $db->rp_getValue("orders", "order_no", "id='" . $_REQUEST['edit_id'] . "'");
			$module_name = "Order";
			$flag = "Web";
			$log_description = "Order " . $order_no . " Customer Changed " . $old_customer . "-" . $old_customer_phone . " To " . $CustomerD['cname'] . "-" . $CustomerD['phone'] . " Done By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");

			/*log entry*/
			$update = $db->rp_update("orders", $update_Rows, "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0, $log_description, $flag, $module_name, "", "");

			/*update new customer id in quotation*/
			$quotation_id = $db->rp_getValue("orders", "quotation_id", "id='" . $_REQUEST['edit_id'] . "'");
			$rows_array = array("customer_id" => $CustomerD['id']);
			$update = $db->rp_update("quotation_detail", $update_Rows, "id='" . $quotation_id . "' AND isDelete=0", 0);
			/*update new customer id in quotation*/
			if ($update) {
				$reply = array("ack" => 1, "ack_msg" => "Data Updated Successfully");
			} else {
				$reply = array("ack" => 0, "ack_msg" => "Data Updated Failed");
			}
		} else {
			$reply = array("ack" => 0, "ack_msg" => "This Customer Is Delete Or Deactivated.Please Check..");
		}
	} else {
		$reply = array("ack" => 0, "ack_msg" => "You Can Not Change Customer For This Quotation Because Already Order Generated For This Quotation.");
	}
	echo json_encode($reply);
}

if ($mode == "invoice") {
	$old_customer = $db->rp_getValue("invoice_new", "customer_name", "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0);
	$old_customer_phone = $db->rp_getValue("invoice_new", "contact_number", "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0);

	$CustomerR = $db->rp_getData("executive", "*", "id='" . $_REQUEST['customer_id'] . "' AND isDelete=0 AND isActive=1");

	$CustomerD 	= mysqli_fetch_assoc($CustomerR);
	if ($CustomerD) {
		$update_Rows = array(
			"customer_id"      => $CustomerD['id'],
			"company_name"     => $CustomerD['company_name'],
			"customer_name"    => $CustomerD['cname'],
			"customer_type"    => $CustomerD['type_of_executive'],
			"contact_number"   => $CustomerD['phone'],
			"address"          => $CustomerD['address'],
			"city"             => $CustomerD['city'],
			"state"            => $CustomerD['state'],
			"country"          => $CustomerD['country'],
			"email"            => $CustomerD['email'],
			"gst"              => $CustomerD['gst'],
			"shipping_address" => $CustomerD['shipping_address'],
			"billing_address"  => $CustomerD['billing_address'],
		);
		/*log entry*/
		$invoice_no = $db->rp_getValue("invoice_new", "invoice_no", "id='" . $_REQUEST['edit_id'] . "'");
		$module_name = "Invoice";
		$flag = "Web";
		$log_description = "Invoice " . $order_no . " Customer Changed " . $old_customer . "-" . $old_customer_phone . " To " . $CustomerD['cname'] . "-" . $CustomerD['phone'] . " Done By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");

		/*log entry*/
		$update = $db->rp_update("invoice_new", $update_Rows, "id='" . $_REQUEST['edit_id'] . "' AND isDelete=0", 0, $log_description, $flag, $module_name, "", "");


		/*update new customer id in packingslip*/
		$dispatch_id     = $db->rp_getValue("invoice_new", "dispatch_ids", "id='" . $_REQUEST['edit_id'] . "'");
		$packing_slip_id = $db->rp_getValue("packing_slip", "id", "dispatch_id='" . $dispatch_id . "'");
		$rows_array = array("customer_id" => $CustomerD['id']);
		$update = $db->rp_update("packing_slip", $rows_array, "id='" . $packing_slip_id . "' AND isDelete=0", 0);
		/*update new customer id in packingslip*/

		/*update new customer id in dispatch*/
		$dispatch_id     = $db->rp_getValue("invoice_new", "dispatch_ids", "id='" . $_REQUEST['edit_id'] . "'");
		$rows_array_dispatch =  array(
			"customer_id"      => $CustomerD['id'],
			"company_name"     => $CustomerD['company_name'],
			"customer_name"    => $CustomerD['cname'],
			"order_type"   	   => $CustomerD['type_of_executive'],
			"contact_number"   => $CustomerD['phone'],
			"address"          => $CustomerD['address'],
			"city"             => $CustomerD['city'],
			"state"            => $CustomerD['state'],
			"country"          => $CustomerD['country'],
			"email"            => $CustomerD['email'],
			"shipping_address" => $CustomerD['shipping_address'],
			"billing_address"  => $CustomerD['billing_address'],
		);
		$update = $db->rp_update("dispatch_detail", $rows_array_dispatch, "id='" . $dispatch_id . "' AND isDelete=0", 0);
		/*update new customer id in dispatch*/

		/*update new customer id in order*/
		$dispatch_id     = $db->rp_getValue("invoice_new", "dispatch_ids", "id='" . $_REQUEST['edit_id'] . "'");
		$orders_ID       = $db->rp_getValue("dispatch_detail", "order_id", "id='" . $dispatch_id . "'");
		$update = $db->rp_update("orders", $update_Rows, "id='" . $orders_ID . "' AND isDelete=0", 0);
		/*update new customer id in order*/

		/*update new customer id in quotation*/
		$orders_ID       = $db->rp_getValue("dispatch_detail", "order_id", "id='" . $dispatch_id . "'");
		$quotation_id = $db->rp_getValue("orders", "quotation_id", "id='" . $orders_ID . "'");
		$update = $db->rp_update("quotation_detail", $update_Rows, "id='" . $quotation_id . "' AND isDelete=0", 0);
		/*update new customer id in quotation*/

		// update in payment receipt
		$db->rp_update("payment", array("customer_id" => $CustomerD['id'], "customer_type" => $CustomerD['type_of_executive']), "invoice_id='" . $_REQUEST['edit_id'] . "'", 0);

		$AccountInfo = $db->rp_getData("account", "*", "cid='" . $CustomerD['id'] . "'", "", 0);
		$AccountInfo = mysqli_fetch_assoc($AccountInfo);
		$db->rp_update("account_transaction", array("cid" => $CustomerD['id'], "account_id" => $AccountInfo['id'], "account_no" => $AccountInfo['acc_no']), "invoice_id='" . $_REQUEST['edit_id'] . "'", 0);
		// update in payment receipt


		if ($update) {
			$reply = array("ack" => 1, "ack_msg" => "Data Updated Successfully");
		} else {
			$reply = array("ack" => 0, "ack_msg" => "Data Updated Failed");
		}
	} else {
		$reply = array("ack" => 0, "ack_msg" => "This Customer Is Delete Or Deactivated.Please Check..");
	}
	echo json_encode($reply);
}
?>
<?php require_once 'disconnect.php';  ?>