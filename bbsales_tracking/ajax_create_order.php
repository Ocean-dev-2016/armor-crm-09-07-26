<?php
//$page_id = 566;$page_slug = 'page_quotation_ajax';
$page_id = 607;$page_slug = 'quotation';
include("connect.php");
include("../include/orders.class.php");
$objOrder = new Order();
//print_r($_REQUEST);exit;

$qid = $_REQUEST['qid'];
$ctable 	= "quotation_detail";

$quo_r = $db->rp_getData($ctable, "*", "id='" . $qid . "' AND isDelete=0", "",0);
if ($quo_d = mysqli_fetch_assoc($quo_r)) {
	// Array ( [id] => 6 [inquiry_id] => 7 [quotation_no] => QUOT/DD/06 [customer_id] => 3 [company_name] => RAJ COOLING SYSTEM [dealer_id] => 2 [super_stockist_id] => 1 [sales_id] => 1 [sales_type] => [class_id] => 0 [proforma_invoice_id] => 0 [class_name] => [area_id] => 0 [area_name] => [customer_name] => MUKESHBHAI [customer_type] => 3 [contact_number] => 9924717310 [address] => RAJ COOLING SYSTEM, Ahmedabad, Gujarat, india [city] => Ahmedabad [state] => GUJARAT [country] => India [email] => [quotation_date] => 2020-09-23 [dispatch_date] => [revised_date] => [status] => 0 [adate] => 0000-00-00 00:00:00 [isDelete] => 0 [isActive] => 1 [remarks] => chick [reason_of_cancel_order] => [created_by] => 1 [created_by_type] => 0 [modified_by] => 1 [modified_by_type] => 0 [created_date] => 2020-09-23 15:44:30 [modified_date] => 2020-09-23 15:44:30 [item_total_price] => 0 [total_qty] => 1500 [taxable] => 0 [discount] => 0 [discount_amount] => 0 [subtotal] => 24375000 [cash_discount] => 0 [cash_discount_amount] => 0 [total_amount] => 0 [cgst_amount] => 0 [sgst_amount] => 0 [igst_amount] => 4387500 [grand_total] => 28762500 [roundoff] => 0 [total_taxamount] => 0 [grand_total_rounded] => 0 [local_id] => 0 [taxable_amount] => 0 [discount_type] => 0 [modify_date] => 0000-00-00 00:00:00 [gst] => [booking] => [transport] => [entry_flag] => 1 ) 
	if($quo_d['gst']!="")
	{
		$faithfully = $db->custom_html_entity_decode2($_SESSION[SITE_SESS.'SESS_NAME']."<br/>".$db->rp_getValue("sales_executive","phone","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'"));

		$detail['cid']		= $quo_d['customer_id'];
		$detail['customer_id']		= $quo_d['customer_id'];
		// $detail['sales_executive_id'] = $quo_d['sales_id'];
		$detail['sales_executive_id'] = ($quo_d['sales_id'] != "" && $quo_d['sales_id'] != NULL && $quo_d['sales_id'] != null && $quo_d['sales_id'] != 0 ) ? $quo_d['sales_id'] : $_SESSION[SITE_SESS.'REFERANCE_ID'];
		/*$detail['order_date']		= $quo_d['quotation_date'];*/
		$detail['order_date']		= date('Y-m-d');
		$order_no = $db->getLastInsertId("orders");
		$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
		$detail['order_no']	= $order_no;
		$detail['chalan_no'] = "";
		$detail['po_no'] = "";
		$detail['po_date'] = date('Y-m-d');
		$detail['terms_comdition']	= $db->clean($quo_d['terms_comdition']);
		// $detail['faithfully']		= $db->clean($quo_d['faithfully']);
		$detail['faithfully']		= $db->clean($faithfully);
		$detail['transport_name']	= $quo_d['transport_name'];
		$detail['transport_through'] = $quo_d['transport_through'];
		$detail['transport_charge']	 = $quo_d['transport_charge'];
		$detail['packing_charge']	 = $quo_d['packing_charge'];
		$detail['shipping_address']	 = $quo_d['shipping_address'];
		$detail['billing_address']	 = $quo_d['billing_address'];
		$detail['name_gstin']		 = $quo_d['gst'];
		$detail['vendor_code']	     = $quo_d['vendor_code'];
		$detail['tendor_code']	     = $quo_d['tendor_code'];
		$detail['igst_amount']	     = $quo_d['igst_amount'];
		$detail['cash_discount']	 = $quo_d['cash_discount'];
		$detail['cash_discount_amount']	 = $quo_d['cash_discount_amount'];
		$detail['additional_discount']	 = $quo_d['additional_discount'];
		$detail['additional_discount_amount']	     = $quo_d['additional_discount_amount'];
		$detail['tcs_amount']	 		= $quo_d['tcs_amount'];
		$detail['transport_charge_gst']	= isset($quo_d['transport_charge_gst'])?$db->clean($quo_d['transport_charge_gst']):"";
		$detail['packing_charge_gst']	= isset($quo_d['packing_charge_gst'])?$db->clean($quo_d['packing_charge_gst']):"";
		$detail['cd_gst']	= isset($quo_d['cd_gst'])?$db->clean($quo_d['cd_gst']):"";
		$detail['ad_gst']	= isset($quo_d['ad_gst'])?$db->clean($quo_d['ad_gst']):"";
		$detail['round_off']	= isset($quo_d['round_off'])?$db->clean($quo_d['round_off']):"";
		$detail['type_of_company']		= $quo_d['type_of_company'];
		$detail['terms_condition_id']	= $quo_d['terms_condition_id'];
		$booking_place=$db->rp_getValue("executive","booking_place","id='".$quo_d['customer_id']."'",0);
		$booking_pincode=$db->rp_getValue("executive","zip","id='".$quo_d['customer_id']."'",0);
		$detail['booking_place']		= $db->clean($booking_place);
		$detail['booking_pincode']		= $db->clean($booking_pincode);
		//$detail['type_of_company']	= isset($quo_d['type_of_company'])?$db->clean($quo_d['type_of_company']):"";

		$detail['apply_scheme']	= 2;

		// print_r($detail);exit;
		$item_r = $db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quo_d['id'] . "' AND isDelete=0","",0);
		if ($item_r) {
			$qty = array();
			$product_id = array();
			$price = array();
			$pro_name = array();
			$weight_id = array();
			$cartoon_qty = array();
			$box_qty = array();
			$original_price = array();
			$is_including = array();

			while ($item_d = mysqli_fetch_assoc($item_r)) {
				$qty[] = $item_d['remaining_qty'];
				$product_id[] = $item_d['pro_id'];
				$price[] = $item_d['unitprice'];
				$original_price[] = $item_d['original_price'];
				$pro_name[] = $item_d['pro_name'];
				$weight_id[] = $item_d['weight_id'];
				$cartoon_qty[] = $item_d['cartoon_qty'];
				$box_qty[] = $item_d['box_qty'];
				$pro_description[] = $item_d['pro_description'];
				$cd_discount[] = $item_d['cash_discount_amount'];
				$ad_discount[] = $item_d['additional_discount_amount'];
				$gst_amount_item[] = $item_d['igst_amount'];
				$taxable_amount[] = $item_d['taxable'];
				$sub_total[] = $item_d['subtotal'];
				$other_charge[] = $item_d['other_charge'];
				$fright_charge[] = $item_d['fright_charge'];
				$discount_amount[] = $item_d['discount_amount'];
				$discount[] = $item_d['discount'];
				$brand_id[] = 0;
				$is_including[] = $item_d['is_including'];
				$item_order_unit[]	 = $item_d['item_order_unit'];
				$order_qty[] = $item_d['order_qty'];
				$order_item_brand_id[] = $item_d['order_item_brand_id'];
			}
			$size[] = sizeof($product_id);
			$value_check = sizeof($product_id);
			if (in_array($value_check, $size)) {
				$isValidArray = true;
			} else {
				$isValidArray = false;
			}
			if ($isValidArray && !empty($product_id)) {
				for ($i = 0; $i < sizeof($qty); $i++) {
					$item[] = array("qty" => $qty[$i], "pid" => $product_id[$i], "price" => $price[$i], "original_price" => $original_price[$i], "pro_name" => $pro_name[$i], "weight_id" => $weight_id[$i], "cartoon_qty" => $cartoon_qty[$i], "box_qty" => $box_qty[$i],"brand_id"=>$brand_id[$i],"pro_description"=>$pro_description[$i],"cd_discount"=>$cd_discount[$i],"ad_discount"=>$ad_discount[$i],"gst_amount_item"=>$gst_amount_item[$i],"taxable_amount"=>$taxable_amount[$i],"sub_total"=>$sub_total[$i],"other_charge"=>$other_charge[$i],"fright_charge"=>$fright_charge[$i],"discount_amount"=>$discount_amount[$i],"discount"=>$discount[$i],"is_including"=>$is_including[$i],"item_order_unit"=>$item_order_unit[$i],"order_qty"=>$order_qty[$i],"order_item_brand_id"=>$order_item_brand_id[$i]);
				}
			}

			// echo "<pre>"; print_r($item);exit;
			$reply = $objOrder->AddToCart($detail, $item);
			if ($reply['ack'] == 1) {

				unset($detail['order_date']);
				$detail['cash_discount_flag'] = 0;
				$detail['order_id'] = $reply['order_id'];
				// $detail['sales_executive_id'] = $quo_d['sales_id'];
				$detail['sales_executive_id'] = ($quo_d['sales_id'] != "" && $quo_d['sales_id'] != NULL && $quo_d['sales_id'] != null && $quo_d['sales_id'] != 0 ) ? $quo_d['sales_id'] : $_SESSION[SITE_SESS.'REFERANCE_ID'];
				$detail['sales_executive_id']=($detail['sales_executive_id'])?$detail['sales_executive_id']:0;
				$detail['remarks'] = $quo_d['remarks'];

				if($detail['igst_amount']!=0)
				{
					$detail['gst_apply_flag'] = 1;
				}
				else
				{
					$detail['gst_apply_flag'] = 0;	
				}

				if($detail['tcs_amount']!=0)
				{
					$detail['tcs_apply_flag'] = 1;
				}
				else
				{
					$detail['tcs_apply_flag'] = 0;	
				}

				$checkorder_update = $objOrder->PlaceOrderPanel($detail);
				//print_r($checkorder_update);exit;
				if ($checkorder_update['ack'] == 1) {
					// update quotation id in order
					$update_order = $db->rp_update("orders", array("quotation_id" => $qid), "id='" . $reply['order_id'] . "'",0);

					/*update Customer flag*/
					$count = $db->rp_getTotalRecord("orders","customer_id='".$detail['customer_id']."' AND isDelete=0",0);
					if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
						$detail['sales_executive_id']="0";
					}

					if($count==1)
					{
						$update_order = $db->rp_update("executive", array("customer_flag" => 0,"seid"=>$detail['sales_executive_id']), "id='" . $detail['customer_id'] . "'",0);
					}
					/*update Customer flag*/
					$update_quo = $db->rp_update($ctable, array("status" => 4), "id='" . $qid . "'");
					$reply1 = array("ack" => 1, "ack_msg" => "Order Generated Successfully", "order_id" => $reply['order_id'], "c_type" => $reply['c_type']);
				} else {
					$reply1 = array("ack"=>0,"ack_msg"=>$checkorder_update['ack_msg']);
				}
			} else {
				$reply1 = array("ack"=>0,"ack_msg"=>$reply['ack_msg']);
			}
		} else {
			$reply1 = array("ack"=>0,"ack_msg"=>"No Such a Item found!!");
		}
	} else {
		$reply1 = array("ack"=>0,"ack_msg"=>"GST Value blank in quotation so first please update it in quotation!!");
	}
	echo json_encode($reply1);
}
?>
<?php require_once 'disconnect.php';  ?>
