<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
require_once("class.system.php");
require_once("product.class.php");
require_once("push_notification.class.php");

class Order extends Functions
{
	public $db, $log, $product;
	public $ctable = "orders";
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
		print_r($_REQUEST);
		exit;
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
		//------for Insert Order No of Customer---------//
		$sales_type = $customer_r['type_of_executive'];
		if ($sales_type == 'super_stockist') {
			$value = $this->db->getlastInsertId($this->ctable);
			$order_no = SS_ORDER_NO . str_pad($value, 3, '0', STR_PAD_LEFT);
		} else if ($sales_type == 'dealer') {
			$value = $this->db->getlastInsertId($this->ctable);
			$order_no = DEALER_ORDER_NO . str_pad($value, 3, '0', STR_PAD_LEFT);
		}
		//----Insert Data In Orders Table ----------------//				
		$adate	= date('Y-m-d');
		$modify_date	= date('Y-m-d H:i:s');
		$rows 	= array(
			"order_no",
			"customer_id",
			"customer_name",
			"company_name",
			"customer_type",
			"contact_number",
			"address",
			"city",
			"state",
			"country",
			"email",
			"order_date",
			"modify_date"
		);
		$values = array(
			$order_no,
			$cid,
			$customer_r['cname'],
			$customer_r['company_name'],
			$customer_r['type_of_executive'],
			$customer_r['phone'],
			$customer_r['address'],
			$customer_r['city'],
			$customer_r['state'],
			$customer_r['country'],
			$customer_r['email'],
			$adate,
			$modify_date
		);
		$order_id = $this->db->rp_insert($this->ctable, $values, $rows, 0);
		if ($order_id != 0) {
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
						"order_id",
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
						$order_id,
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
					$item_id = $this->db->rp_insert("order_product_item", $values, $rows, 0);
				}
				// Final total calculations (amount,qty update in main orders table after inserting product item)
				$total = $total_final;

				$rows 	= array(
					"total_qty"				=> $total_qty,
					"total_amount"          => $total,
					"grand_total"			=> $total,
				);
				$where	= "id='" . $order_id . "'";
				$order = $this->db->rp_update($this->ctable, $rows, $where, 0);

