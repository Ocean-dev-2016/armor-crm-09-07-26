<?php
// Connect to Database
include('connect.php');
require_once('../include/notification.class.php');
require_once('../include/product.class.php');
include('../include/class.executive.php');
include('../include/class.sales_executive.php');
include('../include/employee.class.php');
include('../include/orders.class.php');
include('../include/dispatch.class.php');
include('../include/class.invoice.php');
include('../include/quotation.class.php');
include('../include/class.deep_freezer_scheme.php');



// You have DB object now use it as $db->
//First Check for API key if API key is valid then proceed other stop excute script
// Which service requested is given in params named "service"
// Response Structure given below.
/* $ack=array(
			"ack"=>1/0\2,(1= success,0=failure,2=get otp)
			"ack_msg"=>"Message will printed on View",("This message will be shown to user so make it user readable not for developers")
			"developer_msg"=>"Message for debugging",("This message will be shown to developer on debug mode")
			"extra"=>array("requested_params"=>$_REQUEST,"other"=>array()),"Extra field contains requested params array which returns all the requested params and other array will contains extra params which you want to show on debug mode"
		)
	echo json_encode($ack);
*/
if ($is_valid_api_key) {
	if ($is_valid_service) {
		$objSalesExecutive = new SalesExecutive();
		$system = new System();
		$objProduct = new Product();
		$objEmp = new Employee();
		$objOrder = new Order();
		$objDispatch = new Dispatch();
		$objInvoice = new Invoice();
		$objQuotation = new Quotation();
		$objExecutive = new Executive();
		$objFreezer = new FreezerScheme();


		//#login For Sales Officer---------------------------------// 
		if ($service == 'get_country' || $service == 27) {
			$detail = array();
			$ack = $system->getCountry();
			$db->printJSON($ack);
		} else if ($service == 'get_top_country' || $service == 57) {
			//echo "sadadsad";  exit;
			$detail = array();
			$ack = $system->getTopCategory(array("id", "name", "image_path", "isDelete", "isActive"));
			$db->printJSON($ack);
		} else if ($service == 'get_category' || $service == 58) {
			//echo "sadadsad";  exit;
			$detail = array();
			$ack = $system->getCategory(array("id", "tcid", "name", "image_path", "isDelete", "isActive"));
			$db->printJSON($ack);
		} else if ($service == 'get_state' || $service == 28) {
			$detail = array();
			$ack1 = $system->getAllStateDetail(array("id", "name", "country_id", "isDelete")); //id
			// print_r($ack1);exit;
			$db->printJSON($ack1);
		} else if ($service == 'get_city' || $service == 38) {
			/* State-wise cities from `city` table (same as web ajax_get_main_city.php).
			 * Pass state_id = class.id from get_state (#28), OR state name.
			 * Backward compat: if only city_id is sent (no state_id), return areas for that city
			 * (old wrong wiring). Prefer state_id for city list. */
			$hasState = (isset($_REQUEST['state_id']) && trim($_REQUEST['state_id']) !== '')
				|| (isset($_REQUEST['state']) && trim($_REQUEST['state']) !== '')
				|| (isset($_REQUEST['state_name']) && trim($_REQUEST['state_name']) !== '')
				|| (isset($_REQUEST['class_id']) && trim($_REQUEST['class_id']) !== '')
				|| (isset($_REQUEST['sid']) && trim($_REQUEST['sid']) !== '');
			$hasCityOnly = !$hasState && isset($_REQUEST['city_id']) && trim($_REQUEST['city_id']) !== '';
			if ($hasCityOnly) {
				$ack = $system->getAllRoutDetail(array("id", "name", "class_id", "city_id"));
			} else {
				$ack = $system->getAllCityDetail(array("id", "name", "state_id"));
			}
			$db->printJSON($ack);
		} else if ($service == 'get_class' || $service == 35) {
			$type	= isset($_REQUEST['type']) ? $db->clean($_REQUEST['type']) : "";
			if ($type == "customer") {

				$detail = array();
				$ack = $system->getAllClassDetailCustomer(array("id", "name", "slug", "isDelete", "isActive")); //id
				$db->printJSON($ack);
			}
			$detail = array();
			$ack = $system->getAllClassDetail(array("id", "name", "slug", "isDelete", "isActive")); //id
			$db->printJSON($ack);
		} else if ($service == "get_banner" || $service == 60) {
			$sales_id	= isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
			$ctable 	= "promotion";
			$ctable1 	= "Banner";
			$current_date = date('Y-m-d');
			$isActive = $db->rp_getValue("sales_executive", "isActive", "id='" . $sales_id . "'", 0);
			$isDelete = $db->rp_getValue("sales_executive", "isDelete", "id='" . $sales_id . "'", 0);

			//aek master user hide kre che jenu id -1 che

			if ($isDelete == 0 /*|| $sales_id== "-1"*/) {
				if ($isActive == 1) {
					/*get rights*/
					$rights = array();
					$sales_rights = $db->rp_getData("sales_executive", "executive_in_min,executive_in_max,executive_out,super_stokist_order_view_flag,super_stokist_order_insert_flag,super_stokist_order_update_flag,super_stokist_order_delete_flag,outlets_order_view_flag,outlets_order_insert_flag,outlets_order_update_flag,outlets_order_delete_flag,dealer_order_view_flag,dealer_order_insert_flag,dealer_order_update_flag,dealer_order_delete_flag,project_order_view_flag,project_order_insert_flag,project_order_update_flag,project_order_delete_flag,oem_order_view_flag,oem_order_insert_flag,oem_order_update_flag,oem_order_delete_flag,survey_customer_view_flag,survey_customer_insert_flag,survey_customer_update_flag,survey_customer_delete_flag,customer_view_flag,customer_insert_flag,customer_update_flag,customer_delete_flag,followup_view_flag,followup_insert_flag,followup_update_flag,followup_delete_flag,create_order_view_flag,create_order_insert_flag,create_order_update_flag,create_order_delete_flag,order_history_view_flag,order_history_insert_flag,order_history_update_flag,order_history_delete_flag,complain_view_flag,complain_insert_flag,complain_update_flag,complain_delete_flag,customer_meeting_view_flag,customer_meeting_insert_flag,customer_meeting_update_flag,customer_meeting_delete_flag,near_by_me_view_flag,change_root_view_flag,change_root_insert_flag,change_root_update_flag,change_root_delete_flag,expense_view_flag,expense_insert_flag,expense_update_flag,expense_delete_flag,leave_view_flag,leave_insert_flag,leave_update_flag,leave_delete_flag,area_view_flag,area_insert_flag,area_update_flag,area_delete_flag,visit_view_flag,visit_insert_flag,visit_update_flag,visit_delete_flag,price_list_view_flag,bank_detail_view_flag,scheme_view_flag,discount_dealer_view_flag,discount_distributor_view_flag,gst_view_flag,visit_card_view_flag,traveling_view_flag,attendance_insert_flag,request_view_flag,request_insert_flag,request_update_flag,request_delete_flag,customer_leads_view_flag,customer_leads_insert_flag,customer_leads_update_flag,customer_leads_delete_flag,quotation_view_flag,quotation_insert_flag,quotation_update_flag,quotation_delete_flag,tracking_flag,prospact_insert_flag,prospact_update_flag,prospact_delete_flag,prospact_view_flag,marchent_customer_view_flag,marchent_customer_insert_flag,marchent_customer_update_flag,marchent_customer_delete_flag,my_route_view_flag,my_route_insert_flag,promotional_customer_view_flag,promotional_customer_insert_flag,promotional_customer_update_flag,promotional_customer_delete_flag,corporate_customer_view_flag,corporate_customer_insert_flag,corporate_customer_update_flag,corporate_customer_delete_flag,customer_stock_add_flag,deepfreezscheme_flag,travel_by_bike_flag,travel_by_bus_flag,travel_by_car_flag,tradercontractor_view_flag,tradercontractor_insert_flag,tradercontractor_update_flag,tradercontractor_delete_flag,mep_consultant_view_flag,mep_consultant_insert_flag,mep_consultant_update_flag,mep_consultant_delete_flag,builder_view_flag,builder_insert_flag,builder_update_flag,builder_delete_flag,brand_approval_visit_view_flag,brand_approval_visit_insert_flag,brand_approval_visit_update_flag,brand_approval_visit_delete_flag,weekday,create_order_approve_flag,quotation_approve_flag,chain_wise_view_order_history_flag,monthlyorder_planner_view,monthlyorder_planner_add,monthlyorder_planner_edit,monthlyorder_planner_delete,consultant_process_view,consultant_process_add,consultant_process_delete,consultant_process_edit", "id='" . $sales_id . "' AND isDelete=0 ", "", 0);
					$weekdays = array('01' => 'Sunday', '02' => 'Monday', '03' => 'Tuesday', '04' => 'Wednesday', '05' => 'Thursday', '06' => 'Friday', '07' => 'Saturday');
					$dayName = date("l", strtotime(date("Y-m-d")));
					while ($sales_rights_d = mysqli_fetch_assoc($sales_rights)) {
						$sales_rights_d['class_id'] = $db->rp_getValue("promotion", "class_id", "isDelete=0");
						$sales_rights_d['area_id'] = $db->rp_getValue("promotion", "area_id", "isDelete=0");
						$sales_rights_d['class_name'] = $db->rp_getValue("class", "name", "id='" . $sales_rights_d['class_id'] . "' AND isDelete=0");
						$area_id  = explode(",", $sales_rights_d['area_id']);
						$newArray = array();
						foreach ($area_id as $area) {
							$newArray[] = $db->rp_getValue("area", "name", "id='" . $area . "'", 0);
						}
						$sales_rights_d['area_name'] = implode(",", $newArray);
						$sales_rights_d['tracking_local_time'] = TRACKING_TIME_LOCAL_API;
						$sales_rights_d['tracking_live_time'] = TRACKING_TIME_LIVE_API;
						$sales_rights_d['distance'] = DISTANCE_API;
						$sales_rights_d['tracking_live_url'] = TRACKING_LIVE_URL;
						$sales_rights_d['is_visit_image_flag'] = IS_IMAGE_COMPULSORY;
						$sales_rights_d['visit_start_image_flag'] = VISIT_START_IMAGE_FLAG;
						$sales_rights_d['visit_stop_image_flag'] =  VISIT_STOP_IMAGE_FLAG;
						$expense_vehicle = $db->rp_getValue("expense", "expense_date", "expense_date='" . $current_date . "' AND sales_executive_id='" . $sales_id . "' AND category_id=2 AND isDelete=0", 0);
						if ($expense_vehicle) {
							$sales_rights_d['is_current_vehicle_expense'] = "1";
						} else {
							$sales_rights_d['is_current_vehicle_expense'] = "0";
						}
						if ($weekdays[$sales_rights_d['weekday']] == $dayName) {
							$sales_rights_d['is_today_weekof'] = '1';
						} else {
							$sales_rights_d['is_today_weekof'] = '0';
						}
						$rights[] = $sales_rights_d;
					}
					// print_r($rights);die;
					/*get rights*/


					$application_info_r  = $db->rp_getData("application_info", "version_name", "isActive=1  AND isDelete=0");
					$result = array();
					if ($application_info_r > 0) {
						$result = mysqli_fetch_assoc($application_info_r);
					}
					$result['Version message'] = "New Update <br/>Available <br/><b>" . $result['version_name'] . " vs</b><br/>for better experience <br/>with new function <br/><br/>Please<br/> Update the App";
					if ($result['version_name'] == null) {
						$result['version_name'] = "";
					}

					$banners = array();

					$banner_area_r =	$db->rp_getData($ctable, "*", "promo_type=1 AND isDelete=0", "display_order", 0);
					if (mysqli_num_rows($banner_area_r) > 0) {
						while ($banner_area_d = mysqli_fetch_array($banner_area_r)) {
							$banners[] = SITEURL . BANNER . $banner_area_d['image_path'];
						}

						$count = $db->rp_getTotalRecord("visit", "user_id='" . $sales_id . "' AND DATE(stop_date_time)=0000-00-00 AND DATE(start_date_time)!=0000-00-00 AND isDelete=0", 0);

						if ($count > 0) {
							$is_visit_start = "1";
						} else {
							$is_visit_start = "0";
						}

						$ack = array("ack" => 1, "result" => $banners, "rights" => $rights, "offline_visit_limit" => OFFLINE_VISIT_LIMIT, "is_visit_start" => $is_visit_start, "version_name" => $result['version_name'], "Version message" => $result['Version message']);
						echo json_encode($ack);
					} else {
						$ack = array("ack" => 0, "ack_msg" => "No banner found!!", "offline_visit_limit" => OFFLINE_VISIT_LIMIT);
						echo json_encode($ack);
					}
				} else {
					$ack = array("ack" => 2, "ack_msg" => "User Is Deactive.Please Check!!", "developer_msg" => "User Is Deactive.Please Check");
					echo json_encode($ack);
				}
			} else {
				$ack = array("ack" => 2, "ack_msg" => "User Is Delete.Please Check!!", "developer_msg" => "User Is Delete.Please Check");
				echo json_encode($ack);
			}
		} else if ($service == 'get_area' || $service == 36) {
			$detail = array();
			$ack = $system->getAllAreaDetail(array("id", "name", "class_id", "isDelete", "isActive")); //id
			$db->printJSON($ack);
		} else if ($service == 'get_update_info' || $service == 30) {

			$last_modify_date = $db->getRequestedParam("last_modify_date"); //country_id
			$ack = $system->getUpdateInfo($last_modify_date);
			$db->printJSON($ack);
		} else  if ($service == 'get_updates' || $service == 31) {

			/*$table_code=$db->getRequestedParam("table_code"); //table_code
             $last_sync_date=$db->getRequestedParam("last_sync_date"); //country_id
             $user_id=$db->getRequestedParam("user_id"); //$user_id

            $ack=$system->getUpdates($table_code,$user_id,$last_sync_date);
            $db->printJSON($ack);*/

			$detail['uid']	= isset($_REQUEST['uid']) ? $db->clean($_REQUEST['uid']) : "";
			$detail['cid']	= isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : "";
			$detail['tcid']	= isset($_REQUEST['tcid']) ? $db->clean($_REQUEST['tcid']) : "";
			$limit['ul']	= isset($_REQUEST['ul']) ? $db->clean($_REQUEST['ul']) : "";
			$limit['ll']	= isset($_REQUEST['ll']) ? $db->clean($_REQUEST['ll']) : "";
			$limit = $db->getLimit($limit);

			$ack = $objProduct->getProductPriceList($detail, $limit); //id
			$db->printJSON($ack);
		} else if ($service == 'get_action' || $service == 44) {
			$detail = array();
			$ack = $system->getNoOrderAction(array("id", "name", "isDelete", "isActive")); //id
			$db->printJSON($ack);
		} else if ($service == 'get_product' || $service == 51) {
			$detail['sales_id']	= isset($_REQUEST['tcid']) ? $db->clean($_REQUEST['sales_id']) : "";
			$detail['tcid']	= isset($_REQUEST['tcid']) ? $db->clean($_REQUEST['tcid']) : "";
			$detail['uid']	= isset($_REQUEST['uid']) ? $db->clean($_REQUEST['uid']) : "";
			$detail['cid']	= isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : "";
			$limit['ul']	= isset($_REQUEST['ul']) ? $db->clean($_REQUEST['ul']) : "";
			$limit['ll']	= isset($_REQUEST['ll']) ? $db->clean($_REQUEST['ll']) : "";
			$detail['search_name'] = isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : "";

			$system = new System();
			$limit = $system->getLimit();

			$ack = $objProduct->getProduct($detail, $limit); //id
			// print_r($ack);exit;
			$db->printJSON($ack);
		} else  if ($service == 'update_orders' || $service == 29) {

			$body = file_get_contents('php://input');
			$error_internal = array();
			$error = array();
			$str = isset($_REQUEST['result']) ? $_REQUEST['result'] : ""; //str_replace('\\',"",$_REQUEST['result']);
			$result = ($body != "") ? (array)json_decode($body, true) : array();
			$result_back = array();
			for ($i = 0; $i < sizeof($result['values']); $i++) {

				$detail = $result['values'][$i]['nameValuePairs'];
				$server_id = $detail['server_id'];
				if ($server_id != 0) {
					//echo "hi23";exit;
					// Update Order
					if (isset($detail['id']) && isset($detail['server_id']) && isset($detail['total_qty']) && isset($detail['total_amount']) && isset($detail['discount']) && isset($detail['discount_type']) && isset($detail['grand_total']) && isset($detail['customer_id']) && isset($detail['product'])) {
						$final_total = "";
						$dealer_id					= isset($detail['dealer_id']) ? $db->clean($detail['dealer_id']) : "";
						$class_id					= isset($detail['class_id']) ? $db->clean($detail['class_id']) : "";
						$area_id					= isset($detail['area_id']) ? $db->clean($detail['area_id']) : "";
						$id					= isset($detail['server_id']) ? $db->clean($detail['server_id']) : "";


						$total_qty			= isset($detail['total_qty']) ? $db->clean($detail['total_qty']) : "";
						$total_amount		= isset($detail['total_amount']) ? $db->clean($detail['total_amount']) : "";
						$discount			= isset($detail['discount']) ? $db->clean($detail['discount_amount']) : "";
						$discount_amount			= isset($detail['discount_amount']) ? $db->clean($detail['discount']) : "";
						$discount_type			= isset($detail['discount_type']) ? $db->clean($detail['discount_type']) : "";
						$taxable_amount		= isset($detail['taxable_amount']) ? $db->clean($detail['taxable_amount']) : "";
						$cash_discount		= isset($detail['cash_discount']) ? $db->clean($detail['cash_discount']) : "";
						$cash_discount_amount		= isset($detail['cash_discount_amount']) ? $db->clean($detail['cash_discount_amount']) : "";
						$subtotal		= isset($detail['sub_total']) ? $db->clean($detail['sub_total']) : "";
						$cgst_amount = isset($detail['cgst_amount']) ? $db->clean($detail['cgst_amount']) : "";
						$sgst_amount = isset($detail['sgst_amount']) ? $db->clean($detail['sgst_amount']) : "";
						$igst_amount = isset($detail['igst_amount']) ? $db->clean($detail['igst_amount']) : "";
						$roundoff = isset($detail['round_off']) ? $db->clean($detail['round_off']) : "";
						$grand_total_rounded		= isset($detail['grand_total']) ? $db->clean($detail['grand_total']) : "";
						$grand_total		= isset($detail['grand_total']) ? $db->clean($detail['grand_total']) : "";

						$customer_id		= isset($detail['customer_id']) ? $db->clean($detail['customer_id']) : "";
						$product 	= (isset($detail['product']['values']) && $detail['product']['values'] != "") ? ($detail['product']['values']) : array();

						$detail_ext = $db->rp_getData("executive", "*", "id=" . $customer_id . "", "", 0);
						$data = mysqli_fetch_assoc($detail_ext);
						$customer_name = $data['cname'];
						$customer_type = $data['type_of_executive'];
						$contact_number = $data['phone'];
						$address = $data['address'];
						$city = $data['city'];
						$state = $data['state'];
						$country = $data['country'];
						$email = $data['email'];
						$order_date	= date("Y-m-d");

						$cdrow 	= array(
							"total_qty" => $total_qty,
							"total_amount" =>  $total_amount,
							"discount" => $discount,
							"discount_amount" => $discount_amount,
							"discount_type" => $discount_type,
							"taxable_amount" => $taxable_amount,
							"cash_discount" => $cash_discount,
							"cash_discount_amount" => $cash_discount_amount,
							"subtotal" => $subtotal,
							"cgst_amount" => $cgst_amount,
							"sgst_amount" => $sgst_amount,
							"igst_amount" => $igst_amount,
							"grand_total" => $grand_total,
							"roundoff" => $roundoff,
							"grand_total_rounded" => $grand_total_rounded,
							"customer_id" => $customer_id,
							"customer_name"  => $customer_name,
							"customer_type"  => $customer_type,
							"contact_number"  => $contact_number,
							"address" => $address,
							"city" => $city,
							"email" => $email,
							"state" => $state,
							"country" =>  $country,
							"order_date" =>  $order_date,
							"local_id" => $detail['id'],
							"sales_id" => $detail['sales_id'],
							"order_date" => $detail['order_date'],
							"adate" => $detail['adate'],
							"modify_date" => date("Y-m-d H:i:s"),
							"dealer_id" => $dealer_id,
							"class_id" => $class_id,
							"area_id" => $area_id,
						);

						$cart_id = $db->rp_update("orders", $cdrow, "id='" . $id . "'");
						$adate	= date("Y-m-d H:i:s");
						//checking for updating qty is not greter than dispatched qty//
						$order_id = $id;
						$isError = false;
						foreach ($product as  $p) {
							$p = $p['nameValuePairs'];
							$pro_id     = $p['pro_id'];
							$new_order_qty 		=  $p['pro_qty'];

							//CHECK ORDER UPDATE VALID OR NOT

							$ordered_item_info = $db->rp_getData("order_product_item", "*", "pro_id='" . $pro_id . "' AND order_id='" . $order_id . "'", "", 0);
							if ($ordered_item_info) {
								$ordered_item_info = mysqli_fetch_assoc($ordered_item_info);
								$product_name = $ordered_item_info['pro_name'];
								$dispatched_qty = $ordered_item_info['dispatched_qty'];
								$remaining_qty = $ordered_item_info['remaining_qty'];
								$ordered_qty = $ordered_item_info['pro_qty'];
								//check new order qty > old order qty
								if ($new_order_qty < $ordered_qty) {
									//check new order qty < dispatched qty
									if ($new_order_qty < $dispatched_qty) {
										$isError = true;
										// ERROR YOU CAN NOT ENTER NEW ORDER QTY MORE THEN IT DISPATCHED
										$error[] = array("order_id" => $order_id, "error_target_id" => $pro_id, "error" => $product_name . " has dispatched qty more than your edited qty");
									}
								}
							}
						}
						if (!$isError) {
							$adate	= date("Y-m-d H:i:s");
							$db->rp_delete("order_product_item", "order_id='" . $id . "'", 0);
							foreach ($product as  $p) {
								$p = $p['nameValuePairs'];
								$pro_name = addslashes($p['pro_name']);
								$pro_id = $p['pro_id'];
								$weight_id = $p['weight_id'];
								$unitprice = $p['unitprice'];
								$qty = $p['pro_qty'];
								$totalprice = $p['totalprice'];
								$discount = $p['discount'];
								$discount_amount = $p['discount_amount'];
								$taxable = $p['taxable'];
								$cgst_tax = $p['cgst_tax'];
								$cgst_amount = $p['cgst_amount'];
								$sgst_tax = $p['sgst_tax'];
								$sgst_amount = $p['sgst_amount'];
								$igst_tax = $p['igst_tax'];
								$igst_amount = $p['igst_tax'];
								$subtotal = $p['sub_total'];
								$grandtotal = $p['totalprice'];
								$final_total += $totalprice;
								$where = "pid='" . $pro_id . "' AND order_id='" . $id . "' AND isDelete=0 GROUP BY pid";
								$dispatch_r = $db->rp_getData("dispatch_map_order", "SUM(qty) as dispatched_qty,pid", $where, "pid ASC ", 0);
								if ($dispatch_r) {
									$dispatch_d = mysqli_fetch_assoc($dispatch_r);
								} else {
									$dispatch_d['dispatched_qty'] = 0;
								}
								$remaining_qty = $qty - $dispatch_d['dispatched_qty'];
								$inner_size = $db->rp_getValue("product_weight_price", "inner_size", "product_id='" . $pro_id . "' AND weight_id='" . $weight_id . "'", 0);
								$row = array(
									"order_id",
									"pro_id",
									"weight_id",
									"pro_name",
									"unitprice",
									"adate",
									"inner_size",
									"modify_date",
									"pro_qty",
									"remaining_qty",
									"dispatched_qty",
									"totalprice",
									"discount",
									"discount_amount",
									"taxable",
									"cgst_tax",
									"cgst_amount",
									"sgst_tax",
									"sgst_amount",
									"igst_tax",
									"igst_amount",
									"grandtotal",
								);
								$value = array(
									$id,
									$pro_id,
									$weight_id,
									$pro_name,
									$adate,
									$inner_size,
									date("Y-m-d H:i:s"),
									$unitprice,
									$qty,
									$remaining_qty,
									$dispatch_d['dispatched_qty'],
									$totalprice,
									$discount,
									$discount_amount,
									$taxable,
									$subtotal,
									$cgst_tax,
									$cgst_amount,
									$sgst_tax,
									$sgst_amount,
									$igst_tax,
									$igst_amount,
									$grandtotal,
								);

								$ins = $db->rp_insert("order_product_item", $value, $row, 0);
							}

							$order_pro_detail = mysqli_fetch_assoc($db->rp_getData("orders", "*", "id='" . $id . "' AND isDelete=0"));
							$result_back[] = array("local_id" => $detail['id'], "server_id" => $order_pro_detail['id']);

							$order_pro_detail['product'] = array();
							$where = "order_id='" . $order_pro_detail['id'] . "'";
							$dt = $db->rp_getData("order_product_item", "*", $where);
							$r = array();
							if ($dt) {
								while ($row = mysqli_fetch_assoc($dt)) {
									$r[] = $row;
								}
							}
						}
					} else {
						$error_internal[] = "Row " . $i . " Not Submitted due to some paramter missing";
					}
				} else {
					//
					// Insert Order 
					if (isset($detail['total_qty']) && isset($detail['total_amount']) && isset($detail['discount']) && isset($detail['discount_type']) && isset($detail['grand_total']) && isset($detail['customer_id']) && isset($detail['sales_id']) && isset($detail['product'])) {


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

						);



						$final_total = "";

						//change by hardip

						$total_qty			= isset($detail['total_qty']) ? $db->clean($detail['total_qty']) : "";
						$total_amount			= isset($detail['subtotal']) ? $db->clean($detail['subtotal']) : "";
						$discount			= isset($detail['discount']) ? $db->clean($detail['discount']) : "";
						$discount_amount = isset($detail['discount_amount']) ? $db->clean($detail['discount_amount']) : "";
						$discount_type = isset($detail['discount_type']) ? $db->clean($detail['discount_type']) : "";
						$taxable_amount = isset($detail['taxable_amount']) ? $db->clean($detail['taxable_amount']) : "";
						$igst_amount = isset($detail['igst_amount']) ? $db->clean($detail['igst_amount']) : "";
						$sgst_amount = isset($detail['sgst_amount']) ? $db->clean($detail['sgst_amount']) : "";
						$cgst_amount = isset($detail['cgst_amount']) ? $db->clean($detail['cgst_amount']) : "";
						$cash_discount = isset($detail['cash_discount']) ? $db->clean($detail['cash_discount']) : "";
						$cash_discount_amount = isset($detail['cash_discount_amount']) ? $db->clean($detail['cash_discount_amount']) : "";
						$subtotal = isset($detail['sub_total']) ? $db->clean($detail['sub_total']) : "";
						$roundoff = isset($detail['round_off']) ? $db->clean($detail['round_off']) : "";
						$grand_total_rounded = isset($detail['grand_total']) ? $db->clean($detail['grand_total']) : "";
						$grand_total = $grand_total_rounded + $roundoff;
						// end


						$dealer_id					= isset($detail['dealer_id']) ? $db->clean($detail['dealer_id']) : "";
						$class_id					= isset($detail['class_id']) ? $db->clean($detail['class_id']) : "";
						$area_id					= isset($detail['area_id']) ? $db->clean($detail['area_id']) : "";
						$customer_id		= isset($detail['customer_id']) ? $db->clean($detail['customer_id']) : "";
						$sales_id		= isset($detail['sales_id']) ? $db->clean($detail['sales_id']) : "";

						$product 	= (isset($detail['product']['values']) && $detail['product']['values'] != "") ? ($detail['product']['values']) : array();
						$sales_type = $db->rp_getValue("sales_executive", "type", "id=" . $sales_id . "");
						$detail_sales = $db->rp_getData("executive", "*", "id=" . $customer_id . "", "", 0);
						if ($detail_sales) {
							$data = mysqli_fetch_assoc($detail_sales);
							$customer_name = $data['cname'];
							$company_name = $data['company_name'];
							$customer_type = $data['type_of_executive'];
							$contact_number = $data['phone'];
							$address = $data['address'];
							$city = $data['city'];
							$state = $data['state'];
							$country = $data['country'];
							$email = $data['email'];
						} else {
							$customer_name = $data['cname'];
							$customer_type = "";
							$contact_number = "";
							$company_name = "";
							$address = "";
							$city = "";
							$state = "";
							$country = "";
							$email = "";
						}

						$order_date	= $detail['order_date'];
						$adate = date('Y-m-d H:i:s');
						$cdrow 	= array(
							"subtotal",
							"grand_total",
							"grand_total_rounded",
							"roundoff",
							"igst_amount",
							"sgst_amount",
							"cgst_amount",
							"taxable",
							"discount",
							"discount_amount",
							"discount_type",
							"total_qty",
							"total_amount",
							"cash_discount",
							"cash_discount_amount",
							"customer_id",
							"sales_id",
							"sales_type",
							"customer_name",
							"company_name",
							"customer_type",
							"contact_number",
							"address",
							"city",
							"email",
							"state",
							"country",
							"order_date",
							"adate",
							"local_id",
							"modify_date",
							"dealer_id",
							"class_id",
							"area_id",
						);
						$cdvalue = array(
							$subtotal,
							$grand_total,
							$grand_total_rounded,
							$roundoff,
							$igst_amount,
							$sgst_amount,
							$cgst_amount,
							$taxable_amount,
							$discount,
							$discount_amount,
							$discount_type,
							$total_qty,
							$total_amount,
							$cash_discount,
							$cash_discount_amount,
							$customer_id,
							$sales_id,
							$sales_type,
							$customer_name,
							$company_name,
							$customer_type,
							$contact_number,
							$db->clean($address),
							$city,
							$email,
							$state,
							$country,
							$order_date,
							$adate,
							$detail['id'],
							date("Y-m-d H:i:s"),
							$dealer_id,
							$class_id,
							$area_id,
						);
						$cart_id = $db->rp_insert("orders", $cdvalue, $cdrow, 0);
						$row = array("order_no" => OUTLETS_ORDER_NO . str_pad($cart_id, 3, '0', STR_PAD_LEFT));
						$update_order_no = $db->rp_update("orders", $row, "id='" . $cart_id . "'");


						foreach ($product as  $p) {
							$p = $p['nameValuePairs'];
							//product=[{"name":"product1","id":"33","price":"1325","qty":"50"}]
							$totalprice = "";
							$pro_name = html_entity_decode(addslashes($p['pro_name']));
							$pro_id = $p['pro_id'];
							$weight_id = $p['weight_id'];
							$unitprice = $p['unitprice'];
							$qty = $p['pro_qty'];
							$inner_size = $p['inner_size'];
							$box_qty = $qty / $inner_size;
							$totalprice = $p['totalprice'];
							$discount = $p['discount'];
							$discount_amount = $p['discount_amount'];
							$taxable = $p['taxable'];
							$cgst_tax = $p['cgst_tax'];
							$cgst_amount = $p['cgst_amount'];
							$sgst_tax = $p['sgst_tax'];
							$sgst_amount = $p['sgst_amount'];
							$igst_tax = $p['igst_tax'];
							$igst_amount = $p['igst_tax'];
							$subtotal = $p['sub_total'];
							$grandtotal = $p['totalprice'];
							$cash_discount = $p['cash_discount'];
							$cash_discount_amount = $p['cash_discount_amount'];

							$row = array(
								"order_id",
								"pro_id",
								"weight_id",
								"pro_name",
								"unitprice",
								"pro_qty",
								"box_qty",
								"remaining_qty",
								"totalprice",
								"adate",
								"modify_date",
								"discount",
								"discount_amount",
								"taxable",
								"cgst_tax",
								"cgst_amount",
								"sgst_tax",
								"sgst_amount",
								"igst_tax",
								"igst_amount",
								"inner_size",
								"subtotal",
								"grandtotal",
								"cash_discount",
								"cash_discount_amount"

							);
							$value = array(
								$cart_id,
								$pro_id,
								$weight_id,
								$pro_name,
								$unitprice,
								$qty,
								$box_qty,
								$qty,
								$totalprice,
								$adate,
								date("Y-m-d H:i:s"),
								$discount,
								$discount_amount,
								$taxable,
								$cgst_tax,
								$cgst_amount,
								$sgst_tax,
								$sgst_amount,
								$igst_tax,
								$igst_amount,
								$inner_size,
								$subtotal,
								$grandtotal,
								$cash_discount,
								$cash_discount_amount

							);

							$ins = $db->rp_insert("order_product_item", $value, $row, 0);
						}
						$order_pro_detail = mysqli_fetch_assoc($db->rp_getData("orders", "*", "id='" . $cart_id . "' AND isDelete=0", "", 0));
						$order_pro_detail['product'] = array();
						$where = "order_id='" . $order_pro_detail['id'] . "' AND isDelete=0";
						$dt = $db->rp_getData("order_product_item", "*", $where);
						$r = array();
						if ($dt) {
							while ($row = mysqli_fetch_assoc($dt)) {
								$r[] = $row;
							}

							$order_pro_detail['product'] = $r;
						}
						$result_back[] = array("local_id" => $detail['id'], "server_id" => $cart_id);

						///////////////////////// notification ////////////////////
						$title_description = "Order of <b>Rs." . $grand_total_rounded . "</b> for date <b>" . date('d-m-Y', strtotime($order_date)) . "</b> added by <b>" . $customer_name . "</b>";
						$notification = $system->setNotification(0, 1, "Order Notification.", 5, "Order Message", $title_description, "", "", $order_date, $cart_id, "orders", $customer_type);
					} else {
						echo "hi@###3121";
						exit;
						$error_internal[] = "Row " . $i . " Not Submitted due to some paramter missing";
					}
				}
			}

			$ack = array(
				"ack" => 1,
				"ack_msg" => "Sync complete",
				"error_log" => array("internal" => $error_internal, "other" => $error),
				"developer_msg" => "Awwww see log hardip bhai!!",
				"result" => $result_back
			);
			$db->printJSON($ack);
		} else  if ($service == 'create_update_customer' || $service == 37) {
			// Create Customer 
			$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "add";
			$area_id = array();
			if (isset($_REQUEST['cname']) && isset($_REQUEST['phone'])) {
				$end_user_type			    = isset($_REQUEST['end_user_type']) ? $db->clean($_REQUEST['end_user_type']) : "";
				$type_of_executive		    = isset($_REQUEST['type_of_inquiry']) ? $db->clean($_REQUEST['type_of_inquiry']) : "";
				$company_type			    = isset($_REQUEST['company_type']) ? $db->clean($_REQUEST['company_type']) : "";
				$company_name			    = isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : "";
				$address				    = isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "";
				$zip					    = isset($_REQUEST['zip']) ? $db->clean($_REQUEST['zip']) : "";
				$super_stockist_id		    = isset($_REQUEST['super_stockist_id']) ? $db->clean($_REQUEST['super_stockist_id']) : "";
				$city					    = isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
				$state					    = isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
				$country				    = isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
				$email					    = isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "";
				$dealer_distributor_id	    = isset($_REQUEST['dealer_distributor_id']) ? $db->clean($_REQUEST['dealer_distributor_id']) : "";
				$cname			            = isset($_REQUEST['cname']) ? $db->clean($_REQUEST['cname']) : "";
				$cst	                    = isset($_REQUEST['cst']) ? $db->clean($_REQUEST['cst']) : "";
				$pan				        = isset($_REQUEST['pan']) ? $db->clean($_REQUEST['pan']) : "";
				$phone				        = isset($_REQUEST['phone']) ? $db->clean($_REQUEST['phone']) : "";
				$gst		                = isset($_REQUEST['gst']) ? $db->clean($_REQUEST['gst']) : "";
				$vat		                = isset($_REQUEST['vat']) ? $db->clean($_REQUEST['vat']) : "";
				$excise			            = isset($_REQUEST['excise']) ? $db->clean($_REQUEST['excise']) : "";
				$class_id			        = isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
				$area_id		            = array($_REQUEST['area_id']);
				$discount		            = $_REQUEST['discount'];
				if (!empty($area_id)) {
					for ($i = 0; $i < sizeof($area_id); $i++) {
						$item[] = array("area_id" => $area_id[$i]);
					}
				}

				$inquiry_date			= date("Y-m-d");
				include('../include/class.executive.php');
				$inquiry = new Executive();
				if ($mode == "add") {

					$ack = $inquiry->InsertExecutive($end_user_type, $type_of_executive, $company_type, $company_name, $address, $super_stockist_id, $city, $state, $country, $email, $dealer_distributor_id, $cname, $cst, $pan, $phone, $gst, $vat, $inquiry_date, $zip, $excise, $class_id, $item, $discount);

					if ($ack['ack'] == 1) {
						$reply = array("ack" => 1, "ack_msg" => "Customer successfully saved !!");
					} else {
						$reply = $ack;
					}
				} else {
					if (isset($_REQUEST['cid'])) {
						$executive_id = $_REQUEST['cid'];
						//echo $executive_id;exit;
						$ack = $inquiry->UpdateExecutive($executive_id, $end_user_type, $type_of_executive, $company_type, $company_name, $address, $super_stockist_id, $city, $state, $country, $email, $dealer_distributor_id, $cname, $cst, $pan, $phone, $gst, $vat, $inquiry_date, $zip, $excise, $class_id, $item, $discount, $password);

						if ($ack['ack'] == 1) {
							$reply = array("ack" => 0, "ack_msg" => "Customer successfully updated!!");
						} else {
							$reply = $ack;
						}
					} else {
						$reply = array("ack" => 0, "ack_msg" => "No Customer found to update!!");
					}
				}
			} else {
				$reply = array("ack" => 0, "ack_msg" => "Person name, contact number and type of Customer are compalsary!!");
			}
			$db->printJSON($reply);
		}

		/*else if($service=="sync_offline_customers"|| $service==39)
		{
			$body= file_get_contents('php://input');
			$customers=($body!="")?(array)json_decode($body,true):array();
			$inquiry=new Executive();
			$updated=array();
			$error=array();
			$reply=array();
			for($j=0;$j<sizeof($customers['values']);$j++)
			{
				$customer=$customers['values'][$j]['nameValuePairs'];
					$area_id   =array();
					$server_id =array_key_exists('server_id',$customer)?$db->clean($customer['server_id']):0;
					$local_id  =array_key_exists('local_id',$customer)?$db->clean($customer['local_id']):0;
					$se_id     =array_key_exists('se_id',$customer)?$db->clean($customer['se_id']):0;
					if(array_key_exists('cname',$customer) && array_key_exists('phone',$customer))			
					{
						$end_user_type			    = array_key_exists('end_user_type',$customer)?$db->clean($customer['end_user_type']):"";
						$type_of_executive		    = array_key_exists('type_of_executive',$customer)?$db->clean($customer['type_of_executive']):"";
						$company_type			    = array_key_exists('company_type',$customer)?$db->clean($customer['company_type']):"";
						$company_name			    = array_key_exists('company_name',$customer)?$db->clean($customer['company_name']):"";
						$address				    = array_key_exists('address',$customer)?$db->clean($customer['address']):"";
						$zip					    = array_key_exists('zip',$customer)?$db->clean($customer['zip']):"";
						$super_stockist_id		    = array_key_exists('super_stockist_id',$customer)?$db->clean($customer['super_stockist_id']):"";
						$city					    = array_key_exists('city',$customer)?$db->clean($customer['city']):"";
						$state					    = array_key_exists('state',$customer)?$db->clean($customer['state']):"";
						$country				    = array_key_exists('country',$customer)?$db->clean($customer['country']):"";
						$email					    = array_key_exists('email',$customer)?$db->clean($customer['email']):"";
						$dealer_distributor_id	    = array_key_exists('dealer_distributor_id',$customer)?$db->clean($customer['dealer_distributor_id']):"";
						$cname			            = array_key_exists('cname',$customer)?$db->clean($customer['cname']):"";
						$cst	                    = array_key_exists('cst',$customer)?$db->clean($customer['cst']):"";	
						$pan				        = array_key_exists('pan',$customer)?$db->clean($customer['pan']):"";
						$phone				        = array_key_exists('phone',$customer)?$db->clean($customer['phone']):"";
						$gst		                = array_key_exists('gst',$customer)?$db->clean($customer['gst']):"";
						$mobile_no1		                = array_key_exists('other_contact',$customer)?$db->clean($customer['other_contact']):"";
						$vat		                = array_key_exists('vat',$customer)?$db->clean($customer['vat']):"";
						$excise			            = array_key_exists('excise',$customer)?$db->clean($customer['excise']):"";$discount			        = array_key_exists('discount',$customer)?$db->clean($customer['discount']):"";$class_id			        = array_key_exists('class_id',$customer)?$db->clean($customer['class_id']):"";$area_id		            = array(array_key_exists('area_id',$customer)?$customer['area_id']:0);if(!empty($area_id))
						{
							for($i=0;$i<sizeof($area_id);$i++)
							{
								$item[]=array("area_id"=>$area_id[$i]);

							}
						}

						$inquiry_date			= date("Y-m-d");
						
						
						if($server_id==0)
						{
						
							$ack=$inquiry->InsertExecutive($end_user_type,$type_of_executive,$company_type,$company_name,$address,$super_stockist_id,$city,$state,$country,$email,$dealer_distributor_id,$cname,$cst,$pan,$phone,$gst,$vat,$inquiry_date,$zip,$excise,$class_id,$mobile_no1,$item,$discount,$se_id,$local_id,"android","");
							
							if($ack['ack']==1)
							{
								$reply[]=array("ack"=>1,"ack_msg"=>"Customer successfully saved !!");		
								$updated[]=array("server_id"=>$ack['inserted_id'],"local_id"=>$local_id,"area"=>$ack['areas']);	
							}
							else
							{
								$error[]=$ack;							
							}						
						}
						else
						{
							
							$executive_id=$server_id;
							//echo $executive_id;exit;
							$ack=$inquiry->UpdateExecutive($executive_id,$end_user_type,$type_of_executive,$company_type,$company_name,$address,$super_stockist_id,$city,$state,$country,$email,$dealer_distributor_id,$cname,$cst,$pan,$phone,$gst,$vat,$inquiry_date,$zip,$excise,$class_id,$mobile_no1,$item,$discount,$password,$se_id,$local_id,"");
							if($ack['ack']==1)
							{
								$reply[]=array("ack"=>1,"ack_msg"=>"Customer successfully updated!!");	
								$updated[]=array("server_id"=>$server_id,"local_id"=>$local_id,"area"=>$ack['areas']);									
							}
							else
							{
								$error[]=$ack;			
							}
							
						}											
					}
					else
					{
						$reply[]=array("ack"=>0,"ack_msg"=>"Person name, contact number and type of Customer are compalsary!!");
					}
			}
			
			$ack=array( "ack"=>1,
                "ack_msg"=>"Customer Save Successfully",
                "developer_msg"=>"Customer Save Successfully",
				"result"=>$updated,
				"reply"=>$reply,
				"error"=>$error
            );
			$db->printJSON($ack);
		
		}*/ else if ($service == "sync_offline_customers" || $service == 39) {
			// $detail=array();
			$detail['id']	 =  isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
			$detail['type_of_executive']	 = isset($_REQUEST['type_of_executive']) ? $db->clean($_REQUEST['type_of_executive']) : "";
			$detail['customer_flag']	 = isset($_REQUEST['customer_flag']) ? $db->clean($_REQUEST['customer_flag']) : "0";
			$detail['channel_partner_flag'] = isset($_REQUEST['channel_partner_flag']) ? $db->clean($_REQUEST['channel_partner_flag']) : "0";
			$detail['sales_id']			 = isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
			// $detail['sales_id'];exit();
			$detail['company_name']		 = isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : "";
			$detail['person_name']		 = isset($_REQUEST['person_name']) ? $db->clean($_REQUEST['person_name']) : "";
			$detail['mobile_number']	 = isset($_REQUEST['mobile_number']) ? $db->clean($_REQUEST['mobile_number']) : "";
			$detail['phone'] = isset($_REQUEST['phone']) ? $db->clean($_REQUEST['phone']) : "";
			// echo $detail['phone'];exit();
			$detail['email']	         = isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "";
			$detail['address']	         = isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "";
			$detail['address1']	         = isset($_REQUEST['address1']) ? $db->clean($_REQUEST['address1']) : "";
			$detail['gst_no']	         = isset($_REQUEST['gst_no']) ? $db->clean($_REQUEST['gst_no']) : "";
			$detail['super_stockist_id']	     = isset($_REQUEST['super_stockist_id']) ? $db->clean($_REQUEST['super_stockist_id']) : "";
			$detail['dealer_id']	     = isset($_REQUEST['dealer_id']) ? $db->clean($_REQUEST['dealer_id']) : "";
			$detail['country']	    	 = isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
			$detail['state']	     	 = isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
			$detail['city']	     		 = isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
			$detail['main_city']	     		 = isset($_REQUEST['main_city']) ? $db->clean($_REQUEST['main_city']) : "";
			$detail['class_id']	     	 = isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
			if ($detail['class_id'] == "" && $detail['state'] != "") {
				$detail['class_id'] = $db->rp_getValue("class", "id", "name LIKE '%" . $db->clean($detail['state']) . "%' AND isDelete=0", 0);
				if ($detail['class_id'] === false) {
					$detail['class_id'] = "";
				}
			}

			$detail['area_id']	     	 = isset($_REQUEST['area_id']) ? $db->clean($_REQUEST['area_id']) : "";

			$detail['latitude']	     	 = isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";

			$detail['longitude']	     	 = isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
			$detail['whatsapp_no']	     	 = isset($_REQUEST['whatsapp_no']) ? $db->clean($_REQUEST['whatsapp_no']) : "";
			$detail['price_list_id']	     	 = isset($_REQUEST['price_list_id']) ? $db->clean($_REQUEST['price_list_id']) : "";
			$detail['company_type']	     	 = isset($_REQUEST['company_type']) ? $db->clean($_REQUEST['company_type']) : "";
			$detail['pincode']	     	 = isset($_REQUEST['pincode']) ? $db->clean($_REQUEST['pincode']) : "";
			$detail['email_cc']	     	 = isset($_REQUEST['email_cc']) ? $db->clean($_REQUEST['email_cc']) : "";
			$detail['client_code']	     	 = isset($_REQUEST['client_code']) ? $db->clean($_REQUEST['client_code']) : "";
			// echo $detail['client_code'];exit();
			$detail['pan']	     	 = isset($_REQUEST['pan_no']) ? $db->clean($_REQUEST['pan_no']) : "";
			$detail['zone']	     	 = isset($_REQUEST['zone']) ? $db->clean($_REQUEST['zone']) : "";
			$detail['industry_type_id']	     	 = isset($_REQUEST['industry_type_id']) ? $db->clean($_REQUEST['industry_type_id']) : "";
			$detail['top_category_id']	     	 = isset($_REQUEST['top_category_id']) ? $db->clean($_REQUEST['top_category_id']) : "";
			$detail['entry_flag']	     	 = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "5";
			$detail['shipping_address'] = isset($_REQUEST['shipping_address']) ? str_replace(array("\n", "\r"), ' ', $db->clean($_REQUEST['shipping_address'])) : "";
			$detail['billing_address'] = isset($_REQUEST['billing_address']) ? str_replace(array("\n", "\r"), ' ', $db->clean($_REQUEST['billing_address'])) : "";
			$detail['type_of_company']	     	 = isset($_REQUEST['company_type_id']) ? $db->clean($_REQUEST['company_type_id']) : "";
			$detail['remark']	     	 = isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
			$detail['booking_place']	     	 = isset($_REQUEST['booking_place']) ? $db->clean($_REQUEST['booking_place']) : "";
			$detail['transport_by_id']	     	 = isset($_REQUEST['transport_by_id']) ? $db->clean($_REQUEST['transport_by_id']) : "";
			$detail['transporter_id']	     	 = isset($_REQUEST['transporter_id']) ? $db->clean($_REQUEST['transporter_id']) : "";
			$detail['purchasing_from']	     	 = isset($_REQUEST['purchasing_from']) ? $db->clean($_REQUEST['purchasing_from']) : "";
			$detail['turnover']	     	 = isset($_REQUEST['turnover']) ? $db->clean($_REQUEST['turnover']) : "";
			$detail['turnover_year']	     	 = isset($_REQUEST['turnover_year']) ? $db->clean($_REQUEST['turnover_year']) : "";

			/* Added Code By DINESH */
			if ($detail['area_id'] == "" || empty($detail['area_id']) || $detail['area_id'] == null || $detail['area_id'] == NULL) {

				$city_id = $db->rp_getValue("city", "id", "state_id = '" . $detail['class_id'] . "' AND name LIKE '%" . strtolower(trim($detail['main_city'])) . "%'", 0);

				$detail['area_id'] = $db->rp_getValue("area", "id", " class_id='" . $detail['class_id'] . "' AND city_id='" . $city_id . "' AND name LIKE '%" . strtolower(trim($detail['main_city'])) . "%'", 0);
			}
			if ($detail['area_id'] === false || $detail['area_id'] === null) {
				$detail['area_id'] = "";
			}
			$detail['type'] = isset($_REQUEST['type']) ? $db->clean($_REQUEST['type']) : "";
			/* Added Code By DINESH */

			/* All top category assign in customer */
			// $all_top_category_r = $db->rp_getData("top_category_master","id","isDelete=0","",0);
			// if ($all_top_category_r) {
			// 	while($all_top_category_d = mysqli_fetch_array($all_top_category_r)){
			// 		$all_top_category_arr[] = $all_top_category_d['id'];
			// 	}
			// 	$all_top_category_str = implode(",",$all_top_category_arr);
			// 	$detail['top_category_id'] = $all_top_category_str;
			// } else {
			// 	$all_top_category_str = "";
			// 	$detail['top_category_id'] = $all_top_category_str;
			// }
			/* All top category assign in customer */

			require_once('../include/class.executive.php');
			$inquiry1 = new Executive();
			if ($detail['id'] != "") {

				$ack = $inquiry1->UpdateExecutiveAPI($detail, $_FILES);
			} else {

				$ack = $inquiry1->InsertExecutive($detail, $_FILES);
			}
			if ($ack['ack'] == 1) {
				$reply[] = array("ack" => 1, "ack_msg" => "Customer successfully saved !!");
				//$updated[]=array("server_id"=>$ack['inserted_id'],"local_id"=>$local_id,"area"=>$ack['areas']);	
				$updated[] = array("server_id" => $ack['inserted_id'], "local_id" => $local_id, "area" => $ack['areas']);
			} else {
				$error[] = $ack;
			}
			$db->printJSON($ack);
			/*}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Client Code is Required !!");
				$db->printJSON($ack);
			}
			*/
		}

		// inquiry sync
		else  if ($service == 'update_inquiry_sync' || $service == 45) {
			$id	                            = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
			$type                           = isset($_REQUEST['type']) ? $db->clean($_REQUEST['type']) : "";
			$detail1['executive_type']		= isset($_REQUEST['executive_type']) ? $db->clean($_REQUEST['executive_type']) : "";
			$detail1['sales_executive_id']	= isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
			$detail1['company_name']	    = isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : "";
			$detail1['mobile_number']	    = isset($_REQUEST['mobile_number']) ? $db->clean($_REQUEST['mobile_number']) : "";
			$detail1['person_name']	    	= isset($_REQUEST['person_name']) ? $db->clean($_REQUEST['person_name']) : "";
			$detail1['description']	        = isset($_REQUEST['description']) ? $db->clean($_REQUEST['description']) : "";
			$detail1['datetime']			= date("Y-m-d H:i:s");
			$detail1['inquiry_date'] 		= date("Y-m-d");
			$detail1['date_of_call'] 		= isset($_REQUEST['date_of_call']) ? date("Y-m-d", strtotime($_REQUEST['date_of_call'])) : "";
			$detail1['inquiry_assign_date'] = isset($_REQUEST['inquiry_assign_date']) ? date("Y-m-d", strtotime($_REQUEST['inquiry_assign_date'])) : "";

			$dealer_id = $db->rp_getValue("executive", "id", "mobile_no1='" . $detail1['mobile_number'] . "'");
			$detail1['dealer_id'] = (isset($dealer_id)) ? $db->clean($dealer_id) : "0";

			// $detail1['dealer_id']= isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
			$detail1['latitude']	        = isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";
			$detail1['longitude']	        = isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
			$detail1['address']	        	= isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "";
			$detail1['other_mobile_no']	    = isset($_REQUEST['other_mobile_no']) ? $db->clean($_REQUEST['other_mobile_no']) : "";
			$detail1['distributor_id']	    = isset($_REQUEST['distributor_id']) ? $db->clean($_REQUEST['distributor_id']) : "";
			$detail1['country']	            = isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
			$detail1['state']	            = isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
			$detail1['city']	            = isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
			$detail1['main_city'] = isset($_REQUEST['main_city']) ? $db->clean($_REQUEST['main_city']) : "";
			$detail1['source_of_inquiry'] 	= isset($_REQUEST['source_of_inquiry']) ? $db->clean($_REQUEST['source_of_inquiry']) : "";
			$detail1['designation']	   		= isset($_REQUEST['designation']) ? $db->clean($_REQUEST['designation']) : "";
			$detail1['zone']	   			= isset($_REQUEST['zone']) ? $db->clean($_REQUEST['zone']) : "";
			$detail1['email_address']	   	= isset($_REQUEST['email_address']) ? $db->clean($_REQUEST['email_address']) : "";
			$detail1['inquiry_assign_to']	= isset($_REQUEST['inquiry_assign_to']) ? $db->clean($_REQUEST['inquiry_assign_to']) : "";
			$detail1['inquiry_created_by']	= isset($_REQUEST['inquiry_created_by']) ? $db->clean($_REQUEST['inquiry_created_by']) : "";
			$detail1['inquiry_lead_flag']	= isset($_REQUEST['inquiry_lead_flag']) ? $db->clean($_REQUEST['inquiry_lead_flag']) : "";
			$detail1['inquiry_type']		= isset($_REQUEST['inquiry_lead_flag']) ? $db->clean($_REQUEST['inquiry_lead_flag']) : "";
			$detail1['inq_status']			= isset($_REQUEST['inquiry_lead_flag']) ? $db->clean($_REQUEST['inquiry_lead_flag']) : "";
			$detail1['birth_date']	   	    = isset($_REQUEST['birth_date']) ? date("Y-m-d", strtotime($_REQUEST['birth_date'])) : ""; /*$detail1['product_id']	        = isset($_REQUEST['product_id'])?$db->clean($_REQUEST['product_id']):"";
			$detail1['quantity']	        = isset($_REQUEST['quantity'])?$db->clean($_REQUEST['quantity']):"";
			$detail1['u_w_flag']	        = isset($_REQUEST['u_w_flag'])?$db->clean($_REQUEST['u_w_flag']):"";
			$detail1['u_w_remark']	        = isset($_REQUEST['u_w_remark'])?$db->clean($_REQUEST['u_w_remark']):"";
			$detail1['quotation_flag']	    = isset($_REQUEST['quotation_flag'])?$db->clean($_REQUEST['quotation_flag']):"";
			$detail1['quotation_remark']	= isset($_REQUEST['quotation_remark'])?$db->clean($_REQUEST['quotation_remark']):"";
			$detail1['customer_requirement'] = isset($_REQUEST['customer_requirement'])?$db->clean($_REQUEST['customer_requirement']):"";
			*/
			$detail1['class_id'] = isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
			$detail1['area_id']  = isset($_REQUEST['area_id']) ? $db->clean($_REQUEST['area_id']) : "";
			$detail1['gst_no']  = isset($_REQUEST['gst_no']) ? $db->clean($_REQUEST['gst_no']) : "";
			$detail1['shipping_address']  = isset($_REQUEST['shipping_address']) ? $db->clean($_REQUEST['shipping_address']) : "";
			$detail1['billing_address']  = isset($_REQUEST['billing_address']) ? $db->clean($_REQUEST['billing_address']) : "";
			$detail1['industry_type_id']  = isset($_REQUEST['industry_type_id']) ? $db->clean($_REQUEST['industry_type_id']) : "";
			$detail1['type_of_company']  = isset($_REQUEST['company_type_id']) ? $db->clean($_REQUEST['company_type_id']) : "";
			$detail1['top_category_id']  = isset($_REQUEST['top_category_id']) ? $db->clean($_REQUEST['top_category_id']) : "";
			$detail1['pincode']  = isset($_REQUEST['pincode']) ? $db->clean($_REQUEST['pincode']) : "";
			if (sizeof($_REQUEST['top_category_id']) != 0) {
				$detail['top_category_id'] = implode(',', $_REQUEST['top_category_id']);
			}
			$detail1['purchasing_from']  = isset($_REQUEST['purchasing_from']) ? $db->clean($_REQUEST['purchasing_from']) : "";

			/* Added Code By DINESH */
			if ($detail1['area_id'] == "" || empty($detail1['area_id']) || $detail1['area_id'] == null || $detail1['area_id'] == NULL) {

				$city_id = $db->rp_getValue("city", "id", "state_id = '" . $detail1['class_id'] . "' AND name LIKE '%" . strtolower(trim($detail1['main_city'])) . "%'", 0);

				$detail1['area_id'] = $db->rp_getValue("area", "id", " class_id='" . $detail1['class_id'] . "' AND city_id='" . $city_id . "' AND name LIKE '%" . strtolower(trim($detail1['main_city'])) . "%'", 0);
				$detail1['city'] = $db->rp_getValue("area", "name", " class_id='" . $detail1['class_id'] . "' AND city_id='" . $city_id . "' AND name LIKE '%" . strtolower(trim($detail1['main_city'])) . "%'", 0);
				$detail1['city_id'] = $city_id;
			}
			/* Added Code By DINESH */

			if ($detail1['country'] !=  "" && $detail1['state'] != "" && $detail1["main_city"] != "") {
				// print_r($detail1);exit;
				if ($_REQUEST['id'] == "") {
					$detail1['entry_flag'] = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "5";
					$reply = $objSalesExecutive->addNoOrderInquiry($detail1, $_FILES);
				} else {
					$detail1['update_entry_flag']  = isset($_REQUEST['update_entry_flag']) ? $db->clean($_REQUEST['update_entry_flag']) : "5";
					$reply = $objSalesExecutive->updateNoOrderInquiry($detail1, $id, $type);
				}

				$inquiry_insert_detail = mysqli_fetch_assoc($db->rp_getData("no_order_inquiry", "*", "id='" . $reply['inserted_id'] . "' AND isDelete=0", "", 0));

				$result_back[] = array("local_id" => $detail1['local_id'], "server_id" => $reply['inserted_id'], "reply" => $reply);
				$ack = array("ack" => $reply['ack'], "ack_msg" => $reply['ack_msg'], "developer_msg" => $reply['developer_msg'], "inquiry_id" => $reply['inserted_id']);
				$db->printJSON($ack);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "country state and city are Required !!");
				$db->printJSON($ack);
			}
		} else if ($service == 'add_area' || $service == 49) {
			//$areas=$db->getRequestedParam("areas"); //addArea
			$body = file_get_contents('php://input');
			$areas = ($body != "") ? (array)json_decode($body, true) : array();
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$ack = $system->addArea($areas['values'], $sales_id);
			$db->printJSON($ack);
		} else if ($service == 'add_followup_notification' || $service == 50) {

			$ack = $system->addFollowupNotification();
			$db->printJSON($ack);
		} else if ($service == "get_notification" || $service == 59) {
			if (isset($_REQUEST['user_id']) && isset($_REQUEST['user_type'])) {
				$user_id = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
				$user_type = isset($_REQUEST['user_type']) ? $db->clean($_REQUEST['user_type']) : "";

				if ($user_type == "customer") {
					$ack = $objEmp->getNotification($user_id, $user_type);
					$db->printJSON($ack);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Not Valid User!",
						"developer_msg" => "Not Valid User!",
					);
					$db->printJSON($ack);
				}
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "customer_order_request" || $service == 66) {
			if (isset($_REQUEST['cid']) && isset($_REQUEST['cid'])) {
				$detail['cid'] = isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : "";
				$products = (isset($_REQUEST['products']) && $_REQUEST['products'] != "") ? json_decode($_REQUEST['products'], true) : array();

				$ack = $objOrder->AddCustomerOrderRequest($detail, $products);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "accept_invoice_order" || $service == 67) {
			if (isset($_REQUEST['invoice_id']) && isset($_REQUEST['invoice_id'])) {
				$detail['invoice_id'] = isset($_REQUEST['invoice_id']) ? $db->clean($_REQUEST['invoice_id']) : "";

				$ack = $objOrder->AcceptinvoiceOrder($detail);
				$db->printJSON($ack);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "reject_invoice_order" || $service == 68) {
			if (isset($_REQUEST['invoice_id']) && isset($_REQUEST['invoice_id'])) {
				$detail['invoice_id'] = isset($_REQUEST['invoice_id']) ? $db->clean($_REQUEST['invoice_id']) : "";
				$detail['remarks'] = isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
				$ack = $objOrder->RejectinvoiceOrder($detail);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "order_request_history" || $service == 69) {
			if (isset($_REQUEST['cid']) && isset($_REQUEST['cid'])) {
				$detail['cid'] = isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : "";

				$ack = $objOrder->GetOrderRequestHistory($detail);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "order_request_history_detail" || $service == 70) {
			if (isset($_REQUEST['id']) && isset($_REQUEST['id'])) {
				$detail['id'] = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";

				$ack = $objOrder->GetOrderRequestDetail($detail);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "performa_history" || $service == 71) {
			if (isset($_REQUEST['cid']) && isset($_REQUEST['cid'])) {
				$detail['cid'] = isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : "";

				$ack = $objOrder->GetPerformaHistory($detail);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "performa_history_detail" || $service == 72) {
			if (isset($_REQUEST['id']) && isset($_REQUEST['id'])) {
				$detail['id'] = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";

				$ack = $objOrder->GetPerformaDetail($detail);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "get_dispatch_detail" || $service == 73) {
			if (isset($_REQUEST['dispatch_id']) && isset($_REQUEST['dispatch_id'])) {
				$detail['dispatch_id'] = isset($_REQUEST['dispatch_id']) ? $db->clean($_REQUEST['dispatch_id']) : "";

				$ack = $objDispatch->GetDispatchDetail($detail);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "get_dispatched_order" || $service == 74) {
			if (isset($_REQUEST['order_id']) && isset($_REQUEST['order_id'])) {
				$detail['order_id'] = isset($_REQUEST['order_id']) ? $db->clean($_REQUEST['order_id']) : "";

				$ack = $objDispatch->GetDispatchOrderDetail($detail);
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
		/*else if($service=="get_discount" || $service==81)
		{
			if(isset($_REQUEST['uid']) && $_REQUEST['uid']!="")
			{
				$tc_discount_r=$db->rp_getData("price_table","*","isDelete=0 AND uid='".$_REQUEST['uid']."'");
				
				if($tc_discount_r)
				{
					$Data=array();
					while($tc_discount_d=mysqli_fetch_assoc($tc_discount_r))
					{
						$tc_discount_d['top_category_name']=$db->rp_getValue("top_category_master","name","id='".$tc_discount_d['tcid']."' AND isDelete=0");
						$tc_discount_d['discount']=$db->rp_num($tc_discount_d['discount']);
						$tc_discount_d['basic']=$db->round($tc_discount_d['basic']);
						$tc_discount_d['trade']=$db->round($tc_discount_d['trade']);
						$Data[]=$tc_discount_d;
					}
				}
				else
				{
					$Data=array();
				}
				$cash_discount=$db->rp_getValue("customer","cash_discount","id='".$_REQUEST['uid']."' AND isDelete=0");
				$brand_id=$db->rp_getValue("customer","brand_id","id='".$_REQUEST['uid']."' AND isDelete=0");
				if($cash_discount=="")
				{
					$cash_discount=0;
				}
				$ack=array( "ack"=>1,
                "ack_msg"=>"Customer Cash Discount Found!!",
                "developer_msg"=>"Customer Cash Discount Found!!","result"=>$Data,"cash_discount"=>$cash_discount,"brand_id"=>$brand_id
	            );
				$db->printJSON($ack);
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}
		}*/ else if ($service == "get_news" || $service == 81) {

			$ctable 	= "news";
			$id = $_REQUEST['id'];
			if ($id != "") {
				$ctable_r = $db->rp_getData($ctable, "*", "id='" . $id . "' AND isDelete=0", "display_order", 0);
			} else {
				$ctable_r = $db->rp_getData($ctable, "*", "isDelete=0", "display_order", 0);
			}
			$news = array();
			if ($ctable_r) {
				while ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
					$ctable_d['created_date'] = date('d F Y H:i A', strtotime($ctable_d['created_date']));
					$ctable_d['image_path'] = SITEURL . NEWS . $ctable_d['image_path'];
					$news[] = $ctable_d;
				}
				$ack = array("ack" => 1, "ack_msg" => "News Found", "result" => $news);
				echo json_encode($ack);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No News found!!");
				echo json_encode($ack);
			}
		} else if ($service == "delete_inquiry" || $service == 82) {
			$flag = $_REQUEST['flag'];
			if (isset($_REQUEST['id']) && $_REQUEST['id'] != "") {
				if ($flag == "no_order_inquiry") {

					$delete_inquiry = $db->rp_update("no_order_inquiry", array("isDelete" => 1), "id='" . $_REQUEST['id'] . "'");
				} else if ($flag == "quotation_detail") {

					$delete_inquiry = $db->rp_update("quotation_detail", array("isDelete" => 1), "id='" . $_REQUEST['id'] . "'", 0);
				} else {
					$delete_inquiry = $db->rp_update("customer_inquiry", array("isDelete" => 1), "id='" . $_REQUEST['id'] . "'");
				}
				if ($delete_inquiry) {
					$ack = array("ack" => 1, "ack_msg" => " Delete Successfully");
					echo json_encode($ack);
				} else {
					$ack = array("ack" => 0, "ack_msg" => " Deete Failed");
					echo json_encode($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Inquiry ID required!!",
					"developer_msg" => "Inquiry ID required!!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "search_product" || $service == 83) {
			/*if($_REQUEST['searchName']!=""  && isset($_REQUEST['searchName']))
			{*/
			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";

			if ($uid != "") {
				$price_list_id = $db->rp_getValue("executive", "price_list_id", "id='" . $uid . "' AND isDelete=0");
			} else {
				$price_list_id = 0;
			}
			$limit = $objProduct->getLimit($limit);
			$result = array();
			if ($_REQUEST['searchName'] != "" && isset($_REQUEST['searchName'])) {
				$PROIDS = array();
				$where = "";
				/*$pro_r1=$db->rp_getData("product_weight_price","product_id","catno LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
					if($pro_r1)
					{
						while($pro_d1=mysqli_fetch_assoc($pro_r1))
						{
							$PROIDS[]=$pro_d1['product_id'];
						}
					}
					if(!empty($PROIDS))
					{
						$PROIDS=implode(",", $PROIDS);
						$where=" OR id IN (".$PROIDS."))";
					}*/

				$proids_array = $db->getCommaSepretedData("product_weight_price", "*", $_REQUEST['searchName'], "catno");

				if ($proids_array != "") {
					$where = "(name LIKE '%" . $_REQUEST['searchName'] . "%' OR id IN (" . $proids_array . ")) AND isDelete=0 AND isActive=1 AND isVisible=0";
				} else {
					$where = "name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0 AND isActive=1 AND isVisible=0";
				}
				//echo $a;exit();

				$pro_r = $db->rp_getData("product", "*", $where . " AND cid!=13", "", 0, $limit);
			} else {
				$pro_r = $db->rp_getData("product", "*", "isDelete=0 AND isActive=1 AND isVisible=0 ", "", 0, $limit);
			}
			if ($pro_r) {
				while ($pro_d = mysqli_fetch_assoc($pro_r)) {
					$pro_d['cat_name'] = $db->rp_getValue("category_master", "name", "id='" . $pro_d['cid'] . "' AND isDelete=0", 0);
					$pro_d['top_cat_name'] = $db->rp_getValue("top_category_master", "name", "id='" . $pro_d['tcid'] . "' AND isDelete=0", 0);
					$pro_d['product_code'] = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);

					$pro_d['unitname'] = $db->rp_getValue("unit", "name", "id='" . $pro_d['unit_id'] . "' AND isDelete=0", 0);
					//$pro_d['unit_id']="1";
					$pro_d['total_size'] = $db->rp_getTotalRecord("product_weight_price", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);
					$descr = html_entity_decode($pro_d['descr']);
					$descr = strip_tags($descr);
					// $descr=str_replace("\r\n","",$descr);
					// $descr=str_replace(",",",<br/>",$descr);
					$descr = nl2br($descr);
					$pro_d['descr'] = $descr;
					$pid = $pro_d['id'];
					if ($pro_d['image_path'] != "") {
						$pro_d['image_path'] = SITEURL . PRODUCT . $pro_d['image_path'];
					}

					$price_r =	$db->rp_getData("product_weight_price", "id,weight_id,price,product_id,inner_size,outer_size,stock_qty,catno", "isDelete=0 AND product_id=" . $pid, "", 0);

					if ($price_r) {
						$product_weight_price = array();
						while ($price_d = mysqli_fetch_assoc($price_r)) {
							$price_d['original_price'] = $db->rp_number_format($price_d['price'], 2);

							$price_d['price'] = $db->rp_number_format($price_d['price'], 2);
							$price_d['discount'] = 0;
							$price_d['discounted_amount'] = 0;
							if ($price_list_id != 0) {
								$check_product_in_list = $db->rp_getTotalRecord("product_price_list", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
								if ($check_product_in_list > 0) {
									$price_d['price'] = $db->rp_number_format($db->rp_getValue("product_price_list", "discounted_price", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);

									$price_d['discount'] = $db->rp_number_format($db->rp_getValue("product_price_list", "discount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);

									$price_d['discounted_amount'] = $db->rp_number_format($db->rp_getValue("product_price_list", "discounted_amount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "'"), 2);
								}
							}

							$price_d['name'] = $db->rp_getValue("weight", "name", "id='" . $price_d['weight_id'] . "'");
							$price_d['display_order'] = $db->rp_getValue("weight", "display_order", "id='" . $price_d['weight_id'] . "'");
							$product_weight_price[] = $price_d;
							$objProduct->sortBy('display_order', $product_weight_price, 'asc');
						}
						$pro_d['product_weight_price'] = $product_weight_price;
						$product[] = $pro_d;
					} else {
						$pro_d['product_weight_price'] = array();
						$product[] = $pro_d;
					}
					$result[] = $pro_d;
				}
			}
			if (!empty($result)) {
				$ack = array("ack" => 1, "result" => $result);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No data Avialable");
			}
			/*$ack=array("ack"=>1,"result"=>$result);*/
			echo json_encode($ack);
			/*}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}*/
		} else if ($service == "get_login_profile" || $service == 84) {
			$se_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			if ($se_id != "") {
				$login_r = $db->rp_getData("sales_executive", "*", "id='" . $se_id . "'");
				if ($login_d = mysqli_fetch_assoc($login_r)) {

					$imgpath = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $login_d['id'] . "' AND id='" . $login_d['image_path'] . "'");
					$login_d['image_path'] = ($login_d['image_path'] != "") ? $imgpath : "";
					if ($login_d['type'] == 'sales_manager') {
						$login_d['type'] = 'M. D.';
					} else if ($login_d['type'] == 'area_sales_manager') {
						$login_d['type'] = 'General Manager';
					} else if ($login_d['type'] == 'sales_officer') {
						$login_d['type'] = 'Regional Sales Manager';
					} else if ($login_d['type'] == 'sales_executive') {
						$login_d['type'] = 'Sales Officer';
					} else if ($login_d['type'] == 'area_manager') {
						$login_d['type'] = 'Area Sales Manager';
					} else if ($login_d['type'] == 'service_executive') {
						$login_d['type'] = 'Service Executive';
					}
					$class_r = $db->rp_getData("sales_executive_map_area", "DISTINCT(class_id)", "sales_executive_id='" . $login_d['id'] . "' AND isDelete=0");
					$class_name = array();
					if ($class_r) {
						while ($class_d = mysqli_fetch_assoc($class_r)) {
							$class_name[] = $db->rp_getValue("class", "name", "id='" . $class_d['class_id'] . "'");
						}
						$class_name = implode(",", $class_name);
					} else {
						$class_name = "";
					}
					$login_d['class_name'] = $class_name;
					$area_r = $db->rp_getData("sales_executive_map_area", "area_id", " sales_executive_id='" . $login_d['id'] . "' AND isDelete=0", "", 0);
					$area_name = array();
					if ($area_r) {
						while ($area_d = mysqli_fetch_assoc($area_r)) {
							$area_name[] = $db->rp_getValue("area", "name", "id='" . $area_d['area_id'] . "'");
						}
						$area_name = implode(",", $area_name);
					} else {
						$area_name = "";
					}
					$login_d['area_name'] = $area_name;
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Sales Officer Found!!",
						"developer_msg" => "Sales Officer Found!!",
						"result" => $login_d
					);
					$db->printJSON($ack);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "No such a Sales Officer Found!!",
						"developer_msg" => "No such a Sales Officer Found!!",
					);
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Sales Officer Required!!",
					"developer_msg" => "Sales Officer Required!!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "add_to_cart" || $service == 85) {
			if (isset($_REQUEST['cid']) && isset($_REQUEST['cid']) && isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['sales_executive_id'])) {
				$detail['cid'] = isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : ""; //customer id
				$detail['sales_executive_id'] = isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$detail['order_id'] = isset($_REQUEST['order_id']) ? $db->clean($_REQUEST['order_id']) : "";
				$detail['cart_type'] = isset($_REQUEST['cart_type']) ? $db->clean($_REQUEST['cart_type']) : "";
				$detail['quotation_id'] = isset($_REQUEST['quotation_id']) ? $db->clean($_REQUEST['quotation_id']) : "";
				$detail['entry_flag'] = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "5";

				//$detail['cash_discount_flag']=isset($_REQUEST['cash_discount_flag'])?$db->clean($_REQUEST['cash_discount_flag']):0;
				// print_r($_REQUEST['products']);exit;
				$products = (isset($_REQUEST['products']) && $_REQUEST['products'] != "") ? json_decode($_REQUEST['products'], true) : array();
				//print_r($products);exit;
				// pid,qty,weight_id->product array
				//$body= file_get_contents('php://input');
				$ack = $objOrder->AddToCartApi($detail, $products);
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "place_order" || $service == 86) {
			//echo str_replace(array("\n", "\r"), ' ', $_REQUEST['shipping_address']);
			if (isset($_REQUEST['cid']) && isset($_REQUEST['cid'])) {
				$detail['sales_executive_id'] = isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$detail['cid'] = isset($_REQUEST['cid']) ? $db->clean($_REQUEST['cid']) : ""; //customer id
				$detail['type_of_company'] = $db->rp_getValue("executive", "type_of_company", "isDelete=0 AND id='" . $detail['cid'] . "'"); //company name
				$detail['cart_id'] = isset($_REQUEST['cart_id']) ? $db->clean($_REQUEST['cart_id']) : ""; //customer id
				$detail['cart_type'] = isset($_REQUEST['cart_type']) ? $db->clean($_REQUEST['cart_type']) : "";
				$detail['quotation_id'] = isset($_REQUEST['quotation_id']) ? $db->clean($_REQUEST['quotation_id']) : "";
				$detail['inquiry_id'] = isset($_REQUEST['inquiry_id']) ? $db->clean($_REQUEST['inquiry_id']) : "";

				$detail['shipping_address'] = isset($_REQUEST['shipping_address']) ? str_replace(array("\n", "\r"), ' ', $_REQUEST['shipping_address']) : "";
				$detail['billing_address'] = isset($_REQUEST['billing_address']) ? str_replace(array("\n", "\r"), ' ', $_REQUEST['billing_address']) : "";
				$detail['chalan_no'] = isset($_REQUEST['chalan_no']) ? $db->clean($_REQUEST['chalan_no']) : "";
				$detail['po_no'] = isset($_REQUEST['po_no']) ? $db->clean($_REQUEST['po_no']) : "";
				$detail['po_date'] = isset($_REQUEST['po_date']) ? $db->clean(date('Y-m-d', strtotime($_REQUEST['po_date']))) : "";

				$detail['vendor_code'] = isset($_REQUEST['vendor_code']) ? $db->clean($_REQUEST['vendor_code']) : "";
				$detail['tendor_code'] = isset($_REQUEST['tendor_code']) ? $db->clean($_REQUEST['tendor_code']) : "";
				$detail['transport_through'] = isset($_REQUEST['transport_through']) ? $db->clean($_REQUEST['transport_through']) : "";
				$detail['transport_name'] = isset($_REQUEST['transport_name']) ? $db->clean($_REQUEST['transport_name']) : "";
				$detail['packing_charge'] = isset($_REQUEST['packing_charge']) ? $db->clean($_REQUEST['packing_charge']) : "";
				$detail['transport_charge'] = isset($_REQUEST['transport_charge']) ? $db->clean($_REQUEST['transport_charge']) : "";

				$detail['terms_comdition'] = isset($_REQUEST['terms_comdition']) ? $_REQUEST['terms_comdition'] : "";
				$detail['terms_condition_id'] = isset($_REQUEST['terms_condition_id']) ? $_REQUEST['terms_condition_id'] : "";
				$detail['faithfully'] = isset($_REQUEST['faithfully']) ? $_REQUEST['faithfully'] : "";
				$detail['remark'] = isset($_REQUEST['remark']) ? $_REQUEST['remark'] : "";

				$detail['cash_discount_flag'] = isset($_REQUEST['cash_discount_flag']) ? $db->clean($_REQUEST['cash_discount_flag']) : 0;
				$detail['gst'] = isset($_REQUEST['gst']) ? $db->clean($_REQUEST['gst']) : 0;

				$detail['class_id'] = isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
				$detail['area_id'] = isset($_REQUEST['area_id']) ? $db->clean($_REQUEST['area_id']) : "";
				$detail['dealer_id'] = isset($_REQUEST['dealer_id']) ? $db->clean($_REQUEST['dealer_id']) : "";
				$detail['entry_flag'] = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "";
				$detail['quotation_date'] = isset($_REQUEST['quotation_date']) ? $db->clean(date('Y-m-d', strtotime($_REQUEST['quotation_date']))) : "";
				$detail['order_date'] = isset($_REQUEST['quotation_date']) ? $db->clean(date('Y-m-d', strtotime($_REQUEST['quotation_date']))) : "";
				$detail['tendor_no'] = isset($_REQUEST['tendor_no']) ? $db->clean($_REQUEST['tendor_no']) : "";
				$detail['currency_code'] = isset($_REQUEST['currency_code']) ? $db->clean($_REQUEST['currency_code']) : "";
				$detail['booking_place'] = isset($_REQUEST['booking_place']) ? $db->clean($_REQUEST['booking_place']) : "";
				$detail['booking_pincode'] = isset($_REQUEST['booking_pincode']) ? $db->clean($_REQUEST['booking_pincode']) : "";

				if ($_REQUEST['cart_type'] == "1") {
					$ack = $objOrder->placeQuotationApi($detail);
				} else if ($_REQUEST['cart_type'] == "2") {
					$ack = $objOrder->placeQuotationApi($detail);
				} else {
					$ack = $objOrder->PlaceOrder($detail);
				}
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "delete_product" || $service == 87) {
			if (isset($_REQUEST['cart_item_id']) && $_REQUEST['cart_item_id'] != "") {
				$table = "";
				if ($_REQUEST['cart_type'] == "3") { //1=order 0=cart
					$table = "order_product_item";
				} else if ($_REQUEST['cart_type'] == "2") {
					$table = "quotation_product_item";
				} else {
					$table = "cart_item";
				}
				$delete_item = $db->rp_delete($table, "id='" . $_REQUEST['cart_item_id'] . "'");
				if ($delete_item) {
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Product Remove  successfully!!",
						"developer_msg" => "Product Remove From cart successfully!!",
					);
					$db->printJSON($ack);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Porduct already Not in Cart!!",
						"developer_msg" => "Porduct already Not in Cart!!",
					);
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "update_product" || $service == 88) {
			$table_id = isset($cart_id) ? $cart_id : "";
			$table = "";
			$table_main = "";
			if ($_REQUEST['cart_type'] == "3") {
				$table = "order_product_item";
				$table_main = "orders";
				$oid_column = "order_id";
			} else if ($_REQUEST['cart_type'] == "2") {
				$table = "quotation_product_item";
				$table_main = "quotation_detail";
				$oid_column = "quotation_id";
			} else {
				$table = "cart_item";
				$table_main = "cart_detail";
				$oid_column = "order_id";
			}
			if (isset($_REQUEST['cart_item_id']) && isset($_REQUEST['pid']) && isset($_REQUEST['qty']) && isset($_REQUEST['weight_id']) & $_REQUEST['cart_item_id'] != "" && $_REQUEST['pid'] != "" && $_REQUEST['qty'] != ""  &&  $_REQUEST['weight_id'] != "") {
				$pro_description = isset($_REQUEST['pro_description']) ? $db->clean($_REQUEST['pro_description']) : "";

				$inner_size = $db->rp_getValue("product_weight_price", "inner_size", "weight_id='" . $_REQUEST['weight_id'] . "' AND product_id='" . $_REQUEST['pid'] . "'", 0);

				$outer_size = $db->rp_getValue("product_weight_price", "outer_size", "weight_id='" . $_REQUEST['weight_id'] . "' AND product_id='" . $_REQUEST['pid'] . "'", 0);

				$customer_id = $db->rp_getValue($table_main, "customer_id", "id='" . $table_id . "' AND isDelete=0", 0);
				$type_of_executive = $db->rp_getValue("executive", "type_of_executive", "id='" . $customer_id . "' AND isDelete=0", 0);
				if ($type_of_executive == "8") {

					$GST = 0.1;
				} else {

					$GST = $db->rp_getValue("product", "igst", "id='" . $_REQUEST['pid'] . "'");
				}
				/*if($_REQUEST['cart_type']=="2")
				{
					$unitprice=$db->rp_getValue($table,"unitprice","weight_id='".$_REQUEST['weight_id']."' AND quotation_id='".$_REQUEST['cart_id']."' AND isDelete=0 AND pro_id='".$_REQUEST['pid']."'",0);

				}else{
					$unitprice=$db->rp_getValue($table,"unitprice","weight_id='".$_REQUEST['weight_id']."' AND order_id='".$_REQUEST['cart_id']."' AND isDelete=0 AND pro_id='".$_REQUEST['pid']."'",0);

				}*/

				// added by shivani for common key qty distribution (27-02-2023)
				$item_order_unit_qty = $_REQUEST['item_order_unit_qty'];

				$item_order_unit = $db->rp_getValue($table, "item_order_unit", "weight_id='" . $_REQUEST['weight_id'] . "' AND " . $oid_column . "='" . $_REQUEST['cart_id'] . "' AND isDelete=0 AND pro_id='" . $_REQUEST['pid'] . "'", 0);
				$inner_size = $db->rp_getValue($table, "inner_size", "weight_id='" . $_REQUEST['weight_id'] . "' AND " . $oid_column . "='" . $_REQUEST['cart_id'] . "' AND isDelete=0 AND pro_id='" . $_REQUEST['pid'] . "'", 0);
				$outer_size = $db->rp_getValue($table, "outer_size", "weight_id='" . $_REQUEST['weight_id'] . "' AND " . $oid_column . "='" . $_REQUEST['cart_id'] . "' AND isDelete=0 AND pro_id='" . $_REQUEST['pid'] . "'", 0);

				if ($item_order_unit < 0 && $item_order_unit != 100) {
					$update_bag_qty = $item_order_unit_qty;
					$update_qty = $item_order_unit_qty * $inner_size;
					$update_cartoon_qty = 0;
				} else if ($item_order_unit > 0 && $item_order_unit != 100) {
					$update_cartoon_qty = $item_order_unit_qty;
					$update_qty = $item_order_unit_qty * $outer_size;
					$update_bag_qty = 0;
				} else if ($item_order_unit == 100) {
					$update_qty = $item_order_unit_qty;
					$update_bag_qty = 0;
					$update_cartoon_qty = 0;
				} else {
					$update_qty = 0;
					$update_bag_qty = 0;
					$update_cartoon_qty = 0;
				}
				// added by shivani for common key qty distribution (27-02-2023)

				/*$update_qty=$_REQUEST['qty'];
				$update_bag_qty=$_REQUEST['box_qty'];
				$update_cartoon_qty=$_REQUEST['cartoon_qty'];*/

				$loose = 0;
				/*$bag_box_id = $db->rp_getValue("product","unit_id","id='".$_REQUEST['pid']."' AND isDelete=0");

					if($bag_box_id==2)
					{
						$bag = ($update_qty / $inner_size);
						$bag = floor($bag);
						$update_cartoon_qty = 0;
						
						
						$total_bag = $bag*$inner_size;
						$total_box = $update_cartoon_qty*$outer_size;
						$totalsum = $total_bag+$total_box;
						$loose =  round($update_qty-$totalsum);
						
					}
					else if($bag_box_id==3)
					{
						$update_cartoon_qty = floor($update_qty / $outer_size);
						$bag = 0;
						
						$total_bag = $bag*$inner_size;
						$total_box = $update_cartoon_qty*$outer_size;
						$totalsum = $total_bag+$total_box;
						$loose =  round($update_qty-$totalsum);
						
					}
					else
					{
						$update_cartoon_qty=floor($update_qty/$outer_size);//box
						if($update_cartoon_qty!=0)
						{
							$bagqty = $update_qty - ($outer_size * $update_cartoon_qty);
							if ($bagqty < 0) 
							{
								$bagqty = $bagqty * -1;
							}
							$bagqty = ($bagqty != "") ? floor($bagqty) : 0;
							$bag = ($bagqty / $inner_size);
							$bag = floor($bag);
						}
						else 
						{
							$bag = ($update_qty / $inner_size);
							$bag = floor($bag);
						}

						//loose qty calculation
						$total_bag = $bag*$inner_size;
						$total_box = $update_cartoon_qty*$outer_size;
						$totalsum = $total_bag+$total_box;
						$loose =  round($update_qty-$totalsum);
						//echo $loose;exit;
						//loose qty calculation
					}	*/

				$original_price = $_REQUEST['price'];
				$discount = $_REQUEST['discount'];
				$discount_amt = $_REQUEST['discount_amount'];
				$add_discount = $_REQUEST['add_discount'];
				$item_gst = $GST;
				//$item_gst=$_REQUEST['item_gst'];

				// Max item discount: Regular 44% / Channel Partner 50% — same as web Order/Quotation
				$disc_customer_id = 0;
				$disc_force_cp = false;
				$cart_main_id = isset($_REQUEST['cart_id']) ? (int) $_REQUEST['cart_id'] : (isset($cart_id) ? (int) $cart_id : 0);
				if ($cart_main_id > 0) {
					if ($_REQUEST['cart_type'] == "3") {
						$disc_customer_id = (int) $db->rp_getValue("orders", "customer_id", "id='" . $cart_main_id . "' AND isDelete=0", 0);
						$cp_flag = (int) $db->rp_getValue("orders", "channel_partner_order_flag", "id='" . $cart_main_id . "' AND isDelete=0", 0);
						if ($cp_flag === 1) {
							$disc_force_cp = true;
						}
					} else if ($_REQUEST['cart_type'] == "2") {
						$disc_customer_id = (int) $db->rp_getValue("quotation_detail", "dealer_id", "id='" . $cart_main_id . "' AND isDelete=0", 0);
					} else {
						$disc_customer_id = (int) $db->rp_getValue("cart_detail", "customer_id", "id='" . $cart_main_id . "' AND isDelete=0", 0);
					}
				}
				if (isset($_REQUEST['customer_id']) && (int) $_REQUEST['customer_id'] > 0) {
					$disc_customer_id = (int) $_REQUEST['customer_id'];
				}
				if (function_exists('cp_validate_item_discount_max')) {
					$disc_err = cp_validate_item_discount_max($db, $discount, $discount_amt, $original_price, $disc_customer_id, $disc_force_cp);
					if ($disc_err) {
						$db->printJSON($disc_err);
						exit;
					}
				} else {
					$maxPct = $disc_force_cp ? 50 : 44;
					$discount_check = floatval($discount);
					$discount_amt_check = floatval($discount_amt);
					$original_price_check = floatval($original_price);
					if ($discount_check > $maxPct) {
						$ack = array(
							"ack" => 0,
							"ack_msg" => "You cant add Discount More Than " . $maxPct . "%",
							"developer_msg" => "You cant add Discount More Than " . $maxPct . "%",
						);
						$db->printJSON($ack);
						exit;
					}
					if ($original_price_check > 0 && $discount_amt_check > ($original_price_check * $maxPct / 100)) {
						$ack = array(
							"ack" => 0,
							"ack_msg" => "You cant add Discount More Than " . $maxPct . "%",
							"developer_msg" => "You cant add Discount More Than " . $maxPct . "%",
						);
						$db->printJSON($ack);
						exit;
					}
				}

				if ($discount != "" && $discount != "0.00" && $discount != "0" && $discount != "0.0") {
					$discount_amt = ($original_price * $discount) / 100;
					$after_discount_price = $original_price - $discount_amt;
				} else {
					$after_discount_price = $original_price - $discount_amt;
				}

				$add_discount_amt = ($after_discount_price * $add_discount) / 100;
				$after_add_discount_price = $after_discount_price - $add_discount_amt;
				//echo $after_add_discount_price; exit;

				// $item_gst_amt=($after_add_discount_price*$item_gst)/100;
				// $after_gst_price=$after_add_discount_price+$item_gst_amt;

				$unitprice = $db->rp_num($after_add_discount_price);
				//$unitprice=$db->rp_num($after_gst_price);

				/*	echo $update_qty;
				echo "    ".$unitprice."   ";

			*/
				$update_totalprice = $db->rp_num($update_qty * $unitprice);

				$Newtotalprice = $update_qty * $unitprice;
				$taxable_amount = $Newtotalprice;

				$item_gst_amount1 = (($taxable_amount * $GST) / 100);
				$sub_total = ($taxable_amount + $item_gst_amount1);

				//echo $update_totalprice;exit;

				//$update_box_qty=$db->rp_num($_REQUEST['qty']/$inner_size);
				//$update_cartoon_qty=$db->rp_num($update_box_qty/$outer_size);


				/*$update_box_qty=$db->rp_num($_REQUEST['box_qty']);

				$update_cartoon_qty=$db->rp_num($_REQUEST['cartoon_qty']);*/


				// if($_REQUEST['cart_type']=="3"){
				// 	$update_item=array(
				// 		"pro_qty"=>$update_qty,
				// 		"remaining_qty"=>$update_qty,
				// 		"totalprice"=>$update_totalprice,
				// 		"box_qty"=>$update_bag_qty,
				// 		"cartoon_qty"=>$update_cartoon_qty,
				// 		"loose_qty"=>$loose,
				// 		"unitprice"=>$unitprice,
				// 		"discount"=>$discount,
				// 		"discount_amount"=>$discount_amt,
				// 		/*"add_discount"=>$add_discount,
				// 		"add_discount_amount"=>$add_discount_amt,
				// 		*/"original_price"=>$original_price,
				// 		//"item_gst"=>$item_gst,
				// 		//"item_gst_amount"=>$item_gst_amt,
				// 		"pro_description"=>$db->clean($pro_description),
				// 		"igst_tax" => $GST,
				// 		"igst_amount" =>$item_gst_amount1 ,
				// 		"taxable" => $taxable_amount,
				// 		"subtotal" => $sub_total,
				// 		"modified_date"=>date("Y-m-d H:i:s"),
				// 		"order_item_brand_id"=>$_REQUEST['order_item_brand_id'],
				// 	);
				// } 
				// else 
				if ($_REQUEST['cart_type'] == "0" || $_REQUEST['cart_type'] == "" || $_REQUEST['cart_type'] == "1" || $_REQUEST['cart_type'] == "2" || $_REQUEST['cart_type'] == "3") {
					$update_item = array(
						"pro_qty" => $update_qty,
						"remaining_qty" => $update_qty,
						"totalprice" => $update_totalprice,
						"box_qty" => $update_bag_qty,
						"cartoon_qty" => $update_cartoon_qty,
						"loose_qty" => $loose,
						"unitprice" => $unitprice,
						"discount" => $discount,
						"discount_amount" => $discount_amt,
						/*"add_discount"=>$add_discount,
						"add_discount_amount"=>$add_discount_amt,
						*/
						"original_price" => $original_price,
						//"item_gst"=>$item_gst,
						//"item_gst_amount"=>$item_gst_amt,
						"pro_description" => $db->clean($pro_description),
						"igst_tax" => $GST,
						"igst_amount" => $item_gst_amount1,
						"taxable" => $taxable_amount,
						"subtotal" => $sub_total,
						"modified_date" => date("Y-m-d H:i:s"),
						"order_item_brand_id" => $_REQUEST['order_item_brand_id'],
					);
				} else {
					$update_item = array(
						"pro_qty" => $update_qty,
						"remaining_qty" => $update_qty,
						"totalprice" => $update_totalprice,
						"box_qty" => $update_bag_qty,
						"cartoon_qty" => $update_cartoon_qty,
						"loose_qty" => $loose,
						"unitprice" => $unitprice,
						"discount" => $discount,
						"discount_amount" => $discount_amt,
						/*"add_discount"=>$add_discount,
						"add_discount_amount"=>$add_discount_amt,
						*/
						"original_price" => $original_price,
						//"item_gst"=>$item_gst,
						//"item_gst_amount"=>$item_gst_amt,
						"pro_description" => $db->clean($pro_description),
						"igst_tax" => $GST,
						"igst_amount" => $item_gst_amount1,
						"taxable" => $taxable_amount,
						"subtotal" => $sub_total,
						"modified_date" => date("Y-m-d H:i:s"),

					);
				}
				// echo "<pre>"; print_r($update_item); exit();
				$isUpdate = $db->rp_update($table, $update_item, "id='" . $_REQUEST['cart_item_id'] . "' AND isDelete=0", 0);



				if ($isUpdate) {


					if ($table == "order_product_item") {
						// Delete Scheme Item //
						$order_id = $db->rp_getValue("order_product_item", "order_id", "isDelete=0 AND id='" . $_REQUEST['cart_item_id'] . "'");
						$db->rp_Delete("order_scheme_items", "isDelete=0 AND order_id='" . $order_id . "' AND order_item_id='" . $_REQUEST['cart_item_id'] . "'");

						// Delete Scheme Item //

						$objOrder->AddschemeItems($order_id, $_REQUEST['cart_item_id'], $_REQUEST['pid'], $_REQUEST['weight_id'], $update_qty);
					}




					$reply = array("ack" => 1, "developer_msg" => "Product Updated Successfully", "ack_msg" => "Product Updated Successfully");
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Product Updated Failed", "ack_msg" => "Product Updated Failed");
				}
				$db->printJSON($reply);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "get_cart" || $service == 89) {
			if (isset($_REQUEST['cid']) && isset($_REQUEST['cid']) && isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['sales_executive_id'])) {

				if ($_REQUEST['cart_type'] == "2") {
					$table_main = "quotation_detail";
					$table_item = "quotation_product_item";
				} else {
					$table_main = "cart_detail";
					$table_item = "cart_item";
				}

				if ($_REQUEST['cart_type'] == "0") {
					$order_r = $db->rp_getData($table_main, "*", "customer_id='" . $_REQUEST['cid'] . "' AND sales_id='" . $_REQUEST['sales_executive_id'] . "' AND cart_type='" . $_REQUEST['cart_type'] . "' AND status=-1 AND isDelete=0", "id DESC", 0);
				} else if ($_REQUEST['cart_type'] == "2") {

					$order_r = $db->rp_getData($table_main, "*", "id='" . $_REQUEST['quotation_id'] . "' AND isDelete=0", "id DESC", 0);
				} else {
					if ($_REQUEST['lead_id'] != "") {
						$order_r = $db->rp_getData($table_main, "*", "lead_id='" . $_REQUEST['lead_id'] . "' AND sales_id='" . $_REQUEST['sales_executive_id'] . "' AND cart_type='" . $_REQUEST['cart_type'] . "' AND status=-1 AND isDelete=0", "id DESC", 0);
					} else {

						$order_r = $db->rp_getData($table_main, "*", "customer_id='" . $_REQUEST['cid'] . "' AND sales_id='" . $_REQUEST['sales_executive_id'] . "' AND cart_type='" . $_REQUEST['cart_type'] . "' AND status=-1 AND isDelete=0", "id DESC", 0);
					}
					/*		$order_r=$db->rp_getData($table_main,"*","lead_id='".$_REQUEST['lead_id']."' AND sales_id='".$_REQUEST['sales_executive_id']."' AND cart_type='".$_REQUEST['cart_type']."' AND status=-1 AND isDelete=0","",0);*/
				}
				if ($order_r) {
					$r = array();
					$order_d = mysqli_fetch_assoc($order_r);
					//print_r($order_d);exit;
					if ($_REQUEST['cart_type'] == "2") {
						$order_item_r = $db->rp_getData($table_item, "*", "quotation_id='" . $order_d['id'] . "' AND  isDelete=0", "", 0);
					} else {
						$order_item_r = $db->rp_getData($table_item, "*", "order_id='" . $order_d['id'] . "' AND  isDelete=0", "", 0);
					}

					$price_list_id = $db->rp_getValue("executive", "price_list_id", "id='" . $_REQUEST['cid'] . "'", 0);
					$is_premium = $db->rp_getValue("price_list", "is_premium", "id='" . $price_list_id . "'", 0);
					$customer_state = $db->rp_getValue("executive", "state", "id='" . $_REQUEST['cid'] . "'", 0);
					$order_d['customer_type'] = $db->rp_getValue("executive", "type_of_executive", "id='" . $_REQUEST['cid'] . "'", 0);

					$order_d['price_list_name'] = $db->rp_getValue("price_list", "pricelist_name", "id='" . $price_list_id . "'", 0);
					if ($order_d['terms_condition_id'] != 0) {
						$order_d['terms_condition_id'] = $order_d['terms_condition_id'];
						$order_d['terms_comdition'] = $order_d['terms_comdition'];
					} else {
						$order_d['terms_comdition'] = "";
					}
					$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $_REQUEST['sales_executive_id'] . "'", 0);
					$sales_phone = $db->rp_getValue("sales_executive", "phone", "id='" . $_REQUEST['sales_executive_id'] . "'");

					if ($order_d['faithfully'] == "" || $order_d['faithfully'] == NULL) {
						$order_d['faithfully'] = $sales_name . '<br/>' . $sales_phone;
					}
					$order_d['gst_no'] = $order_d['gst'];
					if ($order_d['gst_no'] == "") {

						$order_d['gst_no'] = $db->rp_getValue("executive", "gst", "id='" . $order_d['customer_id'] . "' AND isDelete=0", 0);
						//echo $order_d['gst_no'];exit;
					}
					//$order_d['gst_no']=$order_d['gst'];
					if ($order_d['cash_discount'] == "" || $order_d['cash_discount'] == 0) {

						$order_d['cash_discount'] = $db->rp_getValue("executive", "cash_discount", "id='" . $order_d['customer_id'] . "' AND isDelete=0", 0);
						//echo $order_d['gst_no'];exit;
					}
					if ($order_d['additional_discount'] == "" || $order_d['additional_discount'] == 0) {

						$order_d['additional_discount'] = $db->rp_getValue("executive", "additional_discount", "id='" . $order_d['customer_id'] . "' AND isDelete=0", 0);
						//echo $order_d['gst_no'];exit;
					}

					//echo $order_d['po_date'];exit();
					if ($order_d['po_date'] == "1970-01-01" || $order_d['po_date'] == "0000-00-00" || $order_d['po_date'] == "") {

						$order_d['po_date'] = "";
					} else {
						$order_d['po_date'] = date('d F Y', strtotime($order_d['po_date']));
					}
					$order_d['quotation_date'] = date('d-m-Y', strtotime($order_d['quotation_date']));
					if ($order_d['quotation_date'] == "01-01-1970" || $order_d['quotation_date'] == "00-00-0000") {

						$order_d['quotation_date'] = "";
					}

					if ($_REQUEST['cart_type'] == "2") {
						$total_qty = $db->rp_getValue($table_item, "SUM(pro_qty)", "quotation_id='" . $order_d['id'] . "' AND  isDelete=0");
					} else {
						$total_qty = $db->rp_getValue($table_item, "SUM(pro_qty)", "order_id='" . $order_d['id'] . "' AND  isDelete=0");
					}
					$order_d['total_qty'] = $total_qty;
					$subtotal = 0;
					$taxable_amount = 0;
					$grandtotal = 0;
					$order_unit_arr = array("-1" => "Box", "-2" => "Strip", "-3" => "Pallet", "1" => "Caret", "2" => "Big Box", "100" => "Nos");

					//if($order_item_r || $_REQUEST['cart_type']!="0")
					if ($order_item_r || $_REQUEST['cart_type'] != "0" || $_REQUEST['cart_type'] == "0") {
						$data = array();
						while ($order_item_d = mysqli_fetch_assoc($order_item_r)) {
							$subtotal += $order_item_d['totalprice'];
							$item_gst_total += $order_item_d['item_gst_amount'];
							$GST = $db->rp_getValue("product", "igst", "id='" . $order_item_d['pro_id'] . "'");
							$order_item_d['size'] = $db->rp_getValue("weight", "name", "id='" . $order_item_d['weight_id'] . "' AND isDelete=0");

							$order_item_d['stock'] = $db->rp_getValue("product_weight_price", "stock_qty", "weight_id='" . $order_item_d['weight_id'] . "' AND product_id='" . $order_item_d['pro_id'] . "'  AND isDelete=0");

							// Min sell removed for app — always 0; discount/rate calc uses MRP (original_price/price)
							$order_item_d['minimum_selling_price'] = 0;

							$order_item_d['order_item_brand_id'] = $order_item_d['order_item_brand_id'];
							$order_item_d['order_item_brand_name'] = $db->rp_getValue("order_item_brand_master", "name", "isDelete=0 AND isActive=1 AND id='" . $order_item_d['order_item_brand_id'] . "'");

							//$unit_id = $db->rp_getValue("product", "display_unit", "id='".$order_item_d['pro_id']."'","",0);
							$order_item_d['unit_name'] =	$order_unit_arr[$order_item_d['item_order_unit']];
							//$order_item_d['unit_name'] = $db->rp_getValue("unit", "name", "id='" . $unit_id . "'",0);

							$order_item_d['hsn_code'] = $db->rp_getValue("product", "hsn_code", "id='" . $order_item_d['pro_id'] . "'", "", 0);

							if ($order_item_d['discount'] == 0) {
							} else {
								$order_item_d['discount_amount'] = "0";
							}
							$cash_discount += $order_item_d['cash_discount'];
							$cash_discount_amount += $order_item_d['cash_discount_amount'];
							$additional_discount += $order_item_d['additional_discount'];
							$additional_discount_amount += $order_item_d['additional_discount_amount'];
							$after_packing_charge += $order_item_d['other_charge'];
							$after_transport_charge += $order_item_d['fright_charge'];


							// $taxable_amount=$order_item_d['totalprice']-$order_item_d['cash_discount_amount']-$order_item_d['additional_discount_amount']+$order_item_d['other_charge']+$order_item_d['fright_charge'];

							$taxable_amount = $order_item_d['taxable']; //added by shivani
							// echo $order_item_d['taxable'];exit;
							$total_taxable_amount += $taxable_amount;

							$gst_amount = ($order_item_d['igst_amount']); //added by shivani
							// $gst_amount=($taxable_amount*$order_item_d['igst_tax'])/100;
							$total_gst_amount += $gst_amount;
							//echo $order_items_d['igst_tax'];exit;
							$final_total = $order_item_d['totalprice'] + $gst_amount;
							$main_total += $final_total;
							//	echo $taxable_amount;

							// added by shivani (27-02-2023)
							if ($order_item_d['item_order_unit'] < 0 && $order_item_d['item_order_unit'] != 100) {
								$item_order_unit_qty = $order_item_d['box_qty'];
							} else if ($order_item_d['item_order_unit'] > 0 && $order_item_d['item_order_unit'] != 100) {
								$item_order_unit_qty = $order_item_d['cartoon_qty'];
							} else if ($order_item_d['item_order_unit'] == 100) {
								$item_order_unit_qty = $order_item_d['pro_qty'];
							} else {
								$item_order_unit_qty = 0;
							}

							$order_item_d['item_order_unit_qty'] = $item_order_unit_qty;
							$order_item_d['is_premium'] = ($is_premium) ? $is_premium : "0";
							// $order_item_d['is_premium']='"'.$order_item_du['is_premium'].'"';
							// added by shivani (27-02-2023)

							$data[] = $order_item_d;
						}

						/*$after_additional_discounted = 0;
						$after_cash_discounted = 0;


						$cash_discount = $order_d['cash_discount'];
						$cash_discount_amount = $order_d['cash_discount_amount'];
						$after_cash_discounted = $db->rp_num($subtotal - $cash_discount_amount);


			    		$additional_discount = $order_d['additional_discount'];
						$additional_discount_amount = $order_d['additional_discount_amount'];
						$after_additional_discounted = $after_cash_discounted - $additional_discount_amount;
						
						
						$final_total = $after_additional_discounted;

			    		$sub_total = $db->rp_num((float)$sub_total, 2, '.', '');
			    		*/
						//$final_total = $final_total+$order_d['packing_charge']+$order_d['transport_charge'];
						//	$after_packing_charge=$final_total+$order_d['packing_charge'];
						//$after_transport_charge=$after_packing_charge+$order_d['transport_charge'];

						/*$final_total=$after_transport_charge;
						$gst_amount=$order_d['igst_amount'];

						//	echo $customer_state;exit;
						if($order_d['igst_amount']!="" && $order_d['igst_amount']!=0){
							if (strtolower(CLIENT_STATE) == strtolower($customer_state)) 
							{
								if($order_d['customer_type']==7){
									$GST = "(CGST:0.05%,SGST:0.05%)";
								}else{
									$GST = "(CGST:9%,SGST:9%)";
								}
								
							} else
							{
								if($order_d['customer_type']==7){
									$GST = "(IGST:0.1%)";
								}else{
									$GST = "(IGST:18%)";
								}
								 
							}
						}else{
							$GST="";
						}*/

						$GST = "";
						//$gst_amount=$db->rp_num(($final_total*$GST)/100);
						//$gst_amount=$order_d['igst_amount'];
						$tcs_amount = $order_d['tcs_amount'];
						//echo $order_d['igst_amount'];exit();

						/*if($order_d['igst_amount']=="0" || $order_d['igst_amount']==null || $order_d['igst_amount']==""){
							//echo "string";exit();
							$total_gst_amount=0;
						}*/
						//echo $total_gst_amount;exit;
						$after_gst = $db->rp_num($main_total + $gst_amount);
						//$grandtotal=$db->rp_num($main_total+$tcs_amount);
						$grandtotal = $db->rp_num($total_taxable_amount + $total_gst_amount + $tcs_amount);
						$before_roundoff = $db->rp_num($total_taxable_amount + $total_gst_amount, 2);
						$whole = floor($before_roundoff);
						$fraction = $before_roundoff - $whole;
						$f1 =  $db->rp_num((float)$fraction, 2, '.', '');
						$roundoff = $db->rp_num($f1, 2);
						$grand_total = strval($db->rp_num(($before_roundoff - $roundoff), 2));
						// echo $grand_total;exit;

						$order_d['average_amount'] = $db->rp_num(($subtotal / $total_qty), 3);

						$order_d['gst'] = $GST;
						//$order_d['gst']="";
						$order_d['gst_amount'] = $db->rp_num($total_gst_amount, 2);
						//echo $order_d['gst_amount'];exit();
						$order_d['additional_discount_amount'] = $db->rp_num($additional_discount_amount, 2);
						$order_d['cash_discount_amount'] = $db->rp_num($cash_discount_amount, 2);
						//$order_d['after_additional_discounted']=$db->rp_num($after_additional_discounted,2);
						//$order_d['after_cash_discounted']=$db->rp_num($after_cash_discounted,2);
						//$order_d['after_transport_charge']=$db->rp_num($after_transport_charge,2);
						//$order_d['after_packing_charge']=$db->rp_num($after_packing_charge,2);
						$order_d['after_transport_charge'] = $db->rp_num($after_transport_charge, 2);
						$order_d['after_packing_charge'] = $db->rp_num($after_packing_charge, 2);

						$order_d['final_total'] = $db->rp_num($total_taxable_amount, 2);
						$order_d['subtotal'] = $db->rp_num($main_total, 2);
						$order_d['roundoff'] = $db->rp_num($f1, 2);
						$order_d['grandtotal'] = $db->rp_num(round($grandtotal), 2);

						$order_d['tcs'] = TCS_CHARGE_IN_PER;
						$order_d['booking_place'] = $db->rp_getValue("executive", "booking_place", "isDelete=0 AND id='" . $_REQUEST['cid'] . "'", 0);
						$order_d['booking_pincode'] = $db->rp_getValue("executive", "booking_pincode", "isDelete=0 AND id='" . $_REQUEST['cid'] . "'", 0);

						/*$gst_amount=$db->rp_num(($subtotal*$GST)/100);
						$grandtotal=$db->rp_num($subtotal+$gst_amount);
						$order_d['gst']=$GST;
						$order_d['gst_amount']=$db->rp_num($gst_amount);
						$order_d['final_total']=$grandtotal;
						$order_d['subtotal']=$db->rp_num($subtotal);
						$whole = floor($grandtotal);      // 1
				        $fraction = $grandtotal - $whole;

				        $f1=  $db->rp_num((float)$fraction, 2, '.', '');
						$order_d['roundoff']=$db->rp_num($f1);
						$order_d['grandtotal']=$db->rp_num(round($grandtotal));*/
						$order_d['products'] = $data;
						$order_d['company_type'] = $db->rp_getValue("company_master", "name", "id='" . $db->rp_getValue("executive", "type_of_company", "id='" . $order_d['customer_id'] . "' AND isDelete=0", 0) . "' AND isDelete=0", 0);

						$r[] = $order_d;

						$ack = array("ack" => 1, "ack_msg" => "Order Detail Found!", "developer_msg" => "Order Detail Found!", "result" => $r, "total_qty" => $total_qty);
					} else {
						$ack = array("ack" => 0, "ack_msg" => "Cart is Empty!", "developer_msg" => "Cart is Empty");
						/*$data=array();
						$order_d['products']=$data;
						$r[]=$order_d;*/
					}
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Cart is Empty!", "developer_msg" => "Cart is Empty");
				}
				$db->printJSON($ack);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal Error!!Some Parameter Missing!",
					"developer_msg" => "Internal Error!!Some Parameter Missing!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "get_cart_qty_count" || $service == 90) {
			if (isset($_REQUEST['cid']) && isset($_REQUEST['cid'])) {
				$sales_id = isset($_REQUEST['sales_executive_id']) ? $_REQUEST['sales_executive_id'] : "";
				$where = "";
				if ($sales_id != "") {
					$where = " AND sales_id='" . $sales_id . "'";
				} else {
					$where = " AND sales_id=0";
				}
				$order_r = $db->rp_getData("cart_detail", "*", "customer_id='" . $_REQUEST['cid'] . "' AND status=-1 AND cart_type=0 AND isDelete=0" . $where, "", 0);
				if ($order_r) {
					$r = array();
					$order_d = mysqli_fetch_assoc($order_r);
					$total_qty = $db->rp_getValue("cart_item", "SUM(pro_qty)", "order_id='" . $order_d['id'] . "' AND  isDelete=0");
					$cartcount = $db->rp_getTotalRecord("cart_item", "order_id='" . $order_d['id'] . "' AND  isDelete=0");

					$ack = array(
						"ack" => 1,
						"ack_msg" => "Total Qty & Cart Count Found!!",
						"developer_msg" => "Total Qty & Cart Count Found!!",
						"total_qty" => $total_qty,
						"cartcount" => $cartcount,
					);
					$db->printJSON($ack);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "No Order Found!!",
						"developer_msg" => "No Order Found!!",
					);
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "download_order_pdf" || $service == 98) {
			$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : (isset($_REQUEST['id']) ? $_REQUEST['id'] : "");

			if (!empty($order_id)) {
				$order_id = $db->clean($order_id);
				$ack = $objSalesExecutive->DownloadOrder($order_id);
				$db->printJSON($ack);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
				$db->printJSON($ack);
			}
		} else if ($service == "download_quotation_pdf" || $service == "share_quotation_pdf" || $service == "quotation_pdf") {
			$quotation_id = isset($_REQUEST['quotation_id']) ? $_REQUEST['quotation_id'] : (isset($_REQUEST['id']) ? $_REQUEST['id'] : "");

			if (!empty($quotation_id)) {
				$quotation_id = $db->clean($quotation_id);
				$ack = $objQuotation->DownloadQuotation($quotation_id);
				$db->printJSON($ack);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
				$db->printJSON($ack);
			}
		}
		/*else if($service=="get_search_customer" || $service==106)
		{
			$searchName=isset($_REQUEST['searchName'])?$_REQUEST['searchName']:"";
			if($searchName!="")
			{
				$data_r=$db->rp_getData("executive","id,company_name,phone,cname,address,type_of_executive,whatsapp_no,country,state,city","company_name LIKE '%".$searchName."%' AND isDelete=0 AND isActive=1");
			}
			else
			{
				$data_r=$db->rp_getData("executive","id,company_name,phone,cname,address,type_of_executive,whatsapp_no,country,state,city","isDelete=0 AND isActive=1","",0);
			}
			if($data_r)
			{
				$DATA=array();
				while($data_d=mysqli_fetch_assoc($data_r))
				{
					$DATA[]=$data_d;
				}
				$ack=array( "ack"=>1,
                "ack_msg"=>"Customer Found!!",
                "developer_msg"=>"Customer Found!!",
                "result"=>$DATA
	            );
				$db->printJSON($ack);
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"No Customer Found!!",
                "developer_msg"=>"No Customer Found!!",
	            );
				$db->printJSON($ack);
			}

		}*/ else if ($service == "get_search_customer" || $service == 106) {
			$flag = isset($_REQUEST['flag']) ? $_REQUEST['flag'] : "";
			$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : "";
			$getData = file_get_contents(ADMINSITEURL . "search_inquiry.php?flag=" . $flag . "&term=" . urlencode($term) . "&api=1");
			echo $getData;
		} else if ($service == 'get_source_of_inquiry' || $service == 142) {
			$source_of_inquiry = array();
			$source_of_inquiry_data = $db->rp_getData("source_of_inquiry", "id,name,display_order", "isDelete=0", "display_order", 0);
			if ($source_of_inquiry_data) {
				while ($source_of_inquiry_d = mysqli_fetch_assoc($source_of_inquiry_data)) {
					$source_of_inquiry[] = $source_of_inquiry_d;
				}
			}

			// }

			if (!empty($source_of_inquiry)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Source Of Inquiry Detail!!", "developer_msg" => "You got it!!", "result" => $source_of_inquiry);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_price_list' || $service == 152) {
			$state_id = isset($_REQUEST['state_id']) ? $_REQUEST['state_id'] : "";
			$tcid = isset($_REQUEST['tcid']) ? $_REQUEST['tcid'] : "";
			$where = "isDelete=0";
			// if($state_id!=""){ 
			// $where = "FIND_IN_SET(".$state_id.",state_id) AND isDelete=0";
			// $where="state_id='".$state_id."' AND isDelete=0";
			/*if($tcid!="")
        		{ 
        			$where.=" AND tcid='".$tcid."'";
        		}*/

			$price_list_data = array();
			$price_list_r = $db->rp_getData("price_list", "*", $where, "", 0);
			if ($price_list_r) {
				while ($price_list_d = mysqli_fetch_assoc($price_list_r)) {
					$price_list_data[] = $price_list_d;
				}
			}

			if (!empty($price_list_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $price_list_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			/*  }else{
	            $ack=array("ack"=>0,"ack_msg"=>"Please Select state","developer_msg"=>"No Data Available!!",);
        	}*/
			$db->printJSON($ack);
		} else if ($service == 'get_company_type' || $service == 153) {
			$company_type_data = array();
			$company_type_r = $db->rp_getData("company_type", "*", "", 0);
			if ($company_type_r) {
				while ($company_type_d = mysqli_fetch_assoc($company_type_r)) {
					$company_type_data[] = $company_type_d;
				}
			}

			// }

			if (!empty($company_type_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $company_type_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		} else if ($service == "get_old_inquiry_data" || $service == 154) {
			$customer = array();
			$GROUP_BY = "";
			$GROUP_BY1 = "";

			$mobile_number = isset($_REQUEST['mobile_number']) ? $_REQUEST['mobile_number'] : "";
			$mobile_or_name = isset($_REQUEST['mobile_or_name']) ? $_REQUEST['mobile_or_name'] : "";
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";

			$status_array = array("0" => "Generate", "1" => "In Followup", "2" => "Interested", "-1" => "Not Interested", "3" => "Working", "-2" => "Non Relavent Inquiry", "4" => "Hot", "5" => "Cold", "6" => "Warm", "7" => "Wrong Call", "8" => "Will Interested", "9" => "Not Working", "10" => "Not Doing Business");

			if ($mobile_or_name == 1) {
				$where = " phone = '" . $mobile_number . "' AND isDelete=0 ";
				$GROUP_BY = " GROUP BY phone";
				$where1 = " mobile_number = '" . $mobile_number . "' AND isDelete=0 ";
				$GROUP_BY1 = " GROUP BY mobile_number";
			} else if ($mobile_or_name == 2) {
				$where = " company_name = '" . $mobile_number . "' AND isDelete=0 ";
				$GROUP_BY = " GROUP BY company_name";
				$where1 = " company_name = '" . $mobile_number . "' AND isDelete=0 ";
				$GROUP_BY1 = " GROUP BY company_name";
			}

			if ($type == "-1") {
				$where1 .= " AND inquiry_lead_flag = '-1' ";
			} else if ($type == "0") {
				$where1 .= " AND inquiry_lead_flag = '0' ";
			} else {
				$where1 .= " AND inquiry_lead_flag = '1' ";
			}

			$inqDataR = $db->rp_getData("no_order_inquiry", "*,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `no_order_inquiry`.`sales_executive_id` ) AS sales_executive_id_a,(SELECT name FROM sales_executive WHERE `sales_executive`.`id` = `no_order_inquiry`.`inquiry_assign_to` ) AS inquiry_assign_to", $where1, "", 0);

			if (mysqli_num_rows($inqDataR) > 0) {
				while ($inqData = mysqli_fetch_assoc($inqDataR)) {
					$inqData['inquiry_date'] = $inqData['inquiry_date'] = ($inqData['inquiry_date'] != "0000-00-00" && $inqData['inquiry_date'] != "1970-01-01") ? date("d-m-Y", strtotime($inqData['inquiry_date'])) : "";
					$inqData['inq_no'] = "#INQ/" . $inqData['id'];
					$inqData['status_slug'] = $status_array[$inqData['status']];

					$inqData['color_code'] = $db->inquiry_status_color[$inqData['status_slug']];

					$inqData['created_by_name'] = $db->rp_getValue("sales_executive", "name", "id='" . $inqData['inquiry_created_by'] . "'");
					$customer[] = $inqData;
				}
			}

			if (!empty($customer)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $customer);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
			/*$getInquiry = file_get_contents(ADMINSITEURL."ajax_get_customer_info_ajax.php?mobile_number=".$mobile_number."&mobile_or_name=".urlencode($mobile_or_name)."&api=1&type=".$type);
			echo $getInquiry; */
		} else if ($service == "add_inquiry_attachment" || $service == 155) {
			$inquiry_id = isset($_REQUEST['inquiry_id']) ? $_REQUEST['inquiry_id'] : "";
			if ($inquiry_id) {
				if (isset($_FILES["file_path"])) {
					$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG");
					$temp = explode(".", $_FILES["file_path"]["name"]);
					$extension = end($temp);
					$fileName 	= $db->clean($_FILES["file_path"]["name"]);
					if ($fileName != "") {
						$fileSize 	= round($_FILES["file_path"]["size"]); // BYTES
						$adate 		= date('Y-m-d H:i:m');
						$extension	= end(explode(".", $fileName));
						if (!in_array($extension, $allowedExts)) {
							$file_error = true;
						}
						$image_path	= 'inquiry_document_' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	= INQUIRY_ATTACH_IMAGE_A . $image_path;
						$_FILES['file_path']['tmp_name'];
						move_uploaded_file($_FILES['file_path']['tmp_name'], $filePath);
						$new_image = true;
					} else {
						$image_path = "";
					}
				} else {
					$new_image = false;
					$image_path = "";
				}
				$rows = array("inquiry_id", "image_path");
				$values = array($inquiry_id, $image_path);
				$insertid = $db->rp_insert("no_order_inquiry_attachment", $values, $rows, 0);
				if ($insertid) {
					$ack = array("ack" => 1, "ack_msg" => "Inquiry Attachment Add Successfully.", "developer_msg" => "Inquiry Attachment Add Successfully.");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Inquiry Attachment Add Failed.", "developer_msg" => "Inquiry Attachment Add Failed.");
			}
			$db->printJSON($ack);
		}

		// get followup reason list API
		else if ($service == 'get_followup_reason_list' || $service == 156) {
			$followup_reason_data = array();
			$followup_reason_r = $db->rp_getData("followup_reason", "*", "isDelete=0", 0);
			if ($followup_reason_r) {
				while ($followup_reason_d = mysqli_fetch_assoc($followup_reason_r)) {
					$followup_reason_data[] = $followup_reason_d;
				}
			}

			// }

			if (!empty($followup_reason_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $followup_reason_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		}
		// get Data transport_by list
		else if ($service == 'get_transport_by_list' || $service == 157) {
			$transport_by_data = array();
			$transport_by_r = $db->rp_getData("transport_by", "*", "isDelete=0", 0);
			// get data for single table only 
			if ($transport_by_r) {
				while ($transport_by_d = mysqli_fetch_assoc($transport_by_r)) {
					$transport_by_data[] = $transport_by_d;
				}
			}

			// }

			if (!empty($transport_by_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $transport_by_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		}

		// get Data transport_master list 
		else if ($service == 'get_transport_master_list' || $service == 158) {
			$transport_throught_id = isset($_REQUEST['transport_throught_id']) ? $_REQUEST['transport_throught_id'] : "";

			$transport_master_data = array();

			$transport_master_r = $db->rp_getData("transport_master", "*", "transport_by_id='" . $transport_throught_id . "' AND isDelete=0", "name ASC", 0);
			// transport_by_id is tar table get name for transport by multi table data

			if ($transport_master_r) {

				while ($transport_master_d = mysqli_fetch_assoc($transport_master_r)) {
					// Here is pass id and get name by transport_master to transport_by table  
					$transport_master_d["transport_by_name"] = $db->rp_getValue("transport_by", "name", "id='" . $transport_master_d['transport_by_id'] . "' ", 0);
					$transport_master_data[] = $transport_master_d;
				}
			}


			if (!empty($transport_master_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $transport_master_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		}

		// this is for get data dispatch_detail with limit and search by sale_id & order_id
		else if ($service == 'get_dispatch_detail_list' || $service == 159) {

			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$limit = $objDispatch->getLimit($limit);

			$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : "";
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";

			$Where .= "isDelete=0";

			if ($order_id != "") {
				$Where .= " AND order_id = '" . $order_id . "'";
			}

			if ($sales_id != "") {
				$Where .= " AND sales_id = '" . $sales_id . "'";
			}

			$dispatch_detail_data = array();
			$dispatch_detail_r = $db->rp_getData("dispatch_detail", "*", $Where, "", 0, $limit);

			// get data for single table only 
			if ($dispatch_detail_r) {
				while ($dispatch_detail_d = mysqli_fetch_assoc($dispatch_detail_r)) {

					$dispatch_detail_d["order_no"] = $db->rp_getValue("orders", "order_no", "id='" . $dispatch_detail_d['order_id'] . "'", 0);

					$dispatch_detail_d['dispatch_date'] = date('d-m-y', strtotime($dispatch_detail_d['dispatch_date']));
					$dispatch_detail_d['expected_dispatch_date'] = date('d-m-y', strtotime($dispatch_detail_d['expected_dispatch_date']));

					if ($dispatch_detail_d['order_type'] == '1') {
						$slug = "Super Stockist";
					} else if ($dispatch_detail_d['order_type'] == '2') {
						$slug = "Distributor";
					} else if ($dispatch_detail_d['order_type'] == '3') {
						$slug = "Dealer";
					} else if ($dispatch_detail_d['order_type'] == '4') {
						$slug = "B2B Customer";
					} else if ($dispatch_detail_d['order_type'] == '6') {
						$slug = "B2C Customer";
					} else if ($dispatch_detail_d['order_type'] == 'normal_user') {
						$slug = "Normal Customer";
					}

					$dispatch_detail_d["order_type"] = $slug;


					$dispatch_status_array = array("0" => "Pending", "1" => "Complete", "2" => "Packing Slip Created");
					$dispatch_detail_d["status_name"] = $dispatch_status_array[$dispatch_detail_d['status']];

					$dispatch_detail_d["transport_charge"] = $db->rp_getValue("orders", "transport_charge", "id='" . $dispatch_detail_d['order_id'] . "'");


					$dispatch_detail_data[] = $dispatch_detail_d;
				}
			}



			if (!empty($dispatch_detail_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $dispatch_detail_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		}

		// get Data packing_slip_list list with limit and search by dispatch_id
		else if ($service == 'get_packing_slip_list' || $service == 160) {

			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$limit = $objDispatch->getLimit($limit);

			$dispatch_id = isset($_REQUEST['dispatch_id']) ? $_REQUEST['dispatch_id'] : "";

			$Where .= "isDelete=0";

			if ($dispatch_id != "") {
				$Where .= " AND dispatch_id = '" . $dispatch_id . "'";
			}


			$packingslip_detail_data = array();
			$packingslip_detail_r = $db->rp_getData("packing_slip", "*", $Where, "", 0, $limit);

			// get data for single table only 
			if ($packingslip_detail_r) {
				while ($packingslip_detail_d = mysqli_fetch_assoc($packingslip_detail_r)) {

					// $packingslip_detail_d["order_no"]=$db->rp_getValue("orders","order_no","id='".$packingslip_detail_d['order_id']."'",0);
					// $invoice_detail_d["sales_name"]=$db->rp_getValue("sales_executive","name","id='".$invoice_detail_d['sales_id']."'",0);

					$packingslip_detail_d["customer_type_name"] = $db->rp_getValue("customer_type", "name", "id='" . $packingslip_detail_d['customer_type'] . "'", 0);

					$sales_id = $db->rp_getValue('dispatch_detail', 'sales_id', 'id="' . $packingslip_detail_d['dispatch_id'] . '" AND isDelete=0 AND isActive=1');

					$packingslip_detail_d["sales_name"] = $db->rp_getValue('sales_executive', 'name', 'id="' . $sales_id . '" AND isDelete=0 AND isActive=1');

					$packingslip_detail_d["company_name"] = $db->rp_getValue("executive", "company_name", "id='" . $packingslip_detail_d['customer_id'] . "'", 0);
					// $packingslip_detail_d["customer_name"]=$db->rp_getValue("customer_type","name","id='".$packingslip_detail_d['customer_id']."'",0);
					$packingslip_detail_d["dispatch_no"] = $db->rp_getValue("dispatch_detail", "dispatch_no", "id='" . $packingslip_detail_d['dispatch_id'] . "'", 0);

					$packingslip_detail_d['packing_slip_date'] = date('d-m-y', strtotime($packingslip_detail_d['packing_slip_date']));

					$packing_status = array("0" => "Pending", "1" => "Approved", "2" => "Dispatch", "3" => "Cancelled", "-1" => "Add to Cart", "-2" => "Disapproved", "4" => "Partially Dispatched");
					$packingslip_detail_d["status_name"] = $packing_status[$packingslip_detail_d['status']];

					$packingslip_detail_d["total_baggage"] = $db->rp_num($db->rp_getValue('packing_slip_item', 'MAX(main_carton_type_count)', 'packing_slip_id="' . $packingslip_detail_d['id'] . '" AND isDelete=0 AND isActive=1'), 2);

					$packingslip_detail_d["total_tem_qty"] = $db->rp_num($db->rp_getValue('packing_slip_item', 'SUM(pro_qty)', 'packing_slip_id="' . $packingslip_detail_d['id'] . '" AND isDelete=0 AND isActive=1'), 2);

					$Mdata = $db->rp_getData('packing_slip_item', 'main_carton_type_weight', 'packing_slip_id="' . $packingslip_detail_d['id'] . '" AND isDelete=0 AND isActive=1 GROUP BY main_carton_type_count');
					$total = 0;
					while ($MdataD = mysqli_fetch_assoc($Mdata)) {
						$total += $MdataD['main_carton_type_weight'];
					}

					$packingslip_detail_d["total_baggage_weight"] = ($db->rp_num($total + $db->rp_getValue('packing_slip_item', 'SUM(pro_weight)', 'packing_slip_id="' . $packingslip_detail_d['id'] . '" AND isDelete=0 AND isActive=1'), 2));


					$Mdata = $db->rp_getData('packing_slip_item', 'main_carton_whole_actual_weight', 'packing_slip_id="' . $packingslip_detail_d['id'] . '" AND isDelete=0 AND isActive=1 GROUP BY main_carton_type_count');
					$total = 0;
					while ($MdataD = mysqli_fetch_assoc($Mdata)) {
						$packingslip_detail_d["actual_baggage_weight"] = $db->rp_num($total += $MdataD['main_carton_whole_actual_weight'], 2);
					}

					$packingslip_detail_data[] = $packingslip_detail_d;
				}
			}



			if (!empty($packingslip_detail_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $packingslip_detail_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		}
		// get Data packing_slip_list list with limit and search by dispatch_id
		else if ($service == 'get_invoice_list' || $service == 161) {

			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$limit = $objDispatch->getLimit($limit);


			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$dispatch_id = isset($_REQUEST['dispatch_id']) ? $_REQUEST['dispatch_id'] : "";

			$Where .= "isDelete=0";

			if ($dispatch_id != "") {
				$Where .= " AND dispatch_ids = '" . $dispatch_id . "'";
			}
			if ($customer_id != "") {
				$Where .= " AND customer_id = '" . $customer_id . "'";
			}


			$invoice_detail_data = array();
			$invoice_detail_r = $db->rp_getData("invoice_new", "*", $Where, "", 0, $limit);

			// get data for single table only 
			if ($invoice_detail_r) {
				while ($invoice_detail_d = mysqli_fetch_assoc($invoice_detail_r)) {

					$invoice_detail_d["dispatch_no"] = $db->rp_getValue("dispatch_detail", "dispatch_no", "id='" . $invoice_detail_d['dispatch_ids'] . "'", 0);
					$invoice_detail_d["sales_name"] = $db->rp_getValue("sales_executive", "name", "id='" . $invoice_detail_d['sales_id'] . "'", 0);

					if ($invoice_detail_d['invoice_date'] != "" && $invoice_detail_d['invoice_date'] != "01-01-70") {
						$invoice_detail_d['invoice_date'] = date('d-m-y', strtotime($invoice_detail_d['invoice_date']));
					} else {
						$invoice_detail_d['invoice_date'] = "";
					}
					$invoice_detail_d['adate'] = date('d-m-y H:i A', strtotime($invoice_detail_d['adate']));
					// if($invoice_detail_d['expected_dispatch_date']!="" && $invoice_detail_d['expected_dispatch_date']!="01-01-70")
					// {
					// 	$invoice_detail_d['expected_dispatch_date'] = date('d-m-y',strtotime($invoice_detail_d['expected_dispatch_date']));
					// }
					// else
					// {
					// 	$invoice_detail_d['expected_dispatch_date'] = "";	
					// }

					$invoice_status = array("0" => "Pending", "1" => "Approved", "2" => "Dispatch", "3" => "Cancelled", "-1" => "Add to Cart", "-2" => "Disapproved", "4" => "Partially Dispatched");
					$invoice_detail_d["status_name"] = $invoice_status[$invoice_detail_d['status']];

					if ($invoice_detail_d['customer_type'] == '1') {
						$slug = "Super Stockist";
					} else if ($invoice_detail_d['customer_type'] == '2') {
						$slug = "Distributor";
					} else if ($invoice_detail_d['customer_type'] == '3') {
						$slug = "Dealer";
					} else if ($invoice_detail_d['customer_type'] == '4') {
						$slug = "B2B Customer";
					} else if ($invoice_detail_d['customer_type'] == '6') {
						$slug = "B2C Customer";
					} else if ($invoice_detail_d['customer_type'] == 'normal_user') {
						$slug = "Normal Customer";
					}
					$invoice_detail_d["customer_type"] = $slug;


					$invoice_detail_data[] = $invoice_detail_d;
				}
			}



			if (!empty($invoice_detail_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $invoice_detail_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		}


		// this is for get data quotation_detail with limit and search by sale_id 
		else if ($service == 'get_quotation_detail_list' || $service == 162) {
			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$limit = $objDispatch->getLimit($limit);

			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$status_id = isset($_REQUEST['status_id']) ? $_REQUEST['status_id'] : "";
			$type_of_company = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : "";
			$Where = "";
			$Where .= "isDelete=0 AND status!=-1";

			if ($sales_id != "") {
				$Where .= " AND sales_id = '" . $sales_id . "'";
			}
			if ($customer_id != "") {
				$Where .= " AND customer_id = '" . $customer_id . "'";
			}

			if ($status_id != "") {
				$Where .= " AND status = '" . $status_id . "'";
			}
			if ($type_of_company != "") {
				$Where .= " AND type_of_company = '" . $type_of_company . "'";
			}

			$quotation_detail_data = array();

			$quotation_detail_r = $db->rp_getData("quotation_detail", "*", $Where, "id DESC", 0, $limit);
			// get data for single table only 
			if ($quotation_detail_r) {

				$status_array = array("-2" => "Disapproved", "0" => "Pending", "1" => "Approved", "3" => "Canceled", "4" => "Order Generated", "5" => "Lost");
				while ($quotation_detail_d = mysqli_fetch_assoc($quotation_detail_r)) {
					$quotation_detail_d["revised_count"] = $db->rp_getTotalRecord("quotation_detail", "refrence_id='" . $quotation_detail_d['id'] . "' AND isDelete=0");

					$quotation_detail_d["sales_person_name"] = $db->rp_getValue("sales_executive", "name", "id='" . $quotation_detail_d['sales_id'] . "'", 0);

					// $quotation_detail_d['customer_type']=$db->rp_getValue("customer_type", "name", "id='" . $quotation_detail_d['customer_type'] . "'",0);

					$quotation_detail_d['quotation_type'] = $db->rp_getValue("customer_type", "name", "id='" . $quotation_detail_d['customer_type'] . "'", 0);

					if ($quotation_detail_d['inquiry_id'] != 0) {
						$quotation_detail_d['inquiry_no'] = "#INQ/" . $quotation_detail_d['inquiry_id'];
					} else {
						$quotation_detail_d['inquiry_no'] = "";
					}

					$quotation_detail_d['terms_comdition'] =  htmlentities($quotation_detail_d['terms_comdition']);
					$quotation_detail_d['customer_name'] =  htmlentities($quotation_detail_d['customer_name']);
					$quotation_detail_d['faithfully'] =  htmlentities($quotation_detail_d['faithfully']);

					$quotation_detail_d['shipping_address'] =  htmlentities($quotation_detail_d['shipping_address']);
					$quotation_detail_d['address'] =  htmlentities($quotation_detail_d['address']);
					$quotation_detail_d['billing_address'] =  htmlentities($quotation_detail_d['billing_address']);
					$quotation_detail_d['quotation_amount'] =  $db->rp_num(round($quotation_detail_d['grand_total']));

					$quotation_detail_d['revised_quotation_main_id'] = $db->rp_getValue("quotation_detail", "quotation_no", "id='" . $quotation_detail_d['refrence_id'] . "'", 0);

					$quotation_raw_date = $quotation_detail_d['quotation_date'];
					$quotation_detail_d['quotation_date'] = date('d-m-y', strtotime($quotation_detail_d['quotation_date']));
					$quotation_detail_d['adate'] = date('d-m-y H:i A', strtotime($quotation_detail_d['adate']));

					/*	if ($quotation_detail_d['status'] == -2) {
						$status = "Disapproved";
					} else if ($quotation_detail_d['status'] == 0) {
						$status = "Pending";
					} else if ($quotation_detail_d['status'] == 1) {
						// $status = "Order Generated";
						$status = "Approved";
					} else if ($quotation_detail_d['status'] == 3) {
						$status = "Cancelled";
					} else if ($quotation_detail_d['status'] == -1) {
						$status = "Add to Cart";
					} else if($quotation_detail_d['status'] == 4) {
						$status = "Order Generated";
					}*/

					//$quotation_detail_d["status_name"]=$status;

					$quotation_detail_d['status_name'] = $status_array[intval($quotation_detail_d['status'])];
					$quotation_detail_d['status_name'] = ($quotation_detail_d['status_name'] != "") ? $quotation_detail_d['status_name'] : "";

					$quotation_detail_d['color_code'] = $db->quotation_status_color[$quotation_detail_d['status_name']];
					if ($quotation_detail_d['color_code'] == "") {
						$quotation_detail_d['color_code'] = "";
					}

					$quotation_clean_no = str_replace(array("/", "\\", " "), "-", stripslashes($quotation_detail_d['quotation_no']));
					$quotation_pdf_folder = date('d_m_Y', strtotime($quotation_raw_date)) . "_Quotation_" . $quotation_clean_no . 'pdf';
					$quotation_pdf_filename = $quotation_pdf_folder . '.pdf';
					$quotation_detail_d['pdf_url'] = ADMINSITEURL . "pdf/orders/" . $quotation_pdf_folder . "/" . $quotation_pdf_filename;
					$quotation_detail_d['file_url'] = $quotation_detail_d['pdf_url'];
					$quotation_detail_d['pdf_name'] = $quotation_pdf_filename;
					$quotation_detail_d['file_name'] = $quotation_pdf_filename;

					$quotation_detail_data[] = $quotation_detail_d;
				}
				/*Get Quotaion Count*/
				$quotationdata = array();
				$QuotationData = $db->rp_getData('quotation_detail', "DISTINCT(status)", "isDelete=0 AND status!='-1'", "", 0, $limit);
				//$OrderData = $this->db->rp_getData('orders',"status",$where,"id DESC",0,$limit);
				$status_array = array("-2" => "Disapproved", "0" => "Pending", "1" => "Approved", "3" => "Canceled", "4" => "Order Generated", "5" => "Lost");
				$status_key_array = array("-2", "0", "1", "3", "4", "5");

				while ($Quotation_d = mysqli_fetch_assoc($QuotationData)) {
					if ($_REQUEST['sales_id'] != "") {
						$Quotation_d['count'] = $db->rp_getTotalRecord("quotation_detail", "sales_id ='" . $_REQUEST['sales_id'] . "' AND status='" . $Quotation_d['status'] . "' AND isDelete=0", 0);
					} else {
						$Quotation_d['count'] = $db->rp_getTotalRecord("quotation_detail", "customer_id ='" . $_REQUEST['customer_id'] . "' AND status='" . $Quotation_d['status'] . "' AND isDelete=0", 0);
					}

					if (($key = array_search($Quotation_d['status'], $status_key_array)) !== false) {
						unset($status_key_array[$key]);
					}

					$Quotation_d['status_slug'] = $status_array[$Quotation_d['status']];

					$Quotation_d['status'] = $Quotation_d['status'];
					// echo "<pre>"; print_r($Order_d);
					$Quotation_d['color_code'] = $db->quotation_status_color[$Quotation_d['status_slug']];

					if ($Quotation_d['color_code'] == "") {
						$Quotation_d['color_code'] = "";
					}

					if ($Quotation_d['status_slug'] == "") {
						$Quotation_d['status_slug'] = "";
					}
					$quotationdata[] = $Quotation_d;
				}
				foreach ($status_key_array as $key => $remainval) {
					$Quotation_d['count'] = 0;
					$Quotation_d['status'] = $remainval;
					$Quotation_d['status_slug'] = $status_array[$remainval];
					$Quotation_d['color_code'] = $db->status_color[$status_array[$remainval]];
					$quotationdata[] = $Quotation_d;
				}

				$quotation = $quotationdata;
				/*Get Order Count*/
			}

			if (!empty($quotation_detail_data)) {
				$quotation_detail_data = $db->toUpperCaseAssocArray($quotation_detail_data);
				$quotation = $db->toUpperCaseAssocArray($quotation);
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $quotation_detail_data, "quotation_count" => $quotation);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		}

		// this my code Dhaval
		else if ($service == "invoice_pdf" || $service == 163) {
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : (isset($_REQUEST['quotation_id']) ? $_REQUEST['quotation_id'] : (isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : ""));

			if (!empty($id)) {
				if ($type == "1") //1=invoice
				{
					$ack = $objInvoice->DownloadInvoice($id);
				} else if ($type == "2") //packing slip
				{
					//packing slip code here
					$ack = $objInvoice->DownloadPackingSlip($id);
				} else if ($type == "3") //dispacth
				{
					//dispatch code here
					$ack = $objInvoice->DownloadDispatch($id);
				} else if ($type == "4" || empty($type)) //quotation
				{
					//quotation code here
					$ack = $objQuotation->DownloadQuotation($id);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
			}
			$db->printJSON($ack);
		} else if ($service == 'get_industry_type' || $service == 173) {

			$industry_type_data = array();

			$industry_type_r = $db->rp_getData("industry_type", "*", "isDelete=0", "", 0);

			if ($industry_type_r) {

				while ($industry_type_d = mysqli_fetch_assoc($industry_type_r)) {

					$industry_type_data[] = $industry_type_d;
				}
			}


			if (!empty($industry_type_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $industry_type_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		} else if ($service == "get_my_route" || $service == 179) {

			$ctable 	= "my_route";
			// $id = $_REQUEST['id'];
			$sales_id = $_REQUEST['sales_id'];
			$date = $_REQUEST['date'];

			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$limit = $objDispatch->getLimit($limit);


			$Where = "isDelete=0";

			if (isset($_REQUEST['date']) && $_REQUEST['date'] != "" && $_REQUEST['date'] != NULL && $sales_id != "") {
				$Where .= " AND date= '" . date("Y-m-d", strtotime($date)) . "'  AND sales_id='" . $sales_id . "'";
			}


			$ctable_r = $db->rp_getData($ctable, "*", $Where, "id DESC", 0, $limit);

			$route = array();
			if ($ctable_r) {
				while ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
					$ctable_d['date'] = date('d M Y', strtotime($ctable_d['date']));
					$customer_r = $db->rp_getData("executive", "*", "id=" . $ctable_d['customer_id'], "", 0);
					$customer_d = mysqli_fetch_assoc($customer_r);


					$ctable_d['customer_name'] = $customer_d['cname'];
					$ctable_d['company_name'] = $customer_d['company_name'];

					if ($ctable_d['no_order_inq_id'] != 0) {
						$ctable_d['customer_name'] = $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $ctable_d['no_order_inq_id'] . "'", 0);
						$ctable_d['company_name'] = $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $ctable_d['no_order_inq_id'] . "'", 0);

						$ctable_d['type_of_inquiry'] = $db->rp_getValue("no_order_inquiry", "inquiry_type", "id='" . $ctable_d['no_order_inq_id'] . "'", 0);
					}

					$ctable_d['address'] = $customer_d['address'];
					$ctable_d['phone'] = $customer_d['phone'];
					$ctable_d['latitude'] = $customer_d['latitude'];
					$ctable_d['longitude'] = $customer_d['longitude'];
					$ctable_d['customer_type'] = $customer_d['type_of_executive'];
					if ($customer_d['customer_flag'] != null) {
						$ctable_d['customer_flag'] = $customer_d['customer_flag'];
					}
					/*$db->rp_getValue("executive","cname","id=".$ctable_d['customer_id'] );
				    $ctable_d['company_name']=$db->rp_getValue("executive","company_name","id=".$ctable_d['customer_id'] );
				    $ctable_d['address']=$db->rp_getValue("executive","address","id=".$ctable_d['customer_id'] );
				    $ctable_d['phone']=$db->rp_getValue("executive","phone","id=".$ctable_d['customer_id'] );*/

					// $ctable_d['class_name']=$db->rp_getValue("class","name","id=".$ctable_d['class_id'] );
					// $ctable_d['area_name']=$db->rp_getValue("area","name","id=".$ctable_d['area_id'] );
					$ctable_d['state'] = $db->rp_getValue("master_route", "state", "isDelete=0 AND id='" . $ctable_d['route_id'] . "'");
					$ctable_d['city'] = $db->rp_getValue("master_route", "city", "isDelete=0 AND id='" . $ctable_d['route_id'] . "'");

					$class_id = $db->rp_getValue("master_route", "class_id", "isDelete=0 AND id='" . $ctable_d['route_id'] . "'");
					$area_id = $db->rp_getValue("master_route", "area_id", "isDelete=0 AND id='" . $ctable_d['route_id'] . "'");

					$ctable_d['class_name'] = $db->rp_getValue("class", "name", "id=" . $class_id);
					$ctable_d['area_name'] = $db->rp_getValue("area", "name", "id=" . $area_id);



					$route[] = $ctable_d;
				}
				$ack = array("ack" => 1, "ack_msg" => "route Found", "result" => $route);
				echo json_encode($ack);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No route found!!");
				echo json_encode($ack);
			}
		} else if ($service == 'add_my_route' || $service == 180) {
			if (isset($_REQUEST['route_id']) &&  isset($_REQUEST['sales_id']) && isset($_REQUEST['date'])) {

				$detail['route_id']      = isset($_REQUEST['route_id']) ? $db->clean($_REQUEST['route_id']) : "";

				$detail['sales_id']      = isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
				$detail['date']            = isset($_REQUEST['date']) ? date("Y-m-d", strtotime($_REQUEST['date'])) : "";
				//    $detail['class_id']        = isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";
				// $detail['area_id']         = isset($_REQUEST['area_id'])?$db->clean($_REQUEST['area_id']):"";
				$detail['customer_id']     = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
				$detail['remark']     = isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
				$detail['no_order_inq_id']     = isset($_REQUEST['no_order_inq_id']) ? $db->clean($_REQUEST['no_order_inq_id']) : "";


				$reply = $objExecutive->InsertMyRoute($detail);
				if ($reply['ack'] == 1) {
					$routeR = $db->rp_getData("my_route", "*", "id='" . $reply['id'] . "'");
					$routeD = mysqli_fetch_assoc($routeR);

					$routeD['date'] = date('d m Y', strtotime($routeD['date']));

					$reply = array("ack" => 1, "developer_msg" => $reply['developer_msg'], "ack_msg" => $reply['ack_msg'], "result" => $routeD);
				} else {
					$reply = array("ack" => 0, "developer_msg" => $reply['developer_msg'], "ack_msg" => $reply['ack_msg']);
				}

				echo json_encode($reply);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Some Perameters Is Missing", "ack_msg" => "Failed! Route Insert Failed.");
				echo json_encode($reply);
			}
		} else if ($service == "get_master_route" || $service == 182) {

			$ctable 	= "master_route";
			// $id = $_REQUEST['id'];
			$sales_id = $_REQUEST['sales_id'];
			$date = $_REQUEST['date'];

			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$limit = $objDispatch->getLimit($limit);

			$Where = "isDelete=0 AND isActive=1";

			if ($sales_id != "") {
				$Where .= " AND sales_id='" . $sales_id . "'";
			}

			$ctable_r = $db->rp_getData($ctable, "*", $Where, "id DESC", 0, $limit);

			$route = array();
			if ($ctable_r) {
				while ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
					$cureentdate = date('Y-m-d');

					if ($cureentdate <= date('Y-m-d', strtotime($ctable_d['end_date']))) {
						$ctable_d['color'] = "#B6DDB6";
						$ctable_d['isexpier'] = "0";
					} else {
						$ctable_d['color'] = "#DDB6B6";
						$ctable_d['isexpier'] = "1";
					}
					$ctable_d['start_date'] = date('d-m-Y', strtotime($ctable_d['start_date']));
					$ctable_d['end_date'] = date('d-m-Y', strtotime($ctable_d['end_date']));

					$ctable_d['class_name'] = $db->rp_getValue("class", "name", "id=" . $ctable_d['class_id']);
					$ctable_d['area_name'] = $db->rp_getValue("area", "name", "id=" . $ctable_d['area_id']);
					$ctable_d['main_city_name'] = $db->rp_getValue("city", "name", "id=" . $ctable_d['main_city_id']);

					$route[] = $ctable_d;
				}
				$ack = array("ack" => 1, "ack_msg" => "master route Found", "result" => $route);
				echo json_encode($ack);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No master route found!!");
				echo json_encode($ack);
			}
		} else if ($service == 'add_master_route' || $service == 183) {
			if (isset($_REQUEST['sales_id']) && isset($_REQUEST['start_date'])  && isset($_REQUEST['end_date']) && isset($_REQUEST['class_id']) && isset($_REQUEST['area_id'])) {

				$detail['sales_id']      = isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
				$detail['start_date']            = isset($_REQUEST['start_date']) ? date("Y-m-d", strtotime($_REQUEST['start_date'])) : "";
				$detail['end_date']            = isset($_REQUEST['end_date']) ? date("Y-m-d", strtotime($_REQUEST['end_date'])) : "";

				$detail['class_id']        = isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
				$detail['area_id']         = isset($_REQUEST['area_id']) ? $db->clean($_REQUEST['area_id']) : "";
				$detail['main_city_id']         = isset($_REQUEST['main_city_id']) ? $db->clean($_REQUEST['main_city_id']) : "";

				$reply = $objExecutive->InsertMasterRoute($detail);
				if ($reply['ack'] == 1) {
					$routeR = $db->rp_getData("master_route", "*", "id='" . $reply['id'] . "'");
					$routeD = mysqli_fetch_assoc($routeR);

					$routeD['start_date'] = date('d m Y', strtotime($routeD['start_date']));
					$routeD['end_date'] = date('d m Y', strtotime($routeD['end_date']));

					$reply = array("ack" => 1, "developer_msg" => $reply['developer_msg'], "ack_msg" => $reply['ack_msg'], "result" => $routeD);
				} else {
					$reply = array("ack" => 0, "developer_msg" => $reply['developer_msg'], "ack_msg" => $reply['ack_msg']);
				}

				echo json_encode($reply);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Some Perameters Is Missing", "ack_msg" => "Failed! Route Insert Failed.");
				echo json_encode($reply);
			}
		} else if ($service == 'get_zone' || $service == 181) {
			$result = array();
			$zone_data = array();
			$zone_r = $db->rp_getData("zone", "*", "isDelete=0", 0);
			// get data for single table only 
			if ($zone_r) {
				while ($zone_d = mysqli_fetch_assoc($zone_r)) {
					$zone_data[] = $zone_d;
				}
			}

			if (!empty($zone_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $zone_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_status' || $service == 184) {

			$status = array("0" => "Generate", "2" => "Positive", "1" => "In Followup", "4" => "Hot", "5" => "Cold", "6" => "Warm", "-1" => "My Work", "3" => "Buy Later", "-2" => "Cancel", "11" => "Lost");

			$Data = array();
			foreach ($status as $SK => $SV) {
				$a = array();
				$a['status_id'] = $SK;
				$a['name'] = $SV;
				$Data[] = $a;
			}
			$ack = array("ack" => 1, "ack_msg" => "Status Get Successfully!!", "developer_msg" => "Status Get Successfully!!", "result" => $Data);
			$db->printJSON($ack);
		} else if ($service == 'add_customer_manual_stock' || $service == 185) {

			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$product_id = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : "";
			$weight_id = isset($_REQUEST['weight_id']) ? $_REQUEST['weight_id'] : "";
			$p_name = isset($_REQUEST['p_name']) ? $_REQUEST['p_name'] : "";
			$pro_qty = isset($_REQUEST['pro_qty']) ? $_REQUEST['pro_qty'] : "";
			$planning_date = isset($_REQUEST['planning_date']) ? date('Y-m-d', strtotime($_REQUEST['planning_date'])) : "";
			$remark = isset($_REQUEST['remark']) ? $_REQUEST['remark'] : "";
			$expiry_date = isset($_REQUEST['expiry_date']) ? date('Y-m-d', strtotime($_REQUEST['expiry_date'])) : "";
			// $expiry_date=isset($_REQUEST['expiry_date'])?$_REQUEST['expiry_date']:"";
			if ($customer_id != ""  && $product_id != "" && $weight_id != "" && $p_name != "" && $pro_qty != "" && $planning_date != "" && $remark != "" && $expiry_date != "") {
				$insert = $db->rp_insert("customer_inward_stock", array($product_id, $weight_id, $p_name, $pro_qty, $planning_date, $remark, $expiry_date, $customer_id, $sales_id), array("pro_id", "weight_id", "pro_name", "pro_qty", "planning_date", "remark", "expiry_date", "customer_id", "sales_id"), 0);
				if ($insert) {
					$ack = array("ack" => 1, "ack_msg" => "Data Added Successfully", "developer_msg" => "Data Added Successfully!!");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Data Added Failed", "developer_msg" => "Data Added Failed!!");
				}
			} else {
				// echo "test";exit;
				$ack = array("ack" => 0, "ack_msg" => "All Fields Are Required", "developer_msg" => "All Fields Are Required!!");
			}

			$db->printJSON($ack);
		} else if ($service == 'get_customer_manual_stock' || $service == 186) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$limit = $objDispatch->getLimit($limit);
			$result = array();
			$customer_inward_stock_data = array();
			$where = "isDelete=0";
			if ($sales_id != "") {
				$where .= " AND sales_id=" . $sales_id;
			} else if ($customer_id != "") {
				$where .= " AND customer_id=" . $customer_id;
			}

			$customer_inward_stock_r = $db->rp_getData("customer_inward_stock", "*", $where, "id DESC", 0, $limit);
			// get data for single table only 
			if ($customer_inward_stock_r) {
				while ($customer_inward_stock_d = mysqli_fetch_assoc($customer_inward_stock_r)) {
					$customer_inward_stock_d['product_name'] = $customer_inward_stock_d['pro_name'] . " - " . $db->rp_getValue("product_weight_price", "catno", "product_id='" . $customer_inward_stock_d['pro_id'] . "'");
					$customer_inward_stock_d['customer_name'] = $db->rp_getValue("executive", "company_name", "id='" . $customer_inward_stock_d['customer_id'] . "'");
					$customer_inward_stock_d['planning_date'] = date('d-m-Y', strtotime($customer_inward_stock_d['planning_date']));
					$customer_inward_stock_d['expiry_date'] = date('d-m-Y', strtotime($customer_inward_stock_d['expiry_date']));
					$customer_inward_stock_data[] = $customer_inward_stock_d;
				}
			}

			if (!empty($customer_inward_stock_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $customer_inward_stock_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_deep_freezer_scheme' || $service == 187) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";

			$where = "isDelete=0";
			if ($sales_id != "") {
				$where .= " AND sales_id=" . $sales_id;
			}
			if ($customer_id != "") {
				$where .= " AND customer_id=" . $customer_id;
			}

			$limit = $objDispatch->getLimit($limit);
			$deep_freezer_scheme_data = array();


			$deep_freezer_scheme_r = $db->rp_getData("freezer_scheme", "id,customer_id,serial_no,shop_name,contact_person,mobile_no,address,taluka,district,state,distributor_agency,center,freeze_model_no,hard_top,class_top,dealer_name,dealer_mo,dealer_sign,distributor_name,distributor_mob,distributor_sign,company_office_name,company_office_mob,company_office_sign,image_path,executive_type,dealer_sign_image,distributor_sign_image,company_office_sign_image,payment,language,created_date,isDelete,isActive,created_by,created_by_type,modified_by,modified_by_type,modified_date", $where, "id DESC", 0, $limit);
			// get data for single table only 
			if ($deep_freezer_scheme_r) {
				while ($deep_freezer_scheme_d = mysqli_fetch_assoc($deep_freezer_scheme_r)) {
					$customer_name = $db->rp_getValue("executive", "cname", "id='" . $deep_freezer_scheme_d['customer_id'] . "'");
					$deep_freezer_scheme_d['customer_name'] = $customer_name;


					$distributor_agency_name = $db->rp_getValue("executive", "company_name", "id='" . $deep_freezer_scheme_d['distributor_agency'] . "'");
					$deep_freezer_scheme_d['distributor_agency_name'] = $distributor_agency_name;





					$executive_type_name = $db->rp_getValue("customer_type", "name", "id='" . $deep_freezer_scheme_d['executive_type'] . "'");

					$deep_freezer_scheme_d['executive_type_name'] = $executive_type_name;


					if ($deep_freezer_scheme_d['created_date'] != "") {

						$deep_freezer_scheme_d['created_date'] = date('d m Y H:i A', strtotime($deep_freezer_scheme_d['created_date']));
					} else {
						$deep_freezer_scheme_d['created_date'] = "";
					}

					if ($deep_freezer_scheme_d['modified_date'] != "") {

						$deep_freezer_scheme_d['modified_date'] = date('d m Y H:i A', strtotime($deep_freezer_scheme_d['modified_date']));
					} else {
						$deep_freezer_scheme_d['modified_date'] = "";
					}


					if ($deep_freezer_scheme_d['payment'] == "1") {
						$payment_name = "Cheque";
					} else if ($deep_freezer_scheme_d['payment'] == "2") {
						$payment_name = "RTGS";
					} else {
						$payment_name = "";
					}
					$deep_freezer_scheme_d['payment_name'] = $payment_name;


					if ($deep_freezer_scheme_d['language'] == "1") {
						$language_name = "Gujarati";
					} else if ($deep_freezer_scheme_d['language'] == "2") {
						$language_name = "Hindi";
					} else if ($deep_freezer_scheme_d['language'] == "3") {
						$language_name = "English";
					} else {
						$language_name = "";
					}
					$deep_freezer_scheme_d['language_name'] = $language_name;



					$deep_freezer_scheme_d['distributor_sign_image'] = SITEURL . DISTRIBUTOR_PHOTO_SIGN . $deep_freezer_scheme_d['distributor_sign_image'];

					$deep_freezer_scheme_d['dealer_sign_image'] = SITEURL . DEALER_PHOTO_SIGN . $deep_freezer_scheme_d['dealer_sign_image'];

					$deep_freezer_scheme_d['company_office_sign_image'] = SITEURL . COMPANY_OFFICE_PHOTO_SIGN . $deep_freezer_scheme_d['company_office_sign_image'];




					$deep_freezer_scheme_data[] = $deep_freezer_scheme_d;
				}
			}

			if (!empty($deep_freezer_scheme_data)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $deep_freezer_scheme_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_scheme_items' || $service == 188) {
			$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : "";

			if ($order_id != "") {
				$get_scheme_data_r = $db->rp_getData("order_scheme_items", "weight_id,pro_id", "isDelete=0 AND order_id='" . $order_id . "'", "", 0);
				if (mysqli_num_rows($get_scheme_data_r) > 0) {

					$data = array();
					while ($get_scheme_data_d = mysqli_fetch_assoc($get_scheme_data_r)) {
						$data['product_name'] = $db->rp_getValue("weight", "name", "id='" . $get_scheme_data_d['weight_id'] . "' AND isDelete=0", 0) . '-' . $db->rp_getValue("product", "name", "isDelete=0 AND id='" . $get_scheme_data_d['pro_id'] . "'", 0);
						$data['hsn_code'] = $db->rp_getValue("product", "hsn_code", "isDelete=0 AND id='" . $get_scheme_data_d['pro_id'] . "'", 0);
						$data['pro_qty'] = $db->rp_getValue("order_scheme_items", "SUM(pro_qty)", "isDelete=0 AND pro_id='" . $get_scheme_data_d['pro_id'] . "' AND order_id='" . $order_id . "' AND weight_id='" . $get_scheme_data_d['weight_id'] . "'", 0);
					}

					$ack = array("ack" => 1, "ack_msg" => "Data Available", "developer_msg" => "Data Available!!", "result" => $data);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "Order Id Required!!",);
			}


			$db->printJSON($ack);
		} else if ($service == 'add_deep_freezer_scheme' || $service == 189) {
			// $Data = array();
			$detail['sales_id'] = isset($_REQUEST['sales_id']) ? trim($db->clean($_REQUEST['sales_id'])) : "";
			$detail['entry_flag'] = isset($_REQUEST['entry_flag']) ? trim($db->clean($_REQUEST['entry_flag'])) : "5";


			//$detail['serial_no']=isset($_REQUEST['serial_no'])?trim($db->clean($_REQUEST['serial_no'])):"";
			$detail['shop_name'] = isset($_REQUEST['shop_name']) ? trim($db->clean($_REQUEST['shop_name'])) : "";
			$detail['contact_person'] = isset($_REQUEST['contact_person']) ? trim($db->clean($_REQUEST['contact_person'])) : "";
			$detail['mobile_no'] = isset($_REQUEST['mobile_no']) ? trim($db->clean($_REQUEST['mobile_no'])) : "";
			$detail['address'] = isset($_REQUEST['address']) ? trim($db->clean($_REQUEST['address'])) : "";
			$detail['taluka'] = isset($_REQUEST['taluka']) ? trim($db->clean($_REQUEST['taluka'])) : "";
			$detail['district'] = isset($_REQUEST['district']) ? trim($db->clean($_REQUEST['district'])) : "";
			$detail['state'] = isset($_REQUEST['state']) ? trim($db->clean($_REQUEST['state'])) : "";
			$detail['distributor_agency'] = isset($_REQUEST['distributor_agency']) ? trim($db->clean($_REQUEST['distributor_agency'])) : "";
			$detail['center'] = isset($_REQUEST['center']) ? trim($db->clean($_REQUEST['center'])) : "";
			$detail['freeze_model_no'] = isset($_REQUEST['freeze_model_no']) ? trim($db->clean($_REQUEST['freeze_model_no'])) : "";
			$detail['hard_top'] = isset($_REQUEST['hard_top']) ? trim($db->clean($_REQUEST['hard_top'])) : "";
			$detail['class_top'] = isset($_REQUEST['class_top']) ? trim($db->clean($_REQUEST['class_top'])) : "";

			$detail['dealer_name'] = isset($_REQUEST['dealer_name']) ? trim($db->clean($_REQUEST['dealer_name'])) : "";
			$detail['dealer_mo'] = isset($_REQUEST['dealer_mo']) ? trim($db->clean($_REQUEST['dealer_mo'])) : "";
			$detail['dealer_sign'] = isset($_REQUEST['dealer_sign']) ? trim($db->clean($_REQUEST['dealer_sign'])) : "";
			$detail['distributor_name'] = isset($_REQUEST['distributor_name']) ? trim($db->clean($_REQUEST['distributor_name'])) : "";
			$detail['distributor_mob'] = isset($_REQUEST['distributor_mob']) ? trim($db->clean($_REQUEST['distributor_mob'])) : "";
			$detail['distributor_sign'] = isset($_REQUEST['distributor_sign']) ? trim($db->clean($_REQUEST['distributor_sign'])) : "";
			$detail['company_office_name'] = isset($_REQUEST['company_office_name']) ? trim($db->clean($_REQUEST['company_office_name'])) : "";
			$detail['company_office_mob'] = isset($_REQUEST['company_office_mob']) ? trim($db->clean($_REQUEST['company_office_mob'])) : "";
			$detail['company_office_sign'] = isset($_REQUEST['company_office_sign']) ? trim($db->clean($_REQUEST['company_office_sign'])) : "";
			// $detail['image_path']=isset($_REQUEST['image_path'])?trim($Application->clean($_REQUEST['image_path'])):"";
			$detail['executive_type'] = isset($_REQUEST['executive_type']) ? trim($db->clean($_REQUEST['executive_type'])) : "";
			$detail['payment'] = isset($_REQUEST['payment']) ? trim($db->clean($_REQUEST['payment'])) : "";
			$detail['language'] = isset($_REQUEST['language']) ? trim($db->clean($_REQUEST['language'])) : "";
			$detail['customer_id'] = isset($_REQUEST['customer_id']) ? trim($db->clean($_REQUEST['customer_id'])) : "";
			$detail['utr'] = isset($_REQUEST['utr']) ? trim($db->clean($_REQUEST['utr'])) : "";


			$reply = $objFreezer->AddFreezeScheme($detail, $_FILES);
			echo json_encode($reply);
		} else if ($service == 'get_language' || $service == 190) {

			$language = array("1" => "Gujarati", "2" => "Hindi", "3" => "English");
			$language_data = array("1" => TERMS_CONDITION_GUJRATI, "2" => TERMS_CONDITION_HINDI, "3" => TERMS_CONDITION_ENGLISH);

			$Data = array();
			foreach ($language as $SK => $SV) {
				$a = array();
				$a['id'] = $SK;
				$a['name'] = $SV;
				$a['data'] = $language_data[$SK];
				$Data[] = $a;
			}
			$ack = array("ack" => 1, "ack_msg" => "Get Successfully!!", "developer_msg" => "Status Get Successfully!!", "result" => $Data);
			$db->printJSON($ack);
		} else if ($service == 'add_deep_freezer_scheme_attachmnet' || $service == 191) {

			$detail['id'] = isset($_REQUEST['id']) ? trim($db->clean($_REQUEST['id'])) : "";

			$detail['dealer_name'] = isset($_REQUEST['dealer_name']) ? trim($db->clean($_REQUEST['dealer_name'])) : "";
			$detail['dealer_mo'] = isset($_REQUEST['dealer_mo']) ? trim($db->clean($_REQUEST['dealer_mo'])) : "";
			$detail['dealer_sign'] = isset($_REQUEST['dealer_sign']) ? trim($db->clean($_REQUEST['dealer_sign'])) : "";
			$detail['distributor_name'] = isset($_REQUEST['distributor_name']) ? trim($db->clean($_REQUEST['distributor_name'])) : "";
			$detail['distributor_mob'] = isset($_REQUEST['distributor_mob']) ? trim($db->clean($_REQUEST['distributor_mob'])) : "";
			$detail['distributor_sign'] = isset($_REQUEST['distributor_sign']) ? trim($db->clean($_REQUEST['distributor_sign'])) : "";
			$detail['company_office_name'] = isset($_REQUEST['company_office_name']) ? trim($db->clean($_REQUEST['company_office_name'])) : "";
			$detail['company_office_mob'] = isset($_REQUEST['company_office_mob']) ? trim($db->clean($_REQUEST['company_office_mob'])) : "";
			$detail['company_office_sign'] = isset($_REQUEST['company_office_sign']) ? trim($db->clean($_REQUEST['company_office_sign'])) : "";
			$detail['type'] = isset($_REQUEST['type']) ? trim($db->clean($_REQUEST['type'])) : "";


			$reply = $objFreezer->AddFreezeSchemeImagesOtherData($detail, $_FILES, $_FILES);

			if ($reply['ack'] == 1) {
				$reply = array("ack" => 1, "developer_msg" => $reply['developer_msg'], "ack_msg" => $reply['ack_msg']);
			} else {
				$reply = array("ack" => 0, "developer_msg" => $reply['developer_msg'], "ack_msg" => $reply['ack_msg']);
			}

			echo json_encode($reply);
		} else if ($service == "download_deepfreezer_pdf" || $service == 192) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";

			if (isset($_REQUEST['id']) && $_REQUEST['id'] != "") {
				$id	= isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				if (!empty($id) && $id != "") {
					$ack = $objFreezer->DownloadDeepFreezer($id);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
				}
				$db->printJSON($ack);
			}
		} else if ($service == 'get_city_for_rout' || $service == 194) {
			$detail = array();
			$ack = $system->getAllCityDetail(array("id", "name", "state_id")); //id
			$db->printJSON($ack);
		} else if ($service == 'get_company_master' || $service == 199) {


			$sales_id = (isset($_REQUEST['sales_id'])) ? $_REQUEST['sales_id'] : "";

			if ($sales_id != "" && $sales_id != 0) {
				$get_top_ids = $db->rp_getValue("sales_executive", "type_of_company", "isDelete=0 AND id='" . $sales_id . "'", 0);
				$sales_where .= " AND id IN(" . $get_top_ids . ")";
			} else {
				$sales_where .= "";
			}

			$company_r = $db->rp_getData("company_master", "*", "isDelete=0" . $sales_where, "id ASC", 0);
			while ($company_d = mysqli_fetch_assoc($company_r)) {

				$data[] = $company_d;
			}
			if (!empty($data)) {
				$ack = array("ack" => 1, "ack_msg" => "Data Get Successfully!!", "developer_msg" => "Data Get Successfully!!", "result" => $data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Found!!", "developer_msg" => "No Data Found!!");
			}
			$db->printJSON($ack);
		} else if ($service == 'get_purpose_master' || $service == 200) {

			$purpose_master_r = $db->rp_getData("purpose_master", "*", "isDelete=0 ", "", 0, $limit);

			// get data for single table only 
			if ($purpose_master_r) {
				while ($purpose_master_d = mysqli_fetch_assoc($purpose_master_r)) {
					$purpose_master_data[] = $purpose_master_d;
				}
				$ack = array("ack" => 1, "ack_msg" => "Status Get Successfully!!", "developer_msg" => "Status Get Successfully!!", "result" => $purpose_master_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No data found", "developer_msg" => "No data found");
			}
			$db->printJSON($ack);
		} else if ($service == 'get_terms_and_condition' || $service == 201) {

			$terms_condition_r = $db->rp_getData("terms_condition", "*", "isDelete=0", "", 0, $limit);

			// get data for single table only 
			if ($terms_condition_r) {
				while ($terms_condition_d = mysqli_fetch_assoc($terms_condition_r)) {
					$terms_condition_data[] = $terms_condition_d;
				}
				$ack = array("ack" => 1, "ack_msg" => "Terms Condition Get Successfully!!", "developer_msg" => "Terms Condition Get Successfully!!", "result" => $terms_condition_data);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No data found", "developer_msg" => "No data found");
			}
			$db->printJSON($ack);
		} else if ($service == "get_product_varient" || $service == 202) {
			/*if($_REQUEST['searchName']!=""  && isset($_REQUEST['searchName']))
			{*/
			$limit = array();
			$limit['ul'] = isset($_REQUEST['ul']) ? $_REQUEST['ul'] : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $_REQUEST['ll'] : "";
			$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";

			if ($uid != "") {
				$price_list_id = $db->rp_getValue("executive", "price_list_id", "id='" . $uid . "' AND isDelete=0");
			} else {
				$price_list_id = 0;
			}
			$limit = $objProduct->getLimit($limit);
			$result = array();
			if ($_REQUEST['searchName'] != "" && isset($_REQUEST['searchName'])) {
				$PROIDS = array();
				$where = "";
				/*$pro_r1=$db->rp_getData("product_weight_price","product_id","catno LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
					if($pro_r1)
					{
						while($pro_d1=mysql_fetch_assoc($pro_r1))
						{
							$PROIDS[]=$pro_d1['product_id'];
						}
					}
					if(!empty($PROIDS))
					{
						$PROIDS=implode(",", $PROIDS);
						$where=" OR id IN (".$PROIDS."))";
					}*/

				$proids_array = $db->getCommaSepretedData("product_weight_price", "*", $_REQUEST['searchName'], "catno");

				if ($proids_array != "") {
					$where = "(name LIKE '%" . $_REQUEST['searchName'] . "%' OR id IN (" . $proids_array . ")) AND isDelete=0 AND isActive=1";
				} else {
					$where = "name LIKE '%" . $_REQUEST['searchName'] . "%' AND isDelete=0 AND isActive=1";
				}
				//echo $a;exit();

				$pro_r = $db->rp_getData("product", "*", $where, "", 0, $limit);
			} else {
				$pro_r = $db->rp_getData("product", "*", "isDelete=0 AND isActive=1", "", 0, $limit);
			}
			if ($pro_r) {
				// echo "fs";
				while ($pro_d = mysqli_fetch_assoc($pro_r)) {
					// print_r($pro_d);die;
					$pro_d['cat_name'] = $db->rp_getValue("category_master", "name", "id='" . $pro_d['cid'] . "' AND isDelete=0", 0);
					$pro_d['top_cat_name'] = $db->rp_getValue("top_category_master", "name", "id='" . $pro_d['tcid'] . "' AND isDelete=0", 0);
					$pro_d['product_code'] = $db->rp_getValue("product_weight_price", "catno", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);

					$pro_d['unitname'] = $db->rp_getValue("unit", "name", "id='" . $pro_d['unit_id'] . "' AND isDelete=0", 0);
					//$pro_d['unit_id']="1";
					$pro_d['total_size'] = $db->rp_getTotalRecord("product_weight_price", "product_id='" . $pro_d['id'] . "' AND isDelete=0", 0);
					$descr = html_entity_decode($pro_d['descr']);
					$descr = strip_tags($descr);
					// $descr=str_replace("\r\n","",$descr);
					// $descr=str_replace(",",",<br/>",$descr);
					$descr = nl2br($descr);
					$pro_d['descr'] = $descr;
					$pid = $pro_d['id'];
					if ($pro_d['image_path'] != "") {
						$pro_d['image_path'] = SITEURL . PRODUCT . $pro_d['image_path'];
					}

					$price_r =	$db->rp_getData("product_weight_price", "id,weight_id,price,product_id,inner_size,outer_size,stock_qty,catno", "isDelete=0 AND product_id=" . $pid, "", 0);

					if ($price_r) {
						$product_weight_price = array();
						while ($price_d = mysqli_fetch_assoc($price_r)) {
							$price_d['top_cat_name'] = $pro_d['top_cat_name'];
							$price_d['cat_name'] = $pro_d['cat_name'];
							$price_d['product_name'] = $pro_d['name'];
							$price_d['unit_id'] = $pro_d['unit_id'];
							$price_d['product_code'] = $pro_d['product_code'];
							$price_d['image_path'] = $pro_d['image_path'];

							$price_d['original_price'] = $db->rp_number_format($price_d['price'], 2);

							$price_d['price'] = $db->rp_number_format($price_d['price'], 2);
							$price_d['discount'] = 0;
							$price_d['discounted_amount'] = 0;
							if ($price_list_id != 0) {
								$check_product_in_list = $db->rp_getTotalRecord("product_price_list", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'", 0);
								if ($check_product_in_list > 0) {
									$price_d['price'] = $db->rp_number_format($db->rp_getValue("product_price_list", "discounted_price", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);

									$price_d['discount'] = $db->rp_number_format($db->rp_getValue("product_price_list", "discount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "' AND price_list_id='" . $price_list_id . "'"), 2);

									$price_d['discounted_amount'] = $db->rp_number_format($db->rp_getValue("product_price_list", "discounted_amount", "pid='" . $price_d['product_id'] . "' AND weight_id='" . $price_d['weight_id'] . "'"), 2);
								}
							}

							$price_d['name'] = $db->rp_getValue("weight", "name", "id='" . $price_d['weight_id'] . "'");
							$price_d['display_order'] = $db->rp_getValue("weight", "display_order", "id='" . $price_d['weight_id'] . "'");
							$product_weight_price[] = $price_d;
							$objProduct->sortBy('display_order', $product_weight_price, 'asc');
							$result[] = $price_d;
						}
						$pro_d['product_weight_price'] = $product_weight_price;
						$product[] = $pro_d;
					} else {
						$pro_d['product_weight_price'] = array();
						$product[] = $pro_d;
					}
					// $result['product_weight_price']=$pro_d['product_weight_price'];
				}
			}
			if (!empty($result)) {
				$ack = array("ack" => 1, "result" => $result);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No data Avialable");
			}
			/*$ack=array("ack"=>1,"result"=>$result);*/
			echo json_encode($ack);
			/*}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}*/
		} else if ($service == "quick_order" || $service == 203) {
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$cart_type = isset($_REQUEST['cart_type']) ? $_REQUEST['cart_type'] : "";
			$customer_d = $db->rp_getData("executive", "*", "id='" . $customer_id . "' AND isDelete=0", "", 0);
			$customer_r = mysqli_fetch_assoc($customer_d);
			$order_no = $db->getLastInsertId("orders");
			$order_no = OUTLETS_ORDER_NO . str_pad($order_no, 2, '0', STR_PAD_LEFT);

			$check_cart_exist = $db->rp_getTotalRecord("cart_detail", "customer_id='" . $customer_id . "' AND isDelete=0 AND sales_id='" . $sales_id . "'", 0);


			if ($check_cart_exist > 0) {
				$ack = array("ack" => 1, "ack_msg" => "Cart is already exists", "developer_msg" => "Cart is already exists!!");
			} else {

				if ($customer_id != ""  && $sales_id != "") {

					$rows 	= array("customer_id", "dealer_id", "super_stockist_id", "customer_name", "company_name", "customer_type", "contact_number", "address", "city", "state", "country", "status", "sales_id", "transport_name", "transport_through", "packing_charge", "cart_type", "email", "type_of_company", "order_no");

					$values = array($customer_r['id'], $customer_r['dealer_distributor_id'], $customer_r['super_stockist_id'], $customer_r['cname'], $customer_r['company_name'], $customer_r['type_of_executive'], $customer_r['mobile_no1'], addslashes($customer_r['address']), $customer_r['city'], $customer_r['state'], $customer_r['country'], "-1", $sales_id, $customer_r['transporter_id'], $customer_r['transport_by_id'], "", $cart_type, $customer_r['email'], $db->clean($customer_r['type_of_company']), $order_no);

					$insert = $db->rp_insert("cart_detail", $values, $rows, 0);

					if ($insert) {
						$ack = array("ack" => 1, "ack_msg" => "Data Added Successfully", "developer_msg" => "Data Added Successfully!!");
					} else {
						$ack = array("ack" => 0, "ack_msg" => "Data Added Failed", "developer_msg" => "Data Added Failed!!");
					}
				} else {
					$ack = array("ack" => 0, "ack_msg" => "All Fields Are Required", "developer_msg" => "All Fields Are Required!!");
				}
			}
			$db->printJSON($ack);
		} else if ($service == "delete_master_route" || $service == 204) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$master_rout_id = isset($_REQUEST['master_rout_id']) ? $_REQUEST['master_rout_id'] : "";

			if ($sales_id != "" && $master_rout_id != "") {

				$check_my_rout = $db->rp_dupCheck("my_route", "sales_id='" . $sales_id . "' AND route_id='" . $master_rout_id . "' AND isDelete=0", 0);

				if ($check_my_rout) {
					$ack = array("ack" => 0, "ack_msg" => "Route Already Available", "developer_msg" => "Not Deleted!!");
				} else {
					$rout_row 	= array("isDelete" => 1);
					$where = "sales_id='" . $sales_id . "' AND id='" . $master_rout_id . "'";

					$update_my_route = $db->rp_update("master_route", $rout_row, $where);

					if ($update_my_route) {
						$ack = array("ack" => 1, "ack_msg" => "Master Route Delete Successfully", "developer_msg" => "Master Route Delete Successfully!!");
					} else {
						$ack = array("ack" => 0, "ack_msg" => "Master Route Delete Failed", "developer_msg" => "Master Route Delete Failed!!");
					}
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "All Fields Are Required", "developer_msg" => "All Fields Are Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "delete_multiple_my_route" || $service == 205) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$master_rout_id = isset($_REQUEST['master_rout_id']) ? $_REQUEST['master_rout_id'] : "";
			$my_route_id = isset($_REQUEST['my_route_ids']) ? $_REQUEST['my_route_ids'] : "";
			$my_route_ids = rtrim($my_route_id, ',');

			$customer_id = isset($_REQUEST['customer_ids']) ? $_REQUEST['customer_ids'] : "";
			$customer_ids = rtrim($customer_id, ',');

			if ($sales_id != "" && $master_rout_id != "" && $my_route_ids != "") {
				$rout_row 	= array("isDelete" => 1);
				$where = "sales_id='" . $sales_id . "' AND route_id='" . $master_rout_id . "' AND id IN(" . $my_route_ids . ") AND customer_id IN(" . $customer_ids . ")";

				$update_my_route = $db->rp_update("my_route", $rout_row, $where, 0);

				if ($update_my_route) {
					$ack = array("ack" => 1, "ack_msg" => "My Route Delete Successfully", "developer_msg" => "My Route Delete Successfully!!");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "My Route Delete Failed", "developer_msg" => "My Route Delete Failed!!");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "All Fields Are Required", "developer_msg" => "All Fields Are Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "order_brand_item" || $service == 206) {
			$orderItemBrand_R = $db->rp_getData("order_item_brand_master", "id,name", "isDelete=0 AND isActive=1");
			if ($orderItemBrand_R) {
				while ($orderItemBrand_D = mysqli_fetch_assoc($orderItemBrand_R)) {
					$orderItemBrand_ARR[] = $orderItemBrand_D;
				}
				$ack = array("ack" => 1, "ack_msg" => "Data fetched", "result" => $orderItemBrand_ARR);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Data fetch Failed");
			}
			$db->printJSON($ack);
		} else if ($service == "add_customer_phone_no" || $service == 208) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : "";
			$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
			$ref_table = isset($_REQUEST['ref_table']) ? $_REQUEST['ref_table'] : "";

			if ($customer_id != "") {
				$rows = array("customer_id", "phone_no", "name", "ref_table");
				$values = array($customer_id, $phone, $name, $ref_table);


				$insert = $db->rp_insert('customer_vs_phone_no', $values, $rows, 0);

				if ($insert) {
					$ack = array("ack" => 1, "ack_msg" => "Phone No Add Successfully", "developer_msg" => "Phone No Add  Successfully!!");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Phone No Add Failed", "developer_msg" => "Phone No Add Failed!!");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Customer Id Required", "developer_msg" => "Customer Id Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "get_customer_phone_no" || $service == 209) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$ref_table = isset($_REQUEST['ref_table']) ? $_REQUEST['ref_table'] : "";

			if ($customer_id != "") {
				$phone_data_r = $db->rp_getData("customer_vs_phone_no", "*", "isDelete=0 AND isActive=1 AND customer_id='" . $customer_id . "' AND ref_table='" . $ref_table . "'", "", 0);
				if ($phone_data_r) {
					while ($phone_data_d = mysqli_fetch_assoc($phone_data_r)) {
						$phone_data[] = $phone_data_d;
					}
					$ack = array("ack" => 1, "ack_msg" => "Data fetched", "result" => $phone_data);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Data fetch Failed");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Customer Id Required", "developer_msg" => "Customer Id Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "delete_customer_phone_no" || $service == 210) {
			$table_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";

			if ($customer_id != "") {
				$rows 	= array("isDelete" => 1,);
				$where	= "id='" . $table_id . "' AND customer_id='" . $customer_id . "'";
				$isUpdated = $db->rp_update("customer_vs_phone_no", $rows, $where, 0);

				if ($isUpdated) {

					$ack = array("ack" => 1, "ack_msg" => "Phone No Delete Successfully", "developer_msg" => "Phone No Delete  Successfully!!");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Phone No Delete Failed");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Customer Id Required", "developer_msg" => "Customer Id Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "delete_customer_phone_no" || $service == 210) {
			$table_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";

			if ($customer_id != "") {
				$rows 	= array("isDelete" => 1,);
				$where	= "id='" . $table_id . "' AND customer_id='" . $customer_id . "'";
				$isUpdated = $db->rp_update("customer_vs_phone_no", $rows, $where, 0);

				if ($isUpdated) {

					$ack = array("ack" => 1, "ack_msg" => "Phone No Delete Successfully", "developer_msg" => "Phone No Delete  Successfully!!");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Phone No Delete Failed");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Customer Id Required", "developer_msg" => "Customer Id Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "change_inquiry_status" || $service == 211) {
			$inquiry_id = isset($_REQUEST['inquiry_id']) ? $_REQUEST['inquiry_id'] : "";

			if ($inquiry_id != "") {
				$rows 	= array("status" => "-1",);
				$where	= "id = " . $inquiry_id;
				$isUpdated = $db->rp_update("no_order_inquiry", $rows, $where, 0);

				if ($isUpdated) {
					$db->addStatusTimelineEntry($inquiry_id, '-1', $_REQUEST['sales_id']);
					$ack = array("ack" => 1, "ack_msg" => "Status Change Successfully", "developer_msg" => "Status Change  Successfully!!");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Status Change Failed");
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Inquiry Id Required", "developer_msg" => "Inquiry Id Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "today_work_count" || $service == 212) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$type_of_company = isset($_REQUEST['type_of_company']) ? $_REQUEST['type_of_company'] : "";
			$where = "";
			$data = array();
			if ($sales_id != "" || $type_of_company = "") {
				$today_date = date('Y-m-d');
				//$where .= "inquiry_assign_to='".$sales_id."' AND  isDelete=0 AND inquiry_date <= '".$today_date."' AND ";
				if ($type_of_company != "") {
					$where .= "type_of_company='" . $type_of_company . "' AND  isDelete=0 AND inquiry_date <= '" . $today_date . "' AND status=-1 AND (inquiry_created_by='" . $sales_id . "' OR inquiry_assign_to='" . $sales_id . "') AND";
				} else {
					$where .= " isDelete=0 AND inquiry_date <= '" . $today_date . "' AND status=-1 AND (inquiry_created_by='" . $sales_id . "' OR inquiry_assign_to='" . $sales_id . "') AND";
				}
				$data['raw_data_count'] = "" . $db->rp_getTotalRecord("no_order_inquiry", $where . " inquiry_lead_flag=-1", 0) . "";
				$data['inquiry_data_count'] = "" . $db->rp_getTotalRecord("no_order_inquiry", $where . " inquiry_lead_flag=0", 0) . "";
				$data['lead_data_count'] = "" . $db->rp_getTotalRecord("no_order_inquiry", $where . " inquiry_lead_flag=1", 0) . "";


				//$data['folloup_count']="".$db->rp_getTotalRecord("followup","followup_date <='".$today_date."' AND response='' AND user_id='".$sales_id."'",0)."";
				// $today_date=date('Y-m-d');

				$folloup_count_r = $db->rp_getData("followup", "*", "DATE(followup_date) <='" . $today_date . "' AND user_id='" . $sales_id . "' AND response='' ", "", 0);
				$folloup_count_ids = array();
				while ($folloup_count_d = mysqli_fetch_assoc($folloup_count_r)) {
					$type_of_company_r = $db->rp_getValue("executive", "type_of_company", "isDelete=0 AND id='" . ($folloup_count_d['reference_id'] or $folloup_count_d['visitor_id']) . "'", 0);
					if ($type_of_company_r == $type_of_company) {

						if ($folloup_count_d['reference_id'] == 0) {
							// echo "hello";exit;	
							$folloup_count_ids[] = $folloup_count_d['reference_id'];
						} else {
							// echo "else";exit;
							$folloup_count_ids[] = $folloup_count_d['visitor_id'];
						}
						//$folloup_count_ids[]=$folloup_count_d['visitor_id'];

					} else {
						$folloup_count_ids[] = $folloup_count_d['id'];
					}
				}
				$data['folloup_count'] = "" . count($folloup_count_ids) . "";

				$ctable_r = $db->rp_getData("master_route", "*", "sales_id='" . $sales_id . "' AND (start_date <= '" . $today_date . "' AND (end_date >='" . $today_date . "'))", "id DESC", 0);
				$route = array();
				if ($ctable_r) {
					while ($ctable_d = mysqli_fetch_assoc($ctable_r)) {
						$ctable_d['start_date'] = date('d-m-Y', strtotime($ctable_d['start_date']));
						$ctable_d['end_date'] = date('d-m-Y', strtotime($ctable_d['end_date']));
						$route[] = $ctable_d;
					}
				}

				$data['my_route_coute'] = "" . $db->rp_getTotalRecord("master_route", "sales_id='" . $sales_id . "' AND (start_date <= '" . $today_date . "' OR end_date <='" . $today_date . "')") . "";

				$dat1[] = $data;


				$ack = array("ack" => 1, "ack_msg" => "Count Found", "result" => $dat1, "route" => $route);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Sales Id Required", "developer_msg" => "Sales Id Required!!");
			}
			$db->printJSON($ack);
		} else if ($service == "add_customer_vs_plan" || $service == 214) {
			$sales_executive_id = isset($_REQUEST['sales_executive_id']) ? $_REQUEST['sales_executive_id'] : "";
			$executive_id = isset($_REQUEST['executive_id']) ? $_REQUEST['executive_id'] : "";
			$expended_order_amount = isset($_REQUEST['expended_order_amount']) ? $_REQUEST['expended_order_amount'] : "";
			$plan_type = isset($_REQUEST['plan_type']) ? $_REQUEST['plan_type'] : "";
			$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : "";
			$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : "";
			$archived_order_amount = isset($_REQUEST['archived_order_amount']) ? $_REQUEST['archived_order_amount'] : "";
			$archived_visit = isset($_REQUEST['archived_visit']) ? $_REQUEST['archived_visit'] : "";

			if ($executive_id != "" && $month != "" && $year != "" && $plan_type != "") {

				$currentYear = date('Y');
				$currentMonth = date('m');

				if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Cannot add plan for past months",
						"developer_msg" => "Attempt to add plan for past month/year"
					);
					$db->printJSON($ack);
					return;
				}

				$dupWhere = "executive_id='" . $executive_id . "' 
                    AND year='" . $year . "' 
                    AND month='" . $month . "' 
                    AND isDelete=0";

				$isDuplicate = $db->rp_dupCheck("sales_vs_plan", $dupWhere, 0);

				if ($isDuplicate) {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Customer Plan Already Exists",
						"developer_msg" => "Duplicate entry found"
					);
				} else {
					// Insert data
					$rows = array("sales_executive_id", "executive_id", "expended_order_amount", "plan_type", "year", "month");
					$values = array($sales_executive_id, $executive_id, $expended_order_amount, $plan_type, $year, $month);

					$insert = $db->rp_insert("sales_vs_plan", $values, $rows, 0);

					if ($insert) {
						$ack = array(
							"ack" => 1,
							"ack_msg" => "Customer Plan Added Successfully",
							"developer_msg" => "Insert success"
						);
					} else {
						$ack = array(
							"ack" => 0,
							"ack_msg" => "Customer Plan Add Failed",
							"developer_msg" => "Insert failed"
						);
					}
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Required Fields Are Missing",
					"developer_msg" => "executive_id / plan_type / year / month required"
				);
			}

			$db->printJSON($ack);
		} else if ($service == "update_customer_vs_plan" || $service == 215) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
			$sales_executive_id = isset($_REQUEST['sales_executive_id']) ? $_REQUEST['sales_executive_id'] : "";
			$executive_id = isset($_REQUEST['executive_id']) ? $_REQUEST['executive_id'] : "";
			$expended_order_amount = isset($_REQUEST['expended_order_amount']) ? $_REQUEST['expended_order_amount'] : "";
			$plan_type = isset($_REQUEST['plan_type']) ? $_REQUEST['plan_type'] : "";
			$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : "";
			$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : "";
			$archived_order_amount = isset($_REQUEST['archived_order_amount']) ? $_REQUEST['archived_order_amount'] : "";
			$archived_visit = isset($_REQUEST['archived_visit']) ? $_REQUEST['archived_visit'] : "";

			if ($executive_id != "" && $plan_type != "" && $year != "" && $month != "") {

				$currentYear = date('Y');
				$currentMonth = date('m');

				if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Cannot Update plan for past months",
						"developer_msg" => "Attempt to Update plan for past month/year"
					);
					$db->printJSON($ack);
					return;
				}
				// Duplicate check excluding the current ID
				$dupWhere = "executive_id='" . $executive_id . "' 
							AND plan_type='" . $plan_type . "' 
							AND year='" . $year . "' 
							AND month='" . $month . "' 
							AND id != '" . $id . "'
							AND isDelete = 0";

				$isDuplicate = $db->rp_dupCheck("sales_vs_plan", $dupWhere, 0);

				if ($isDuplicate) {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Customer Plan Already Exists",
						"developer_msg" => "Duplicate entry found"
					);
				} else {
					// Proceed with update
					$rows = array(
						"sales_executive_id"     => $sales_executive_id,
						"executive_id"           => $executive_id,
						"expended_order_amount"  => $expended_order_amount,
						"plan_type"              => $plan_type,
						"year"                   => $year,
						"month"                  => $month
					);

					$where = "id='" . $id . "'";

					$isUpdated = $db->rp_update("sales_vs_plan", $rows, $where, 0);

					if ($isUpdated) {
						$ack = array(
							"ack" => 1,
							"ack_msg" => "Customer Plan Updated Successfully",
							"developer_msg" => "Update success"
						);
					} else {
						$ack = array(
							"ack" => 0,
							"ack_msg" => "Customer Plan Update Failed",
							"developer_msg" => "Update failed"
						);
					}
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Required Fields Are Missing",
					"developer_msg" => "executive_id / plan_type / year / month required"
				);
			}

			$db->printJSON($ack);
		} else if ($service == "delete_customer_vs_plan" || $service == 216) {
			$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : "";
			$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : "";
			$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : "";
			$currentYear = date('Y');
			$currentMonth = date('m');

			if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Cannot Delete plan for past months",
					"developer_msg" => "Attempt to Delete plan for past month/year"
				);
				$db->printJSON($ack);
				return;
			}
			if ($id != "") {
				$rows = array("isDelete" => 1);
				$where = "id='" . $id . "'";
				$isUpdated = $db->rp_update("sales_vs_plan", $rows, $where, 0);

				if ($isUpdated) {
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Customer Plan Deleted Successfully",
						"developer_msg" => "Customer plan marked as deleted successfully."
					);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Customer Plan Delete Failed",
						"developer_msg" => "rp_update returned false or no rows were affected."
					);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Invalid ID",
					"developer_msg" => "ID is required and cannot be empty."
				);
			}

			$db->printJSON($ack);
		} else if ($service == "get_customer_vs_plan" || $service == 217) {
			$sales_executive_id = isset($_REQUEST['sales_executive_id']) ? trim($_REQUEST['sales_executive_id']) : "";
			$month = isset($_REQUEST['month']) ? trim($_REQUEST['month']) : "";
			$year = isset($_REQUEST['year']) ? trim($_REQUEST['year']) : "";
			$plan_type = isset($_REQUEST['plan_type']) ? trim($_REQUEST['plan_type']) : "";

			if ($sales_executive_id != "" && $month != "" && $year != "") {

				$where = "sales_executive_id='" . $sales_executive_id . "' 
                  AND month='" . $month . "' 
                  AND plan_type='" . $plan_type . "' 
                  AND year='" . $year . "' 
                  AND isDelete=0";

				$sales_vs_plan_r = $db->rp_getData("sales_vs_plan", "*", $where, "", 0);

				$sales_vs_plan_arr = array();
				if ($sales_vs_plan_r) {
					while ($row = mysqli_fetch_assoc($sales_vs_plan_r)) {
						$executive_id = $row['executive_id'];

						$excutive_name_data = $db->rp_getData("executive", "cname,company_name,type_of_executive", "isDelete=0 AND id='" . $executive_id . "'");
						if ($excutive_name_data) {
							$excutive_data_get = mysqli_fetch_assoc($excutive_name_data);
							$row['executive_name'] = $excutive_data_get['cname'];
							$row['company_name'] = $excutive_data_get['company_name'];
							$row['type_of_executive'] = $excutive_data_get['type_of_executive'];
							$sales_vs_plan_arr[] = $row;
						}
					}

					$ack = array(
						"ack" => 1,
						"ack_msg" => "Data fetched successfully",
						"result" => $sales_vs_plan_arr
					);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Data not Found",
						"result" => "Data not Found"
					);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Required fields are missing"
				);
			}

			$db->printJSON($ack);
		} else if ($service == "add_consultant_approval_process" || $service == 218) {
			$process_one_sales_executive_id = isset($_REQUEST['process_one_sales_executive_id']) ? $_REQUEST['process_one_sales_executive_id'] : "";
			$process_one_executive_id = isset($_REQUEST['process_one_executive_id']) ? $_REQUEST['process_one_executive_id'] : "";
			$process_one_approval_type = isset($_REQUEST['process_one_approval_type']) ? $_REQUEST['process_one_approval_type'] : "";

			if ($process_one_sales_executive_id != "") {
				// Insert data without duplicate check
				$rows = array("process_one_sales_executive_id", "process_one_executive_id", "process_one_approval_type");
				$values = array($process_one_sales_executive_id, $process_one_executive_id, $process_one_approval_type);

				$insert = $db->rp_insert("sales_vs_consultant_approval_process", $values, $rows, 0);

				if ($insert) {
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Consultant process added successfully",
						"insert_id" => $insert,
						"developer_msg" => "Insert success"
					);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Consultant process add failed",
						"developer_msg" => "Insert failed"
					);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Required fields are missing",
					"developer_msg" => "executive_id / approval_type required"
				);
			}

			$db->printJSON($ack);
		} else if ($service == "update_consultant_approval_process" || $service == 219) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
			$process_one_sales_executive_id = isset($_REQUEST['process_one_sales_executive_id']) ? $_REQUEST['process_one_sales_executive_id'] : "";
			$process_one_executive_id = isset($_REQUEST['process_one_executive_id']) ? $_REQUEST['process_one_executive_id'] : "";
			$process_one_approval_type = isset($_REQUEST['process_one_approval_type']) ? $_REQUEST['process_one_approval_type'] : "";

			$process_two_consultant_name = isset($_REQUEST['process_two_consultant_name']) ? $_REQUEST['process_two_consultant_name'] : "";
			$process_two_consultant_mobile = isset($_REQUEST['process_two_consultant_mobile']) ? $_REQUEST['process_two_consultant_mobile'] : "";
			$process_two_consultant_email = isset($_REQUEST['process_two_consultant_email']) ? $_REQUEST['process_two_consultant_email'] : "";

			$process_three_project_name = isset($_REQUEST['process_three_project_name']) ? $_REQUEST['process_three_project_name'] : "";
			$process_three_project_location = isset($_REQUEST['process_three_project_location']) ? $_REQUEST['process_three_project_location'] : "";

			$process_four_product_name = isset($_REQUEST['process_four_product_name']) ? $_REQUEST['process_four_product_name'] : "";
			$process_four_contractor_name = isset($_REQUEST['process_four_contractor_name']) ? $_REQUEST['process_four_contractor_name'] : "";
			$process_four_contractor_mobile = isset($_REQUEST['process_four_contractor_mobile']) ? $_REQUEST['process_four_contractor_mobile'] : "";
			$process_four_contractor_email = isset($_REQUEST['process_four_contractor_email']) ? $_REQUEST['process_four_contractor_email'] : "";
			$process_four_purchase_date = isset($_REQUEST['process_four_purchase_date']) ? date('Y-m-d', strtotime($_REQUEST['process_four_purchase_date'])) : "";



			if ($id != "") {
				// Only update non-empty fields
				$rows = array();
				if ($process_one_sales_executive_id != "") $rows["process_one_sales_executive_id"] = $process_one_sales_executive_id;
				if ($process_one_executive_id != "") $rows["process_one_executive_id"] = $process_one_executive_id;
				if ($process_one_approval_type != "") $rows["process_one_approval_type"] = $process_one_approval_type;

				if ($process_two_consultant_name != "") $rows["process_two_consultant_name"] = $process_two_consultant_name;
				if ($process_two_consultant_mobile != "") $rows["process_two_consultant_mobile"] = $process_two_consultant_mobile;
				if ($process_two_consultant_email != "") $rows["process_two_consultant_email"] = $process_two_consultant_email;

				if ($process_three_project_name != "") $rows["process_three_project_name"] = $process_three_project_name;
				if ($process_three_project_location != "") $rows["process_three_project_location"] = $process_three_project_location;

				if ($process_four_product_name != "") $rows["process_four_product_name"] = $process_four_product_name;
				if ($process_four_contractor_name != "") $rows["process_four_contractor_name"] = $process_four_contractor_name;
				if ($process_four_contractor_mobile != "") $rows["process_four_contractor_mobile"] = $process_four_contractor_mobile;
				if ($process_four_contractor_email != "") $rows["process_four_contractor_email"] = $process_four_contractor_email;
				if ($process_four_purchase_date != "") $rows["process_four_purchase_date"] = $process_four_purchase_date;

				if (!empty($rows)) {
					$where = "id='" . $id . "' AND process_one_sales_executive_id='" . $process_one_sales_executive_id . "'";
					$isUpdated = $db->rp_update("sales_vs_consultant_approval_process", $rows, $where, 0);

					if ($isUpdated) {
						$ack = array(
							"ack" => 1,
							"ack_msg" => "Consultant process Updated Successfully",
							"developer_msg" => "Update success"
						);
					} else {
						$ack = array(
							"ack" => 0,
							"ack_msg" => "Consultant process Update Failed",
							"developer_msg" => "Update failed"
						);
					}
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "No valid fields to update",
						"developer_msg" => "All fields empty"
					);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Required Fields Are Missing",
					"developer_msg" => "Required Fields Are Missing"
				);
			}

			$db->printJSON($ack);
		} else if ($service == "get_consultant_approval_process" || $service == 220) {
			$sales_executive_id = isset($_REQUEST['process_one_sales_executive_id']) ? trim($_REQUEST['process_one_sales_executive_id']) : "";
			$limit['ul'] = isset($_REQUEST['ul']) ? $db->clean($_REQUEST['ul']) : "";
			$limit['ll'] = isset($_REQUEST['ll']) ? $db->clean($_REQUEST['ll']) : "";

			$system = new System();
			$limit = $system->getLimit();

			if ($sales_executive_id != "") {


				$where = "process_one_sales_executive_id='" . $sales_executive_id . "' AND isDelete=0";

				$sales_consultant_r = $db->rp_getData("sales_vs_consultant_approval_process", "*", $where, "", 0, $limit);

				$sales_vs_plan_arr = array();
				if ($sales_consultant_r) {
					while ($row = mysqli_fetch_assoc($sales_consultant_r)) {
						$executive_id = $row['process_one_executive_id'];


						if (!empty($row['process_four_purchase_date']) && $row['process_four_purchase_date'] != '0000-00-00') {
							$row['process_four_purchase_date'] = date("d-m-Y", strtotime($row['process_four_purchase_date']));
						} else {
							$row['process_four_purchase_date'] = "";
						}


						$excutive_name_data = $db->rp_getData("executive", "cname,company_name,type_of_executive", "isDelete=0 AND id='" . $executive_id . "'", "", 0);
						if ($excutive_name_data) {
							$excutive_data_get = mysqli_fetch_assoc($excutive_name_data);
							$row['executive_name'] = $excutive_data_get['cname'];
							$row['company_name'] = $excutive_data_get['company_name'];
							$row['type_of_executive'] = $excutive_data_get['type_of_executive'];
						}

						$sales_vs_plan_arr[] = $row;
					}

					$ack = array(
						"ack" => 1,
						"ack_msg" => "Data fetched successfully",
						"result" => $sales_vs_plan_arr
					);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Data not Found",
						"result" => "Data not Found"
					);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Required fields are missing"
				);
			}

			$db->printJSON($ack);
		} else if ($service == "get_channel_partner_list" || $service == 223) {
			require_once('../include/class.channel_partner_customer.php');
			$objCP = new ChannelPartnerCustomer();
			$sales_id = '';
			if (isset($_REQUEST['sales_id']) && $_REQUEST['sales_id'] !== '') {
				$sales_id = $db->clean($_REQUEST['sales_id']);
			} else if (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] !== '') {
				$sales_id = $db->clean($_REQUEST['sales_executive_id']);
			}
			$ack = $objCP->GetChannelPartnerList(array(
				'sales_id' => $sales_id,
				'sales_executive_id' => $sales_id,
			));
			$db->printJSON($ack);
		} else if ($service == "get_channel_partner_customer_list" || $service == 224) {
			require_once('../include/class.channel_partner_customer.php');
			$objCP = new ChannelPartnerCustomer();
			$detail = array(
				'channel_partner_id' => isset($_REQUEST['channel_partner_id']) ? $db->clean($_REQUEST['channel_partner_id']) : "",
				'search_name' => isset($_REQUEST['search_name']) ? $db->clean($_REQUEST['search_name']) : "",
				'ul' => isset($_REQUEST['ul']) ? $db->clean($_REQUEST['ul']) : "0",
				'll' => isset($_REQUEST['ll']) ? $db->clean($_REQUEST['ll']) : "50",
			);
			$ack = $objCP->GetChannelPartnerCustomerList($detail);
			$db->printJSON($ack);
		} else if ($service == "get_channel_partner_customer_detail" || $service == 228) {
			require_once('../include/class.channel_partner_customer.php');
			$objCP = new ChannelPartnerCustomer();
			$detail = array('id' => isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "");
			$ack = $objCP->GetChannelPartnerCustomerDetail($detail);
			$db->printJSON($ack);
		} else if ($service == "add_channel_partner_customer" || $service == 225) {
			require_once('../include/class.channel_partner_customer.php');
			$objCP = new ChannelPartnerCustomer();
			$detail = array(
				'channel_partner_id' => isset($_REQUEST['channel_partner_id']) ? $db->clean($_REQUEST['channel_partner_id']) : "",
				'company_name' => isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : "",
				'person_name' => isset($_REQUEST['person_name']) ? $db->clean($_REQUEST['person_name']) : "",
				'mobile_no' => isset($_REQUEST['mobile_no']) ? $db->clean($_REQUEST['mobile_no']) : "",
				'email' => isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "",
				'gst' => isset($_REQUEST['gst']) ? $db->clean($_REQUEST['gst']) : "",
				'country' => isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "",
				'state' => isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "",
				'city' => isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "",
				'pincode' => isset($_REQUEST['pincode']) ? $db->clean($_REQUEST['pincode']) : "",
				'address' => isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "",
			);
			$ack = $objCP->InsertChannelPartnerCustomer($detail);
			$db->printJSON($ack);
		} else if ($service == "update_channel_partner_customer" || $service == 226) {
			require_once('../include/class.channel_partner_customer.php');
			$objCP = new ChannelPartnerCustomer();
			$detail = array(
				'id' => isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "",
				'channel_partner_id' => isset($_REQUEST['channel_partner_id']) ? $db->clean($_REQUEST['channel_partner_id']) : "",
				'company_name' => isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : "",
				'person_name' => isset($_REQUEST['person_name']) ? $db->clean($_REQUEST['person_name']) : "",
				'mobile_no' => isset($_REQUEST['mobile_no']) ? $db->clean($_REQUEST['mobile_no']) : "",
				'email' => isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "",
				'gst' => isset($_REQUEST['gst']) ? $db->clean($_REQUEST['gst']) : "",
				'country' => isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "",
				'state' => isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "",
				'city' => isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "",
				'pincode' => isset($_REQUEST['pincode']) ? $db->clean($_REQUEST['pincode']) : "",
				'address' => isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "",
			);
			$ack = $objCP->UpdateChannelPartnerCustomer($detail);
			$db->printJSON($ack);
		} else if ($service == "delete_channel_partner_customer" || $service == 227) {
			require_once('../include/class.channel_partner_customer.php');
			$objCP = new ChannelPartnerCustomer();
			$detail = array('id' => isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "");
			$ack = $objCP->DeleteChannelPartnerCustomer($detail);
			$db->printJSON($ack);
		} else if ($service == "old_get_banner") {
			$sales_id	= isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
			$ctable 	= "promotion";
			$ctable1 	= "Banner";
			$current_date = date('Y-m-d');
			$isActive = $db->rp_getValue("sales_executive", "isActive", "id='" . $sales_id . "'", 0);
			$isDelete = $db->rp_getValue("sales_executive", "isDelete", "id='" . $sales_id . "'", 0);

			//aek master user hide kre che jenu id -1 che

			if ($isDelete == 0 /*|| $sales_id== "-1"*/) {
				if ($isActive == 1) {
					/*get rights*/
					$rights = array();
					$sales_rights = $db->rp_getData("sales_executive", "executive_in_min,executive_in_max,executive_out,super_stokist_order_view_flag,super_stokist_order_insert_flag,super_stokist_order_update_flag,super_stokist_order_delete_flag,outlets_order_view_flag,outlets_order_insert_flag,outlets_order_update_flag,outlets_order_delete_flag,dealer_order_view_flag,dealer_order_insert_flag,dealer_order_update_flag,dealer_order_delete_flag,project_order_view_flag,project_order_insert_flag,project_order_update_flag,project_order_delete_flag,oem_order_view_flag,oem_order_insert_flag,oem_order_update_flag,oem_order_delete_flag,survey_customer_view_flag,survey_customer_insert_flag,survey_customer_update_flag,survey_customer_delete_flag,customer_view_flag,customer_insert_flag,customer_update_flag,customer_delete_flag,followup_view_flag,followup_insert_flag,followup_update_flag,followup_delete_flag,create_order_view_flag,create_order_insert_flag,create_order_update_flag,create_order_delete_flag,order_history_view_flag,order_history_insert_flag,order_history_update_flag,order_history_delete_flag,complain_view_flag,complain_insert_flag,complain_update_flag,complain_delete_flag,customer_meeting_view_flag,customer_meeting_insert_flag,customer_meeting_update_flag,customer_meeting_delete_flag,near_by_me_view_flag,change_root_view_flag,change_root_insert_flag,change_root_update_flag,change_root_delete_flag,expense_view_flag,expense_insert_flag,expense_update_flag,expense_delete_flag,leave_view_flag,leave_insert_flag,leave_update_flag,leave_delete_flag,area_view_flag,area_insert_flag,area_update_flag,area_delete_flag,visit_view_flag,visit_insert_flag,visit_update_flag,visit_delete_flag,price_list_view_flag,bank_detail_view_flag,scheme_view_flag,discount_dealer_view_flag,discount_distributor_view_flag,gst_view_flag,visit_card_view_flag,traveling_view_flag,attendance_insert_flag,request_view_flag,request_insert_flag,request_update_flag,request_delete_flag,customer_leads_view_flag,customer_leads_insert_flag,customer_leads_update_flag,customer_leads_delete_flag,quotation_view_flag,quotation_insert_flag,quotation_update_flag,quotation_delete_flag,tracking_flag,prospact_insert_flag,prospact_update_flag,prospact_delete_flag,prospact_view_flag,marchent_customer_view_flag,marchent_customer_insert_flag,marchent_customer_update_flag,marchent_customer_delete_flag,my_route_view_flag,my_route_insert_flag,promotional_customer_view_flag,promotional_customer_insert_flag,promotional_customer_update_flag,promotional_customer_delete_flag,corporate_customer_view_flag,corporate_customer_insert_flag,corporate_customer_update_flag,corporate_customer_delete_flag,customer_stock_add_flag,deepfreezscheme_flag,travel_by_bike_flag,travel_by_bus_flag,travel_by_car_flag,tradercontractor_view_flag,tradercontractor_insert_flag,tradercontractor_update_flag,tradercontractor_delete_flag,mep_consultant_view_flag,mep_consultant_insert_flag,mep_consultant_update_flag,mep_consultant_delete_flag,builder_view_flag,builder_insert_flag,builder_update_flag,builder_delete_flag,brand_approval_visit_view_flag,brand_approval_visit_insert_flag,brand_approval_visit_update_flag,brand_approval_visit_delete_flag", "id='" . $sales_id . "' AND isDelete=0 ", "", 0);

					while ($sales_rights_d = mysqli_fetch_assoc($sales_rights)) {
						$sales_rights_d['class_id'] = $db->rp_getValue("promotion", "class_id", "isDelete=0");
						$sales_rights_d['area_id'] = $db->rp_getValue("promotion", "area_id", "isDelete=0");
						$sales_rights_d['class_name'] = $db->rp_getValue("class", "name", "id='" . $sales_rights_d['class_id'] . "' AND isDelete=0");
						$area_id  = explode(",", $sales_rights_d['area_id']);
						$newArray = array();
						foreach ($area_id as $area) {
							$newArray[] = $db->rp_getValue("area", "name", "id='" . $area . "'", 0);
						}
						$sales_rights_d['area_name'] = implode(",", $newArray);
						$sales_rights_d['tracking_local_time'] = TRACKING_TIME_LOCAL_API;
						$sales_rights_d['tracking_live_time'] = TRACKING_TIME_LIVE_API;
						$sales_rights_d['distance'] = DISTANCE_API;
						$sales_rights_d['tracking_live_url'] = TRACKING_LIVE_URL;
						$sales_rights_d['is_visit_image_flag'] = IS_IMAGE_COMPULSORY;
						$sales_rights_d['visit_start_image_flag'] = VISIT_START_IMAGE_FLAG;
						$sales_rights_d['visit_stop_image_flag'] =  VISIT_STOP_IMAGE_FLAG;
						$expense_vehicle = $db->rp_getValue("expense", "expense_date", "expense_date='" . $current_date . "' AND sales_executive_id='" . $sales_id . "' AND category_id=2 AND isDelete=0", 0);
						if ($expense_vehicle) {
							$sales_rights_d['is_current_vehicle_expense'] = "1";
						} else {
							$sales_rights_d['is_current_vehicle_expense'] = "0";
						}
						$rights[] = $sales_rights_d;
					}
					// print_r($rights);die;
					/*get rights*/

					/*visiting_card*/
					$visiting_card = $db->rp_getValue("sales_executive", "visiting_card_file_path", "id='" . $sales_id . "' AND isDelete=0");
					if ($visiting_card != "") {
						$ext = pathinfo($visiting_card, PATHINFO_EXTENSION);
						$visiting_card = SITEURL . GST_VISITING_DETAIL . $visiting_card;
						if ($ext == "pdf" || $ext == "PDF") {
							$title = "visiting_card.pdf";
						} else {
							$title = "visiting_card.jpge";
						}
					} else {
						$visiting_card = "";
						$title = "";
					}
					/*visiting_card*/

					/*Gst detail*/
					$gst_detail = $db->rp_getValue("dealer_distributor_network", "file_path", "isDelete=0", 0);
					if ($gst_detail != "") {
						$ext = pathinfo($gst_detail, PATHINFO_EXTENSION);
						$gst_detail_file_path = SITEURL . GST_VISITING_DETAIL . $gst_detail;
						if ($ext == "pdf" || $ext == "PDF") {
							$gst_title = "gst_detail.pdf";
						} else {
							$gst_title = "gst_detail.jpge";
						}
					} else {
						$gst_detail_file_path = "";
						$gst_title = "";
					}
					/*Gst detail*/

					/*price list*/
					$pricelist_detail = $db->rp_getValue("dealer_distributor_network", "price_list_path", "isDelete=0", 0);
					if ($pricelist_detail != "") {
						$ext = pathinfo($pricelist_detail, PATHINFO_EXTENSION);
						$price_list = SITEURL . GST_VISITING_DETAIL . $pricelist_detail;
						if ($ext == "pdf" || $ext == "PDF") {
							$price_list_name = "price_list_name.pdf";
						} else {
							$price_list_name = "price_list_name.jpge";
						}
					} else {
						$price_list = "";
						$price_list_name = "";
					}
					/*price list*/

					/*bank detail*/
					$bank_detail_path_detail = $db->rp_getValue("dealer_distributor_network", "bank_detail_path", "isDelete=0", 0);
					if ($bank_detail_path_detail != "") {
						$ext = pathinfo($bank_detail_path_detail, PATHINFO_EXTENSION);
						$bank_detail = SITEURL . GST_VISITING_DETAIL . $bank_detail_path_detail;
						if ($ext == "pdf" || $ext == "PDF") {
							$bank_detail_name = "bank_detail_name.pdf";
						} else {
							$bank_detail_name = "bank_detail_name.jpge";
						}
					} else {
						$bank_detail = "";
						$bank_detail_name = "";
					}
					/*bank detail*/

					/*scheme detail*/
					$scheme_path = $db->rp_getValue("dealer_distributor_network", "scheme_path", "isDelete=0", 0);
					if ($scheme_path != "") {
						$ext = pathinfo($scheme_path, PATHINFO_EXTENSION);
						$scheme_path = SITEURL . GST_VISITING_DETAIL . $scheme_path;
						if ($ext == "pdf" || $ext == "PDF") {
							$scheme_name = "scheme_name.pdf";
						} else {
							$scheme_name = "scheme_name.jpge";
						}
					} else {
						$scheme_path = "";
						$scheme_name = "";
					}
					/*scheme detail*/

					/*dealer_discount_path detail*/
					$dealer_discount_path = $db->rp_getValue("dealer_distributor_network", "dealer_discount_path", "isDelete=0", 0);
					if ($dealer_discount_path != "") {
						$ext = pathinfo($dealer_discount_path, PATHINFO_EXTENSION);
						$dealer_discount_path = SITEURL . GST_VISITING_DETAIL . $dealer_discount_path;
						if ($ext == "pdf" || $ext == "PDF") {
							$dealer_discount_name = "dealer_discount_name.pdf";
						} else {
							$dealer_discount_name = "dealer_discount_name.jpge";
						}
					} else {
						$dealer_discount_path = "";
						$dealer_discount_name = "";
					}
					/*dealer_discount_path detail*/

					/*distributor_discount_path detail*/
					$distributor_discount_path = $db->rp_getValue("dealer_distributor_network", "distributor_discount_path", "isDelete=0", 0);
					if ($dealer_discount_path != "") {
						$ext = pathinfo($distributor_discount_path, PATHINFO_EXTENSION);
						$distributor_discount_path = SITEURL . GST_VISITING_DETAIL . $distributor_discount_path;
						if ($ext == "pdf" || $ext == "PDF") {
							$distributor_discount_name = "distributor_discount_name.pdf";
						} else {
							$distributor_discount_name = "distributor_discount_name.jpge";
						}
					} else {
						$distributor_discount_path = "";
						$distributor_discount_name = "";
					}
					/*distributor_discount_path detail*/
					$class_data_r = $db->rp_getData("sales_executive_map_area", "*", "isDelete=0 AND sales_executive_id='" . $sales_id . "' ", "", 0);
					$class_data = array();

					if (mysqli_num_rows($class_data_r) > 0) {
						while ($class_data_d = mysqli_fetch_array($class_data_r)) {
							$class_data[] = $class_data_d['class_id'];
						}
					}
					$class_data = implode(",", $class_data);
					$area_data = array();

					$area_data_r = $db->rp_getData("sales_executive_map_area", "*", "isDelete=0 AND sales_executive_id='" . $sales_id . "' ", "", 0);

					if (mysqli_num_rows($area_data_r) > 0) {
						while ($area_data_d = mysqli_fetch_array($area_data_r)) {
							$area_data[] = $area_data_d['area_id'];
						}
					}
					$banners = array();
					// $ctable_r = $db->rp_getData($ctable,"*","promo_type=1 AND isDelete=0","display_order",0);
					// if(mysqli_num_rows($ctable_r)>0){
					// while($ctable_d = mysqli_fetch_array($ctable_r)){
					// 	array_push($banners,SITEURL.BANNER.$ctable_d['image_path']);

					// }
					$banner_area_r =	$db->rp_getData($ctable, "*", "promo_type=1 AND isDelete=0", "display_order", 0);
					if (mysqli_num_rows($banner_area_r) > 0) {
						while ($banner_area_d = mysqli_fetch_array($banner_area_r)) {
							if ($banner_area_d['area_id'] != "" && $banner_area_d['area_id'] != null) {
								$banner_area1 = explode(',', $banner_area_d['area_id']);
								$result1 = array_intersect($area_data, $banner_area1);
								//print_r($result1);exit;
								//echo count($result1);exit;
								if (count($result1) != 0) {
									// echo "hello";exit;
									$banners[] = SITEURL . BANNER . $banner_area_d['image_path'];
								}
							} else {
								// echo "hello11";exit;
								$banners[] = SITEURL . BANNER . $banner_area_d['image_path'];
							}
						}

						$count = $db->rp_getTotalRecord("visit", "user_id='" . $sales_id . "' AND DATE(stop_date_time)=0000-00-00 AND DATE(start_date_time)!=0000-00-00 AND isDelete=0", 0);

						if ($count > 0) {
							$is_visit_start = "1";
						} else {
							$is_visit_start = "0";
						}

						$ack = array("ack" => 1, "result" => $banners, "download_path" => DOWNLOAD_PATH, "catalog_title" => CATALOG_TITLE, "visiting_card_download_path" => $visiting_card, "visiting_card_title" => $title, "price_list" => $price_list, "price_list_name" => $price_list_name, "gst_title" => $gst_title, "gst_detail_file_path" => $gst_detail_file_path, "bank_title" => $bank_detail_name, "bank_detail_file_path" => $bank_detail, "scheme_title" => $scheme_name, "scheme_detail_file_path" => $scheme_path, "dealer_discount_title" => $dealer_discount_name, "dealer_discount_file_path" => $dealer_discount_path, "distributor_discount_title" => $distributor_discount_name, "distributor_discount_file_path" => $distributor_discount_path, "rights" => $rights, "offline_visit_limit" => OFFLINE_VISIT_LIMIT, "is_visit_start" => $is_visit_start);
						echo json_encode($ack);
					} else {
						$ack = array("ack" => 0, "ack_msg" => "No banner found!!", "download_path" => DOWNLOAD_PATH, "catalog_title" => CATALOG_TITLE, "visiting_card_download_path" => $visiting_card, "visiting_card_title" => $title, "price_list" => $price_list, "price_list_name" => $price_list_name, "gst_title" => $gst_title, "gst_detail_file_path" => $gst_detail_file_path, "bank_title" => $bank_detail_name, "bank_detail_file_path" => $bank_detail, "scheme_title" => $scheme_name, "scheme_detail_file_path" => $scheme_path, "dealer_discount_title" => $dealer_discount_name, "dealer_discount_file_path" => $dealer_discount_path, "distributor_discount_title" => $distributor_discount_name, "distributor_discount_file_path" => $distributor_discount_path, "offline_visit_limit" => OFFLINE_VISIT_LIMIT);
						echo json_encode($ack);
					}
				} else {
					$ack = array("ack" => 2, "ack_msg" => "User Is Deactive.Please Check!!", "developer_msg" => "User Is Deactive.Please Check");
					echo json_encode($ack);
				}
			} else {
				$ack = array("ack" => 2, "ack_msg" => "User Is Delete.Please Check!!", "developer_msg" => "User Is Delete.Please Check");
				echo json_encode($ack);
			}
		} else {
			$ack = array(
				"ack" => 0,
				"ack_msg" => "Internal error!!",
				"developer_msg" => "Service Parameter missing or not valid!!",
			);
			$db->printJSON($ack);
		}
	} else {
		$ack = array(
			"ack" => 0,
			"ack_msg" => "Internal error!!",
			"developer_msg" => "Service Parameter missing or not valid!!",
		);
		$db->printJSON($ack);
	}
} else {
	$ack = array(
		"ack" => 0,
		"ack_msg" => "Internal error!!",
		"developer_msg" => "Check your API Key or contact Admin",
		"extra" => array(
			"requested_params" => $_REQUEST,
			"other" => array()
		)
	);
	$db->printJSON($ack);
}

function aj_sendOTP($number, $activationCode)
{
	$msgId = "";
	$nt = new Notification();
	if ($number != "") {
		$sms = $activationCode . " is Your One Time Password!!";
		$msgId = $nt->rp_checkSMS($number, $sms);
	}
	return array('ack' => 1, 'status' => "msgId" . $msgId . "&OTP=" . $activationCode);
}
function aj_sendSecurityCode($email, $number, $activationCode)
{
	$nt = new Notification();

	$body = "Hello User, Someone requested new password for your " . SITENAME . " account if its you then enter this security code to application.<br> Security Code:" . $activationCode . "<br> Thank You,<br> Team " . SITENAME;
	$sms = $activationCode . " is your " . SITENAME . " security code";
	$email = $nt->aj_sendSecurityCode($email, "Security Check " . SITENAME, $body);
	$msgId = "NO";
	if ($number != "") {
		$msgId = $nt->rp_checkSMS($number, $sms);
	}
	return array('ack' => 1, 'status' => "msgId" . $msgId . "&email=" . $email);
}
function generateActivationCode()
{
	$characters = '0123456789';
	$randStr = "";
	for ($i = 0; $i <= 5; $i++) {
		$randStr = $randStr . $characters[rand(0, strlen($characters) - 1)];
	}
	return $randStr;
}
$db->disconnect();
// echo $db->rp_getValue("department","name","id=1");
