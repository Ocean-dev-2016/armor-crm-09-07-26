<?php
include('connect.php');

include('../include/orders.class.php');
$objOrder = new Order();

require_once("../include/push_notification.class.php");
$objPushNotification = new PushNotification();

if ($is_valid_api_key) {
	if ($is_valid_service) {
		if ($service == 'get_quotation_data_from_lead' || $service == 164) {
			$lead_id = isset($_REQUEST['lead_id']) ? $_REQUEST['lead_id'] : "";
			if ($lead_id) {
				$detail = array();
				$leaddata_r = $db->rp_getData("no_order_inquiry", "*", "id='" . $lead_id . "' AND isDelete=0", "", 0);
				while ($leaddata_d = mysqli_fetch_assoc($leaddata_r)) {
					/*get inquiry item*/
					$item = array();
					$sales_name =  $db->rp_getValue("sales_executive", "name", "id='" . $leaddata_d['sales_executive_id'] . "' AND isDelete=0");
					$sales_no =  $db->rp_getValue("sales_executive", "phone", "id='" . $leaddata_d['sales_executive_id'] . "' AND isDelete=0");
					$LeadItemR = $db->rp_getData("no_order_inquiry_item", "*", "inquiry_id='" . $leaddata_d['id'] . "' AND isDelete=0");
					while ($LeadItemD = mysqli_fetch_assoc($LeadItemR)) {
						$LeadItemD['original_price'] = $db->rp_getValue("product_weight_price", "price", "product_id='" . $LeadItemD['pro_id'] . "' AND weight_id='" . $LeadItemD['weight_id'] . "' AND isDelete=0");
						$LeadItemD['discount'] = 0;
						$LeadItemD['rate'] = $LeadItemD['original_price'];
						$subtotal += $LeadItemD['rate'] * $LeadItemD['pro_qty'];
						$item[] = $LeadItemD;
					}
					/*get inquiry item*/
					$leaddata_d['subtotal'] = $subtotal;
					$leaddata_d['cash_discount'] = 0;
					$leaddata_d['cash_discount_amount'] = 0;
					$leaddata_d['additional_discount'] = 0;
					$leaddata_d['additional_discount_amount'] = 0;
					$leaddata_d['transport_charge'] = 0;
					$leaddata_d['transport_charge'] = 0;
					$leaddata_d['gst'] = 0;
					$leaddata_d['gst_amount'] = 0;
					$leaddata_d['grand_total'] = $subtotal;
					$leaddata_d['terms_comdition'] = DEFAULT_TERMS;
					$leaddata_d['faithfully'] = $sales_name . " <br/> " . $sales_no;
					$leaddata_d['inquiry_item'] = $item;
					$detail[] = $leaddata_d;
				}
				if (!empty($detail)) {
					$reply = array("ack" => 1, "ack_msg" => "Data Get Successfully..", "developer_msg" => "Data Get Successfully..", "result" => $detail);
					echo json_encode($reply);
				} else {
					$reply = array("ack" => 0, "ack_msg" => "Lead Id Required", "developer_msg" => "Lead Id Required");
					echo json_encode($reply);
				}
			} else {
				$reply = array("ack" => 0, "ack_msg" => "Lead Id Required", "developer_msg" => "Lead Id Required");
				echo json_encode($reply);
			}
		} else if ($service == "create_quotation" || $service == 165) {
			$detail['customer_id'] = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$detail['sales_executive_id'] = isset($_REQUEST['sales_executive_id']) ? $_REQUEST['sales_executive_id'] : "";
			$detail['shipping_address'] = isset($_REQUEST['shipping_address']) ? $_REQUEST['shipping_address'] : "";
			$detail['billing_address'] = isset($_REQUEST['billing_address']) ? $_REQUEST['billing_address'] : "";
			$detail['vendor_code'] = isset($_REQUEST['vendor_code']) ? $_REQUEST['vendor_code'] : "";
			$detail['tendor_code'] = isset($_REQUEST['tendor_code']) ? $_REQUEST['tendor_code'] : "";
			$detail['tendor_no'] = isset($_REQUEST['tendor_no']) ? $_REQUEST['tendor_no'] : "";
			$detail['transport_through'] = isset($_REQUEST['transport_through']) ? $_REQUEST['transport_through'] : "";
			$detail['transport_name'] = isset($_REQUEST['transport_name']) ? $_REQUEST['transport_name'] : "";
			$detail['quotation_date'] = isset($_REQUEST['quotation_date']) ? $_REQUEST['quotation_date'] : "";
			$detail['packing_charge'] = isset($_REQUEST['packing_charge']) ? $_REQUEST['packing_charge'] : "";
			$detail['transport_charge'] = isset($_REQUEST['transport_charge']) ? $_REQUEST['transport_charge'] : "";
			$detail['terms_comdition'] = isset($_REQUEST['terms_comdition']) ? $db->clean($_REQUEST['terms_comdition']) : "";
			$detail['faithfully'] = isset($_REQUEST['faithfully']) ? $db->clean($_REQUEST['faithfully']) : "";

			if (isset($_REQUEST['customer_id']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['sales_executive_id'])) {
				include("../include/quotation.class.php");
				$objQuotation = new Quotation();
				$body = file_get_contents('php://input');
				$ack = $objQuotation->AddQuotationApi($detail, $body);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		}

		/*else if($service == "update_cash_additional_discount_flag" || $service ==1667)
		{		
			$gst_apply_flag=isset($_REQUEST['gst_apply_flag'])?$db->clean($_REQUEST['gst_apply_flag']):"";
			$tcs_apply_flag=isset($_REQUEST['tcs_apply_flag'])?$db->clean($_REQUEST['tcs_apply_flag']):"";

			if($_REQUEST['cart_type']=="3")
			{
				$table_main="orders";
				$table_item="order_product_item";
			}
			else if($_REQUEST['cart_type']=="2")
			{
				$table_main="quotation_detail";
				$table_item="quotation_product_item";
			}
			else{
				$table_main="cart_detail";
				$table_item="cart_item";
			}

			$customer_id = isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
		    $sales_id = isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
			$cart_id = isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";
			$packing_charge = isset($_REQUEST['packing_charge'])?$db->clean($_REQUEST['packing_charge']):0;
			$transport_charge = isset($_REQUEST['transport_charge'])?$db->clean($_REQUEST['transport_charge']):0;
			
			
			$additional_discount_flag = isset($_REQUEST['additional_discount_flag'])?$db->clean($_REQUEST['additional_discount_flag']):"";
			$additional_discount = isset($_REQUEST['additional_discount'])?$db->clean($_REQUEST['additional_discount']):"";
			
			$cash_discount_flag = isset($_REQUEST['cash_discount_flag'])?$db->clean($_REQUEST['cash_discount_flag']):"";
			$cash_discount = isset($_REQUEST['cash_discount'])?$db->clean($_REQUEST['cash_discount']):"";
			if($cart_id!="" && $customer_id!="") {
				$count = $db->rp_getTotalRecord($table_main,"id='".$cart_id."' AND isDelete=0",0);
				if($count>0) {
					if($_REQUEST['cart_type']=="2"){
						$order_items_r=$db->rp_getData($table_item,"*","quotation_id='".$cart_id."' AND isDelete=0","",0);
					}
					else{
						$order_items_r=$db->rp_getData($table_item,"*","order_id='".$cart_id."' AND isDelete=0","",0);
					}
					$sub_total=0;
					while($order_items_d=mysqli_fetch_assoc($order_items_r)) {
						$totalprice=$order_items_d['pro_qty']*$order_items_d['unitprice'];
						$totalprice=$db->rp_num($totalprice);
						$original_price=$unitprice;
						$final_price=$totalprice;
						$sub_total+=$final_price;
						$item_gst_total+=$order_items_d['item_gst_amount'];
						//$GST=$db->rp_getValue("product","igst","id='".$order_items_d['pro_id']."'");

		    		}
		    		if($db->rp_getValue("executive","type_of_executive","id='".$customer_id."'",0)==7){
		    			$GST =0.1;

		    		}else{

		    				$GST=18;
		    		}


		    		$additional_discounted_amount = $sub_total;
		    		$cash_discounted_amount = $sub_total;
		    		$additional_discount_amount = 0;
		    		$cash_discount_amount = 0;
		    		
		    		
		    		if($cash_discount!=0 && $cash_discount!=""){
		    			$cash_discount_amount = ($sub_total*$cash_discount)/100;
		    			$cash_discounted_amount = $sub_total-$cash_discount_amount;
					}else{
						$cash_discounted_amount=$sub_total;
					}
		    		

					if($additional_discount!=0 && $additional_discount!=""){
						$additional_discount_amount = ($cash_discounted_amount*$additional_discount)/100;
		    			$additional_discounted_amount = $cash_discounted_amount-$additional_discount_amount;
		    	
					}else{
						$additional_discounted_amount=$cash_discounted_amount;
					}
		    		
		    		$cash_discounted_amount = $db->rp_num((float)$cash_discounted_amount, 2, '.', '');
		    		$additional_discounted_amount = $db->rp_num((float)$additional_discounted_amount, 2, '.', '');

		    		$cash_discount_amount = $db->rp_num((float)$cash_discount_amount, 2, '.', '');
		    		$additional_discount_amount = $db->rp_num((float)$additional_discount_amount, 2, '.', '');
		    		
		    		$sub_total = $db->rp_num((float)$sub_total, 2, '.', '');
		    		

		    		$final_total = $additional_discounted_amount+$packing_charge+$transport_charge;
		    		//echo $GST;exit;

		    		if($gst_apply_flag=="1")
		    		{
						$gst_amount=$db->rp_num(($final_total*$GST)/100);
						//$gst_amount=$item_gst_total;
					}else{
						$gst_amount=0;
					}	


					if($tcs_apply_flag=="1")
		    		{
						$tcs_amount=$db->rp_num(($final_total*TCS_CHARGE_IN_PER)/100);
						$tcs_per=TCS_CHARGE_IN_PER;
					}else{
						$tcs_amount=0;
						$tcs_per=0;
					}

					$grandtotal=$db->rp_num($final_total+$gst_amount+$tcs_amount);

					//echo $grandtotal; exit;
					$before_roundoff=$db->rp_num($grandtotal,2);
					$whole = floor($before_roundoff);
			        $fraction = $before_roundoff - $whole;
			        $f1=  $db->rp_num((float)$fraction, 2, '.', '');
					$roundoff=$db->rp_num($f1,2);
					$grand_total=strval($db->rp_num(($before_roundoff-$roundoff),2));
					$grand_total=$db->rp_num(round($grandtotal),2);

					$dt=date("Y-m-d");

					$update_array = array(
					    "subtotal" => $sub_total,
					    "grand_total" => $grand_total,
					    "additional_discount" => $additional_discount,
					    "igst_amount" => $gst_amount,
					    //"additional_discount_flag" => $additional_discount_flag,
					    "additional_discount_amount" => $additional_discount_amount,
					    "cash_discount" => $cash_discount,
					    //"cash_discount_flag" => $cash_discount_flag,
					    "cash_discount_amount" => $cash_discount_amount,
					    "packing_charge" => $packing_charge,
					    "transport_charge" => $transport_charge,
					    "tcs_per" => $tcs_per,
					    "tcs_amount" => $tcs_amount,
					);

					$isUpdated=$db->rp_update($table_main ,$update_array,"id='".$cart_id."'",0);

					if($isUpdated)
		    		{
		    			$ack = array(
						    "ack" => 1,
						    "ack_msg" => "Discount Apply Successfully",
						    "subtotal" => $sub_total,
						    "gst_amount" => 0,
						    "additional_discount" => $additional_discount,
						    "additional_discount_amount" => $additional_discount_amount,
						    "additional_discount_flag" => $additional_discount_flag,
						    "after_additional_discount" => $additional_discounted_amount,
						    "cash_discount" => $cash_discount,
						    "cash_discount_amount" => $cash_discount_amount,
						    "cash_discount_flag" => $cash_discount_flag,
						    "after_cash_discount" => $cash_discounted_amount,
						    "before_roundoff" => $before_roundoff,
						    "roundoff" => $roundoff,
						    "grandtotal" => $grand_total,
						    "packing_charge" => $packing_charge,
						    "transport_charge" => $transport_charge,
						);
		    		}
		    		else
		    		{
		    			$ack=array("ack"=>0,"ack_msg"=>"Discount Not Apply.Please Check.");
		    		}
		    	}
			}
			else
		    {
		    	$ack=array("ack"=>0,"ack_msg"=>"Cart Id Sales Id And Customer Id Required");
		    }

			$db->printJSON($ack);
		}*/ else if ($service == "update_cash_additional_discount_flag" || $service == 166) {
			$gst_apply_flag = isset($_REQUEST['gst_apply_flag']) ? $db->clean($_REQUEST['gst_apply_flag']) : "";
			$tcs_apply_flag = isset($_REQUEST['tcs_apply_flag']) ? $db->clean($_REQUEST['tcs_apply_flag']) : "";

			if ($_REQUEST['cart_type'] == "3") {
				$table_main = "orders";
				$table_item = "order_product_item";
			} else if ($_REQUEST['cart_type'] == "2") {
				$table_main = "quotation_detail";
				$table_item = "quotation_product_item";
			} else {
				$table_main = "cart_detail";
				$table_item = "cart_item";
			}

			$customer_id = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$sales_id = isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
			$cart_id = isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : "";
			$packing_charge = isset($_REQUEST['packing_charge']) ? $db->clean($_REQUEST['packing_charge']) : 0;
			$transport_charge = isset($_REQUEST['transport_charge']) ? $db->clean($_REQUEST['transport_charge']) : 0;
			$additional_discount_flag = isset($_REQUEST['additional_discount_flag']) ? $db->clean($_REQUEST['additional_discount_flag']) : "";
			$additional_discount = isset($_REQUEST['additional_discount']) ? $db->clean($_REQUEST['additional_discount']) : 0;
			$cash_discount_flag = isset($_REQUEST['cash_discount_flag']) ? $db->clean($_REQUEST['cash_discount_flag']) : "";
			$cash_discount = isset($_REQUEST['cash_discount']) ? $db->clean($_REQUEST['cash_discount']) : 0;
			$additional_discount_amount = isset($_REQUEST['additional_discount_amount']) ? $db->clean($_REQUEST['additional_discount_amount']) : 0;
			$cash_discount_amount = isset($_REQUEST['cash_discount_amount']) ? $db->clean($_REQUEST['cash_discount_amount']) : 0;
			//echo $transport_charge;exit;
			if ($cart_id != "" && $customer_id != "") {
				$count = $db->rp_getTotalRecord($table_main, "id='" . $cart_id . "' AND isDelete=0", 0);
				if ($count > 0) {
					if ($_REQUEST['cart_type'] == "2") {
						$order_items_r = $db->rp_getData($table_item, "*", "quotation_id='" . $cart_id . "' AND isDelete=0", "", 0);
					} else {
						$order_items_r = $db->rp_getData($table_item, "*", "order_id='" . $cart_id . "' AND isDelete=0", "", 0);
					}
					$sub_total = 0;
					$total_items = 0;
					$gst_amount = 0;


					if ($_REQUEST['cart_type'] == "2") {
						$sub_total = $db->rp_getValue($table_item, "SUM(totalprice)", "quotation_id='" . $cart_id . "'", 0);
					} else {
						$sub_total = $db->rp_getValue($table_item, "SUM(totalprice)", "order_id='" . $cart_id . "'", 0);
					}
					//echo $sub_total;
					$cash_discount_amount = ($sub_total * $cash_discount) / 100;
					$cash_total = $sub_total - $cash_discount_amount;
					$additional_discount_amount = ($cash_total * $additional_discount) / 100;

					while ($order_items_d = mysqli_fetch_assoc($order_items_r)) {
						$total_items++;
						$totalprice = $order_items_d['pro_qty'] * $order_items_d['unitprice'];
						$totalprice = $db->rp_num($totalprice);
						$original_price = $unitprice;
						$final_price = $totalprice;
						//$sub_total+=$final_price;
						//echo $final_price."/".$sub_total."*".$cash_discount_amount."<br/>";



						$item_cd = $db->rp_num(($final_price / $sub_total) * $cash_discount_amount, 2);
						$item_ad = $db->rp_num(($final_price / $sub_total) * $additional_discount_amount, 2);
						$item_fright_charge = $db->rp_num(($final_price / $sub_total) * $transport_charge, 2);
						$item_other_charge = $db->rp_num(($final_price / $sub_total) * $packing_charge, 2);

						$taxable_amount = $final_price - $item_cd - $item_ad + $item_fright_charge + $item_other_charge;
						$total_taxable_amount += $taxable_amount;
						if ($gst_apply_flag == "1") {
							$gst_amount = $db->rp_num(($taxable_amount * $order_items_d['igst_tax']) / 100, 2);
						} else {
							$gst_amount = 0;
						}
						$total_gst_amount += $gst_amount;
						$final_total = $final_price + $gst_amount;
						$main_total = +$final_total;
						//	echo $taxable_amount;


						$update_item_array = array(
							"cash_discount" => $cash_discount,
							"cash_discount_amount" => $item_cd,
							"additional_discount" => $additional_discount,
							"additional_discount_amount" => $item_ad,
							"other_charge" => $item_other_charge,
							"fright_charge" => $item_fright_charge,
							"igst_amount" => $gst_amount,

							"taxable" => $taxable_amount,
							"subtotal" => $final_total,


						);
						//exit();
						$isUpdated = $db->rp_update($table_item, $update_item_array, "id='" . $order_items_d['id'] . "'", 0);
						//$gst_amount
						//	echo $item_cd;
					}
					//	echo $sub_total; exit();



					$additional_discounted_amount = $sub_total;
					$cash_discounted_amount = $sub_total;
					$additional_discount_amount = 0;
					$cash_discount_amount = 0;


					if ($cash_discount != 0 && $cash_discount != "") {
						$cash_discount_amount = ($sub_total * $cash_discount) / 100;
						$cash_discounted_amount = $sub_total - $cash_discount_amount;
					} else if ($cash_discount_amount != "" && $cash_discount_amount != 0) {
						$cash_discounted_amount = $sub_total - $cash_discount_amount;
					} else {
						$cash_discounted_amount = $sub_total;
					}



					if ($additional_discount != 0 && $additional_discount != "") {
						$additional_discount_amount = ($cash_discounted_amount * $additional_discount) / 100;
						$additional_discounted_amount = $cash_discounted_amount - $additional_discount_amount;
					} else if ($additional_discount_amount != "" && $additional_discount_amount != 0) {

						$additional_discounted_amount = $cash_discounted_amount - $additional_discount_amount;
					} else {
						$additional_discounted_amount = $cash_discounted_amount;
					}


					$cash_discounted_amount = $db->rp_num((float)$cash_discounted_amount, 2, '.', '');
					$additional_discounted_amount = $db->rp_num((float)$additional_discounted_amount, 2, '.', '');


					$sub_total = $db->rp_num((float)$sub_total, 2, '.', '');


					$final_total = $additional_discounted_amount + $packing_charge + $transport_charge;



					if ($tcs_apply_flag == "1") {
						$tcs_amount = $db->rp_num(($main_total * TCS_CHARGE_IN_PER) / 100);
						$tcs_per = TCS_CHARGE_IN_PER;
					} else {
						$tcs_amount = 0;
						$tcs_per = 0;
					}

					$grandtotal = $db->rp_num($total_taxable_amount + $total_gst_amount + $tcs_amount);

					$before_roundoff = $db->rp_num($grandtotal, 2);
					$whole = floor($before_roundoff);
					$fraction = $before_roundoff - $whole;
					$f1 =  $db->rp_num((float)$fraction, 2, '.', '');
					$roundoff = $db->rp_num($f1, 2);
					$grand_total = strval($db->rp_num(($before_roundoff - $roundoff), 2));
					$grand_total = $db->rp_num(round($grandtotal), 2);

					$dt = date("Y-m-d");

					$update_array = array(
						"subtotal" => $total_taxable_amount,
						"grand_total" => $grandtotal,
						"roundoff" => $roundoff,
						"grand_total_rounded" => $grand_total,
						"additional_discount" => $additional_discount,
						"igst_amount" => $total_gst_amount,
						"additional_discount_amount" => $additional_discount_amount,
						"cash_discount" => $cash_discount,
						"cash_discount_amount" => $cash_discount_amount,
						"packing_charge" => $packing_charge,
						"transport_charge" => $transport_charge,
						"tcs_per" => $tcs_per,
						"tcs_amount" => $tcs_amount,
					);
					//exit();
					$isUpdated = $db->rp_update($table_main, $update_array, "id='" . $cart_id . "'", 0);

					if ($isUpdated) {
						$ack = array(
							"ack" => 1,
							"ack_msg" => "Discount Apply Successfully",
							"subtotal" => $sub_total,
							"gst_amount" => 0,
							"additional_discount" => $additional_discount,
							"additional_discount_amount" => $additional_discount_amount,
							"additional_discount_flag" => $additional_discount_flag,
							"after_additional_discount" => $additional_discounted_amount,
							"cash_discount" => $cash_discount,
							"cash_discount_amount" => $cash_discount_amount,
							"cash_discount_flag" => $cash_discount_flag,
							"after_cash_discount" => $cash_discounted_amount,
							"before_roundoff" => $before_roundoff,
							"roundoff" => $roundoff,
							"grandtotal" => $grand_total,
							"packing_charge" => $packing_charge,
							"transport_charge" => $transport_charge,
						);
					} else {
						$ack = array("ack" => 0, "ack_msg" => "Discount Not Apply.Please Check.");
					}
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Cart Id Sales Id And Customer Id Required");
			}

			$db->printJSON($ack);
		} else if ($service == "convert_lead_to_quotation" || $service == 167) {
			include("../include/quotation.class.php");
			$ObjQuotation = new Quotation();
			$lead_id = isset($_REQUEST['lead_id']) ? $_REQUEST['lead_id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";

			if ($lead_id) {
				$detail = array();
				$leaddata_r = $db->rp_getData("no_order_inquiry", "*", "id='" . $lead_id . "'", "", 0);
				if ($leaddata_d = mysqli_fetch_assoc($leaddata_r)) {
					$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $_REQUEST['sales_id'] . "'", 0);
					$sales_phone = $db->rp_getValue("sales_executive", "phone", "id='" . $_REQUEST['sales_id'] . "'");
					$detail['inquiry_id']		  = $lead_id;
					$detail['cid']			 	  = $leaddata_d['dealer_id'];
					$detail['dealer_id']		  = $leaddata_d['dealer_id'];
					$detail['type_of_company']	  = $leaddata_d['type_of_company'];
					$detail['sales_executive_id'] = $_REQUEST['sales_id'];
					$detail['gst_no'] = $leaddata_d['gst_no'];
					$detail['quotation_date']	  = date('Y-m-d');
					$quotation_no                 = $db->getLastInsertId("quotation_detail");
					$detail['quotation_no']       = ""; // DEALER_QUOTATION_NO.str_pad($quotation_no, 2, '0', STR_PAD_LEFT);	
					$detail['currency_code']	= 1;
					$detail['reference']	    = "";
					$detail['reference_date']	= "";
					$detail['attn']	            = "";
					$detail['attn_no']	        = "";
					$detail['attn_email']	    = "";
					$detail['billing_address']	    = $db->clean($leaddata_d['billing_address']);
					$detail['shipping_address']	    = $db->clean($leaddata_d['shipping_address']);
					$detail['terms_comdition']	    = DEFAULT_TERMS;
					$detail['faithfully']	    = $sales_name . '<br/>' . $sales_phone;
					$detail['quotation_expiry_date'] = date('Y-m-d');

					$leaditem_r = $db->rp_getData("no_order_inquiry_item", "*", "inquiry_id='" . $leaddata_d['id'] . "' AND isDelete=0", "", 0);
					if ($leaditem_r) {
						$qty1 = array();
						$product_id1 = array();
						$pro_name1 = array();
						$weight_id1 = array();
						$item_remark1 = array();
						while ($leaditem_d = mysqli_fetch_assoc($leaditem_r)) {
							$price = $db->rp_getValue("product_weight_price", "price", "product_id='" . $leaditem_d['pro_id'] . "' AND weight_id='" . $leaditem_d['weight_id'] . "'", 0);
							$type_of_executive = $db->rp_getValue("executive", "type_of_executive", "id='" . $customer_id . "' AND isDelete=0", 0);
							if ($type_of_executive == "8") {

								$item_gst = 0.1;
							} else {
								$item_gst = $db->rp_getValue("product", "igst", "id='" . $leaditem_d['pro_id'] . "'", 0);
							}
							//	$item_gst = $db->rp_getValue("product","igst","id='".$leaditem_d['pro_id']."'",0);
							$qty1[] = $leaditem_d['pro_qty'];
							$product_id1[] = $leaditem_d['pro_id'];
							$pro_name1[] = $db->clean($leaditem_d['pro_name']);
							$weight_id1[] = $leaditem_d['weight_id'];
							$remark1[] = $db->clean($leaditem_d['item_remark']);
							$weight1[] = 0;
							$box_qty1[] = 0;
							$cartoon_qty1[] = 0;
							$original_price1[] = $price;
							$price1[] = $price;
							$discount1[] = 0;
							$item_gst1[] = $item_gst;
						}
						$size1[] = sizeof($product_id1);
						$value_check1 = sizeof($product_id1);
						if (in_array($value_check1, $size1)) {
							$isValidArray1 = true;
						} else {
							$isValidArray1 = false;
						}
						if ($isValidArray1 && !empty($product_id1)) {
							for ($j = 0; $j < sizeof($qty1); $j++) {
								$item[] = array("qty" => $qty1[$j], "pid" => $product_id1[$j], "pro_name" => $pro_name1[$j], "weight_id" => $weight_id1[$j], "item_remark" => $remark1[$j], "weight" => $weight1[$j], "box_qty" => $box_qty1[$j], "cartoon_qty" => $cartoon_qty1[$j], "original_price" => $original_price1[$j], "discount" => $discount1[$j], "price" => $price1[$j], "item_gst" => $item_gst1[$j]);
							}
						}
					}
					// print_r($detail);exit;

				}
			} else if ($customer_id != "") {
				$customer_d = $db->rp_getData("executive", "*", "id='" . $customer_id . "' AND isDelete=0", "", 0);

				$customer_r = mysqli_fetch_assoc($customer_d);

				$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'", 0);
				$sales_phone = $db->rp_getValue("sales_executive", "phone", "id='" . $sales_id . "'");
				$detail['inquiry_id']		  = "";
				$detail['cid']			 	  = $customer_id;
				$detail['dealer_id']		  = $customer_id;
				$detail['sales_executive_id'] = $sales_id;
				$detail['gst_no'] = $customer_r['gst'];
				$detail['quotation_date']	  = date('Y-m-d');
				$quotation_no                 = $db->getLastInsertId("quotation_detail");
				$detail['quotation_no']       = ""; //DEALER_QUOTATION_NO.str_pad($quotation_no, 2, '0', STR_PAD_LEFT);	
				$detail['currency_code']	= 1;
				$detail['reference']	    = "";
				$detail['reference_date']	= "";
				$detail['attn']	            = "";
				$detail['attn_no']	        = "";
				$detail['attn_email']	    = "";
				$detail['billing_address']	    = $customer_r['billing_address'];
				$shipping_address =	 $db->rp_getValue("customer_vs_shipping_address", "shipping_address", "customer_id='" . $customer_r['id'] . "' AND isDelete=0 ORDER BY id ASC", 0);
				$detail['shipping_address']	    =		isset($shipping_address) ? $shipping_address : "";
				$detail['terms_comdition']	    = DEFAULT_TERMS;
				$detail['faithfully']	    = $sales_name . '<br/>' . $sales_phone;
				$detail['quotation_expiry_date'] = date('Y-m-d');
			}


			$reply1 = $ObjQuotation->AddQuotationApi($detail, $item);
			if ($reply1['ack'] == 1) {
				$reply1 = array("ack" => 1, "ack_msg" => "Quotation Generated Successfully", "quotation_id" => $reply1['order_id']);
			} else {
				$reply1 = array("ack" => 0, "ack_msg" => $reply1['ack_msg']);
			}
			echo json_encode($reply1);
		} else if ($service == "cart_to_order" || $service == 168) {
			$cart_id = isset($_REQUEST['cart_id']) ? $_REQUEST['cart_id'] : "";
			if ($cart_id) {
				$detail = array();
				$cart_r = $db->rp_getData("cart_detail", "*", "id='" . $cart_id . "'", "", 0);
				if ($cart_d = mysqli_fetch_assoc($cart_r)) {

					if ($_REQUEST['cart_type'] == "1") { //quotation 
						$quotation_no = $db->getLastInsertId("quotation_detail");
						$quotation_no = DEALER_QUOTATION_NO . str_pad($quotation_no, 2, '0', STR_PAD_LEFT);
						$created_date	= date('Y-m-d H:i:s');
						$quotation_date	= isset($detail['quotation_date']) ? $detail['quotation_date'] : date("Y-m-d");
						$rows 	= array(
							"quotation_no",
							"inquiry_id",
							"customer_id",
							"dealer_id",
							"super_stockist_id",
							"customer_name",
							"client_code",
							"customer_flag",
							"company_name",
							"customer_type",
							"contact_number",
							"address",
							"city",
							"state",
							"country",
							"quotation_date",
							"status",
							"sales_id",
							"sales_type",
							"class_id",
							"proforma_invoice_id",
							"class_name",
							"area_id",
							"area_name",
							"email",
							"item_total_price",
							"taxable",
							"discount",
							"discount_amount",
							"subtotal",
							"cash_discount",
							"cash_discount_amount",
							"additional_discount",
							"additional_discount_amount",
							"total_amount",
							"cgst_amount",
							"sgst_amount",
							"igst_amount",
							"grand_total",
							"roundoff",
							"total_taxamount",
							"grand_total_rounded",
							"taxable_amount",
							"discount_type",
							"gst",
							"entry_flag",
							"terms_comdition",
							"faithfully",
							"transport_name",
							"transport_through",
							"shipping_address",
							"billing_address",
							"transport_charge",
							"packing_charge",
							"tcs_per",
							"tcs_amount",
							"remarks",
						);
						$values = array(
							$quotation_no,
							isset($cart_d['lead_id']) ? $cart_d['lead_id'] : "",
							isset($cart_d['customer_id']) ? $cart_d['customer_id'] : "",
							isset($cart_d['dealer_id']) ? $cart_d['dealer_id'] : "",
							isset($cart_d['super_stockist_id']) ? $cart_d['super_stockist_id'] : "",
							isset($cart_d['customer_name']) ? $cart_d['customer_name'] : "",
							isset($cart_d['client_code']) ? $cart_d['client_code'] : "",
							isset($cart_d['customer_flag']) ? $cart_d['customer_flag'] : "",
							isset($cart_d['company_name']) ? $cart_d['company_name'] : "",
							isset($cart_d['customer_type']) ? $cart_d['customer_type'] : "",
							isset($cart_d['contact_number']) ? $cart_d['contact_number'] : "",
							isset($cart_d['address']) ? addslashes($cart_d['address']) : "",
							isset($cart_d['city']) ? $cart_d['city'] : "",
							isset($cart_d['state']) ? $cart_d['state'] : "",
							isset($cart_d['country']) ? $cart_d['country'] : "",
							$quotation_date,
							-1,
							isset($cart_d['sales_id']) ? $cart_d['sales_id'] : "",
							isset($cart_d['sales_type']) ? $cart_d['sales_type'] : "",
							isset($cart_d['class_id']) ? $cart_d['class_id'] : "",
							isset($cart_d['proforma_invoice_id']) ? $cart_d['proforma_invoice_id'] : "",
							isset($cart_d['class_name']) ? $cart_d['class_name'] : "",
							isset($cart_d['area_id']) ? $cart_d['area_id'] : "",
							isset($cart_d['area_name']) ? $cart_d['area_name'] : "",
							isset($cart_d['email']) ? $cart_d['email'] : "",
							isset($cart_d['item_total_price']) ? $cart_d['item_total_price'] : "",
							isset($cart_d['taxable']) ? $cart_d['taxable'] : "",
							isset($cart_d['discount']) ? $cart_d['discount'] : "",
							isset($cart_d['discount_amount']) ? $cart_d['discount_amount'] : "",
							isset($cart_d['subtotal']) ? $cart_d['subtotal'] : "",
							isset($cart_d['cash_discount']) ? $cart_d['cash_discount'] : "",
							isset($cart_d['cash_discount_amount']) ? $cart_d['cash_discount_amount'] : "",
							isset($cart_d['additional_discount']) ? $cart_d['additional_discount'] : "",
							isset($cart_d['additional_discount_amount']) ? $cart_d['additional_discount_amount'] : "",
							isset($cart_d['total_amount']) ? $cart_d['total_amount'] : "",
							isset($cart_d['cgst_amount']) ? $cart_d['cgst_amount'] : "",
							isset($cart_d['sgst_amount']) ? $cart_d['sgst_amount'] : "",
							isset($cart_d['igst_amount']) ? $cart_d['igst_amount'] : "",
							isset($cart_d['grand_total']) ? $cart_d['grand_total'] : "",
							isset($cart_d['roundoff']) ? $cart_d['roundoff'] : "",
							isset($cart_d['total_taxamount']) ? $cart_d['total_taxamount'] : "",
							isset($cart_d['grand_total_rounded']) ? $cart_d['grand_total_rounded'] : "",
							isset($cart_d['taxable_amount']) ? $cart_d['taxable_amount'] : "",
							isset($cart_d['discount_type']) ? $cart_d['discount_type'] : "",
							isset($cart_d['gst']) ? $cart_d['gst'] : "",
							isset($cart_d['entry_flag']) ? $cart_d['entry_flag'] : "",
							isset($cart_d['terms_comdition']) ? $db->clean($cart_d['terms_comdition']) : "",
							isset($cart_d['faithfully']) ? $db->clean($cart_d['faithfully']) : "",
							isset($cart_d['transport_name']) ? $cart_d['transport_name'] : "",
							isset($cart_d['transport_through']) ? $cart_d['transport_through'] : "",
							isset($cart_d['shipping_address']) ? $db->clean($cart_d['shipping_address']) : "",
							isset($cart_d['billing_address']) ? $db->clean($cart_d['billing_address']) : "",
							isset($cart_d['transport_charge']) ? $cart_d['transport_charge'] : "",
							isset($cart_d['packing_charge']) ? $cart_d['packing_charge'] : "",
							isset($cart_d['tcs_per']) ? $cart_d['tcs_per'] : "",
							isset($cart_d['tcs_amount']) ? $cart_d['tcs_amount'] : "",
							isset($cart_d['remarks']) ? $db->clean($cart_d['remarks']) : "",
						);

						/*log entry*/
						/*$sales_name = $db->rp_getValue("sales_executive","name","id='".$cart_d['sales_id']."'",0);
									$module_name = "Quotation";
									$flag = "Application";	
									$log_description = $module_name." ".$quotation_no." Created By ".$sales_name." ON ".date("Y-m-d H:i:s");*/
						/*log entry*/

						$quotation_id = $db->rp_insert(
							"quotation_detail",
							$values,
							$rows,
							0/*,$log_description,$flag,$module_name,$cart_d['sales_id'],$cart_d['customer_id']*/
						);

						if ($quotation_id != 0) {
							$cart_item_r = $db->rp_getData("cart_item", "*", "order_id='" . $cart_id . "' AND isDelete=0", "", 0);
							if ($cart_item_r) {
								while ($cart_item_d = mysqli_fetch_assoc($cart_item_r)) {
									//print_r($cart_item_d);exit;
									$rows 	= array(
										"quotation_id",
										"top_cat_id",
										"cat_id",
										"pro_id",
										"weight_id",
										"pro_name",
										"pro_description",
										"pro_qty",
										"remaining_qty",
										"inner_size",
										"outer_size",
										"box_qty",
										"cartoon_qty",
										"loose_qty",
										"unitprice",
										"original_price",
										"totalprice",
										"discount",
										"discount_amount",

										"price_list_id",
										"price_list_price",
										"price_list_discounted_price",
										"price_list_discounted_amount",
										"price_list_discount_type",
										"price_list_discount",
										"cash_discount_amount",
										"additional_discount_amount",
										"igst_tax",
										"igst_amount",
										"taxable",
										"subtotal",
										"hsn_code",
										"other_charge",
										"fright_charge",
										"is_including",
										"item_order_unit",
										"order_qty",
										"order_item_brand_id",

									);
									$values = array(
										$quotation_id,
										isset($cart_item_d['top_cat_id']) ? $cart_item_d['top_cat_id'] : "",
										isset($cart_item_d['cat_id']) ? $cart_item_d['cat_id'] : "",
										isset($cart_item_d['pro_id']) ? $cart_item_d['pro_id'] : "",
										isset($cart_item_d['weight_id']) ? $cart_item_d['weight_id'] : "",
										isset($cart_item_d['pro_name']) ? $db->clean($cart_item_d['pro_name']) : "",
										isset($cart_item_d['pro_description']) ? $db->clean($cart_item_d['pro_description']) : "",
										isset($cart_item_d['pro_qty']) ? $cart_item_d['pro_qty'] : "",
										isset($cart_item_d['remaining_qty']) ? $cart_item_d['remaining_qty'] : "",
										isset($cart_item_d['inner_size']) ? $cart_item_d['inner_size'] : "",
										isset($cart_item_d['outer_size']) ? $cart_item_d['outer_size'] : "",
										isset($cart_item_d['box_qty']) ? $cart_item_d['box_qty'] : "",
										isset($cart_item_d['cartoon_qty']) ? $cart_item_d['cartoon_qty'] : "",
										isset($cart_item_d['loose']) ? $cart_item_d['loose'] : "",
										isset($cart_item_d['unitprice']) ? $cart_item_d['unitprice'] : "",
										isset($cart_item_d['original_price']) ? $cart_item_d['original_price'] : "",
										isset($cart_item_d['totalprice']) ? $cart_item_d['totalprice'] : "",
										isset($cart_item_d['discount']) ? $cart_item_d['discount'] : "",
										isset($cart_item_d['discount_amount']) ? $cart_item_d['discount_amount'] : "",

										isset($cart_item_d['price_list_id']) ? $cart_item_d['price_list_id'] : "",
										isset($cart_item_d['price_list_price']) ? $cart_item_d['price_list_price'] : "",
										isset($cart_item_d['price_list_discounted_price']) ? $cart_item_d['price_list_discounted_price'] : "",
										isset($cart_item_d['price_list_discounted_amount']) ? $cart_item_d['price_list_discounted_amount'] : "",
										isset($cart_item_d['price_list_discount_type']) ? $cart_item_d['price_list_discount_type'] : "",
										isset($cart_item_d['price_list_discount']) ? $cart_item_d['price_list_discount'] : "",
										isset($cart_item_d['cash_discount_amount']) ? $cart_item_d['cash_discount_amount'] : "",
										isset($cart_item_d['additional_discount_amount']) ? $cart_item_d['additional_discount_amount'] : "",
										isset($cart_item_d['igst_tax']) ? $cart_item_d['igst_tax'] : "",
										isset($cart_item_d['igst_amount']) ? $cart_item_d['igst_amount'] : "",
										isset($cart_item_d['taxable']) ? $cart_item_d['taxable'] : "",
										isset($cart_item_d['subtotal']) ? $cart_item_d['subtotal'] : "",
										isset($cart_item_d['hsn_code']) ? $cart_item_d['hsn_code'] : "",
										isset($cart_item_d['other_charge']) ? $cart_item_d['other_charge'] : "",
										isset($cart_item_d['fright_charge']) ? $cart_item_d['fright_charge'] : "",
										isset($cart_item_d['is_including']) ? $cart_item_d['is_including'] : "",
										isset($cart_item_d['item_order_unit']) ? $cart_item_d['item_order_unit'] : "",
										isset($cart_item_d['box_qty']) ? $cart_item_d['box_qty'] : "",
										isset($cart_item_d['order_item_brand_id']) ? $cart_item_d['order_item_brand_id'] : "",


									);
									$total_qty += $cart_item_d['pro_qty'];
									$sub_total += $cart_item_d['totalprice'];
									$item_id = $db->rp_insert("quotation_product_item", $values, $rows, 0);
								}
							}
							$total_items = $db->rp_getTotalRecord("quotation_product_item", "quotation_id='" . $quotation_id . "' AND isDelete=0");
							if ($total_items != 0) {
								$reply = array("ack" => 1, "developer_msg" => "Quotation Added Successfully", "ack_msg" => "Quotation Added  Successfully", "order_id" => $quotation_id);
							} else {
								$reply = array("ack" => 0, "developer_msg" => "Quotation Item Not inserted", "ack_msg" => "Quotation Item Not inserted");
							}
						} else {
							$reply = array("ack" => 0, "developer_msg" => "Order  Not inserted", "ack_msg" => "Order Not inserted2");
						}
					} else {
						/*$order_no=$db->getLastInsertId("orders");
						$order_no=OUTLETS_ORDER_NO.str_pad($order_no, 2, '0', STR_PAD_LEFT);*/

						$c_type2 = $cart_d['customer_type'];
						$order_no = $db->getlastInsertId("orders");
						if ($c_type2 == '1') {
							$order_no = SS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
						} else if ($c_type2 == '2') {
							$order_no = DISTRIBUTOR_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
						} else {
							$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
						}
						$created_date	= date('Y-m-d H:i:s');
						$order_date	= isset($detail['order_date']) ? $detail['order_date'] : date("Y-m-d");
						$rows 	= array(
							"order_no",
							"customer_id",
							"dealer_id",
							"super_stockist_id",
							"customer_name",
							"client_code",
							"customer_flag",
							"company_name",
							"customer_type",
							"contact_number",
							"address",
							"city",
							"state",
							"country",
							"order_date",
							"status",
							"sales_id",
							"sales_type",
							"class_id",
							"proforma_invoice_id",
							"class_name",
							"area_id",
							"area_name",
							"email",
							"item_total_price",
							"taxable",
							"discount",
							"discount_amount",
							"subtotal",
							"cash_discount",
							"cash_discount_amount",
							"additional_discount",
							"additional_discount_amount",
							"total_amount",
							"cgst_amount",
							"sgst_amount",
							"igst_amount",
							"grand_total",
							"roundoff",
							"total_taxamount",
							"grand_total_rounded",
							"taxable_amount",
							"discount_type",
							"gst",
							"entry_flag",
							"terms_comdition",
							"faithfully",
							"transport_name",
							"transport_through",
							"shipping_address",
							"billing_address",
							"transport_charge",
							"packing_charge",
							"remarks",
						);
						$values = array(
							$order_no,
							isset($cart_d['customer_id']) ? $cart_d['customer_id'] : "",
							isset($cart_d['dealer_id']) ? $cart_d['dealer_id'] : "",
							isset($cart_d['super_stockist_id']) ? $cart_d['super_stockist_id'] : "",
							isset($cart_d['customer_name']) ? $cart_d['customer_name'] : "",
							isset($cart_d['client_code']) ? $cart_d['client_code'] : "",
							isset($cart_d['customer_flag']) ? $cart_d['customer_flag'] : "",
							isset($cart_d['company_name']) ? $cart_d['company_name'] : "",
							isset($cart_d['customer_type']) ? $cart_d['customer_type'] : "",
							isset($cart_d['contact_number']) ? $cart_d['contact_number'] : "",
							isset($cart_d['address']) ? addslashes($cart_d['address']) : "",
							isset($cart_d['city']) ? $cart_d['city'] : "",
							isset($cart_d['state']) ? $cart_d['state'] : "",
							isset($cart_d['country']) ? $cart_d['country'] : "",
							$order_date,
							-1,
							isset($cart_d['sales_id']) ? $cart_d['sales_id'] : "",
							isset($cart_d['sales_type']) ? $cart_d['sales_type'] : "",
							isset($cart_d['class_id']) ? $cart_d['class_id'] : "",
							isset($cart_d['proforma_invoice_id']) ? $cart_d['proforma_invoice_id'] : "",
							isset($cart_d['class_name']) ? $cart_d['class_name'] : "",
							isset($cart_d['area_id']) ? $cart_d['area_id'] : "",
							isset($cart_d['area_name']) ? $cart_d['area_name'] : "",
							isset($cart_d['email']) ? $cart_d['email'] : "",
							isset($cart_d['item_total_price']) ? $cart_d['item_total_price'] : "",
							isset($cart_d['taxable']) ? $cart_d['taxable'] : "",
							isset($cart_d['discount']) ? $cart_d['discount'] : "",
							isset($cart_d['discount_amount']) ? $cart_d['discount_amount'] : "",
							isset($cart_d['subtotal']) ? $cart_d['subtotal'] : "",
							isset($cart_d['cash_discount']) ? $cart_d['cash_discount'] : "",
							isset($cart_d['cash_discount_amount']) ? $cart_d['cash_discount_amount'] : "",
							isset($cart_d['additional_discount']) ? $cart_d['additional_discount'] : "",
							isset($cart_d['additional_discount_amount']) ? $cart_d['additional_discount_amount'] : "",
							isset($cart_d['total_amount']) ? $cart_d['total_amount'] : "",
							isset($cart_d['cgst_amount']) ? $cart_d['cgst_amount'] : "",
							isset($cart_d['sgst_amount']) ? $cart_d['sgst_amount'] : "",
							isset($cart_d['igst_amount']) ? $cart_d['igst_amount'] : "",
							isset($cart_d['grand_total']) ? $cart_d['grand_total'] : "",
							isset($cart_d['roundoff']) ? $cart_d['roundoff'] : "",
							isset($cart_d['total_taxamount']) ? $cart_d['total_taxamount'] : "",
							isset($cart_d['grand_total_rounded']) ? $cart_d['grand_total_rounded'] : "",
							isset($cart_d['taxable_amount']) ? $cart_d['taxable_amount'] : "",
							isset($cart_d['discount_type']) ? $cart_d['discount_type'] : "",
							isset($cart_d['gst']) ? $cart_d['gst'] : "",
							isset($cart_d['entry_flag']) ? $cart_d['entry_flag'] : "",
							isset($cart_d['terms_comdition']) ? $db->clean($cart_d['terms_comdition']) : "",
							isset($cart_d['faithfully']) ? $db->clean($cart_d['faithfully']) : "",
							isset($cart_d['transport_name']) ? $cart_d['transport_name'] : "",
							isset($cart_d['transport_through']) ? $cart_d['transport_through'] : "",
							isset($cart_d['shipping_address']) ? $db->clean($cart_d['shipping_address']) : "",
							isset($cart_d['billing_address']) ? $db->clean($cart_d['billing_address']) : "",
							isset($cart_d['transport_charge']) ? $cart_d['transport_charge'] : "",
							isset($cart_d['packing_charge']) ? $cart_d['packing_charge'] : "",
							isset($cart_d['remarks']) ? $db->clean($cart_d['remarks']) : "",

						);

						$order_id = $db->rp_insert("orders", $values, $rows, 0);

						if ($order_id != 0) {
							$cart_item_r = $db->rp_getData("cart_item", "*", "order_id='" . $cart_id . "' AND isDelete=0", "", 0);
							if ($cart_item_r) {
								while ($cart_item_d = mysqli_fetch_assoc($cart_item_r)) {
									//print_r($cart_item_d);exit;
									$rows 	= array(
										"order_id",
										"top_cat_id",
										"cat_id",
										"pro_id",
										"weight_id",
										"pro_name",
										"pro_description",
										"pro_qty",
										"remaining_qty",
										"inner_size",
										"outer_size",
										"box_qty",
										"cartoon_qty",
										"loose_qty",
										"unitprice",
										"original_price",
										"totalprice",
										"discount",
										"discount_amount",
										"item_gst",
										"item_gst_amount",
										"price_list_id",
										"price_list_price",
										"price_list_discounted_price",
										"price_list_discounted_amount",
										"price_list_discount_type",
										"price_list_discount",
										"cash_discount_amount",
										"additional_discount_amount",
										"igst_tax",
										"igst_amount",
										"taxable",
										"subtotal",
										"hsn_code",
										"other_charge",
										"fright_charge",
										"is_including",
										"item_order_unit",
										"order_qty",
										"order_item_brand_id",
									);
									$values = array(
										$order_id,
										isset($cart_item_d['top_cat_id']) ? $cart_item_d['top_cat_id'] : "",
										isset($cart_item_d['cat_id']) ? $cart_item_d['cat_id'] : "",
										isset($cart_item_d['pro_id']) ? $cart_item_d['pro_id'] : "",
										isset($cart_item_d['weight_id']) ? $cart_item_d['weight_id'] : "",
										isset($cart_item_d['pro_name']) ? $db->clean($cart_item_d['pro_name']) : "",
										isset($cart_item_d['pro_description']) ? $db->clean($cart_item_d['pro_description']) : "",
										isset($cart_item_d['pro_qty']) ? $cart_item_d['pro_qty'] : "",
										isset($cart_item_d['remaining_qty']) ? $cart_item_d['remaining_qty'] : "",
										isset($cart_item_d['inner_size']) ? $cart_item_d['inner_size'] : "",
										isset($cart_item_d['outer_size']) ? $cart_item_d['outer_size'] : "",
										isset($cart_item_d['box_qty']) ? $cart_item_d['box_qty'] : "",
										isset($cart_item_d['cartoon_qty']) ? $cart_item_d['cartoon_qty'] : "",
										isset($cart_item_d['loose']) ? $cart_item_d['loose'] : "",
										isset($cart_item_d['unitprice']) ? $cart_item_d['unitprice'] : "",
										isset($cart_item_d['original_price']) ? $cart_item_d['original_price'] : "",
										isset($cart_item_d['totalprice']) ? $cart_item_d['totalprice'] : "",
										isset($cart_item_d['discount']) ? $cart_item_d['discount'] : "",
										isset($cart_item_d['discount_amount']) ? $cart_item_d['discount_amount'] : "",
										isset($cart_item_d['item_gst']) ? $cart_item_d['item_gst'] : "",
										isset($cart_item_d['item_gst_amount']) ? $cart_item_d['item_gst_amount'] : "",
										isset($cart_item_d['price_list_id']) ? $cart_item_d['price_list_id'] : "",
										isset($cart_item_d['price_list_price']) ? $cart_item_d['price_list_price'] : "",
										isset($cart_item_d['price_list_discounted_price']) ? $cart_item_d['price_list_discounted_price'] : "",
										isset($cart_item_d['price_list_discounted_amount']) ? $cart_item_d['price_list_discounted_amount'] : "",
										isset($cart_item_d['price_list_discount_type']) ? $cart_item_d['price_list_discount_type'] : "",
										isset($cart_item_d['price_list_discount']) ? $cart_item_d['price_list_discount'] : "",
										isset($cart_item_d['cash_discount_amount']) ? $cart_item_d['cash_discount_amount'] : "",
										isset($cart_item_d['additional_discount_amount']) ? $cart_item_d['additional_discount_amount'] : "",
										isset($cart_item_d['igst_tax']) ? $cart_item_d['igst_tax'] : "",
										isset($cart_item_d['igst_amount']) ? $cart_item_d['igst_amount'] : "",
										isset($cart_item_d['taxable']) ? $cart_item_d['taxable'] : "",
										isset($cart_item_d['subtotal']) ? $cart_item_d['subtotal'] : "",
										isset($cart_item_d['hsn_code']) ? $cart_item_d['hsn_code'] : "",
										isset($cart_item_d['other_charge']) ? $cart_item_d['other_charge'] : "",
										isset($cart_item_d['fright_charge']) ? $cart_item_d['fright_charge'] : "",
										isset($cart_item_d['is_including']) ? $cart_item_d['is_including'] : "",
										isset($cart_item_d['item_order_unit']) ? $cart_item_d['item_order_unit'] : "",
										isset($cart_item_d['box_qty']) ? $cart_item_d['box_qty'] : "",
										isset($cart_item_d['order_item_brand_id']) ? $cart_item_d['order_item_brand_id'] : "",

									);
									$total_qty += $cart_item_d['pro_qty'];
									$sub_total += $cart_item_d['totalprice'];
									$item_id = $db->rp_insert("order_product_item", $values, $rows, 0);

									$objOrder->AddschemeItems($order_id, $item_id, $cart_item_d['pro_id'], $cart_item_d['weight_id'], $cart_item_d['pro_qty']);
								}
							}
							$total_items = $db->rp_getTotalRecord("order_product_item", "order_id='" . $order_id . "' AND isDelete=0");
							if ($total_items != 0) {
								$reply = array("ack" => 1, "developer_msg" => "Order Added Successfully", "ack_msg" => "Order Added  Successfully", "order_id" => $order_id);
							} else {
								$reply = array("ack" => 0, "developer_msg" => "Order Item Not inserted", "ack_msg" => "Order Item Not inserted");
							}
						} else {
							$reply = array("ack" => 0, "developer_msg" => "Order  Not inserted", "ack_msg" => "Order Not inserted3");
						}
					}
				} else {
					$reply = array("ack" => 0, "ack_msg" => "No Such a Item found!!");
				}
				echo json_encode($reply);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Cart Id Required", "developer_msg" => "Cart Id Required");
				$db->printJSON($ack);
			}
		} else if ($service == "quotation_to_order" || $service == 169) {
			$quotation_id = isset($_REQUEST['quotation_id']) ? $_REQUEST['quotation_id'] : "";
			if ($quotation_id) {
				$detail = array();
				$quotation_r = $db->rp_getData("quotation_detail", "*", "id='" . $quotation_id . "'", "", 0);
				if ($quotation_d = mysqli_fetch_assoc($quotation_r)) {
					if ($quotation_d['gst'] != "") {
						$order_no = $db->getLastInsertId("orders");
						$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
						$created_date	= date('Y-m-d H:i:s');
						$order_date	= isset($detail['order_date']) ? $detail['order_date'] : date("Y-m-d");
						$booking_place = $db->rp_getValue("executive", "booking_place", "id='" . $quotation_d['customer_id'] . "'", 0);
						$booking_pincode = $db->rp_getValue("executive", "zip", "id='" . $quotation_d['customer_id'] . "'", 0);

						$rows 	= array(
							"order_no",
							"order_date",
							"quotation_id",
							"customer_id",
							"dealer_id",
							"super_stockist_id",
							"customer_name",
							"client_code",
							"customer_flag",
							"company_name",
							"customer_type",
							"contact_number",
							"address",
							"city",
							"state",
							"country",
							"status",
							"sales_id",
							"sales_type",
							"class_id",
							"proforma_invoice_id",
							"class_name",
							"area_id",
							"area_name",
							"email",
							"item_total_price",
							"taxable",
							"discount",
							"discount_amount",
							"subtotal",
							"cash_discount",
							"cash_discount_amount",
							"additional_discount",
							"additional_discount_amount",
							"total_amount",
							"cgst_amount",
							"sgst_amount",
							"igst_amount",
							"grand_total",
							"roundoff",
							"total_taxamount",
							"grand_total_rounded",
							"taxable_amount",
							"discount_type",
							"gst",
							"entry_flag",
							"terms_comdition",
							"faithfully",
							"vendor_code",
							"tendor_code",
							"transport_name",
							"transport_through",
							"shipping_address",
							"billing_address",
							"transport_charge",
							"packing_charge",
							"tcs_per",
							"tcs_amount",
							"remarks",
							"type_of_company",
							"terms_condition_id",
							"booking_place",
							"booking_pincode",
						);
						$values = array(
							$order_no,
							$order_date,
							$quotation_id,
							isset($quotation_d['customer_id']) ? $quotation_d['customer_id'] : "",
							isset($quotation_d['dealer_id']) ? $quotation_d['dealer_id'] : "",
							isset($quotation_d['super_stockist_id']) ? $quotation_d['super_stockist_id'] : "",
							isset($quotation_d['customer_name']) ? $quotation_d['customer_name'] : "",
							isset($quotation_d['client_code']) ? $quotation_d['client_code'] : "",
							isset($quotation_d['customer_flag']) ? $quotation_d['customer_flag'] : "",
							isset($quotation_d['company_name']) ? $quotation_d['company_name'] : "",
							isset($quotation_d['customer_type']) ? $quotation_d['customer_type'] : "",
							isset($quotation_d['contact_number']) ? $quotation_d['contact_number'] : "",
							isset($quotation_d['address']) ? addslashes($quotation_d['address']) : "",
							isset($quotation_d['city']) ? $quotation_d['city'] : "",
							isset($quotation_d['state']) ? $quotation_d['state'] : "",
							isset($quotation_d['country']) ? $quotation_d['country'] : "",
							0,
							isset($quotation_d['sales_id']) ? $quotation_d['sales_id'] : "",
							isset($quotation_d['sales_type']) ? $quotation_d['sales_type'] : "",
							isset($quotation_d['class_id']) ? $quotation_d['class_id'] : "",
							isset($quotation_d['proforma_invoice_id']) ? $quotation_d['proforma_invoice_id'] : "",
							isset($quotation_d['class_name']) ? $quotation_d['class_name'] : "",
							isset($quotation_d['area_id']) ? $quotation_d['area_id'] : "",
							isset($quotation_d['area_name']) ? $quotation_d['area_name'] : "",
							isset($quotation_d['email']) ? $quotation_d['email'] : "",
							isset($quotation_d['item_total_price']) ? $quotation_d['item_total_price'] : "",
							isset($quotation_d['taxable']) ? $quotation_d['taxable'] : "",
							isset($quotation_d['discount']) ? $quotation_d['discount'] : "",
							isset($quotation_d['discount_amount']) ? $quotation_d['discount_amount'] : "",
							isset($quotation_d['subtotal']) ? $quotation_d['subtotal'] : "",
							isset($quotation_d['cash_discount']) ? $quotation_d['cash_discount'] : "",
							isset($quotation_d['cash_discount_amount']) ? $quotation_d['cash_discount_amount'] : "",
							isset($quotation_d['additional_discount']) ? $quotation_d['additional_discount'] : "",
							isset($quotation_d['additional_discount_amount']) ? $quotation_d['additional_discount_amount'] : "",
							isset($quotation_d['total_amount']) ? $quotation_d['total_amount'] : "",
							isset($quotation_d['cgst_amount']) ? $quotation_d['cgst_amount'] : "",
							isset($quotation_d['sgst_amount']) ? $quotation_d['sgst_amount'] : "",
							isset($quotation_d['igst_amount']) ? $quotation_d['igst_amount'] : "",
							isset($quotation_d['grand_total']) ? $quotation_d['grand_total'] : "",
							isset($quotation_d['roundoff']) ? $quotation_d['roundoff'] : "",
							isset($quotation_d['total_taxamount']) ? $quotation_d['total_taxamount'] : "",
							isset($quotation_d['grand_total_rounded']) ? $quotation_d['grand_total_rounded'] : "",
							isset($quotation_d['taxable_amount']) ? $quotation_d['taxable_amount'] : "",
							isset($quotation_d['discount_type']) ? $quotation_d['discount_type'] : "",
							isset($quotation_d['gst']) ? $quotation_d['gst'] : "",
							isset($quotation_d['entry_flag']) ? $quotation_d['entry_flag'] : "",
							isset($quotation_d['terms_comdition']) ? $db->clean($quotation_d['terms_comdition']) : "",
							isset($quotation_d['faithfully']) ? $db->clean($quotation_d['faithfully']) : "",
							isset($quotation_d['vendor_code']) ? $quotation_d['vendor_code'] : "",
							isset($quotation_d['tendor_code']) ? $quotation_d['tendor_code'] : "",
							isset($quotation_d['transport_name']) ? $quotation_d['transport_name'] : "",
							isset($quotation_d['transport_through']) ? $quotation_d['transport_through'] : "",
							isset($quotation_d['shipping_address']) ? $db->clean($quotation_d['shipping_address']) : "",
							isset($quotation_d['billing_address']) ? $db->clean($quotation_d['billing_address']) : "",
							isset($quotation_d['transport_charge']) ? $quotation_d['transport_charge'] : "",
							isset($quotation_d['packing_charge']) ? $quotation_d['packing_charge'] : "",
							isset($quotation_d['tcs_per']) ? $quotation_d['tcs_per'] : "",
							isset($quotation_d['tcs_amount']) ? $quotation_d['tcs_amount'] : "",
							isset($quotation_d['remarks']) ? $db->clean($quotation_d['remarks']) : "",
							isset($quotation_d['type_of_company']) ? $db->clean($quotation_d['type_of_company']) : "",
							isset($quotation_d['terms_condition_id']) ? $db->clean($quotation_d['terms_condition_id']) : "",
							$booking_place,
							$booking_pincode,
						);

						/*log entry*/
						$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $quotation_d['sales_id'] . "'", 0);
						$module_name = "Order";
						$flag = "Application";
						$log_description = $module_name . " " . $order_no . " Created By " . $sales_name . " ON " . date("Y-m-d H:i:s");
						/*log entry*/
						$order_id = $db->rp_insert("orders", $values, $rows, 0, $log_description, $flag, $module_name, $quotation_d['sales_id'], $quotation_d['customer_id']);

						if ($order_id != 0) {
							$cart_item_r = $db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quotation_id . "' AND isDelete=0", "", 0);
							if ($cart_item_r) {
								while ($cart_item_d = mysqli_fetch_assoc($cart_item_r)) {
									//print_r($cart_item_d);exit;
									$rows 	= array(
										"order_id",
										"top_cat_id",
										"cat_id",
										"pro_id",
										"weight_id",
										"pro_name",
										"pro_description",
										"pro_qty",
										"remaining_qty",
										"inner_size",
										"outer_size",
										"box_qty",
										"cartoon_qty",
										"loose_qty",
										"unitprice",
										"original_price",
										"totalprice",
										"discount",
										"discount_amount",
										"item_gst",
										"item_gst_amount",
										"price_list_id",
										"price_list_price",
										"price_list_discounted_price",
										"price_list_discounted_amount",
										"price_list_discount_type",
										"price_list_discount",
										"cash_discount_amount",
										"additional_discount_amount",
										"igst_tax",
										"igst_amount",
										"taxable",
										"subtotal",
										"hsn_code",
										"other_charge",
										"fright_charge",
										"is_including",
										"item_order_unit",
										"order_item_brand_id"
									);
									$values = array(
										$order_id,
										isset($cart_item_d['top_cat_id']) ? $cart_item_d['top_cat_id'] : "",
										isset($cart_item_d['cat_id']) ? $cart_item_d['cat_id'] : "",
										isset($cart_item_d['pro_id']) ? $cart_item_d['pro_id'] : "",
										isset($cart_item_d['weight_id']) ? $cart_item_d['weight_id'] : "",
										isset($cart_item_d['pro_name']) ? $db->clean($cart_item_d['pro_name']) : "",
										isset($cart_item_d['pro_description']) ? $db->clean($cart_item_d['pro_description']) : "",
										isset($cart_item_d['pro_qty']) ? $cart_item_d['pro_qty'] : "",
										isset($cart_item_d['remaining_qty']) ? $cart_item_d['remaining_qty'] : "",
										isset($cart_item_d['inner_size']) ? $cart_item_d['inner_size'] : "",
										isset($cart_item_d['outer_size']) ? $cart_item_d['outer_size'] : "",
										isset($cart_item_d['box_qty']) ? $cart_item_d['box_qty'] : "",
										isset($cart_item_d['cartoon_qty']) ? $cart_item_d['cartoon_qty'] : "",
										isset($cart_item_d['loose']) ? $cart_item_d['loose'] : "",
										isset($cart_item_d['unitprice']) ? $cart_item_d['unitprice'] : "",
										isset($cart_item_d['original_price']) ? $cart_item_d['original_price'] : "",
										isset($cart_item_d['totalprice']) ? $cart_item_d['totalprice'] : "",
										isset($cart_item_d['discount']) ? $cart_item_d['discount'] : "",
										isset($cart_item_d['discount_amount']) ? $cart_item_d['discount_amount'] : "",
										isset($cart_item_d['item_gst']) ? $cart_item_d['item_gst'] : "",
										isset($cart_item_d['item_gst_amount']) ? $cart_item_d['item_gst_amount'] : "",
										isset($cart_item_d['price_list_id']) ? $cart_item_d['price_list_id'] : "",
										isset($cart_item_d['price_list_price']) ? $cart_item_d['price_list_price'] : "",
										isset($cart_item_d['price_list_discounted_price']) ? $cart_item_d['price_list_discounted_price'] : "",
										isset($cart_item_d['price_list_discounted_amount']) ? $cart_item_d['price_list_discounted_amount'] : "",
										isset($cart_item_d['price_list_discount_type']) ? $cart_item_d['price_list_discount_type'] : "",
										isset($cart_item_d['price_list_discount']) ? $cart_item_d['price_list_discount'] : "",
										isset($cart_item_d['cash_discount_amount']) ? $cart_item_d['cash_discount_amount'] : "",
										isset($cart_item_d['additional_discount_amount']) ? $cart_item_d['additional_discount_amount'] : "",
										isset($cart_item_d['igst_tax']) ? $cart_item_d['igst_tax'] : "",
										isset($cart_item_d['igst_amount']) ? $cart_item_d['igst_amount'] : "",
										isset($cart_item_d['taxable']) ? $cart_item_d['taxable'] : "",
										isset($cart_item_d['subtotal']) ? $cart_item_d['subtotal'] : "",
										isset($cart_item_d['hsn_code']) ? $cart_item_d['hsn_code'] : "",
										isset($cart_item_d['other_charge']) ? $cart_item_d['other_charge'] : "",
										isset($cart_item_d['fright_charge']) ? $cart_item_d['fright_charge'] : "",
										isset($cart_item_d['is_including']) ? $cart_item_d['is_including'] : "",
										isset($cart_item_d['item_order_unit']) ? $cart_item_d['item_order_unit'] : "",
										isset($cart_item_d['order_item_brand_id']) ? $cart_item_d['order_item_brand_id'] : "",
									);
									$total_qty += $cart_item_d['pro_qty'];
									$sub_total += $cart_item_d['totalprice'];
									$item_id = $db->rp_insert("order_product_item", $values, $rows, 0);
									$objOrder->AddschemeItems($order_id, $item_id, $cart_item_d['pro_id'], $cart_item_d['weight_id'], $cart_item_d['pro_qty']);
								}
							}
							$total_items = $db->rp_getTotalRecord("order_product_item", "order_id='" . $order_id . "' AND isDelete=0");
							$update_quo = $db->rp_update("quotation_detail", array("status" => 4), "id='" . $quotation_id . "'");
							if ($total_items != 0 && $update_quo) {
								$update_executive = $db->rp_update("executive", array("customer_flag" => 0, "gst" => $quotation_d['gst']), "id='" . $quotation_d['customer_id'] . "'");

								$order_no  = $db->rp_getValue("orders", "order_no", "id='" . $order_id . "'");
								$notification_description = "Added Order for Order No " . $order_no;

								// send sales executive notification added by shivani     
								$sales_id = $db->rp_getValue("orders", "sales_id", "id='" . $order_id . "'");
								$objPushNotification->commonNotification($sales_id, $order_id, "orders", "Add Order", $notification_description, "sales_executive", "orders");
								// send sales executive notification added by shivani 

								// send customer notification added by shivani
								$customer_id = $db->rp_getValue("orders", "customer_id", "id='" . $order_id . "'");
								$objPushNotification->commonNotification($customer_id, $order_id, "orders", "Add Order", $notification_description, "customer", "orders");
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
								$objPushNotification->commonNotification($upper_chanel_id, $order_id, "orders", "Add Order", $notification_description, "customer", "orders");
								// send customer upper chanel notification added by shivani

								$reply = array("ack" => 1, "developer_msg" => "Order Added Successfully", "ack_msg" => "Order Added  Successfully", "order_id" => $order_id);
							} else {
								$reply = array("ack" => 0, "developer_msg" => "Order Item Not inserted", "ack_msg" => "Order Item Not inserted");
							}
						} else {
							$reply = array("ack" => 0, "developer_msg" => "Order Not inserted", "ack_msg" => "Order Not inserted1");
						}
					} else {
						$reply = array("ack" => 0, "developer_msg" => "GST Value blank in quotation so first please update it in quotation!!", "ack_msg" => "GST Value blank in quotation so first please update it in quotation!!");
					}
				} else {
					$reply = array("ack" => 0, "ack_msg" => "No Such a Item found!!");
				}
				echo json_encode($reply);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Cart Id Required", "developer_msg" => "Cart Id Required");
				$db->printJSON($ack);
			}
		} else if ($service == 'generate_prospact_inquiry' || $service == 174) {
			$IsInquiry = $db->rp_getTotalRecord("no_order_inquiry", "id='" . $_REQUEST['inquiry_id'] . "' AND inquiry_lead_flag='-1' AND isDelete=0", 0);
			if ($IsInquiry > 0) {
				//echo $IsInquiry;exit;

				$module_name = "Raw Data";
				$flag = "Application";
				$log_description = $module_name . " #INQ/" . $_REQUEST['inquiry_id'] . " Convert To Lead By " . $sales_name . " ON " . date("Y-m-d H:i:s");

				$entry_flag = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "5";
				$update_data = array("inquiry_lead_flag" => 0, "inquiry_type" => 0, "inquiry_date" => date('Y-m-d'), "entry_flag" => $entry_flag, "inq_status" => 2);
				$update = $db->rp_update("no_order_inquiry", $update_data, "id='" . $_REQUEST['inquiry_id'] . "'", 0, $log_description, $flag, $module_name, $_REQUEST['sales_id']);

				if ($update) {
					$ack = array("ack" => 1, "ack_msg" => "Raw Data Convert To Inquiry Successfully");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Something Went Wrong.Please Check");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "There Is No Such Raw Data Found.Please Check And Try Again.");
			}

			$db->printJSON($ack);
		}
	} else {
		$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Check your API Key or contact Admin", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
		$db->printJSON($ack);
	}
} else {
	$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Check your API Key or contact Admin", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
	$db->printJSON($ack);
}

$db->disconnect();