				$reply = array("ack" => 1, "developer_msg" => "Product Item Order Added.", "ack_msg" => "Success! Product Item Order Successfully.", "type" => $order_id);
				return $reply;
			}
			$reply = array("ack" => 1, "developer_msg" => "Product Item Order Added.", "ack_msg" => "Success! Product Item Order Successfully.");
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
		$order_id = $_REQUEST['id'];
		$error = array();
		$isError = false;
		$modify_date	= date('Y-m-d H:i:s');
		extract($detail);
		for ($i = 0; $i < sizeof($item); $i++) {
			$current_item = $item[$i];
			$pro_id     = $current_item['product_id'];
			$new_order_qty 		=  $current_item['qty'];
			$ordered_item_info = $this->db->rp_getData("order_product_item", "*", "pro_id='" . $current_item['product_id'] . "' AND weight_id='" . $current_item['weight_id'] . "' AND order_id='" . $order_id . "'", "", 0);

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
			$this->db->rp_delete("order_product_item", "order_id='" . $_REQUEST['id'] . "'", 0);
			for ($i = 0; $i < sizeof($item); $i++) {
				$current_item = $item[$i];
				$total = $current_item['price'] * $current_item['qty'];
				$adate	= date('Y-m-d H:i:s');
				$price = $current_item['price'];
				$where = "pid='" . $current_item['product_id'] . "' AND weight_id='" . $current_item['weight_id'] . "' AND order_id='" . $order_id . "' AND isDelete=0 GROUP BY pid";
				$dispatch_r = $this->db->rp_getData("dispatch_map_order", "SUM(qty) as dispatched_qty,pid", $where, "pid ASC ", 0);
				if ($dispatch_r) {
					$dispatch_d = mysqli_fetch_assoc($dispatch_r);
				} else {
					$dispatch_d['dispatched_qty'] = 0;
				}
				$remaining_qty = $current_item['qty'] - $dispatch_d['dispatched_qty'];
				$rows 	= array(
					"order_id",
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
					$order_id,
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
				$item_id = $this->db->rp_insert("order_product_item", $values, $rows, 0);

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

			$where	= "id='" . $order_id . "'";
			$orderUpdated = $this->db->rp_update($this->ctable, $rows, $where, 0);
			if ($orderUpdated) {
				$reply = array("ack" => 1, "developer_msg" => "Product Item Order Updated.", "ack_msg" => "Success! Product Item Order Update Successfully.", "type" => $order_id);
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
	public function GetOrder($detail)
	{
		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where, 0, 0);
		$ctable_d = mysqli_fetch_array($ctable_r);

		$result = array();
		$result['customer_type']		= htmlentities($ctable_d['customer_type']);
		$result['customer_id']		= htmlentities($ctable_d['customer_id']);
		$result['order_no']		= htmlentities($ctable_d['order_no']);
		$result['order_date']		= date("d-m-Y", strtotime($ctable_d['order_date']));
		$result['total_amount']		= htmlentities($ctable_d['total_amount']);
		$result['total_qty']		= htmlentities($ctable_d['total_qty']);
		$result['grand_total']		= htmlentities($ctable_d['grand_total']);
		$result['remarks']		= htmlentities($ctable_d['remarks']);
		$result['chalan_no']		= htmlentities($ctable_d['chalan_no']);
		$result['po_no']		= htmlentities($ctable_d['po_no']);
		$result['po_date']		= htmlentities(date('d-m-Y', strtotime($ctable_d['po_date'])));
		$result['terms_comdition']		= htmlentities($ctable_d['terms_comdition']);
		$result['faithfully']		= htmlentities($ctable_d['faithfully']);
		$result['transport_name']		= htmlentities($ctable_d['transport_name']);
		$result['transport_through']		= htmlentities($ctable_d['transport_through']);
		$result['transport_charge']		= htmlentities($ctable_d['transport_charge']);
		$result['shipping_address']		= htmlentities($ctable_d['shipping_address']);
		$result['billing_address']		= htmlentities($ctable_d['billing_address']);
		$result['packing_charge']		= htmlentities($ctable_d['packing_charge']);
		$result['vendor_code']		= htmlentities($ctable_d['vendor_code']);
		$result['tendor_code']		= htmlentities($ctable_d['tendor_code']);
		$result['name_gstin']		= htmlentities($ctable_d['gst']);
		$result['cash_discount']        = htmlentities($ctable_d['cash_discount']);
		$result['additional_discount']  = htmlentities($ctable_d['additional_discount']);
		$result['cash_discount_amount']  		= htmlentities($ctable_d['cash_discount_amount']);
		$result['additional_discount_amount']  		= htmlentities($ctable_d['additional_discount_amount']);
		$result['igst_amount']  		= htmlentities($ctable_d['igst_amount']);
		$result['tcs_amount']  		= htmlentities($ctable_d['tcs_amount']);
		$result['transport_charge_per']  		= htmlentities($ctable_d['transport_charge_gst']);
		$result['packing_charge_per']  		= htmlentities($ctable_d['packing_charge_gst']);
		$result['cd_gst']  		= htmlentities($ctable_d['cd_gst']);
		$result['ad_gst']  		= htmlentities($ctable_d['ad_gst']);
		$result['apply_scheme']  		= htmlentities($ctable_d['apply_scheme']);
		$result['type_of_company']  		= htmlentities($ctable_d['type_of_company']);
		$result['terms_condition_id']  		= htmlentities($ctable_d['terms_condition_id']);
		$result['booking_place']  		= htmlentities($ctable_d['booking_place']);
		$result['booking_pincode']  		= htmlentities($ctable_d['booking_pincode']);
		$result['sales_executive_id']  		= htmlentities($ctable_d['sales_id']);
		$result['max_dispatch_date'] = htmlentities(date('d-m-Y', strtotime($ctable_d['max_dispatch_date'])));

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
	public function GetOrderItems($detail)
	{
		$where = "order_id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_item = $this->db->rp_getData("order_product_item", "*", $where, "", 0);
		if ($ctable_item) {
			while ($ctable_item_d = mysqli_fetch_array($ctable_item)) {
				$result_item = array();
				$result_item['product_id']			= htmlentities($ctable_item_d['pro_id']);
				$result_item['order_id']			= htmlentities($ctable_item_d['order_id']);
				$result_item['weight_id']			= htmlentities($ctable_item_d['weight_id']);
				$pro_name                           = $this->db->rp_getValue("product", "name", "id='" . $ctable_item_d['pro_id'] . "'");
				$size_name                          = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_d['weight_id'] . "'");
				$result_item['product_name']        = $size_name . " " . $pro_name . " ";
				$result_item['qty']					= htmlentities($ctable_item_d['pro_qty']);
				$result_item['inner_size']			= htmlentities($ctable_item_d['inner_size']);
				$result_item['outer_size']			= htmlentities($ctable_item_d['outer_size']);
				$result_item['box']					= htmlentities($ctable_item_d['cartoon_qty']);
				$result_item['order_qty']					= htmlentities($ctable_item_d['order_qty']);
				$result_item['item_order_unit']					= htmlentities($ctable_item_d['item_order_unit']);
				$result_item['is_including']					= htmlentities($ctable_item_d['is_including']);
				$result_item['bag']					= $this->db->rp_num(htmlentities($ctable_item_d['box_qty']));
				$result_item['loose']				= $this->db->rp_num(htmlentities($ctable_item_d['loose_qty']));
				$result_item['discount_per']		= $this->db->rp_num(htmlentities($ctable_item_d['discount']));
				$result_item['product_price']		= $this->db->rp_num(htmlentities($ctable_item_d['unitprice']));
				$result_item['original_price']		= $this->db->rp_num(htmlentities($ctable_item_d['original_price']));
				$result_item['product_total']		= $this->db->rp_num(htmlentities($ctable_item_d['totalprice']));
				$result_item['discount_amount']		= $this->db->rp_num(htmlentities($ctable_item_d['discount_amount']));
				$result_item['pro_description']		= htmlentities($ctable_item_d['pro_description']);
				$result_item['stock']		        = $this->db->rp_getValue("product_weight_price", "stock_qty", "product_id='" . $ctable_item_d['pro_id'] . "'");
				$result_item['cd_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['cash_discount_amount']));
				$result_item['ad_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['additional_discount_amount']));
				$result_item['gst_amount_item']	= $this->db->rp_num(htmlentities($ctable_item_d['igst_amount']));
				$result_item['taxable_amount']	= $this->db->rp_num(htmlentities($ctable_item_d['taxable']));
				$result_item['sub_total']	= $this->db->rp_num(htmlentities($ctable_item_d['subtotal']));
				$result_item['gst']         = $this->db->rp_getValue("product", "igst", "id='" . $result_item['product_id'] . "' AND isDelete=0");
				$result_item['other_charge']	= $this->db->rp_num(htmlentities($ctable_item_d['other_charge']));
				$result_item['fright_charge']	= $this->db->rp_num(htmlentities($ctable_item_d['fright_charge']));
				$result_item['order_item_brand_id']	= htmlentities($ctable_item_d['order_item_brand_id']);
				$result_item['item_idfbrand']	= htmlentities($ctable_item_d['id']);

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
	public function DeleteOrder($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);

		$where	= "id='" . $_REQUEST['id'] . "'";
		/*log entry*/
		$customer_id = $this->db->rp_getValue("orders", "customer_id", "id='" . $_REQUEST['id'] . "'");
		$order_no = $this->db->rp_getValue("orders", "order_no", "id='" . $_REQUEST['id'] . "'");
		$module_name = "Order";
		$flag = "Web";
		$log_description = $module_name . " " . $order_no . " Deleted By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
		/*log entry*/
		$where_item	= "order_id='" . $_REQUEST['id'] . "'";
		$order_id = $this->db->rp_update($this->ctable, $rows, $where, 0, $log_description, $flag, $module_name, "", $customer_id);
		$id = $this->db->rp_update("order_product_item", $rows, $where_item);
		//$this->log->insertLog($this->ctable,$_REQUEST['id'],"delete","Order Deleted ");
		if ($order_id != 0) {
			$order_id         = $this->db->clean($_REQUEST['id']);

			require_once('../include/class.system.php');
			$system = new System();

			$customer_id = $this->db->rp_getValue("orders", "customer_id", "id='" . $order_id . "'", 0);
			$status = $this->db->rp_getValue("orders", "status", "id='" . $order_id . "'", 0);
			$AccountInfo = $this->db->rp_getData("account", "*", "cid='" . $customer_id . "'", "", 0);
			$AccountInfo = mysqli_fetch_assoc($AccountInfo);
			if ($AccountInfo && $status == 1) {
				$AccountID = $AccountInfo['id'];
				$AccountNo = $AccountInfo['acc_no'];
				$count = $this->db->rp_getTotalRecord("account_transaction", "reference_id='" . $order_id . "' AND reference_table='orders' AND isDelete=0", 0);
				if ($count >= 0) {
					$delete = $this->db->rp_update("account_transaction", array("isDelete" => 1), "reference_id='" . $order_id . "' AND reference_table='orders'", 0);
				}
			}
			$reply = array("ack" => 1, "developer_msg" => "deleted data.", "ack_msg" => "Success! Delete Order Successfully.", "type" => $_REQUEST['id']);
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Delete Order Failed.");
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

		$where	= "id='" . $order_id . "'";
		$where_item	= "order_id='" . $order_id . "'";
		$order_id = $this->db->rp_update($this->ctable, $rows, $where, 0);

		$row = array(
			"isActive"	=> "0",
			"status"	=> "3",
		);

		$id = $this->db->rp_update("order_product_item", $row, $where_item);
		//$this->log->insertLog($this->ctable,$_REQUEST['id'],"delete","Order Deleted ");
		if ($order_id != 0) {
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
						$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error", "ack_msg" => "Request Generated With Error");
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
			$details['grand_total_rounded']		= isset($invoice_info['grand_total_rounded']) ? $this->db->clean($invoice_info['grand_total_rounded']) : "";
			$details['roundoff']		= isset($invoice_info['roundoff']) ? $this->db->clean($invoice_info['roundoff']) : "";

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
			$details['sales_id']		= "";
			$items = array();
			$invoice_items = $this->db->rp_getData("proforma_invoice_item", "*", "proforma_invoice_id='" . $detail['invoice_id'] . "'", 1);
			if ($invoice_items) {
				while ($invoice_item = mysqli_fetch_assoc($invoice_items)) {
					$items[] = $invoice_item;
				}
			}
			$result = $this->InsertOrdersFinal($details, $items);
			$Update = $this->db->rp_update($this->ctablePerforma, array("status" => 1), "id='" . $detail['invoice_id'] . "'", 0);
			$Update = $this->db->rp_update("orders", array("proforma_invoice_id" => $detail['invoice_id']), "id='" . $result['order_id'] . "'", 0);
			return $result;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Invoice Not Found!!", "ack_msg" => "Invoice Not Found!!");
			return $reply;
		}
	}
	public function InsertOrdersFinal($details, $items)
	{
		extract($details);
		$order_date	= date("Y-m-d");
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
			"order_date",
			"modify_date",
			"company_name",
			"type_of_company",
			"terms_condition_id",
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
			$order_date,
			$modify_date,
			$company_name,
			$type_of_company,
			$terms_condition_id,
		);

		$cart_id = $this->db->rp_insert("orders", $cdvalue, $cdrow, 0);
		$row = array("order_no" => OUTLETS_ORDER_NO . str_pad($cart_id, 3, '0', STR_PAD_LEFT));
		$update_order_no = $this->db->rp_update("orders", $row, "id='" . $cart_id . "'", 0);
		$adate = date('Y-m-d H:i:s');
		$total_taxamount = 0;
		$total_tax = 0;
		$total_discount = 0;
		$grand_total = 0;

		foreach ($items as  $p) {
			$row = array(
				"order_id",
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
			$ins = $this->db->rp_insert("order_product_item", $value, $row, 0);
		}
		$order_pro_detail = mysqli_fetch_assoc($this->db->rp_getData("orders", "*", "id='" . $cart_id . "' AND isDelete=0", "", 0));
		$order_pro_detail['product'] = array();
		$where = "order_id='" . $cart_id . "' AND isDelete=0";
		$dt = $this->db->rp_getData("order_product_item", "*", $where, "", 0);
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
		$title_description = "Order of <b>Rs." . $grand_total_rounded . "</b> for date <b>" . date('d-m-Y', strtotime($order_date)) . "</b> added by <b>" . $customer_name . "</b>";
		$notification = $this->system->setNotification(0, 1, "Order Notification.", 5, "Order Message", $title_description, "", "", $order_date, $cart_id, "orders", $customer_type);

		$reply = array(
			"ack" => 1,
			"ack_msg" => "Order Add Successfully!!",
			"developer_msg" => "You got it!!",
			"result" => $order_pro_detail,
			"order_id" => $cart_id,
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
				$order_id = $invoice_item['request_id'];

				$remaining_qty = $this->db->rp_getValue("customer_order_request_item", "pending_qty", "item_id='" . $pro_id . "' AND weight_id='" . $weight_id . "' AND request_id='" . $order_id . "'", 0);

				$final_remaining_qty = ($remaining_qty) + ($qty);

				$rows 	= array(
					"pending_qty"				=> $final_remaining_qty,
				);
				$where	= "item_id='" . $pro_id . "' AND weight_id='" . $weight_id . "' AND request_id='" . $order_id . "'";
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

	public function AddToCart($detail, $products)
	{
		// echo "<pre>";
		// print_r($detail);die;
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
				$check_cart_exist = $this->db->rp_getTotalRecord("orders", "id='" . $detail['order_id'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "'" . $where, 0);
			} else {
				$check_cart_exist = $this->db->rp_getTotalRecord("orders", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "' AND status=-1 " . $where, 0);
			}
			// echo $check_cart_exist;exit;
			if ($check_cart_exist != 0) {
				// already cart exist
				if ($detail['order_id'] != "") {
					$order_id = $detail['order_id'];
				} else {
					$order_id = $this->db->rp_getValue("orders", "id", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND status=-1 " . $where . " ORDER BY id DESC", 0);
				}
				if ($order_id == "" || $order_id == 0 || $order_id == null) {
					$reply = array("ack" => 0, "developer_msg" => "Cart order id not found", "ack_msg" => "Product added Failed");
					return $reply;
				}

				$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
				if (!empty($products)) {
					$total_items = $this->db->rp_getTotalRecord("order_product_item", "order_id='" . $order_id . "'");
					// print_r($products);exit;
					foreach ($products as $p) {
						// $product_detail=$this->db->rp_getData("product","*","id='".$p['pid']."'","","0");
						//check item already in cart or not
						$check_item_in_cart = $this->db->rp_getValue("order_product_item", "id", "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");
						if ($check_item_in_cart != 0 && $check_item_in_cart != "") {
							// update qty of that item
							$pro_r = $this->db->rp_getData("order_product_item", "*", "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");
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

							$discount_amount = $p['original_price'] - $p['price'];
							$discount = ($discount_amount * 100) / $p['original_price'];
							$user_discount = $this->db->rp_num($discount);
							$unitprice_amt = $this->db->rp_num($discount_amount);

							$update_item = array(
								"unitprice" => $p['price'],
								// "original_price"=>$p['original_price'],
								"pro_qty" => $update_qty,
								"remaining_qty" => $update_qty,
								"totalprice" => $update_totalprice,
								"box_qty" => $update_box_qty,
								"cartoon_qty" => $update_cartoon_qty,
								"discount" => $user_discount,
								"discount_amount" => $unitprice_amt,
								"modified_date" => date("Y-m-d H:i:s"),
							);

							$isUpdate = $this->db->rp_update("order_product_item", $update_item, "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0", 0);
							$reply = array("ack" => 1, "developer_msg" => "Product Updated Successfully", "ack_msg" => "Product Updated Successfully", "order_id" => $order_id);
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
										"brand_id",
										"pro_description",
										"loose_qty",
										"item_order_unit",
										"order_qty",
									);
									$values = array(
										$order_id,
										$top_cat_id,
										$cat_id,
										$p['pid'],
										$p['weight_id'],
										$this->db->clean($p['item_name']),
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
										$p['brand_id'],
										$this->db->clean($p['pro_description']),
										isset($p['loose']) ? $p['loose'] : "0",
										isset($p['item_order_unit']) ? $p['item_order_unit'] : "0",
										isset($p['order_qty']) ? $p['order_qty'] : "0",
									);
									$total_qty += $p['qty'];
									$sub_total += $final_price;
									$item_id = $this->db->rp_insert("order_product_item", $values, $rows, 0);

									$is_apply = $this->db->rp_getValue("orders", "apply_scheme", "isDelete=0 AND id='" . $order_id . "'");

									if ($is_apply == 1) {
										$this->AddschemeItems($order_id, $item_id, $p['pid'], $p['weight_id'], $p['qty']);
									}
									if ($item_id != 0) {
										$reply = array("ack" => 1, "developer_msg" => "Product added Successfully", "ack_msg" => "Product added Successfully", "order_id" => $order_id);
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
				// echo "Dfd";exit;
				// create new cart
				$c_type = $_REQUEST['c_type'];
				$customer_r = mysqli_fetch_assoc($customer_d);
				$price_list_id = $customer_r['price_list_id'];
				$order_no = $this->db->getlastInsertId("orders");
				if ($c_type == '1') {
					$order_no = SS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
				} else if ($c_type == '2') {
					$order_no = DISTRIBUTOR_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
				} else {
					$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
				}

				$channel_partner_order_flag = 0;
				if ((isset($_REQUEST['c_type']) && $_REQUEST['c_type'] == 'channel_partner')
					|| (isset($detail['channel_partner_order_flag']) && $detail['channel_partner_order_flag'] == 1)
					|| (isset($customer_r['channel_partner_flag']) && $customer_r['channel_partner_flag'] == 1)
				) {
					$channel_partner_order_flag = 1;
				}

				/* Only include flag column when it exists (avoids insert fail before db_sync) */
				$has_cp_order_flag = false;
				$cp_col_check = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'channel_partner_order_flag'");
				if ($cp_col_check && mysqli_num_rows($cp_col_check) > 0) {
					$has_cp_order_flag = true;
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
					// "contact_number",
					"address",
					"city",
					"main_city",
					"state",
					"country",
					// "email",
					// "gst",
					"order_date",
					// "brand_id",
					"status",
					"sales_id",
					"chalan_no",
					"po_no",
					"po_date",
					"terms_comdition",
					"faithfully",
					"transport_name",
					"transport_through",
					"transport_charge",
					"shipping_address",
					"billing_address",
					"packing_charge",
					"gst",
					"vendor_code",
					"tendor_code",
					"entry_flag",
					"apply_scheme",
					"type_of_company",
					"terms_condition_id",
					"booking_place",
					"booking_pincode",
					"max_dispatch_date"
				);
				$values = array(
					$order_no,
					$customer_r['id'],
					$customer_r['dealer_distributor_id'],
					$customer_r['super_stockist_id'],
					$customer_r['cname'],
					$customer_r['client_code'],
					$customer_r['customer_flag'],
					$customer_r['company_name'],
					$customer_r['type_of_executive'],
					// $customer_r['phone'],
					addslashes($customer_r['address']),
					$customer_r['city'],
					$customer_r['main_city'],
					$customer_r['state'],
					$customer_r['country'],
					// $customer_r['email'],
					// $customer_r['gst'],
					$order_date,
					// $customer_r['brand_id'],
					-1,
					($detail['sales_executive_id']) ? $detail['sales_executive_id'] : "",
					($detail['chalan_no']) ? $detail['chalan_no'] : "",
					($detail['po_no']) ? $detail['po_no'] : "",
					($detail['po_date']) ? $detail['po_date'] : "",
					($detail['terms_comdition']) ? $detail['terms_comdition'] : "",
					($detail['faithfully']) ? $detail['faithfully'] : "",
					($detail['transport_name']) ? $detail['transport_name'] : "",
					($detail['transport_through']) ? $detail['transport_through'] : "",
					($detail['transport_charge']) ? $detail['transport_charge'] : "",
					$this->db->clean($detail['shipping_address']),
					$this->db->clean($detail['billing_address']),
					($detail['packing_charge']) ? $detail['packing_charge'] : "",
					($detail['name_gstin']) ? $detail['name_gstin'] : "",
					($detail['vendor_code']) ? $detail['vendor_code'] : "",
					($detail['tendor_code']) ? $detail['tendor_code'] : "",
					1,
					($detail['apply_scheme']) ? $detail['apply_scheme'] : "",
					($detail['type_of_company']) ? $detail['type_of_company'] : "",
					($detail['terms_condition_id']) ? $detail['terms_condition_id'] : "",
					($detail['booking_place']) ? $detail['booking_place'] : "",
					($detail['booking_pincode']) ? $detail['booking_pincode'] : "",
					($detail['max_dispatch_date']) ? $detail['max_dispatch_date'] : "",
				);
				if ($has_cp_order_flag) {
					/* Insert after customer_type */
					array_splice($rows, 9, 0, array("channel_partner_order_flag"));
					array_splice($values, 9, 0, array($channel_partner_order_flag));
				}

				/*log entry*/
				$module_name = "Order";
				$flag = "Web";
				$log_description = $module_name . " " . $order_no . " Created By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
				/*log entry*/

				$order_id = $this->db->rp_insert("orders", $values, $rows, 0, $log_description, $flag, $module_name, "", $customer_r['id']);
				if ($order_id != 0) {
					if ($has_cp_order_flag && $channel_partner_order_flag == 1) {
						$this->db->rp_update("orders", array("channel_partner_order_flag" => 1), "id='" . $order_id . "'", 0);
					}
					/* CP portal simple order → pending Convert to Order for admin */
					$cp_portal_flag = 0;
					if (isset($detail['cp_portal_order_flag']) && (int) $detail['cp_portal_order_flag'] === 1) {
						$cp_portal_flag = 1;
					} else if (isset($_REQUEST['cp_portal_order_flag']) && (int) $_REQUEST['cp_portal_order_flag'] === 1) {
						$cp_portal_flag = 1;
					}
					$cp_portal_col = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'cp_portal_order_flag'");
					if ($cp_portal_flag == 1 && $cp_portal_col && mysqli_num_rows($cp_portal_col) > 0) {
						$portalUpd = array("cp_portal_order_flag" => 1);
						$cp_mode_col = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'cp_order_mode'");
						if ($cp_mode_col && mysqli_num_rows($cp_mode_col) > 0) {
							$cp_mode_val = '';
							if (isset($detail['cp_order_mode'])) {
								$cp_mode_val = $detail['cp_order_mode'];
							} else if (isset($_REQUEST['cp_mode'])) {
								$cp_mode_val = $_REQUEST['cp_mode'];
							}
							if ($cp_mode_val == 'own' || $cp_mode_val == 'customer') {
								$portalUpd['cp_order_mode'] = $cp_mode_val;
							}
						}
						$this->db->rp_update("orders", $portalUpd, "id='" . $order_id . "'", 0);
					}
					/* Link Channel Partner end-customer (channel_partner_customer) when provided */
					$cp_end_customer_id = 0;
					if (isset($detail['channel_partner_customer_id'])) {
						$cp_end_customer_id = (int) $detail['channel_partner_customer_id'];
					} else if (isset($_REQUEST['channel_partner_customer_id'])) {
						$cp_end_customer_id = (int) $_REQUEST['channel_partner_customer_id'];
					}
					if ($cp_end_customer_id > 0 && $channel_partner_order_flag == 1) {
						$cp_end_r = $this->db->rp_getData(
							"channel_partner_customer",
							"*",
							"id='" . $cp_end_customer_id . "' AND channel_partner_id='" . (int) $customer_r['id'] . "' AND isDelete=0",
							"",
							0
						);
						if ($cp_end_r && $cp_end_d = mysqli_fetch_assoc($cp_end_r)) {
							$cp_order_upd = array(
								"channel_partner_customer_id" => $cp_end_customer_id,
								"customer_name" => $cp_end_d['person_name'],
								"company_name" => $cp_end_d['company_name'],
							);
							$cp_col_check2 = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `orders` LIKE 'channel_partner_customer_id'");
							if (!($cp_col_check2 && mysqli_num_rows($cp_col_check2) > 0)) {
								unset($cp_order_upd['channel_partner_customer_id']);
							}
							if (!empty($detail['shipping_address'])) {
								$cp_order_upd['shipping_address'] = $this->db->clean($detail['shipping_address']);
							} else {
								$addrParts = array_filter(array($cp_end_d['city'], $cp_end_d['state'], $cp_end_d['pincode'], $cp_end_d['country']));
								$cp_order_upd['shipping_address'] = implode(', ', $addrParts);
							}
							if (!empty($detail['billing_address'])) {
								$cp_order_upd['billing_address'] = $this->db->clean($detail['billing_address']);
							} else {
								$cp_order_upd['billing_address'] = $cp_order_upd['shipping_address'];
							}
							if (!empty($detail['name_gstin'])) {
								$cp_order_upd['gst'] = $detail['name_gstin'];
							} else if (!empty($cp_end_d['gst'])) {
								$cp_order_upd['gst'] = $cp_end_d['gst'];
							}
							if (!empty($detail['booking_place'])) {
								$cp_order_upd['booking_place'] = $detail['booking_place'];
							} else if (!empty($cp_end_d['city'])) {
								$cp_order_upd['booking_place'] = $cp_end_d['city'] . (!empty($cp_end_d['state']) ? ', ' . $cp_end_d['state'] : '');
							}
							if (!empty($detail['booking_pincode'])) {
								$cp_order_upd['booking_pincode'] = $detail['booking_pincode'];
							} else if (!empty($cp_end_d['pincode'])) {
								$cp_order_upd['booking_pincode'] = $cp_end_d['pincode'];
							}
							$this->db->rp_update("orders", $cp_order_upd, "id='" . $order_id . "'", 0);
						}
					}
					// covert prospect customer into customer
					if ($customer_r['customer_flag'] == 1) {
						$this->db->rp_update("executive", array("customer_flag" => 0, "customer_flag_change_date" => date('Y-m-d H:i:s')), "id='" . $customer_r['id'] . "'", 0);
					}
					// covert prospect customer into customer
					if (!empty($products)) {
						foreach ($products as $p) {
							$product_detail = $this->db->rp_getData("product", "*", "id='" . $p['pid'] . "'", "", "0");
							if ($product_detail) {
								$product_detail = mysqli_fetch_assoc($product_detail);
								$top_cat_id = $product_detail['tcid'];
								$cat_id = $product_detail['cid'];
								$hsn_code = (isset($product_detail['hsn_code']) && $product_detail['hsn_code'] !== null) ? $product_detail['hsn_code'] : "";
								$igst = (isset($product_detail['igst']) && $product_detail['igst'] !== null && $product_detail['igst'] !== '') ? $product_detail['igst'] : 0;
								$ctable_item_weight_detail = false;
								if (isset($p['weight_id']) && $p['weight_id'] !== '' && $p['weight_id'] !== null) {
									$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "weight_id='" . $p['weight_id'] . "' AND product_id='" . $p['pid'] . "'", "", 0);
								}
								if (!$ctable_item_weight_detail) {
									$ctable_item_weight_detail = $this->db->rp_getData("product_weight_price", "*", "product_id='" . $p['pid'] . "'", "id ASC", 0);
								}

								if ($ctable_item_weight_detail) {
									$ctable_item_weight_detail = mysqli_fetch_assoc($ctable_item_weight_detail);
									$p['weight_id'] = $ctable_item_weight_detail['weight_id'];
									$weight_name = $this->db->rp_getValue("weight", "name", "id='" . $ctable_item_weight_detail['weight_id'] . "'");
									$product_code = $ctable_item_weight_detail['catno'];

									if ($ctable_item_weight_detail['weight_id'] == -1) {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (#" . stripslashes($product_code) . ")"));
									} else {
										$p['item_name'] = addslashes(html_entity_decode($product_detail['name'] . " (" . stripslashes($weight_name) . ")" . " (#" . stripslashes($product_code) . ")"));
									}
									$p['inner_size'] = ($ctable_item_weight_detail['inner_size'] !== null && $ctable_item_weight_detail['inner_size'] !== '' && (float) $ctable_item_weight_detail['inner_size'] > 0) ? $ctable_item_weight_detail['inner_size'] : 1;
									$p['outer_size'] = ($ctable_item_weight_detail['outer_size'] !== null && $ctable_item_weight_detail['outer_size'] !== '' && (float) $ctable_item_weight_detail['outer_size'] > 0) ? $ctable_item_weight_detail['outer_size'] : 1;
									if (!isset($p['box_qty']) || $p['box_qty'] === '' || $p['box_qty'] === null) {
										$p['box_qty'] = $p['qty'] / $p['inner_size'];
									}
									if (!isset($p['cartoon_qty']) || $p['cartoon_qty'] === '' || $p['cartoon_qty'] === null) {
										$p['cartoon_qty'] = $p['box_qty'] / $p['outer_size'];
									}

									// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."'",0);
									$unitprice = isset($p['price']) ? $p['price'] : $ctable_item_weight_detail['price'];
									$unitprice = $this->db->rp_num($unitprice);
									$GST = $igst;
									$totalprice = $p['qty'] * $unitprice;
									$totalprice = $this->db->rp_num($totalprice);
									$original_price = isset($p['original_price']) && $p['original_price'] !== '' && $p['original_price'] !== null ? $p['original_price'] : $unitprice;

									$user_discount = isset($p['discount']) ? $p['discount'] : 0;
									if ($user_discount == 0 || $user_discount == '' || $user_discount === null) {
										$user_discount = 0;
										$discount_amount = $this->db->rp_num(isset($p['discount_amount']) ? $p['discount_amount'] : 0);
										if ($discount_amount == "") {
											$discount_amount = 0;
										}
									} else {
										$discount_amount = $this->db->rp_num(($original_price * $user_discount) / 100);
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

											$GST = $igst;
											$totalprice = $p['qty'] * $unitprice;
											$totalprice = $this->db->rp_num($totalprice);
											// $original_price=$price_list_price;
											$final_price = $this->db->rp_num($totalprice);

											$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
											if ($price_list_discount_type === null || $price_list_discount_type === false) {
												$price_list_discount_type = 0;
											}

											$p_discount = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											// $discount = $p['discount'];
											$price_list_discount = $this->db->rp_num($p_discount);
											$discount_amount1 = ($original_price * $price_list_discount) / 100;
											$price_list_discounted_amount = $this->db->rp_num($discount_amount1);
											$price_list_discounted_price = $original_price - $price_list_discounted_amount;
										} else {
											$add_price_list_id = 0;
										}
									} else {
										$add_price_list_id = 0;
									}

									if ($price_list_price === null || $price_list_price === false || $price_list_price === '') {
										$price_list_price = 0;
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
										"pro_description",
										/*"brand_id",*/
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
										$order_id,
										$top_cat_id !== null ? $top_cat_id : 0,
										$cat_id !== null ? $cat_id : 0,
										$p['pid'],
										$p['weight_id'],
										$this->db->clean($p['item_name']),
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
										$this->db->clean(isset($p['pro_description']) ? $p['pro_description'] : ""),
										/*$p['brand_id'],*/
										isset($p['loose']) ? $p['loose'] : "0",
										isset($p['cd_discount']) ? $p['cd_discount'] : 0,
										isset($p['ad_discount']) ? $p['ad_discount'] : 0,
										$igst,
										isset($p['gst_amount_item']) ? $p['gst_amount_item'] : 0,
										isset($p['taxable_amount']) ? $p['taxable_amount'] : 0,
										isset($p['sub_total']) ? $p['sub_total'] : 0,
										isset($p['other_charge']) ? $p['other_charge'] : 0,
										isset($p['fright_charge']) ? $p['fright_charge'] : 0,
										$hsn_code,
										isset($p['is_including']) && $p['is_including'] !== '' && $p['is_including'] !== null ? $p['is_including'] : 0,
										isset($p['item_order_unit']) && $p['item_order_unit'] !== '' && $p['item_order_unit'] !== null ? $p['item_order_unit'] : "0",
										isset($p['order_qty']) ? $p['order_qty'] : $p['qty'],
										isset($p['order_item_brand_id']) && $p['order_item_brand_id'] !== '' && $p['order_item_brand_id'] !== null ? $p['order_item_brand_id'] : "0",
									);
									/* Force non-null scalars for rp_insert (null breaks SQL commas) */
									for ($vi = 0; $vi < count($values); $vi++) {
										if ($values[$vi] === null || $values[$vi] === false) {
											$values[$vi] = "";
										}
									}
									$total_qty += $p['qty'];
									$sub_total += $final_price;
									$item_id = $this->db->rp_insert("order_product_item", $values, $rows, 0);

									$is_apply = $this->db->rp_getValue("orders", "apply_scheme", "isDelete=0 AND id='" . $order_id . "'");

									if ($is_apply == 1) {

										$this->AddschemeItems($order_id, $item_id, $p['pid'], $p['weight_id'], $p['qty']);
									}

									// $get_free_product=$this->db->rp_getData("scheme_master_item","*","isDelete=0 AND product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."' AND qty='".$p['qty']."'","",0);


									// $get_order_date=$this->db->rp_getValue("orders","order_date","isDelete=0 AND id='".$order_id."'",0);
									// while($get_free_product_d=mysqli_fetch_array($get_free_product))
									// {

									// 	$get_dates=$this->db->rp_getData("scheme_master","	start_date,end_date","isDelete=0 AND id='".$get_free_product_d['scheme_id']."'","",0);
									// 	$date_array=mysqli_fetch_array($get_dates);


									// 	if(strtotime($date_array['start_date']) <= strtotime($get_order_date) && strtotime($date_array['end_date']) >= strtotime($get_order_date))
									// 	{

									// 		$rows_item=array("order_id","order_item_id","scheme_id","pro_id","weight_id","pro_qty");
									// 		$values_item=array($order_id,$item_id,$get_free_product_d['scheme_id'],$get_free_product_d['product_id_2'],$get_free_product_d['weight_id_2'],$get_free_product_d['free_qty']);
									// 		$scheme_item_insert=$this->db->rp_insert("order_scheme_items",$values_item,$rows_item,0);
									// 	}


									// }


									// mysqli_data_seek($get_free_product, 0);
								}
							}
						}

						$total_items = $this->db->rp_getTotalRecord("order_product_item", "order_id='" . $order_id . "' AND isDelete=0");
						if ($total_items != 0) {
							$reply = array("ack" => 1, "developer_msg" => "Order Added To Cart Successfully", "ack_msg" => "Order Added To Cart Successfully", "order_id" => $order_id, "c_type" => $customer_r['type_of_executive']);
							return $reply;
						} else {
							$reply = array("ack" => 0, "developer_msg" => "Order Item Not inserted", "ack_msg" => "Order Item Not inserted");
							return $reply;
						}
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Request Generated With Error Product Not Found ", "ack_msg" => "Please add at least one product.");
						return $reply;
					}
				} else {
					$db_err = @mysqli_error($this->db->myconn);
					$reply = array("ack" => 0, "developer_msg" => "Request Not Generated: " . $db_err, "ack_msg" => "Order save failed. Please run db_sync and try again.");
					return $reply;
				}
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Customer Not Found!!", "ack_msg" => "Customer Not Found!!");
			return $reply;
		}
	}

	public function UpdateOrder($detail, $products)
	{
		// echo "<pre>";
		// print_r($detail);
		// echo "<hr>";
		// print_r($products);exit;
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0", "", 0);
		if ($customer_d) {
			$where = "";
			if ($detail['sales_executive_id'] != "") {
				if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
					$where = " AND sales_id = '" . $detail['sales_executive_id'] . "' ";
				}
			} else {
				$detail['sales_executive_id'] = "";
				if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
					$where = " AND sales_id = 0 ";
				}
			}

			$check_cart_exist = $this->db->rp_getTotalRecord("orders", "id='" . $detail['order_id'] . "' AND isDelete=0 " . $where, 0);

			// if($check_cart_exist!=0)
			// {
			if ($detail['order_id'] != 0) {
				$order_id = $detail['order_id'];
				if (!empty($products)) {
					// delete product
					$this->db->rp_delete("order_product_item", "order_id='" . $order_id . "'");
					// delete product


					// delete Scheme product
					$this->db->rp_delete("order_scheme_items", "order_id='" . $order_id . "'");
					// delete Scheme product

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
								// echo $totalprice;exit;
								$original_price = $p['original_price'];

								$user_discount = $p['discount'];
								if ($user_discount == 0) {
									$discount_amount = $this->db->rp_num($p['discount_amount']);
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
									"pro_description",
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
									$order_id,
									$top_cat_id,
									$cat_id,
									$p['pid'],
									$p['weight_id'],
									$this->db->clean($p['item_name']),
									$p['qty'],
									$p['qty'],
									$p['inner_size'],
									$p['outer_size'],
									$p['box_qty'],
									$p['cartoon_qty'],
									$unitprice,
									$original_price,
									$final_price,
									isset($user_discount) ? $user_discount : "0",
									isset($unitprice_amt) ? $unitprice_amt : "0",
									$add_price_list_id,
									$price_list_price,
									($price_list_discounted_price) ? $price_list_discounted_price : 0,
									($price_list_discounted_amount) ? $price_list_discounted_amount : 0,
									($price_list_discount_type) ? $price_list_discount_type : 0,
									($price_list_discount) ? $price_list_discount : 0,
									$this->db->clean($p['pro_description']),
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
								);
								$total_qty += $p['qty'];
								$sub_total += $final_price;
								// echo $sub_total;exit;
								/*log entry*/
								$module_name = "Order";
								$order_no = $this->db->rp_getValue("orders", "order_no", "id='" . $order_id . "'");
								$flag = "Web";
								$log_description = $module_name . " " . $order_no . " Edited By " . $_SESSION[SITE_SESS . 'SESS_NAME'] . " ON " . date("Y-m-d H:i:s");
								/*log entry*/
								$item_id = $this->db->rp_insert("order_product_item", $values, $rows, 0, $log_description, $flag, $module_name, "", $detail['cid']);


								$is_apply = $this->db->rp_getValue("orders", "apply_scheme", "id='" . $order_id . "'");


								if ($is_apply == 1) {
									$this->AddschemeItems($order_id, $item_id, $p['pid'], $p['weight_id'], $p['qty']);
								}




								// $get_free_product=$this->db->rp_getData("scheme_master_item","*","isDelete=0 AND product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."' AND qty='".$p['qty']."'","",0);


								// 	$get_order_date=$this->db->rp_getValue("orders","order_date","isDelete=0 AND id='".$order_id."'");
								// while($get_free_product_d=mysqli_fetch_array($get_free_product))
								// {



								// 	$get_dates=$this->db->rp_getData("scheme_master","	start_date,end_date","isDelete=0 AND id='".$get_free_product_d['scheme_id']."'","",0);
								// 	$date_array=mysqli_fetch_array($get_dates);

								// 	if(strtotime($date_array['start_date']) <= strtotime($get_order_date) && strtotime($date_array['end_date']) >= strtotime($get_order_date))
								// 	{

								// 		$rows_item=array("order_id","order_item_id","scheme_id","pro_id","weight_id","pro_qty");
								// 		$values_item=array($order_id,$item_id,$get_free_product_d['scheme_id'],$get_free_product_d['product_id_2'],$get_free_product_d['weight_id_2'],$get_free_product_d['free_qty']);
								// 		$scheme_item_insert=$this->db->rp_insert("order_scheme_items",$values_item,$rows_item,0);
								// 	}






								// }






							}
						}
					}

					$total_items = $this->db->rp_getTotalRecord("order_product_item", "order_id='" . $order_id . "' AND isDelete=0");
					if ($total_items != 0) {
						$reply = array("ack" => 1, "developer_msg" => "Order Updated To Cart Successfully", "ack_msg" => "Order Updated To Cart Successfully", "order_id" => $order_id);
						return $reply;
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Order Item Not Updated9", "ack_msg" => "Order Item Not Updated10");
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
			// else
			// {
			// 	$reply=array("ack"=>0,"developer_msg"=>"Order Not Found!!","ack_msg"=>"Order Not Found!!");
			// 	return $reply;
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
		$order_id = $this->db->rp_getValue("orders", "id", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND status=-1" . $where . " ORDER BY id DESC LIMIT 1", 0);
		if ($order_id != 0) {
			// covert prospect customer into customer
			if ($customer_r['customer_flag'] == 1) {
				$this->db->rp_update("executive", array("customer_flag" => 0), "id='" . $customer_r['id'] . "'");
			}
			// covert prospect customer into customer

			$order_items_r = $this->db->rp_getData("order_product_item", "*", "order_id='" . $order_id . "' AND isDelete=0 ");
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
							$item_gst_total += $order_items_d['item_gst_amount'];
							//$item_id = $this->db->rp_insert("order_product_item",$values,$rows,0);
						}
					}
				}
				$additional_discounted_amount = $sub_total;
				$cash_discounted_amount = $sub_total;
				$additional_discount_amount = 0;
				$cash_discount_amount = 0;

				$additional_discount_amount = $this->db->rp_getValue("orders", "additional_discount_amount", "id='" . $order_id . "' ", 0);


				//$additional_discount_amount = $detail['additional_discount_amount'];
				$additional_discounted_amount = $sub_total - $additional_discount_amount;
				//$cash_discounted_amount = $additional_discounted_amount;

				$cash_discount_amount = $this->db->rp_getValue("orders", "cash_discount_amount", "id='" . $order_id . "' ", 0);
				//$cash_discount_amount=$detail['cash_discount_amount'];
				//$cash_discount_amount = ($additional_discounted_amount*$cash_discount)/100;

				$cash_discounted_amount = $additional_discounted_amount - $cash_discount_amount;
				$cash_discounted_amount = $this->db->rp_num((float)$cash_discounted_amount, 3, '.', '');
				$additional_discounted_amount = $this->db->rp_num((float)$additional_discounted_amount, 2, '.', '');

				$cash_discount_amount = $this->db->rp_num((float)$cash_discount_amount, 3, '.', '');
				$additional_discount_amount = $this->db->rp_num((float)$additional_discount_amount, 3, '.', '');

				$packing_charge = $this->db->rp_getValue("orders", "packing_charge", "id='" . $order_id . "' ", 0);
				$transport_charge = $this->db->rp_getValue("orders", "transport_charge", "id='" . $order_id . "' ", 0);
				$sub_total = $this->db->rp_num((float)$sub_total, 3, '.', '');
				$final_total = $cash_discounted_amount + $packing_charge + $transport_charge;
				$gst_amount = $this->db->rp_getValue("orders", "igst_amount", "id='" . $order_id . "' ", 0);

				//$gst_amount=$this->db->rp_num(($final_total*$GST)/100);
				//$gst_amount=$this->db->rp_num(($final_total*$GST)/100);
				//$gst_amount=$this->db->rp_getValue("orders","igst_amount","id='".$order_id."' ",0);;
				$tcs_amount = $this->db->rp_getValue("orders", "tcs_amount", "id='" . $order_id . "' ", 0);;
				$grandtotal = $this->db->rp_num($final_total + $gst_amount + $tcs_amount);

				$before_roundoff = $this->db->rp_num($grandtotal, 3);
				$whole = floor($before_roundoff);
				$fraction = $before_roundoff - $whole;
				$f1 =  $this->db->rp_num((float)$fraction, 3, '.', '');
				$roundoff = $this->db->rp_num($f1, 3);
				//$grand_total=strval($this->db->rp_num(($before_roundoff-$roundoff),2));
				$grand_total = $this->db->rp_num(round($grandtotal), 3);

				$dt = date("Y-m-d");
				/*log entry*/
				$sales_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $detail['sales_executive_id'] . "'", 0);
				$order_no = $this->db->rp_getValue("cart_detail", "order_no", "id='" . $order_id . "'", 0);
				$customer_id = $this->db->rp_getValue("cart_detail", "customer_id", "id='" . $order_id . "'", 0);
				$module_name = "Order";
				$flag = "Application";
				$log_description = $module_name . " " . $order_no . " Place By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				/*log entry*/

				/*if($customer_r['type_of_executive']=="3"){
					$status=1;
				}else{
				*/
				$status = 0;
				//}

				$update_array1 = array("total_qty" => $total_qty,/*"subtotal"=>$sub_total,"grand_total"=>$grand_total,*//*"cash_discount_amount"=>$cash_discount_amount,*//*"cash_discount"=>$cash_discount,*//*"igst_amount"=>$gst_amount,*/ "status" => $status, "class_id" => $detail['class_id'], "area_id" => $detail['area_id'], "dealer_id" => $detail['dealer_id'], "chalan_no" => $detail['chalan_no'], "po_no" => $detail['po_no'], "po_date" => date('Y-m-d', strtotime($detail['po_date'])), "terms_comdition" => $detail['terms_comdition'], "faithfully" => $detail['faithfully'], "transport_name" => $detail['transport_name'], "transport_through" => $detail['transport_through'],/*"transport_charge"=>$detail['transport_charge'],*//*"packing_charge"=>$detail['packing_charge'],*/ "shipping_address" => $this->db->clean($detail['shipping_address']), "billing_address" => $this->db->clean($detail['billing_address']), "vendor_code" => $detail['vendor_code'], "tendor_code" => $detail['tendor_code'], "remarks" => $detail['remark'], "gst" => $detail['gst'], "order_date" => $detail['order_date'], "type_of_company" => $detail['type_of_company'], "terms_condition_id" => $detail['terms_condition_id'], "booking_place" => $detail['booking_place'], "booking_pincode" => $detail['booking_pincode']);
				// "entry_flag"=>$detail['entry_flag']
				$isUpdated = $this->db->rp_update("orders", $update_array1, "id='" . $order_id . "'", 0, $log_description, $flag, $module_name, $detail['sales_executive_id'], $customer_id);
				if ($isUpdated) {
					$rows = array("gst" => $detail['gst']);
					$this->db->rp_update("executive", $rows, "id='" . $detail['cid'] . "' AND isDelete=0", 0);

					$this->db->rp_delete("cart_detail", "id=" . $detail['cart_id'], 0);
					$this->db->rp_delete("cart_item", "order_id=" . $detail['cart_id'], 0);

					$root_count = $this->db->rp_getTotalRecord("my_route", "date='" . $dt . "' AND customer_id='" . $detail['cid'] . "' AND sales_id='" . $detail['sales_executive_id'] . "'", 0);

					if ($root_count > 0) {
						$isrootUpdate = $this->db->rp_update("my_route", array("order_flag" => 1), "date='" . $dt . "' AND customer_id='" . $detail['cid'] . "' AND sales_id='" . $detail['sales_executive_id'] . "'", 0);
					}
				}
				if ($isUpdated) {
					$order_no  = $this->db->rp_getValue("orders", "order_no", "id='" . $order_id . "'");

					$sales_id = $this->db->rp_getValue("orders", "sales_id", "id='" . $order_id . "'");
					$customer_id = $this->db->rp_getValue("orders", "customer_id", "id='" . $order_id . "'");

					$entry_flag  = $this->db->rp_getValue("orders", "entry_flag", "id='" . $order_id . "'");
					$company_name = $this->db->rp_getValue("executive", "company_name", "id='" . $customer_id . "'", 0);
					$customer_name = $this->db->rp_getValue("executive", "cname", "id='" . $customer_id . "'", 0);
					$forOrder = "";
					if ($entry_flag == 6) {
						$order_add_name = " for " . $company_name . "-" . $customer_name;
					} else if ($entry_flag == 5) {
						$order_add_name = " by " . $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'");
						$forOrder =   " for " . $company_name . "-" . $customer_name;
					} else if ($entry_flag == 1) {
						$order_add_name = " by " . $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'");
						$forOrder =   " for " . $company_name . "-" . $customer_name;
					} else if ($entry_flag == 2) {
						$order_add_name = " for " . $company_name . "-" . $customer_name;
					}
					$notification_description = "New order added with Order No " . $order_no . $order_add_name . $forOrder;

					// send sales executive notification added by shivani     
					$this->objPushNotification->commonNotification($sales_id, $order_id, "orders", "Order Added", $notification_description, "sales_executive", "orders");
					// send sales executive notification added by shivani 

					// send customer notification added by shivani
					$this->objPushNotification->commonNotification($customer_id, $order_id, "orders", "Order Added", $notification_description, "customer", "orders");
					// send customer notification added by shivani 

					// send customer upper chanel notification added by shivani 
					$customer_type  = $this->db->rp_getValue("orders", "customer_type", "id='" . $order_id . "'");
					if ($customer_type == 2)  //distributor
					{
						$upper_chanel_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $customer_id . "'");
					} else if ($customer_type == 3) //retailer 
					{
						$upper_chanel_id = $this->db->rp_getValue("executive", "dealer_distributor_id", "id='" . $customer_id . "'");
					}
					$this->objPushNotification->commonNotification($upper_chanel_id, $order_id, "orders", "Order Added", $notification_description, "customer", "orders");
					// send customer upper chanel notification added by shivani

					/*require_once("push_notification.class.php");
					$this->objPushNotification=new PushNotification();
					$created_date=date("Y-m-d H:i:s");
					$notification_title="Order Add By ".$customer_r['cname'];
					$notification_description="Order of <b>Qty.".$total_qty."</b> for date <b>".date('d-m-Y',strtotime($created_date))."</b> Added by <b>".$customer_r['cname']."</b>";
					// $notification_type="1";
					$type_slug="";

					$rows 	= array("user_id","referance_id","referance_type","notification_title","notification_description","notification_type","type_slug");
				    $values = array($detail['cid'],$order_id,"orders",$notification_title,$notification_description,$notification_type,$type_slug);
				    $insert = $this->db->rp_insert("notification",$values,$rows,0);
					
					$msg = array(
						"type"		     => 'order',
						"title"		     => $notification_title,
						"description"    => $notification_description,
						"user_id"        => $detail['cid'],
						"reference_id"   => $order_id,
						"item_id"        => $order_id,
						"reference_type" => 'orders',
					);
					$where="refreshToken!='' AND id='".$detail['cid']."'";
					$refreshTokens[]=$this->db->rp_getValue("executive","refreshToken",$where,0);
					$result1=$this->objPushNotification->send_CustomerNotification($msg,$refreshTokens,0);*/

					///////////////////////// notification //////////////////// 
					$customer_order_detail = $this->db->rp_getData("orders", "*", "id='" . $order_id . "'");
					if ($customer_order_detail) {
						$customer_order_detail = mysqli_fetch_assoc($customer_order_detail);
						$customer_order_items = $this->db->rp_getData("order_product_item", "*", "order_id='" . $order_id . "'", "", 0);
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
							$Params['order_id'] = $order_id;
							$Params['text'] = "Create Order for." . $customer_r['company_name'];
							//$Body=$this->notification->getEmailBodyForOrders('CREATE_ORDER',$Params);

							//$this->system->AddEmailToQueue($detail['cid'],$dealer_email,$Body,date('Y-m-d H:i:s'),0,$order_id,"Order");
						}

						$admin_email  = $this->db->rp_getValue(CTABLE_ADMIN, "email", "id='1'");

						//Admin Email.....
						if ($admin_email != "") {
							$Params['order_id'] = $order_id;
							$Params['text'] = "Order Placed for." . $customer_r['company_name'];
							//$Body=$this->notification->getEmailBodyForOrders('CREATE_ORDER',$Params);

							//$this->system->AddEmailToQueue($detail['cid'],$admin_email,$Body,date('Y-m-d H:i:s'),0,$order_id,"Order");
						}
						// Customer Email.......
						if ($customer_r['email'] != "") {
							$Params['order_id'] = $order_id;
							$Params['text'] = "Thank you for your Order";
							//$Body=$this->notification->getEmailBodyForOrders('CREATE_ORDER',$Params);

							//$this->system->AddEmailToQueue($detail['cid'],$customer_r['email'],$Body,date('Y-m-d H:i:s'),0,$order_id,"Order");
						}
						$reply = array("ack" => 1, "developer_msg" => "Your Order is Placed Successfully", "ack_msg" => "Your Order is Placed Successfully", "result" => $result);
						return $reply;
					}
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Your Order is Not Placed", "ack_msg" => "Your Order is Not Placed");
					return $reply;
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Something went wrong!! Product Not in cart please check1", "ack_msg" => "Something went wrong!! Product Not in cart please check");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "No Order Found", "ack_msg" => "No Order Found");
			return $reply;
		}
	}

	public function AddToCartApi($detail, $products)
	{
		//echo "hello";
		//print_r($products);exit;
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0");
		if ($detail['order_id'] != "") {
			$table_main = "orders";
			$table_item = "order_product_item";
		} else if ($detail['quotation_id'] != "") {
			$table_main = "quotation_detail";
			$table_item = "quotation_product_item";
		} else {
			$table_main = "cart_detail";
			$table_item = "cart_item";
		}
		if ($customer_d) {
			$where = "";
			if ($detail['sales_executive_id'] != "") {
				$where = " AND sales_id='" . $detail['sales_executive_id'] . "' ";
			} else {
				$detail['sales_executive_id'] = "";
				$where = " AND sales_id=0";
			}

			if ($detail['order_id'] != "") {
				$check_cart_exist = $this->db->rp_getTotalRecord($table_main, "id='" . $detail['order_id'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "'" . $where, 0);
			} else if ($detail['quotation_id'] != "") {
				$check_cart_exist = $this->db->rp_getTotalRecord($table_main, "id='" . $detail['quotation_id'] . "' AND isDelete=0 " . $where, 0);
			} else {
				$check_cart_exist = $this->db->rp_getTotalRecord($table_main, "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "' AND status=-1 AND cart_type='" . $detail['cart_type'] . "'" . $where, 0);
			}
			//echo $check_cart_exist;exit;

			if ($check_cart_exist != 0) {
				// already cart exist
				if ($detail['order_id'] != "") {
					$order_id = $detail['order_id'];
				} else if ($detail['quotation_id'] == "") {
					if ($detail['sales_executive_id'] == "") {
						$detail['sales_executive_id'] = 0;
					}
					$order_id = $this->db->rp_getValue($table_main, "id", "customer_id='" . $detail['cid'] . "' AND sales_id='" . $detail['sales_executive_id'] . "' AND isDelete=0 AND cart_type='" . $detail['cart_type'] . "' AND status=-1" . " ORDER BY id DESC", 0);
				}

				$type_of_executive = $this->db->rp_getValue("executive", "type_of_executive", "id='" . $detail['cid'] . "' AND isDelete=0", 0);

				$price_list_id = $this->db->rp_getValue("executive", "price_list_id", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
				if (!empty($products)) {
					if ($detail['quotation_id'] != "") {
						$total_items = $this->db->rp_getTotalRecord($table_item, "quotation_id='" . $detail['quotation_id'] . "'");
					} else {
						$total_items = $this->db->rp_getTotalRecord($table_item, "order_id='" . $order_id . "'");
					}
					foreach ($products as $p) {
						// $product_detail=$this->db->rp_getData("product","*","id='".$p['pid']."'","","0");
						//check item already in cart or not
						if ($detail['quotation_id'] != "") {
							$check_item_in_cart = $this->db->rp_getValue($table_item, "id", "quotation_id='" . $detail['quotation_id'] . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");
						} else {
							$check_item_in_cart = $this->db->rp_getValue($table_item, "id", "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0");
						}
						if ($check_item_in_cart != 0 && $check_item_in_cart != "") {
							// update qty of that item
							if ($detail['quotation_id'] != "") {
								$pro_r = $this->db->rp_getData($table_item, "*", "quotation_id='" . $detail['quotation_id'] . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0", "", 0);
							} else {
								$pro_r = $this->db->rp_getData($table_item, "*", "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0", "", 0);
							}
							$pro_d = mysqli_fetch_assoc($pro_r);
							$update_qty = $p['qty'] + $pro_d['pro_qty'];
							$pro_d['unitprice'] = $this->db->rp_num($pro_d['unitprice']);
							$update_totalprice = $this->db->rp_num($update_qty * $pro_d['unitprice']);

							$Newtotalprice = $update_qty * $pro_d['unitprice'];
							$totalprice = $this->db->rp_num($Newtotalprice);
							$original_price = $unitprice;
							$taxable_amount = $Newtotalprice;

							$GST = $pro_d['igst_tax'];

							$item_gst_amount1 = (($taxable_amount * $GST) / 100);
							$sub_total = ($taxable_amount + $item_gst_amount1);
							$bag = $p['box_qty'] + $pro_d['box_qty'];
							$update_cartoon_qty = $p['cartoon_qty'] + $pro_d['cartoon_qty'];
							$loose = 0;
							/*$update_box_qty=$this->db->rp_num($update_qty/$pro_d['inner_size']);
							$update_cartoon_qty=$this->db->rp_num($update_box_qty/$pro_d['outer_size']);*/

							// $update_box_qty=$p['box_qty'];
							// $update_cartoon_qty=$p['cartoon_qty'];

							/*$update_box_qty=$p['box_qty']+$pro_d['box_qty'];
							$update_cartoon_qty=$p['cartoon_qty']+$pro_d['cartoon_qty'];
							*/

							/*$bag_box_id = $this->db->rp_getValue("product","unit_id","id='".$p['pid']."' AND isDelete=0",0);
							if($bag_box_id==2)
							{
								$bag = ($update_qty / $pro_d['inner_size']);
								$bag = round($bag);
								$update_cartoon_qty = 0;
								$loose = 0;
							}
							else if($bag_box_id==3)
							{
								$update_cartoon_qty = round($update_qty / $pro_d['outer_size']);
								$bag = 0;
								$loose = 0;
							}
							else if($bag_box_id==4)
							{
								$bag =$p['box_qty']+$pro_d['box_qty'];
								$update_cartoon_qty =$p['cartoon_qty']+$pro_d['cartoon_qty'];
								$loose = 0;
							}else if($bag_box_id==5)
							{
								$bag =$p['box_qty']+$pro_d['box_qty'];
								$update_cartoon_qty =$p['cartoon_qty']+$pro_d['cartoon_qty'];
								$loose=0;
								//echo $loose;exit;

							}
							else
							{
								$update_cartoon_qty=round($update_qty/$pro_d['outer_size']);//box
								if($update_cartoon_qty!=0)
								{
									$bagqty = $update_qty - ($pro_d['outer_size'] * $update_cartoon_qty);
									if ($bagqty < 0) 
									{
										$bagqty = $bagqty * -1;
									}
									$bagqty = ($bagqty != "") ? floor($bagqty) : 0;
									$bag = ($bagqty / $pro_d['inner_size']);
									$bag = floor($bag);
								}
								else 
								{
									$bag = ($update_qty / $pro_d['inner_size']);
									$bag = round($bag);
								}

								//loose qty calculation
									$total_bag = $bag*$pro_d['inner_size'];
									$total_box = $update_cartoon_qty*$pro_d['outer_size'];
									$totalsum = $total_bag+$total_box;
									$loose =  round($update_qty-$totalsum);
								//loose qty calculation
							}*/

							$update_item = array(
								"pro_qty" => $update_qty,
								"remaining_qty" => $update_qty,
								"totalprice" => $update_totalprice,
								"box_qty" => $bag,
								"loose_qty" => "0",
								"cartoon_qty" => $update_cartoon_qty,
								"igst_tax" => $GST,
								"igst_amount" => $item_gst_amount1,
								"taxable" => $taxable_amount,
								"subtotal" => $sub_total,
								"order_item_brand_id" => ($p['order_item_brand_id']) ? $p['order_item_brand_id'] : "",
								"modified_date" => date("Y-m-d H:i:s"),

							);

							if ($detail['quotation_id'] != "") {

								$isUpdate = $this->db->rp_update($table_item, $update_item, "quotation_id='" . $detail['quotation_id'] . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0", 0);
							} else {
								/*log entry*/
								$order_no = $this->db->rp_getValue("cart_detail", "order_no", "id='" . $order_id . "'", 0);
								$sales_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $detail['sales_executive_id'] . "'", 0);
								$customer_id = $this->db->rp_getValue("cart_detail", "customer_id", "id='" . $order_id . "'", 0);
								$module_name = "Order";
								$flag = "Application";
								$log_description = $module_name . " " . $order_no . " Edited Cart Qty By " . $sales_name . " ON " . date("Y-m-d H:i:s");
								/*log entry*/

								$isUpdate = $this->db->rp_update($table_item, $update_item, "order_id='" . $order_id . "' AND pro_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND isDelete=0", 0, $log_description, $flag, $module_name, $detail['sales_executive_id'], $customer_id);
							}
							$reply = array("ack" => 1, "developer_msg" => "Product Updated Successfully", "ack_msg" => "Product Updated Successfully", "order_id" => $order_id);
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

									/*
									$p['box_qty']=$p['box_qty'];
									$p['cartoon_qty']=$p['cartoon_qty'];
									*/

									$update_qty = $p['qty'];
									$bag = $p['box_qty'];
									$update_cartoon_qty = $p['cartoon_qty'];
									$loose = 0;
									//echo $new_qty_bag;exit;

									/*$bag_box_id = $this->db->rp_getValue("product","unit_id","id='".$p['pid']."' AND isDelete=0");
									if($bag_box_id==2)
									{
										$bag = ($update_qty / $p['inner_size']);
										$bag = round($bag);
										$update_cartoon_qty = 0;
										$loose = 0;
									}
									else if($bag_box_id==4)
									{
										$bag =$p['box_qty'];
										$update_cartoon_qty =$p['cartoon_qty'];
										
										 //round($update_qty / $pro_d['outer_size']);
										//$bag = 0;
										$loose = 0;
									}else if($bag_box_id==5)
									{
										$bag =$p['box_qty'];
										$update_cartoon_qty =$p['cartoon_qty'];
										$loose = 0;
										//echo $loose; exit;

									}else if($bag_box_id==3)
									{
										$update_cartoon_qty = round($update_qty / $p['outer_size']);
										$bag = 0;
										$loose = 0;
									}
									else
									{
										$update_cartoon_qty=round($update_qty/$p['outer_size']);//box
										if($update_cartoon_qty!=0)
										{
											$bagqty = $update_qty - ($p['outer_size'] * $update_cartoon_qty);
											if ($bagqty < 0) 
											{
												$bagqty = $bagqty * -1;
											}
											$bagqty = ($bagqty != "") ? floor($bagqty) : 0;
											$bag = ($bagqty / $p['inner_size']);
											$bag = floor($bag);
										}
										else 
										{
											$bag = ($update_qty / $p['inner_size']);
											$bag = round($bag);
										}

											//loose qty calculation
											$total_bag = $bag*$p['inner_size'];
											$total_box = $update_cartoon_qty*$p['outer_size'];
											$totalsum = $total_bag+$total_box;
											$loose =  round($update_qty-$totalsum);
											//loose qty calculation
									}
						*/
									$unitprice = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);
									$orignal_price = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);

									// for including gst calculation

									$is_including = $this->db->rp_getValue("product_weight_price", "is_including", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);
									if ($is_including == 1) {
										if ($product_detail['igst'] != "" && $product_detail['igst'] != 0) {
											$gst_val = 1 + ($product_detail['igst'] / 100);
											$unitprice = $unitprice / $gst_val;
										}
									}

									// for including gst calculation

									$unitprice = $this->db->rp_num($unitprice);
									if ($type_of_executive == "8") {
										$GST = 0.1;
									} else {
										$GST = $product_detail['igst'];
									}

									$totalprice = $p['qty'] * $unitprice;
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

									// echo $original_price."-".$unitprice;exit;

									if ($price_list_id != 0) {
										$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
										if ($check_product_in_list > 0) {
											$add_price_list_id = $price_list_id;
											// $price_list_price=$this->db->rp_getValue("product_price_list","price","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");

											$price_list_price = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);

											$discount = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");



											if ($is_including == 1) {
												if ($product_detail['igst'] != "" && $product_detail['igst'] != 0) {

													// $gst_val="1.".$product_detail['igst'];
													$gst_val = 1 + ($product_detail['igst'] / 100);
													$price_list_price = $price_list_price / $gst_val;
													$unitprice = ($price_list_price * $discount) / 100;
													$unitprice = $this->db->rp_num($unitprice);
													$unitprice = ($price_list_price) - ($unitprice);
												} else {
													$unitprice = ($price_list_price * $discount) / 100;
													$unitprice = $this->db->rp_num($unitprice);
													$unitprice = ($price_list_price) - ($unitprice);
												}
											} else {
												$unitprice = ($price_list_price * $discount) / 100;
												$unitprice = $this->db->rp_num($unitprice);
												$unitprice = ($price_list_price) - ($unitprice);
											}
											$unitprice = $this->db->rp_num($unitprice);
											$price_list_discounted_price = $unitprice;

											//$GST=$product_detail['igst'];
											$totalprice = $p['qty'] * $unitprice;
											$totalprice = $this->db->rp_num($totalprice);
											$original_price = $orignal_price;
											$final_price = $this->db->rp_num($totalprice);


											$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
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


									$totalprice = $p['qty'] * $unitprice;
									$totalprice = $this->db->rp_num($totalprice);
									$Newtotalprice = $p['qty'] * $unitprice;
									$totalprice = $this->db->rp_num($totalprice);
									$original_price = $orignal_price;
									$taxable_amount = $Newtotalprice;
									$item_gst_amount1 = $this->db->rp_num((($taxable_amount * $GST) / 100));
									$sub_total = ($taxable_amount + $item_gst_amount1);

									$rows 	= array(
										$detail['quotation_id'] != "" ? "quotation_id" : "order_id",
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
										"order_item_brand_id",
									);
									$values = array(
										$detail['quotation_id'] != "" ? $detail['quotation_id'] : $order_id,
										$top_cat_id,
										$cat_id,
										$p['pid'],
										$p['weight_id'],
										trim($p['item_name']),
										$p['qty'],
										$p['qty'],
										$p['inner_size'],
										$p['outer_size'],
										$bag,
										$update_cartoon_qty,
										$loose,
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
										0,
										0,
										$GST,
										$item_gst_amount1,
										$taxable_amount,
										$sub_total,
										$hsn_code,
										0,
										0,
										$is_including,
										($p['item_order_unit']) ? $p['item_order_unit'] : "",
										($p['order_item_brand_id']) ? $p['order_item_brand_id'] : "",
									);
									$total_qty += $p['qty'];
									$sub_total += $final_price;

									/*log entry*/
									$sales_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $detail['sales_executive_id'] . "'", 0);
									$order_no = $this->db->rp_getValue("cart_detail", "order_no", "id='" . $order_id . "'", 0);
									$customer_id = $this->db->rp_getValue("cart_detail", "customer_id", "id='" . $order_id . "'", 0);
									$module_name = "Order";
									$flag = "Application";
									$log_description = $module_name . " " . $order_no . " New Product Added To Cart By " . $sales_name . " ON " . date("Y-m-d H:i:s");
									/*log entry*/

									$item_id = $this->db->rp_insert($table_item, $values, $rows, 0, $log_description, $flag, $module_name, $detail['sales_executive_id'], $customer_id);



									if ($item_id != 0) {

										$get_free_product = $this->db->rp_getData("scheme_master_item", "*", "isDelete=0 AND product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "' AND qty='" . $p['qty'] . "'", "", 0);


										$get_order_date = $this->db->rp_getValue("orders", "order_date", "isDelete=0 AND id='" . $order_id . "'", 0);
										while ($get_free_product_d = mysqli_fetch_array($get_free_product)) {

											$get_dates = $this->db->rp_getData("scheme_master", "	start_date,end_date", "isDelete=0 AND id='" . $get_free_product_d['scheme_id'] . "'", "", 0);
											$date_array = mysqli_fetch_array($get_dates);


											if (strtotime($date_array['start_date']) <= strtotime($get_order_date) && strtotime($date_array['end_date']) >= strtotime($get_order_date)) {

												$rows_item = array("order_id", "order_item_id", "scheme_id", "pro_id", "weight_id", "pro_qty");
												$values_item = array($order_id, $item_id, $get_free_product_d['scheme_id'], $get_free_product_d['product_id_2'], $get_free_product_d['weight_id_2'], $get_free_product_d['free_qty']);
												$scheme_item_insert = $this->db->rp_insert("order_scheme_items", $values_item, $rows_item, 0);
											}
										}
										$reply = array("ack" => 1, "developer_msg" => "Product added Successfully", "ack_msg" => "Product added Successfully", "order_id" => $order_id);
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
				$shipping_address = $this->db->rp_getValue("customer_vs_shipping_address", "shipping_address", "customer_id='" . $customer_r['id'] . "' AND isDelete=0 ORDER BY id ASC", 0);

				$order_no = $this->db->getLastInsertId($table_main);
				$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);
				$created_date	= date('Y-m-d H:i:s');
				$order_date	= isset($detail['order_date']) ? $detail['order_date'] : date("Y-m-d");
				$rows 	= array(
					"order_no",
					"customer_id",
					"dealer_id",
					"super_stockist_id",
					"customer_name",
					"company_name",
					"client_code",
					"customer_flag",
					"customer_type",
					"contact_number",
					"address",
					"city",
					"state",
					"country",
					"email",
					"gst",
					"order_date",
					// "brand_id",
					"status",
					"sales_id",
					"billing_address",
					"shipping_address",
					"entry_flag",
					"transport_through",
					"transport_name",
				);
				$values = array(
					$order_no,
					$customer_r['id'],
					$customer_r['dealer_distributor_id'],
					$customer_r['super_stockist_id'],
					$customer_r['cname'],
					$customer_r['company_name'],
					$customer_r['client_code'],
					$customer_r['customer_flag'],
					$customer_r['type_of_executive'],
					$customer_r['phone'] != "" ? $customer_r['phone'] : "",
					addslashes($customer_r['address']),
					$customer_r['city'],
					$customer_r['state'],
					$customer_r['country'],
					$customer_r['email'],
					$customer_r['gst'],
					$order_date,
					// $customer_r['brand_id'],
					-1,
					$detail['sales_executive_id'],
					addslashes($customer_r['billing_address']),
					isset($shipping_address) ? addslashes($shipping_address) : "",
					// 5,
					$detail['entry_flag'],
					$customer_r['transport_by_id'],
					$customer_r['transporter_id'],
				);

				/*log entry*/
				$sales_name = $this->db->rp_getValue("sales_executive", "name", "id='" . $detail['sales_executive_id'] . "'", 0);
				$module_name = "Order";
				$flag = "Application";
				$log_description = $module_name . " " . $order_no . " Added To Cart By " . $sales_name . " ON " . date("Y-m-d H:i:s");
				/*log entry*/

				$order_id = $this->db->rp_insert($table_main, $values, $rows, 0, $log_description, $flag, $module_name, $detail['sales_executive_id'], $customer_r['id'], 0);
				if ($order_id != 0) {
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
									/*$p['box_qty']=$p['box_qty'];
									$p['cartoon_qty']=$p['cartoon_qty'];*/
									$update_qty = $p['qty'];

									$bag = $p['box_qty'];
									$update_cartoon_qty = $p['cartoon_qty'];
									$loose = 0;
									//echo $update_qty;exit();
									/*$bag_box_id = $this->db->rp_getValue("product","unit_id","id='".$p['pid']."' AND isDelete=0",0);

									if($bag_box_id==2)
									{
										$bag = ($update_qty / $ctable_item_weight_detail['inner_size']);
										//echo $bag;exit();
										$bag = round($bag);
										$update_cartoon_qty = 0;
										$loose = 0;
									}
									else if($bag_box_id==3)
									{
										$update_cartoon_qty = round($update_qty / $ctable_item_weight_detail['outer_size']);
										$bag = 0;
										$loose = 0;
									}else if($bag_box_id==4)
									{
										$bag =$p['box_qty'];
										$update_cartoon_qty =$p['cartoon_qty'];
										
										//$bag_qty=$p['box_qty']*$ctable_item_weight_detail['inner_size'];
										//$cartoon_qty=$p['cartoon_qty']*$ctable_item_weight_detail['outer_size'];
										//$qty=$bag_qty+$cartoon_qty;
										//$loose=$update_qty-$qty;

										 //round($update_qty / $pro_d['outer_size']);
										//$bag = 0;
										$loose = 0;
									}else if($bag_box_id==5)
									{
										$bag =$p['box_qty'];
										$update_cartoon_qty =$p['cartoon_qty'];
										$loose = 0;

										//print_r($p);exit;
										//$bag_qty=$bag*$ctable_item_weight_detail['inner_size'];
										//$cartoon_qty=$update_cartoon_qty*$ctable_item_weight_detail['outer_size'];
										//$qty=$bag_qty+$cartoon_qty;
										//$loose=$update_qty-$qty;
										//echo $loose; exit;

									}
									else
									{
										$update_cartoon_qty=round($update_qty/$ctable_item_weight_detail['outer_size']);//box
										if($update_cartoon_qty!=0)
										{
											$bagqty = $update_qty - ($ctable_item_weight_detail['outer_size'] * $update_cartoon_qty);
											if ($bagqty < 0) 
											{
												$bagqty = $bagqty * -1;
											}
											$bagqty = ($bagqty != "") ? floor($bagqty) : 0;
											$bag = ($bagqty / $ctable_item_weight_detail['inner_size']);
											$bag = floor($bag);
										}
										else 
										{
											$bag = ($update_qty / $ctable_item_weight_detail['inner_size']);
											$bag = round($bag);
										}

										//loose qty calculation
											$total_bag = $bag*$ctable_item_weight_detail['inner_size'];
											$total_box = $update_cartoon_qty*$ctable_item_weight_detail['outer_size'];
											$totalsum = $total_bag+$total_box;
											$loose =  round($update_qty-$totalsum);
										//loose qty calculation
									}*/
									//echo $bag;exit();


									$unitprice = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);
									$orignal_price = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);


									// for including gst calculation 
									$is_including = $this->db->rp_getValue("product_weight_price", "is_including", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);
									if ($is_including == 1) {
										if ($product_detail['igst'] != "" && $product_detail['igst'] != 0) {
											$gst_val = 1 + ($product_detail['igst'] / 100);
											$unitprice = $unitprice / $gst_val;
										}
									}

									// for including gst calculation

									$unitprice = $this->db->rp_num($unitprice);
									$type_of_executive = $this->db->rp_getValue("executive", "type_of_executive", "id='" . $detail['cid'] . "' AND isDelete=0", 0);
									if ($type_of_executive == "8") {

										$GST = 0.1;
									} else {
										$GST = $product_detail['igst'];
									}
									//echo $GST;exit();

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
											// $price_list_price=$this->db->rp_getValue("product_price_list","price","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");




											$price_list_price = $this->db->rp_getValue("product_weight_price", "price", "product_id='" . $p['pid'] . "' AND weight_id='" . $p['weight_id'] . "'", 0);

											$discount = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");



											if ($is_including == 1) {
												if ($product_detail['igst'] != "" && $product_detail['igst'] != 0) {

													$gst_val = 1 + ($product_detail['igst'] / 100);
													$price_list_price = $price_list_price / $gst_val;
													$unitprice = ($price_list_price * $discount) / 100;
													$unitprice = $this->db->rp_num($unitprice);
													$unitprice = ($price_list_price) - ($unitprice);
												} else {
													$unitprice = ($price_list_price * $discount) / 100;
													$unitprice = $this->db->rp_num($unitprice);
													$unitprice = ($price_list_price) - ($unitprice);
												}
											} else {
												$unitprice = ($price_list_price * $discount) / 100;
												$unitprice = $this->db->rp_num($unitprice);
												$unitprice = ($price_list_price) - ($unitprice);
											}

											$unitprice = $this->db->rp_num($unitprice);
											$price_list_discounted_price = $unitprice;

											//$GST=$product_detail['igst'];
											$totalprice = $p['qty'] * $unitprice;
											$totalprice = $this->db->rp_num($totalprice);
											$original_price = $orignal_price;
											// $original_price=$unitprice;
											$final_price = $this->db->rp_num($totalprice);

											$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");


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

									$totalprice = $p['qty'] * $unitprice;
									$Newtotalprice = $p['qty'] * $unitprice;
									$totalprice = $this->db->rp_num($totalprice);
									$original_price = $orignal_price;
									$taxable_amount = $Newtotalprice;
									$item_gst_amount1 = $this->db->rp_num((($taxable_amount * $GST) / 100));
									$sub_total = ($taxable_amount + $item_gst_amount1);

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
										"order_item_brand_id",
									);
									$values = array(
										$order_id,
										$top_cat_id,
										$cat_id,
										$p['pid'],
										$p['weight_id'],
										$p['item_name'],
										$p['qty'],
										$p['qty'],
										$p['inner_size'],
										$p['outer_size'],
										$bag,
										$update_cartoon_qty,
										$loose,
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
										0,
										0,
										$GST,
										$item_gst_amount1,
										$taxable_amount,
										$sub_total,
										$hsn_code,
										0,
										0,
										$is_including,
										($p['item_order_unit']) ? $p['item_order_unit'] : "",
										($p['order_item_brand_id']) ? $p['order_item_brand_id'] : "",
									);
									$total_qty += $p['qty'];
									$sub_total += $final_price;
									$item_id = $this->db->rp_insert($table_item, $values, $rows, 0);


									// $this->AddschemeItems($order_id,$item_id,$p['pid'],$p['weight_id'],$p['qty']);

									// $get_free_product=$this->db->rp_getData("scheme_master_item","*","isDelete=0 AND product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."' AND qty='".$p['qty']."'","",0);


									// $get_order_date=$this->db->rp_getValue("orders","order_date","isDelete=0 AND id='".$order_id."'",0);
									// while($get_free_product_d=mysqli_fetch_array($get_free_product))
									// {

									// 	$get_dates=$this->db->rp_getData("scheme_master","	start_date,end_date","isDelete=0 AND id='".$get_free_product_d['scheme_id']."'","",0);
									// 	$date_array=mysqli_fetch_array($get_dates);


									// 	if(strtotime($date_array['start_date']) <= strtotime($get_order_date) && strtotime($date_array['end_date']) >= strtotime($get_order_date))
									// 	{

									// 		$rows_item=array("order_id","order_item_id","scheme_id","pro_id","weight_id","pro_qty");
									// 		$values_item=array($order_id,$item_id,$get_free_product_d['scheme_id'],$get_free_product_d['product_id_2'],$get_free_product_d['weight_id_2'],$get_free_product_d['free_qty']);
									// 		$scheme_item_insert=$this->db->rp_insert("order_scheme_items",$values_item,$rows_item,0);
									// 	}


									// }

								}
							}
						}

						$total_items = $this->db->rp_getTotalRecord($table_item, "order_id='" . $order_id . "' AND isDelete=0");
						if ($total_items != 0) {
							$reply = array("ack" => 1, "developer_msg" => "Order Added To Cart Successfully", "ack_msg" => "Order Added To Cart Successfully", "order_id" => $order_id);
							return $reply;
						} else {
							$reply = array("ack" => 0, "developer_msg" => "Order Item Not inserted", "ack_msg" => "Order Item Not inserted");
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
	public function UpdateOrderApi($detail, $products)
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

			$check_cart_exist = $this->db->rp_getTotalRecord("orders", "id='" . $detail['order_id'] . "' AND isDelete=0 AND sales_id='" . $detail['sales_executive_id'] . "'" . $where, 0);

			if ($check_cart_exist != 0) {
				if ($detail['order_id'] != 0) {
					$order_id = $detail['order_id'];
					if (!empty($products)) {
						// delete product
						$this->db->rp_delete("order_product_item", "order_id='" . $order_id . "'");
						// delete product

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
									/*$p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
									$p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];*/
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

									if ($price_list_id != 0) {
										$check_product_in_list = $this->db->rp_getTotalRecord("product_price_list", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
										if ($check_product_in_list > 0) {
											$add_price_list_id = $price_list_id;
											$price_list_price = $this->db->rp_getValue("product_price_list", "price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
											$unitprice = $this->db->rp_getValue("product_price_list", "discounted_price", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
											$unitprice = $this->db->rp_num($unitprice);
											$price_list_discounted_price = $unitprice;

											$GST = $product_detail['igst'];
											$totalprice = $p['qty'] * $unitprice;
											$totalprice = $this->db->rp_num($totalprice);
											$original_price = $price_list_price;
											$final_price = $this->db->rp_num($totalprice);

											$price_list_discount_type = $this->db->rp_getValue("product_price_list", "discount_type", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");

											$discount = $this->db->rp_getValue("product_price_list", "discount", "pid='" . $p['pid'] . "' AND weight_id='" . $ctable_item_weight_detail['weight_id'] . "' AND price_list_id='" . $price_list_id . "'");
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
									);
									$values = array(
										$order_id,
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
										$final_price,
										$user_discount,
										$unitprice_amt,
										$add_price_list_id,
										$price_list_price,
										$price_list_discounted_price,
										$price_list_discounted_amount,
										$price_list_discount_type,
										$price_list_discount,
									);
									$total_qty += $p['qty'];
									$sub_total += $final_price;
									$item_id = $this->db->rp_insert("order_product_item", $values, $rows, 0);
								}
							}
						}

						$total_items = $this->db->rp_getTotalRecord("order_product_item", "order_id='" . $order_id . "' AND isDelete=0");
						if ($total_items != 0) {
							$reply = array("ack" => 1, "developer_msg" => "Order Updated To Cart Successfully", "ack_msg" => "Order Updated To Cart Successfully", "order_id" => $order_id);
							return $reply;
						} else {
							$reply = array("ack" => 0, "developer_msg" => "Order Item Not Updated3", "ack_msg" => "Order Item Not Updated4");
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

	public function PlaceOrderPanel($detail)
	{
		// echo "<pre>";
		// print_r($detail); exit; 
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0");
		$customer_r = mysqli_fetch_assoc($customer_d);
		$order_items_r = $this->db->rp_getData("order_product_item", "*", "order_id='" . $detail['order_id'] . "' AND isDelete=0 ");
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
						//$item_id = $this->db->rp_insert("order_product_item",$values,$rows,0);
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
			// echo $additional_discount_amount;exit;
			if ($detail['gst_apply_flag'] != 0) {
				$sub_total1 = $sub_total + $detail['transport_charge'] + $detail['packing_charge'];
				// $gst_amount = $this->db->rp_num(($sub_total1*$GST)/100);
				// echo $sub_total;exit;
				$gst_amount = $tot_gst_amount;
				// echo $sub_total1;
				$grand_total = $this->db->rp_num($sub_total1 + $gst_amount);
			} else {
				$sub_total1 = $sub_total + $detail['transport_charge'] + $detail['packing_charge'];
				$gst_amount = 0;
				$grand_total = $sub_total1;
			}
			// echo $grand_total;
			if ($detail['tcs_apply_flag'] != 0) {
				// $sub_total1 = $sub_total+$detail['transport_charge']+$detail['packing_charge'];
				// $gst_amount = $this->db->rp_num(($sub_total1*$GST)/100);
				$tcs_amount = $this->db->rp_num(($grand_total * TCS_CHARGE_IN_PER) / 100);
				$grand_total = $this->db->rp_num($grand_total + $tcs_amount);
			} else {
				// $sub_total1 = $sub_total+$detail['transport_charge']+$detail['packing_charge'];
				$tcs_amount = 0;
				$grand_total = $grand_total;
			}
			// echo $sub_total1;exit;
			// echo $grand_total;exit;

			$dt = date("Y-m-d");
			//$tcs_amount = 0;
			// echo "<pre>";
			// print_r($detail);exit;
			$isUpdated = $this->db->rp_update("orders", array("total_qty" => $total_qty, "subtotal" => $sub_total1, "grand_total" => $grand_total, "remaining_amount" => round($grand_total), "cash_discount_amount" => $detail['cash_discount_amount'], "cash_discount" => $detail['cash_discount'], "additional_discount_amount" => $detail['additional_discount_amount'], "additional_discount" => $detail['additional_discount'], "igst_amount" => $gst_amount, "status" => 0, "remarks" => $detail['remarks'], "chalan_no" => $detail['chalan_no'], "po_no" => $detail['po_no'], "po_date" => date('Y-m-d', strtotime($detail['po_date'])), "terms_comdition" => $detail['terms_comdition'], "faithfully" => $detail['faithfully'], "transport_name" => $detail['transport_name'], "transport_through" => $detail['transport_through'], "transport_charge" => $detail['transport_charge'], "billing_address" => $detail['billing_address'], "shipping_address" => $detail['shipping_address'], "packing_charge" => $detail['packing_charge'], "vendor_code" => $detail['vendor_code'], "tendor_code" => $detail['tendor_code'], "roundoff" => $detail['round_off'], "grand_total_rounded" => round($grand_total), "tcs_per" => TCS_CHARGE_IN_PER, "tcs_amount" => $tcs_amount, "update_entry_flag" => 1, "transport_charge_gst" => $detail['transport_charge_gst'], "packing_charge_gst" => $detail['packing_charge_gst'], "cd_gst" => $detail['cd_gst'], "ad_gst" => $detail['ad_gst'], "type_of_company" => $detail['type_of_company'], "terms_condition_id" => $detail['terms_condition_id'], "booking_place" => $detail['booking_place'], "booking_pincode" => $detail['booking_pincode'], "max_dispatch_date" => date('Y-m-d', strtotime($detail['max_dispatch_date'])), "sales_id" => $detail['sales_executive_id']), "id='" . $detail['order_id'] . "'", 0);

			if ($isUpdated) {
				//update GST number in Customer
				$customer_id = $this->db->rp_getValue("orders", "customer_id", "id='" . $detail['order_id'] . "'", 0);
				$this->db->rp_update("executive", array("gst" => $detail['name_gstin']), "id='" . $customer_id . "'", 0);
				$sales_id = $this->db->rp_getValue("executive", "seid", "id='" . $customer_id . "'", 0);
				$company_name = $this->db->rp_getValue("executive", "company_name", "id='" . $customer_id . "'", 0);
				$customer_name = $this->db->rp_getValue("executive", "cname", "id='" . $customer_id . "'", 0);
				if ($sales_id == 0) {
					$isUpdated1 = $this->db->rp_update("executive", array("seid" => $detail['sales_executive_id']), "id='" . $customer_id . "'", 0);
				}
				if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] != 0) {
					if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 2) //sales executive 
					{
						$order_add_name = $this->db->rp_getValue("sales_executive", "name", "isDelete=0 AND id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
					}
					if ($_SESSION[SITE_SESS . 'REFERANCE_TYPE'] == 3) //customer  
					{
						$order_add_name = $this->db->rp_getValue("executive", "cname", "isDelete=0 AND id='" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "'", 0);
					}
				} else {
					$order_add_name = "Admin";
				}

				$order_no  = $this->db->rp_getValue("orders", "order_no", "id='" . $detail['order_id'] . "'");
				$notification_description = "New order added with Order No " . $order_no . " by " . $order_add_name . " for " . $company_name . "-" . $customer_name;
				// send sales executive notification added by shivani     
				$sales_id = $this->db->rp_getValue("orders", "sales_id", "id='" . $detail['order_id'] . "'");
				$this->objPushNotification->commonNotification($sales_id, $detail['order_id'], "orders", "Order Added", $notification_description, "sales_executive", "orders");
				// send sales executive notification added by shivani 

				// send customer notification added by shivani
				$customer_id = $this->db->rp_getValue("orders", "customer_id", "id='" . $detail['order_id'] . "'");
				$this->objPushNotification->commonNotification($customer_id, $detail['order_id'], "orders", "Order Added", $notification_description, "customer", "orders");
				// send customer notification added by shivani 

				// send customer upper chanel notification added by shivani 
				$customer_type  = $this->db->rp_getValue("orders", "customer_type", "id='" . $detail['order_id'] . "'");
				if ($customer_type == 2)  //distributor
				{
					$upper_chanel_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $customer_id . "'");
				} else if ($customer_type == 3) //retailer 
				{
					$upper_chanel_id = $this->db->rp_getValue("executive", "dealer_distributor_id", "id='" . $customer_id . "'");
				}
				$this->objPushNotification->commonNotification($upper_chanel_id, $detail['order_id'], "orders", "Order Added", $notification_description, "customer", "orders");
				// send customer upper chanel notification added by shivani

				// update flag if order is not generate
				/*$check_order = $this->db->rp_getTotalRecord('orders',"customer_id=".$detail['customer_id'],0);
				if($check_order == 1) {
					$this->db->rp_update("executive",array("customer_flag"=>1),"id='".$detail['customer_id']."'",0);
				}*/
				$reply = array("ack" => 1, "developer_msg" => "Your Order is Placed Successfully", "ack_msg" => "Your Order is Placed Successfully");
				// $reply=array("ack"=>1,"developer_msg"=>"Your Order is Placed Successfully","ack_msg"=>"Your Order is Placed Successfully","result"=>$result);
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Your Order is Not Placed", "ack_msg" => "Your Order is Not Placed");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Something went wrong!! Product Not in cart please check2", "ack_msg" => "Something went wrong!! Product Not in cart please check");
			return $reply;
		}
	}


	public function UpdateCartDiscount($detail, $discount)
	{

		if ($detail['cart_type'] == "3") {
			$table_item = "order_product_item";
			$table_main = "orders";
		} else if ($detail['cart_type'] == "2") {
			$table_main = "quotation_detail";
			$table_item = "quotation_product_item";
		} else {
			$table_item = "cart_item";
			$table_main = "cart_detail";
		}
		if ($detail['mode'] == "edit") {
			$check_cart_exist = $this->db->rp_getTotalRecord($table_main, "isDelete=0 AND id='" . $detail['cart_id'] . "' AND sales_id='" . $detail['sales_executive_id'] . "'", 0);
		} else if ($detail['cart_type'] == "2") {
			$check_cart_exist = $this->db->rp_getTotalRecord($table_main, "isDelete=0 AND id='" . $detail['cart_id'] . "'", 0);
		} else {
			$check_cart_exist = $this->db->rp_getTotalRecord($table_main, "isDelete=0 AND id='" . $detail['cart_id'] . "' AND sales_id='" . $detail['sales_executive_id'] . "' AND status=-1", 0);
		}
		if ($check_cart_exist != 0) {
			if (!empty($discount)) {
				foreach ($discount as $d) {
					if ($detail['cart_type'] == "2") {
						$get_item = $this->db->rp_getData($table_item, "*", "isDelete=0 AND quotation_id='" . $detail['cart_id'] . "'", "", 0);
					} else {
						$get_item = $this->db->rp_getData($table_item, "*", "isDelete=0 AND order_id='" . $detail['cart_id'] . "'", "", 0);
					}
					while ($get_item_d = mysqli_fetch_assoc($get_item)) {
						if ($d['tcid'] == $get_item_d['top_cat_id'] && $d['cid'] == $get_item_d['cat_id']) {

							$discount_amount = ($get_item_d['original_price'] * $d['discount']) / 100;
							$unitprice  = $get_item_d['original_price'] - $discount_amount;
							$totalprice  = $get_item_d['pro_qty'] * $unitprice;

							if ($detail['cart_type'] == "2") {

								$update = $this->db->rp_update($table_item, array("discount" => $d['discount'], "discount_amount" => $discount_amount, "unitprice" => $unitprice, "totalprice" => $totalprice), "quotation_id='" . $detail['cart_id'] . "' AND id='" . $get_item_d['id'] . "'", 0);
							} else {
								$update = $this->db->rp_update($table_item, array("discount" => $d['discount'], "discount_amount" => $discount_amount, "unitprice" => $unitprice, "totalprice" => $totalprice), "order_id='" . $detail['cart_id'] . "' AND id='" . $get_item_d['id'] . "'", 0);
							}
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
			$reply = array("ack" => 0, "developer_msg" => "Order Data Not Found!!", "ack_msg" => "Order Data Not Found!!");
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
			}
			/*else
			{
				$detail['sales_executive_id']="";
				$where=" AND sales_id=0";
			}*/
			$check_cart_exist = $this->db->rp_getTotalRecord("orders", "customer_id='" . $detail['cid'] . "' AND isDelete=0 AND id='" . $detail['order_id'] . "' " . $where, 0);
			if ($check_cart_exist != 0) {


				$order_items_r = $this->db->rp_getData("order_product_item", "*", "order_id='" . $detail['order_id'] . "' AND isDelete=0 ");
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
								//$GST=$product_detail['igst'];
								$unitprice = $this->db->rp_num($order_items_d['unitprice']);
								$totalprice = $order_items_d['pro_qty'] * $unitprice;
								$totalprice = $this->db->rp_num($totalprice);
								$original_price = $unitprice;
								$final_price = $totalprice;

								$total_qty += $order_items_d['pro_qty'];
								//	$sub_total+=$final_price;

								$taxable_amount = $order_items_d['totalprice'] - $order_items_d['cash_discount_amount'] - $order_items_d['additional_discount_amount'] + $order_items_d['other_charge'] + $order_items_d['fright_charge'];
								$total_taxable_amount += $taxable_amount;
								$gst_amount = ($taxable_amount * $order_items_d['igst_tax']) / 100;
								$total_gst_amount += $gst_amount;
								//echo $order_items_d['igst_tax'];exit;
								$final_total = $order_items_d['totalprice'] + $gst_amount;
								$main_total += $final_total;
								//$item_id = $this->db->rp_insert("order_product_item",$values,$rows,0);
							}
						}
					}
					$additional_discounted_amount = $sub_total;
					$cash_discounted_amount = $sub_total;
					$additional_discount_amount = 0;
					$cash_discount_amount = 0;

					$additional_discount_amount = $this->db->rp_getValue("orders", "additional_discount_amount", "id='" . $detail['order_id'] . "' ", 0);


					//$additional_discount_amount = $detail['additional_discount_amount'];
					$additional_discounted_amount = $sub_total - $additional_discount_amount;
					$cash_discounted_amount = $additional_discounted_amount;

					$cash_discount_amount = $this->db->rp_getValue("orders", "cash_discount_amount", "id='" . $detail['order_id'] . "' ", 0);
					//$cash_discount_amount=$detail['cash_discount_amount'];
					//$cash_discount_amount = ($additional_discounted_amount*$cash_discount)/100;


					$cash_discounted_amount = $additional_discounted_amount - $cash_discount_amount;
					$cash_discounted_amount = $this->db->rp_num((float)$cash_discounted_amount, 2, '.', '');
					$additional_discounted_amount = $this->db->rp_num((float)$additional_discounted_amount, 2, '.', '');

					$cash_discount_amount = $this->db->rp_num((float)$cash_discount_amount, 2, '.', '');
					$additional_discount_amount = $this->db->rp_num((float)$additional_discount_amount, 2, '.', '');

					$packing_charge = $this->db->rp_getValue("orders", "packing_charge", "id='" . $detail['order_id'] . "' ", 0);
					$transport_charge = $this->db->rp_getValue("orders", "transport_charge", "id='" . $detail['order_id'] . "' ", 0);
					$sub_total = $this->db->rp_num((float)$sub_total, 2, '.', '');
					$final_total = $total_taxable_amount; //$cash_discounted_amount+$packing_charge+$transport_charge;

					$gst_amount = $this->db->rp_getValue("orders", "igst_amount", "id='" . $detail['order_id'] . "' ", 0);

					$tcs_amount = $this->db->rp_getValue("orders", "tcs_amount", "id='" . $detail['order_id'] . "' ", 0);

					if ($gst_amount == 0) {
						$total_gst_amount = 0;
					}
					$grandtotal = $this->db->rp_num($final_total + $total_gst_amount + $tcs_amount);

					$before_roundoff = $this->db->rp_num($grandtotal, 2);
					$whole = floor($before_roundoff);
					$fraction = $before_roundoff - $whole;
					$f1 =  $this->db->rp_num((float)$fraction, 2, '.', '');
					$roundoff = $this->db->rp_num($f1, 2);
					//$grand_total=strval($this->db->rp_num(($before_roundoff-$roundoff),2));
					$grand_total = $this->db->rp_num(round($grandtotal), 2);

					//	echo $grand_total;exit();

					$dt = date("Y-m-d");
					//echo $total_taxable_amount;exit;

					$isUpdated = $this->db->rp_update("orders", array("total_qty" => $total_qty, "subtotal" => $total_taxable_amount, "grand_total" => $grand_total,/*"cash_discount_amount"=>$cash_discount_amount,"cash_discount"=>$cash_discount,*/ "igst_amount" => $total_gst_amount, "status" => 0,/*"order_date"=>$dt,*/ "shipping_address" => $this->db->clean($detail['shipping_address']), "billing_address" => $this->db->clean($detail['billing_address']), "chalan_no" => $detail['chalan_no'], "po_no" => $detail['po_no'], "po_date" => date('Y-m-d', strtotime($detail['po_date'])), "vendor_code" => $detail['vendor_code'], "tendor_code" => $detail['tendor_code'], "transport_through" => $detail['transport_through'], "transport_name" => $detail['transport_name'], "packing_charge" => $packing_charge, "transport_charge" => $transport_charge, "terms_comdition" => $detail['terms_comdition'], "faithfully" => $detail['faithfully'], "remarks" => $detail['remark'], "gst" => $detail['gst'], "update_entry_flag" => $detail['update_entry_flag'], "booking_place" => $detail['booking_place'], "booking_pincode" => $detail['booking_pincode'], "terms_condition_id" => $detail['terms_condition_id'], "max_dispatch_date" => date('Y-m-d', strtotime($detail['max_dispatch_date']))), "id='" . $detail['order_id'] . "'", 0);

					$is_gst = $this->db->rp_update("executive", array("gst" => $detail['gst']), "isDelete=0 AND id='" . $detail['cid'] . "'");

					$total_items = $this->db->rp_getTotalRecord("order_product_item", "order_id='" . $detail['order_id'] . "' AND isDelete=0");
					if ($total_items != 0) {
						$reply = array("ack" => 1, "developer_msg" => "Order Edit Successfully", "ack_msg" => "Order Edit Successfully", "order_id" => $detail['order_id']);
						return $reply;
					} else {
						$reply = array("ack" => 0, "developer_msg" => "Order Item Not inserted", "ack_msg" => "Order Item Not inserted");
						return $reply;
					}
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

	public function PlaceQuotationApi($detail)
	{
		$customer_d = $this->db->rp_getData("executive", "*", "id='" . $detail['cid'] . "' AND isDelete=0");
		$customer_r = mysqli_fetch_assoc($customer_d);

		$where = "";
		if ($detail['sales_executive_id'] != "") {
			$where = " AND sales_id='" . $detail['sales_executive_id'] . "' ";
		} else {
			$where = " AND sales_id=0";
		}
		if ($detail['cart_type'] == "2") {
			$quotation_id = $detail['quotation_id'];
		} else {
			$quotation_id = $_REQUEST['inserted_id']; //$this->db->rp_getValue("quotation_detail","id","inquiry_id='".$detail['inquiry_id']."' AND isDelete=0 AND status=-1".$where,0); 
		}

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

							$GST = $product_detail['igst'];
							$unitprice = $this->db->rp_num($order_items_d['unitprice']);
							$totalprice = $order_items_d['pro_qty'] * $unitprice;
							$totalprice = $this->db->rp_num($totalprice);
							$original_price = $unitprice;
							$final_price = $totalprice;

							$total_qty += $order_items_d['pro_qty'];
							$sub_total += $final_price;
							$item_gat_total += $order_items_d['item_gst_amount'];
						}
					}
				}
				$additional_discounted_amount = $sub_total;
				$cash_discounted_amount = $sub_total;
				$additional_discount_amount = 0;
				$cash_discount_amount = 0;

				$additional_discount_amount = $this->db->rp_getValue("quotation_detail", "additional_discount_amount", "id='" . $quotation_id . "' ", 0);

				//$additional_discount_amount = $detail['additional_discount_amount'];
				$additional_discounted_amount = $sub_total - $additional_discount_amount;
				//$cash_discounted_amount = $additional_discounted_amount;

				$cash_discount_amount = $this->db->rp_getValue("quotation_detail", "cash_discount_amount", "id='" . $quotation_id . "' ", 0);
				//$cash_discount_amount=$detail['cash_discount_amount'];
				//$cash_discount_amount = ($additional_discounted_amount*$cash_discount)/100;

				$cash_discounted_amount = $additional_discounted_amount - $cash_discount_amount;
				$cash_discounted_amount = $this->db->rp_num((float)$cash_discounted_amount, 2, '.', '');
				$additional_discounted_amount = $this->db->rp_num((float)$additional_discounted_amount, 2, '.', '');

				$cash_discount_amount = $this->db->rp_num((float)$cash_discount_amount, 2, '.', '');
				$additional_discount_amount = $this->db->rp_num((float)$additional_discount_amount, 2, '.', '');

				$packing_charge = $this->db->rp_getValue("quotation_detail", "packing_charge", "id='" . $quotation_id . "' ", 0);
				$transport_charge = $this->db->rp_getValue("quotation_detail", "transport_charge", "id='" . $quotation_id . "' ", 0);
				$sub_total = $this->db->rp_num((float)$sub_total, 2, '.', '');
				$final_total = $cash_discounted_amount + $packing_charge + $transport_charge;

				$gst_amount = $this->db->rp_getValue("quotation_detail", "igst_amount", "id='" . $quotation_id . "' ", 0);

				//$gst_amount=$this->db->rp_num(($final_total*$GST)/100);
				//$gst_amount=$this->db->rp_getValue("quotation_detail","igst_amount","id='".$quotation_id."' ",0);
				$tcs_amount = $this->db->rp_getValue("quotation_detail", "tcs_amount", "id='" . $quotation_id . "' ", 0);
				$grandtotal = $this->db->rp_num($final_total + $gst_amount + $tcs_amount);

				$before_roundoff = $this->db->rp_num($grandtotal, 2);
				$whole = floor($before_roundoff);
				$fraction = $before_roundoff - $whole;
				$f1 =  $this->db->rp_num((float)$fraction, 2, '.', '');
				$roundoff = $this->db->rp_num($f1, 2);
				//$grand_total=strval($this->db->rp_num(($before_roundoff-$roundoff),2));

				$grand_total = $this->db->rp_num(round($grandtotal), 2);

				$dt = date("Y-m-d");

				$isUpdated = $this->db->rp_update("quotation_detail", array("total_qty" => $total_qty,/*,"subtotal"=>$sub_total,"grand_total"=>$before_roundoff,"grand_total_rounded"=>$grand_total,"roundoff"=>$roundoff,*/ "quotation_date" => $detail['quotation_date'],/*"cash_discount_amount"=>$cash_discount_amount,*//*"cash_discount"=>$cash_discount,*//*"igst_amount"=>$gst_amount,*/ "status" => 1, "class_id" => $detail['class_id'], "area_id" => $detail['area_id'], "dealer_id" => $detail['dealer_id'], "entry_flag" => $detail['entry_flag'], "terms_comdition" => $detail['terms_comdition'], "faithfully" => $detail['faithfully'], "transport_name" => $detail['transport_name'], "transport_through" => $detail['transport_through'],/*"transport_charge"=>$detail['transport_charge'],*//*"packing_charge"=>$detail['packing_charge'],*/ "shipping_address" => $this->db->clean($detail['shipping_address']), "billing_address" => $this->db->clean($detail['billing_address']), "vendor_code" => $detail['vendor_code'], "tendor_no" => $detail['tendor_no'], "tendor_code" => $detail['tendor_code'], "remarks" => $detail['remark'], "gst" => $detail['gst'], "currency_code" => $detail['currency_code'], "entry_flag" => 5, "type_of_company" => $detail['type_of_company'], "terms_condition_id" => $detail['terms_condition_id']), "id='" . $quotation_id . "'", 0);

				if ($isUpdated) {
					$this->db->rp_delete("cart_detail", "id=" . $detail['cart_id'], 0);
					$this->db->rp_delete("cart_item", "order_id=" . $detail['cart_id'], 0);

					// send sales executive notification added by shivani     
					$sales_id = $this->db->rp_getValue("quotation_detail", "sales_id", "id='" . $quotation_id . "'");
					$customer_id = $this->db->rp_getValue("quotation_detail", "customer_id", "id='" . $quotation_id . "'", 0);

					$company_name = $this->db->rp_getValue("executive", "company_name", "id='" . $customer_id . "'", 0);
					$customer_name = $this->db->rp_getValue("executive", "cname", "id='" . $customer_id . "'", 0);
					$quotation_no  = $this->db->rp_getValue("quotation_detail", "quotation_no", "id='" . $quotation_id . "'");

					$quo_add_name = " by " . $this->db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'");
					$forquo =  " for " . $company_name . "-" . $customer_name;

					$notification_description = "Added Quotation With Quotation No " . $quotation_no . $quo_add_name . $forquo;

					$this->objPushNotification->commonNotification($sales_id, $quotation_id, "quotation_detail", "Add Quotation", $notification_description, "sales_executive", "quotation");
					// send sales executive notification added by shivani 

					// send customer notification added by shivani
					$customer_id = $this->db->rp_getValue("quotation_detail", "customer_id", "id='" . $quotation_id . "'");
					$this->objPushNotification->commonNotification($customer_id, $quotation_id, "quotation_detail", "Add Quotation", $notification_description, "customer", "quotation");
					// send customer notification added by shivani 

					// send customer upper chanel notification added by shivani 
					$customer_type  = $this->db->rp_getValue("quotation_detail", "customer_type", "id='" . $quotation_id . "'");
					if ($customer_type == 2)  //distributor
					{
						$upper_chanel_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $customer_id . "'");
					} else if ($customer_type == 3) //retailer 
					{
						$upper_chanel_id = $this->db->rp_getValue("executive", "dealer_distributor_id", "id='" . $customer_id . "'");
					}
					$this->objPushNotification->commonNotification($upper_chanel_id, $quotation_id, "quotation_detail", "Add Quotation", $notification_description, "customer", "quotation");
					// send customer upper chanel notification added by shivani

					$reply = array("ack" => 1, "developer_msg" => "Your Quotation Placed Successfully", "ack_msg" => "Your Quotation Placed Successfully");
					return $reply;
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Your Quotation is Not Placed", "ack_msg" => "Your Quotation is Not Placed");
					return $reply;
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Something went wrong!! Product Not in cart please check3", "ack_msg" => "Something went wrong!! Product Not in cart please check");
				return $reply;
			}
		} else {
			$reply = array("ack" => 0, "developer_msg" => "No Quotation Found", "ack_msg" => "No Quotation Found");
			return $reply;
		}
	}

	function Checkdup($products)
	{
		$isValid = true;
		$error = array();
		$new_array = array();
		foreach ($products as $p) {
			$new_array['pid'] = $p['pid'];
			$new_array['weight_id'] = $p['weight_id'];
			$new_array['pro_description'] = $p['pro_description'];
			$new_array['pro_name'] = $p['pro_name'];
		}

		$target = array();
		foreach ($products as $p1) {
			$target['pid'] = $p1['pid'];
			$target['weight_id'] = $p1['weight_id'];
			$target['pro_description'] = $p1['pro_description'];
			$target['pro_name'] = $p1['pro_name'];
		}
		print_r(array_intersect($new_array, $target));
		exit();
		if (count(array_intersect($new_array, $target)) == count($target)) {
			$isValid = 0;
			$error[] = "Product " . $target['pro_name'] . "";
		}
		return array("isValid" => $isValid, "error" => $error);
	}


	public function AddschemeItems($order_id, $order_item_id, $product_id, $weight_id, $pro_qty)
	{
		$customer_id = $this->db->rp_getValue("orders", "customer_id", "isDelete=0 AND id='" . $order_id . "'");

		$get_free_product = $this->db->rp_getData("scheme_master_item", "*", "isDelete=0 AND product_id='" . $product_id . "' AND weight_id='" . $weight_id . "' AND qty<='" . $pro_qty . "'", "", 0);
		if (mysqli_num_rows($get_free_product) > 0) {
			$get_order_date = $this->db->rp_getValue("orders", "order_date", "isDelete=0 AND id='" . $order_id . "'", 0);
			while ($get_free_product_d = mysqli_fetch_array($get_free_product)) {

				$scheme_qty = $get_free_product_d['qty'];
				$scheme_free_qty = $get_free_product_d['free_qty'];

				// 15   2

				// // 65   ?  
				$free_qty = ($pro_qty / $scheme_qty);

				// $free_qty=$free_qty/$scheme_free_qty;
				// $free_qty=($free_qty*$scheme_free_qty);

				$free_qty = floor($free_qty) * ($scheme_free_qty);
				// $free_qty=floor($free_qty);


				// $free_qty=($pro_qty * $get_free_product_d['qty'] / ($get_free_product_d['qty'] + $get_free_product_d['free_qty']));


				$get_dates = $this->db->rp_getData("scheme_master", "	start_date,end_date", "isDelete=0 AND id='" . $get_free_product_d['scheme_id'] . "'", "", 0);
				$date_array = mysqli_fetch_array($get_dates);


				if (strtotime($date_array['start_date']) <= strtotime($get_order_date) && strtotime($date_array['end_date']) >= strtotime($get_order_date)) {
					$rows_item = array("order_id", "order_item_id", "scheme_id", "pro_id", "weight_id", "pro_qty", "customer_id");
					$values_item = array($order_id, $order_item_id, $get_free_product_d['scheme_id'], $get_free_product_d['product_id_2'], $get_free_product_d['weight_id_2'], $free_qty, $customer_id);
					$scheme_item_insert = $this->db->rp_insert("order_scheme_items", $values_item, $rows_item, 0);
				}
			}
		}
	}
}
