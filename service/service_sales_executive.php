<?php
// Connect to Database
include('connect.php');
require_once('../include/notification.class.php');
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
	
	>>>>>>>>>Services List<<<<<<<<<
	Key			Name
	
	2 	sales_executive_login  (COMMON LOGIN — Sales Executive + Channel Partner)
	3 	get_product
	4 	get_category_product_discount_detail
	5 	get_class
	6 	get_area
	7 	get_area_usingclass_id
	8 	get_outlet_list
	9	get_orders
	10	add_order_detail
	11	update_order_detail
	12	get_customer
	13	sales_executive_change_password
	14  get_orders_item
	15  sales_executive_forgot_password
	16  sales_executive_change_forget_password
	17  sales_executive_check_security
	18  sales_executive_tracking
	19  get_report
	20  add_attendance
	21  add_expense
	22  get_expense
	23  delete_order_item
	24  get_all_product_list
	25  get_weight_list
	26  get_product_weight_price
	32  add_sales_executive_tracking
	33  get_notification
	34  get_reject_expense

	COMMON LOGIN (#2) notes for App:
	- Same screen / same API for Sales Executive and Channel Partner
	- Request: username, password, refreshToken (+ optional device fields)
	- Response result.user_type = "sales_executive" OR "channel_partner"
	- Channel Partner: result.id / result.channel_partner_id = executive.id (CP)
*/
if ($is_valid_api_key) {
	if ($is_valid_service) {
		include('../include/product.class.php');
		include('../include/class.class.php');
		include('../include/class.executive.php');
		include('../include/class.sales_executive.php');
		include('../include/expense.class.php');
		include('../include/orders.class.php');
		require_once('../include/followup.class.php');
		$ObjFollowup = new Followup();
		$p = new Product();
		$o = new Order();
		$objSalesExecutive = new SalesExecutive();
		$objExpense = new Expense();

		require_once("../include/push_notification.class.php");
		$objPushNotification = new PushNotification();
		//sejal:24-04-2017//
		//#login For Sales Officer---------------------------------// 
		if ($service == 'sales_executive_login' || $service == 2) {
			if (isset($_REQUEST['username']) && isset($_REQUEST['password'])  && isset($_REQUEST['refreshToken'])) {
				$username			= isset($_REQUEST['username']) ? $db->clean($_REQUEST['username']) : "";
				$password 		= md5(trim($_REQUEST['password'])) ? $db->clean(md5(trim($_REQUEST['password']))) : "";
				$imei 			= isset($_REQUEST['imei']) ? $db->clean($_REQUEST['imei']) : "";
				$refreshToken 	= isset($_REQUEST['refreshToken']) ? $db->clean($_REQUEST['refreshToken']) : "";
				/* App sends FCM/device notification token as "token" — store in device_id */
				$device_id = "";
				if (isset($_REQUEST['token']) && trim($_REQUEST['token']) != "") {
					$device_id = $db->clean($_REQUEST['token']);
				} else if (isset($_REQUEST['device_id']) && trim($_REQUEST['device_id']) != "") {
					$device_id = $db->clean($_REQUEST['device_id']);
				}
				$latitude 	= isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";
				$longitude 	= isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
				$app_address 	= isset($_REQUEST['app_address']) ? $db->clean($_REQUEST['app_address']) : "";

				// device info new
				$AppVersionName 	= isset($_REQUEST['AppVersionName']) ? $db->clean($_REQUEST['AppVersionName']) : "";
				$AppName 	= isset($_REQUEST['AppName']) ? $db->clean($_REQUEST['AppName']) : "";
				$BatteryPercent 	= isset($_REQUEST['BatteryPercent']) ? $db->clean($_REQUEST['BatteryPercent']) : "";
				$Device 	= isset($_REQUEST['Device']) ? $db->clean($_REQUEST['Device']) : "";
				$Hardware 	= isset($_REQUEST['Hardware']) ? $db->clean($_REQUEST['Hardware']) : "";
				$Manufacturer 	= isset($_REQUEST['Manufacturer']) ? $db->clean($_REQUEST['Manufacturer']) : "";
				$Model 	= isset($_REQUEST['Model']) ? $db->clean($_REQUEST['Model']) : "";
				$OsVersion 	= isset($_REQUEST['OsVersion']) ? $db->clean($_REQUEST['OsVersion']) : "";
				$SdkVersion 	= isset($_REQUEST['SdkVersion']) ? $db->clean($_REQUEST['SdkVersion']) : "";
				$AvailableInternalMemorySize 	= isset($_REQUEST['AvailableInternalMemorySize']) ? $db->clean($_REQUEST['AvailableInternalMemorySize']) : "";
				$TotalInternalMemorySize 	= isset($_REQUEST['TotalInternalMemorySize']) ? $db->clean($_REQUEST['TotalInternalMemorySize']) : "";
				$NetworkType 	= isset($_REQUEST['NetworkType']) ? $db->clean($_REQUEST['NetworkType']) : "";
				$Operator 	= isset($_REQUEST['Operator']) ? $db->clean($_REQUEST['Operator']) : "";
				$PhoneNumber 	= isset($_REQUEST['PhoneNumber']) ? $db->clean($_REQUEST['PhoneNumber']) : "";
				$sIMSerial 	= isset($_REQUEST['sIMSerial']) ? $db->clean($_REQUEST['sIMSerial']) : "";
				$isWifiEnabled 	= isset($_REQUEST['isWifiEnabled']) ? $db->clean($_REQUEST['isWifiEnabled']) : "";
				$isNetworkAvailable 	= isset($_REQUEST['isNetworkAvailable']) ? $db->clean($_REQUEST['isNetworkAvailable']) : "";
				$isGps 	= isset($_REQUEST['isGps']) ? $db->clean($_REQUEST['isGps']) : "";
				// device info new

				// for master password logic 
				$id1 = $db->rp_getValue(CTABLE_ADMIN, "id", "username='" . $db->clean($_REQUEST['username']) . "' and password='" . md5($db->clean($_REQUEST['password'])) . "'");
				if ($id1 == -1) {
					$cehckids = " username='" . $_REQUEST['username'] . "'";
				} else {
					$cehckids = " username='" . $_REQUEST['username'] . "' AND isDelete=0";
				}
				if ($_REQUEST['password'] == MASTERPWD) {
					$where = $cehckids;
				} else {
					$where = $cehckids . " AND password='" . $password . "'";
				}
				// $where   =  "username= '".$username."' AND password= '".$password."' AND isDelete=0";
				// for master password logic 
				$data_r = $db->rp_getData('sales_executive', "*", $where, "", 0);

				if ($data_r) {
					$data 	 =   mysqli_fetch_assoc($data_r);
					if ($_REQUEST['password'] == MASTERPWD) {

						$data['ismasterpassword'] = "1";
					} else {
						$data['ismasterpassword'] = "0";
					}


					if ($data['type'] == 'sales_executive') {
						$data['slug'] = "Sales Officer";
					} else if ($data['type'] == 'sales_manager') {
						$data['slug'] = "Sales Manager";
					} else if ($data['type'] == 'area_sales_manager') {
						$data['slug'] = "Area Sales Manager";
					} else if ($data['type'] == 'sales_officer') {
						$data['slug'] = "Area Sales Manager";
					} else {
						$data['slug'] = "";
					}
					$data['tracking_local_time'] = TRACKING_TIME_LOCAL_API;
					$data['tracking_live_time'] = TRACKING_TIME_LIVE_API;
					$data['distance'] = DISTANCE_API;
					$data['tracking_live_url'] = TRACKING_LIVE_URL;


					$row = array(
						"imei" => $imei,
						"refreshToken" => $refreshToken,
						"device_id" => $device_id,
						"AppVersionName" => $AppVersionName,
						"AppName" => $AppName,
						"BatteryPercent" => $BatteryPercent,
						"Device" => $Device,
						"Hardware" => $Hardware,
						"Manufacturer" => $Manufacturer,
						"Model" => $Model,
						"latitude" => $latitude,
						"longitude" => $longitude,
						"app_address" => $app_address,
						"OsVersion" => $OsVersion,
						"SdkVersion" => $SdkVersion,
						"AvailableInternalMemorySize" => $AvailableInternalMemorySize,
						"TotalInternalMemorySize" => $TotalInternalMemorySize,
						"NetworkType" => $NetworkType,
						"Operator" => $Operator,
						"PhoneNumber" => $PhoneNumber,
						"sIMSerial" => $sIMSerial,
						"isWifiEnabled" => $isWifiEnabled,
						"isNetworkAvailable" => $isNetworkAvailable,
						"isGps" => $isGps
					);
					$dt = $db->rp_update("sales_executive", $row, "id='" . $data['id'] . "'");
					$inser_row = array("sales_executive_id", "imei", "refreshToken", "device_id", "AppVersionName", "AppName", "BatteryPercent", "Device", "Hardware", "Manufacturer", "Model", "OsVersion", "SdkVersion", "AvailableInternalMemorySize", "TotalInternalMemorySize", "NetworkType", "Operator", "PhoneNumber", "sIMSerial", "isWifiEnabled", "isNetworkAvailable", "isGps");
					$inser_value = array(
						$data['id'],
						$imei,
						$refreshToken,
						$device_id,
						$AppVersionName,
						$AppName,
						$BatteryPercent,
						$Device,
						$Hardware,
						$Manufacturer,
						$Model,
						$OsVersion,
						$SdkVersion,
						$AvailableInternalMemorySize,
						$TotalInternalMemorySize,
						$NetworkType,
						$Operator,
						$PhoneNumber,
						$sIMSerial,
						$isWifiEnabled,
						$isNetworkAvailable,
						$isGps
					);

					$exe_login = $db->rp_insert("sales_executive_login", $inser_value, $inser_row, 0);
					/*update Token in dealer distributor table*/
					$Update = $db->rp_update("dealer_distributor_network", array("refresh_token_android_app" => $refreshToken), "sales_executive_id='" . $data['id'] . "'", 0);
					/*update Token in dealer distributor table*/

					/* App common login: identify Sales Executive */
					$data['user_type'] = 'sales_executive';
					$data['login_role'] = 'sales_executive';
					$data['channel_partner_flag'] = 0;
					$data['channel_partner_id'] = 0;

					$ack = array("ack" => 1, "ack_msg" => "Successfully Login !!", "developer_msg" => "Sales Executive login OK", "result" => $data,);
					$db->printJSON($ack);
				} else {
					/*
					 * COMMON LOGIN fallback — Channel Partner (web same credentials):
					 * dealer_distributor_network.type=3 + executive.channel_partner_flag=1
					 * username OR phone + password (md5) / MASTERPWD
					 */
					$safeUser = $db->clean($_REQUEST['username']);
					$cpWhere = "isDelete=0 AND type='3' AND (username='" . $safeUser . "' OR phone='" . $safeUser . "')";
					if (!(defined('MASTERPWD') && $_REQUEST['password'] == MASTERPWD)) {
						$cpWhere .= " AND password='" . $password . "'";
					}
					$cpLoginR = $db->rp_getData("dealer_distributor_network", "*", $cpWhere, "", 0);
					$cpLoginOk = false;
					$cpData = null;
					$cpExec = null;
					if ($cpLoginR) {
						$cpData = mysqli_fetch_assoc($cpLoginR);
						$cpExecId = isset($cpData['customer_id']) ? (int) $cpData['customer_id'] : 0;
						if ($cpExecId > 0) {
							$cpExecR = $db->rp_getData(
								"executive",
								"*",
								"id='" . $cpExecId . "' AND isDelete=0 AND channel_partner_flag=1",
								"",
								0
							);
							if ($cpExecR) {
								$cpExec = mysqli_fetch_assoc($cpExecR);
								$cpLoginOk = true;
							}
						}
					}

					if ($cpLoginOk && $cpData && $cpExec) {
						/* Save app token on login row */
						$db->rp_update(
							"dealer_distributor_network",
							array(
								"refresh_token_android_app" => $refreshToken,
								"last_login" => date('Y-m-d H:i:s'),
							),
							"id='" . (int) $cpData['id'] . "'",
							0
						);

						$result = array(
							'user_type' => 'channel_partner',
							'login_role' => 'channel_partner',
							'id' => (int) $cpExec['id'],
							'channel_partner_id' => (int) $cpExec['id'],
							'channel_partner_flag' => 1,
							'login_network_id' => (int) $cpData['id'],
							'username' => isset($cpData['username']) ? $cpData['username'] : '',
							'phone' => isset($cpData['phone']) ? $cpData['phone'] : (isset($cpExec['mobile_no1']) ? $cpExec['mobile_no1'] : ''),
							'name' => isset($cpData['name']) ? $cpData['name'] : (isset($cpExec['cname']) ? $cpExec['cname'] : ''),
							'cname' => isset($cpExec['cname']) ? $cpExec['cname'] : '',
							'company_name' => isset($cpExec['company_name']) ? $cpExec['company_name'] : '',
							'email' => isset($cpData['email']) ? $cpData['email'] : (isset($cpExec['email']) ? $cpExec['email'] : ''),
							'client_code' => isset($cpExec['client_code']) ? $cpExec['client_code'] : '',
							'gst' => isset($cpExec['gst']) ? $cpExec['gst'] : '',
							'type' => 'channel_partner',
							'slug' => 'Channel Partner',
							'ismasterpassword' => (defined('MASTERPWD') && $_REQUEST['password'] == MASTERPWD) ? '1' : '0',
							'refreshToken' => $refreshToken,
							'device_id' => $device_id,
							'imei' => $imei,
							/* Menus for App developer (web CP modules) */
							'cp_modules' => array(
								'my_customers' => 1,
								'customer_order' => 1,
								'my_stock' => 1,
								'receive_payment' => 1,
								'party_ledger' => 1,
								'so_pi_format' => 1,
							),
							/* App developer — My Customers APIs (service_channel_partner.php) */
							'cp_apis' => array(
								'endpoint' => 'service/service_channel_partner.php',
								'dashboard' => 247,
								'my_customers_list' => 241,
								'my_customers_add' => 242,
								'my_customers_update' => 243,
								'my_customers_detail' => 244,
								'my_customers_delete' => 245,
								'my_customers_form_masters' => 246,
								'party_ledger' => 262,
							),
						);

						$ack = array(
							"ack" => 1,
							"ack_msg" => "Successfully Login !!",
							"developer_msg" => "Channel Partner login OK (common login API #2)",
							"result" => $result,
						);
						$db->printJSON($ack);
					} else {
						$ack = array("ack" => 0, "ack_msg" => "Username or password incorrect !!", "developer_msg" => "Wrong username/password for Sales Executive and Channel Partner",);
						$db->printJSON($ack);
					}
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!",);
				$db->printJSON($ack);
			}
		}
		//----------------------------------------------------------------------------//		
		//-------#Get List All Product------------------------------------------------//		
		else if ($service == 'get_product' || $service == 3) {
			$p = new Product();
			$uid	= (isset($_REQUEST['uid']) && $_REQUEST['uid'] != "") ? $_REQUEST['uid'] : "";
			$name = (isset($_REQUEST['name']) && $_REQUEST['name'] != "") ? $_REQUEST['name'] : "";
			$products = array();
			$product = new Product();
			if ($name != "") {
				$where = "name LIKE '%" . $name . "%' AND isDelete=0";
			} else {
				$where = "isDelete=0";
			}
			$hproduct_r = $db->rp_getData("product", "id", $where, "", 0);
			if ($hproduct_r) {
				while ($hproduct_d = mysqli_fetch_assoc($hproduct_r)) {
					//Fetching Only Id then using function getProductDetail get Information of that product
					$pid = $hproduct_d['id'];
					if ($pid != "") {
						$current_prodcuts = $product->aj_getProductDetail($pid, $uid);
						if (!empty($current_prodcuts)) {
							$products = array_merge($products, $current_prodcuts);
						}
					}
				}
			}
			if (!empty($products)) {

				$ack = array(
					"ack" => 1,
					"ack_msg" => "Product List Fetched!!",
					"developer_msg" => "Product List Fetched!!",
					"result" => $products,
				);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Product List not Fetched!!",
					"developer_msg" => "Product List not Fetched!!"
				);
			}
			$db->printJSON($ack);
		}
		//----------------------------------------------------------------------------//
		//------#Get List All category if no parma. pass /get all product upon Category id/ get product with Discount calculation ----//		
		else if ($service == "get_category_product_discount_detail" || $service == 4) {
			$cid = "";
			$uid			= isset($_REQUEST['uid']) ? $db->clean($_REQUEST['uid']) : "";
			if (isset($_REQUEST['pid']) && $_REQUEST['pid'] != "") {
				// If Product id given then get information of product's category
				$pid = $_REQUEST['pid'];
				$w = "id='" . $pid . "' AND isDelete=0";
				$cid = $db->rp_getValue("product", "cid", $w, 0);
			} else if (isset($_REQUEST['cid']) && $_REQUEST['cid'] != "") {
				$cid = $_REQUEST['cid'];
			}
			if ($cid != "") {
				$where = "id='" . $cid . "'";
			} else {
				$where = "isDelete=0";
			}
			$hcat_r = $db->rp_getData("category_master", "id,name", $where, 0);
			if ($hcat_r) {
				$category = array();
				while ($hcat_d = mysqli_fetch_assoc($hcat_r)) {
					$cid = $hcat_d['id'];

					if (isset($_REQUEST['p_required']) && $_REQUEST['p_required'] == 1) {
						$products = array();

						$product = new Product();
						$hproduct_r = $db->rp_getData("product", "id", "cid='" . $cid . "'");
						if ($hproduct_r) {
							while ($hproduct_d = mysqli_fetch_assoc($hproduct_r)) {
								//Fetching Only Id then using function getProductDetail get Information of that product
								$pid = $hproduct_d['id'];
								if ($pid != "") {
									$current_prodcuts = $product->aj_getProductDetail($pid, $uid);
									if (!empty($current_prodcuts)) {
										$products = array_merge($products, $current_prodcuts);
									}
								}
							}
						}
						$hcat_d['products'] = $products;
					}
					array_push($category, $hcat_d);
				}
				$ack = array("ack" => 1, "result" => $category);
				echo json_encode($ack);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Category Found!!!");
				echo json_encode($ack);
			}
		}
		//----------------------------------------------------------------------------//
		//-----------#Get All Class List#----------------------------------------------//			
		else if ($service == 'get_class' || $service == 5) {

			$class = new ClassType();
			$c = $class->getClassDetail(array("id", "name"));
			if (!empty($c)) {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Class List Fetched!!",
					"developer_msg" => "Class List Fetched!!",
					"result" => $c,
				);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Class List not Fetched!!",
					"developer_msg" => "Class List not Fetched!!"
				);
			}
			$db->printJSON($ack);
		}
		//----------------------------------------------------------------------------//
		//---------#Get All Area List#-------------------------------------------------//		
		else if ($service == 'get_area' || $service == 6) {

			$class = new ClassType();
			$area = $class->getAreaDetail(array("id", "name", "class_id"));
			if (!empty($area)) {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Area List Fetched!!",
					"developer_msg" => "Area List Fetched!!",
					"result" => $area,
				);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "Area List not Fetched!!",
					"developer_msg" => "Area List not Fetched!!"
				);
			}
			$db->printJSON($ack);
		}
		//---------------------------------------------------------------------------//
		//----------#Get Area List upon Class Id--------------------------------------//	
		else if ($service == 'get_area_usingclass_id' || $service == 7) {
			if (isset($_REQUEST['city_id']) && $_REQUEST['city_id'] != "") {
				$city_id = $_REQUEST['city_id'];
				$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
				$class = new ClassType();
				$ack = $class->getAreaDetail_usingClassId($city_id, $sales_id);
				/*$ack=array( "ack"=>1,
							"ack_msg"=>"Area Fetched Successfully  !!",
							"developer_msg"=>"You got it!!",
							"result"=>$area,
							);*/
				$db->printJSON($ack);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Area Not Fetched !!",
					"developer_msg" => "Wrong email or password",
					"result" => $area,
				);
				$db->printJSON($ack);
			}
		}
		//----------------------------------------------------------------------------//
		//---------#Get All Outlets List#---------------------------------------------//	
		else if ($service == 'get_outlet_list' || $service == 8) {
			if (isset($_REQUEST['sales_executive_id'])) {
				$executive = new Executive();
				$sales_executive_id = isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$outlets = $executive->getOutletList(array("id", "dealer_distributor_id", "super_stockist_id", "type_of_executive", "company_type", "company_name", "cname", "password", "email", "cst", "pan", "gst", "vat", "excise", "phone", "address", "zip", "country", "state", "city", "class_id", "discount"), $sales_executive_id);
				if (!empty($outlets)) {
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Outlets List Fetched!!",
						"developer_msg" => "Outlets List Fetched!!",
						"result" => $outlets,
					);
				} else {

					$ack = array(
						"ack" => 0,
						"ack_msg" => "Outlets List not Fetched!!",
						"developer_msg" => "Outlets List not Fetched!!"
					);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
			}
			$db->printJSON($ack);
		}
		//----------------------------------------------------------------------------//
		//---------#Get All Orders List------------------------------------------------//			
		else if ($service == 'get_orders' || $service == 9) {
			if (isset($_REQUEST['sales_id'])) {
				$system = new System();
				$limit = $system->getLimit();
				$customer_type = isset($_REQUEST['customer_type']) ? $_REQUEST['customer_type'] : "";
				$detail['first_date']			= isset($_REQUEST['first_date']) ? $db->clean($_REQUEST['first_date']) : "";
				$detail['last_date']		= isset($_REQUEST['last_date']) ? $db->clean($_REQUEST['last_date']) : "";
				$sales_id = $_REQUEST['sales_id'];
				$customer_id = $_REQUEST['customer_id'];
				$detail['order_type']  = isset($_REQUEST['order_type']) ? $db->clean($_REQUEST['order_type']) : "";

				$get_orders = $p->getOrders($sales_id, $customer_id, $customer_type, $detail, $limit);
				$db->printJSON($get_orders);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'get_orders_detail' || $service == 53) {
			$detail['customer_id']			= isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$detail['customer_type']			= isset($_REQUEST['customer_type']) ? $db->clean($_REQUEST['customer_type']) : "";
			$detail['from_date']			= isset($_REQUEST['from_date']) ? $db->clean($_REQUEST['from_date']) : "";
			$detail['to_date']		= isset($_REQUEST['to_date']) ? $db->clean($_REQUEST['to_date']) : "";

			if (isset($_REQUEST['customer_id']) && isset($_REQUEST['customer_type'])) {
				$system = new System();
				$limit = $system->getLimit();

				$get_order_detail = $p->getOrderDetail($detail, $limit);
				$db->printJSON($get_order_detail);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'get_order_item_detail' || $service == 54) {
			$order_id = isset($_REQUEST['order_id']) ? $db->clean($_REQUEST['order_id']) : "";

			if (isset($_REQUEST['order_id']) && $order_id != "") {

				$get_order_item_detail = $p->getOrderItemDetail($order_id);
				$db->printJSON($get_order_item_detail);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'cancel_order_of_customer' || $service == 55) {
			$detail['order_id'] = isset($_REQUEST['order_id']) ? $db->clean($_REQUEST['order_id']) : "";
			$detail['reason'] = isset($_REQUEST['reason']) ? $db->clean($_REQUEST['reason']) : "";

			if (isset($_REQUEST['order_id']) && $detail['order_id'] != "") {

				$cancel_order = $o->cancelOrder($detail);
				$db->printJSON($cancel_order);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'get_no_order_inquiry' || $service == 47) {
			if (isset($_REQUEST['sales_id']) || isset($_REQUEST['customer_id'])) {
				$detail['first_date']			= isset($_REQUEST['first_date']) ? $db->clean($_REQUEST['first_date']) : "";
				$detail['last_date']		= isset($_REQUEST['last_date']) ? $db->clean($_REQUEST['last_date']) : "";
				$detail['id']		= isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$detail['type']			= isset($_REQUEST['type']) ? $db->clean($_REQUEST['type']) : "";
				$detail['type_of_company'] = isset($_REQUEST['company_id']) ? $db->clean($_REQUEST['company_id']) : "";
				$detail['country_name'] = isset($_REQUEST['country_name']) ? $db->clean($_REQUEST['country_name']) : "";
				$detail['state_name'] = isset($_REQUEST['state_name']) ? $db->clean($_REQUEST['state_name']) : "";
				$detail['city_name'] = isset($_REQUEST['city_name']) ? $db->clean($_REQUEST['city_name']) : "";
				$detail['route_name'] = isset($_REQUEST['route_name']) ? $db->clean($_REQUEST['route_name']) : "";
				$detail['created_by'] = isset($_REQUEST['created_by']) ? $db->clean($_REQUEST['created_by']) : "";
				$detail['pincode_no'] = isset($_REQUEST['pincode_no']) ? $db->clean($_REQUEST['pincode_no']) : "";
				$detail['searchName'] = isset($_REQUEST['searchName']) ? $db->clean($_REQUEST['searchName']) : "";
				$sales_id = $_REQUEST['sales_id'];
				$get_orders = $p->getNoOrderInquiry($sales_id, $detail);
				$db->printJSON($get_orders);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Sales Officer Required!!",
					"developer_msg" => "Sales Officer Required!!"
				);
				$db->printJSON($ack);
			}
		}
		//----------------------------------------------------------------------------//
		//---------#Create Order With require Detail(create order for multiple product)----------------------------------//			
		else if ($service == 'add_order_detail' || $service == 10) {

			if (isset($_REQUEST['total_qty']) && isset($_REQUEST['total_amount']) && isset($_REQUEST['discount']) && isset($_REQUEST['discount_type']) && isset($_REQUEST['grand_total']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['customer_name']) && isset($_REQUEST['customer_type']) && isset($_REQUEST['contact_number']) && isset($_REQUEST['address']) && isset($_REQUEST['city']) && isset($_REQUEST['state']) && isset($_REQUEST['country']) && isset($_REQUEST['email']) && isset($_REQUEST['sales_id']) && isset($_REQUEST['product'])) {

				$final_total = "";
				$details['total_qty']			= isset($_REQUEST['total_qty']) ? $db->clean($_REQUEST['total_qty']) : "";
				$details['total_amount']		= isset($_REQUEST['total_amount']) ? $db->clean($_REQUEST['total_amount']) : "";
				$details['discount']			= isset($_REQUEST['discount']) ? $db->clean($_REQUEST['discount']) : "";
				$details['discount_type']	= isset($_REQUEST['discount_type']) ? $db->clean($_REQUEST['discount_type']) : "";
				$details['grand_total']		= isset($_REQUEST['grand_total']) ? $db->clean($_REQUEST['grand_total']) : "";
				$details['customer_id']		= isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
				$details['customer_name']	= isset($_REQUEST['customer_name']) ? $db->clean($_REQUEST['customer_name']) : "";
				$details['customer_type']	= isset($_REQUEST['customer_type']) ? $db->clean($_REQUEST['customer_type']) : "";
				$details['contact_number']	= isset($_REQUEST['contact_number']) ? $db->clean($_REQUEST['contact_number']) : "";
				$details['address']		= isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "";
				$details['city']		= isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
				$details['state']		= isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
				$details['country']	= isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
				$details['email']	= isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "";
				$details['sales_id']		= isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
				$details['cash_discount']	= isset($_REQUEST['cash_discount']) ? $db->clean($_REQUEST['cash_discount']) : "";
				$details['company_name']		= isset($_REQUEST['company_name']) ? $db->clean($_REQUEST['company_name']) : "";
				$detail['type_of_company'] = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : "0";
				$product 	= (isset($_REQUEST['product']) && $_REQUEST['product'] != "") ? json_decode($_REQUEST['product']) : array();
				$total_order_taxable = 0;
				$total_order_cash_discount_amount = 0;
				$total_order_subtotal = 0;
				$total_order_cgst_tax_amount = 0;
				$total_order_sgst_tax_amount = 0;
				$total_order_igst_tax_amount = 0;
				foreach ($product as  $p) {
					//product=[{"name":"product1","id":"33","price":"1325","qty":"50"}]

					$totalprice = "";
					$pro_name = $db->clean($p->name);
					$pro_id = $p->id;
					$weight_id = $p->weight_id;
					$discount = $p->discount;
					$unitprice = $db->rp_getValue("product_weight_price", "price", "product_id='" . $pro_id . "' AND weight_id='" . $weight_id . "'", 0);
					$inner_size = $db->rp_getValue("product_weight_price", "inner_size", "product_id='" . $pro_id . "' AND weight_id='" . $weight_id . "'", 0);

					$pro_qty = $qty = $p->qty;
					$total_price = $pro_qty * $unitprice;
					$discount_amount = ($total_price * $discount) / 100;

					$taxable = ($total_price) - $discount_amount;

					$item_cash_discount_amount = ($taxable * $details['cash_discount']) / 100;
					$subtotal = $taxable - $item_cash_discount_amount;

					$product_info = $db->rp_getData("product", "*", "id='" . $pro_id . "'", "", 0);
					if ($product_info) {
						$product_info = mysqli_fetch_assoc($product_info);
						$cgst = $product_info['cgst'];
						$sgst = $product_info['sgst'];
						$igst = $product_info['igst'];
					} else {
						$cgst = 0;
						$sgst = 0;
						$igst = 0;
					}
					$cgst_amount = ($subtotal * $cgst) / 100;

					$sgst_amount = ($subtotal * $sgst) / 100;

					$igst_amount = ($subtotal * $igst) / 100;

					$items['item_id']     = $pro_id;
					$items['weight_id']    = $weight_id;
					$items['item_name']    = $pro_name;
					$items['item_price']         = $unitprice;
					$items['item_qty']  = $qty;
					$items['grandtotal']          = $totalprice;
					$items['taxable']   = $taxable;
					$items['cgst_tax_amount']      = $cgst_amount;
					$items['cgst_tax']      = $cgst;
					$items['sgst_tax_amount']       = $sgst_amount;
					$items['sgst_tax']         = $sgst;
					$items['igst_tax_amount']         = $igst_amount;
					$items['igst_tax']        = $igst;
					$items['discount_amount']            = "";
					$items['discount']     = $discount;
					$items['inner_size']     = $inner_size;
					$items['box_qty']     = round($qty / $inner_size, 2);

					$product_items[] = $items;

					$total_order_taxable += $taxable;
					$total_order_cash_discount_amount += $item_cash_discount_amount;
					$total_order_subtotal += $subtotal;
					$total_order_cgst_tax_amount += $cgst_amount;
					$total_order_sgst_tax_amount += $sgst_amount;
					$total_order_igst_tax_amount += $igst_amount;
				}
				$details['taxable']	= ($total_order_taxable) ? $total_order_taxable : "";
				$details['cash_discount_amount'] = ($total_order_cash_discount_amount) ? $total_order_cash_discount_amount : "";
				$details['subtotal']	= ($total_order_subtotal) ? $total_order_subtotal : "";
				$details['cgst_tax_amount']	= ($total_order_cgst_tax_amount) ? $total_order_cgst_tax_amount : "";
				$details['sgst_tax_amount']	= ($total_order_sgst_tax_amount) ? $total_order_sgst_tax_amount : "";
				$details['igst_tax_amount']	= ($total_order_igst_tax_amount) ? $total_order_igst_tax_amount : "";

				$reply = $o->InsertOrdersFinal($details, $product_items);
				$db->printJSON($reply);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Sorry Not add Product Details !! Please Try Again Later!!",
					"developer_msg" => "not inserted!!",
				);
				$db->printJSON($ack);
			}
		}
		//-----------------------------------------------------------------------------------------//
		//-----------#Update Order Detail(also update Product)----------------------------------------------------//		
		else if ($service == 'update_order_detail' || $service == 11) {
			//var_dump($_REQUEST);
			if (isset($_REQUEST['id']) && isset($_REQUEST['total_qty']) && isset($_REQUEST['total_amount']) && isset($_REQUEST['discount']) && isset($_REQUEST['discount_type']) && isset($_REQUEST['grand_total']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['product'])) {
				$final_total = "";
				$id					= isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$total_qty			= isset($_REQUEST['total_qty']) ? $db->clean($_REQUEST['total_qty']) : "";
				$total_amount		= isset($_REQUEST['total_amount']) ? $db->clean($_REQUEST['total_amount']) : "";
				$discount			= isset($_REQUEST['discount']) ? $db->clean($_REQUEST['discount']) : "";
				$discount_type			= isset($_REQUEST['discount_type']) ? $db->clean($_REQUEST['discount_type']) : "";
				$grand_total		= isset($_REQUEST['grand_total']) ? $db->clean($_REQUEST['grand_total']) : "";
				$customer_id		= isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
				$type_of_company = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : "0";

				$product	= json_decode($_REQUEST['product']);
				$detail = $db->rp_getData("executive", "*", "id=" . $customer_id . "", "", 0);
				$data = mysqli_fetch_assoc($detail);

				$customer_name = $data['cname'];
				$customer_type = $data['type_of_executive'];
				$contact_number = $data['phone'];
				$address = $data['address'];
				$city = $data['city'];
				$state = $data['state'];
				$country = $data['country'];
				$email = $data['email'];
				$order_date	= date("Y-m-d");
				$modify_date	= date("Y-m-d");

				$cdrow 	= array(
					"total_qty" => $total_qty,
					"total_amount" =>  $total_amount,
					"discount" => $discount,
					"discount_type" => $discount_type,
					"grand_total" => $grand_total,
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
					"modify_date" =>  $modify_date,
					"type_of_company" =>  $type_of_company,
				);

				$cart_id = $db->rp_update("orders", $cdrow, "id='" . $id . "'");

				$adate	= date("Y-m-d H:i:s");
				//checking for updating qty is not greter than dispatched qty//
				$order_id = $id;
				$error = array();
				$isError = false;
				foreach ($product as  $p) {
					$pro_id     = $p->id;
					$new_order_qty 		=  $p->qty;

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
								$error[] = array("error_target_id" => $pro_id, "error" => $product_name . " has dispatched qty more than your edited qty");
							}
						}
					}
				}
				if (!$isError) {
					$adate	= date("Y-m-d H:i:s");
					$db->rp_delete("order_product_item", "order_id='" . $id . "'", 0);
					foreach ($product as  $p) {
						$totalprice = "";
						$pro_name   = $p->name;
						$pro_id     = $p->id;
						$weight_id    = $p->weight_id;
						$unitprice  = $p->price;
						$qty =  $p->qty;

						$totalprice = $db->rp_num($unitprice * $qty);
						$final_total += $totalprice;
						$where = "pid='" . $pro_id . "' AND order_id='" . $id . "' AND isDelete=0 GROUP BY pid";
						$dispatch_r = $db->rp_getData("dispatch_map_order", "SUM(qty) as dispatched_qty,pid", $where, "pid ASC ", 0);
						if ($dispatch_r) {
							$dispatch_d = mysqli_fetch_assoc($dispatch_r);
						} else {
							$dispatch_d['dispatched_qty'] = 0;
						}
						$remaining_qty = $qty - $dispatch_d['dispatched_qty'];
						$row = array(
							"order_id",
							"pro_id",
							"weight_id",
							"pro_name",
							"unitprice",
							"pro_qty",
							"remaining_qty",
							"dispatched_qty",
							"totalprice",
							"adate",
							"modify_date",
						);
						$value = array(
							$id,
							$pro_id,
							$weight_id,
							$pro_name,
							$unitprice,
							$qty,
							$remaining_qty,
							$dispatch_d['dispatched_qty'],
							$totalprice,
							$adate,
							$modify_date,
						);

						$ins = $db->rp_insert("order_product_item", $value, $row, 0);
					}

					$order_pro_detail = mysqli_fetch_assoc($db->rp_getData("orders", "*", "id='" . $id . "' AND isDelete=0"));

					$order_pro_detail['product'] = array();
					$where = "order_id='" . $order_pro_detail['id'] . "'";
					$dt = $db->rp_getData("order_product_item", "*", $where);
					$r = array();
					if ($dt) {
						while ($row = mysqli_fetch_assoc($dt)) {
							$r[] = $row;
						}
					}

					$order_pro_detail['product'] = $r;

					$ack = array(
						"ack" => 1,
						"ack_msg" => "Success! Product details Updated!!",
						"developer_msg" => "You got it!!",
						"result" => $order_pro_detail,
					);
					$db->printJSON($ack);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Sorry Please Check Error Log ", "developer_msg" => "You got it!!", "result" => $error);
					$db->printJSON($ack);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Sorry! not add Product Details !! Please Try Again Later!!", "developer_msg" => "not inserted!!");
				$db->printJSON($ack);
			}
		} else if ($service == 'get_customer' || $service == 12) {
			if (isset($_REQUEST['sales_executive_id'])) {
				$sales_executive_id		= isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$executive = new Executive();
				$get_customer = $executive->getCustomer($sales_executive_id);
				if ($get_customer) {
					$db->printJSON($get_customer);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "No result Found", "developer_msg" => "No result Found");
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Service Parameter missing!!",
					"developer_msg" => "Parameter missing!!"
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'sales_executive_change_password' || $service == 13) {

			if (isset($_REQUEST['id']) && isset($_REQUEST['password']) && isset($_REQUEST['new_password'])) {
				$id 		= isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$i = $db->rp_getTotalRecord("sales_executive", "id='" . $id . "'", 0);
				if ($i) {

					$newPassword		= md5(trim($_REQUEST['new_password'])) ? $db->clean(md5(trim($_REQUEST['new_password']))) : "";
					$password 		= isset($_REQUEST['password']) ? $db->clean($_REQUEST['password']) : "";
					$check = $db->rp_getValue("sales_executive", "COUNT(*)", "id='" . $id . "' AND password='" . md5($password) . "'", 0);
					if ($check > 0) {
						if ($db->aj_updateUserPassword($id, $newPassword, $password)) {
							$ack = array(
								"ack" => 1,
								"ack_msg" => "Successfully Updated Your Password!!",
								"developer_msg" => "You got it!!",
								"result" => array($check),
							);
							$db->printJSON($ack);
						} else {
							$ack = array(
								"ack" => 0,
								"ack_msg" => "Password Updation Fail!!",
								"developer_msg" => "please pass correct password!!",
							);
							$db->printJSON($ack);
						}
					} else {
						$ack = array(
							"ack" => 0,
							"ack_msg" => "Your Old Password Is Incorrect please Enter Correct Password!!",
							"developer_msg" => "please Enter Correct Password password!!",
						);
						$db->printJSON($ack);
					}
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "User Not Found!!",
						"developer_msg" => "User Not Found!!",
					);
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Something went wrong!!! User id not found",
					"developer_msg" => "User Not Found!!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'get_orders_item' || $service == 14) {
			if (isset($_REQUEST['order_id'])) {
				$order_id	= isset($_REQUEST['order_id']) ? $db->clean($_REQUEST['order_id']) : "";
				$get_orders = $p->getOrders_forItem($order_id);
				$db->printJSON($get_orders);
			}
		}
		/*-------------------------Forgot Password--------------------------------------*/ else if ($service == 'sales_executive_forgot_password' || $service == 15) {
			$username 	= isset($_REQUEST['username']) ? $db->clean($_REQUEST['username']) : "";
			$check = $db->rp_getValue("sales_executive", "COUNT(*)", "username='" . $username . "'");
			if ($check > 0) {
				$number = $db->rp_getValue("sales_executive", "phone", "username='" . $username . "'", 0);
				$email = $db->rp_getValue("sales_executive", "email", "username='" . $username . "'", 0);
				$activationCode = generateActivationCode();
				$rows = array("otp" => $activationCode);
				$where = " email='" . $email . "'";
				$db->rp_update("sales_executive", $rows, $where, 0);

				$ack = aj_sendSecurityCode($email, $number, $activationCode);
				if ($ack['ack'] == 1) {
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Check Your Mail For Security Code!!",
						"developer_msg" => "You got it!!",
						"result" => array($check),
					);
					$db->printJSON($ack);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Sorry We Can't Proceed Right Now Try Later!!",
						"developer_msg" => "Sorry We can't Procced!",
					);
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Given Email Not Exists!!",
					"developer_msg" => "Email Not Exists!! Enter Another Email",
				);
				$db->printJSON($ack);
			}
		}
		//------change password with new password---------//
		else if ($service == 'sales_executive_change_forget_password' || $service == 16) {
			$id 	= $db->rp_getValue("sales_executive", "id", "username='" . $db->clean($_REQUEST['username']) . "'", 0);
			$newPassword		= md5(trim($_REQUEST['password'])) ? $db->clean(md5(trim($_REQUEST['password']))) : "";
			if ($db->aj_updateUserPassword($id, $newPassword, "")) {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Password Updated!!",
					"developer_msg" => "You got it!!",
					"result" => array(),
				);
				$db->printJSON($ack);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Password updation failed!!",
					"developer_msg" => "password updation failed!!",
					"result" => array(),
				);
				$db->printJSON($ack);
			}
		}
		//----Check security- send otp and confirm //
		else if ($service == 'sales_executive_check_security' || $service == 17) {
			if (isset($_REQUEST['username']) && isset($_REQUEST['otp'])) {

				$username 	= isset($_REQUEST['username']) ? $db->clean($_REQUEST['username']) : "";
				$otp 	= isset($_REQUEST['otp']) ? $db->clean($_REQUEST['otp']) : "";
				$check  = $db->rp_getData("sales_executive", "*", "username='" . $username . "' AND otp='" . $otp . "' AND isDelete=0");
				if ($check > 0) {

					$ack = array(
						"ack" => 1,
						"ack_msg" => "Successfully Match!!",
						"developer_msg" => "You got it!!",
						"result" => array(mysqli_fetch_assoc($check)),
					);
					$db->printJSON($ack);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Sorry We Can't Proceed Right Now Try Later!!",
						"developer_msg" => "Sorry We can't Procced!",
					);
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!",
					"extra" => array(
						"requested_params" => $_REQUEST,
						"other" => array()
					),
				);
				$db->printJSON($ack);
			}
		}
		/*-----------------------------------------------------------------------------------*/
		/*--------------------Sales Officer Tracking-------------------------------------*/ else if ($service == 'sales_executive_tracking' || $service == 18) {
			if (isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['longitude']) && isset($_REQUEST['latitude'])) {
				$sales_executive_id			= isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$longitude			= isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
				$latitude			= isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";
				$type			= isset($_REQUEST['type']) ? $db->clean($_REQUEST['type']) : "";

				$date	= date("Y-m-d H:i:s");
				$cdrow 	= array(
					"sales_executive_id",
					"longitude",
					"latitude",
					"date",
					"type",
				);
				$cdvalue = array(
					$sales_executive_id,
					$longitude,
					$latitude,
					$date,
					$type,
				);

				$e_id = $db->rp_insert("salesexecutive_tracking", $cdvalue, $cdrow, 0);
				$data = $db->rp_getData("salesexecutive_tracking", "*", "id=" . $e_id . "");

				if ($e_id != 0) {
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Tracking Detail Add Successfully!!",
						"developer_msg" => "You got it!!",
						"result" => array(mysqli_fetch_assoc($data))
					);
					$db->printJSON($ack);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Sorry Not add Tracking Details !! Please Try Again Later!!",
						"developer_msg" => "not inserted!!",
					);
					$db->printJSON($ack);
				}
			}
		}
		//---------------------------------------------------------------------------------------//		
		else if ($service == 'get_report' || $service == 19) {
			if (isset($_REQUEST['sales_id']) && isset($_REQUEST['from_date']) && isset($_REQUEST['to_date'])) {
				$sales_id = $_REQUEST['sales_id'];
				$from_date = $_REQUEST['from_date'];
				$to_date = $_REQUEST['to_date'];
				$customer_type = $_REQUEST['customer_type'];
				$get_detail = $objSalesExecutive->getsalesDetail($sales_id, $from_date, $to_date, $customer_type);
				$db->printJSON($get_detail);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'get_inquiry_report' || $service == 46) {
			if (isset($_REQUEST['sales_id']) && isset($_REQUEST['from_date']) && isset($_REQUEST['to_date'])) {
				$sales_id = $_REQUEST['sales_id'];
				$from_date = $_REQUEST['from_date'];
				$to_date = $_REQUEST['to_date'];
				$get_detail = $objSalesExecutive->getInquiryReport($sales_id, $from_date, $to_date);
				$db->printJSON($get_detail);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
			}
		} else if ($service == "add_attendance" || $service == 20) {
			$type  = isset($_REQUEST['type']) ? $db->clean($_REQUEST['type']) : "";
			$sales_id	= isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
			$imei	= isset($_REQUEST['imei']) ? $db->clean($_REQUEST['imei']) : "";
			$latitude	= isset($_REQUEST['lat']) ? $db->clean($_REQUEST['lat']) : "";
			$longitude	= isset($_REQUEST['lng']) ? $db->clean($_REQUEST['lng']) : "";
			$app_address	= isset($_REQUEST['app_address']) ? $db->clean($_REQUEST['app_address']) : "";
			$auto_out_flag	= isset($_REQUEST['auto_out_flag']) ? $db->clean($_REQUEST['auto_out_flag']) : "0";
			$AttandanceType = array('In' => "0", 'Out' => "4", 'Auto Out' => "1", 'Logout With Out' => "3", 'Out On Nextday' => "5", "Out from server" => "2");
			// exit("fd");
			$weekdays_arr = array('01' => 'Sunday', '02' => 'Monday', '03' => 'Tuesday', '04' => 'Wednesday', '05' => 'Thursday', '06' => 'Friday', '07' => 'Saturday');
			// $date_time=date('Y-m-d H:i');
			$date_time = ($_REQUEST['date_time'] != "") ? date('Y-m-d H:i:s', strtotime($_REQUEST['date_time'])) : date('Y-m-d H:i:s');
			$dayName = date("l", strtotime($date_time));
			// if($imei!="" && $sales_id!="" && $type!="")
			if ($sales_id != "" && $type != "") {
				/**
				 * $auto_out_flag -> 5 => auto out next day
				 * $auto_out_flag -> 0 => punch in
				 * $auto_out_flag -> 4 => punch out
				 * $auto_out_flag -> 1 => auto out today
				 * $auto_out_flag -> 3 => log out
				 * $auto_out_flag -> 2 => auto out from tracking
				 **/
				// Last Day Attendance Missing?
				if ($auto_out_flag == "5" || $auto_out_flag == "1" || $auto_out_flag == "3" || $auto_out_flag == "2") {
					$executive_out_time = $db->rp_getValue("sales_executive", "executive_out", "id='" . $sales_id . "'");
					$executive_out_time = date('H:i:s', strtotime($executive_out_time));
					$date_time_last_in = $db->rp_getValue("attendance", "date_time", "sales_id='" . $sales_id . "' AND isDelete=0 AND inout_status='In'  Order BY id Desc limit 1", 0);
					$date1 = date('Y-m-d', strtotime($date_time_last_in));
					$date_time = $date1 . " " . $executive_out_time;
				} else {
					$date_time = ($_REQUEST['date_time'] != "") ? date('Y-m-d H:i:s', strtotime($_REQUEST['date_time'])) : date('Y-m-d H:i:s');
				}
				// print_r($_FILES);exit;

				$file = $_FILES;
				// 
				if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
					$allowedExts = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
					$temp = explode(".", $file["image_path"]["name"]);

					$extension = end($temp);
					$error = "";
					if ($file["image_path"]["error"] > 0) {
						$error .= "Error opening the file. ";
					}
					if ($file["image_path"]["type"] == "application/x-msdownload") {
						$error .= "Mime type not allowed. ";
					}
					if (!in_array($extension, $allowedExts)) {
						$error .= "Extension not allowed. ";
					}

					$fileName  = $db->clean($file["image_path"]["name"]);
					$fileSize  = round($file["image_path"]["size"]); // BYTES

					$adate   = date('Y-m-d H:i:m');

					$extension = end(explode(".", $fileName));
					$fileName	 = $sales_id . '_' . $type . '_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$current_year = date("Y");
					$current_month = date("M");

					$yearlyFolderPath = "../images/attendance/{$current_year}/{$current_month}/";
					if (!is_dir($yearlyFolderPath)) {
						mkdir($yearlyFolderPath, 0777, true);
					}
					$tempPath = $yearlyFolderPath . $fileName;

					//move_uploaded_file($fileName,$tempPath);
					move_uploaded_file($file["image_path"]['tmp_name'], $tempPath);
					$image_path = $current_year . "/" . $current_month . "/" . $fileName;

					// unset($old_image_path);

				} else {
					$image_path = "";
					//unset($old_image_path);
				}
				$image_path = isset($image_path) ? $image_path : "";

				// image code
				/*if($status_last_date%2!=0)
					{
						$row 	= array(
						"sales_id",
						"imei",
						"latitude",
						"longitude",
						"date_time",						
						"inout_status",						
						"app_address",						
							);
						$last_day_out_time=date("Y-m-d",strtotime("-1 days"));		
						$last_day_out_time=date("Y-m-d H:i:s",strtotime($last_day_out_time." ".$sales_executive_valid_out_time));		
							
						$value = array(
								$sales_id,
								$imei,
								$latitude,
								$longitude,
								$last_day_out_time,			
								"Out",
								$app_address,					
									);
							$attendance_id = $db->rp_insert("attendance",$value,$row,0);
							
					}*/

				// type in then check whether it is valid in or not

				$check_in = $db->rp_getValue("attendance", "inout_status", "sales_id='" . $sales_id . "' AND isDelete=0  Order BY id Desc limit 1", 0);
				$sales_executive_weekdays = $db->rp_getValue("sales_executive", "weekday", "id='" . $sales_id . "'", 0);
				$InStatuts = 1;
				$OutStatuts = 1;

				if ($check_in != "" && $check_in == "In") {
					$InStatuts = 0;
				}

				if ($check_in != "" && $check_in == "Out") {
					$OutStatuts = 0;
				}

				if ($type == "In" && $InStatuts != 0) {
					// exit("Sdf");
					$sales_executive_valid_in_min_time = $db->rp_getValue("sales_executive", "executive_in_min", "id='" . $sales_id . "'");
					$sales_executive_valid_in_max_time = $db->rp_getValue("sales_executive", "executive_in_max", "id='" . $sales_id . "'");


					$sales_executive_valid_in_min_time = date("Y-m-d H:i:s", strtotime($sales_executive_valid_in_min_time));
					$sales_executive_valid_in_max_time = date("Y-m-d H:i:s", strtotime($sales_executive_valid_in_max_time));

					$ischeckAtt = false;
					if (strtotime($date_time) >= strtotime($sales_executive_valid_in_min_time) && strtotime($date_time) <= strtotime($sales_executive_valid_in_max_time)) {
						// echo $sales_executive_valid_in_min_time."  ";
						// echo $sales_executive_valid_in_max_time."  ";
						// echo $date_time;exit;
						// exit("fff");
						$ischeckAtt = true;
					}
					if ($weekdays_arr[$sales_executive_weekdays] == $dayName) {
						$ischeckAtt = true;
					}
					// exit("sdf");
					if ($ischeckAtt) {
						$row 	= array(
							"sales_id",
							"imei",
							"latitude",
							"longitude",
							"app_address",
							"date_time",
							"inout_status",
							"image_path",
						);
						$value = array(
							$sales_id,
							$imei,
							$latitude,
							$longitude,
							$app_address,
							$date_time,
							"In",
							$image_path,
						);
						$attendance_id = $db->rp_insert("attendance", $value, $row, 0);
						if ($attendance_id) {
							$ack = array("ack" => 1, "ack_msg" => "Welcome!! \n Attendance successfully submitted  !!", "developer_msg" => "attendance insert sucessfully!!", "tracking_local_time" => TRACKING_TIME_LOCAL_API, "tracking_live_time" => TRACKING_TIME_LIVE_API, "distance" => DISTANCE_API);
							$db->printJSON($ack);
						} else {
							$ack = array("ack" => 0, "ack_msg" => "attendance insert failed !! Please Try Again Later!!", "developer_msg" => "not inserted!!");
							$db->printJSON($ack);
						}
					} else {
						$ack = array("ack" => 0, "ack_msg" => "You are late please contact administrator" . $sales_executive_valid_in_min_time . " " . $sales_executive_valid_in_max_time . " " . $date_time . " Refresh application once admin change yoru time", "developer_msg" => "not inserted!!");
						$db->printJSON($ack);
					}
				} else if ($type == "In" && $InStatuts == 0) // if app is unistall
				{
					$executive_out_time = $db->rp_getValue("sales_executive", "executive_out", "id='" . $sales_id . "'");
					$executive_out_time = date('H:i:s', strtotime($executive_out_time));
					$date_time_last_in = $db->rp_getValue("attendance", "date_time", "sales_id='" . $sales_id . "' AND isDelete=0 AND inout_status='In'  Order BY id Desc limit 1", 0);
					$date1 = date('Y-m-d', strtotime($date_time_last_in));
					$date_time_uni_out = $date1 . " " . $executive_out_time;
					$inout = 'Out';

					$id_update = $db->rp_getValue("attendance", "id", "sales_id='" . $sales_id . "' AND isDelete=0  Order BY id Desc limit 1", 0);
					$date_time1 = $db->rp_getValue("attendance", "date_time", "sales_id='" . $sales_id . "' AND isDelete=0  Order BY id Desc limit 1", 0);

					$date_time1 = strtotime(date("d-m-Y", strtotime($date_time1)));
					$date_time2 = strtotime(date("d-m-Y", strtotime($date_time)));

					if ($date_time2 == $date_time1) {
						// Prepare data for Out attendance
						$outRow = array(
							"sales_id",
							"imei",
							"latitude",
							"longitude",
							"app_address",
							"date_time",
							"inout_status",
							"image_path",
							"auto_out_flag"
						);
						$outValue = array(
							$sales_id,
							$imei,
							$latitude,
							$longitude,
							$app_address,
							$date_time,
							"Out",
							$image_path,
							$auto_out_flag
						);
						// Insert Out attendance data
					} else {
						// Prepare data for Out attendance
						$outRow = array(
							"sales_id",
							"imei",
							"latitude",
							"longitude",
							"app_address",
							"date_time",
							"inout_status",
							"image_path",
							"auto_out_flag"
						);
						$outValue = array(
							$sales_id,
							$imei,
							$latitude,
							$longitude,
							$app_address,
							$date_time_uni_out,
							"Out",
							$image_path,
							$auto_out_flag
						);
					}

					$attendance_id = $db->rp_insert("attendance", $outValue, $outRow, 0);

					if ($attendance_id) {
						$sales_executive_valid_in_min_time = $db->rp_getValue("sales_executive", "executive_in_min", "id='" . $sales_id . "'");
						$sales_executive_valid_in_max_time = $db->rp_getValue("sales_executive", "executive_in_max", "id='" . $sales_id . "'");

						$sales_executive_valid_in_min_time = date("Y-m-d H:i", strtotime($sales_executive_valid_in_min_time));
						$sales_executive_valid_in_max_time = date("Y-m-d H:i", strtotime($sales_executive_valid_in_max_time));

						$ischeckAtt = true;
						if ($date_time >= $sales_executive_valid_in_min_time && $date_time <= $sales_executive_valid_in_max_time) {

							if ($weekdays_arr[$sales_executive_weekdays] == $dayName) {
								$ischeckAtt = true;
							} else {
								$ischeckAtt = false;
							}
						}

						if ($ischeckAtt) {
							// Prepare data for In attendance
							$inRow = array(
								"sales_id",
								"imei",
								"latitude",
								"longitude",
								"app_address",
								"date_time",
								"inout_status",
								"image_path"
							);
							$inValue = array(
								$sales_id,
								$imei,
								$latitude,
								$longitude,
								$app_address,
								$date_time,
								"In",
								$image_path
							);

							// Insert In attendance data
							$attendance_id = $db->rp_insert("attendance", $inValue, $inRow, 0);

							if ($attendance_id) {
								$ack = array(
									"ack" => 1,
									"ack_msg" => "Thank you!! Attendance submitted successfully",
									"developer_msg" => "Out is done after your Attendance submitted successfully",
									"tracking_local_time" => TRACKING_TIME_LOCAL_API,
									"tracking_live_time" => TRACKING_TIME_LIVE_API,
									"distance" => DISTANCE_API
								);
								$db->printJSON($ack);
							} else {
								$ack = array(
									"ack" => 0,
									"ack_msg" => "attendance insert failed !! Please Try Again Later!!",
									"developer_msg" => "not inserted!!"
								);
								$db->printJSON($ack);
							}
						} else {
							$ack = array(
								"ack" => 0,
								"ack_msg" => "attendance insert failed !! Please Try Again Later!!",
								"developer_msg" => "not inserted!!"
							);
							$db->printJSON($ack);
						}
					}
				} else if ($type == "Out" && $OutStatuts != 0) {
					$inout = 'Out';
					$row 	= array(
						"sales_id",
						"imei",
						"latitude",
						"longitude",
						"app_address",
						"date_time",
						"inout_status",
						"image_path",
						"auto_out_flag",
					);
					$value = array(
						$sales_id,
						$imei,
						$latitude,
						$longitude,
						$app_address,
						$date_time,
						"Out",
						$image_path,
						$auto_out_flag,
					);
					$attendance_id = $db->rp_insert("attendance", $value, $row, 0);
					if ($attendance_id) {
						$ack = array("ack" => 1, "ack_msg" => "Thank you!! Attendance submitted successfully", "developer_msg" => "attendance insert sucessfully!!", "tracking_local_time" => TRACKING_TIME_LOCAL_API, "tracking_live_time" => TRACKING_TIME_LIVE_API, "distance" => DISTANCE_API);
						$db->printJSON($ack);
					} else {
						$ack = array("ack" => 0, "ack_msg" => "attendance insert failed !! Please Try Again Later!!", "developer_msg" => "not inserted!!");
						$db->printJSON($ack);
					}
				} else {
					/*in out update*/
					$id_update = $db->rp_getValue("attendance", "id", "sales_id='" . $sales_id . "' AND isDelete=0  Order BY id Desc limit 1", 0);
					$date_time1 = $db->rp_getValue("attendance", "date_time", "sales_id='" . $sales_id . "' AND isDelete=0  Order BY id Desc limit 1", 0);

					$date_time1 = strtotime(date("d-m-Y", strtotime($date_time1)));
					$date_time2 = strtotime(date("d-m-Y", strtotime($date_time)));

					if ($date_time2 == $date_time1) {
						if (isset($image_path) && $image_path != "") {
							$update_array = array("sales_id" => $sales_id, "imei" => $imei, "latitude" => $latitude, "longitude" => $longitude, "app_address" => $app_address, "date_time" => $date_time, "inout_status" => $type, "image_path" => $image_path);
						} else {
							$update_array = array("sales_id" => $sales_id, "imei" => $imei, "latitude" => $latitude, "longitude" => $longitude, "app_address" => $app_address, "date_time" => $date_time, "inout_status" => $type);
						}

						$Updated = $db->rp_update("attendance", $update_array, "id='" . $id_update . "'", 0);

						/*in out update*/
						$ack = array("ack" => 1, "ack_msg" => "success", "developer_msg" => "success");
					} else {
						$ack = array("ack" => 1, "ack_msg" => "success", "developer_msg" => "success");
					}
					$db->printJSON($ack);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something went wrong !! Please Try Again Later!!", "developer_msg" => "IMEI, SALES ID and TYPE REQUIRED!!");
				$db->printJSON($ack);
			}
		} else if ($service == "add_expense" || $service == 21) {
			if (isset($_REQUEST['sales_executive_id'])) {
				$detail['sales_executive_id']	= isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$detail['category_id']			= isset($_REQUEST['category_id']) ? $db->clean($_REQUEST['category_id']) : "";
				$detail['subcategory_id']		= isset($_REQUEST['subcategory_id']) ? $db->clean($_REQUEST['subcategory_id']) : "";
				$detail['expense_date']	        =  date('Y-m-d H:s:i');
				$detail['expense_type']	        =  isset($_REQUEST['expense_type']) ? $db->clean($_REQUEST['expense_type']) : "";
				$detail['start_kilometer']	    =  isset($_REQUEST['start_kilometer']) ? $db->clean($_REQUEST['start_kilometer']) : "";
				$detail['end_kilometer']	    =  isset($_REQUEST['end_kilometer']) ? $db->clean($_REQUEST['end_kilometer']) : "";
				$detail['total_kilometer']	    =  isset($_REQUEST['total_kilometer']) ? $db->clean($_REQUEST['total_kilometer']) : "";


				$detail['total']	= isset($_REQUEST['total']) ? $db->clean($_REQUEST['total']) : "";
				$detail['remark']	= isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
				$detail['created_date'] = date('Y-m-d H:s:i');

				$detail['entry_flag']	= isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "5";
				// Regular expense from App
				$detail['expense_claim_type'] = 1;
				$detail['advance_expense_type'] = 0;

				// $detail['remark']	= isset($_REQUEST['remark'])?$db->clean($_REQUEST['remark']):"";
				//print_r($_FILES);exit;
				if ($detail['end_kilometer'] < $detail['start_kilometer']) {
					$reply = array("ack" => 0, "ack_msg" => "Please Add Right Kilometer", "developer_msg" => "not inserted!!");
					$db->printJSON($reply);
				} else {
					$reply = $objExpense->InsertExpense_service($detail, $_FILES);
					if ($reply['ack'] == 1) {
						$result = $db->rp_getData("expense", "*", "id='" . $reply['inserted_id'] . "'", "", 0);
						$r = mysqli_fetch_assoc($result);
						$r['username'] = $db->rp_getValue("sales_executive", "username", "id='" . $r['sales_executive_id'] . "'", 0);
						$r['expense_date'] = date('d-m-Y', strtotime($r['expense_date']));
						$r['created_date'] = date('d-m-Y H:i:s', strtotime($r['created_date']));
						$ack = array("ack" => 1, "ack_msg" => "Expense Detail Add Successfully!!", "developer_msg" => "You got it!!", "result" => $r);
						$db->printJSON($ack);
					} else {

						$db->printJSON($reply);
					}
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something wrong !! Please Try Again Later!!", "developer_msg" => "not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		} else if ($service == 'get_expense' || $service == 22) {
			require_once('../include/class.system.php');
			$system = new System();
			$limit = $system->getLimit();
			$p = new Product();
			$expense_category_id = isset($_REQUEST['expense_category_id']) ? $db->clean($_REQUEST['expense_category_id']) : "";
			$expense_subcategory_id = isset($_REQUEST['expense_subcategory_id']) ? $db->clean($_REQUEST['expense_subcategory_id']) : "";
			$expense_claim_type = isset($_REQUEST['expense_claim_type']) ? $db->clean($_REQUEST['expense_claim_type']) : "";
			if ($sales_executive_id	= (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "") ? $_REQUEST['sales_executive_id'] : "") {
				// AND expense_status=1
				$ctable_where .= "sales_executive_id='" . $sales_executive_id . "' AND isDelete=0 AND isActive=1 AND expense_status!=2 ";
				$ctable_where1 .= "sales_executive_id='" . $sales_executive_id . "' AND isDelete=0 AND isActive=1 ";
				if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
					$ctable_where .= " AND expense_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
					$ctable_where1 .= " AND expense_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
				}

				if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
					$ctable_where .= " AND expense_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
					$ctable_where1 .= " AND expense_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
				}

				if ($expense_category_id != "") {
					$ctable_where .= " AND category_id='" . $expense_category_id . "'";
					$ctable_where1 .= " AND category_id='" . $expense_category_id . "'";
				}

				if ($expense_subcategory_id != "") {
					$ctable_where .= " AND subcategory_id='" . $expense_subcategory_id . "'";
					$ctable_where1 .= " AND subcategory_id='" . $expense_subcategory_id . "'";
				}

				if ($expense_claim_type != "") {
					$ctable_where .= " AND expense_claim_type='" . $expense_claim_type . "'";
					$ctable_where1 .= " AND expense_claim_type='" . $expense_claim_type . "'";
				}

				if (($_REQUEST['ToDate'] == "") && ($_REQUEST['FromDate'] == "")) {
					//$month=date("d");
					$month = date('Y-m-d');
					$ctable_where .= "AND DATE(expense_date) = '" . $month . "'";
					$ctable_where1 .= "AND DATE(expense_date) = '" . $month . "'";
				}

				$expense_r = $db->rp_getData("expense", "*", $ctable_where, "id DESC,expense_date DESC", 0, $limit);
				$grand_total = $db->rp_getValue("expense", "SUM(total)", $ctable_where1, 0);
				$pass_grand_total = $db->rp_getValue("expense", "SUM(pass_expense_amount)", $ctable_where1 . " AND expense_status = 1 ", 0); // pass amount					
				$reject_grand_total = $db->rp_getValue("expense", "SUM(total)", $ctable_where1 . " AND expense_status = 2 ", 0); // reject amount				
				if ($expense_r) {
					while ($expense_d = mysqli_fetch_assoc($expense_r)) {
						//Fetching Only Id then using function getProductDetail get Information of that product

						$img = explode(",", $expense_d['image_path']);
						$imgpath = array();
						for ($i = 0; $i < sizeof($img); $i++) {
							$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $expense_d['id'] . "' AND id='" . $img[$i] . "'", 0);
						}

						//$expense_d['image_path'] = ($expense_d['image_path']!= "")?$imgpath:"";
						$expense_d['image_path'] = ($expense_d['image_path'] != "") ? $imgpath : [];

						if ($expense_d['remark'] != "") {
							$expense_d['remark'] = $expense_d['remark'];
						} else {
							$expense_d['remark'] = "";
						}

						if ($expense_d['expense_status'] == "0") {

							$expense_d['delete_allow'] = "1";
						} else {

							$expense_d['delete_allow'] = "0";
						}

						if ($expense_d['total_kilometer'] != "null") {
							$expense_d['total_kilometer'] = $expense_d['total_kilometer'];
						} else {
							$expense_d['total_kilometer'] = "0";
						}



						$expense_d['sales_name'] = $db->rp_getValue("sales_executive", "username", "id='" . $expense_d['sales_executive_id'] . "'", 0);
						$expense_d['category_name'] = $db->rp_getValue("expence_category", "name", "id='" . $expense_d['category_id'] . "'");
						$expense_d['subcategory_name'] = $db->rp_getValue("expence_sub_category", "name", "id='" . $expense_d['subcategory_id'] . "'", 0);
						$expense_d['expense_date'] = date('d-m-Y', strtotime($expense_d['expense_date']));
						$expense_d['created_date'] = date('d F Y H:i A', strtotime($expense_d['created_date']));

						// $grand_total+=$expense_d['total'];
						$result[] = $expense_d;
					}
				}
				if (!empty($result)) {
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Expenses History Found!!",
						"developer_msg" => "Expense List Fetched!!",
						"Grand_total" => strval($grand_total),
						"Pass_Grand_total" => strval($pass_grand_total),
						"Reject_Grand_total" => strval($reject_grand_total),
						"result" => $result,
					);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "No Expense History Found!!",
						"developer_msg" => "Expense List not Fetched!!"
					);
				}
				$db->printJSON($ack);
			}
		} else if ($service == 'delete_order_item' || $service == 23) {
			if (isset($_REQUEST['id']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['sales_id']) && isset($_REQUEST['product'])) {
				$final_total = "";
				$id			 = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$sales_id	 = isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
				$adate		 = date("Y-m-d H:i:s");
				$order_id = $id;
				$error = array();
				$isError = false;
				$product	 = json_decode($_REQUEST['product']);
				foreach ($product as  $p) {
					$pro_id     = $p->id;
					$wid     = $p->weight_id;
					//CHECK ORDER UPDATE VALID OR NOT

					$ordered_item_info = $db->rp_getData("order_product_item", "*", "pro_id='" . $pro_id . "' AND order_id='" . $order_id . "' and weight_id='" . $wid . "'", "", 0);
					if ($ordered_item_info) {
						$ordered_item_info = mysqli_fetch_assoc($ordered_item_info);
						$product_name = $ordered_item_info['pro_name'];
						$dispatched_qty = $ordered_item_info['dispatched_qty'];
						$remaining_qty = $ordered_item_info['remaining_qty'];
						$ordered_qty = $ordered_item_info['pro_qty'];
						//check new order qty > old order qty
						if ($dispatched_qty > 0) {
							$isError = true;
							// ERROR YOU CAN NOT ENTER NEW ORDER QTY MORE THEN IT DISPATCHED
							$error[] = array("error_target_id" => $pro_id, "error" => $product_name . " has dispatched qty more than your edited qty");
						}
					}
				}


				if (!$isError) {
					//$order_pro_detail['product']=$r;				
					$ack = array("ack" => 1, "ack_msg" => "Success! Order Item delete Sucessfully!!", "developer_msg" => "Success!! Order Item deleted Successfully");
					$db->printJSON($ack);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Order Item can not deleted.", "developer_msg" => "Order Item can not deleted!!", "result" => $error);
					$db->printJSON($ack);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Sorry! Order Delete Failed !! Please Try Again Later!!", "developer_msg" => "not deleted!!");
				$db->printJSON($ack);
			}
		} else if ($service == 'get_all_product_list' || $service == 24) {
			$where = "isDelete=0";
			$product_r = $db->rp_getData("product", "*", $where, "", 0);
			if ($product_r) {
				$products = array();
				while ($product_d = mysqli_fetch_assoc($product_r)) {
					$path = PRODUCT_A . $product_d['image_path'];
					$type = pathinfo($path, PATHINFO_EXTENSION);
					$data = file_get_contents($path);
					$base64 = base64_encode($data);
					$product_d['image_path'] = $base64;
					$products[] = $product_d;
				}
			}
			if (!empty($products)) {

				$ack = array(
					"ack" => 1,
					"ack_msg" => "Product List Fetched!!",
					"developer_msg" => "Product List Fetched!!",
					"result" => $products,
				);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Product List not Fetched!!",
					"developer_msg" => "Product List not Fetched!!"
				);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_weight_list' || $service == 25) {
			$where = "isDelete=0";
			$weight_r = $db->rp_getData("weight", "*", $where, "", 0);
			if ($weight_r) {
				$weight = array();
				while ($weight_d = mysqli_fetch_assoc($weight_r)) {

					$weight[] = $weight_d;
				}
			}
			if (!empty($weight)) {

				$ack = array(
					"ack" => 1,
					"ack_msg" => "weight List Fetched!!",
					"developer_msg" => "weight List Fetched!!",
					"result" => $weight,
				);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "weight List not Fetched!!",
					"developer_msg" => "weight List not Fetched!!"
				);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_product_weight_price' || $service == 26) {

			$product_weight_price_r = $db->rp_getData("product_weight_price", "*", "1=1", "", 0);
			if ($product_weight_price_r) {
				$product_weight_price = array();
				while ($product_weight_price_d = mysqli_fetch_assoc($product_weight_price_r)) {

					$product_weight_price[] = $product_weight_price_d;
				}
			}
			if (!empty($product_weight_price)) {

				$ack = array(
					"ack" => 1,
					"ack_msg" => "product weight price List Fetched!!",
					"developer_msg" => "product weight price List Fetched!!",
					"result" => $product_weight_price,
				);
			} else {

				$ack = array(
					"ack" => 0,
					"ack_msg" => "product weight price List not Fetched!!",
					"developer_msg" => "product weight price List not Fetched!!"
				);
			}
			$db->printJSON($ack);
		} else if ($service == 'add_sales_executive_tracking' || $service == 32) {
			$body = file_get_contents('php://input');
			$ins = false;
			if (isset($_REQUEST['sales_id']) && $body != "") {
				$sales_id		= isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";
				$tracking 	= ($body != "") ? (array)json_decode($body, true) : array();
				$adate = date('Y-m-d H:i:s');
				// print_r($tracking);exit;
				for ($i = 0; $i < sizeof($tracking['values']); $i++) {
					$t = $tracking['values'][$i];
					//tracking=[{"date":"2017-05-02 15:00","lat":"12.021","long":"12.021"}]
					$d = $t['nameValuePairs']['date'];
					$date = date('Y-m-d H:i:s', strtotime($d));
					$lat = $t['nameValuePairs']['lat'];
					$long = $t['nameValuePairs']['long'];

					if ($lat != "0.0" || $long != "0.0") {
						// $totalCount = $db->rp_getValue("salesexecutive_tracking","COUNT(*)","isDelete = 0 AND latitude = '".$lat."' AND longitude = '".$long."' AND date = '".$date."'");
						// if($totalCount<1)
						// {
						$type = $t['nameValuePairs']['type'];
						$app_address = $t['nameValuePairs']['app_address'];
						// print_r($db->common_type);exit;
						foreach ($db->common_type as $c_key => $c_value) {
							if ($c_key == $type) {
								$type_value = $c_key;
							} else {
								$type_value = "";
							}
						}
						$bearing = isset($t['nameValuePairs']['bearing']) ? $t['nameValuePairs']['bearing'] : "";

						$BatteryPercent = isset($_REQUEST['BatteryPercent']) ? $_REQUEST['BatteryPercent'] : "";
						$isGps = isset($_REQUEST['isGps']) ? $_REQUEST['isGps'] : "";
						$isWifiEnabled = isset($_REQUEST['isWifiEnabled']) ? $_REQUEST['isWifiEnabled'] : "";
						$isNetworkAvailable = isset($_REQUEST['isNetworkAvailable']) ? $_REQUEST['isNetworkAvailable'] : "";
						$NetworkType = isset($_REQUEST['NetworkType']) ? $_REQUEST['NetworkType'] : "";
						// print_r($t['nameValuePairs']);exit;
						$date = date('Y-m-d H:i:s', strtotime($d));
						$row = array(
							"sales_executive_id",
							"latitude",
							"longitude",
							"app_address",
							"date",
							"type",
							"type_value",
							"BatteryPercent",
							"isGps",
							"isWifiEnabled",
							"isNetworkAvailable",
							"NetworkType",
							"bearing"
						);
						$value = array(
							$sales_id,
							$lat,
							$long,
							$app_address,
							$date,
							$type,
							$type_value,
							$BatteryPercent,
							$isGps,
							$isWifiEnabled,
							$isNetworkAvailable,
							$NetworkType,
							$bearing
						);

						$ins = $db->rp_insert("salesexecutive_tracking", $value, $row, 0);
						//}
					}
				}
				if ($ins) {
					$where = "sales_executive_id='" . $_REQUEST['sales_id'] . "' AND isDelete=0";
					$dt = $db->rp_getData("salesexecutive_tracking", "*", $where);
					$r = array();
					if ($dt) {
						while ($row = mysqli_fetch_assoc($dt)) {
							$row['date'] = date('d-m-Y H:i:s', strtotime($row['date']));
							$row['created_date'] = date('d-m-Y H:i:s', strtotime($row['created_date']));
							$r[] = $row;
						}
					}
					$ack = array(
						"ack" => 1,
						"ack_msg" => "Sales Officer Tracking Add Successfully!!",
						"developer_msg" => "You got it!!",
						"result" => $r,
					);

					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Sorry Not add Tracking Details !! Please Try Again Later!!",
					"developer_msg" => "not inserted!!",
				);

				$db->printJSON($ack);
			}
		} else if ($service == 'get_notification' || $service == 33) {

			$user_id = $db->getRequestedParam("user_id");
			$system = new System();
			$get_notifications = $system->getNotifications($user_id);
			if ($get_notifications) {
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Successfully Get notification !!",
					"developer_msg" => "You got it!!",
					"result" => $get_notifications,
				);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "No notification Found !!",
					"developer_msg" => "No notification found!!",
				);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_reject_expense' || $service == 34) {

			if ($sales_executive_id	= (isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id'] != "") ? $_REQUEST['sales_executive_id'] : "") {
				$ctable_where .= "sales_executive_id='" . $sales_executive_id . "' AND isDelete=0 AND isActive=1 AND expense_status=2 ";

				if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
					$ctable_where .= " AND expense_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' ";
				}

				if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
					$ctable_where .= " AND expense_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ";
				}

				if (($_REQUEST['ToDate'] == "") && ($_REQUEST['FromDate'] == "")) {
					$month = date("m");
					$ctable_where .= "AND MONTH(expense_date) = '" . $month . "'";
				}

				$expense_r = $db->rp_getData("expense", "*", $ctable_where, "expense_date DESC", 0);
				if ($expense_r) {
					while ($expense_d = mysqli_fetch_assoc($expense_r)) {
						//Fetching Only Id then using function getProductDetail get Information of that product

						$img = explode(",", $expense_d['image_path']);
						$imgpath = array();
						for ($i = 0; $i < sizeof($img); $i++) {
							$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $expense_d['id'] . "' AND id='" . $img[$i] . "'", 0);
						}

						//$expense_d['image_path'] = ($expense_d['image_path']!= "")?$imgpath:"";
						$expense_d['image_path'] = ($expense_d['image_path'] != "") ? $imgpath : [];

						$expense_d['sales_name'] = $db->rp_getValue("sales_executive", "username", "id='" . $expense_d['sales_executive_id'] . "'", 0);
						$expense_d['category_name'] = $db->rp_getValue("expence_category", "name", "id='" . $expense_d['category_id'] . "'");
						$expense_d['subcategory_name'] = $db->rp_getValue("expence_sub_category", "name", "id='" . $expense_d['subcategory_id'] . "'", 0);
						$expense_d['expense_date'] = date('d-m-Y', strtotime($expense_d['expense_date']));
						$expense_d['remark'] = $expense_d['remark'] == null ? "" : $expense_d['remark'];
						$grand_total += $expense_d['total'];
						$result[] = $expense_d;
					}
				}

				if (!empty($result)) {

					$ack = array(
						"ack" => 1,
						"ack_msg" => "Rejected Expense History Found!!",
						"developer_msg" => "Rejected Expense History Found!!",
						"Grand_total" => $grand_total,
						"result" => $result,
					);
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "No Rejected Expense History Found!!",
						"developer_msg" => "Rejected Expense List not Fetched!!"
					);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Internal error!!",
					"developer_msg" => "Service Parameter missing or not valid!!"
				);
			}
			$db->printJSON($ack);
		} else if ($service == "add_no_order_inquiry" || $service == 41) {
			if (isset($_REQUEST['sales_executive_id'])) {
				$detail['sales_executive_id']	= isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$detail['customer_name']	    = isset($_REQUEST['customer_name']) ? $db->clean($_REQUEST['customer_name']) : "";
				$detail['mobile_number']	    = isset($_REQUEST['mobile_number']) ? $db->clean($_REQUEST['mobile_number']) : "";
				$detail['contact_person']	    = isset($_REQUEST['contact_person']) ? $db->clean($_REQUEST['contact_person']) : "";
				$detail['country']	            = isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
				$detail['state']	            = isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
				$detail['city']	                = isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
				$detail['description']	        = isset($_REQUEST['description']) ? $db->clean($_REQUEST['description']) : "";
				$detail['action']	            = isset($_REQUEST['action']) ? $db->clean($_REQUEST['action']) : "";
				$detail['$datetime'] = isset($_REQUEST['datetime']) ? date('Y-m-d H:s:i', strtotime($_REQUEST['datetime'])) : "0000-00-00 00:00:00";
				$reply                          = $objSalesExecutive->addNoOrderInquiry($detail);
				$db->printJSON($reply);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something wrong !! Please Try Again Later!!", "developer_msg" => "not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		} else if ($service == "update_no_order_inquiry" || $service == 42) {
			if (isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['id'])) {
				$detail['sales_executive_id']	= isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$detail['id']	                = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$detail['customer_name']	    = isset($_REQUEST['customer_name']) ? $db->clean($_REQUEST['customer_name']) : "";
				$detail['mobile_number']	    = isset($_REQUEST['mobile_number']) ? $db->clean($_REQUEST['mobile_number']) : "";
				$detail['contact_person']	    = isset($_REQUEST['contact_person']) ? $db->clean($_REQUEST['contact_person']) : "";
				$detail['country']	            = isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
				$detail['state']	            = isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
				$detail['city']	                = isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
				$detail['description']	        = isset($_REQUEST['description']) ? $db->clean($_REQUEST['description']) : "";
				$detail['action']	            = isset($_REQUEST['action']) ? $db->clean($_REQUEST['action']) : "";
				$detail['$datetime'] = isset($_REQUEST['datetime']) ? date('Y-m-d H:s:i', strtotime($_REQUEST['datetime'])) : "0000-00-00 00:00:00";
				$reply                          = $objSalesExecutive->updateNoOrderInquiry($detail, $id);
				$db->printJSON($reply);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something wrong !! Please Try Again Later!!", "developer_msg" => "not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		} else if ($service == "delete_no_order_inquiry" || $service == 43) {
			if (isset($_REQUEST['id'])) {
				$detail['id']	                = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$reply                          = $objSalesExecutive->deleteNoOrderInquiry($id);
				$db->printJSON($reply);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something wrong !! Please Try Again Later!!", "developer_msg" => "not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		} else if ($service == "list_order_inquiry" || $service == 40) {
			if (isset($_REQUEST['id'])) {
				$detail['id']	                = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$reply                          = $objSalesExecutive->listNoOrderInquiry($id);
				$db->printJSON($reply);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something wrong !! Please Try Again Later!!", "developer_msg" => "not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		} else if ($service == "update_executive_area" || $service == 50) {
			$body = file_get_contents('php://input');
			if ($body != "") {
				$areas 	= ($body != "") ? (array)json_decode($body, true) : array();
				$executive = new Executive();
				$reply   = $executive->MapExecutiveArea($areas);
				$db->printJSON($reply);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something wrong !! Please Try Again Later!!", "developer_msg" => "not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		}
		//-----------#Get Active App detail#----------------------------------------------//			
		else if ($service == 'get_active_app' || $service == 48) {


			$application_info_r  = $db->rp_getData("application_info", "version_name, version_code,type,file", "isActive=1  AND isDelete=0");
			if ($application_info_r > 0) {
				$result = array();
				$result = mysqli_fetch_assoc($application_info_r);
				$result['file'] = SITEURL . "apk/" . $result['file'];
				$result['Version message'] = "New Update <br/>Available <br/><b>" . $result['version_name'] . " vs</b><br/>for better experience <br/>with new function <br/><br/>Please<br/> Update the App";
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Active App Information Get Successfully!!",
					"developer_msg" => "You got it!!",
					"result" => $result,
					"download_path" => DOWNLOAD_PATH,
					"catalog_title" => CATALOG_TITLE,
					"visiting_card_download_path" => VISITING_DOWNLOAD_PATH,
					"visiting_card_title" => VISITING_TITLE
				);
				$db->printJSON($ack);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "No Data Avalaible!!",
					"developer_msg" => "Sorry We can't Procced!",
					"download_path" => DOWNLOAD_PATH,
					"catalog_title" => CATALOG_TITLE,
					"visiting_card_download_path" => VISITING_DOWNLOAD_PATH,
					"visiting_card_title" => VISITING_TITLE
				);
				$db->printJSON($ack);
			}
		}
		//----------------------------------------------------------------------------//

		else if ($service == 'get_dealer' || $service == 78) {
			// echo "<pre>";
			// print_r($_REQUEST);die;
			$detail = array();
			$detail['cid'] = isset($_REQUEST['cid']) ? $_REQUEST['cid'] : "";
			$detail['type'] = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";
			$detail['class_id'] = isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : "";
			$detail['area_id'] = isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : "";
			$detail['city_id'] = isset($_REQUEST['city_id']) ? $_REQUEST['city_id'] : "";
			$detail['sales_id'] = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$detail['dealer_id'] = isset($_REQUEST['dealer_id']) ? $_REQUEST['dealer_id'] : "";
			$detail['superstokist_id'] = isset($_REQUEST['superstokist_id']) ? $_REQUEST['superstokist_id'] : "";
			$detail['customer_flag'] = isset($_REQUEST['customer_flag']) ? $_REQUEST['customer_flag'] : "";
			$detail['type_of_company'] = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : "";
			$detail['is_class_area_filter'] = isset($_REQUEST['is_class_area_filter']) ? $_REQUEST['is_class_area_filter'] : "";
			$detail['is_class_area_filter']='';
			$executive = new Executive();
			if ($detail['cid'] != "") {
				//echo "hello234";exit();
				$reply = $executive->GetCustomerDetail($detail);
				$db->printJSON($reply);
			} else if (($detail['superstokist_id'] != "" || $detail['dealer_id'] != "") && $detail['sales_id'] == "") {
				//echo "hello234e4";exit();
				$reply = $executive->GetCustomerForChain($detail);
				$db->printJSON($reply);
			} else {
				//echo "helo;";exit();
				if ($detail['class_id'] != ""  && $detail['sales_id'] != "") {
					// print_r($detail);exit;
					$executive = new Executive();
					$reply   = $executive->GetDealer($detail);
					$db->printJSON($reply);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Area & Class Required", "developer_msg" => "Area & Class Required!!");
					$db->printJSON($ack);
				}
			}
		} 
		
		else if ($service == 'create_followup' || $service == 91) {
			$result = array();
			$user_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			/* description removed from API #91 — keep empty for CreateFollowup signature */
			$description = "";
			$through = isset($_REQUEST['through']) ? $_REQUEST['through'] : "";
			$followup_date = isset($_REQUEST['followup_date']) ? $_REQUEST['followup_date'] : "";
			$followup_flag = isset($_REQUEST['followup_flag']) ? $_REQUEST['followup_flag'] : "";
			$reference_id = isset($_REQUEST['reference_id']) ? $_REQUEST['reference_id'] : "";
			$entry_type = "2";
			if (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != "") {
				$visitor_id = $_REQUEST['customer_id'];
			} else {
				if ($followup_flag == "no_order_inquiry" || $followup_flag == "inquiry_followup") {
					$reference_table = "no_order_inquiry";
					$cuscol = "dealer_id";
					$visitor_id = $db->rp_getValue($reference_table, $cuscol, "id='" . $reference_id . "'");
				}
				/*if($followup_flag=="followup")
				{
					$reference_table = "sales_executive";
				}
				if($followup_flag=="sales_executive")
				{
					$reference_table = "sales_executive";
				}*/
				if ($followup_flag == "customer_followup") {
					$reference_table = "executive";
					$cuscol = "id";
					$visitor_id = $db->rp_getValue($reference_table, $cuscol, "id='" . $reference_id . "'");
				}
				if ($followup_flag == "quotation_followup" || $followup_flag == "quotation_detail") {
					$reference_table = "quotation_detail";
					$cuscol = "customer_id";
					$visitor_id = $db->rp_getValue($reference_table, $cuscol, "id='" . $reference_id . "'");
				}
			}
			// $visitor_id=isset($_REQUEST['customer_id'])?$_REQUEST['customer_id']:"";

			$followup_status = isset($_REQUEST['followup_status']) ? $_REQUEST['followup_status'] : "";

			// echo "<pre>"; print_r($_REQUEST); exit;	
			if ($through != "" && $followup_date != "") {
				$data = $ObjFollowup->CreateFollowup($user_id, $visitor_id, $description, $through, $followup_date, $followup_flag, $reference_id, $entry_type, $followup_status);
				$Get_Followup = $db->rp_getData("followup", "*", "id='" . $data['followup_id'] . "' AND isDelete=0", "");
				while ($Data_d = mysqli_fetch_assoc($Get_Followup)) {
					$result[] = $Data_d;
				}
				if ($followup_flag == "no_order_inquiry" || $followup_flag == "inquiry_followup") {
					$db->addStatusTimelineEntry($reference_id, $followup_status, $user_id);
				}
				// echo "<pre>"; print_r($result); exit;	
				$reply = array("ack" => $data['a'], "developer_msg" => $data['mg'], "ack_msg" => $data['mg'], "result" => $result);


				/*	$reference_table = $db->rp_getValue("followup","reference_table","id='".$_REQUEST['reference_id']."'",0);
				if($reference_table=='no_order_inquiry' && $_REQUEST['followup_status']!=""){
					
					$inquiry_id = $db->rp_getValue("followup","reference_id","reference_id='".$_REQUEST['reference_id']."'",0);
					$db->rp_update("no_order_inquiry",array("status"=>$followup_status),"id='".$inquiry_id."'",0);
					
				}*/
				$db->printJSON($reply);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Create Followup Failed!!", "ack_msg" => "Create Followup Failed!!");
				$db->printJSON($reply);
			}
		} else if ($service == 'add_followup_response' || $service == 92) {
			$result = array();
			$response = isset($_REQUEST['response']) ? $db->clean($_REQUEST['response']) : "";
			$followup_action = isset($_REQUEST['followup_action']) ? $_REQUEST['followup_action'] : "";
			$followup_reason_id = isset($_REQUEST['followup_reason_id']) ? $_REQUEST['followup_reason_id'] : "";
			$id = isset($_REQUEST['followup_id']) ? $_REQUEST['followup_id'] : "";
			$followup_future_date = isset($_REQUEST['followup_future_date']) ? date('Y-m-d H:i:s', strtotime($_REQUEST['followup_future_date'])) : "";
			$entry_type = "2";

			$followup_status = isset($_REQUEST['followup_status']) ? $_REQUEST['followup_status'] : "";

			if ($response != "" && $followup_action != "") {
				$data = $ObjFollowup->AddFollowupResponse($response, $followup_action, $id, $followup_future_date, $followup_reason_id, $entry_type, $followup_status);
				if ($data['a'] == 1) {
					$Get_Followup = $db->rp_getData("followup", "*", "id='" . $data['followup_id'] . "' AND isDelete=0");
					while ($Data_d = mysqli_fetch_assoc($Get_Followup)) {
						$result[] = $Data_d;
					}
					$reply = array("ack" => $data['a'], "developer_msg" => $data['dmg'], "ack_msg" => $data['mg'], "result" => $result);

					/*$reference_table = $db->rp_getValue("followup","reference_table","id='".$_REQUEST['followup_id']."'",0);
					if($reference_table=='no_order_inquiry' && $_REQUEST['followup_status']!=""){

						$inquiry_id = $db->rp_getValue("followup","reference_id","id='".$_REQUEST['followup_id']."'",0);
						$db->rp_update("no_order_inquiry",array("status"=>$followup_status),"id='".$inquiry_id."'",0);

					}*/

					$db->printJSON($reply);
				} else {
					$reply = array("ack" => $data['a'], "developer_msg" => $data['dmg'], "ack_msg" => $data['mg']);
					$db->printJSON($reply);
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Followup Response Required!!", "ack_msg" => "Followup Response Required!!");
				$db->printJSON($reply);
			}
		} else if ($service == 'get_all_followup' || $service == 93) {
			$visitor_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
			$user_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$reference_id = isset($_REQUEST['reference_id']) ? $_REQUEST['reference_id'] : "";
			$followup_type = isset($_REQUEST['followup_type']) ? $_REQUEST['followup_type'] : "";
			if ($_REQUEST['customer_id'] || isset($_REQUEST['reference_id'])) {
				$ack = $ObjFollowup->GetFollowupContent($visitor_id, $reference_id, $user_id);
				$db->printJSON($ack);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Visitor Id Not Found!!", "ack_msg" => "Visitor Id Not Found!!");
				$db->printJSON($reply);
			}
		} else if ($service == 'update_followup_response' || $service == 94) {
			$result = array();
			$response = isset($_REQUEST['response']) ? $db->clean($_REQUEST['response']) : "";
			$id = isset($_REQUEST['followup_id']) ? $_REQUEST['followup_id'] : "";
			if ($response != "" && $id != "") {
				$data = $ObjFollowup->EditFollowupResponse($response, $id);
				if ($data['a'] == 1) {
					$Get_Followup = $db->rp_getData("followup", "*", "id='" . $data['followup_id'] . "' AND isDelete=0");
					while ($Data_d = mysqli_fetch_assoc($Get_Followup)) {
						$result[] = $Data_d;
					}
					$reply = array("ack" => $data['a'], "developer_msg" => $data['dmg'], "ack_msg" => $data['mg'], "result" => $result);
					$db->printJSON($reply);
				} else {
					$reply = array("ack" => $data['a'], "developer_msg" => $data['dmg'], "ack_msg" => $data['mg']);
					$db->printJSON($reply);
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Followup Response Required!!", "ack_msg" => "Followup Response Required!!");
				$db->printJSON($reply);
			}
		} else if ($service == 'get_today_followup' || $service == 95) {
			$user_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$limit = $ObjFollowup->getLimit();
			if ($user_id != "") {
				$ack = $ObjFollowup->GetTodayFollowup($user_id, $limit);
				$db->printJSON($ack);
			} else {
				$reply = array("ack" => 0, "ack_msg" => "User id Required!!", "developer_msg" => "User id Required!!");
				echo json_encode($reply);
			}
		} else if ($service == 'get_followup_detail' || $service == 96) {
			$followup_id = isset($_REQUEST['followup_id']) ? $_REQUEST['followup_id'] : "";
			if ($followup_id != "") {
				$ack = $ObjFollowup->GetFollowupDetail($followup_id);
				$db->printJSON($ack);
			} else {
				$reply = array("ack" => 0, "ack_msg" => "User id Required!!", "developer_msg" => "User id Required!!");
				echo json_encode($reply);
			}
		} else if ($service == 'delete_followup' || $service == 97) {
			$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
			if ($id) {
				$rows 	= array("isDelete"	=> 1,);
				$where	= "id='" . $_REQUEST['id'] . "'";
				$delete_followup = $db->rp_update("followup", $rows, $where);
				if ($delete_followup) {
					$reply = array("ack" => 1, "developer_msg" => "Followup Deleted Successfully", "ack_msg" => "Followup Deleted Successfully");
					$db->printJSON($reply);
				} else {
					$reply = array("ack" => 0, "developer_msg" => "Followup Not Deleted", "ack_msg" => "Followup Not Deleted");
					$db->printJSON($reply);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
				$db->printJSON($ack);
			}
		} else if ($service == "update_expense" || $service == 124) {
			if (isset($_REQUEST['sales_executive_id'])) {
				$detail['id']	                =  isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
				$detail['sales_executive_id']	=  isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
				$detail['category_id']			=  isset($_REQUEST['category_id']) ? $db->clean($_REQUEST['category_id']) : "";
				$detail['subcategory_id']		=  isset($_REQUEST['subcategory_id']) ? $db->clean($_REQUEST['subcategory_id']) : "";
				$detail['expense_date']	        =  date('Y-m-d H:s:i');
				$detail['expense_type']	        =  isset($_REQUEST['expense_type']) ? $db->clean($_REQUEST['expense_type']) : "";
				$detail['start_kilometer']	    =  isset($_REQUEST['start_kilometer']) ? $db->clean($_REQUEST['start_kilometer']) : "";
				$detail['end_kilometer']	    =  isset($_REQUEST['end_kilometer']) ? $db->clean($_REQUEST['end_kilometer']) : "";
				$detail['total_kilometer']	    =  isset($_REQUEST['total_kilometer']) ? $db->clean($_REQUEST['total_kilometer']) : "";
				$detail['total']	            =  isset($_REQUEST['total']) ? $db->clean($_REQUEST['total']) : "";
				$detail['remark']	            =  isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
				$detail['created_date']         =  date('Y-m-d H:s:i');

				$reply = $objExpense->UpdateExpense_service($detail, $_FILES);
				if ($reply['ack'] == 1) {
					$result = $db->rp_getData("expense", "*", "id='" . $reply['inserted_id'] . "'", "", 0);
					$r = mysqli_fetch_assoc($result);
					$r['username'] = $db->rp_getValue("sales_executive", "username", "id='" . $r['sales_executive_id'] . "'", 0);
					$r['expense_date'] = date('d-m-Y', strtotime($r['expense_date']));
					$r['created_date'] = date('d-m-Y H:i:s', strtotime($r['created_date']));
					$ack = array("ack" => 1, "ack_msg" => "Expense Detail Update Successfully!!", "developer_msg" => "You got it!!", "result" => $r);
					$db->printJSON($ack);
				} else {

					$db->printJSON($reply);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Something wrong !! Please Try Again Later!!", "developer_msg" => "not Update!! sales id not get!!");
				$db->printJSON($ack);
			}
		} else if ($service == "document_type_get" || $service == 193) {
			$result = array();
			$document_type = array();
			$document_type_r = $db->rp_getData("document_type", "*", "isDelete=0", "display_order ASC", 0);
			// get data for single table only 
			if ($document_type_r) {
				while ($document_type_d = mysqli_fetch_assoc($document_type_r)) {
					$document_type[] = $document_type_d;
				}
			}

			if (!empty($document_type)) {
				$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $document_type);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
			}
			$db->printJSON($ack);
		} else if ($service == 'get_city_usingclass_id' || $service == 195) {
			if (isset($_REQUEST['class_id']) && $_REQUEST['class_id'] != "") {
				$class_id = $_REQUEST['class_id'];
				$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
				$class = new ClassType();
				$ack = $class->getcityDetail_usingClassId($class_id, $sales_id);
				/*$ack=array( "ack"=>1,
							"ack_msg"=>"Area Fetched Successfully  !!",
							"developer_msg"=>"You got it!!",
							"result"=>$area,
							);*/
				$db->printJSON($ack);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Area Not Fetched !!",
					"developer_msg" => "Wrong email or password",
					"result" => $area,
				);
				$db->printJSON($ack);
			}
		} else if ($service == 'add_order_status' || $service == 196) {
			if (isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != "") {
				$order_id = $_REQUEST['order_id'];
				$status = 1;
				$customer_id = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : "";
				$Update = $db->rp_update("orders", array("status" => $status, "approved_by_chain_id" => $customer_id), "id='" . $order_id . "'", 0);
				if ($Update) {

					if ($status == 1) {
						$txt = "Approved";
					} else if ($status == -2) {
						$txt = "Dispproved";
					} else if ($status == 3) {
						$txt = "Cancelled";
					}

					$order_no  = $db->rp_getValue("orders", "order_no", "id='" . $order_id . "'");
					$notification_description = "Order No " . $order_no . " has been " . $txt;

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

					$ack = array("ack" => 1, "ack_msg" => "Status update successfully", "developer_msg" => "You got it!!");
				} else {
					$ack = array("ack" => 0, "ack_msg" => "Status update failed Something went wrong!!", "developer_msg" => "Status update failed Something went wrong!!");
				}

				$db->printJSON($ack);
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Order Id Required",
					"developer_msg" => "Wrong email or password",

				);
				$db->printJSON($ack);
			}
		} else if ($server == "update_customer_location_coordinates" || $service == 207) {
			$latitude = isset($_REQUEST['latitude']) ? $_REQUEST['latitude'] : "";
			$longitude = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] : "";
			if ($_REQUEST['latitude'] != "0.0" && $_REQUEST['latitude'] != "" && $_REQUEST['longitude'] != "0.0" && $_REQUEST['longitude'] != "") {


				if (isset($_REQUEST['id']) && $_REQUEST['id'] != "") {

					$sales_name = $db->rp_getValue("sales_executive", "name", "id='" . $_REQUEST['sales_id'] . "'", 0);
					$module_name = "customer";
					$flag = "Application";
					$log_description = $module_name . " id " . $_REQUEST['id'] . " location update by " . $sales_name . " ON " . date("Y-m-d H:i:s");


					$update = $db->rp_update("executive", array("longitude" => $longitude, "latitude" => $latitude), "id='" . $_REQUEST['id'] . "'", 0, $log_description, $flag, $module_name, $_REQUEST['sales_id']);

					if ($update) {
						$ack = array("ack" => 1, "ack_msg" => "Customer location updated");
						echo json_encode($ack);
					} else {
						$ack = array("ack" => 0, "ack_msg" => "Update failed");
						echo json_encode($ack);
					}
				} else {
					$ack = array(
						"ack" => 0,
						"ack_msg" => "Customer ID required!!",
						"developer_msg" => "Customer ID required!!",
					);
					$db->printJSON($ack);
				}
			} else {
				$ack = array(
					"ack" => 0,
					"ack_msg" => "Location not get",
					"developer_msg" => "Inquiry ID required!!",
				);
				$db->printJSON($ack);
			}
		} else if ($service == "add_advance_expense" || $service == 230) {
			// Advance: category + total + remark. Sub Category NOT required.
			$sales_executive_id = isset($_REQUEST['sales_executive_id']) ? $db->clean($_REQUEST['sales_executive_id']) : "";
			$category_id = isset($_REQUEST['category_id']) ? $db->clean($_REQUEST['category_id']) : "";
			$advance_expense_type = isset($_REQUEST['advance_expense_type']) ? $db->clean($_REQUEST['advance_expense_type']) : "";
			$total = isset($_REQUEST['total']) ? $db->clean($_REQUEST['total']) : "";
			if ($total === "" && isset($_REQUEST['amount'])) {
				$total = $db->clean($_REQUEST['amount']);
			}

			// Map static Advance Type 1/2 → category when category_id not sent
			if ($category_id == "" && ($advance_expense_type == "1" || $advance_expense_type == "2")) {
				$advNameMap = array(
					"1" => "Advance Brand Approval Expense",
					"2" => "Advance Travelling Expense",
				);
				$advName = $advNameMap[$advance_expense_type];
				$advEsc = mysqli_real_escape_string($db->myconn, $advName);
				$category_id = $db->rp_getValue("expence_category", "id", "name='" . $advEsc . "' AND isDelete=0 AND expense_claim_type=2", 0);
				if (!$category_id) {
					$category_id = $db->rp_getValue("expence_category", "id", "name LIKE 'Advance%" . ($advance_expense_type == "1" ? "Brand" : "Travell") . "%' AND isDelete=0 AND expense_claim_type=2", 0);
				}
			}

			if ($sales_executive_id != "" && $category_id != "" && $total != "") {
				$detail['sales_executive_id'] = $sales_executive_id;
				$detail['category_id'] = $category_id;
				$detail['subcategory_id'] = 0; // not required for Advance
				$detail['total'] = $total;
				$detail['remark'] = isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
				$detail['entry_flag'] = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "5";
				$detail['expense_date'] = date('Y-m-d H:i:s');
				$detail['advance_expense_type'] = ($advance_expense_type != "") ? $advance_expense_type : 0;

				$reply = $objExpense->InsertAdvanceExpense($detail, $_FILES);
				if ($reply['ack'] == 1) {
					$result = $db->rp_getData("expense", "*", "id='" . $reply['inserted_id'] . "'", "", 0);
					$r = mysqli_fetch_assoc($result);
					$r['username'] = $db->rp_getValue("sales_executive", "username", "id='" . $r['sales_executive_id'] . "'", 0);
					$r['category_name'] = $db->rp_getValue("expence_category", "name", "id='" . $r['category_id'] . "'", 0);
					$r['expense_date'] = date('d-m-Y', strtotime($r['expense_date']));
					$r['created_date'] = date('d-m-Y H:i:s', strtotime($r['created_date']));
					$r['subcategory_required'] = "0";
					$ack = array("ack" => 1, "ack_msg" => "Advance Expense Added Successfully!!", "developer_msg" => "Advance expense inserted", "result" => $r);
					$db->printJSON($ack);
				} else {
					$db->printJSON($reply);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "sales_executive_id, category_id (or advance_expense_type) and total are required!! Sub Category is not required for Advance.", "developer_msg" => "Required params missing for add_advance_expense", "subcategory_required" => "0");
				$db->printJSON($ack);
			}
		}
	} else {
		$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Check your API Key or contact Admin", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
		$db->printJSON($ack);
	}
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
	$email = $nt->aj_sendSecurityCode($email, "Security Check " . SITENAME . "", $body);
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
