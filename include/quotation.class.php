<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
require_once("class.system.php");
require_once("product.class.php");
require_once("push_notification.class.php");


class Quotation extends Functions
{
	public $db, $log, $product;
	public $ctable = "quotation_detail";
	public $ctablePerforma = "proforma_invoice_info";
	public $ctablePerformaItems = "proforma_invoice_item";
	public $ctableRequest = "customer_order_request_info";
	public $ctableRequestItems = "customer_order_request_item";
	public $RequestStatus = array("Pending", "Completed");
	public $PerformaStatus = array("Pending", "Accepted", "Rejected");

	function __construct($id = "")
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db = $db;
		$this->log = new Log();
		$this->product = new Product();
		$this->system = new System();

		$this->objPushNotification = new PushNotification();
	}
	public function InsertOrder($detail, $item)
	{
		$total_qty = 0;
		$grand_total = 0;
		$subtotal = 0;
		$total = 0;
		$sum = 0;
		$qty = 0;
		$discount_type = $detail['discount_type'];
		extract($detail);
		$cid = $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $cid . "'");
		$customer_r = mysqli_fetch_assoc($customer_d);


		//----Insert Data In Orders Table ----------------//				
		$adate	= date('Y-m-d');
		$modify_date	= date('Y-m-d H:i:s');
		$rows 	= array(
			"quotation_no",
			"customer_id",
			"dealer_id",
			"customer_name",
			"company_name",
			"customer_type",
			"contact_number",
			"address",
			"city",
			"state",
			"country",
			"email",
			"quotation_date",
			"modify_date"
		);
		$values = array(
			$quotation_no,
			$cid,
			$dealer_id,
			$customer_r['cname'],
			$customer_r['company_name'],
			$customer_r['type_of_executive'],
			$customer_r['phone'],
			$customer_r['address'],
			$customer_r['city'],
			$customer_r['state'],
			$customer_r['country'],
			$customer_r['email'],
			$quotation_date,
			$modify_date
		);

		$quotation_id = $this->db->rp_insert($this->ctable, $values, $rows, 1);
		if ($quotation_id != 0) {
			// ---------Insert Order Product Item------------------------------------//

			if (!empty($item)) {
				$total_final = 0;
				$total_qty = 0;
				for ($i = 0; $i < sizeof($item); $i++) {
					$current_item = $item[$i];

					$total = $current_item['price'] * $current_item['qty'];
					$price = $current_item['price'];
					$adate	= date('Y-m-d H:i:s');
					$rows 	= array(
						"quotation_id",
						"pro_id",
						"weight_id",
						"pro_name",
						"remaining_qty",
						"pro_qty",
						"unitprice",
						"totalprice",
						"adate",
						"isDelete",
						"modify_date"
					);
					$values = array(
						$quotation_id,
						$current_item['product_id'],
						$current_item['weight_id'],
						$current_item['pro_name'],
						$current_item['qty'],
						$current_item['qty'],
						$price,
						$total,
						$adate,
						$isDelete,
						$modify_date
					);
					$total_final += $total;
					$total_qty += $current_item['qty'];
					$item_id = $this->db->rp_insert("quotation_product_item", $values, $rows, 0);
				}
				// Final total calculations (amount,qty update in main orders table after inserting product item)
				$total = $total_final;

				$rows 	= array(
					"total_qty"				=> $total_qty,
					"total_amount"          => $total,
					"grand_total"			=> $total,
				);
				$where	= "id='" . $quotation_id . "'";
				$order = $this->db->rp_update($this->ctable, $rows, $where, 0);

				$reply = array("ack" => 1, "developer_msg" => "Product Item Quotation Added.", "ack_msg" => "Success! Product Item Order Successfully.", "type" => $quotation_id);
				return $reply;
			}
			$reply = array("ack" => 1, "developer_msg" => "Product Item Quotation Added.", "ack_msg" => "Success! Product Item Order Successfully.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Product Item added Failed.");
			return $reply;
		}
	}

	//------------Update Order Product Item----------------------//		 
	public function UpdateProductItem($detail, $item)
	{
		$total_qty = 0;
		$grand_total = 0;
		$sum = 0;
		$quotation_id = $_REQUEST['id'];
		$error = array();
		$isError = false;
		$modify_date	= date('Y-m-d H:i:s');
		extract($detail);
		for ($i = 0; $i < sizeof($item); $i++) {
			$current_item = $item[$i];
			$pro_id     = $current_item['product_id'];
			$new_order_qty 		=  $current_item['qty'];
			$ordered_item_info = $this->db->rp_getData("quotation_product_item", "*", "pro_id='" . $current_item['product_id'] . "' AND weight_id='" . $current_item['weight_id'] . "' AND quotation_id='" . $quotation_id . "'", "", 0);
			if ($ordered_item_info) {
				//get dispatch qty & remaining qty
				$ordered_item_info = mysqli_fetch_assoc($ordered_item_info);
				$product_name = $ordered_item_info['pro_name'];
				$dispatched_qty = $ordered_item_info['dispatched_qty'];
				$remaining_qty = $ordered_item_info['remaining_qty'];
				$ordered_qty = $ordered_item_info['pro_qty'];
				//check new order qty < old order qty
				if ($new_order_qty < $ordered_qty) {
					//check new order qty < dispatched qty
					if ($new_order_qty < $dispatched_qty) {
						$isError = true;
						// ERROR YOU CAN NOT ENTER NEW ORDER QTY MORE THEN IT DISPATCHED
						$error[] = array("error_target_id" => $pro_id, "error" => $product_name . " has dispatched qty more than your edited qty");
						$error_html[] = $product_name . " has dispatched qty more than your edited qty";
					}
				}
			}
		}

		//update if no error found(first delete all old items and insert new item)
		if (!$isError) {
			$this->db->rp_delete("quotation_product_item", "quotation_id='" . $_REQUEST['id'] . "'", 0);
			for ($i = 0; $i < sizeof($item); $i++) {
				$current_item = $item[$i];
				$total = $current_item['price'] * $current_item['qty'];
				$adate	= date('Y-m-d H:i:s');
				$price = $current_item['price'];
				$where = "pid='" . $current_item['product_id'] . "' AND weight_id='" . $current_item['weight_id'] . "' AND quotation_id='" . $quotation_id . "' AND isDelete=0 GROUP BY pid";
				$dispatch_r = $this->db->rp_getData("dispatch_map_order", "SUM(qty) as dispatched_qty,pid", $where, "pid ASC ", 0);
				if ($dispatch_r) {
					$dispatch_d = mysqli_fetch_assoc($dispatch_r);
				} else {
					$dispatch_d['dispatched_qty'] = 0;
				}
				$remaining_qty = $current_item['qty'] - $dispatch_d['dispatched_qty'];
				$rows 	= array(
					"quotation_id",
					"pro_id",
					"weight_id",
					"pro_name",
					"pro_qty",
					"dispatched_qty",
					"remaining_qty",
					"unitprice",
					"totalprice",
					"adate",
					"isDelete",
					"modify_date"
				);
				$values = array(
					$quotation_id,
					$current_item['product_id'],
					$current_item['weight_id'],
					$current_item['pro_name'],
					$current_item['qty'],
					$dispatch_d['dispatched_qty'],
					$remaining_qty,
					$price,
					$total,
					$adate,
					$isDelete,
					$modify_date
				);
				$total_final += $total;
				$item_id = $this->db->rp_insert("quotation_product_item", $values, $rows, 0);

				$total_qty += $current_item['qty'];	//}	
			}
			// Final total calculations

			$total = $total_final;

			$rows 	= array(
				"total_qty"				=> $total_qty,
				"total_amount"          => $total,
				"grand_total"			=> $total,
				"modify_date"			=> $modify_date,
			);
			$where	= "id='" . $quotation_id . "'";
			$orderUpdated = $this->db->rp_update($this->ctable, $rows, $where, 0);
			if ($orderUpdated) {
				$reply = array("ack" => 1, "developer_msg" => "Product Item Order Updated.", "ack_msg" => "Success! Product Item Order Update Successfully.", "type" => $quotation_id);
				return $reply;
			} else {

				$reply = array("ack" => 0, "developer_msg" => "Product Item Order Updated Failed.", "ack_msg" => "failed! Product Item Order Updated Failed!!");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Error While updating order due to dispatch qty ", "ack_msg" => "" . implode("<br>", $error_html));
			return $reply;
		}
	}



	//-------------Get order Detail----------------//		
	public function GetInquiry($detail)
	{

		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData("no_order_inquiry", "*", $where, "", 0);
		$ctable_d = mysqli_fetch_array($ctable_r);

		$result = array();
		$result['customer_type']		= htmlentities($ctable_d['executive_type']);;
		$result['dealer_id']			= htmlentities($ctable_d['dealer_id']);
		$result['sales_executive_id']	= htmlentities($ctable_d['sales_executive_id']);
		$result['quotation_date']		= date("d-m-Y", strtotime($ctable_d['inquiry_date']));
		$result['remarks']		        = htmlentities($ctable_d['description']);
		$result['shipping_address']		= htmlentities($ctable_d['shipping_address']);
		$result['billing_address']		= htmlentities($ctable_d['billing_address']);
		$result['type_of_company']		= htmlentities($ctable_d['type_of_company']);
		$result['terms_condition_id']		= htmlentities($ctable_d['terms_condition_id']);

		$result1 = array();
		$result1['product_id']		= $ctable_d['product_id'];
		$result1['product_name']	= $this->db->rp_getValue("product", "name", "id='" . $ctable_d['product_id'] . "' AND isDelete=0");
		$result1['qty']		    	= $ctable_d['quantity'];
		$result1['item_remark']		= $ctable_d['remark'];

		$customer_id = $this->db->rp_getValue("no_order_inquiry", "dealer_id", "id='" . $detail['id'] . "' AND isDelete=0", 0);

		$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $customer_id . "' AND isDelete=0", 0);


		/*$result1['original_price']	= $this->rp_getValue("product_weight_price","price","product_id='".$ctable_d['product_id']."' AND weight_id='".$ctable_d['weight_id']."' AND isDelete=0");*/

		$result1['original_price']	= 100;
		if ($price_list_id != 0) {
			$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $ctable_d['product_id'] . "' AND weight_id='" . $ctable_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
			if ($check_product_in_list > 0) {
				$result1['original_price'] = $this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $ctable_d['product_id'] . "' AND weight_id='" . $ctable_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
			}
		}
		// Purchase Item

		$reply = array("ack" => 1, "developer_msg" => "Product Item detail fetched!!.", "ack_msg" => "Success! Product Item Edit Successfully.", "result" => $result, "item" => $result1);
		return $reply;
	}

	public function GetInquiryItem($detail)
	{

		$result = array();
		$where = " inquiry_id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData("no_order_inquiry_item", "*", $where, "", 0);
		while ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
			// print_r($ctable_d);
			$ctable_d['cid']				= $this->db->rp_getValue("product", "cid", "id='" . $ctable_d['pro_id'] . "'");
			$ctable_d['tcid']				= $this->db->rp_getValue("product", "tcid", "id='" . $ctable_d['pro_id'] . "'");
			$ctable_d['product_id']			= $ctable_d['pro_id'];
			$ctable_d['product_name']		= $ctable_d['pro_name'];
			$ctable_d['qty']		    	= $ctable_d['pro_qty'];
			$ctable_d['pro_description']	= $ctable_d['item_remark'];
			$ctable_d['discount_per']		= 0;
			$ctable_d['cat_no']				= $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . $ctable_d['pro_id'] . "'");
			$ctable_d['stock']				= $this->db->rp_getValue("product_weight_price", "stock_qty", "product_id='" . $ctable_d['pro_id'] . "'", 0);
			$ctable_d['hsn_code']			= $this->db->rp_getValue("product", "hsn_code", "id='" . $ctable_d['pro_id'] . "'");

			$ctable_d['inner_size']				= $this->db->rp_getValue("product_weight_price", "inner_size", "product_id='" . $ctable_d['pro_id'] . "'", 0);
			$ctable_d['outer_size']				= $this->db->rp_getValue("product_weight_price", "outer_size", "product_id='" . $ctable_d['pro_id'] . "'", 0);

			$customer_id = $this->db->rp_getValue("no_order_inquiry", "dealer_id", "id='" . $detail['id'] . "' AND isDelete=0", 0);

			$ctable_d['top_cat_name'] = $this->db->rp_getValue("top_category_master", "name", "id='" . $ctable_d['tcid'] . "' AND isDelete=0", 0);
			$ctable_d['category_name'] = $this->db->rp_getValue("category_master", "name", "id='" . $ctable_d['cid'] . "' AND isDelete=0", 0);

			$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $customer_id . "' AND isDelete=0", 0);

			$ctable_d['original_price']	= $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $ctable_d['pro_id'] . "' AND weight_id='" . $ctable_d['weight_id'] . "' AND isDelete=0", 0);

			if ($price_list_id != 0) {
				$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $ctable_d['pro_id'] . "' AND weight_id='" . $ctable_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
				if ($check_product_in_list > 0) {
					$ctable_d['original_price'] = $this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $ctable_d['pro_id'] . "' AND weight_id='" . $ctable_d['weight_id'] . "' AND isDelete=0", 0);
				}
			}

			$ctable_d['gst'] = $this->rp_getValue("product", "igst", "id='" . $ctable_d['pro_id'] . "' AND isDelete=0", 0);
			$ctable_d['gst_amount_item'] = $ctable_d['original_price'] * $ctable_d['gst'] / 100;

			$ctable_d['quotation_date']	= date("d-m-Y", strtotime($ctable_d['inquiry_date']));
			$ctable_d['product_total']	= $ctable_d['original_price'] * $ctable_d['qty'];
			$ctable_d['product_price']	= $ctable_d['original_price'];
			$result[] = $ctable_d;
		}

		$reply = array("ack" => 1, "developer_msg" => "Product Item detail fetched!!.", "ack_msg" => "Success! Product Item Edit Successfully.", "result" => $result);
		return $reply;
	}



	public function GetCustomer($detail)
	{

		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData("executive", "*", $where, "", 0);
		$ctable_d = mysqli_fetch_array($ctable_r);

		$result = array();
		$result['customer_type']		= htmlentities($ctable_d['type_of_executive']);
		$result['dealer_id']			= htmlentities($ctable_d['id']);
		//   echo $result['dealer_id'];exit;
		$result['sales_executive_id']	= htmlentities($ctable_d['seid']);
		$result['quotation_date']		= date("d-m-Y");


		$shipping_address = $this->db->rp_getValue("customer_vs_shipping_address", "shipping_address", "customer_id='" . $detail['id'] . "' AND isDelete=0 limit 1", 0);
		//  	$result['remarks']		        = htmlentities($ctable_d['description']);
		// $result['shipping_address']		= htmlentities($ctable_d['shipping_address']);

		$result['shipping_address']		= htmlentities($shipping_address);
		$result['billing_address']		= htmlentities($ctable_d['billing_address']);

		/*	$result1 = array();
	   	$result1['product_id']		= $ctable_d['product_id'];
	   	$result1['product_name']	= $this->db->rp_getValue("product","name","id='".$ctable_d['product_id']."' AND isDelete=0");
    	$result1['qty']		    	= $ctable_d['quantity'];
    	$result1['item_remark']		= $ctable_d['remark'];
*/
		$customer_id = $this->db->rp_getValue("executive", "id", "id='" . $detail['id'] . "' AND isDelete=0", 0);

		$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $customer_id . "' AND isDelete=0", 0);


		/*$result1['original_price']	= $this->rp_getValue("product_weight_price","price","product_id='".$ctable_d['product_id']."' AND weight_id='".$ctable_d['weight_id']."' AND isDelete=0");*/

		$result1['original_price']	= 100;
		if ($price_list_id != 0) {
			$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $ctable_d['product_id'] . "' AND weight_id='" . $ctable_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
			if ($check_product_in_list > 0) {
				$result1['original_price'] = $this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $ctable_d['product_id'] . "' AND weight_id='" . $ctable_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
			}
		}
		// Purchase Item

		$reply = array("ack" => 1, "developer_msg" => "Product Item detail fetched!!.", "ack_msg" => "Success! Product Item Edit Successfully.", "result" => $result, "item" => $result1);
		return $reply;
	}

	public function GetQuotationDetail($detail)
	{

		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where, "", 0);
		$ctable_d = mysqli_fetch_assoc($ctable_r);
		// echo "<pre>"; print_r($ctable_d);exit;
		$result = array();
		$result['customer_type']		= htmlentities($ctable_d['customer_type']);
		$result['dealer_id']		= htmlentities($ctable_d['customer_id']);
		$result['sales_executive_id']		= htmlentities($ctable_d['sales_executive_id']);
		$result['quotation_no']		= htmlentities($ctable_d['quotation_no']);
		$result['quotation_date']		= date("d-m-Y", strtotime($ctable_d['quotation_date']));
		$result['reference_date']		= ($ctable_d['reference_date'] != "0000-00-00") ? date("d-m-Y", strtotime($ctable_d['reference_date'])) : "";
		/* $result['total_amount']		= htmlentities($ctable_d['total_amount']);
		$result['total_qty']		= htmlentities($ctable_d['total_qty']);
		$result['grand_total']		= htmlentities($ctable_d['grand_total']);*/
		$result['remarks']			 = htmlentities($ctable_d['remarks']);
		$result['reference']		 = htmlentities($ctable_d['reference']);
		$result['attn']				 = htmlentities($ctable_d['attn']);
		$result['attn_no']			 = htmlentities($ctable_d['attn_no']);
		$result['attn_email']		 = htmlentities($ctable_d['attn_email']);
		$result['transport_charge']	 = htmlentities($ctable_d['transport_charge']);
		$result['vendor_code']		 = htmlentities($ctable_d['vendor_code']);
		$result['tendor_code']		 = htmlentities($ctable_d['tendor_code']);
		$result['tendor_no']		 = htmlentities($ctable_d['tendor_no']);
		$result['transport_name']	 = htmlentities($ctable_d['transport_name']);
		$result['transport_through'] = htmlentities($ctable_d['transport_through']);
		$result['packing_charge']    = htmlentities($ctable_d['packing_charge']);
		$result['shipping_address']  = htmlentities($ctable_d['shipping_address']);
		$result['billing_address']   = htmlentities($ctable_d['billing_address']);
		$result['terms_comdition']   = htmlentities($ctable_d['terms_comdition']);
		$result['faithfully']        = htmlentities($ctable_d['faithfully']);
		$result['cash_discount']        = htmlentities($ctable_d['cash_discount']);
		$result['additional_discount']  = htmlentities($ctable_d['additional_discount']);
		$result['igst_amount']  		= htmlentities($ctable_d['igst_amount']);
		$result['cash_discount_amount']  		= htmlentities($ctable_d['cash_discount_amount']);
		$result['additional_discount_amount']  		= htmlentities($ctable_d['additional_discount_amount']);
		$result['subtotal']  		= htmlentities($ctable_d['subtotal']);
		$result['roundoff']  		= htmlentities($ctable_d['roundoff']);
		$result['grand_total']  		= htmlentities($ctable_d['grand_total']);
		$result['tcs_amount']  		= htmlentities($ctable_d['tcs_amount']);
		$result['transport_charge_per']  		= htmlentities($ctable_d['transport_charge_gst']);
		$result['packing_charge_per']  		= htmlentities($ctable_d['packing_charge_gst']);
		$result['cd_gst']  		= htmlentities($ctable_d['cd_gst']);
		$result['ad_gst']  		= htmlentities($ctable_d['ad_gst']);
		$result['type_of_company']  		= htmlentities($ctable_d['type_of_company']);
		$result['terms_condition_id']  		= htmlentities($ctable_d['terms_condition_id']);
		$result['attachment']  		= htmlentities($ctable_d['attachment']);
		$result['sales_executive_id']  		= htmlentities($ctable_d['sales_id']);
		$result['name_gstin']  		= htmlentities($ctable_d['gst']);
		// Purchase Item

		$reply = array("ack" => 1, "developer_msg" => "Product Item detail fetched!!.", "ack_msg" => "Success! Product Item Edit Successfully.", "result" => $result);
		return $reply;
	}

	public function GetOrderRequestHistory($detail)
	{

		$where = " customer_id='" . $detail['cid'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctableRequest, "*", $where, "id DESC", 0);
		if ($ctable_r) {
			$result = array();
			while ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
				$ctable_d['created_date'] = date("d-m-Y", strtotime($ctable_d['created_date']));
				$ctable_d['status_slug'] = array_key_exists($ctable_d['status'], $this->RequestStatus) ? $this->RequestStatus[$ctable_d['status']] : $this->RequestStatus[0];
				$result[] = $ctable_d;
			}

			$reply = array("ack" => 1, "developer_msg" => "Request history Found", "ack_msg" => "Request history Found.", "result" => $result);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Request history not Found", "ack_msg" => "Request history not Found.");
			return $reply;
		}
	}
	public function GetOrderRequestDetail($detail)
	{

		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctableRequest, "*", $where, "id DESC", 0);
		if ($ctable_r) {
			$result = array();
			if ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
				$result = $ctable_d;
				$result['created_date'] = date("d-m-Y", strtotime($result['created_date']));
				$result['status_slug'] = $this->RequestStatus[$ctable_d['status']];
				$result['items'] = array();
				$ctable_item_r = $this->db->rp_getData($this->ctableRequestItems, "*", "request_id='" . $ctable_d['id'] . "'", "id DESC", 0);
				if ($ctable_item_r) {
					while ($ctable_item_d = mysqli_fetch_assoc($ctable_item_r)) {
						$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $ctable_item_d['weight_id'] . "' AND product_id='" . $ctable_item_d['item_id'] . "'", "", 0);
						if ($ctable_item_weight_detail) {
							$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
							$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
							//$ctable_item_d['item_name']=$ctable_item_d['item_name']." (".stripslashes($weight_name).")";
							$ctable_item_d['inner_size'] = $ctable_item_weight_detail['inner_size'];
							$ctable_item_d['box_qty'] = $ctable_item_d['request_qty'] / $ctable_item_d['inner_size'];
							$result['items'][] = $ctable_item_d;
						}
					}
				}
			}

			$reply = array("ack" => 1, "developer_msg" => "Request history Found", "ack_msg" => "Request history Found.", "result" => $result);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Request history not Found", "ack_msg" => "Request history not Found.");
			return $reply;
		}
	}

	public function GetPerformaHistory($detail)
	{

		$where = " customer_id='" . $detail['cid'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctablePerforma, "*", $where, "id DESC", 0);
		if ($ctable_r) {
			$result = array();
			while ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
				$ctable_d['request_no'] = $this->db->rp_getValue("customer_order_request_info", "request_no", "id='" . $ctable_d['request_id'] . "'");
				$ctable_d['created_date'] = date("d-m-Y", strtotime($ctable_d['created_date']));
				$ctable_d['invoice_date'] = date("d-m-Y", strtotime($ctable_d['invoice_date']));
				$ctable_d['status_slug'] = $this->PerformaStatus[$ctable_d['status']];
				$result[] = $ctable_d;
			}

			$reply = array("ack" => 1, "developer_msg" => "Performa history Found", "ack_msg" => "Performa history Found.", "result" => $result);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Performa history not Found", "ack_msg" => "Performa history not Found.");
			return $reply;
		}
	}
	public function GetPerformaDetail($detail)
	{

		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctablePerforma, "*", $where, "id DESC", 0);
		if ($ctable_r) {
			$result = array();
			if ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
				$result = $ctable_d;
				$result['created_date'] = date("d-m-Y", strtotime($result['created_date']));
				$result['invoice_date'] = date("d-m-Y", strtotime($result['invoice_date']));
				$result['status_slug'] = $this->PerformaStatus[$ctable_d['status']];
				$result['items'] = array();
				$ctable_item_r = $this->db->rp_getData($this->ctablePerformaItems, "*", "proforma_invoice_id='" . $ctable_d['id'] . "'", "id DESC", 0);
				if ($ctable_item_r) {
					while ($ctable_item_d = mysqli_fetch_assoc($ctable_item_r)) {
						$result['items'][] = $ctable_item_d;
					}
				}
			}

			$reply = array("ack" => 1, "developer_msg" => "Performa history Found", "ack_msg" => "Performa history Found.", "result" => $result);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Performa history not Found", "ack_msg" => "Performa history not Found.");
			return $reply;
		}
	}
	//------------get All ordered Product Item------------------//	
	public function GetQuotationDetailItems($detail)
	{

		$where = "quotation_id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_item = $this->db->rp_getData("quotation_product_item", "*", $where, "", 0);
		if ($ctable_item) {
			while ($ctable_item_d = mysqli_fetch_array($ctable_item)) {
				$result_item = array();
				$result_item['product_id']		= htmlentities($ctable_item_d['pro_id']);
				$result_item['cid']				= htmlentities($ctable_item_d['cat_id']);
				$result_item['tcid']			= htmlentities($ctable_item_d['top_cat_id']);
				$result_item['quotation_id']	= htmlentities($ctable_item_d['quotation_id']);
				$result_item['weight_id']		= htmlentities($ctable_item_d['weight_id']);
				$result_item['brand_id']		= htmlentities($ctable_item_d['brand_id']);
				$result_item['pro_description']		= htmlentities($ctable_item_d['pro_description']);
				$pro_name = $this->db->rp_getValue("product", "name", "id='" . $ctable_item_d['pro_id'] . "'");
				$size_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_d['weight_id'] . "'");
				$result_item['product_name'] = $size_name . " " . $pro_name . " ";
				// $result_item['product_name']	= htmlentities($ctable_item_d['pro_name']);
				//$result_item['product_code']	= htmlentities($ctable_item_d['product_code']);
				$result_item['qty']		= htmlentities($ctable_item_d['pro_qty']);
				$result_item['order_item_brand_id']	= htmlentities($ctable_item_d['order_item_brand_id']);
				$result_item['inner_size']		= htmlentities($ctable_item_d['inner_size']);
				$result_item['outer_size']		= htmlentities($ctable_item_d['outer_size']);
				$result_item['box']		= htmlentities($ctable_item_d['cartoon_qty']);
				$result_item['bag']		= $this->db->rp_num(htmlentities($ctable_item_d['box_qty']));
				$result_item['loose']				= $this->db->rp_num(htmlentities($ctable_item_d['loose_qty']));
				$result_item['discount_per']		= $this->db->rp_num(htmlentities($ctable_item_d['discount']));
				$result_item['item_idfbrand']	= htmlentities($ctable_item_d['id']);
				$result_item['product_price'] = $this->db->rp_num(htmlentities($ctable_item_d['unitprice']));
				$result_item['original_price'] = $this->db->rp_num(htmlentities($ctable_item_d['original_price']));
				$result_item['product_total']	= $this->db->rp_num(htmlentities($ctable_item_d['totalprice']));
				$result_item['discount_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['discount_amount']));
				$result_item['cat_no']		    = $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . $result_item['product_id'] . "'");
				$result_item['hsn_code']         = $this->db->rp_getValue("product", "hsn_code", "id='" . $result_item['product_id'] . "' AND isDelete=0");
				$result_item['stock']				= $this->db->rp_getValue("product_weight_price", "stock_qty", "product_id='" . $result_item['product_id'] . "'", 0);

				$result_item['item_gst']	= $this->db->rp_num(htmlentities($ctable_item_d['item_gst']));
				$result_item['gst']         = $this->db->rp_getValue("product", "igst", "id='" . $result_item['product_id'] . "' AND isDelete=0");

				$gst_amount1 = $ctable_item_d['item_gst_amount'] * $ctable_item_d['pro_qty'];
				$result_item['gst_amount'] = $gst_amount1;

				$cid = $this->db->rp_getValue("quotation_detail", "customer_id", "id='" . $ctable_item_d['quotation_id'] . "' AND isDelete=0", 0);
				$last_quotation_id = $this->db->rp_getValue("quotation_detail", "id", "customer_id='" . $cid . "' AND isDelete=0 ORDER BY id DESC", 0);

				$result_item['last_quotation_price'] = $this->db->rp_getValue("quotation_product_item", "original_price", "quotation_id='" . $last_quotation_id . "' AND isDelete=0");

				$result_item['top_cat_name'] = $this->db->rp_getValue("top_category_master", "name", "id='" . $ctable_item_d['top_cat_id'] . "' AND isDelete=0", 0);
				$result_item['category_name'] = $this->db->rp_getValue("category_master", "name", "id='" . $ctable_item_d['cat_id'] . "' AND isDelete=0", 0);

				$result_item['cd_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['cash_discount_amount']));
				$result_item['ad_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['additional_discount_amount']));
				$result_item['gst_amount_item']	= $this->db->rp_num(htmlentities($ctable_item_d['igst_amount']));
				$result_item['taxable_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['taxable']));
				$result_item['sub_total']	= $this->db->rp_num(htmlentities($ctable_item_d['subtotal']));
				$result_item['other_charge']	= $this->db->rp_num(htmlentities($ctable_item_d['other_charge']));
				$result_item['fright_charge']	= $this->db->rp_num(htmlentities($ctable_item_d['fright_charge']));
				$result_item['is_including']		= htmlentities($ctable_item_d['is_including']);
				$result_item['order_qty'] = htmlentities($ctable_item_d['order_qty']);
				$result_item['item_order_unit'] = htmlentities($ctable_item_d['item_order_unit']);

				$result[] = $result_item;
			}
			$reply = array("ack" => 1, "developer_msg" => "Product Item detail fetched!!.", "ack_msg" => "Success! Update Product Item Successfully.", "result" => $result);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Update not fetched!!.", "ack_msg" => "Success! Update Failed");
			return $reply;
		}
	}
	//------------------Delete Order Also Delete product item-----------------	//
	public function DeleteQuotation($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='" . $_REQUEST['id'] . "'";
		$where_item	= "quotation_id='" . $_REQUEST['id'] . "'";
		$quotation_id = $this->db->rp_update($this->ctable, $rows, $where);
		$id = $this->db->rp_update("quotation_product_item", $rows, $where_item);
		//$this->log->insertLog($this->ctable,$_REQUEST['id'],"delete","Order Deleted ");
		if ($quotation_id != 0) {
			$reply = array("ack" => 1, "developer_msg" => "deleted data.", "ack_msg" => "Success! Delete Quotation Successfully.", "type" => $_REQUEST['id']);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Delete Quotation Failed.");
			return $reply;
		}
	}

	public function cancelOrder($detail)
	{
		extract($detail);
		$rows 	= array(
			"isActive"	=> "0",
			"reason_of_cancel_order" => $reason,
			"status" => "3"
		);
		$where	= "id='" . $quotation_id . "'";
		$where_item	= "quotation_id='" . $quotation_id . "'";
		$quotation_id = $this->db->rp_update($this->ctable, $rows, $where, 0);

		$row = array(
			"isActive"	=> "0",
			"status"	=> "3",
		);
		$id = $this->db->rp_update("quotation_product_item", $row, $where_item);
		//$this->log->insertLog($this->ctable,$_REQUEST['id'],"delete","Order Deleted ");
		if ($quotation_id != 0) {
			$reply = array("ack" => 1, "developer_msg" => "deleted data.", "ack_msg" => "Success! Cancel Order Successfully.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Cancel Order Failed.");
			return $reply;
		}
	}
	public function AddCustomerOrderRequest($detail, $products)
	{
		$customer_d = $this->db->rp_getData("customer", "*", "id='" . $detail['cid'] . "'");
		if ($customer_d) {
			$total_qty = 0;
			$customer_r = mysqli_fetch_assoc($customer_d);
			$request_no = "RE" . $this->db->getLastInsertId("customer_order_request_info");
			$created_date	= date('Y-m-d H:i:s');
			$rows 	= array(
				"request_no",
				"customer_id",
				"customer_name",
				"company_name",
				"type_of_customer",
				"phone",
				"address",
				"city",
				"state",
				"country",
				"email"
			);
			$values = array(
				$request_no,
				$customer_r['id'],
				$customer_r['name'],
				$customer_r['company_name'],
				"normal_user",
				$customer_r['phone'],
				$customer_r['address1'],
				$customer_r['city'],
				$customer_r['state'],
				$customer_r['country'],
				$customer_r['email']
			);

			$customer_order_request_id = $this->db->rp_insert("customer_order_request_info", $values, $rows, 0);
			if ($customer_order_request_id != 0) {
				if (!empty($products)) {
					foreach ($products as $p) {
						$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
						if ($product_detail) {
							$product_detail = mysqli_fetch_assoc($product_detail);
							$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);
							if ($ctable_item_weight_detail) {
								$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
								$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
								$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")"));
								$p['inner_size'] = $ctable_item_weight_detail['inner_size'];
								$p['box_qty'] = $p['qty'] / $ctable_item_weight_detail['inner_size'];
								$rows 	= array(
									"request_id",
									"item_id",
									"weight_id",
									"item_name",
									"request_qty",
									"pending_qty",
									"inner_size",
									"box_qty"
								);
								$values = array(
									$customer_order_request_id,
									$p['pid'],
									$p['weight_id'],
									$p['item_name'],
									$p['qty'],
									$p['qty'],
									$p['inner_size'],
									$p['box_qty']
								);
								$total_qty += $p['qty'];
								$item_id = $this->db->rp_insert("customer_order_request_item", $values, $rows, 0);
							}
						}
					}
					$isUpdated = $this->db->rp_update("customer_order_request_info", array("total_qty" => $total_qty), "id='" . $customer_order_request_id . "'");
					if ($isUpdated) {
						///////////////////////// notification ////////////////////
						$title_description = "Order Request of <b>Qty." . $total_qty . "</b> for date <b>" . date('d-m-Y', strtotime($created_date)) . "</b> added by <b>" . $customer_r['name'] . "</b>";
						$notification = $this->system->setNotification(0, 1, "Order Request Notification.", 6, "Order Request Message", $title_description, "", "", $created_date, $customer_order_request_id, "customer_order_request_info", "customer");

						$customer_order_detail = $this->db->rp_getData("customer_order_request_info", "*", "id='" . $customer_order_request_id . "'");
						if ($customer_order_detail) {
							$customer_order_detail = mysqli_fetch_assoc($customer_order_detail);
							$customer_order_items = $this->db->rp_getData("customer_order_request_item", "*", "request_id='" . $customer_order_request_id . "'");
							if ($customer_order_items) {
								while ($customer_order_item = mysqli_fetch_assoc($customer_order_items)) {
									$customer_order_detail['order_items'][] = $customer_order_item;
								}
							}
							$result[] = $customer_order_detail;
							$reply = array("ack" => 1, "developer_msg" => "Customer Order request inserted Successfully", "ack_msg" => "Customer Order request inserted Successfully", "result" => $result);
							return $reply;
						}
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Request Generated With Errore", "ack_msg" => "Request Generated With Error");
						return $reply;
					}
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Request Generated With Error");
					return $reply;
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Request Not Generated", "ack_msg" => "Request Generated With Error");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Customer Not Found!!", "ack_msg" => "Customer Not Found!!");
			return $reply;
		}
	}
	public function AcceptinvoiceOrder($detail)
	{
		$invoice_info = $this->db->rp_getData("proforma_invoice_info", "*", "id='" . $detail['invoice_id'] . "'");
		if ($invoice_info) {
			$invoice_info = mysqli_fetch_assoc($invoice_info);
			$details['total_qty'] = isset($invoice_info['total_qty']) ? $this->db->clean($invoice_info['total_qty']) : "";
			$details['item_totalprice']	= isset($invoice_info['subtotal']) ? $this->db->clean($invoice_info['subtotal']) : "";
			$details['discount']	= isset($invoice_info['discount']) ? $this->db->clean($invoice_info['discount']) : "";
			$details['discount_type']			= "";
			$details['taxable']		= isset($invoice_info['taxable']) ? $this->db->clean($invoice_info['taxable']) : "";
			$details['cash_discount']		= isset($invoice_info['cash_discount']) ? $this->db->clean($invoice_info['cash_discount']) : "";
			$details['cash_discount_amount']		= isset($invoice_info['cash_discount_amount']) ? $this->db->clean($invoice_info['cash_discount_amount']) : "";
			$details['subtotal']		= isset($invoice_info['subtotal']) ? $this->db->clean($invoice_info['subtotal']) : "";
			$details['cgst_tax_amount']		= isset($invoice_info['cgst_tax_amount']) ? $this->db->clean($invoice_info['cgst_tax_amount']) : "";
			$details['sgst_tax_amount']		= isset($invoice_info['sgst_tax_amount']) ? $this->db->clean($invoice_info['sgst_tax_amount']) : "";
			$details['igst_tax_amount']		= isset($invoice_info['igst_tax_amount']) ? $this->db->clean($invoice_info['igst_tax_amount']) : "";
			$details['grand_total_rounded']	= isset($invoice_info['grand_total_rounded']) ? $this->db->clean($invoice_info['grand_total_rounded']) : "";
			$details['roundoff']		    = isset($invoice_info['roundoff']) ? $this->db->clean($invoice_info['roundoff']) : "";

			$details['grand_total'] = isset($invoice_info['grand_total']) ? $this->db->clean($invoice_info['grand_total']) : "";

			$details['customer_id'] = isset($invoice_info['customer_id']) ? $this->db->clean($invoice_info['customer_id']) : "";
			$details['customer_name'] = isset($invoice_info['customer_name']) ? $this->db->clean($invoice_info['customer_name']) : "";
			$details['customer_type'] = $this->db->rp_getValue("customer_order_request_info", "type_of_customer", "id='" . $invoice_info['request_id'] . "'");
			$details['contact_number']		= isset($invoice_info['phone']) ? $this->db->clean($invoice_info['phone']) : "";
			$details['address']		= isset($invoice_info['address']) ? $this->db->clean($invoice_info['address']) : "";
			$details['city']		= isset($invoice_info['city']) ? $this->db->clean($invoice_info['city']) : "";
			$details['state']		= isset($invoice_info['state']) ? $this->db->clean($invoice_info['state']) : "";
			$details['country']		= isset($invoice_info['country']) ? $this->db->clean($invoice_info['country']) : "";
			$details['email']		= isset($invoice_info['email']) ? $this->db->clean($invoice_info['email']) : "";
			$details['company_name']		= isset($invoice_info['company_name']) ? $this->db->clean($invoice_info['company_name']) : "";
			$details['remarks']		= isset($invoice_info['remarks']) ? $this->db->clean($invoice_info['remarks']) : "";
			$details['sales_id']	= "";
			$items = array();
			$invoice_items = $this->db->rp_getData("proforma_invoice_item", "*", "proforma_invoice_id='" . $detail['invoice_id'] . "'", 1);
			if ($invoice_items) {
				while ($invoice_item = mysqli_fetch_assoc($invoice_items)) {
					$items[] = $invoice_item;
				}
			}
			$result = $this->InsertOrdersFinal($details, $items);
			$Update = $this->db->rp_update($this->ctablePerforma, array("status" => 1), "id='" . $detail['invoice_id'] . "'", 0);
			$Update = $this->db->rp_update("quotation_detail", array("proforma_invoice_id" => $detail['invoice_id']), "id='" . $result['quotation_id'] . "'", 0);
			return $result;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Invoice Not Found!!", "ack_msg" => "Invoice Not Found!!");
			return $reply;
		}
	}
	public function InsertOrdersFinal($details, $items)
	{
		extract($details);
		$quotation_date	= date("Y-m-d");
		$modify_date	= date("Y-m-d H:i:s");

		$cdrow 	= array(
			"total_qty",
			"total_amount",
			"discount",
			"discount_type",
			"taxable",
			"cash_discount",
			"cash_discount_amount",
			"subtotal",
			"cgst_amount",
			"sgst_amount",
			"igst_amount",
			"grand_total",
			"roundoff",
			"grand_total_rounded",
			"customer_id",
			"sales_id",
			"sales_type",
			"customer_name",
			"customer_type",
			"contact_number",
			"address",
			"city",
			"email",
			"state",
			"country",
			"quotation_date",
			"modify_date",
			"company_name",

		);
		$cdvalue = array(
			$total_qty,
			$item_totalprice,
			$discount,
			$discount_type,
			$taxable,
			$cash_discount,
			$cash_discount_amount,
			$subtotal,
			$cgst_tax_amount,
			$sgst_tax_amount,
			$igst_tax_amount,
			$grand_total,
			$roundoff,
			$grand_total_rounded,
			$customer_id,
			$sales_id,
			$customer_type,
			$customer_name,
			$customer_type,
			$contact_number,
			$address,
			$city,
			$email,
			$state,
			$country,
			$quotation_date,
			$modify_date,
			$company_name,

		);

		$cart_id = $this->db->rp_insert("quotation_detail", $cdvalue, $cdrow, 0);
		/*$row = array("quotation_no"=>OUTLETS_ORDER_NO.str_pad($cart_id, 3, '0', STR_PAD_LEFT));
				$update_order_no = $this->db->rp_update("quotation_detail",$row,"id='".$cart_id."'",0);*/
		$adate = date('Y-m-d H:i:s');
		$total_taxamount = 0;
		$total_tax = 0;
		$total_discount = 0;
		$grand_total = 0;

		foreach ($items as  $p) {
			$row = array(
				"quotation_id",
				"pro_id",
				"weight_id",
				"pro_name",
				"unitprice",
				"pro_qty",
				"remaining_qty",
				"totalprice",
				"discount_amount",
				"discount",
				"taxable",
				"cgst_amount",
				"cgst_tax",
				"sgst_amount",
				"sgst_tax",
				"igst_amount",
				"igst_tax",
				"adate",
				"modify_date",
				"inner_size",
				"outer_size",
				"box_qty",
				"grandtotal",
				"cash_discount",
				"cash_discount_amount",
				"subtotal",

			);
			$value = array(
				$cart_id,
				$p['item_id'],
				$p['weight_id'],
				$p['item_name'],
				$p['item_price'],
				$p['item_qty'],
				$p['item_qty'],
				$p['item_totalprice'],
				$p['discount_amount'],
				$p['discount'],
				$p['taxable'],
				$p['cgst_tax_amount'],
				$p['cgst_tax'],
				$p['sgst_tax_amount'],
				$p['sgst_tax'],
				$p['igst_tax_amount'],
				$p['igst_tax'],
				date("Y-m-d H:i:s"),
				date("Y-m-d H:i:s"),
				$p['inner_size'],
				$p['outer_size'],
				$p['box_qty'],
				$p['grandtotal'],
				$p['cash_discount'],
				$p['cash_discount_amount'],
				$p['subtotal'],
			);

			$ins = $this->db->rp_insert("quotation_product_item", $value, $row, 0);
		}
		$order_pro_detail = mysqli_fetch_assoc($this->db->rp_getData("quotation_detail", "*", "id='" . $cart_id . "' AND isDelete=0", "", 0));
		$order_pro_detail['product'] = array();
		$where = "quotation_id='" . $cart_id . "' AND isDelete=0";
		$dt = $this->db->rp_getData("quotation_product_item", "*", $where, "", 0);
		$r = array();
		if ($dt) {
			while ($row = mysqli_fetch_assoc($dt)) {
				$r[] = $row;
			}

			$order_pro_detail['product'] = $r;
		}

		if ($customer_type == 'normal_user') {
			$customer_type = 'customer';
		}
		///////////////////////// notification ////////////////////
		$title_description = "Order of <b>Rs." . $grand_total_rounded . "</b> for date <b>" . date('d-m-Y', strtotime($quotation_date)) . "</b> added by <b>" . $customer_name . "</b>";
		$notification = $this->system->setNotification(0, 1, "Order Notification.", 5, "Order Message", $title_description, "", "", $quotation_date, $cart_id, "quotation_detail", $customer_type);

		$reply = array(
			"ack" => 1,
			"ack_msg" => "Order Add Successfully!!",
			"developer_msg" => "You got it!!",
			"result" => $order_pro_detail,
			"quotation_id" => $cart_id,
		);
		return $reply;
	}
	public function RejectinvoiceOrder($details)
	{

		$invoice_info = $this->db->rp_getData("proforma_invoice_info", "*", "id='" . $details['invoice_id'] . "'");
		if ($invoice_info) {
			$invoice_info = mysqli_fetch_assoc($invoice_info);
			$request_id = $invoice_info['request_id'];
			$customer_name = $invoice_info['customer_name'];
			$invoice_items = $this->db->rp_getData("proforma_invoice_item", "*", "proforma_invoice_id='" . $details['invoice_id'] . "'", "", 0);

			while ($invoice_item = mysqli_fetch_array($invoice_items)) {
				$pro_id = $invoice_item['item_id'];
				$weight_id = $invoice_item['weight_id'];
				$qty = $invoice_item['item_qty'];
				$quotation_id = $invoice_item['request_id'];

				$remaining_qty = $this->db->rp_getValue("customer_order_request_item", "pending_qty", "item_id='" . $pro_id . "' AND weight_id='" . $weight_id . "' AND request_id='" . $quotation_id . "'", 0);

				$final_remaining_qty = ($remaining_qty) + ($qty);

				$rows 	= array(
					"pending_qty"				=> $final_remaining_qty,
				);
				$where	= "item_id='" . $pro_id . "' AND weight_id='" . $weight_id . "' AND request_id='" . $quotation_id . "'";
				$orderItemUpdated = $this->db->rp_update("customer_order_request_item", $rows, $where, 0);
			}
			$this->db->rp_update("proforma_invoice_info", array("remarks" => $details['remarks'], "status" => 2), "id='" . $details['invoice_id'] . "'", 0);

			$isUpdated = $this->db->rp_update("customer_order_request_info", array("status" => 1), "id='" . $request_id . "'");

			///////////////////////// notification ////////////////////
			$title_description = "ProForma Invoice Rejected by <b>" . $customer_name . "</b> Due to " . $details['remarks'];
			$notification = $this->system->setNotification(0, 1, "ProForma Invoice Reject Notification.", 7, "ProForma Invoice Reject Message", $title_description, "", "", date('Y-m-d'), $details['invoice_id'], "proforma_invoice_info", "normal_user");

			if ($isUpdated) {
				$reply = array(
					"ack" => 1,
					"ack_msg" => "Invoice Rejected Successfully!!",
					"developer_msg" => "Invoice Rejected Successfully!!",
				);
				return $reply;
			}
		} else {
			$reply = array(
				"ack" => 0,
				"ack_msg" => "Invoice Not Found!!",
				"developer_msg" => "Invoice Not Found!!",
			);
			return $reply;
		}
	}

	public function AddQuotation($detail, $products, $file)
	{
		//print_r($products);exit;
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0", "", 0);
		if ($customer_d) {
			$where = "";
			if ($detail['sales_executive_id'] != "") {
				$where = " AND sales_id='" . $detail['sales_executive_id'] . "' ";
			} else {
				$detail['sales_executive_id'] = "";
				$where = " AND sales_id=0";
			}

			if ($detail['quotation_id'] != "") {
				$check_cart_exist = $this->db->rp_getTotalRecord("quotation_detail", "id='" . $detail['quotation_id'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "'" . $where, 0);
			} else {
				$check_cart_exist = $this->db->rp_getTotalRecord("quotation_detail", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "' AND status=-1 " . $where, 0);
			}

			if ($check_cart_exist != 0) {
				// already cart exist
				if ($detail['quotation_id'] != "") {
					$quotation_id = $detail['quotation_id'];
				} else {
					$quotation_id = $this->db->rp_getValue("quotation_detail", "id", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND status=-1", 0);
				}

				$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
				if (!empty($products)) {
					$total_items = $this->db->rp_getTotalRecord("quotation_product_item", "quotation_id='" . $quotation_id . "'");
					foreach ($products as $p) {
						// $product_detail=$this->db->rp_getData("product","*","id='".$p['pid']."'","","0");
						//check item already in cart or not
						$check_item_in_cart = $this->db->rp_getValue("quotation_product_item", "id", "quotation_id='" . $quotation_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");
						if ($check_item_in_cart != 0 && $check_item_in_cart != "") {
							// update qty of that item
							$pro_r = $this->db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quotation_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");
							$pro_d = mysqli_fetch_assoc($pro_r);
							$update_qty = $p['qty'] + $pro_d['pro_qty'];
							$pro_d['unitprice'] = $this->db->rp_num($p['price']);
							$update_totalprice = $this->db->rp_num($update_qty * $pro_d['unitprice']);

							/*$update_box_qty=$this->db->rp_num($update_qty/$pro_d['inner_size']);
							$update_cartoon_qty=$this->db->rp_num($update_box_qty/$pro_d['outer_size']);*/

							// $update_box_qty=$p['box_qty'];
							// $update_cartoon_qty=$p['cartoon_qty'];

							$update_box_qty = $p['box_qty'] + $pro_d['box_qty'];
							$update_cartoon_qty = $p['cartoon_qty'] + $pro_d['cartoon_qty'];

							$update_item = array(
								"pro_description" => $p['pro_description'],
								"unitprice" => $p['price'],
								"original_price" => $p['original_price'],
								"order_item_brand_id" => $p['order_item_brand_id'],
								"pro_qty" => $update_qty,
								"remaining_qty" => $update_qty,
								"totalprice" => $update_totalprice,
								"box_qty" => $update_box_qty,
								"cartoon_qty" => $update_cartoon_qty,
								"modified_date" => date("Y-m-d H:i:s"),
							);

							$isUpdate = $this->db->rp_update("quotation_product_item", $update_item, "quotation_id='" . $quotation_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0", 1);
							$reply = array("ack" => 1, "developer_msg" => "Product Updated Successfully", "ack_msg" => "Product Updated Successfully", "quotation_id" => $quotation_id);
						} else {
							// insert new item
							$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
							if ($product_detail) {
								$product_detail = mysqli_fetch_assoc($product_detail);

								$top_cat_id = $product_detail['tcid'];
								$cat_id = $product_detail['cid'];

								$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);
								$user_discount = $this->db->rp_getValue("price_table", "discount", "tcid='" . $product_detail['tcid'] . "' AND uid='" . $detail['cid'] . "' AND isDelete=0");
								if ($ctable_item_weight_detail) {
									$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
									$product_code = $ctable_item_weight_detail['catno'];
									$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");

									if ($ctable_item_weight_detail['weight_id'] == -1) {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (#" . stripslashes($product_code) . ")"));
									} else {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")" . " (#" . stripslashes($product_code) . ")"));
									}
									$p['inner_size'] = $ctable_item_weight_detail['inner_size'];
									$p['outer_size'] = $ctable_item_weight_detail['outer_size'];

									/*$p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
									$p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];*/

									$p['box_qty'] = $p['box_qty'];
									$p['cartoon_qty'] = $p['cartoon_qty'];

									// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."'",0);
									$unitprice = $this->db->rp_num($p['price']);
									$GST = $product_detail['igst'];
									$totalprice = $p['qty'] * $unitprice;
									$totalprice = $this->db->rp_num($totalprice);
									$original_price = $p['original_price'];

									$user_discount = $p['discount'];
									if ($user_discount == 0) {
										$discount_amount = $p['discount_amount'];
									} else {
										$discount_amount = ($p['original_price'] * $user_discount) / 100;
									}

									$unitprice_amt = $discount_amount;
									$final_price = $this->db->rp_num($totalprice);

									$price_list_price = 0;
									$price_list_discounted_price = 0;
									$price_list_discounted_amount = 0;
									$price_list_discount_type = 0;
									$price_list_discount = 0;

									if ($price_list_id != 0) {
										$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
										if ($check_product_in_list > 0) {
											$add_price_list_id = $price_list_id;
											$price_list_price = $this->db->rp_getValue("product_price_list", "price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											$GST = $product_detail['igst'];
											$totalprice = $p['qty'] * $unitprice;
											$totalprice = $this->db->rp_num($totalprice);
											// $original_price=$price_list_price;
											$final_price = $this->db->rp_num($totalprice);

											$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											$p_discount = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											// $discount = $p['discount'];
											$price_list_discount = $this->db->rp_num($p_discount);
											$discount_amount1 = ($p['original_price'] * $price_list_discount) / 100;
											$price_list_discounted_amount = $this->db->rp_num($discount_amount1);
											$price_list_discounted_price = $p['original_price'] - $price_list_discounted_amount;
										} else {
											$add_price_list_id = 0;
										}
									} else {
										$add_price_list_id = 0;
									}
									$rows 	= array(
										"quotation_id",
										"top_cat_id",
										"cat_id",
										"pro_id",
										"weight_id",
										"brand_id",
										"pro_name",
										"pro_description",
										"pro_qty",
										"remaining_qty",
										"inner_size",
										"outer_size",
										"box_qty",
										"cartoon_qty",
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
										"loose_qty",
										"item_order_unit",
										"order_qty",
										"order_item_brand_id",
									);
									$values = array(
										$quotation_id,
										$top_cat_id,
										$cat_id,
										$p['pid'],
										$p['weight_id'],
										$p['brand_id'],
										trim($p['item_name']),
										trim($p['pro_description']),
										$p['qty'],
										$p['qty'],
										$p['inner_size'],
										$p['outer_size'],
										$p['box_qty'],
										$p['cartoon_qty'],
										$unitprice,
										$original_price,
										$final_price,
										$user_discount,
										$unitprice_amt,
										$add_price_list_id,
										$price_list_price,
										($price_list_discounted_price) ? $price_list_discounted_price : 0,
										($price_list_discounted_amount) ? $price_list_discounted_amount : 0,
										($price_list_discount_type) ? $price_list_discount_type : 0,
										($price_list_discount) ? $price_list_discount : 0,
										isset($p['loose']) ? $p['loose'] : "0",
										isset($p['item_order_unit']) ? $p['item_order_unit'] : "0",
										isset($p['order_qty']) ? $p['order_qty'] : "0",
										isset($p['order_item_brand_id']) ? $p['order_item_brand_id'] : "0",
									);
									$total_qty += $p['qty'];
									$sub_total += $final_price;
									$item_id = $this->db->rp_insert("quotation_product_item", $values, $rows, 0);
									if ($item_id != 0) {
										$reply = array("ack" => 1, "developer_msg" => "Product added Successfully", "ack_msg" => "Product added Successfully", "quotation_id" => $quotation_id);
									} else {
										$reply = array("ack" => 0, "developer_msg" => "Product added Failed", "ack_msg" => "Product added Failed");
									}
								}
							} else {
								$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Request Generated With Error Product Not Found");
							}
						}
					}
					return $reply;
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Request Generated With Error Product Not Found");
					return $reply;
				}
			} else {
				// create new cart
				$customer_r = mysqli_fetch_assoc($customer_d);
				$price_list_id = $customer_r['price_list_id'];
				if ($detail['revised_quotation_main_id'] == 0) {
					// $quotation_no=$this->db->getLastInsertId("quotation_detail");
					$quotation_no = $this->db->rp_getValue("quotation_detail", "MAX(id)", "revised_quotation_main_id=0 AND isDelete = 0 AND isActive = 1");
					$quotation_no = $quotation_no + 1;
					$quotation_no = DEALER_QUOTATION_NO . str_pad($quotation_no, 2, '0', STR_PAD_LEFT);
				} else {
					$quotation_no = $this->db->rp_getValue("quotation_detail", "quotation_no", "id='" . $detail['revised_quotation_main_id'] . "'");
					$totalCount = $this->db->rp_getTotalRecord("quotation_detail", "revised_quotation_main_id='" . $detail['revised_quotation_main_id'] . "'");
					$totalCount = $totalCount + 1;

					// update code //
					$new_quotation_no = $quotation_no . "-" . "R" . $totalCount;

					// update code //
					$quotation_no = $new_quotation_no;
				}

				$created_date	= date('Y-m-d H:i:s');
				// $quotation_date	= isset($detail['quotation_date'])?$detail['quotation_date']:date("Y-m-d");

				if (isset($file["attachment"])) {
					// print_r($file);exit;
					$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
					$temp = explode(".", $file["attachment"]["name"]);
					$extension = end($temp);

					$fileName 	= $this->db->clean($file["attachment"]["name"]);
					if ($fileName != "") {
						$fileSize 	= round($file["attachment"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');

						$extension	= end(explode(".", $fileName));
						if (!in_array($extension, $allowedExts)) {
							$file_error = true;
						}

						$image_path	= 'attachment' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	=  QUOTATION_ATTACHMENT_A . $image_path;
						$file['attachment']['tmp_name'];
						move_uploaded_file($file['attachment']['tmp_name'], $filePath);
						$new_image = true;
					} else {
						$image_path = "";
					}
				} else {
					$new_image = false;
					$image_path = "";
				}
				$rows 	= array(
					"inquiry_id",
					"refrence_id",
					"revised_quotation_main_id",
					"quotation_no",
					"customer_id",
					"dealer_id",
					"super_stockist_id",
					"customer_name",
					"company_name",
					"client_code",
					"customer_flag",
					"customer_type",
					// "contact_number",
					"address",
					"city",
					"main_city",
					"state",
					"country",
					"email",
					// "gst",
					"quotation_date",
					// "brand_id",
					"status",
					"sales_id",
					"reference",
					"reference_date",
					"attn",
					"transport_charge",
					"terms_comdition",
					"faithfully",
					"vendor_code",
					"tendor_code",
					"tendor_no",
					"transport_name",
					"transport_through",
					"packing_charge",
					"shipping_address",
					"billing_address",
					"gst",
					"currency_code",
					"entry_flag",
					"type_of_company",
					"terms_condition_id",
					"attachment",
				);
				$values = array(
					$detail['inquiry_id'],
					$detail['reference_id'],
					$detail['revised_quotation_main_id'],
					$quotation_no,
					$customer_r['id'],
					$customer_r['dealer_distributor_id'],
					$customer_r['super_stockist_id'],
					$customer_r['cname'],
					$customer_r['company_name'],
					$customer_r['client_code'],
					$customer_r['customer_flag'],
					$customer_r['type_of_executive'],
					// $customer_r['phone'],
					addslashes($customer_r['address']),
					$customer_r['city'],
					$customer_r['main_city'],
					$customer_r['state'],
					$customer_r['country'],
					$customer_r['email'],
					// $customer_r['gst'],
					$detail['quotation_date'],
					// $customer_r['brand_id'],
					0,
					$detail['sales_executive_id'],
					$detail['reference'],
					$detail['reference_date'],
					$detail['attn'],
					$detail['transport_charge'],
					$this->db->clean($detail['terms_comdition']),
					$this->db->clean($detail['faithfully']),
					$detail['vendor_code'],
					$detail['tendor_code'],
					$detail['tendor_no'],
					$detail['transport_name'],
					$detail['transport_through'],
					$detail['packing_charge'],
					$this->db->clean($detail['shipping_address']),
					$this->db->clean($detail['billing_address']),
					$detail['gstin'],
					$detail['currency_code'],
					1,
					$detail['type_of_company'],
					$detail['terms_condition_id'],
					$image_path,
				);

				$quotation_id = $this->db->rp_insert("quotation_detail", $values, $rows, 0);
				if ($quotation_id != 0) {
					if (!empty($products)) {
						foreach ($products as $p) {
							$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
							if ($product_detail) {
								$product_detail = mysqli_fetch_assoc($product_detail);
								$top_cat_id = $product_detail['tcid'];
								$cat_id = $product_detail['cid'];
								$hsn_code = $product_detail['hsn_code'];
								$igst = $product_detail['igst'];
								$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);

								if ($ctable_item_weight_detail) {
									$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
									$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
									$product_code = $ctable_item_weight_detail['catno'];

									if ($ctable_item_weight_detail['weight_id'] == -1) {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (#" . stripslashes($product_code) . ")"));
									} else {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")" . " (#" . stripslashes($product_code) . ")"));
									}
									$p['inner_size'] = $ctable_item_weight_detail['inner_size'];
									$p['outer_size'] = $ctable_item_weight_detail['outer_size'];
									/*$p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
									$p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];*/
									$p['box_qty'] = $p['box_qty'];
									$p['cartoon_qty'] = $p['cartoon_qty'];

									// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."'",0);
									$unitprice = $p['price'];
									$unitprice = $this->db->rp_num($unitprice);
									$GST = $product_detail['igst'];
									$totalprice = $p['qty'] * $unitprice;
									$totalprice = $this->db->rp_num($totalprice);
									$original_price = $p['original_price'];

									$user_discount = $p['discount'];
									if ($user_discount == 0 || $user_discount == "") {
										$discount_amount = $this->db->rp_num($p['discount_amount']);
										if ($discount_amount == "") {
											$discount_amount = 0;
										}
									} else {
										$discount_amount = $this->db->rp_num(($p['original_price'] * $user_discount) / 100);
									}
									$unitprice_amt = $discount_amount;
									$final_price = $this->db->rp_num($totalprice);

									$price_list_price = 0;
									$price_list_discounted_price = 0;
									$price_list_discounted_amount = 0;
									$price_list_discount_type = 0;
									$price_list_discount = 0;

									if ($price_list_id != 0) {
										$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
										if ($check_product_in_list > 0) {
											$add_price_list_id = $price_list_id;
											$price_list_price = $this->db->rp_getValue("product_price_list", "price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											$GST = $product_detail['igst'];
											$totalprice = $p['qty'] * $unitprice;
											$totalprice = $this->db->rp_num($totalprice);
											// $original_price=$price_list_price;
											$final_price = $this->db->rp_num($totalprice);

											$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											$p_discount = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											// $discount = $p['discount'];
											$price_list_discount = $this->db->rp_num($p_discount);
											$discount_amount1 = ($p['original_price'] * $price_list_discount) / 100;
											$price_list_discounted_amount = $this->db->rp_num($discount_amount1);
											$price_list_discounted_price = $p['original_price'] - $price_list_discounted_amount;
										} else {
											$add_price_list_id = 0;
										}
									} else {
										$add_price_list_id = 0;
									}

									$rows 	= array(
										"quotation_id",
										"top_cat_id",
										"cat_id",
										"pro_id",
										"weight_id",
										"brand_id",
										"pro_name",
										"pro_description",
										"pro_qty",
										"remaining_qty",
										"inner_size",
										"outer_size",
										"box_qty",
										"cartoon_qty",
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
										"loose_qty",
										"cash_discount_amount",
										"additional_discount_amount",
										"igst_tax",
										"igst_amount",
										"taxable",
										"subtotal",
										"other_charge",
										"fright_charge",
										"hsn_code",
										"is_including",
										"item_order_unit",
										"order_qty",
										"order_item_brand_id",
									);
									$values = array(
										$quotation_id,
										$top_cat_id,
										$cat_id,
										$p['pid'],
										$p['weight_id'],
										$p['brand_id'],
										$p['item_name'],
										$p['pro_description'],
										$p['qty'],
										$p['qty'],
										$p['inner_size'],
										$p['outer_size'],
										$p['box_qty'],
										$p['cartoon_qty'],
										$unitprice,
										$original_price,
										$final_price,
										$user_discount,
										$unitprice_amt,
										$add_price_list_id,
										$price_list_price,
										($price_list_discounted_price) ? $price_list_discounted_price : 0,
										($price_list_discounted_amount) ? $price_list_discounted_amount : 0,
										($price_list_discount_type) ? $price_list_discount_type : 0,
										($price_list_discount) ? $price_list_discount : 0,
										isset($p['loose']) ? $p['loose'] : "0",
										$p['cd_discount'],
										$p['ad_discount'],
										$igst,
										$p['gst_amount_item'],
										$p['taxable_amount'],
										$p['sub_total'],
										$p['other_charge'],
										$p['fright_charge'],
										$hsn_code,
										$p['is_including'],
										isset($p['item_order_unit']) ? $p['item_order_unit'] : "0",
										isset($p['order_qty']) ? $p['order_qty'] : "0",
										isset($p['order_item_brand_id']) ? $p['order_item_brand_id'] : "0",
									);
									$total_qty += $p['qty'];
									$sub_total += $final_price;
									$item_id = $this->db->rp_insert("quotation_product_item", $values, $rows, 0);
								}
							}
						}
						$total_items = $this->db->rp_getTotalRecord("quotation_product_item", "quotation_id='" . $quotation_id . "' AND isDelete=0");
						if ($total_items != 0) {
							$reply = array("ack" => 1, "developer_msg" => "Quotation Added Successfully", "ack_msg" => "Quotation Added Successfully", "quotation_id" => $quotation_id);
							return $reply;
						} else {
							$reply = array("ack" => 0, "developer_msg" => "Quotation Item Not inserted", "ack_msg" => "Quotation Item Not inserted");
							return $reply;
						}
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Request Generated With Error");
						return $reply;
					}
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Request Not Generated", "ack_msg" => "Request Generated With Error");
					return $reply;
				}
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Customer Not Found!!", "ack_msg" => "Customer Not Found!!");
			return $reply;
		}
	}

	public function UpdateQuotation($detail, $products)
	{
		// print_r($detail); exit;
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0");
		if ($customer_d) {
			$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $detail['cid'] . "' AND isDelete=0");
			$where = "";
			if ($detail['sales_executive_id'] != "") {
				$where = " AND sales_id='" . $detail['sales_executive_id'] . "' ";
			} else {
				$detail['sales_executive_id'] = "";
				$where = " AND sales_id=0";
			}

			$check_cart_exist = $this->db->rp_getTotalRecord("quotation_detail", "id='" . $detail['quotation_id'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "'" . $where, 0);

			// echo "sdf";exit;
			// if($check_cart_exist!=0) //====> sales id change kari sake etle aa if comment kari hati
			// {
			if ($detail['quotation_id'] != 0) {
				$quotation_id = $detail['quotation_id'];
				$this->db->rp_update("quotation_detail", array("gst" => $detail['gstin']), "id='" . $quotation_id . "'", 0);
				if (!empty($products)) {
					// delete product
					$this->db->rp_delete("quotation_product_item", "quotation_id='" . $quotation_id . "'");
					// delete product

					// print_r($products);exit;
					foreach ($products as $p) {
						$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
						if ($product_detail) {
							$product_detail = mysqli_fetch_assoc($product_detail);
							$top_cat_id = $product_detail['tcid'];
							$cat_id = $product_detail['cid'];
							$hsn_code = $product_detail['hsn_code'];
							$igst = $product_detail['igst'];
							$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);

							if ($ctable_item_weight_detail) {
								$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
								$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
								$product_code = $ctable_item_weight_detail['catno'];

								if ($ctable_item_weight_detail['weight_id'] == -1) {
									$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (#" . stripslashes($product_code) . ")"));
								} else {
									$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")" . " (#" . stripslashes($product_code) . ")"));
								}
								$p['inner_size'] = $ctable_item_weight_detail['inner_size'];
								$p['outer_size'] = $ctable_item_weight_detail['outer_size'];
								/*$p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
									$p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];*/
								$p['box_qty'] = $p['box_qty'];
								$p['cartoon_qty'] = $p['cartoon_qty'];

								// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."'",0);
								$unitprice = $this->db->rp_num($p['price']);
								$GST = $product_detail['igst'];
								$totalprice = $p['qty'] * $unitprice;
								$totalprice = $this->db->rp_num($totalprice);
								$original_price = $p['original_price'];

								/*$unitprice_amt=0;*/
								$unitprice = $unitprice;
								/*$user_discount=0;
									$discount_amount=0;*/


								$user_discount = $p['discount'];
								if ($user_discount == 0) {
									$discount_amount = $this->db->rp_num($p['discount_amount']);
									if ($discount_amount == "") {
										$discount_amount = 0;
									}
								} else {
									$discount_amount = $this->db->rp_num(($p['original_price'] * $user_discount) / 100);
								}
								$unitprice_amt = $discount_amount;
								$final_price = $this->db->rp_num($totalprice);

								$price_list_price = 0;
								$price_list_discounted_price = 0;
								$price_list_discounted_amount = 0;
								$price_list_discount_type = 0;
								$price_list_discount = 0;

								if ($price_list_id != 0) {
									$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
									if ($check_product_in_list > 0) {
										$add_price_list_id = $price_list_id;
										$price_list_price = $this->db->rp_getValue("product_price_list", "price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

										$GST = $product_detail['igst'];
										$totalprice = $p['qty'] * $unitprice;
										$totalprice = $this->db->rp_num($totalprice);
										// $original_price=$price_list_price;
										$final_price = $this->db->rp_num($totalprice);

										$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

										$p_discount = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

										// $discount = $p['discount'];
										$price_list_discount = $this->db->rp_num($p_discount);
										$discount_amount1 = ($p['original_price'] * $price_list_discount) / 100;
										$price_list_discounted_amount = $this->db->rp_num($discount_amount1);
										$price_list_discounted_price = $p['original_price'] - $price_list_discounted_amount;
									} else {
										$add_price_list_id = 0;
									}
								} else {
									$add_price_list_id = 0;
								}

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
									"loose_qty",
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
									$top_cat_id,
									$cat_id,
									$p['pid'],
									$p['weight_id'],
									$p['item_name'],
									$p['pro_description'],
									$p['qty'],
									$p['qty'],
									$p['inner_size'],
									$p['outer_size'],
									$p['box_qty'],
									$p['cartoon_qty'],
									$unitprice,
									$original_price,
									$final_price,
									$user_discount,
									$unitprice_amt,
									$add_price_list_id,
									$price_list_price,
									($price_list_discounted_price) ? $price_list_discounted_price : 0,
									($price_list_discounted_amount) ? $price_list_discounted_amount : 0,
									($price_list_discount_type) ? $price_list_discount_type : 0,
									($price_list_discount) ? $price_list_discount : 0,
									isset($p['loose']) ? $p['loose'] : "0",
									$p['cd_discount'],
									$p['ad_discount'],
									$igst,
									$p['gst_amount_item'],
									$p['taxable_amount'],
									$p['sub_total'],
									$hsn_code,
									$p['other_charge'],
									$p['fright_charge'],
									$p['is_including'],
									isset($p['item_order_unit']) ? $p['item_order_unit'] : "0",
									isset($p['order_qty']) ? $p['order_qty'] : "0",
									isset($p['order_item_brand_id']) ? $p['order_item_brand_id'] : "0",
									// $p['fright_charge'],
								);
								$total_qty += $p['qty'];
								$sub_total += $final_price;
								$item_id = $this->db->rp_insert("quotation_product_item", $values, $rows, 0);
							}
						}
					}

					$total_items = $this->db->rp_getTotalRecord("quotation_product_item", "quotation_id='" . $quotation_id . "' AND isDelete=0");
					if ($total_items != 0) {
						$reply = array("ack" => 1, "developer_msg" => "Quotation Updated Successfully", "ack_msg" => "Quotation Updated Successfully", "quotation_id" => $quotation_id);
						return $reply;
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Quotation Item Not Updated", "ack_msg" => "Quotation Item Not Updated");
						return $reply;
					}
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Request Generated With Error");
					return $reply;
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Request Not Generated", "ack_msg" => "Request Generated With Error");
				return $reply;
			}
			// }
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Customer Not Found!!", "ack_msg" => "Customer Not Found!!");
			return $reply;
		}
	}
	public function PlaceOrder($detail)
	{
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0");
		$customer_r = mysqli_fetch_assoc($customer_d);

		$where = "";
		if ($detail['sales_executive_id'] != "") {
			$where = " AND sales_id='" . $detail['sales_executive_id'] . "' ";
		} else {
			$where = " AND sales_id=0";
		}
		$quotation_id = $this->db->rp_getValue("quotation_detail", "id", "customer_id='" . $detail['cid'] . "' AND sales_id='" . $detail['sales_executive_id'] . "' AND isDelete=0 AND status=-1" . $where, 0);
		if ($quotation_id != 0) {

			$order_items_r = $this->db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quotation_id . "' AND isDelete=0 ");
			if ($order_items_r) {
				$total_qty = 0;
				$sub_total = 0;
				while ($order_items_d = mysqli_fetch_assoc($order_items_r)) {

					$product_detail = $this->db->rp_getData("product", "*", "id='" . $order_items_d['pro_id'] . "'", "", "0");
					if ($product_detail) {
						$product_detail = mysqli_fetch_assoc($product_detail);
						$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $order_items_d['weight_id'] . "' AND product_id='" . $order_items_d['pro_id'] . "'", "", 0);
						if ($ctable_item_weight_detail) {
							$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
							$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
							$order_items_d['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")"));
							$order_items_d['inner_size'] = $ctable_item_weight_detail['inner_size'];
							$order_items_d['box_qty'] = $order_items_d['pro_qty'] / $ctable_item_weight_detail['inner_size'];
							// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$order_items_d['pro_id']."' AND weight_id='".$order_items_d['weight_id']."'",0);
							$GST = $product_detail['igst'];
							$unitprice = $this->db->rp_num($order_items_d['unitprice']);
							$totalprice = $order_items_d['pro_qty'] * $unitprice;
							$totalprice = $this->db->rp_num($totalprice);
							$original_price = $unitprice;
							$final_price = $totalprice;
							/*if($user_discount!=0 && $user_discount!="")
							{
								$unitprice_amt=$this->db->rp_num(($unitprice*$user_discount)/100);
								$unitprice=$this->db->rp_num($unitprice-$unitprice_amt);

								$discount_amount=$this->db->rp_num(($totalprice*$user_discount)/100);
								// $final_price=$this->db->rp_num($totalprice-$discount_amount);
								$final_price=$this->db->rp_num($order_items_d['pro_qty']*$unitprice);
							}
							else
							{
								$unitprice_amt=0;
								$unitprice=$unitprice;
								$user_discount=0;
								$discount_amount=0;
								$final_price=$this->db->rp_num($totalprice);
							}*/
							$total_qty += $order_items_d['pro_qty'];
							$sub_total += $final_price;
							//$item_id = $this->db->rp_insert("quotation_product_item",$values,$rows,0);
						}
					}
				}

				if ($detail['cash_discount_flag'] != 0 && $customer_r['cash_discount'] != "") {
					$cash_discount = 0;
					$cash_discount_amount = $this->db->rp_num(($sub_total * $customer_r['cash_discount']) / 100);
					if ($sub_total > $cash_discount_amount) {
						$sub_total = $this->db->rp_num($sub_total - $cash_discount_amount);
					} else {
						$sub_total = $this->db->rp_num($cash_discount_amount - $sub_total);
					}
				} else {
					$cash_discount_amount = 0;
					$cash_discount = 0;
					$sub_total = $this->db->rp_num($sub_total);
				}
				$gst_amount = $this->db->rp_num(($sub_total * $GST) / 100);
				$grand_total = $this->db->rp_num($sub_total + $gst_amount);
				$dt = date("Y-m-d");
				$isUpdated = $this->db->rp_update("quotation_detail", array("total_qty" => $total_qty, "subtotal" => $sub_total, "grand_total" => $grand_total, "cash_discount_amount" => $cash_discount_amount, "cash_discount" => $cash_discount, "booking" => $detail['booking'], "transport" => $detail['transport'], "igst_amount" => $gst_amount, "status" => 0, "quotation_date" => $dt, "class_id" => $detail['class_id'], "area_id" => $detail['area_id'], "dealer_id" => $detail['dealer_id'], "entry_flag" => $detail['entry_flag']), "id='" . $quotation_id . "'", 0);
				if ($isUpdated) {
					$customer_order_detail = $this->db->rp_getData("quotation_detail", "*", "id='" . $quotation_id . "'");
					if ($customer_order_detail) {
						$customer_order_detail = mysqli_fetch_assoc($customer_order_detail);
						$customer_order_items = $this->db->rp_getData("quotation_product_item", "*", "quotation_id='" . $quotation_id . "'", "", 0);
						if ($customer_order_items) {
							while ($customer_order_item = mysqli_fetch_assoc($customer_order_items)) {
								$customer_order_detail['order_items'][] = $customer_order_item;
							}
						}
						$result[] = $customer_order_detail;

						//Add Queue Email.......... 


						$dealer_email  = $this->db->rp_getValue("customer", "email", "id='" . $detail['cid'] . "'");
						//Dealer Email
						if ($dealer_email != "") {
							$Params['quotation_id'] = $quotation_id;
							$Params['text'] = "Create Quotation for." . $customer_r['company_name'];
							//$Body=$this->notification->getEmailBodyForOrders('CREATE_ORDER',$Params);

							//$this->system->AddEmailToQueue($detail['cid'],$dealer_email,$Body,date('Y-m-d H:i:s'),0,$quotation_id,"Order");
						}

						$admin_email  = $this->db->rp_getValue(CTABLE_ADMIN, "email", "id='1'");

						//Admin Email.....
						if ($admin_email != "") {
							$Params['quotation_id'] = $quotation_id;
							$Params['text'] = "Quotation Placed for." . $customer_r['company_name'];
							//$Body=$this->notification->getEmailBodyForOrders('CREATE_ORDER',$Params);

							//$this->system->AddEmailToQueue($detail['cid'],$admin_email,$Body,date('Y-m-d H:i:s'),0,$quotation_id,"Order");
						}
						// Customer Email.......
						if ($customer_r['email'] != "") {
							$Params['quotation_id'] = $quotation_id;
							$Params['text'] = "Thank you for your Quotation";
							//$Body=$this->notification->getEmailBodyForOrders('CREATE_ORDER',$Params);

							//$this->system->AddEmailToQueue($detail['cid'],$customer_r['email'],$Body,date('Y-m-d H:i:s'),0,$quotation_id,"Order");
						}
						$reply = array("ack" => 1, "developer_msg" => "Your Quotation is Placed Successfully", "ack_msg" => "Your Quotation is Placed Successfully", "result" => $result);
						return $reply;
					}
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Your Quotation is Not Placed", "ack_msg" => "Your Quotation is Not Placed");
					return $reply;
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Something went wrong!! Product Not in cart please check", "ack_msg" => "Something went wrong!! Product Not in cart please check");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "No Quotation Found", "ack_msg" => "No Quotation Found");
			return $reply;
		}
	}

	public function PlaceQuotationPanel($detail, $file)
	{
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0");
		$customer_r = mysqli_fetch_assoc($customer_d);

		$order_items_r = $this->db->rp_getData("quotation_product_item", "*", "quotation_id='" . $detail['quotation_id'] . "' AND isDelete=0 ");
		if ($order_items_r) {
			$total_qty = 0;
			$sub_total = 0;
			while ($order_items_d = mysqli_fetch_assoc($order_items_r)) {

				$product_detail = $this->db->rp_getData("product", "*", "id='" . $order_items_d['pro_id'] . "'", "", "0");
				if ($product_detail) {
					$product_detail = mysqli_fetch_assoc($product_detail);
					$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $order_items_d['weight_id'] . "' AND product_id='" . $order_items_d['pro_id'] . "'", "", 0);
					if ($ctable_item_weight_detail) {
						$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
						$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
						$order_items_d['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")"));
						$order_items_d['inner_size'] = $ctable_item_weight_detail['inner_size'];
						$order_items_d['box_qty'] = $order_items_d['pro_qty'] / $ctable_item_weight_detail['inner_size'];
						/*if($customer_r['type_of_executive']==7)	
						{
							$GST = 0.1;
						}
						else
						{*/
						$GST = $product_detail['igst'];
						//}
						$unitprice = $this->db->rp_num($order_items_d['unitprice']);
						$totalprice = $order_items_d['pro_qty'] * $unitprice;
						$totalprice = $this->db->rp_num($totalprice);
						$original_price = $unitprice;
						$final_price = $totalprice;

						$total_qty += $order_items_d['pro_qty'];
						$sub_total += $final_price;
						//$item_id = $this->db->rp_insert("quotation_product_item",$values,$rows,0);
					}
				}
				$tot_gst_amount += $order_items_d['igst_amount'];
			}

			if ($detail['cash_discount'] != "" || ($detail['cash_discount_amount'] != "" && $detail['cash_discount_amount'] != 0)) {
				$cash_discount = $detail['cash_discount'];
				if ($cash_discount) {
					$cash_discount_amount = $this->db->rp_num(($sub_total * $detail['cash_discount']) / 100);
				} else {
					$cash_discount_amount = $detail['cash_discount_amount'];
				}
				if ($sub_total > $cash_discount_amount) {
					$sub_total = $this->db->rp_num($sub_total - $cash_discount_amount);
				} else {
					$sub_total = $this->db->rp_num($cash_discount_amount - $sub_total);
				}
			} else {
				$cash_discount_amount = 0;
				$cash_discount = 0;
				$sub_total = $this->db->rp_num($sub_total);
			}


			if ($detail['additional_discount'] != "" || ($detail['additional_discount_amount'] != "" && $detail['additional_discount_amount'] != 0)) {
				$additional_discount = $detail['additional_discount'];
				if ($additional_discount) {
					$additional_discount_amount = $this->db->rp_num(($sub_total * $detail['additional_discount']) / 100);
				} else {
					$additional_discount_amount = $this->db->rp_num($detail['additional_discount_amount']);
				}
				if ($sub_total > $additional_discount_amount) {
					$sub_total = $this->db->rp_num($sub_total - $additional_discount_amount);
				} else {
					$sub_total = $this->db->rp_num($additional_discount_amount - $sub_total);
				}
			} else {
				$additional_discount_amount = 0;
				$additional_discount = 0;
				$sub_total = $this->db->rp_num($sub_total);
			}

			// echo $detail['gst_apply_flag']; exit;

			if ($detail['gst_apply_flag'] != 0) {
				$sub_total1 = $sub_total + $detail['transport_charge'] + $detail['packing_charge'];
				// $gst_amount = $this->db->rp_num(($sub_total1*$GST)/100);
				$gst_amount = $tot_gst_amount;
				$grand_total = $this->db->rp_num($sub_total1 + $gst_amount);
			} else {
				$sub_total1 = $sub_total + $detail['transport_charge'] + $detail['packing_charge'];
				$gst_amount = 0;
				$grand_total = $sub_total1;
			}

			if ($detail['tcs_apply_flag'] != 0) {
				// $sub_total1 = $sub_total+$detail['transport_charge']+$detail['packing_charge'];
				// $gst_amount = $this->db->rp_num(($sub_total1*$GST)/100);
				$tcs_amount = $this->db->rp_num(($grand_total * TCS_CHARGE_IN_PER) / 100);
				$grand_total = $this->db->rp_num($grand_total + $tcs_amount);
			} else {
				// $sub_total1 = $grand_total+$detail['transport_charge']+$detail['packing_charge'];
				$tcs_amount = 0;
				$grand_total = $grand_total;
			}
			// echo $gst_amount;
			// echo $grand_total; exit;

			/*$sub_total1 = $sub_total+$detail['transport_charge']+$detail['packing_charge'];
			$gst_amount=$this->db->rp_num(($sub_total1*$GST)/100);
			$grand_total=$this->db->rp_num($sub_total1+$gst_amount);*/
			//$tcs_amount = 0;
			$dt = date("Y-m-d");


			// if($_REQUEST['mode'] == "edit"){
			// 	$update_entry_flag = 1;
			// }
			// else{
			// 	$update_entry_flag = 1;	
			// }
			/*log entry*/
			$quotation_no = $this->db->rp_getValue("quotation_detail", "quotation_no", "id='" . $detail['quotation_id'] . "'");
			$module_name = "Quotation";
			$flag = "Web";
			if ($_REQUEST['mode'] == "add") {
				$log_description = $module_name . " " . $quotation_no . " Created By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
			} else {
				$log_description = $module_name . " " . $quotation_no . " Edited By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
			}
			/*log entry*/


			//"cash_discount_amount"=>$cash_discount_amount,"cash_discount"=>$cash_discount,"additional_discount_amount"=>$additional_discount_amount,"additional_discount"=>$additional_discount
			/*igst_amount"=>$gst_amount*/


			if (isset($file["attachment"])) {
				// print_r($file);exit();
				// $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["attachment"]["name"]);
				$extension = end($temp);

				$fileName 	= $this->db->clean($file["attachment"]["name"]);
				// echo "heelo";exit();
				if ($fileName != "") {
					$fileSize 	= round($file["attachment"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$image_path	= 'attachment' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= QUOTATION_ATTACHMENT_A . $image_path;
					$file['attachment']['tmp_name'];
					// print_r($filePath); exit;
					move_uploaded_file($file['attachment']['tmp_name'], $filePath);
					$image_path = $image_path;
					unset($old_image_path);
				} else {
					$image_path = $old_image_path;
					unset($old_image_path);
				}
			}

			$update_array = array("total_qty" => $total_qty, "subtotal" => $sub_total1, "grand_total" => $grand_total, "cash_discount_amount" => $detail['cash_discount_amount'], "cash_discount" => $detail['cash_discount'], "additional_discount_amount" => $detail['additional_discount_amount'], "additional_discount" => $detail['additional_discount'], "igst_amount" => $gst_amount, "status" => 1, "remarks" => $detail['remarks'], "reference_date" => $detail['reference_date'], "reference" => $detail['reference'], "attn" => $detail['attn'], "attn_no" => $detail['attn_no'], "attn_email" => $detail['attn_email'], "transport_charge" => $detail['transport_charge'], "terms_comdition" => $detail['terms_comdition'], "faithfully" => $detail['faithfully'], "vendor_code" => $detail['vendor_code'], "tendor_code" => $detail['tendor_code'], "tendor_no" => $detail['tendor_no'], "transport_name" => $detail['transport_name'], "transport_through" => $detail['transport_through'], "packing_charge" => $detail['packing_charge'], "shipping_address" => $detail['shipping_address'], "billing_address" => $detail['billing_address'], "roundoff" => $detail['round_off'], "grand_total_rounded" => round($grand_total), "tcs_per" => TCS_CHARGE_IN_PER, "tcs_amount" => $tcs_amount, "currency_code" => $detail['currency_code'], "update_entry_flag" => 1, "transport_charge_gst" => $detail['transport_charge_gst'], "packing_charge_gst" => $detail['packing_charge_gst'], "cd_gst" => $detail['cd_gst'], "ad_gst" => $detail['ad_gst'], "type_of_company" => $detail['type_of_company'], "terms_condition_id" => $detail['terms_condition_id'], "attachment" => $this->db->clean($image_path), "sales_id" => $detail['sales_executive_id']);

			$isUpdated = $this->db->rp_update("quotation_detail", $update_array, "id='" . $detail['quotation_id'] . "'", 0, $log_description, $flag, $module_name, "", "");
			if ($isUpdated) {

				$customer_id = $this->db->rp_getValue("quotation_detail", "customer_id", "id='" . $detail['quotation_id'] . "'", 0);
				$company_name = $this->db->rp_getValue("executive", "company_name", "id='" . $customer_id . "'", 0);
				$customer_name = $this->db->rp_getValue("executive", "cname", "id='" . $customer_id . "'", 0);

				if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 0) {
					if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) //sales executive 
					{
						$quotation_add_name = $this->db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
					}
					if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) //customer  
					{
						$quotation_add_name = $this->db->rp_getValue("executive", "cname", "isDelete=0 AND id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
					}
				} else {
					$quotation_add_name = "Admin";
				}

				// send sales executive notification added by shivani     
				$sales_id = $this->db->rp_getValue("quotation_detail", "sales_id", "id='" . $detail['quotation_id'] . "'");
				$quotation_no  = $this->db->rp_getValue("quotation_detail", "quotation_no", "id='" . $detail['quotation_id'] . "'");
				$notification_description = "Added Quotation with Quotation No " . $quotation_no . " by " . $quotation_add_name . " for " . $company_name . "-" . $customer_name;

				$this->objPushNotification->commonNotification($sales_id, $detail['quotation_id'], "quotation_detail", "Add Quotation", $notification_description, "sales_executive", "quotation");
				// send sales executive notification added by shivani 

				// send customer notification added by shivani
				$customer_id = $this->db->rp_getValue("quotation_detail", "customer_id", "id='" . $detail['quotation_id'] . "'");
				$this->objPushNotification->commonNotification($customer_id, $detail['quotation_id'], "quotation_detail", "Add Quotation", $notification_description, "customer", "quotation");
				// send customer notification added by shivani 

				// send customer upper chanel notification added by shivani 
				$customer_type  = $this->db->rp_getValue("quotation_detail", "customer_type", "id='" . $detail['quotation_id'] . "'");
				if ($customer_type == 2)  //distributor
				{
					$upper_chanel_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $customer_id . "'");
				} else if ($customer_type == 3) //retailer 
				{
					$upper_chanel_id = $this->db->rp_getValue("executive", "dealer_distributor_id", "id='" . $customer_id . "'");
				}
				$this->objPushNotification->commonNotification($upper_chanel_id, $detail['quotation_id'], "quotation_detail", "Add Quotation", $notification_description, "customer", "quotation");
				// send customer upper chanel notification added by shivani

				$reply = array("ack" => 1, "developer_msg" => "Your Order is Placed Successfully", "ack_msg" => "Your Order is Placed Successfully", "result" => $result);
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Your Order is Not Placed", "ack_msg" => "Your Order is Not Placed");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Something went wrong!! Product Not in cart please check", "ack_msg" => "Something went wrong!! Product Not in cart please check");
			return $reply;
		}
	}


	public function UpdateCartDiscount($detail, $discount)
	{
		if ($detail['mode'] == "edit") {
			$check_cart_exist = $this->db->rp_getTotalRecord("quotation_detail", "isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "'", 0);
		} else {
			$check_cart_exist = $this->db->rp_getTotalRecord("quotation_detail", "isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "' AND status=-1", 0);
		}
		if ($check_cart_exist != 0) {
			if (!empty($discount)) {
				foreach ($discount as $d) {
					$get_item = $this->db->rp_getData("quotation_product_item", "*", "isDelete=0 AND quotation_id='" . $detail['cart_id'] . "'", "", 0);
					while ($get_item_d = mysqli_fetch_assoc($get_item)) {
						if ($d['tcid'] == $get_item_d['top_cat_id'] && $d['cid'] == $get_item_d['cat_id']) {

							$discount_amount = ($get_item_d['original_price'] * $d['discount']) / 100;
							$unitprice  = $get_item_d['original_price'] - $discount_amount;
							$totalprice  = $get_item_d['pro_qty'] * $unitprice;
							$update = $this->db->rp_update("quotation_product_item", array("discount" => $d['discount'], "discount_amount" => $discount_amount, "unitprice" => $unitprice, "totalprice" => $totalprice), "quotation_id='" . $detail['cart_id'] . "' AND id='" . $get_item_d['id'] . "'", 0);
						}
					}
				}

				$reply = array("ack" => 1, "developer_msg" => "Discount Update Successfully", "ack_msg" => "Discount Update Successfully");
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Request Generated With Error Product Not Found");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Quotation Data Not Found!!", "ack_msg" => "Quotation Data Not Found!!");
			return $reply;
		}
	}

	public function UpdateToCart($detail, $products)
	{
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0");
		if ($customer_d) {
			$where = "";
			if ($detail['sales_executive_id'] != "") {
				$where = " AND sales_id='" . $detail['sales_executive_id'] . "' ";
			} else {
				$detail['sales_executive_id'] = "";
				$where = " AND sales_id=0";
			}
			$check_cart_exist = $this->db->rp_getTotalRecord("quotation_detail", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND id='" . $detail['quotation_id'] . "' " . $where, 0);
			if ($check_cart_exist != 0) {

				if (!empty($products)) {
					$this->db->rp_delete("quotation_product_item", "quotation_id='" . $detail['quotation_id'] . "'", 0);
					$customer_r = mysqli_fetch_assoc($customer_d);
					$price_list_id = $customer_r['price_list_id'];
					foreach ($products as $p) {
						$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
						if ($product_detail) {
							$product_detail = mysqli_fetch_assoc($product_detail);
							$top_cat_id = $product_detail['tcid'];
							$cat_id = $product_detail['cid'];
							$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);

							if ($ctable_item_weight_detail) {
								$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
								$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
								$product_code = $ctable_item_weight_detail['catno'];
								if ($ctable_item_weight_detail['weight_id'] == -1) {
									$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (#" . stripslashes($product_code) . ")"));
								} else {
									$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")" . " (#" . stripslashes($product_code) . ")"));
								}
								$p['inner_size'] = $ctable_item_weight_detail['inner_size'];
								$p['outer_size'] = $ctable_item_weight_detail['outer_size'];
								$p['box_qty'] = $p['box_qty'];
								$p['cartoon_qty'] = $p['cartoon_qty'];

								$unitprice = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);
								$unitprice = $this->db->rp_num($unitprice);
								$GST = $product_detail['igst'];
								$totalprice = $p['qty'] * $unitprice;
								$totalprice = $this->db->rp_num($totalprice);
								$original_price = $unitprice;

								$unitprice_amt = 0;
								$unitprice = $unitprice;
								$user_discount = 0;
								$discount_amount = 0;
								$final_price = $this->db->rp_num($totalprice);

								$price_list_price = 0;
								$price_list_discounted_price = 0;
								$price_list_discounted_amount = 0;
								$price_list_discount_type = 0;
								$price_list_discount = 0;

								/*if($price_list_id!=0)
								{
									$check_product_in_list=$this->db->rp_getTotalRecord("product_price_list","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'",0);
									if($check_product_in_list>0)
									{
										$add_price_list_id=$price_list_id;
										$price_list_price=$this->db->rp_getValue("product_price_list","price","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");											
										$unitprice=$this->db->rp_getValue("product_price_list","discounted_price","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");											
										$unitprice=$this->db->rp_num($unitprice);
										$price_list_discounted_price=$unitprice;

										$GST=$product_detail['igst'];
										$totalprice=$p['qty']*$unitprice;
										$totalprice=$this->db->rp_num($totalprice);
										$original_price=$price_list_price;
										$final_price=$this->db->rp_num($totalprice);

										$price_list_discount_type=$this->db->rp_getValue("product_price_list","discount_type","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");

										$discount=$this->db->rp_getValue("product_price_list","discount","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");
										$price_list_discount=$this->db->rp_num($discount);

										$discount_amount=$this->db->rp_getValue("product_price_list","discounted_amount","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");
										$price_list_discounted_amount=$this->db->rp_num($discount_amount);
										$user_discount=$discount;
										$unitprice_amt=$price_list_discounted_amount;
									}
									else
									{
										$add_price_list_id=0;
									}
								}
								else
								{
									$add_price_list_id=0;
								}*/

								$discount = $p['discount'];
								$discount_amount = ($original_price * $p['discount']) / 100;
								$unitprice  = $original_price - $discount_amount;
								$totalprice  = $p['qty'] * $unitprice;
								$add_price_list_id = 0;


								$rows 	= array(
									"quotation_id",
									"top_cat_id",
									"cat_id",
									"pro_id",
									"weight_id",
									"pro_name",
									"pro_qty",
									"remaining_qty",
									"inner_size",
									"outer_size",
									"box_qty",
									"cartoon_qty",
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
								);
								$values = array(
									$detail['quotation_id'],
									$top_cat_id,
									$cat_id,
									$p['pid'],
									$p['weight_id'],
									$p['item_name'],
									$p['qty'],
									$p['qty'],
									$p['inner_size'],
									$p['outer_size'],
									$p['box_qty'],
									$p['cartoon_qty'],
									$unitprice,
									$original_price,
									$totalprice,
									$discount,
									$discount_amount,
									$add_price_list_id,
									$price_list_price,
									$price_list_discounted_price,
									$price_list_discounted_amount,
									$price_list_discount_type,
									$price_list_discount,
								);
								$total_qty += $p['qty'];
								$sub_total += $totalprice;
								$item_id = $this->db->rp_insert("quotation_product_item", $values, $rows, 0);
							}
						}
					}

					if ($detail['cash_discount_flag'] != 0 && $customer_r['cash_discount'] != "") {
						$cash_discount = 0;
						$cash_discount_amount = $this->db->rp_num(($sub_total * $customer_r['cash_discount']) / 100);
						if ($sub_total > $cash_discount_amount) {
							$sub_total = $this->db->rp_num($sub_total - $cash_discount_amount);
						} else {
							$sub_total = $this->db->rp_num($cash_discount_amount - $sub_total);
						}
					} else {
						$cash_discount_amount = 0;
						$cash_discount = 0;
						$sub_total = $this->db->rp_num($sub_total);
					}
					$gst_amount = $this->db->rp_num(($sub_total * $GST) / 100);
					$grand_total = $this->db->rp_num($sub_total + $gst_amount);
					$dt = date("Y-m-d");
					$isUpdated = $this->db->rp_update("quotation_detail", array("total_qty" => $total_qty, "subtotal" => $sub_total, "grand_total" => $grand_total, "cash_discount_amount" => $cash_discount_amount, "cash_discount" => $cash_discount, "igst_amount" => $gst_amount, "status" => 0, "quotation_date" => $dt), "id='" . $detail['quotation_id'] . "'", 0);


					$total_items = $this->db->rp_getTotalRecord("quotation_product_item", "quotation_id='" . $detail['quotation_id'] . "' AND isDelete=0");
					if ($total_items != 0) {
						$reply = array("ack" => 1, "developer_msg" => "Quotation Added Successfully", "ack_msg" => "Quotation Added Successfully", "quotation_id" => $detail['quotation_id']);
						return $reply;
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Quotation Item Not inserted", "ack_msg" => "Quotation Item Not inserted");
						return $reply;
					}
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Request Generated With Error Product Not Found");
					return $reply;
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Cart data Not Found ", "ack_msg" => "Request Generated With Error Cart data  Not Found");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Customer Not Found!!", "ack_msg" => "Customer Not Found!!");
			return $reply;
		}
	}


	/*Revised Quotation*/
	public function GetRevisedQuotationDetail($detail)
	{

		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where, "", 0);
		$ctable_d = mysqli_fetch_assoc($ctable_r);
		// print_r($ctable_d);exit;
		$result = array();
		$result['customer_type']		= htmlentities($ctable_d['customer_type']);
		$result['dealer_id']		= htmlentities($ctable_d['customer_id']);
		$result['sales_executive_id']		= htmlentities($ctable_d['sales_executive_id']);
		$result['quotation_no']		= htmlentities($ctable_d['quotation_no']);
		$result['quotation_date']		= date("d-m-Y", strtotime($ctable_d['quotation_date']));
		$result['reference_date']		= ($ctable_d['reference_date'] != "0000-00-00") ? date("d-m-Y", strtotime($ctable_d['reference_date'])) : "";
		/* $result['total_amount']		= htmlentities($ctable_d['total_amount']);
		$result['total_qty']		= htmlentities($ctable_d['total_qty']);
		$result['grand_total']		= htmlentities($ctable_d['grand_total']);*/
		$result['remarks']			 = htmlentities($ctable_d['remarks']);
		$result['reference']		 = htmlentities($ctable_d['reference']);
		$result['attn']				 = htmlentities($ctable_d['attn']);
		$result['attn_no']			 = htmlentities($ctable_d['attn_no']);
		$result['attn_email']		 = htmlentities($ctable_d['attn_email']);
		$result['transport_charge']	 = htmlentities($ctable_d['transport_charge']);
		$result['vendor_code']		 = htmlentities($ctable_d['vendor_code']);
		$result['tendor_code']		 = htmlentities($ctable_d['tendor_code']);
		$result['tendor_no']		 = htmlentities($ctable_d['tendor_no']);
		$result['transport_name']	 = htmlentities($ctable_d['transport_name']);
		$result['transport_through'] = htmlentities($ctable_d['transport_through']);
		$result['packing_charge']    = htmlentities($ctable_d['packing_charge']);
		$result['shipping_address']  = htmlentities($ctable_d['shipping_address']);
		$result['billing_address']   = htmlentities($ctable_d['billing_address']);
		$result['currency_code']   = htmlentities($ctable_d['currency_code']);
		$result['igst_amount']   = htmlentities($ctable_d['igst_amount']);
		$result['cash_discount']   = htmlentities($ctable_d['cash_discount']);
		$result['cash_discount_amount']   = htmlentities($ctable_d['cash_discount_amount']);
		$result['additional_discount']   = htmlentities($ctable_d['additional_discount']);
		$result['additional_discount_amount']   = htmlentities($ctable_d['additional_discount_amount']);
		$result['tcs_amount']   = htmlentities($ctable_d['tcs_amount']);


		// Purchase Item

		$reply = array("ack" => 1, "developer_msg" => "Product Item detail fetched!!.", "ack_msg" => "Success! Product Item Edit Successfully.", "result" => $result);
		return $reply;
	}



	public function GetRevisedQuotationDetailItems($detail)
	{
		$where = "quotation_id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_item = $this->db->rp_getData("quotation_product_item", "*", $where, "", 0);
		if ($ctable_item) {
			while ($ctable_item_d = mysqli_fetch_array($ctable_item)) {
				$result_item = array();
				$result_item['product_id']		= htmlentities($ctable_item_d['pro_id']);
				$result_item['quotation_id']	= htmlentities($ctable_item_d['quotation_id']);
				$result_item['weight_id']		= htmlentities($ctable_item_d['weight_id']);
				$result_item['brand_id']		= htmlentities($ctable_item_d['brand_id']);
				$result_item['pro_description']		= htmlentities($ctable_item_d['pro_description']);
				$pro_name = $this->db->rp_getValue("product", "name", "id='" . $ctable_item_d['pro_id'] . "'");
				$size_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_d['weight_id'] . "'");
				$result_item['product_name'] = $size_name . " " . $pro_name . " ";
				// $result_item['product_name']	= htmlentities($ctable_item_d['pro_name']);
				//$result_item['product_code']	= htmlentities($ctable_item_d['product_code']);
				$result_item['qty']		= htmlentities($ctable_item_d['pro_qty']);
				$result_item['is_including']		= htmlentities($ctable_item_d['is_including']);
				$result_item['inner_size']		= htmlentities($ctable_item_d['inner_size']);
				$result_item['outer_size']		= htmlentities($ctable_item_d['outer_size']);
				$result_item['box']		= htmlentities($ctable_item_d['cartoon_qty']);
				$result_item['bag']		= $this->db->rp_num(htmlentities($ctable_item_d['box_qty']));
				$result_item['discount_per']		= $this->db->rp_num(htmlentities($ctable_item_d['discount']));
				$result_item['product_price'] = $this->db->rp_num(htmlentities($ctable_item_d['unitprice']));
				$result_item['original_price'] = $this->db->rp_num(htmlentities($ctable_item_d['original_price']));
				$result_item['product_total']	= $this->db->rp_num(htmlentities($ctable_item_d['totalprice']));
				$result_item['discount_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['discount_amount']));
				$result_item['cat_no']		    = $this->db->rp_getValue("product_weight_price", "catno", "product_id='" . $result_item['product_id'] . "'");
				$result_item['hsn_code']         = $this->db->rp_getValue("product", "hsn_code", "id='" . $result_item['product_id'] . "' AND isDelete=0");
				$result_item['stock']				= $this->db->rp_getValue("product_weight_price", "stock_qty", "product_id='" . $result_item['product_id'] . "'", 0);

				$cid = $this->db->rp_getValue("quotation_detail", "customer_id", "id='" . $ctable_item_d['quotation_id'] . "' AND isDelete=0", 0);
				$last_quotation_id = $this->db->rp_getValue("quotation_detail", "id", "customer_id='" . $cid . "' AND isDelete=0 ORDER BY id DESC", 0);

				$result_item['last_quotation_price'] = $this->db->rp_getValue("quotation_product_item", "original_price", "quotation_id='" . $last_quotation_id . "' AND isDelete=0");
				$result_item['top_cat_name'] = $this->db->rp_getValue("top_category_master", "name", "id='" . $ctable_item_d['top_cat_id'] . "' AND isDelete=0", 0);
				$result_item['category_name'] = $this->db->rp_getValue("category_master", "name", "id='" . $ctable_item_d['cat_id'] . "' AND isDelete=0", 0);

				$result[] = $result_item;
			}
			$reply = array("ack" => 1, "developer_msg" => "Product Item detail fetched!!.", "ack_msg" => "Success! Update Product Item Successfully.", "result" => $result);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Update not fetched!!.", "ack_msg" => "Success! Update Failed");
			return $reply;
		}
	}
	/*Revised Quotation*/


	/*for api function*/
	public function AddQuotationApi($detail, $body)
	{
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0", "", 0);
		if ($customer_d) {
			$where = "";
			if ($detail['sales_executive_id'] != "") {
				$where = " AND sales_id='" . $detail['sales_executive_id'] . "' ";
			} else {
				$detail['sales_executive_id'] = "";
				$where = " AND sales_id=0";
			}
			if ($detail['order_id'] != "") {
				$check_cart_exist = $this->db->rp_getTotalRecord("cart_detail", "id='" . $detail['order_id'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "'" . $where, 0);
			} else {
				if ($detail['inquiry_id'] != "") {
					$check_cart_exist = $this->db->rp_getTotalRecord("cart_detail", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND lead_id='" . $detail['inquiry_id'] . "' AND sales_id='" . $detail['sales_executive_id'] . "' AND status=-1 " . $where, 0);
				} else {
					$check_cart_exist = $this->db->rp_getTotalRecord("cart_detail", "customer_id='" . $detail['cid'] . "' AND isDelete=0 cart_type=1 AND sales_id='" . $detail['sales_executive_id'] . "' AND status=-1 " . $where, 0);
				}
			}
			//echo $check_cart_exist; exit;
			if ($check_cart_exist != 0) {
				// already cart exist
				if ($detail['order_id'] != "") {
					$order_id = $detail['order_id'];
				} else {
					$order_id = $this->db->rp_getValue("cart_detail", "id", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND status=-1" . $where, 0);
				}


				$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
				if ($order_id != 0) {
					if (!empty($body)) {
						$total_items = $this->db->rp_getTotalRecord("cart_item", "order_id='" . $order_id . "'");
						foreach ($body as $p) {
							// $product_detail=$this->db->rp_getData("product","*","id='".$p['pid']."'","","0");
							//check item already in cart or not
							$check_item_in_cart = $this->db->rp_getValue("cart_item", "id", "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");


							if ($check_item_in_cart != 0 && $check_item_in_cart != "") {
								// update qty of that item
								$pro_r = $this->db->rp_getData("cart_item", "*", "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");
								$pro_d = mysqli_fetch_assoc($pro_r);
								//$update_qty=$p['qty']+$pro_d['pro_qty'];
								$update_qty = $p['qty'];
								$pro_d['unitprice'] = $this->db->rp_num($pro_d['unitprice']);
								$update_totalprice = $this->db->rp_num($update_qty * $pro_d['unitprice']);

								/*$update_box_qty=$this->db->rp_num($update_qty/$pro_d['inner_size']);
								$update_cartoon_qty=$this->db->rp_num($update_box_qty/$pro_d['outer_size']);*/

								// $update_box_qty=$p['box_qty'];
								// $update_cartoon_qty=$p['cartoon_qty'];

								$update_box_qty = $p['box_qty'] + $pro_d['box_qty'];
								$update_cartoon_qty = $p['cartoon_qty'] + $pro_d['cartoon_qty'];

								$update_item = array(
									"pro_qty" => $update_qty,
									"remaining_qty" => $update_qty,
									"totalprice" => $update_totalprice,
									"box_qty" => $update_box_qty,
									"cartoon_qty" => $update_cartoon_qty,
									"modified_date" => date("Y-m-d H:i:s"),
								);

								$isUpdate = $this->db->rp_update("cart_item", $update_item, "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0", 0);
								/*$reply=array("ack"=>1,"developer_msg"=>"Product Updated Successfully","ack_msg"=>"Product Updated Successfully","order_id"=>$order_id);*/
							} else {
								// insert new item
								$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
								if ($product_detail) {
									$product_detail = mysqli_fetch_assoc($product_detail);

									$top_cat_id = $product_detail['tcid'];
									$cat_id = $product_detail['cid'];
									$hsn_code = $product_detail['hsn_code'];
									$igst = $product_detail['igst'];

									$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);
									$user_discount = $this->db->rp_getValue("price_table", "discount", "tcid='" . $product_detail['tcid'] . "' AND uid='" . $detail['cid'] . "' AND isDelete=0");
									if ($ctable_item_weight_detail) {
										$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
										$product_code = $ctable_item_weight_detail['catno'];
										$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");

										if ($ctable_item_weight_detail['weight_id'] == -1) {
											$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (#" . stripslashes($product_code) . ")"));
										} else {
											$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")" . " (#" . stripslashes($product_code) . ")"));
										}
										$p['inner_size'] = $ctable_item_weight_detail['inner_size'];
										$p['outer_size'] = $ctable_item_weight_detail['outer_size'];

										/*$p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
										$p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];*/

										$p['box_qty'] = $p['box_qty'];
										$p['cartoon_qty'] = $p['cartoon_qty'];

										$unitprice = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);
										$unitprice = $this->db->rp_num($unitprice);
										$type_of_executive = $this->db->rp_getValue("executive", "type_of_executive", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
										if ($type_of_executive == "8") {

											$GST = 0.1;
										} else {

											$GST = $product_detail['igst'];
										}
										$totalprice = $p['qty'] * $unitprice;
										$totalprice = $this->db->rp_num($totalprice);
										$Newtotalprice = $p['qty'] * $unitprice;
										$original_price = $unitprice;
										$taxable_amount = $Newtotalprice;
										$item_gst_amount1 = (($taxable_amount * $GST) / 100);
										$sub_total = ($taxable_amount + $item_gst_amount1);

										$unitprice_amt = 0;
										$unitprice = $unitprice;
										$user_discount = 0;
										$discount_amount = 0;
										$final_price = $this->db->rp_num($totalprice);

										$price_list_price = 0;
										$price_list_discounted_price = 0;
										$price_list_discounted_amount = 0;
										$price_list_discount_type = 0;
										$price_list_discount = 0;

										if ($price_list_id != 0) {
											$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
											if ($check_product_in_list > 0) {
												$add_price_list_id = $price_list_id;
												$price_list_price = $this->db->rp_getValue("product_price_list", "price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
												$unitprice = $this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
												$unitprice = $this->db->rp_num($unitprice);
												$price_list_discounted_price = $unitprice;

												//$GST=$product_detail['igst'];
												$totalprice = $p['qty'] * $unitprice;
												$totalprice = $this->db->rp_num($totalprice);
												$original_price = $price_list_price;
												$final_price = $this->db->rp_num($totalprice);


												$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

												$discount = $this->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
												$price_list_discount = $this->db->rp_num($discount);

												$discount_amount = $this->db->rp_getValue("product_price_list", "discounted_amount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
												$price_list_discounted_amount = $this->db->rp_num($discount_amount);

												$user_discount = $discount;
												$unitprice_amt = $price_list_discounted_amount;
											} else {
												$add_price_list_id = 0;
											}
										} else {
											$add_price_list_id = 0;
										}
										$rows 	= array(
											"order_id",
											"top_cat_id",
											"cat_id",
											"pro_id",
											"weight_id",
											"pro_name",
											"pro_qty",
											"remaining_qty",
											"inner_size",
											"outer_size",
											"box_qty",
											"cartoon_qty",
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
										);
										$values = array(
											$order_id,
											$top_cat_id,
											$cat_id,
											$p['pid'],
											$p['weight_id'],
											trim($p['item_name']),
											$p['qty'],
											$p['qty'],
											$p['inner_size'],
											$p['outer_size'],
											$p['box_qty'],
											$p['cartoon_qty'],
											$unitprice,
											$original_price,
											$final_price,
											$user_discount,
											$unitprice_amt,
											$add_price_list_id,
											$price_list_price,
											$price_list_discounted_price,
											$price_list_discounted_amount,
											$price_list_discount_type,
											$price_list_discount,
											0,
											0,
											$igst,
											$item_gst_amount1,
											$taxable_amount,
											$sub_total,
											$hsn_code,
											0,
											0,
										);
										$total_qty += $p['qty'];
										$sub_total += $final_price;
										$item_id = $this->db->rp_insert("cart_item", $values, $rows, 0);
										/*if($item_id!=0)
										{
											$reply=array("ack"=>1,"developer_msg"=>"Product added Successfully","ack_msg"=>"Product added Successfully","order_id"=>$order_id);
										}
										else
										{
											$reply=array("ack"=>0,"developer_msg"=>"Product added Failed","ack_msg"=>"Product added Failed");
										}*/
									}
								}
								/*else
								{
									$reply=array("ack"=>0,"developer_msg"=>"Request Generated With Error Product Not Found ","ack_msg"=>"Request Generated With Error Product Not Found");
								}*/
							}
						}
					}
					/*else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Request Generated With Error Product Not Found ","ack_msg"=>"Request Generated With Error Product Not Found");
					return $reply;
				}*/
					$reply = array("ack" => 1, "developer_msg" => "Added Successfully", "ack_msg" => "Added Successfully", "order_id" => $order_id);

					return $reply;
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Added Failed", "ack_msg" => "Added Failed", "order_id" => $order_id);
				}
			} else {
				//echo "h";exit;

				// create new cart
				$customer_r = mysqli_fetch_assoc($customer_d);
				$price_list_id = $customer_r['price_list_id'];

				/*if($detail['revised_quotation_main_id']==0)
				{
					// $quotation_no=$this->db->getLastInsertId("quotation_detail");
					$quotation_no=$this->db->rp_getValue("cart_detail","MAX(id)","revised_quotation_main_id=0 AND isDelete = 0 AND isActive = 1");
					$quotation_no=$quotation_no+1;
					$quotation_no=DEALER_QUOTATION_NO.str_pad($quotation_no, 2, '0', STR_PAD_LEFT);
				}
				else
				{
					$quotation_no = $this->db->rp_getValue("cart_detail","quotation_no","id='".$detail['revised_quotation_main_id']."'");
					$totalCount = $this->db->rp_getTotalRecord("cart_detail","revised_quotation_main_id='".$detail['revised_quotation_main_id']."'");
					$totalCount = $totalCount+1;

					$new_quotation_no = "R".$totalCount." - ".$quotation_no;
					$quotation_no = $new_quotation_no;
				}
				*/

				$created_date	= date('Y-m-d H:i:s');

				$rows 	= array(
					"lead_id",
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
					"transport_charge",
					"terms_comdition",
					"faithfully",
					"vendor_code",
					"tendor_code",
					"transport_name",
					"transport_through",
					"packing_charge",
					"shipping_address",
					"billing_address",
					"gst",
					"cart_type",
					"email",
					"type_of_company",
					"terms_condition_id"
				);

				// print_r($customer_r);exit();
				$values = array(
					$detail['inquiry_id'],
					$customer_r['id'],
					$customer_r['dealer_distributor_id'],
					$customer_r['super_stockist_id'],
					$customer_r['cname'],
					$customer_r['client_code'],
					$customer_r['customer_flag'],
					$customer_r['company_name'],
					$customer_r['type_of_executive'],
					$customer_r['mobile_no1'],
					addslashes($customer_r['address']),
					$customer_r['city'],
					$customer_r['state'],
					$customer_r['country'],
					"-1",
					$detail['sales_executive_id'],
					"",
					$this->db->clean($detail['terms_comdition']),
					$detail['faithfully'],
					"",
					"",
					$customer_r['transporter_id'],
					$customer_r['transport_by_id'],
					"",
					$this->db->clean($detail['shipping_address']),
					$this->db->clean($detail['billing_address']),
					$detail['gst_no'],
					"1",
					$customer_r['email'],
					$this->db->clean($detail['type_of_company']),
					$this->db->clean($detail['terms_condition_id'])
				);

				/*log entry*/
				$sales_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $detail['sales_executive_id'] . "'", 0);
				$module_name = "Quotation";
				$flag = "Application";
				$log_description = $module_name . " Create From Lead #INQ" . $detail['inquiry_id'] . " Created By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				/*log entry*/

				$quotation_id = $this->db->rp_insert(
					"cart_detail",
					$values,
					$rows,
					0,
					$log_description,
					$flag,
					$module_name,
					$detail['sales_executive_id'],
					$customer_r['id']
				);
				if ($quotation_id != 0) {
					if (!empty($body)) {
						//$products 	= ($body!="")?(array)json_decode($body,true):array();
						for ($i = 0; $i < sizeof($body); $i++) {
							$p = $body[$i];
							$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
							if ($product_detail) {
								$product_detail = mysqli_fetch_assoc($product_detail);
								$top_cat_id = $product_detail['tcid'];
								$cat_id = $product_detail['cid'];
								$hsn_code = $product_detail['hsn_code'];
								$igst = $product_detail['igst'];
								$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);

								if ($ctable_item_weight_detail) {
									$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
									$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
									$product_code = $ctable_item_weight_detail['catno'];

									if ($ctable_item_weight_detail['weight_id'] == -1) {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (#" . stripslashes($product_code) . ")"));
									} else {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")" . " (#" . stripslashes($product_code) . ")"));
									}
									$p['inner_size'] = $ctable_item_weight_detail['inner_size'];
									$p['outer_size'] = $ctable_item_weight_detail['outer_size'];
									$p['box_qty']    = $p['box_qty'];
									$p['cartoon_qty'] = $p['cartoon_qty'];

									// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."'",0);

									$unitprice = $p['price'];
									$unitprice = $this->db->rp_num($unitprice);

									$type_of_executive = $this->db->rp_getValue("executive", "type_of_executive", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
									if ($type_of_executive == "8") {

										$GST = 0.1;
									} else {

										$GST = $product_detail['igst'];
									}
									//$GST = $product_detail['igst'];
									$totalprice = $p['qty'] * $unitprice;
									$totalprice = $this->db->rp_num($totalprice);
									$Newtotalprice = $p['qty'] * $unitprice;
									$original_price = $p['original_price'];
									$taxable_amount = $Newtotalprice;
									$item_gst_amount1 = (($taxable_amount * $GST) / 100);
									$sub_total = ($taxable_amount + $item_gst_amount1);

									//$unitprice_amt=0;
									$unitprice = $unitprice;
									/*$user_discount=0;
									$discount_amount=0;*/
									$user_discount = $p['discount'];
									$discount_amount = ($p['original_price'] * $user_discount) / 100;
									$unitprice_amt = $discount_amount;
									$final_price = $this->db->rp_num($totalprice);

									$price_list_price = 0;
									$price_list_discounted_price = 0;
									$price_list_discounted_amount = 0;
									$price_list_discount_type = 0;
									$price_list_discount = 0;

									if ($price_list_id != 0) {
										$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
										if ($check_product_in_list > 0) {
											$add_price_list_id = $price_list_id;
											$price_list_price = $this->db->rp_getValue("product_price_list", "price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
											// $unitprice=$this->db->rp_getValue("product_price_list","discounted_price","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");											
											$unitprice = $this->db->rp_num($unitprice);
											$price_list_discounted_price = $unitprice;

											//$GST=$product_detail['igst'];
											$type_of_executive = $this->db->rp_getValue("executive", "type_of_executive", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
											if ($type_of_executive == "8") {

												$GST = 0.1;
											} else {

												$GST = $product_detail['igst'];
											}
											$totalprice = $p['qty'] * $unitprice;
											$totalprice = $this->db->rp_num($totalprice);
											// $original_price=$price_list_price;
											$final_price = $this->db->rp_num($totalprice);

											$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											//$discount=$this->db->rp_getValue("product_price_list","discount","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");
											$discount = ['discount'];
											$price_list_discount = $this->db->rp_num($discount);
											// $discount_amount=$this->db->rp_getValue("product_price_list","discounted_amount","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");
											$discount_amount = ($p['original_price'] * $discount) / 100;
											$price_list_discounted_amount = $this->db->rp_num($discount_amount);
											$user_discount = $discount;
											$unitprice_amt = $price_list_discounted_amount;
										} else {
											$add_price_list_id = 0;
										}
									} else {
										$add_price_list_id = 0;
									}

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
									);
									$values = array($quotation_id, $top_cat_id, $cat_id, $p['pid'], $p['weight_id'], $p['item_name'], $p['item_remark'], $p['qty'], $p['qty'], $p['inner_size'], $p['outer_size'], $p['box_qty'], $p['cartoon_qty'], $unitprice, $original_price, $final_price, $user_discount, $unitprice_amt, $add_price_list_id, $price_list_price, $price_list_discounted_price, $price_list_discounted_amount, $price_list_discount_type, $price_list_discount, 0, 0, $igst, $item_gst_amount1, $taxable_amount, $sub_total, $hsn_code, 0, 0,);
									$total_qty += $p['qty'];
									$sub_total += $final_price;
									$item_id = $this->db->rp_insert("cart_item", $values, $rows, 0);
								}
							}
						}
						$total_items = $this->db->rp_getTotalRecord("cart_item", "order_id='" . $quotation_id . "' AND isDelete=0");
						/*if($total_items!=0)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Quotation Added Successfully","ack_msg"=>"Quotation Added Successfully","order_id"=>$quotation_id);
							return $reply;
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Quotation Item Not inserted","ack_msg"=>"Quotation Item Not inserted");
							return $reply;
						}*/
					}

					$reply = array("ack" => 1, "developer_msg" => "Quotation Added Successfully", "ack_msg" => "Quotation Added Successfully", "order_id" => $quotation_id);
					return $reply;
					/*else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Request Generated With Error Product Not Found ","ack_msg"=>"Request Generated With Error");
						return $reply;
					}*/
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Request Not Generated", "ack_msg" => "Request Generated With Errord");
					return $reply;
				}
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Customer Not Found!!", "ack_msg" => "Customer Not Found!!");
			return $reply;
		}
	}
	/*for api function*/

	public function DownloadQuotation($id)
	{
		//$customer_id=$this->db->rp_getValue("invoice_new","id","id='".$id."'",0);

		if ($id) {

			$count = $this->db->rp_getTotalRecord("quotation_detail", "id='" . $id . "'", 0);

			if ($count > 0) {

				//$d=file_get_contents(ADMINSITEURL.'order_view_new.php?order_id='.$order_id.'');
				//$d.=$string;

				// $d = file_get_contents(ADMINSITEURL_STATIC . 'bbsales_tracking/quotation_view_new_quotation_new_1.php?quotation_id=' . $id . '');
				
				$body_url = ADMINSITEURL . 'quotation_view_new_quotation_new_1.php?quotation_id=' . $id;
				$d = @file_get_contents($body_url);
				if(empty($d)) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $body_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					$d = curl_exec($ch);
					curl_close($ch);
				}
				//$d=file_get_contents(ADMINSITEURL.'quotation_view_new_quotation.php?quotation_id='.$id.'');
				// $d=file_get_contents(ADMINSITEURL.'quotation_view_new_quotation_new.php?quotation_id='.$id.'');
				//$d.=$string;
				//print_r($d); exit;
				require('../bbsales_tracking/mpdf60/mpdf.php');

				$mpdf = new mPDF(
					'',    // mode - default ''

					'A4',    // format - A4, for example, default ''

					15,     // font size - default 0

					'sans-serif',    // default font family

					1,    // margin_left

					3,    // margin right

					3,     // margin top

					3,    // margin bottom

					0,     // margin header

					0,     // margin footer

					'P'
				);  // L - landscape, P - portrait
				$mpdf->autoScriptToLang = true;
				$mpdf->baseScript = 1; // Use Gujarati script
				$mpdf->autoLangToFont = true;
				$mpdf->WriteHTML($d);

				/*LOG eNTRY*/
				$sales_id = $this->db->rp_getValue("quotation_detail", "sales_id", "id='" . $id . "'", 0);
				$sales_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'", 0);
				$customer_id = $this->db->rp_getValue("quotation_detail", "customer_id", "id='" . $id . "'");
				$quotation_no = $this->db->rp_getValue("quotation_detail", "quotation_no", "id='" . $id . "'");

				$last_id = $order_id;
				$flag = "Application";
				$ctable = "quotation_detail";
				$module_name = "Quotation";
				$log_description = $module_name . " " . $quotation_no . " PDF Download By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				$this->db->insertLog($ctable, $last_id, "insert", "", $insert, 0, $log_description, $flag, $module_name, $sales_id, $customer_id);
				/*LOG eNTRY*/

				$uname	= str_replace(" ", "-", stripslashes($this->db->rp_getValue("quotation_detail", "company_name", "id='" . $id . "'", 0)));
				$quotation_no	= str_replace("/", "-", stripslashes($this->db->rp_getValue("quotation_detail", "quotation_no", "id='" . $id . "'", 0)));


				//$fileName = "Quotation_".SITENAME."_".date('d_m_Y')."_".$quotation_no."_".$uname.'.pdf'; 
				$fileName = date('d_m_Y') . "_" . "Quotation_" . $quotation_no . 'pdf';


				if (!is_dir($fileName)) {

					mkdir(ORDERS_PDF . $fileName);
				}

				$pdf_file_path	= ORDERS_PDF . $fileName . "/" . $fileName . '.pdf';



				if (file_exists($pdf_file_path)) {

					unlink($pdf_file_path);
				}

				$mpdf->Output($pdf_file_path);

				$file_path = $pdf_file_path;

				// echo $file_path;exit;
				$result = array();
				$result['pdf'] = ADMINSITEURL . "pdf/orders/" . $fileName . "/" . $fileName . '.pdf';


				$reply = array("ack" => 1, "developer_msg" => "Quotation PDF Generate Successfully", "ack_msg" => "Quotation PDF Generate Successfully", "result" => $result);
				// echo $reply;exit;
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Quotation PDF Not Generate!!", "ack_msg" => "Quotation PDF Not Generate!!");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Invoice No Require!!", "ack_msg" => "Invoice No Require!!");
			return $reply;
		}
	}
}
