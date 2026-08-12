<?php
	$page_id=565;$page_slug='page_order';
	$ctable 	= "orders";
	$ctable1 	= "Orders"; 
	$page_title = ucwords($_REQUEST['mode']) . " " . $ctable1;
	$c_type = $_REQUEST['c_type'];

	$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"dealer_orders_manage.php?type='".$_REQUEST['c_type']."'","title"=>$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));

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

	// Channel Partner self-order: lock customer to logged-in CP and prefill profile fields
	$is_cp_self_order = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
	$cp_login_id = function_exists('cp_get_login_channel_partner_id') ? cp_get_login_channel_partner_id() : 0;
	$cp_exec_d = array();
	$cp_company_label = "";
	$cp_type_label = "";
	$cp_price_list_name = "";
	$cp_gst_type = "";
	if ($is_cp_self_order && $cp_login_id > 0) {
		$c_type = 'channel_partner';
		$_REQUEST['c_type'] = 'channel_partner';
		$cp_r = $db->rp_getData("executive", "*", "id='" . (int) $cp_login_id . "' AND isDelete=0", "", 0);
		if ($cp_r) {
			$cp_exec_d = mysqli_fetch_assoc($cp_r);
		}
		$selected_cp_customer_id = 0;
		if (isset($_REQUEST['cp_customer_id']) && (int) $_REQUEST['cp_customer_id'] > 0) {
			$selected_cp_customer_id = (int) $_REQUEST['cp_customer_id'];
		} else if (isset($_REQUEST['channel_partner_customer_id']) && (int) $_REQUEST['channel_partner_customer_id'] > 0) {
			$selected_cp_customer_id = (int) $_REQUEST['channel_partner_customer_id'];
		}
		$cp_customers_r = $db->rp_getData(
			"channel_partner_customer",
			"*",
			"channel_partner_id='" . (int) $cp_login_id . "' AND isDelete=0",
			"company_name ASC",
			0
		);
		if (!empty($cp_exec_d) && isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add" && (!isset($_REQUEST['order_id']) || $_REQUEST['order_id'] == "")) {
			$customer_id = (int) $cp_login_id;
			$type_of_company = isset($cp_exec_d['type_of_company']) ? $cp_exec_d['type_of_company'] : "";
			$customer_type = isset($cp_exec_d['type_of_executive']) ? $cp_exec_d['type_of_executive'] : "";
			$sales_executive_id = !empty($cp_exec_d['seid']) ? $cp_exec_d['seid'] : "";
			$name_gstin = isset($cp_exec_d['gst']) ? $cp_exec_d['gst'] : "";
			$billing_address = !empty($cp_exec_d['billing_address']) ? $cp_exec_d['billing_address'] : (isset($cp_exec_d['address']) ? $cp_exec_d['address'] : "");
			$shipping_address = !empty($cp_exec_d['shipping_address']) ? $cp_exec_d['shipping_address'] : $billing_address;
			$booking_pincode = isset($cp_exec_d['zip']) ? $cp_exec_d['zip'] : "";
			if (!empty($cp_exec_d['booking_place'])) {
				$booking_place = $cp_exec_d['booking_place'];
			} else {
				$booking_place = trim((isset($cp_exec_d['main_city']) ? $cp_exec_d['main_city'] : '') . (isset($cp_exec_d['state']) && $cp_exec_d['state'] != '' ? ', ' . $cp_exec_d['state'] : ''));
			}
			$cash_discount = isset($cp_exec_d['cash_discount']) ? $cp_exec_d['cash_discount'] : "";
			$additional_discount = isset($cp_exec_d['additional_discount']) ? $cp_exec_d['additional_discount'] : "";

			/* Prefill delivery fields from selected end-customer when opened via Add Order link */
			if ($selected_cp_customer_id > 0) {
				$pre_cp_cust_r = $db->rp_getData(
					"channel_partner_customer",
					"*",
					"id='" . (int) $selected_cp_customer_id . "' AND channel_partner_id='" . (int) $cp_login_id . "' AND isDelete=0",
					"",
					0
				);
				if ($pre_cp_cust_r && $pre_cp_cust = mysqli_fetch_assoc($pre_cp_cust_r)) {
					$name_gstin = !empty($pre_cp_cust['gst']) ? $pre_cp_cust['gst'] : $name_gstin;
					$addrParts = array_filter(array($pre_cp_cust['city'], $pre_cp_cust['state'], $pre_cp_cust['pincode'], $pre_cp_cust['country']));
					$endAddr = implode(', ', $addrParts);
					if ($endAddr != "") {
						$billing_address = $endAddr;
						$shipping_address = $endAddr;
					}
					$booking_place = !empty($pre_cp_cust['city']) ? ($pre_cp_cust['city'] . (!empty($pre_cp_cust['state']) ? ', ' . $pre_cp_cust['state'] : '')) : $booking_place;
					$booking_pincode = !empty($pre_cp_cust['pincode']) ? $pre_cp_cust['pincode'] : $booking_pincode;
				}
			}
		}
		if (!empty($cp_exec_d)) {
			$cp_company_label = $db->rp_getValue("company_master", "name", "id='" . (int) $cp_exec_d['type_of_company'] . "'", 0);
			$cp_type_label = $db->rp_getValue("customer_type", "name", "id='" . (int) $cp_exec_d['type_of_executive'] . "'", 0);
			$cp_price_list_name = $db->rp_getValue("price_list", "pricelist_name", "id='" . (int) $cp_exec_d['price_list_id'] . "'", 0);
			if ($cp_price_list_name == "") {
				$cp_price_list_name = "N/A";
			}
			if (defined('CLIENT_STATE') && strtolower(CLIENT_STATE) == strtolower($cp_exec_d['state'])) {
				$cp_gst_type = "(CGST:9%,SGST:9%)";
			} else {
				$cp_gst_type = "(IGST:18%)";
			}
		}
	} else {
		$selected_cp_customer_id = 0;
		$cp_customers_r = false;
	}

	// print_r($_REQUEST);exit;
	// $invoice_no=INVOICE_NO.str_pad($order_no."/20-21", 8, '0', STR_PAD_LEFT);
	//$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
	if (isset($_REQUEST['submit'])) {
		// echo "<pre>";
		// print_r($_REQUEST);die;
		$item = array();
		$detail = array();
		$detail['order_id'] = isset($_REQUEST['order_id']) ? $db->clean($_REQUEST['order_id']) : "";
		$quotation_id = $db->rp_getValue('orders', 'quotation_id', "id=".$_REQUEST['id']." AND customer_id=".$_REQUEST['edit_customer_id'], 0);
		$isActive					= 1;
		$detail['isDelete']			= 0;
		$detail['remarks']			= html_entity_decode($_REQUEST['remarks']);
		$detail['terms_comdition']	= isset($_REQUEST['terms_comdition'])?html_entity_decode($_REQUEST['terms_comdition']):"";
		$detail['faithfully']	    = isset($_REQUEST['faithfully'])?html_entity_decode($_REQUEST['faithfully']):"";
		$detail['vendor_code']	      = isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
		$detail['tendor_code']	      = isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
		$detail['uid'] 				= $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
		$detail['cash_discount']	= isset($_REQUEST['cash_discount'])?$db->clean($_REQUEST['cash_discount']):"";
		$detail['additional_discount']	= isset($_REQUEST['additional_discount'])?$db->clean($_REQUEST['additional_discount']):"";
		$detail['cash_discount_amount']	= isset($_REQUEST['cash_discount_amount'])?$db->clean($_REQUEST['cash_discount_amount']):"";
		$detail['additional_discount_amount']	= isset($_REQUEST['addtional_discount_amount'])?$db->clean($_REQUEST['addtional_discount_amount']):"";
		$detail['gst_apply_flag']	= isset($_REQUEST['gst_apply_flag'])?$db->clean($_REQUEST['gst_apply_flag']):"";
		$detail['tcs_apply_flag']	= isset($_REQUEST['tcs_apply_flag'])?$db->clean($_REQUEST['tcs_apply_flag']):"";
		$detail['transport_charge_gst']	= isset($_REQUEST['transport_charge_per'])?$db->clean($_REQUEST['transport_charge_per']):"";
		$detail['packing_charge_gst']	= isset($_REQUEST['packing_charge_per'])?$db->clean($_REQUEST['packing_charge_per']):"";
		$detail['cd_gst']	= isset($_REQUEST['cd_gst'])?$db->clean($_REQUEST['cd_gst']):"";
		$detail['ad_gst']	= isset($_REQUEST['ad_gst'])?$db->clean($_REQUEST['ad_gst']):"";
		$detail['booking_place']	= isset($_REQUEST['booking_place'])?$db->clean($_REQUEST['booking_place']):"";
		$detail['booking_pincode']	= isset($_REQUEST['booking_pincode'])?$db->clean($_REQUEST['booking_pincode']):"";


		//Insert Orders item
		// var_dump($_REQUEST);exit;
		$product_id     = $_REQUEST['product_id'];
		$qty            = $_REQUEST['qty'];
		$original_price = $_REQUEST['original_price'];
		$price          = $_REQUEST['price'];
		$pro_name       = $_REQUEST['pro_name'];
		$weight_id      = $_REQUEST['weight_id'];
		$box_qty        = $_REQUEST['box_qty'];
		$bag            = $_REQUEST['bag'];
		$loose          = $_REQUEST['loose'];
		$brand_id       = $_REQUEST['brand_id'];
		$pro_description = $_REQUEST['pro_description'];
		$cd_discount = $_REQUEST['cd_discount'];
		$ad_discount = $_REQUEST['ad_discount'];
		$gst_amount_item = $_REQUEST['gst_amount_item'];
		$taxable_amount = $_REQUEST['taxable_amount'];
		$sub_total = $_REQUEST['sub_total'];
		$other_charge = $_REQUEST['other_charge'];
		$fright_charge = $_REQUEST['fright_charge'];
		$discount = $_REQUEST['discount'];
		$discount_amount = $_REQUEST['discount_amount'];
		$is_including = $_REQUEST['is_including'];
		$item_order_unit	 = $_REQUEST['item_order_unit'];
		$order_qty = $_REQUEST['order_qty'];
		$order_item_brand_id = $_REQUEST['order_item_brand_id'];


		$size[] = sizeof($product_id);
		$size[] = sizeof($qty);
		$size[] = sizeof($price);
		$size[] = sizeof($pro_name);
		$size[] = sizeof($pro_description);

		$brand_id[] = sizeof($brand_id);

		$weight_id[] = sizeof($weight_id);
		$box_qty[] = sizeof($box_qty);
		$loose[] = sizeof($loose);

		$value_check = sizeof($product_id);
		$is_including[] = sizeof($is_including);
		$order_item_brand_id[] = sizeof($order_item_brand_id);

		if (in_array($value_check, $size)) {
			$isValidArray = true;
		} else {
			$isValidArray = false;
		}
		// print_r($product_id);exit;
		/*"cd_discount"=>$cd_discount[$i],"ad_discount"=>$ad_discount[$i]*/
		if ($isValidArray && !empty($product_id)) {
			for ($i = 0; $i < sizeof($product_id); $i++) {
				/*$item[]=array("qty"=>$qty[$i],"pid"=>$product_id[$i],"price"=>$price[$i],"pro_name"=>$pro_name[$i],"weight_id"=>$weight_id[$i],"cartoon_qty"=>$box_qty[$i],"box_qty"=>$bag[$i]);*/
				$item[] = array("qty" => $qty[$i], "pid" => $product_id[$i], "original_price" => $original_price[$i], "price" => $price[$i], "pro_name" => $pro_name[$i], "weight_id" => $weight_id[$i], "cartoon_qty" => $box_qty[$i], "box_qty" => $bag[$i], "loose"=>$loose[$i], "brand_id" => $brand_id[$i] , "pro_description" =>$pro_description[$i],"cd_discount"=>$cd_discount[$i],"ad_discount"=>$ad_discount[$i],"gst_amount_item"=>$gst_amount_item[$i],"taxable_amount"=>$taxable_amount[$i],"sub_total"=>$sub_total[$i],"other_charge"=>$other_charge[$i],"fright_charge"=>$fright_charge[$i],"discount_amount"=>$discount_amount[$i],"discount" => $discount[$i],"is_including"=>$is_including[$i],"item_order_unit"=>$item_order_unit[$i],"order_qty"=>$order_qty[$i],"order_item_brand_id"=>$order_item_brand_id[$i]);
			}
		}
		// print_r($item); exit;
		if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "add") {
			$detail['cid']                = $db->clean($_REQUEST['customer_id']);
			$detail['customer_id']        = $db->clean($_REQUEST['customer_id']);
			//detail['sales_executive_id'] = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
			$detail['sales_executive_id'] = (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != 0 && !empty($_REQUEST['sales_executive'])) ? $_REQUEST['sales_executive'] : $_SESSION[SITE_SESS . 'REFERANCE_ID'];
			$detail['order_date']         = date('Y-m-d', strtotime($_REQUEST['order_date']));
			/*$detail['phone']	= $db->clean($_REQUEST['name_phone']);
			$detail['email']	= $db->clean($_REQUEST['name_email']);
			$detail['address']	= $db->clean($_REQUEST['name_address']);
			$detail['customer_po_no']	= $db->clean($_REQUEST['customer_po_no']);
			$detail['booking']	= isset($_REQUEST['booking'])?$db->clean($_REQUEST['booking']):"";
			$detail['transport']	= isset($_REQUEST['transport'])?$db->clean($_REQUEST['transport']):"";*/
			$detail['order_no']		= isset($_REQUEST['order_no']) ? $db->clean($_REQUEST['order_no']) : "";
			$detail['brand_id']		= isset($_REQUEST['brand_id']) ? $db->clean($_REQUEST['brand_id']) : "";


			$detail['chalan_no']	= isset($_REQUEST['chalan_no']) ? $db->clean($_REQUEST['chalan_no']) : "";
			$detail['po_no']		= isset($_REQUEST['po_no']) ? $db->clean($_REQUEST['po_no']) : "";
			$detail['po_date']		= date('Y-m-d', strtotime($_REQUEST['po_date']));

			$detail['terms_comdition']	  = isset($_REQUEST['terms_comdition'])?html_entity_decode($_REQUEST['terms_comdition']):"";
			$detail['faithfully']	      = isset($_REQUEST['faithfully'])?html_entity_decode($_REQUEST['faithfully']):"";

			$detail['transport_name']	  = isset($_REQUEST['transport_name'])?trim($_REQUEST['transport_name']):"";
			$detail['transport_through']  = isset($_REQUEST['transport_through'])?trim($_REQUEST['transport_through']):"";
			$detail['transport_charge']	  = isset($_REQUEST['transport_charge'])?trim($_REQUEST['transport_charge']):"";
			$detail['packing_charge']	  = isset($_REQUEST['packing_charge'])?$db->clean($_REQUEST['packing_charge']):"";
			$detail['shipping_address']	  = isset($_REQUEST['shipping_address'])?trim($_REQUEST['shipping_address']):"";
			$detail['billing_address']	  = isset($_REQUEST['billing_address'])?trim($_REQUEST['billing_address']):"";
			$detail['name_gstin']	  	  = isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";
			$detail['apply_scheme']	  	  = isset($_REQUEST['apply_scheme'])?trim($_REQUEST['apply_scheme']):"";
			$detail['type_of_company']	= isset($_REQUEST['type_of_company'])?$db->clean($_REQUEST['type_of_company']):"";
			$detail['terms_condition_id']	= isset($_REQUEST['terms_condition_id'])?$db->clean($_REQUEST['terms_condition_id']):"";
			$detail['max_dispatch_date']		= date('Y-m-d', strtotime($_REQUEST['max_dispatch_date']));
			$detail['channel_partner_order_flag'] = (isset($_REQUEST['c_type']) && $_REQUEST['c_type'] == 'channel_partner') ? 1 : ((isset($_REQUEST['channel_partner_order_flag']) && $_REQUEST['channel_partner_order_flag'] == 1) ? 1 : 0);
			if (function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db)) {
				$detail['cid'] = (int) cp_get_login_channel_partner_id();
				$detail['customer_id'] = (int) cp_get_login_channel_partner_id();
				$detail['channel_partner_order_flag'] = 1;
				$cp_end_id = isset($_REQUEST['channel_partner_customer_id']) ? (int) $_REQUEST['channel_partner_customer_id'] : 0;
				if ($cp_end_id <= 0) {
					$db->addErrorMessage("Please select your Customer for this order.");
					$db->rp_location("orders_crud.php?mode=add&c_type=channel_partner");
				}
				$ownCpCust = $db->rp_getTotalRecord(
					"channel_partner_customer",
					"id='" . $cp_end_id . "' AND channel_partner_id='" . (int) cp_get_login_channel_partner_id() . "' AND isDelete=0",
					0
				);
				if ($ownCpCust <= 0) {
					$db->addErrorMessage("Invalid customer selected.");
					$db->rp_location("orders_crud.php?mode=add&c_type=channel_partner");
				}
				$detail['channel_partner_customer_id'] = $cp_end_id;
			}

		 

			if ($rights['insert_flag'] != 1) {
				$db->rp_location('access_denied.php?msg=delete_access_denied');
			}
			// print_r($detail);exit;


			// $reply=$objOrder->InsertOrder($detail,$item);
			$reply = $objOrder->AddToCart($detail, $item);
			if ($reply['ack'] == 1) {
				unset($detail['order_date']);
				$detail['cash_discount_flag'] 				= 0;
				$detail['order_id'] 									= $reply['order_id'];
				$detail['sales_executive_id'] = (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != 0 && !empty($_REQUEST['sales_executive'])) ? $_REQUEST['sales_executive'] : $_SESSION[SITE_SESS . 'REFERANCE_ID'];
				$detail['name_gstin']	  							= isset($_REQUEST['name_gstin'])?trim($_REQUEST['name_gstin']):"";
				$detail['vendor_code']	      				= isset($_REQUEST['vendor_code'])?trim($_REQUEST['vendor_code']):"";
				$detail['tendor_code']	      				= isset($_REQUEST['tendor_code'])?trim($_REQUEST['tendor_code']):"";
				$detail['cash_discount']							= isset($_REQUEST['cash_discount'])?$db->clean($_REQUEST['cash_discount']):"";
				$detail['additional_discount']				= isset($_REQUEST['additional_discount'])?$db->clean($_REQUEST['additional_discount']):"";
				$detail['cash_discount_amount']				= isset($_REQUEST['cash_discount_amount'])?$db->clean($_REQUEST['cash_discount_amount']):"";
				$detail['additional_discount_amount']	= isset($_REQUEST['addtional_discount_amount'])?$db->clean($_REQUEST['addtional_discount_amount']):"";
				$detail['gst_apply_flag']							= isset($_REQUEST['gst_apply_flag'])?$db->clean($_REQUEST['gst_apply_flag']):""; 
				$detail['tcs_apply_flag']							= isset($_REQUEST['tcs_apply_flag'])?$db->clean($_REQUEST['tcs_apply_flag']):"";
				$detail['round_off']									= isset($_REQUEST['round_off'])?$db->clean($_REQUEST['round_off']):"";

				$objOrder->PlaceOrderPanel($detail);
				$db->addSuccessMessage($reply['ack_msg']);
				$db->rp_location("dealer_orders_manage.php?type=".$_REQUEST['c_type']);
				//for redirect to location after insert
				$db->rp_location("dealer_orders_manage.php?msg=inserted&type=".$_REQUEST['c_type']);
			} else {
			$db->addErrorMessage($reply['ack_msg']);
			}
		} else if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "edit") {
			if ($rights['update_flag'] != 1) {
				$db->rp_location('access_denied.php?msg=delete_access_denied');
			}

			// $reply=$objOrder->UpdateProductItem($detail,$item);
			$detail['customer_id']        = $db->clean($_REQUEST['edit_customer_id']);
			$detail['sales_executive_id'] = (isset($_REQUEST['sales_executive']) && $_REQUEST['sales_executive'] != "" && $_REQUEST['sales_executive'] != 0 && !empty($_REQUEST['sales_executive'])) ? $_REQUEST['sales_executive'] : $_SESSION[SITE_SESS . 'REFERANCE_ID'];
			$detail['cid']                = $db->clean($_REQUEST['edit_customer_id']);
			$detail['order_id']           = $db->clean($_REQUEST['id']);

			$detail['chalan_no']          = isset($_REQUEST['chalan_no'])?($_REQUEST['chalan_no']):"";
			$detail['po_no']           	  = isset($_REQUEST['po_no'])?($_REQUEST['po_no']):"";
			$detail['po_date']            = $db->clean($_REQUEST['po_date']);
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
			$detail['round_off']						= isset($_REQUEST['round_off'])?$db->clean($_REQUEST['round_off']):"";
			$detail['type_of_company']	      = isset($_REQUEST['type_of_company'])?trim($_REQUEST['type_of_company']):""; 
			$detail['terms_condition_id']	      = isset($_REQUEST['terms_condition_id'])?trim($_REQUEST['terms_condition_id']):""; 
			$detail['max_dispatch_date']            = $db->clean($_REQUEST['max_dispatch_date']);
				// echo "<pre>";
				// print_r($detail);exit;
			$reply = $objOrder->UpdateOrder($detail, $item);
			// print_r($reply);exit;
			if ($reply['ack'] == 1) {
				$db->rp_update('quotation_detail', ['status' => 4], "id=".$quotation_id, $die=0);
				unset($detail['order_date']);
				$detail['cash_discount_flag'] = 0;
				$detail['order_id'] = $reply['order_id'];
				// $detail['sales_executive_id'] = "";
				$objOrder->PlaceOrderPanel($detail);
				$db->addSuccessMessage($reply['ack_msg']);
				$type = $db->rp_getValue("orders", "customer_type", "id='" . $reply['type'] . "'");
				$db->rp_location("dealer_orders_manage.php?type=".$_REQUEST['c_type']);
			} else {
				$db->addErrorMessage($reply['ack_msg']);
				$db->rp_location("orders_crud.php?mode=edit&id=".$_REQUEST['id']."&type=".$_REQUEST['c_type']);
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
		// print_r($item_info);exit;
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

			$db->rp_update("account_transaction",array("isDelete"=>1),"reference_id='".$_REQUEST['id']."' AND reference_table='orders'",0);

			//for redirect to location after Delete
			$db->rp_location("dealer_orders_manage.php?msg=deleted&type=".$_REQUEST['c_type']);
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

	// for repeat order
	if (isset($_REQUEST['order_id']) && $_REQUEST['order_id'] > 0 && $_REQUEST['mode'] == "add") 
	{
		// echo "hello";exit;
		// $generate_invoice_flag = $db->rp_getValue("orders", "generate_invoice_flag", "id=" . $_REQUEST['order_id'] . " AND isDelete=0", 0);

		// if ($rights['update_flag'] != 1 || $generate_invoice_flag == 1) {
		// $db->rp_location('access_denied.php?msg=delete_access_denied');
		// }
		$detail['id'] = $_REQUEST['order_id'];
		/*$OrderNo = $db->rp_getValue("orders", "order_no", "id=" . $_REQUEST['order_id'] . " AND isDelete=0", 0);
		$Order = $db->rp_getValue("orders", "customer_id", "id=" . $_REQUEST['order_id'] . " AND isDelete=0", 0);
		$customer_name = $db->rp_getValue("customer", "company_name", "id=" . $Order . " AND isDelete=0", 0);
		$page_title = ucwords($_REQUEST['mode']) . '&nbsp' . "Order" . "- " . ucwords($OrderNo) . '&nbsp';*/
		$reply = $objOrder->GetOrder($detail);
		$item_info = $objOrder->GetOrderItems($detail);

		if ($reply['ack'] == 1) { 
		$result = $reply['result']; 
		// print_r($result);exit; 
		extract($result);
		}
		if ($item_info['ack'] == 1) { 
		$item_info = $item_info['result'];
		} else {
		$item_info = array();
		}

		// $order_no1     = $db->getLastInsertId("orders"); 
		// $order_no = OUTLETS_ORDER_NO . str_pad($order_no1, 2, '0', STR_PAD_LEFT); 

		// fyear wise code generate by shivani
		$order_no     = $db->getLastInsertId("orders");
		// fyear wise code generate by shivani

		$order_date = date("d-m-Y");
	}
	// for repeat order
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
					$back = 'dealer_orders_manage.php';
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
						<input type="hidden" name="c_type" id="c_type" value="<?= htmlspecialchars($c_type); ?>">
						<input type="hidden" name="channel_partner_order_flag" id="channel_partner_order_flag" value="<?= ($c_type == 'channel_partner') ? '1' : '0'; ?>">
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
											<?php

											if ($_REQUEST['mode'] == "edit") {
											$disabled = "disabled";
											} else {
											$disabled = "";
											}
											// echo $type_of_company;die;
											?>
											<div class="col-md-4 col-sm-4">
												<?php if (!empty($is_cp_self_order) && !empty($cp_exec_d)) {
													$cpPhone = !empty($cp_exec_d['phone']) ? $cp_exec_d['phone'] : $cp_exec_d['mobile_no1'];
													$shipAddr = !empty($cp_exec_d['shipping_address']) ? $cp_exec_d['shipping_address'] : $cp_exec_d['address'];
													$billAddr = !empty($cp_exec_d['billing_address']) ? $cp_exec_d['billing_address'] : $cp_exec_d['address'];
													$is_channel_partner_order = true;
												?>
												<div class="form-group">
													<label>Company</label>
													<input type="hidden" name="type_of_company" id="type_of_company" value="<?php echo htmlentities($cp_exec_d['type_of_company']); ?>">
													<input type="text" class="form-control" value="<?php echo htmlentities($cp_company_label); ?>" readonly>
												</div>
												<div class="form-group">
													<label>Customer Type</label>
													<input type="hidden" name="customer_type" id="customer_type" value="<?php echo htmlentities($cp_exec_d['type_of_executive']); ?>">
													<input type="text" class="form-control" value="<?php echo htmlentities($cp_type_label); ?>" readonly>
												</div>
												<div class="form-group">
													<label>Channel Partner</label>
													<?php if ($_REQUEST['mode'] == "edit") { ?>
														<input type="hidden" name="edit_customer_id" id="edit_customer_id" value="<?php echo (int) $cp_login_id; ?>" class="customer_id_s">
													<?php } ?>
													<select class="form-control customer_id_s" name="customer_id" id="customer_id" onChange="getCategory(this.value)">
														<option value="<?php echo (int) $cp_login_id; ?>" selected
															data-phone="<?php echo htmlentities($cpPhone); ?>"
															data-email="<?php echo htmlentities($cp_exec_d['email']); ?>"
															data-address="<?php echo htmlentities($cp_exec_d['address']); ?>"
															data-state="<?php echo htmlentities($cp_exec_d['state']); ?>"
															data-cname="<?php echo htmlentities($cp_exec_d['cname']); ?>"
															data-gstin="<?php echo htmlentities($cp_exec_d['gst']); ?>"
															data-top_category_id="<?php echo htmlentities($cp_exec_d['top_category_id']); ?>"
															data-price-list="<?php echo htmlentities($cp_price_list_name); ?>"
															data-cutomer-type="<?php echo htmlentities($cp_type_label); ?>"
															data-gst-type="<?php echo htmlentities($cp_gst_type); ?>"
															data-c_id="<?php echo (int) $cp_login_id; ?>"
															data-shipping-add="<?php echo htmlentities($shipAddr); ?>"
															data-billing-add="<?php echo htmlentities($billAddr); ?>"
															data-customer_cash_discount="<?php echo htmlentities($cp_exec_d['cash_discount']); ?>"
															data-customer_additional_discount="<?php echo htmlentities($cp_exec_d['additional_discount']); ?>"
															data-booking_place="<?php echo htmlentities(isset($booking_place) ? $booking_place : $cp_exec_d['booking_place']); ?>"
															data-zip="<?php echo htmlentities($cp_exec_d['zip']); ?>"
															data-transporter_id="<?php echo htmlentities($cp_exec_d['transporter_id']); ?>"
															data-transport_thr="<?php echo htmlentities($cp_exec_d['transport_by_id']); ?>"
															data-shipping_address="<?php echo htmlentities($shipAddr); ?>"
															data-billing_address="<?php echo htmlentities($billAddr); ?>">
															<?php echo htmlentities($cp_exec_d['company_name'] . ' - ' . $cp_exec_d['cname']); ?>
														</option>
													</select>
													<p class="help-block text-muted">Pricing uses your Channel Partner account.</p>
												</div>
												<div class="form-group">
													<label>Select Customer<code>*</code></label>
													<select class="form-control" name="channel_partner_customer_id" id="channel_partner_customer_id">
														<option value="">-- Select Customer --</option>
														<?php
														if ($cp_customers_r) {
															while ($cp_cust = mysqli_fetch_assoc($cp_customers_r)) {
																$cpCustAddr = implode(', ', array_filter(array($cp_cust['city'], $cp_cust['state'], $cp_cust['pincode'], $cp_cust['country'])));
																$cpCustBook = trim($cp_cust['city'] . (!empty($cp_cust['state']) ? ', ' . $cp_cust['state'] : ''));
																$sel = ((int) $selected_cp_customer_id === (int) $cp_cust['id']) ? 'selected' : '';
																?>
																<option value="<?php echo (int) $cp_cust['id']; ?>" <?php echo $sel; ?>
																	data-company="<?php echo htmlentities($cp_cust['company_name']); ?>"
																	data-person="<?php echo htmlentities($cp_cust['person_name']); ?>"
																	data-mobile="<?php echo htmlentities($cp_cust['mobile_no']); ?>"
																	data-email="<?php echo htmlentities($cp_cust['email']); ?>"
																	data-gst="<?php echo htmlentities($cp_cust['gst']); ?>"
																	data-address="<?php echo htmlentities($cpCustAddr); ?>"
																	data-city="<?php echo htmlentities($cp_cust['city']); ?>"
																	data-state="<?php echo htmlentities($cp_cust['state']); ?>"
																	data-pincode="<?php echo htmlentities($cp_cust['pincode']); ?>"
																	data-booking_place="<?php echo htmlentities($cpCustBook); ?>">
																	<?php echo htmlentities($cp_cust['company_name'] . ' - ' . $cp_cust['person_name'] . ' (' . $cp_cust['mobile_no'] . ')'); ?>
																</option>
																<?php
															}
														}
														?>
													</select>
													<p class="help-block text-success">Select your customer, then choose product to place order.</p>
													<?php if (!$cp_customers_r) { ?>
														<p class="help-block text-danger">No customers found. Please add customer first from <a href="channel_partner_customer_manage.php">My Customers</a>.</p>
													<?php } ?>
												</div>
												<?php } else { ?>
												<div class="form-group">
													<label class="test">Select Company<code>*</code></label>
													<input type="hidden" name="type_of_company" value="<?=$type_of_company?>">
													<select class="form-control b-3" <?php echo $disabled; ?> id="type_of_company" name="type_of_company" autofocus <?php if($c_type != 100){ echo 'onChange="getCustomer()"'; } ?>>
														<option value="">Select Company</option>
														<?php
															$company_r = $db->rp_getData("company_master","*","isDelete=0","id DESC",0);
																if(mysqli_num_rows($company_r)>0){
																	while($company_d = mysqli_fetch_array($company_r)){
														?>
																	<option value="<?php echo $company_d['id']; ?>" <?=($type_of_company == $company_d['id'])?"selected":"";?>><?php echo $company_d['name']; ?></option>
														<?php
																	}
																}
														?>
													</select> 
													<p class="help-block"></p>
												</div>
												<div class="form-group">
													<?php
														$is_channel_partner_order = ($c_type == 'channel_partner');
														if ((!empty($c_type) || $_REQUEST['mode'] == "edit") && $c_type != 100 && !$is_channel_partner_order){
															$disabled_customer = "disabled";
														} else {
															$disabled_customer = "";
														}
													?>
													<label>Select Customer Type<code>*</code></label>
													<select class="form-control" onChange="getCustomer()" <?php echo $disabled_customer; ?> id="customer_type" name="customer_type">
														<option value="">Select Customer Type</option>
														<?php
															$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
															if ($cust_R) 
															{
																if (!empty($c_type) && $c_type != 'channel_partner' && $c_type != 100) {
															        $customer_type = $c_type;
															    }
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
												    <label><?= $is_channel_partner_order ? 'Select Channel Partner' : 'Select Customer'; ?><code>*</code></label>
												    <?php
														if ($_REQUEST['mode'] == "edit") {
													?>
														    <input type="hidden" name="edit_customer_id" id="edit_customer_id" value="<?= $customer_id; ?>" class="customer_id_s">
												    <?php
														}
													?>
												    <select  class="form-control customer_id_s" name="customer_id" placeholder="<?= $is_channel_partner_order ? 'Select Channel Partner' : 'Select Customer'; ?>" id="customer_id"   type="text" <?php echo $disabled; ?> onChange="getCategory(this.value)">
															<option value=""><?= $is_channel_partner_order ? 'Select Channel Partner' : 'Select Customer'; ?></option>
															<?php
														if ($_REQUEST['mode'] == "edit"){
																	$customers = $db->rp_getData("executive", "*", "isDelete=0 AND id='".$customer_id."'");
																	// $customers = $db->rp_getData("executive", "*", "isDelete=0");
																	if ($customers){
																while ($customer = mysqli_fetch_assoc($customers)){
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
																	} ?> data-phone="<?php echo $customer['phone'] ?>" data-email="<?php echo $customer['email'] ?>" data-address="<?php echo $customer['address'] ?>" data-state="<?php echo $customer['state'] ?>" data-cname="<?= $customer['cname'] ?>" data-customer_id="<?= $customer['top_cat_id'] ?>"  data-gstin="<?= $customer['gst'] ?>" data-price-list="<?= $price_list_name; ?>" data-cutomer-type="<?= $customer_type1; ?>" data-gst-type="<?= $gst_type?>" data-gst-type="<?= $gst_type ?>"><?php echo $customer['company_name']." - ".$customer['cname']; ?></option>
																	<?php
																}
																	}
														}
														else
														{
															if ($_REQUEST['mode'] == "add" && $_REQUEST['c_type'] == 7)
															{
																$customers = $db->rp_getData("executive", "*", "isDelete=0 AND isActive=1", "company_name ASC", 0);
																// $customers = $db->rp_getData("executive", "*", "isDelete=0");
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
																	} ?> data-phone="<?php echo $customer['phone'] ?>" data-email="<?php echo $customer['email'] ?>" data-address="<?php echo $customer['address'] ?>" data-state="<?php echo $customer['state'] ?>" data-cname="<?= $customer['cname'] ?>" data-customer_id="<?= $customer['top_cat_id'] ?>"  data-gstin="<?= $customer['gst'] ?>" data-price-list="<?= $price_list_name; ?>" data-cutomer-type="<?= $customer_type1; ?>" data-gst-type="<?= $gst_type?>" data-gst-type="<?= $gst_type ?>" data-shipping_address="<?php echo $customer['shipping_address'] ?>" data-billing_address="<?php echo $customer['billing_address'] ?>"><?php echo $customer['company_name']." - ".$customer['cname']; ?></option>
																	<?php
																				}
																}
															}
														}
														?>
														</select>
												    <p class="help-block"></p>
												</div>
												<?php } // end non-CP order customer selectors ?>
												<!-- <div class="form-group">
												    <label>Select Customer<code>*</code></label>
												    <select  class="form-control customer_id_select2" name="customer_id_select2" placeholder="Select Customer" id="customer_id_select2"   type="text"  >
														<option value="">Select Customer</option>
														<?php
															$customers_select2 = $db->rp_getData("executive", "*", "isDelete=0 AND isActive=1", "", 0);				
															while ($customer_select2 = mysqli_fetch_assoc($customers_select2)){
														?>
																<option value="<?php echo $customer_select2['id']; ?>">
																	<?= $customer_select2['company_name']." - ".$customer_select2['cname']; ?>
																</option>
														<?php
															}				
														?>
													</select>
												    <p class="help-block"></p>
												</div> -->
												<!-- CUSTOMER EDIT BUTTON -->
												<?php
													if($_REQUEST['mode']=="edit" && $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0)
													{
												?>
													<div class="form-group" style="text-align: right;margin-right: 64px;">
											    <?php
													$Check_Dispatch = $db->rp_getTotalRecord("dispatch_detail","order_id='".$_REQUEST['id']."' AND isDelete=0");
													if($Check_Dispatch==0)
													{
												?>
													    <a class="btn btn-primary" href='#CustomerChangeModel' data-title="Customer Details"
													        data-customer_id="<?= $customer_id; ?>" data-mode="order" data-quotation_id="<?= $_REQUEST['id']; ?>"
													        data-toggle='modal'><i class="fa fa-pencil"></i> Change Customer</a>
											    <?php
													}
												?>
													    <a class="btn btn-success" href='#CustomerEditModel' data-title="Customer Details"
													        data-customer_id="<?= $customer_id; ?>" data-mode="order" data-quotation_id="<?= $_REQUEST['id']; ?>"
													        data-toggle='modal'><i class="fa fa-edit"></i> Edit Customer</a>
													</div>
												<?php
													}
												?>
												<div class="form-group">
                                                	<p><b>Total Order Count : </b><span id="tot_order_cnt"></span></p>
                                                	<p><b>Monthly Order Count : </b><span id="monthly_order_cnt"></span></p>
                                                	<p></p>
                                                </div>

												<!-- CUSTOMER EDIT BUTTON -->
												<div class="form-group" >
													<label class="test">Select Terms & Condition <code>*</code></label>
													<select class="form-control" id="terms_condition_id" name="terms_condition_id" onchange="getTermsDescr()">
														<option value="">Select Terms & Condition</option>
														<?php 
														$terms_r = $db->rp_getData("terms_condition","*","isDelete=0");
														if ($terms_r) {
															while ($terms_d = mysqli_fetch_assoc($terms_r)) {
														?>
														<option <?= ($terms_condition_id == $terms_d['id']) ? "selected" : ""; ?> value="<?= $terms_d['id']; ?>"><?= $terms_d['name']; ?></option>
														<?php
															}
														}
														?>
													</select>
													<p class="help-block"></p>
												</div>
												<div class="form-group">
												    <div class="row static-info phone">
												        <div class="col-md-5 name"> Name : </div>
												        <div class="col-md-7 value" name="name" id="name">
												            <?php echo $customer_name; ?>
												        </div>
												    </div>
												    <div class="row static-info phone">
												        <div class="col-md-5 name"> Phone : </div>
												        <div class="col-md-7 value" name="name_phone" id="name_phone">
												            <?php echo $contact_number; ?>
												        </div>
												    </div>
												    <div class="row static-info address">
												        <div class="col-md-5 name"> Address : </div>
												        <div class="col-md-7 value" name="name_address" id="name_address">
												            <?php echo $address; ?>
												        </div>
												    </div>
												    <div class="row static-info address">
												        <div class="col-md-5 name"> State : </div>
												        <div class="col-md-7 value" name="name_state" id="name_state">
												            <?php echo $customer_state; ?>
												        </div>
												    </div>
												    <div class="row static-info address">
												        <div class="col-md-5 name"> GSTIN : <code>*</code> </div>
												        <!-- <div class="col-md-7 value" name="name_gstin" id="name_gstin"><?php echo $customer_gstin; ?></div> -->
												        <div class="col-md-7">
												            <input class="form-control" type="text" name="name_gstin" id="name_gstin" value="<?php echo $name_gstin ?>" maxlength="15">
												            <p class="help-block"></p>
												        </div>
												    </div>
												    <div class="row static-info address">
												        <div class="col-md-5 name"> Pricelist : </div>
												        <div class="col-md-7 value" name="name_pricelist" id="name_pricelist">
												            <?php echo $customer_pricelist; ?>
												        </div>
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
											        <label>Shipping Address</label>

											        <a class="btn-sm btn-success" id='customer_id_c' href='#CustomerChangeShippingAddressModel'
											            data-title="Customer Details" data-customer_id="<?= $dealer_id; ?>" data-mode="quotation_change_shipping"
											            data-quotation_id="<?= $_REQUEST['id']; ?>" data-toggle='modal'><i class="fa fa-edit"></i> Change Shipping
											        </a>

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
											            <label>Transport By </label>
											            <!-- update code - sagar  -->
											            <input type="hidden" id="transport_name_selected_id" value="<?=$transport_name ?>">
											            <!-- update code - sagar  -->
											            <select class="form-control" name="transport_through" id="transport_through" onchange="getTransportname(this.value);">
															<option value="">Select Transport By</option>
															<?php
																$transport_by_r = $db->rp_getData("transport_by","*","isDelete=0","name ASC");
																if(mysqli_num_rows($transport_by_r)>0){
																	while($transport_by_d = mysqli_fetch_array($transport_by_r)){
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
											    <?php 
													if($_REQUEST['mode'] == "add"){
												?>
													    <!-- <div class="col-md-6" >
														<div class="form-group">
															<label>Apply Scheme</label>
															<select class="form-control" name="apply_scheme" id="apply_scheme">
																<option value="1" <?=($apply_scheme==1)?"selected":""?>>Yes</option>
																<option value="2" <?=($apply_scheme==2)?"selected":""?>>No</option>
															</select>
															<p class="help-block"></p>
														</div>
													</div> -->

											    <?php
													}
													else
													{
												?>
													    <input type="hidden" name="apply_scheme" id="apply_scheme" value="<?=$apply_scheme?>">
												<?php
													}	
												?>
												<div class="col-md-6">
											        <div class="form-group">
											            <label>Booking Place<code>*</code></label>
											            <!-- <input style="resize: vertical;" type="text" class="form-control" name="booking_place" id="booking_place" value="<?php echo $booking_place ?>">
											            <p class="help-block"></p> -->
											            <textarea class="form-control" id="booking_place" name="booking_place" style="resize: vertical;"><?= $booking_place; ?></textarea>
												          <p class="help-block"></p>
											        </div>
											    </div>
											    <div class="col-md-6">
											        <div class="form-group">
											            <label>Booking Pincode<code>*</code></label>
											            <input type="text" class="form-control" name="booking_pincode" id="booking_pincode" value="<?php echo $booking_pincode ?>">
											            <p class="help-block" max></p>
											        </div>
											    </div>
											     <div class="col-md-6">
											        <div class="form-group">
											            <label>Max. Dispatch Date</label>
											            <input type="text"  class="form-control" name="max_dispatch_date" id="max_dispatch_date" value="<?= $max_dispatch_date != "01-01-1970" ? $max_dispatch_date : "" ?>" />
											            <p class="help-block"></p>
											        </div>
											    </div>
											    <div class="col-md-12">
											        <div class="form-group">
											            <label>Sales Executive<code>*</code></label>
											            <select class="form-control" onchange="getRegards(this)" name="sales_executive" id="sales_executive">
															<option value="">Select Sales Executive</option>
															<?php
															if ($sales_executive_id == "" || $sales_executive_id == 0 || $sales_executive_id == NULL || empty($sales_executive_id)) 
															{
																if (!empty($is_cp_self_order) && !empty($cp_exec_d['seid'])) {
																	$sales_executive_id = $cp_exec_d['seid'];
																} else {
																	$sales_executive_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
																}
															}
															
															$sales_R = $db->rp_getData("sales_executive","id,name,phone","isDelete=0 AND isActive=1");
															if ($sales_R) {
																while($sales_d = mysqli_fetch_assoc($sales_R) )
																{
																	?>
																	<option data-regrads="<?php echo $sales_d['name']." <br> ".$sales_d['phone'] ?>" value="<?php echo $sales_d['id'] ?>" <?php echo $sales_d['id'] == $sales_executive_id ? "selected" : ''; ?> ><?php echo $sales_d['name']." - ".$sales_d['phone'] ?></option>
																	<?php
																}
															}
															?>
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
												<div class="col-md-3">
													<div class="form-group">
														<!-- <?php
														if ($_REQUEST['mode'] == "edit") {
														$disabled = "disabled";
														} else {
														$disabled = "";
														}
														?> -->
														<label>Category  <code>*</code></label>
														<!-- <input type="text" name="category_id1" id="category_id1" > -->
														<select class="form-control" name="category_id" id="category_id">
															<option value="">select Category </option>
															<?php

															// if($category_id!="")
															// {
															// $cat_r1=$db->rp_getData("executive","top_category_id","isDelete=0 AND isActive=1 AND id='".$category_id."'","",1);
															// }

															// $cat_r=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",0);
															// while($cat_d=mysqli_fetch_assoc($cat_r))
															// {
															?><!-- 
															<option  value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option> -->
															<?php
															//}
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
												        <label>Select Brand <code>*</code></label>
												        <select class="form-control" name="order_item_brand" id="order_item_brand">
															<option value="">Select Brand</option>
															<?php 
															$orderItemBrand_R = $db->rp_getData("order_item_brand_master","id,name","isDelete=0 AND isActive=1");
															if ($orderItemBrand_R) {
																while($orderItemBrand_D = mysqli_fetch_assoc($orderItemBrand_R)) {
																	?>
																	<option data-order_item_brand_name="<?= $orderItemBrand_D['name'] ?>" value="<?= $orderItemBrand_D['id'] ?>"><?= $orderItemBrand_D['name'] ?></option>
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
												        <label>Select Order Unit <code>*</code></label>
												        <select class="form-control" name="bag_box_id" id="bag_box_id">
															<option value="">Select Order Unit</option>
															<!-- <option value="-1">Box</option>
															<option value="-2">Strip</option>
															<option value="-3">Pallet</option>
															<option value="1">Carte</option>
															<option value="2">Big Box</option>
															<option value="100">Qty</option>  -->
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
												<div class="col-md-1 hidden" >
													<div class="form-group" style="margin-top:21px;">
														<label><b>Box <br>Qty : </b> </label>
														<span class='inner_size'></span>
														<br/><label><b>Caret <br>Qty : </b></label>
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
										            <th style="padding: 1px!important;" class="text-center" width="100px;">Brand 
										            	<?php if ($_REQUEST['mode'] == 'edit'):?> 
										            		<input type="checkbox" id="changeallbrand" />
										            	<?php endif; ?>
										            </th>
										            <th style="padding: 1px!important;" class="text-center" width="50px;">Unit</th>
										            <th style="padding: 1px!important;" class="text-center">Order Qty</th>
										            <th hidden style="padding: 1px!important;" class="text-center">Box Qty<br/>(NOS)</th>
										            <th hidden style="padding: 1px!important;" class="text-center">Caret Qty<br/>(in NOS)</th>
										            <th hidden style="padding: 1px!important;" class="text-center">Loose
										                Qty<br/><span class="f-10">(in NOS)</span></th>

										            <th hidden style="padding: 1px!important;" class="text-center">Stock</th>
										            <th style="padding: 1px!important;" class="" width="100px;">Product Description</th>
										            <th style="padding: 1px!important;" class="text-center" width="50px;">HSN Code</th>
										            <!-- <th  class="text-center">Avail Stock</th>
													<th  class="text-center">Balance</th> -->
													            <!-- <th  class="text-center">Bag<br/><span class="f-10">(inner in NOS)</span> </th>
													<th  class="text-center">Box<br/><span class="f-10">(outer in NOS)</span></th> -->
										            <th style="padding: 1px!important;" class="text-center">Qty<br /><span class="f-10">NOS</span></th>
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
										        $price_list_id=$db->rp_getValue("executive","price_list_id","id='".$customer_id."'",0);
												$is_premium=$db->rp_getValue("price_list","is_premium","id='".$price_list_id."'",0);
												if (!empty($item_info)) {
													$total_amount = 0;
													$order_unit_arr = array("-1"=>"Box","-2"=>"Strip","-3"=>"Pallet","1"=>"Caret","2"=>"Big Box","100"=>"Nos");
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

														$top_cat_id = $db->rp_getValue("product","tcid","id='". $i['product_id']."'");

														// $unit_id = $db->rp_getValue("product","unit_id","id='". $i['product_id']."'");
														// $unit_name = $db->rp_getValue("unit","name","id='". $unit_id."'");
														$unit_name = $order_unit_arr[$i['item_order_unit']];
														if($i['is_including'] == 1)
														{
														  $get_pro_price="Including Gst : ".$db->rp_getValue("product_weight_price","price","isDelete=0 AND product_id='".$i['product_id']."' AND weight_id='".$i['weight_id']."'");
														}
														else
														{
														  $get_pro_price="";
														}

												?>
										        <tr>
										            <!-- <td><?php echo ++$count; ?><input type='hidden' name='count[]' value="<?php echo $count; ?>" class='count'></td> -->
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
										            <td style="width: 300px;text-align: center;padding: 1px!important;">
										                <input type='hidden' name='product_id[]' class='product_id' value="<?php echo $i['product_id']; ?>">
										                <input class="pro_id" type='hidden' name='pro_id[]' value="<?php echo $i['product_id'] . "" . $i['weight_id']; ?>">
										                <input type='hidden' style="text-align:right" name='subtotal[]' value="">
										                <input type='hidden' style="text-align:right" name='total[]' value="">
										                <input type='hidden' style="text-align:right" name='item_name[]'>
										                <input type='hidden' name='pro_name[]' value="<?php echo $i['product_name']; ?>" id='pro_name'>
										                <input type='hidden' name='weight_id[]' value="<?php echo $i['weight_id']; ?>" id='weight_id'>
										                <?php echo $i['product_name']; ?>
										            </td>

										            <td class='text-center' style="width: 100px;text-align: center;padding: 1px!important;"><a href='#brandChange' data-item_idfbrand="<?php echo $i['item_idfbrand'] ?>" data-toggle='modal'><input type='hidden' class="order_item_brand_id" name='order_item_brand_id[]' value="<?php echo $i['order_item_brand_id']; ?>" /><?php echo $db->rp_getValue("order_item_brand_master","name","isDelete=0 AND isActive=1 AND id='".$i['order_item_brand_id']."'") ?></a></td>

										            <td style="padding: 1px!important">
										                <?= $unit_name ?><input type='hidden' name='item_order_unit[]' class='item_order_unit' value='<?= $i['item_order_unit'] ?>'>
										            </td>
											            <td style='padding: 1px!important;' ; class='text-center'><input type='text' name='order_qty[]' class='form-control positive  order_qty' style='text-align:right;width:80px;' value='<?= $i['order_qty'] ?>' onChange='recalculateRow(this)'  id='order_qty'/>
										            </td>
										            <td hidden style="text-align:right;padding: 1px!important">
										                <input class="inner_size" type='hidden' name='inner_size' value="<?php echo $i['inner_size']?>"> <input readonly name='bag[]' class='form-control bag' style="text-align:right;width:70px;"  type='text' value="<?php echo $i['bag'];?>">
										            </td>
										            <td hidden style='text-align:right;padding: 1px!important'>
										                <input type='hidden' name='outer_size' class='outer_size' value="<?php echo $i['outer_size']; ?>"><input readonly type='text' class='form-control box_qty' style='text-align:right;width:70px;' name='box_qty[]' class='box_qty positive' value="<?php echo $i['box']; ?>">
										            </td>
										            <td hidden style='text-align:right;padding: 1px!important'>
										                <input type='hidden' name='loose_qty' class='loose_qty' value="<?php echo $i['loose']; ?>"><input readonly type='text' class='form-control loose' style='text-align:right;width:70px;' name='loose[]' class='loose positive' value="<?php echo $i['loose']; ?>">
										            </td>
										            <td hidden style="padding: 1px!important" class='text-center'>
										                <?php echo $i['stock']; ?>
										            </td>
										            <td style="padding: 1px!important;">
										                <textarea rows="2" cols="10" id="pro_description" class="pro_desc" name="pro_description[]" style="margin: 0!important;"><?=$i['pro_description']?></textarea>
										            </td>
										            <td style="padding: 1px!important" class='text-center'>
										                <?php echo $i['hsn_code']; ?>
										            </td>
										            <!-- <td style="text-align:right">
													<input class="inner_size" type='hidden' name='inner_size[]' value="<?php echo $i['inner_size']; ?>">
													<input readonly name='bag[]' class='form-control bag positive' style="text-align:right;width:100px;"  type='text' value="<?php echo $i['bag']; ?>">
													</td>
													<td style='text-align:right'><input type='hidden' name='outer_size' class='outer_size' value="<?php echo $i['outer_size']; ?>"><input readonly type='text' class='form-control box_qty' style='text-align:right;width:100px;' name='box_qty[]' class='box_qty positive' value="<?php echo $i['box']; ?>"></td> -->
										            <td style="text-align:right;padding: 1px!important;">
										                <input readonly type='text' style="text-align:right;width:80px;" onChange='recalculateRow(this)' class='qty form-control' name='qty[]' value="<?php echo $i['qty']; ?>" class="positive">
										            </td>
													<td style="text-align:right;padding: 1px!important;">
														<input type='hidden' value="<?php echo $i['original_price']; ?>" class='original_price_hidden'/>
													    <input name='original_price[]' class='original_price form-control' style="text-align:right;width:80px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['original_price']; ?>">
													    <?php $i['original_price']; ?>
													    <input type="hidden" name="is_including[]" value="<?=  $i['is_including']?>">
													    <span>
														  <?=$get_pro_price?>
														</span>
														<span>
															<input type='hidden' value="<?= ($is_premium)?$is_premium:0; ?>" class='is_premium'/>
														</span>
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
										            <td style="padding: 1px!important;">
										                <input style="text-align:right;width:80px;" type="text" name='discount[]' onChange='recalculateRow(this,2)' class="discount form-control" value="<?php echo $i['discount_per']; ?>">
										                <?php $i['discount_per']; ?>
										            </td>
										            <td style="text-align:right;padding: 1px!important;">
										                <input name='price[]' readonly class='price form-control' style="text-align:right;width:80px;" onChange='recalculateRow(this)' type='text' value="<?php echo $i['product_price']; ?>">
										                <input class='old_price form-control' style="text-align:right;" type='hidden' value="<?php echo $i['product_price']; ?>">
										                <?php $i['product_price']; ?>
										            </td>
										            <td style="text-align:right;padding: 1px!important;">
										                <input type='text' style="text-align:right;width: 100px;" disabled class='total form-control' disabled onChange='recalculateRow(this)' name='subtotal[]' value="<?php echo $i['product_total']; ?>">
										            </td>
										            <td class='hidden' style="padding: 1px!important;">
										                <input style="width: 150px;" readonly type='text' class='cd_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='cd_discount[]' value="<?php echo $i['cd_amount']; ?>">
										            </td>
										            <td class='hidden' style="padding: 1px!important;">
										            	<input style="width: 150px;" readonly type='text' class='ad_discount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='ad_discount[]' value="<?php echo $i['ad_amount']; ?>">
										            </td>
										            <td class='hidden' style='padding: 1px!important;' ;>
										            	<input readonly type='text' class='other_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='other_charge[]' value=<?php echo $i['other_charge']; ?>>
										            </td>
										            <td class='hidden' style='padding: 1px!important;' ;>
										            	<input readonly type='text' class='fright_charge form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='fright_charge[]' value=<?php echo $i['fright_charge']; ?>>
										            </td>
										            <td style="padding: 1px!important;">
										                <input readonly type='text' class='taxable_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='taxable_amount[]' value="<?php echo $i['taxable_amount'] ?>"><input class='new_taxable' type='hidden' value='<?php echo $i['taxable_amount'] ?>' id='taxable_amount'>
										            </td>
										            <td style="padding: 1px!important;">
										            	<input readonly  type='text' class='gst_amount form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='gst_amount_item[]' value="<?php echo $i['gst_amount_item'] ?>">
										            </td>
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
										            <td style="text-align: center;padding:1px;"><input type='hidden' style='text-align:right;width: 80px;' id='gst_tax'  class='gst_tax form-control' value='<?php echo $gst ?>'>
										                <?php echo $gst ?>%
										            </td>
										            <td style="padding: 1px!important;"><input readonly type='text' class='sub_total form-control' onChange='recalculateRow(this)' style='text-align:right;width:150px;' name='sub_total[]' value="<?php echo $i['sub_total'] ?>">
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
										            <td colspan="7" align="right">Total</td>
										            <td style="text-align: right;">
										                <input type="text" style="text-align: right;" id="sum_qty" class="form-control" disabled value="<?php echo $qty_total; ?>">
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
										            <td class="hidden" colspan="2"></td>
										            <td style="text-align:right">
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
										            <td colspan="12" align="right">Cash Discount (%)</td>
										            <td style="text-align: right;">
										                <input type="text" style="text-align: right;" id="cash_discount" name="cash_discount"  onChange='recalculateRow(this)' class="form-control" value="<?php echo ($cash_discount>0)?$cash_discount:""; ?>">
										            </td>
										            <td style="text-align: right;">
										                <input type="text" style="text-align: right;" id="cash_discount_amount" onChange='recalculateRow(this)' name="cash_discount_amount" class="form-control cd_calculate" value="<?php echo $cash_discount_amount; ?>">
										            </td>
										        </tr>
										        <tr>
										            <td colspan="12" align="right">Additional Discount (%)</td>
										            <td style="text-align: right;">
										                <input type="text" style="text-align: right;" id="additional_discount" name="additional_discount"  onChange='recalculateRow(this)' class="form-control" value="<?php echo ($additional_discount>0)?$additional_discount:""; ?>">
										            </td>
										            <td style="text-align: right;">
										                <input type="text" style="text-align: right;" id="addtional_discount_amount" onChange='recalculateRow(this)' name="addtional_discount_amount" class="form-control ad_calculate" value="<?php echo $additional_discount_amount; ?>">
										            </td>
										        </tr>
										        <tr>
										            <td colspan="12" align="right" hidden>Packing & Forwarding Charge</td>
										            <td style="text-align: right;" hidden>
										                <input type="text" style="text-align: right;" id="packing_charge" name="packing_charge"  onChange='recalculateRow(this)' class="form-control packing_calculate" value="<?php echo $packing_charge; ?>">
										            </td>
										        </tr>
										        <tr>
										            <td colspan="12" align="right" hidden>Transport</td>
										            <td style="text-align:right" hidden>
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
										            <td colspan="13" align="right">Total Taxable</td>
										            <td style="text-align: right;">
										                <input type="text" disabled style="text-align: right;" id="total1"  class="form-control" value="<?php echo $db->rp_number_format($total); ?>">
										            </td>
										        </tr>
										        <tr>
										            <?php
														// echo $igst_amount;
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

										            <td colspan='12' align="right"></td>
										            <td align="right">GST On Off Switch
										                <!-- <?= $igst_amount; ?> -->
										                <input  type="checkbox"
															<?php
																if($_REQUEST['mode']=="add")
																{
																echo "checked";
																$gst_apply_flag = 1;
																}
																else if($igst_amount==0)
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
										                <!-- <?= $gst_apply_flag ?> -->
										                <input type="hidden" name="gst_apply" id="gst_apply" value="<?= $gst_apply?>">
										                <input type="hidden" name="gst_apply_flag" id="gst_apply_flag" value="<?= $gst_apply_flag?>">
										            </td>
										            <td style="text-align:right">
										                <input type="hidden" class="item_gst_amount" value="">
										                <input type='text' style="text-align:right;width:150px;" id='gst_amount' align="right" class="form-control" disabled value="<?= $db->rp_number_format($gst_amount) ?>" name='gst_amount'>
										            </td>
											    </tr>
										        <tr>
										            <td colspan='13' align="right">TCS On Off Switch
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
										            <td colspan='13' align="right">Round Off</td>
										            <td style="text-align:right">
										                <input id="round_off" name="round_off" type='text' style="text-align:right;width:150px;" class="form-control" readonly value="<?php echo $db->rp_number_format($f1); ?>">
										            </td>
										            <td></td>
										        </tr>
										        <tr>
										            <td colspan='13' align="right"> Grand Total </td>
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
													<textarea class="form-control" id="terms_comdition" rows="5" name="terms_comdition" style="resize: vertical;">
														<?php 
															/*if($_REQUEST['mode']=="add")
															{ 
																echo $db->custom_html_entity_decode2(DEFAULT_TERMS);
															} 
															else 
															{ */
																echo str_replace('rn','',$terms_comdition);
															/*}*/
														?>		
													</textarea>
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
												<label>Note</label>
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
		<!-- Brand Item Change Modal -->
		<div id="brandChange" class="modal fade" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Brand Change </div>
							<div class="tools">

								<a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

								<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
							</div>
						</div>
						<div class="portlet-body portlet-empty" style="">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Brand Item Change Modal -->
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

			$('#brandChange').on('show.bs.modal', function (event) {
				var changeallbrand = ($("#changeallbrand").prop('checked')) ? 'all' : '';
		  		var button = $(event.relatedTarget) // Button that triggered the modal
		  		var order_idfbrand= "<?php echo $_REQUEST['id'] ?>" ;
		  		var item_idfbrand=button.data("item_idfbrand");
				$("#requesting_ajax").attr("data-url","brand_item_change_modal_ajax.php?mode=show&id="+item_idfbrand+"&order_idfbrand="+order_idfbrand+"&checkall="+ changeallbrand+"&reference_table=order_product_item");
				$("#requesting_ajax").click();
			})

			$("#changeallbrand").on('change',function(){
				($("#changeallbrand").prop('checked')) ? $("#brandChange").modal("show") : false;
			});

			$(".discount_amount").numeric();
			$(".discount").numeric();
			$(".original_price").numeric();
			$(".price").numeric();
			$("#booking_pincode").numeric({
			  maxlanth: 6
			});
			$("#booking_pincode").attr("maxlength", 6);
		</script>
		<script type="text/javascript">
		   $(document).ready(function() {
		   		// $("#customer_id_select2").select2(
		   		// 	$.ajax({
				// 		type: "post",
				// 		url: "ajax_get_category_from_customer.php",
				// 		data: "customer_id=" + cus_id,
				// 		beforeSend: function() {
				// 			// $(".transCover").fadeIn(800);
				// 			$("#loading-modal").modal('show');
				// 			// $('.preloader').fadeIn('slow');
				// 		},
				// 		success: function(result) {
				// 			$('#customer_id_select2').html(result);
				// 		}
				// 	})
		   		// );
		       var admintype = '<?=$_SESSION[SITE_SESS.'_ADMIN_TYPE'] ?>';
		       if(admintype=="0")
		       {
		            CKEDITOR.config.readOnly = true;
		       }
		       var admin = '<?=$_SESSION[SITE_SESS.'_ADMIN_TYPE'] ?>';
		       if(admin!="0")
		       {
		            CKEDITOR.config.readOnly = true;
		       }
		   });
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


			if(mode == 'edit')
			{ 
				getCustomer(<?= $_REQUEST['type_of_executive'] ?>,<?= $_REQUEST['customer_id'] ?>);
			}
			});
		</script>
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
			$('#max_dispatch_date').datepicker({
				datepicker: true,
				autoclose: true,
				dateFormat: 'dd-mm-yy',
				minDate: 0
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
				var booking_place = $("#customer_id").find('option:selected').data("booking_place");
				var booking_pincode = $("#customer_id").find('option:selected').data("zip");
				// alert(booking_place);
				var customer_cash_discount = $("#customer_id").find('option:selected').data("customer_cash_discount");
				var customer_additional_discount = $("#customer_id").find('option:selected').data("customer_additional_discount");

				var cname = $("#customer_id").find('option:selected').data("cname");
				var shipping_add = $("#customer_id").find('option:selected').data("shipping-add");
				var billing_add = $("#customer_id").find('option:selected').data("billing-add");

				var shipping_addreses = $("#customer_id").find('option:selected').data("shipping_address");
				var billing_address = $("#customer_id").find('option:selected').data("billing_address");

				var c_id = $("#customer_id").find('option:selected').data("c_id");
				var cat_id = $("#customer_id").find('option:selected').data("top_category_id");
				var cus_id = $("#customer_id").find('option:selected').data("c_id");

				var transport_through = $("#customer_id").find('option:selected').data("transport_thr");
				var transport_name = $("#customer_id").find('option:selected').data("transporter_id");
				// alert(transport_name)

				$('#customer_id_c').attr("data-customer_id",c_id); //setter

				var c1 =  $('.customer_id_s').val();
				$('.cat_id_s').html(cat_id);

				$("#shipping_address").html(shipping_add);
				$("#billing_address").html(billing_add);

				$("#shipping_address").html(shipping_addreses);
				$("#billing_address").html(billing_address); 

				/*$('#transport_through').select2('destroy');
				$('#transport_through').val(transport_through);
				$('#transport_through').select2();

				getTransportname(transport_through,transport_name);*/

				//var category_id = $("#category_id").find('option:selected').data("category_id");

				// alert(category_id);
				var add_mode="<?=$_REQUEST['mode']?>";
				// alert(booking_place);
				// alert(booking_pincode);
				if(mode == "add")
				{
					$("#cash_discount").val(customer_cash_discount);
					$("#additional_discount").val(customer_additional_discount);
					$("#booking_place").val(booking_place);
					$("#booking_pincode").val(booking_pincode);
					$("#transport_through").select2("val",transport_through);
					$("#transport_name").select2("val",transport_name);
	                 getTransportname(transport_name,transport_through);
					//$("#category_id").val(category_id);

	                var cusid = $("#customer_id").val();
		            getCustomerCount(cusid);
				}

				//$("#place_of_supply").val(place_of_supply);
				$("#name_value").html(customer_name);
				$("#name_phone").html(phone);
				$("#name_address").html(address);
				$("#name_state").html(state);
				/*$("#name_gstin").html(gstin);*/
				$("#name_gstin").val(gstin);
				$("#name").html(cname);
				$("#name_pricelist").html(pricelist);

				 //$("#category_id1").val(cus_id);
				$(".gst_type").html(gst_type);

				// $("#category_id").select2("destroy");
				// $('#category_id').val(cus_id);
				// $("#category_id").select2();


				//$("#category_id").select2("val","");
				// $("#place_of_supply").val(place_of_supply.toUpperCase());
			})

			function getCustomer(customer_type,id) 
			{

			   	var cus_id = '<?= $customer_id ?>';
				var form_c_type = '<?= $c_type ?>';
				customer_type = $("#customer_type").val();
				var type_of_company = $("#type_of_company").val();
				if (customer_type == "" || customer_type == null) {
					$('#customer_id').select2('destroy');
					$('#customer_id').html('<option value=""><?= ($c_type == 'channel_partner') ? 'Select Channel Partner' : 'Select Customer'; ?></option>');
					$('#customer_id').select2();
					return;
				}
				// alert(type_of_company);
				// alert(ctype);
				// $('#customer_id').select2("val", "");
				$.ajax({
					type: "post",
					url: "ajax_get_customer.php",
					data: {
						customer_type: customer_type,
						companytype: type_of_company,
						selected_value: id,
						channel_partner_order: (form_c_type == 'channel_partner') ? 1 : 0
					},
					beforeSend: function() {
					// $(".transCover").fadeIn(800);
					$("#loading-modal").modal('show');
					// $('.preloader').fadeIn('slow');
					},
					success: function(result) 
					{
						setTimeout(function() {
						$('#customer_id').select2('destroy');
						$('#customer_id').html(result);
						$('#customer_id').select2();
						$("#customer_id").trigger('change');
						$("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');

						});
						// getTermescondition();
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


			function getCategory(cus_id) {
				$.ajax({
					type: "post",
					url: "ajax_get_category_from_customer.php",
					data: "customer_id=" + cus_id,
					beforeSend: function() {
						// $(".transCover").fadeIn(800);
						$("#loading-modal").modal('show');
						// $('.preloader').fadeIn('slow');
					},
					success: function(result) {
						setTimeout(function() {
							$('#category_id').html(result);
							$("#loading-modal").modal('hide');
							$('.preloader').fadeOut('slow');
						});
					}
				})
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
					 
					var pro_id = $("#product_id").find('option:selected').data('pro_id');
					$.ajax({
						type: "post",
						url: "ajax_get_order_unit_from_product.php",
						data: "pro_id=" + pro_id, 
						success: function(result) {
							$('#bag_box_id').html(result);
							
							<?php 
							if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3)
							{
							?>
							var item_order_unit = $("#product_id").find('option:selected').data('item_order_unit');
							// alert(item_order_unit);
							$("#bag_box_id").select2("destroy");
							$("#bag_box_id").val(item_order_unit);
							//$("#bag_box_id").attr("disabled","");
							$("#bag_box_id").select2();
							<?php
							}   
							?>
				 		}
					}) 

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
				var original_price_hidden = $(row).find("td").find("input.original_price_hidden").val();
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
				var item_order_unit = $(row).find("td").find("input.item_order_unit").val();
				var order_qty = $(row).find("td").find("input.order_qty").val();
				var changed_before_price = $(row).find("td").find("input.price").val();
				var is_premium = $(row).find("td").find("input.is_premium").val();
			  	// alert(is_premium);
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
				// unit logic by shivani //04-03-2023
				var bag=0;
				var box=0;
				var loose=0;  
				if(item_order_unit<0 && item_order_unit!=100)
				{
					bag = order_qty;  
					qty= order_qty*inner_size;
					box=0;
				}
				else if(item_order_unit>0 && item_order_unit!=100)
				{
					box = order_qty; 
					qty= order_qty*outer_size;
					bag=0;
				}	
				else if(item_order_unit==100)
				{
					qty = order_qty;	
					bag=0;
					box=0; 
				}
				else
				{
					qty=0;
					bag=0;
					box=0;
				}
				// unit logic by shivani //04-03-2023

					/*if(bag_box_id==""){
					var bag_box_id = 3;	
					}
					else{
					var bag_box_id = bag_box_id;
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
					var total_bag = bag*inner_size;
					var total_box = box*outer_size;
					var totalsum = total_bag+total_box;
					var loose = Math.floor(qty-totalsum);*/
			 
			 
				$(row).find("td").find("input.box_qty").val(box);
				$(row).find("td").find("input.bag").val(bag);
				$(row).find("td").find("input.loose").val(loose);

				// alert("original_price="+original_price);
				// alert("price="+price);

				if ((parseFloat(price) > parseFloat(original_price))) { 
					// toastr.error("Rate should not be higher than Available Price");
					$(row).find("td").find("input.price").val(old_price);
					price = $(row).find("td").find("input.price").val();
				}

				if (parseFloat(discount_amount_new) > (parseFloat(original_price) * 50 / 100)) {
					toastr.error("You cant add Discount More Than 50%");
					$(row).find("td").find("input.discount_amount").val(0);
					discount_amount_new=0;
				} 
				if(parseFloat(discount) > 50)
				{
					toastr.error("You cant add Discount More Than 50%");
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
				// alert("price1="+price1);
				// alert("changed_before_price="+changed_before_price);

				/*var discount_amount = (original_price * discount) / 100;
				price = (original_price - discount_amount);
				price1 = (original_price - discount_amount);*/
				price1 = price1.toFixed(2);
				price = price + ((price*item_gst)/100);
				price = price.toFixed(2);
				$(row).find("td").find("input.price").val(price1);
				var total = qty1 * price;
				var total1 = price1 * qty;
				// price=format(price,3);
				// total=format(total,3);
				total = total.toFixed(2);
				total1 = total1.toFixed(2);
				var total_balance = stock - qty;
				var taxable_amount = parseFloat(total1) - parseFloat(cd_discount) - parseFloat(ad_discount) + parseFloat(other_charge) + parseFloat(fright_charge);
				var item_gst_amount1 = ((taxable_amount*item_gst)/100);
				item_gst_amount1 = item_gst_amount1.toFixed(2);
				var taxable_amount = taxable_amount.toFixed(2);
				var sub_total = (parseFloat(total1) + parseFloat(item_gst_amount1)).toFixed(2);
				$(row).find("td").find("input.qty").val(qty);
				$(row).find("td").find("input.total").val(total1);
				$(row).find("td").find("input.final_balance").val(total_balance);
				$(row).find("td").find("input.gst_amount").val(item_gst_amount1);
				$(row).find("td").find("input.final_balance").val(total_balance);
				$(row).find("td").find("input.sub_total").val(sub_total);
				$(row).find("td").find("input.taxable_amount").val(taxable_amount);
				$(row).find("td").find("input.new_taxable").val(taxable_amount);
				// recalculateFinalValues();
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
					if (event.keyCode == 46 || event.keyCode == 8) {
						// let it happen, don't do anything
					}
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
					var order_item_brand = $("#order_item_brand").val();
					var order_item_brand_name = $("#order_item_brand").find("option:selected").data("order_item_brand_name");
					count = 0;
					// alert(product_id);
					// alert(qty)
					// alert(order_item_brand_name)
					// alert(order_item_brand)
					//var isProductAvailable=check_form();
					if (product_id == "") {
						toastr.error('Please Select product!!');
					} else if (qty == "" || qty == 0) {
						toastr.error('Please Enter At least one Quantity!!');
					}
					else if (order_item_brand == "" || order_item_brand == 0) {
						toastr.error('Please Select Brand!!');
					}
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
						var pro_master_price = $("#product_id").find('option:selected').data('pro_master_price');
						var is_including = $("#product_id").find('option:selected').data('is_including');
						var is_premium = $("#product_id").find('option:selected').data('is_premium');
						// var item_order_unit = $("#product_id").find('option:selected').data('item_order_unit');
						// var customer_cash_discount = $("#product_id").find('option:selected').data('customer_cash_discount');
						// var customer_additional_discount = $("#product_id").find('option:selected').data('customer_additional_discount');
						// alert(original_price);
						var unitname = $("#bag_box_id").find('option:selected').data('unitname');
						var item_order_unit = bag_box_id;
						original_price=parseFloat(original_price);
						var qty = $("#qty").val();
						var order_qty = qty;
						if(pro_master_price != original_price)
						{
							// alert(pro_master_price)
							// $("#pro_master_price").html("ori.Price:"+pro_master_price);
							var pro_price_main="Including Gst :"+pro_master_price;
						}
						else
						{
							var pro_price_main="";
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
						// unit logic by shivani //04-03-2023
						var bag=0;
						var box=0;
						var loose=0; 
						// alert(bag_box_id);
						if(bag_box_id<0 && bag_box_id!=100)
						{
							bag = order_qty;  
							qty= order_qty*inner_size;
							box=0;
						}
						else if(bag_box_id>0 && bag_box_id!=100)
						{
							box = order_qty; 
							qty= order_qty*outer_size;
							bag=0;
						}	
						else if(bag_box_id==100)
						{
							qty = order_qty;	
							bag=0;
							box=0; 
						}
						else
						{
							qty=0;
							bag=0;
							box=0;
						}
						// unit logic by shivani //04-03-2023

						/*if(bag_box_id==2)
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
						var total_bag = bag*inner_size;
						var total_box = box*outer_size;
						var totalsum = total_bag+total_box;
						var loose =  Math.floor(qty-totalsum);
						 
						}

						qty = (qty != "") ? parseFloat(qty) : 0;
						outer_size = (outer_size != "") ? parseFloat(outer_size) : 0;
						*/
						   
						// $p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
						// $p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];

						var customer_type = $("#customer_type").val();
						if(customer_type==8)
						{
							var item_gst = 0.1;
						}
						else
						{
							item_gst=gst;
						}


						//var item_gst=gst;
						var cd_discount = 0;
						var ad_discount = 0;
						var discount_amount = (original_price * discountPer) / 100;
						price = original_price - discount_amount;
						price1 = (original_price - discount_amount);

						//var total=box*price;
						price = price.toFixed(2);
						var total = qty * price;
						// price=format(price,3);
						// total=format(total,3);
						// total=total.replace(",","");
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
						 
						var brand_id = $("#brand_id").val();
				 
						// var duplicate = hasValue($("input.pro_id[value='" + pro_id + weight_id + "']"));
						var duplicate = 0;
						if (duplicate == 0) {
							var new_row = "<tr><td  class='text-center'><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></td><td style='padding: 1px!important;min-width: 187px;' width='300px;' class='text-center'><input type='hidden' class='pro_id' name='pro_id' id='pro_id' value='" + pro_id + weight_id + "'><input type='hidden' name='product_id[]' value='" + pro_id + "' class='product_id' id='product_id'/><input type='hidden' name='pro_name[]' value='" + p_name + "' id='pro_name'><input type='hidden' name='weight_id[]' value='" + weight_id + "' id='weight_id'><input type='hidden' name='brand_id[]' value='" + brand_id + "' id='brand_id'>" + p_name + "</td>" +

							"<td class='text-center'><a><input type='hidden' class='order_item_brand_id' name='order_item_brand_id[]' value='"+ order_item_brand +"' /></a>" + order_item_brand_name + "</td>"+

							"<td style='padding: 1px!important;'; class='text-center'>"+unitname+"<input type='hidden' name='item_order_unit[]' class='item_order_unit' value='"+item_order_unit+"'></td>" +
							"<td style='padding: 1px!important;'; class='text-center'><input type='text' name='order_qty[]' class='form-control positive  order_qty' style='text-align:right;width:80px;' value='" + order_qty + "' onChange='recalculateRow(this)'  id='order_qty'/></td>" +
							"<td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='inner_size' class='inner_size' value='"+inner_size+"'><input readonly class='form-control bag' type='text' name='bag[]' style='text-align:right;width:70px;' value='"+bag+"''></td>"+

							"<td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='outer_size' class='outer_size' value='"+outer_size+"'><input readonly type='text' class='form-control box_qty' style='text-align:right;width:70px;' name='box_qty[]' class='box_qty positive' value='"+box+"'></td>"+

							"<td hidden style='text-align:right;padding: 1px!important;'><input type='hidden' name='loose_qty' class='loose_qty' value='"+outer_size+"'><input readonly type='text' class='form-control loose' style='text-align:right;width:70px;' name='loose[]' class='loose positive' value='"+loose+"'></td>"+


							"<td hidden style='padding: 1px!important;' class='text-center'>"+stock+"</td>" +

							"<td style='padding: 1px!important;'><textarea rows='2' cols='10' id='pro_description' name='pro_description[]' class='pro_desc' style='margin: 0!important;'></textarea></td>" +

							"<td style='padding: 1px!important;' class='text-center'>"+hsncode+"</td>" +
							/*"<td style='text-align:right'><input type='hidden' name='inner_size' class`='inner_size' value='"+inner_size+"'><input readonly class='form-control bag' type='text' name='bag[]' style='text-align:right;width:100px;' value='"+bag+"''></td>"+

							"<td style='text-align:right'><input type='hidden' name='outer_size' class='outer_size' value='"+outer_size+"'><input readonly type='text' class='form-control box_qty' style='text-align:right;width:100px;' name='box_qty[]' class='box_qty positive' value='"+box+"'></td>"+*/

							"<td style='padding: 1px!important;' class='text-center'><input readonly type='text' name='qty[]' class='form-control positive  qty' style='text-align:right;width:80px;' value='" + qty + "' onChange='recalculateRow(this)'  id='qty'/><input class='new_qty' type='hidden' value='" + qty + "' id='qty'></td>" +

							"<td style='text-align:right;padding: 1px!important;'><input type='text' name='original_price[]' class='form-control  original_price' style='text-align:right;width:80px;' value='" + original_price + "' onChange='recalculateRow(this)'  id='original_price'/><input type='hidden' value=" + original_price + " class='original_price_hidden'/><input type='hidden' name=is_including[] id=is_including value="+is_including+"><span id='pro_master_price'>"+pro_price_main+"</span><span><input type='hidden' value=" + is_premium + " class='is_premium'/></span></td>" +


							"<td style='text-align:right;padding: 1px!important;'><input type='text' name='discount_amount[]' style='text-align:right;width: 80px;' class='form-control discount_amount' onChange='recalculateRow(this,1)' value=''></td>" +

							"<td style='text-align:right;padding: 1px!important;'><input style='text-align:right;width:80px;' type='text' name='discount[]' class='discount form-control' value='" + discountPer + "' onChange='recalculateRow(this,2)'></td>" +

							"<td style='padding: 1px!important;' class='price_val' ><input type='text' style='text-align:right;width:80px;'  name='price[]' class='price form-control' readonly onChange='recalculateRow(this)' value='" + price + "'><input type='hidden' style='text-align:right;' class='old_price form-control' value='" + price + "'></td>" +

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

							// alert(customer_cash_discount);
							// $("#cash_discount").val(customer_cash_discount);
							// $("#additional_discount").val(customer_additional_discount);
							// $('#category_id').prop('disabled',true);
							recalculateRow();
							// cdadCalculate();
						} else {
							/*$mainInputBox = $("input.pro_id[value='" + pro_id + weight_id + "']").parent().parent().find("td>input.qty");
							var old_va = $mainInputBox.val();
							var new_va = parseFloat(old_va) + parseFloat(qty);
							$mainInputBox.val(new_va);
							$mainInputBox.change();
							toastr.success("Product Qty Update Successfuly.");*/
							toastr.error("Product already added!!");
						}
					/*}
					else
					{
					toastr.error('Entered Quantity Is Not Available In Stock!!');
					}*/
					}
				$("#qty").val("");
				$("#bag_box_id").select2("val","");
				$("#bag_box_id").removeAttr("disabled","");
				$("#product_id").select2("val", "");

				$(".inner_size").html("");
				$(".outer_size").html("");
				$(".qty").html("");
				$("#order_item_brand").val("");
				$("#order_item_brand").select2("val","");
			})

			function check_form() {
				$(".form-body").children().removeClass("has-error");
				var isValid = true;
				var isCpSelfOrder = <?= !empty($is_cp_self_order) ? 'true' : 'false'; ?>;
				<?php 
					if ($_REQUEST['mode'] == "add") {
					?>
						if (!isCpSelfOrder) {
						if ($("#customer_type").val() == "" || $("#customer_type").val().split(" ").join("") == ""){
							vd = aj.error('customer_type', "Please Select Customer Type", "add_error");
							isValid = false;
						}
						if ($("#type_of_company").val() == "" || $("#type_of_company").val().split(" ").join("") == "") {
							vd = aj.error('type_of_company', "Please Select Company Type", "add_error");
							isValid = false;
						}
						}
						if ($("#customer_id").val() == "" || $("#customer_id").val().split(" ").join("") == "") {
							vd = aj.error('customer_id', "<?= ($c_type == 'channel_partner') ? 'Please Select Channel Partner.' : 'Please Select Customer.'; ?>", "add_error");
							isValid = false;
						}
						if (isCpSelfOrder) {
							if ($("#channel_partner_customer_id").val() == "" || $("#channel_partner_customer_id").val() == "0") {
								vd = aj.error('channel_partner_customer_id', "Please Select Customer.", "add_error");
								isValid = false;
							}
						}
						if ($("#order_date").val() == "" || $("#order_date").val().split(" ").join("") == "") {
							vd = aj.error('order_date', "Please Enter Order Date.", "add_error");
							isValid = false;
						}
					<?php 
					} 
				?>
				if ($("#name_gstin").val() == "" || $("#name_gstin").val().split(" ").join("") == "") {
							vd = aj.error('name_gstin', "Please Select GSTIN.", "add_error");
							isValid = false;
						}

				// if ($("#name_gstin").val() == "" || $("#name_gstin").val().split(" ").join("") == "") {
				// vd = aj.error('name_gstin', "Please Enter GST Number", "add_error");
				// isValid = false;
				// }
				if ($("#booking_place").val() == "" || $("#booking_place").val().split(" ").join("") == "") {
							vd = aj.error('booking_place', "Please Enter Booking Place.", "add_error");
							isValid = false;
						}
						if ($("#booking_pincode").val() == "" || $("#booking_pincode").val().split(" ").join("") == "") {
							vd = aj.error('booking_pincode', "Please Enter Booking Pincode.", "add_error");
							isValid = false;
						}

				// if ($("#shipping_address").val() == "" || $("#shipping_address").val().split(" ").join("") == "") {
				// 	vd = aj.error('shipping_address', "Please Enter Shipping Address", "add_error");
				// 	isValid = false;
				// }

				if ($("#billing_address").val() == "" || $("#billing_address").val().split(" ").join("") == "") {
					vd = aj.error('billing_address', "Please Enter Billing Address", "add_error");
					isValid = false;
				}
				if ($("#sales_executive").val() == "" || $("#sales_executive").val().split(" ").join("") == "") {
					vd = aj.error('sales_executive', "Please Select Sales Executive", "add_error");
					isValid = false;
				}
				 
				var count_row = $("#datatable_1").find('tbody tr').length;
				// Get a reference to the table element
				// const table = document.getElementById("datatable_1");
				const table = document.querySelector("#datatable_1 tbody");

				// Iterate through each row in the table
				$sdfcnt=0;
				table.querySelectorAll("tr").forEach(row => {
					// console.log($(row).find("td").find("a").find("input.order_item_brand_id").val());
				   	if ($(row).find("td").find("a").find("input.order_item_brand_id").val() == "" || $(row).find("td").find("a").find("input.order_item_brand_id").val() == undefined || $(row).find("td").find("a").find("input.order_item_brand_id").val() == null || $(row).find("td").find("a").find("input.order_item_brand_id").val() == 0) {
				   		$sdfcnt++
				   	}
				});
				// alert($sdfcnt);
				if ($sdfcnt > 0) {
					toastr.error("Please Add Brand!!");
					isValid = false;
				}
				// alert(count_row);
				if (count_row == 0) {
					toastr.error("Please Add At Least one Product!!");
					isValid = false;
				} 

				if (isValid) {
					var r = confirm("Are You sure want to Save this Order??");
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

			$(document).ready(function() {
				var oid = '<?= $_REQUEST['order_id'] ?>';
				var form_c_type = '<?= $c_type ?>';
				var isCpSelfOrder = <?= !empty($is_cp_self_order) ? 'true' : 'false'; ?>;
				var cpSelfId = '<?= (int) $cp_login_id ?>';
				if (isCpSelfOrder && mode == 'add') {
					if ($('#customer_id').data('select2')) {
						$('#customer_id').select2('destroy');
					}
					$('#customer_id').prop('disabled', true);
					// disabled select does not post - keep value via hidden
					if ($('#cp_customer_id_hidden').length == 0) {
						$('<input>').attr({type:'hidden', id:'cp_customer_id_hidden', name:'customer_id', value: cpSelfId}).insertAfter('#customer_id');
						$('#customer_id').removeAttr('name');
					}
					$("#customer_id").trigger('change');
					getCategory(cpSelfId);
					getCustomerCount(cpSelfId);

					function applyCpEndCustomerFields() {
						var $opt = $("#channel_partner_customer_id option:selected");
						if (!$opt.length || $opt.val() == "") {
							return;
						}
						var gst = $opt.data("gst") || "";
						var address = $opt.data("address") || "";
						var person = $opt.data("person") || "";
						var mobile = $opt.data("mobile") || "";
						var company = $opt.data("company") || "";
						var bookingPlace = $opt.data("booking_place") || "";
						var pincode = $opt.data("pincode") || "";
						if (gst != "") {
							$("#name_gstin").val(gst);
						}
						if (address != "") {
							$("#shipping_address").val(address);
							$("#billing_address").val(address);
						}
						if (bookingPlace != "") {
							$("#booking_place").val(bookingPlace);
						}
						if (pincode != "") {
							$("#booking_pincode").val(pincode);
						}
						$("#name").html(person);
						$("#name_phone").html(mobile);
						$("#name_address").html(address);
						$("#name_value").html(company);
					}
					$("#channel_partner_customer_id").off("change.cpEnd").on("change.cpEnd", applyCpEndCustomerFields);
					if ($("#channel_partner_customer_id").val() != "") {
						applyCpEndCustomerFields();
					}
				} else if(mode=='add' && oid!="")
				{ 
					//getCategory('<?= $customer_id ?>');
					getCustomer('<?= $customer_type ?>','<?= $customer_id ?>');
					getTransportname('<?= $transport_through ?>','<?= $transport_name ?>');
				}
				/* Channel Partner order: load partners only after Customer Type is selected via onChange */
				recalculateRow();
				recalculateFinalValues();
				$("#datatable_1").on('click', '.delete', function() {
					var rows = $("#datatable_1").find("tbody").find("tr").length;
					var r = confirm("Are you sure you want to delete?");
					if (r) {
						$(this).closest('tr').remove();
						cdadCalculate();

						if(rows==1){
							// $('#category_id').prop('disabled',false);
						}
					}
				});
			});

			$(document).ready(function() {

				var mode = "<?= $_REQUEST['mode']; ?>";
				if (mode == "edit") {
					$("#customer_id").trigger("change");
					var cid = "<?= $customer_id; ?>";

					getCustomerCount(cid);

					var top_cat_id = "<?= $top_cat_id; ?>";

					$("#category_id").select2("destroy");
					// $('#category_id option[value='+top_cat_id+']').attr('selected', 'selected');
					$("#category_id").select2();

					// $('#category_id').prop('disabled',true);
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
			if(mode=="edit"){
				var id = $("#transport_through").val(); 
				var transport_name_selected_id = $("#transport_name_selected_id").val();
				getTransportname(id,transport_name_selected_id);
			}
			function getTransportname(id,transport_name_selected_id=""){	
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


			// function getTermescondition() 
			// {

			//     var companytype = $("#type_of_company").val();
			//     $.ajax({
			//         type: "post",
			//         url: "get_terms_condition_ajax.php",
			//         data: "companytype=" + companytype,
			//         beforeSend: function() {
			//             $('.preloader').fadeIn('slow'); 
			//         },
			//         success: function(result) {
			//             setTimeout(function() {
			//                 $('.preloader').fadeOut('slow'); 
			//                 CKEDITOR.instances['terms_comdition'].setData(result); 
			//             });
			//         }           
			//    })            
			// }
		</script>
		<script type="text/javascript">
			var c_type = '<?= $_REQUEST['c_type']; ?>';
			if (c_type==7) 
			{
				$("#customer_id").click(function() 
				{
				   	var customer_id = $("#customer_id").val();
					$.ajax({
						type:'POST',
						url:'get_company_customer_type_detail_ajax.php',
						data:{
							customer_id:customer_id
						},
						beforeSend:function () {
							// 
						},
						success:function (result) {
							result = JSON.parse(result);
							if (result.ack==1) {
								$("#type_of_company").select2("val",result.type_of_company);
								$("#customer_type").select2("val",result.type_of_executive);
								// getTermescondition(result.type_of_company);
								$('#requesting_ajax_change_shipping').trigger('show.bs.modal');
								// setTimeout($("#customer_id").select2("val",result.customer_id), 3000);
								// $("#customer_id").select2("val",result.customer_id);
							}
						}
					}); 
				})
			}
		</script>
		<script type="text/javascript">
        	function getTermsDescr()
			{
				var terms_condition_id = $("#terms_condition_id").val();
				$.ajax({
					type: "post",
					url: "ajax_get_terms_description.php",
					data: "id=" + terms_condition_id,
					beforeSend: function() {
						$(".transCover").fadeIn(800);
						$('.preloader').fadeIn('slow'); 
					},
					success: function(result) 
					{
						$('.preloader').fadeOut('slow'); 
						CKEDITOR.instances['terms_comdition'].setData(result); 
					}
				})
			}

			function getRegards(R)
			{
				// alert($("#sales_executive").find("option:selected").attr("data-regrads"));
				CKEDITOR.instances['faithfully'].setData($("#sales_executive").find("option:selected").attr("data-regrads")); 
			}
			function getCustomerCount(cusid)
			{ 
				// alert();
				$.ajax({
					type: "post",
					url: "ajax_get_customer_count.php",
					data: "customer_id="+cusid+"&type=orders", 
					success: function(data) 
					{ 
						var result = JSON.parse(data); 
						$("#tot_order_cnt").html(result.total_customer_cnt);
						$("#monthly_order_cnt").html(result.current_month_customer_cnt);
					}
				})
			}
        </script>
	</body>
</html>