
<?php
// echo "hello";exit();
require_once("main.class.php");
require_once("function.class.php");
class Executive extends Functions
{
	public $db;
	public $ctable = "executive";
	public $ctableMap = "executive_map_area";

	function __construct($id = "")
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db = $db;
	}

	function executiveDupWhere($mobile, $client_code, $exclude_id = '', $company_name = '')
	{
		$dup_parts = array();
		if ($mobile != "") {
			$dup_parts[] = "mobile_no1 = '" . $mobile . "'";
		}
		if ($client_code != "") {
			$dup_parts[] = "client_code = '" . $client_code . "'";
		}
		if (empty($dup_parts)) {
			return "";
		}
		$where = "(" . implode(" OR ", $dup_parts) . ") AND isDelete=0";
		if ($company_name != "") {
			$where .= " AND company_name = '" . $company_name . "'";
		}
		if ($exclude_id != "") {
			$where .= " AND id != '" . (int)$exclude_id . "'";
		}
		return $where;
	}

	function executiveDupWhereForUpdate($mobile, $client_code, $executive_id, $original_mobile, $original_client_code, $company_name = '')
	{
		return $this->executiveDupWhere($mobile, $client_code, $executive_id, $company_name);
	}

	function getStateClientCodePrefix($state, $class_id = '')
	{
		$state_map = array(
			'gujarat' => 'GJ',
			'maharashtra' => 'MH',
			'madhya pradesh' => 'MP',
			'rajasthan' => 'RJ',
			'karnataka' => 'KA',
			'tamil nadu' => 'TN',
			'uttar pradesh' => 'UP',
			'west bengal' => 'WB',
			'andhra pradesh' => 'AP',
			'telangana' => 'TS',
			'kerala' => 'KL',
			'punjab' => 'PB',
			'haryana' => 'HR',
			'bihar' => 'BR',
			'odisha' => 'OD',
			'chhattisgarh' => 'CG',
			'goa' => 'GA',
			'delhi' => 'DL',
		);

		$state_key = strtolower(trim($state));
		if ($state_key != '' && isset($state_map[$state_key])) {
			return $state_map[$state_key];
		}

		if ($class_id != '') {
			$slug = strtolower(trim($this->db->rp_getValue("class", "slug", "id='" . (int)$class_id . "' AND isDelete=0", 0)));
			if ($slug != '' && isset($state_map[$slug])) {
				return $state_map[$slug];
			}
			$class_name = strtolower(trim($this->db->rp_getValue("class", "name", "id='" . (int)$class_id . "' AND isDelete=0", 0)));
			if ($class_name != '' && isset($state_map[$class_name])) {
				return $state_map[$class_name];
			}
		}

		return '';
	}

	function resolveClassIdFromState($state, $class_id = '')
	{
		if ($class_id != '' && $class_id != '0') {
			return $class_id;
		}
		if ($state == '') {
			return $class_id;
		}
		$resolved = $this->db->rp_getValue("class", "id", "name LIKE '%" . $this->db->clean($state) . "%' AND isDelete=0", 0);
		return ($resolved !== false && $resolved !== '') ? $resolved : $class_id;
	}

	function generateClientCode($state, $class_id = '', $type_of_company = '')
	{
		$prefix = $this->getStateClientCodePrefix($state, $class_id);
		$code = '';
		$client_code = '';

		if ($prefix != '') {
			$state_clean = $this->db->clean($state);
			$where = "isDelete=0 AND state LIKE '%" . $state_clean . "%' AND client_code REGEXP '^" . $prefix . "[0-9]+$' ORDER BY id DESC LIMIT 1";
			$last_code = $this->db->rp_getValue("executive", "client_code", $where, 0);
			if ($last_code && preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $last_code, $matches)) {
				$code = (int)$matches[1] + 1;
			} else {
				$code = 1;
			}
			$client_code = $prefix . $code;
		} elseif ($type_of_company != '') {
			$prefix = $this->db->rp_getValue("company_master", "prefix", "id='" . (int)$type_of_company . "' AND isDelete=0", 0);
			$last_insert_ids = $this->db->rp_getValue("executive", "MAX(`client_code_sr_by_type`)", "type_of_company='" . (int)$type_of_company . "' AND isDelete=0", 0);
			$code = str_pad(((int)$last_insert_ids + 1), 4, '0', STR_PAD_LEFT);
			$client_code = $prefix . $code;
		}

		return array("client_code" => $client_code, "client_code_sr_by_type" => $code);
	}

	//-------#Insert Executive Detail------------------------------//	

	/*insert executive in admin*/
	public function InsertExecutivePanel($end_user_type, $type_of_executive, $company_type, $company_name, $address, $address2 = "", $super_stockist_id, $zone, $city, $state, $country, $image_path, $email, $email_cc, $dealer_distributor_id, $cname, $cst, $pan, $phone, $gst, $vat, $inquiry_date, $zip, $excise, $class_id, $mobile_no1, $whatsapp_no, $area_id, $discount, $price_list_id, $vendor_desk, $office_supplier, $gst_detail, $other_image, $seid = "", $local_id = "", $type = "", $latitude = "", $longitude = "", $client_code, $industry_type, $cash_discount, $additional_discount, $credit_limit, $credit_day, $shipping_address, $billing_address, $remark, $password, $file, $entry_flag, $credit_debit_type, $openinig_balance, $client_code_sr_by_type, $top_cat_id, $customer_insert_flag, $customer_update_flag, $order_view_flag, $order_insert_flag, $order_update_flag, $dealer_order_view_flag, $dealer_order_insert_flag, $outlets_order_insert_flag, $main_city, $order_approve_flag = "", $top_category_id, $outlets_order_view_flag, $type_of_company, $booking_place, $transport_by_id, $transporter_id, $purchasing_from, $customer_name, $turnover, $turnover_year, $channel_partner_flag = 0)
	{


		// echo "<pre>"; print_r($file);exit;
		/*$dup_where = "phone = '" . $phone . "' AND isDelete=0";*/
		/*if($gst!="")
		{
			$dup_where = "gst = '" . $gst . "' AND isDelete=0";
		}
		else
		{
			$dup_where = "company_name = '" . $company_name . "' AND phone = '".$phone."' AND isDelete=0";
		}*/

		//$dup_where = "(gst = '" . $gst . "' AND phone = '" . $phone . "' OR client_code = '".$client_code."') AND isDelete=0";
		//$dup_where = "(gst = '" . $gst . "' AND client_code = '".$client_code."') AND isDelete=0";

		// $dup_where = "(mobile_no1 = '" . $mobile_no1 . "' AND company_name = '".$company_name."') AND isDelete=0";
		// $dup_where = " (mobile_no1 = '" . $mobile_no1 . "' OR client_code = '" . $client_code . "') AND company_name = '" . $company_name . "' AND isDelete=0";
		$class_id = $this->resolveClassIdFromState($state, $class_id);
		if ($client_code == "") {
			$generated = $this->generateClientCode($state, $class_id, $type_of_company);
			$client_code = $generated['client_code'];
			$client_code_sr_by_type = $generated['client_code_sr_by_type'];
		}
		$dup_where = $this->executiveDupWhere($mobile_no1, $client_code);

		if ($type == "") {
			$r = ($dup_where != "") ? $this->db->rp_dupCheck($this->ctable, $dup_where, 0) : 0;
		} else {
			$r = false;
		}

		if ($r) {
			$reply = array("ack" => 0, "developer_msg" => "Mobile number already assigned to another customer!! Try another number.", "ack_msg" => "A mobile number or client code already exists, or the company name is already associated with another customer. Please check.");
			// $reply = array("ack" => 0, "developer_msg" => "GST number OR Phone no AND Client Code already Exists in another customer!! Please Check.", "ack_msg" => "GST number OR Phone no AND already Exists in another customer!! Please Check.");
			return $reply;
		} else {
			if ($type_of_executive == "outlets")
				$super_stockist_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $dealer_distributor_id . "'", 0);
			$adate	= date('Y-m-d H:i:s');
			$rows = array(
				"company_type",
				"type_of_executive",
				"company_name",
				"cname",
				"super_stockist_id",
				"dealer_distributor_id",
				"email",
				"email_cc",
				"cst",
				"pan",
				"gst",
				"vat",
				"excise",
				//"phone",
				"address",
				"address2",
				"zip",
				"country",
				"state",
				"city",
				"zone",
				// "image_path",
				"isActive",
				"class_id",
				"mobile_no1",
				"whatsapp_no",
				"discount",
				"adate",
				"seid",
				"modify_date",
				"price_list_id",
				"latitude",
				"longitude",
				"vendor_desk",
				"office_supplier",
				"gst_detail",
				"other_image",
				"customer_from",
				"client_code",
				"industry_type_id",
				"cash_discount",
				"additional_discount",
				"credit_limit",
				"credit_day",
				// "shipping_address",
				"billing_address",
				"remark",
				"password",
				"entry_flag",
				"credit_debit_type",
				"openinig_balance",
				"client_code_sr_by_type",
				// "category_id",
				"customer_insert_flag",
				"customer_update_flag",
				"order_view_flag",
				"order_insert_flag",
				"order_update_flag",
				"dealer_order_view_flag",
				"dealer_order_insert_flag",
				"outlets_order_view_flag",
				"outlets_order_insert_flag",
				"main_city",
				"order_approve_flag",
				"top_category_id",
				"type_of_company",
				"booking_place",
				"transport_by_id",
				"transporter_id",
				"purchasing_from",
				"booking_pincode",
				"turnover",
				"turnover_year",
				"channel_partner_flag",
			);
			$values = array(
				//				$rtid,
				$company_type,
				$type_of_executive,
				$company_name,
				$cname,
				$super_stockist_id,
				$dealer_distributor_id,
				$email,
				$email_cc,
				isset($cst) ? $cst : "",
				$pan,
				$gst,
				isset($vat) ? $vat : "",
				isset($excise) ? $excise : "",
				//$phone,
				$address,
				$address2,
				$zip,
				$country,
				$state,
				$city,
				$zone,
				// $image_path,
				"1",
				$class_id,
				$mobile_no1,
				$whatsapp_no,
				0,
				$adate,
				$seid,
				$adate,
				$price_list_id,
				$latitude,
				$longitude,
				$vendor_desk,
				$office_supplier,
				$gst_detail,
				$other_image,
				0,
				$client_code,
				$industry_type,
				$cash_discount,
				$additional_discount,
				$credit_limit,
				$credit_day,
				// $shipping_address,
				$billing_address,
				$remark,
				$password,
				$entry_flag,
				$credit_debit_type,
				$openinig_balance,
				$client_code_sr_by_type,
				// $category_id,
				($customer_insert_flag) ? $customer_insert_flag : "",
				($customer_update_flag) ? $customer_update_flag : "",
				($order_view_flag) ? $order_view_flag : "",
				($order_insert_flag) ? $order_insert_flag : "",
				($order_update_flag) ? $order_update_flag : "",
				($dealer_order_view_flag) ? $dealer_order_view_flag : "",
				($dealer_order_insert_flag) ? $dealer_order_insert_flag : "",
				($outlets_order_view_flag) ? $outlets_order_view_flag : "",
				($outlets_order_insert_flag) ? $outlets_order_insert_flag : "",
				$main_city,
				$order_approve_flag,
				$top_category_id,
				$type_of_company,
				$booking_place,
				$transport_by_id,
				$transporter_id,
				$purchasing_from,
				$zip,
				$turnover,
				$turnover_year,
				($channel_partner_flag) ? $channel_partner_flag : 0,

			);

			// echo "<pre>"; print_r($values); echo "<br>";
			// echo "<pre>"; print_r($rows); exit;
			$uid = $this->db->rp_insert($this->ctable, $values, $rows, 0);

			if ($uid != 0) {

				$count_value = 0;
				foreach ($_REQUEST['shipping_address'] as $key) {

					$item_rows = array("customer_id", "shipping_address");
					// $item_values = array($uid,$key);
					$item_values = array($uid, addslashes(html_entity_decode($key)));
					$item_id = $this->db->rp_insert("customer_vs_shipping_address", $item_values, $item_rows, 0);
				}

				foreach ($_REQUEST['phone'] as $key) {
					$item_rows = array("customer_id", "phone_no", "name", "ref_table");
					$item_values = array($uid, addslashes(html_entity_decode($key)), $_REQUEST['customer_name'][$count_value], "executive");
					$count_value++;
					$item_id = $this->db->rp_insert("customer_vs_phone_no", $item_values, $item_rows, 0);
				}
			}


			/*add image*/
			if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
				$ri = $uid;
				$rt = "executive";
				$tc = "executive";
				$rc = "id";
				//for ($i = 0; $i < sizeof($file["image_path"]['name']); $i++) {
				//print_r($file["image_path"]);
				$file_name = $file['image_path']['name'];
				$file_size = $file['image_path']['size'];
				$file_tmp = $file['image_path']['tmp_name'];
				$file_type = $file['image_path']['type'];
				$extension = explode(".", $file_name);

				$allowed_extentions = array("jpg", "jpeg", "png", "JPEG", "JPEG", "PNG");
				$extension = $extension[sizeof($extension) - 1];
				if (!in_array($extension, $allowed_extentions)) {
					$file_error = true;
				}
				$orignal_file_name = $extension[0];
				if (in_array($extension, $allowed_extentions)) {
					$attachment = "../resource/image/";
					move_uploaded_file($file_tmp, $attachment . $file_name);
				}
				$MediaTitle = $file_name;
				$MediaOrignalTitle = $file_name;

				$MediaFileName = $file_name;
				// $MediaType=User::$ValidMediaType[$extension];
				$UploadDate = date("Y-m-d H:i:s");

				//$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
				//echo $file_name;exit;
				$Values = array($MediaTitle, $MediaOrignalTitle, $MediaFileName, $extension, $UploadDate, $ri, $rt, $tc);
				// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
				$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");
				$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);

				$image_path = $MediaID;
				//}
				//$image_path = implode(",", $image_path);
				$upadateid = $this->db->rp_update($this->ctable, array("image_path" => $image_path), "id='" . $uid . "'", 0);
			}
			/*add image*/

			// echo $uid; exit();

			$this->addOutletsBranch($uid, $company_name, 1);
			if ($uid != 0) {
				/*create Account*/

				$CreateAccount = $this->CreateCustomerAccount($uid);

				/*create Account*/


				// update account transaction openinig balance
				// get financial year date
				$date = "01";
				$month = "04";
				$year  = date('Y');
				// $time = date('h:i:s');
				$created_date = $year . "-" . $month . "-" . $date;
				$transaction_date = $year . "-" . $month . "-" . $date;
				// get financial year date

				$debit = $openinig_balance * -1;
				$description = $created_date . " Opening Balance RS " . $openinig_balance;

				$acc_id = $this->rp_getValue("account", "id", "cid='" . $uid . "'");
				$acc_no = $this->rp_getValue("account", "acc_no", "cid='" . $uid . "'");

				if ($credit_debit_type == 1) {
					$rows = array(
						"reference_table",
						"reference_id",
						"cid",
						"account_id",
						"account_no",
						"opening",
						"amount",
						"credit",
						"debit",
						"type",
						"description",
						"payment_date",
					);
					$value =  array(
						"customer",
						$uid,
						$uid,
						$acc_id,
						$acc_no,
						1,
						$openinig_balance,
						$openinig_balance,
						"",
						1,
						$description,
						$transaction_date,
					);

					$insert = $this->rp_insert("account_transaction", $value, $rows, 0);
				} else {
					$rows = array(
						"reference_table",
						"reference_id",
						"cid",
						"account_id",
						"account_no",
						"opening",
						"amount",
						"credit",
						"debit",
						"type",
						"description",
						"payment_date",
					);
					$value =  array(
						"customer",
						$uid,
						$uid,
						$acc_id,
						$acc_no,
						1,
						$openinig_balance,
						"",
						$debit,
						2,
						$description,
						$transaction_date,
					);

					$insert = $this->rp_insert("account_transaction", $value, $rows, 0);
				}
				// update account transaction openinig balance

				// $ack = $this->addArea($uid, $type_of_executive, $class_id, $item);
				/*$activationCode=$this->generateActivationCode();
				$activationCode_md5=md5($activationCode);
				$executive_r=$this->db->rp_getData($this->ctable,"*","id ='".$uid."'");
				$executive=mysqli_fetch_assoc($executive_r);
					$sms="Hello ".$executive['cname']."\nWelcome to ".SITETITLE.", Your login credentials for account given below:\nMobile:".$executive['phone']."\npassword:".$activationCode."\nTeam ".SITETITLE;
				$a=$this->aj_sendSMS($executive['phone'],$sms);
				$rows 	= array(
							"password"	=> $activationCode_md5,
							);
				$this->db->rp_update($this->ctable,$rows,"id='".$uid."'",0);*/



				$class_id = $this->db->rp_getValue("class", "id", "name LIKE '%" . strtolower(trim($state)) . "%'", 0);

				$city_id = $this->db->rp_getValue("city", "id", "name LIKE '%" . strtolower(trim($main_city)) . "%'", 0);

				if ($city != "" && !empty($city)) {
					$area_id = $this->db->rp_getValue("area", "id", "class_id='" . $class_id . "' AND city_id='" . $city_id . "' AND name LIKE '%" . strtolower(trim($city)) . "%'", 0);
				} else {
					$area_id = $this->db->rp_getValue("area", "id", "class_id='" . $class_id . "' AND city_id='" . $city_id . "' AND name LIKE '%" . strtolower(trim($main_city)) . "%'", 0);
				}

				/*add arae code*/
				// $city_id = $this->db->rp_getValue( "area", "city_id", "isDelete=0 AND id='".$area_id."'", 0 );

				$this->db->rp_delete($this->ctableMap, "executive_id=" . $uid . "", 0);
				//echo  $type_of_executive;exit;
				if ($type_of_executive == '3') {
					// Check if dealer and its super stockist has this area or not if not then add to them first_area
					$outlet_detail = $this->db->rp_getData("executive", "super_stockist_id,dealer_distributor_id", "id='" . $uid . "'", "", 0);
					if ($outlet_detail) {
						$outlet_detail = mysqli_fetch_assoc($outlet_detail);
						if ($outlet_detail['dealer_distributor_id'] != "0") {
							$dealer_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['dealer_distributor_id'] . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
							if ($dealer_has_area_or_not <= 0) {
								$mapping_id_dd = $this->db->rp_insert($this->ctableMap, array($outlet_detail['dealer_distributor_id'], "dealer", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
							}
						}

						if ($outlet_detail['super_stockist_id'] != "") {
							//$super_stockist_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $outlet_detail['super_stockist_id'] . "'", 0);
							$super_stockist_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['super_stockist_id'] . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
							if ($super_stockist_has_area_or_not <= 0) {
								$mapping_id_ss = $this->db->rp_insert($this->ctableMap, array($outlet_detail['super_stockist_id'], "super_stockist", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
							}
						}
					}
				}
				if ($type_of_executive == '5') {
					// Check if dealer and its super stockist has this area or not if not then add to them first_area
					$outlet_detail = $this->db->rp_getData("executive", "super_stockist_id,dealer_distributor_id,dealer_id", "id='" . $uid . "'", "", 0);
					if ($outlet_detail) {
						$outlet_detail = mysqli_fetch_assoc($outlet_detail);
						if ($outlet_detail['dealer_distributor_id'] != "0") {
							$distributor_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['dealer_distributor_id'] . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
							if ($distributor_has_area_or_not <= 0) {
								$mapping_id_dd = $this->db->rp_insert($this->ctableMap, array($outlet_detail['dealer_distributor_id'], "dealer", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
							}
						}

						if ($outlet_detail['dealer_id'] != "0") {
							$dealer_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['dealer_id'] . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
							if ($dealer_has_area_or_not <= 0) {
								$mapping_id_dd = $this->db->rp_insert($this->ctableMap, array($outlet_detail['dealer_id'], "outlate", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
							}
						}

						if ($outlet_detail['super_stockist_id'] != "0") {
							$super_stockist_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['super_stockist_id'] . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
							if ($super_stockist_has_area_or_not <= 0) {
								$mapping_id_ss = $this->db->rp_insert($this->ctableMap, array($outlet_detail['super_stockist_id'], "super_stockist", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
							}
						}
						//$super_stockist_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $outlet_detail['super_stockist_id'] . "'", 0);

					}
				} else if ($type_of_executive == '2') {
					// Check if dealer and its super stockist has this area or not if not then add to them first_area
					$outlet_detail = $this->db->rp_getData("executive", "super_stockist_id,dealer_distributor_id", "id='" . $uid . "'", "", 0);
					if ($outlet_detail) {
						$outlet_detail = mysqli_fetch_assoc($outlet_detail);

						//$super_stockist_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $outlet_detail['super_stockist_id'] . "'", 0);
						$super_stockist_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['super_stockist_id'] . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
						if ($super_stockist_has_area_or_not <= 0) {
							$mapping_id_ss = $this->db->rp_insert($this->ctableMap, array($outlet_detail['super_stockist_id'], "super_stockist", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
						}
					}
				}

				if ($area_id != "" && !empty($area_id)) {

					$mapping_id = $this->db->rp_insert($this->ctableMap, array($uid, $type_of_executive, $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
				}
				//$mapping_id = $this->db->rp_insert("executive_map_area",array($uid,$type_of_executive,$class_id,$area_id,$city_id),array("executive_id","executive_type","class_id","area_id","city_id"),0);


				$reply = array("ack" => 1, "developer_msg" => "insert Successfully", "ack_msg" => "Success! Insert Customer Successfully.", "inserted_id" => $uid, "areas" => $ack['areas']);
				return $reply;
			} else {
				$dbError = isset($this->db->myconn) ? mysqli_error($this->db->myconn) : '';
				$reply = array(
					"ack" => 0,
					"developer_msg" => "Database error!!" . ($dbError != '' ? " " . $dbError : ""),
					"ack_msg" => "Failed! Insert Record Failed."
				);
				return $reply;
			}
		}
	}
	/*insert executive in admin*/


	/*public function InsertExecutive($end_user_type,$type_of_executive,$company_type,$company_name,$address,$super_stockist_id,$city,$state,$country,$image_path,$email,$dealer_distributor_id,$cname,$cst,$pan,$phone,$gst,$vat,$inquiry_date,$zip,$excise,$class_id,$mobile_no1,$item,$discount,$price_list_id,$seid="",$local_id="",$type="",$file) */
	public function InsertExecutive($detail, $file)
	{
		extract($detail);
		//if ($type_of_executive == "outlets")
		//	$super_stockist_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" .$dealer_distributor_id. "'","",0);
		// $lastInsertId=$this->db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive='".$type_of_executive."'"); 

		//$lastInsertId=$this->db->rp_getTotalRecord("executive","isDelete=0 AND zone='".$zone."'",0);  

		$class_id = $this->resolveClassIdFromState($state, $class_id);
		if (empty($detail['client_code'])) {
			$generated = $this->generateClientCode($state, $class_id, $type_of_company);
			$client_code = $generated['client_code'];
			$code = $generated['client_code_sr_by_type'];
		} else {
			$code = isset($detail['client_code_sr_by_type']) ? $detail['client_code_sr_by_type'] : "";
		}


		//$dup_where = "phone = '" . $phone . "' AND isDelete=0";
		//$dup_where = "(gst = '" . $gst_no . "' AND client_code = '" . $client_code . "') AND isDelete=0";
		// $dup_where = "(mobile_no1 = '" . $mobile_number . "' AND company_name='".$company_name."') AND isDelete=0";
		$dup_where = $this->executiveDupWhere($mobile_number, $client_code, '', $company_name);
		if ($type == "") {
			$r = ($dup_where != "") ? $this->db->rp_dupCheck($this->ctable, $dup_where, 0) : 0;
		} else {
			$r = false;
		}

		if ($r) {
			//$reply = array("ack" => 0, "developer_msg" => "Phone number already assigned to another customer!! Try another number.", "ack_msg" => "Phone number already assigned to another customer!! Try another number.");
			$reply = array("ack" => 0, "developer_msg" => "Mobile number already assigned to another customer!! Try another number.", "ack_msg" => "A mobile number or client code already exists, or the company name is already associated with another customer. Please check.");
			return $reply;
		} else {
			/*if($zone=="1")
			{
				$client_code="RCSW".($code);
			}
			else if($zone=="2")
			{
				$client_code="RCSE".($code);
			}
			else if($zone=="5")
			{
				$client_code="RCSN".($code);
			}
			else if($zone=="6")
			{
				$client_code="RCSS".($code);
			}*/
			// if($type_of_executive=="1"){
			// 	$client_code = "SPSK".($code);
			// }elseif ($type_of_executive=="2") {
			// $client_code = "RTL".($code);
			// }elseif ($type_of_executive=="3") {
			// $client_code = "DEL".($code);
			// }elseif ($type_of_executive=="4") {
			// $client_code = "PRM".($code);
			// }elseif ($type_of_executive=="6") {
			// $client_code = "B2C".($code);
			// }



			/*if($category_id!=""){
						$category_r=explode(",", $category_id);
					$tcid_s= array();
					for($i=0;$i<sizeof($category_r);$i++){
						$tcid_s[]=$this->db->rp_getValue("category_master","tcid","id='".$category_r[$i]."'",0);   
					}

					$tcid_s=array_unique($tcid_s);
					$top_category_id=implode(",", $tcid_s);
				}else{
					$top_category_id="";
				}*/

			//echo($all_tcid);exit;


			// print_r($super_stockist_id); exit();
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"type_of_executive",
				"company_name",
				"cname",
				"phone",
				"mobile_no1",
				"email",
				"address",
				"address2",
				"shipping_address",
				"billing_address",
				"remark",
				"country",
				"state",
				"main_city",
				"city",
				"zone",
				"gst",
				"super_stockist_id",
				"dealer_distributor_id",
				"class_id",
				"area_id",
				"latitude",
				"longitude",
				"whatsapp_no",
				"price_list_id",
				"company_type",
				"zip",
				"email_cc",
				"client_code",
				"pan",
				"industry_type_id",
				"seid",
				"entry_flag",
				"client_code_sr_by_type",
				"top_category_id",
				"customer_flag",
				"channel_partner_flag",
				"type_of_company",
				"booking_place",
				"transport_by_id",
				"transporter_id",
				"booking_pincode",
				"purchasing_from",
				"turnover",
				"turnover_year",
			);
			$values = array(
				$type_of_executive,
				$company_name,
				$person_name,
				$phone,
				$mobile_number,
				$email,
				$address,
				$address1,
				$shipping_address,
				$billing_address,
				$remark,
				$country,
				$state,
				$main_city,
				$city,
				$zone,
				$gst_no,
				$super_stockist_id,
				$dealer_id,
				$class_id,
				$area_id,
				$latitude,
				$longitude,
				$whatsapp_no,
				$price_list_id,
				$company_type,
				$pincode,
				$email_cc,
				$client_code,
				$pan,
				$industry_type_id,
				$sales_id,
				$entry_flag,
				$code,
				$top_category_id,
				$customer_flag,
				(isset($channel_partner_flag) && $channel_partner_flag == 1) ? '1' : '0',
				$type_of_company,
				$booking_place,
				$transport_by_id,
				$transporter_id,
				$pincode,
				$purchasing_from,
				$turnover,
				$turnover_year,
			);
			$values = array_map(function ($value) {
				if ($value === null || $value === false) {
					return '';
				}
				return (string) $value;
			}, $values);

			// echo "<pre>"; print_r($rows); 
			// </br>


			$uid = $this->db->rp_insert($this->ctable, $values, $rows, 0);
			// echo "<pre>"; print_r($uid); exit();
			$CreateAccount = $this->CreateCustomerAccount($uid);
			/*add image*/
			$image_path = array();
			if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
				$ri = $uid;
				$rt = "executive";
				$tc = "executive";
				$rc = "id";
				for ($i = 0; $i < sizeof($file["image_path"]['name']); $i++) {
					//print_r($file["image_path"]);
					$file_name = $file['image_path']['name'][$i];
					$file_size = $file['image_path']['size'][$i];
					$file_tmp = $file['image_path']['tmp_name'][$i];
					$file_type = $file['image_path']['type'][$i];
					$extension = explode(".", $file_name);

					$allowed_extentions = array("jpg", "jpeg", "png", "JPEG", "JPEG", "PNG");
					$extension = $extension[sizeof($extension) - 1];
					if (!in_array($extension, $allowed_extentions)) {
						$file_error = true;
					}
					$orignal_file_name = $extension[0];
					/*if (in_array($extension, $allowed_extentions)) {
						$attachment = "../resource/image/";
						move_uploaded_file($file_tmp, $attachment . $file_name);
					}*/

					if (in_array($extension, $allowed_extentions)) {
						$attachment = "../resource/image/";
						//	compressImage($file_tmp,$attachment.$file_name,60);
						$compressedImage = $this->db->compressImage($file_tmp, $attachment . $file_name);

						if ($compressedImage) {
							$compressedImageSize = filesize($compressedImage);
							$compressedImageSize = $this->db->convert_filesize($compressedImageSize);

							$status = 'success';
							$statusMsg = "Image compressed successfully.";
						} else {
							$statusMsg = "Image compress failed!";
						}
						//move_uploaded_file($file_tmp,$attachment.$file_name);
					}
					$MediaTitle = $file_name;
					$MediaOrignalTitle = $file_name;

					$MediaFileName = $file_name;
					// $MediaType=User::$ValidMediaType[$extension];
					$UploadDate = date("Y-m-d H:i:s");

					// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
					$Values = array($MediaTitle, $MediaOrignalTitle, $MediaFileName, $extension, $UploadDate, $ri, $rt, $tc);
					// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
					$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");
					$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);

					$image_path[] = $MediaID;
				}
				$image_path = implode(",", $image_path);
				$upadateid = $this->db->rp_update($this->ctable, array("image_path" => $image_path), "id='" . $uid . "'", 0);
			}
			/*add image*/
			if ($uid != 0) {
				$item_rows = array("customer_id", "shipping_address");
				$item_values = array($uid, $shipping_address);
				$item_id = $this->db->rp_insert("customer_vs_shipping_address", $item_values, $item_rows, 0);
			}


			$this->addOutletsBranch($uid, $company_name, 1);



			if ($uid != 0) {
				/*create Account*/

				$CreateAccount = $this->CreateCustomerAccount($uid);

				/*create Account*/

				/*add arae code*/
				$city_id = $this->db->rp_getValue("area", "city_id", "isDelete=0 AND id='" . $area_id . "'", 0);

				$this->db->rp_delete($this->ctableMap, "executive_id=" . $uid . "", 0);
				if ($type_of_executive == '3') {
					// Check if dealer and its super stockist has this area or not if not then add to them first_area
					$outlet_detail = $this->db->rp_getData("executive", "super_stockist_id,dealer_distributor_id", "id='" . $uid . "'", "", 0);
					if ($outlet_detail) {
						$outlet_detail = mysqli_fetch_assoc($outlet_detail);
						$dealer_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['dealer_distributor_id'] . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
						if ($dealer_has_area_or_not <= 0) {
							$mapping_id_dd = $this->db->rp_insert($this->ctableMap, array($outlet_detail['dealer_distributor_id'], "dealer", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
						}
						$super_stockist_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $outlet_detail['dealer_distributor_id'] . "'", 0);
						$super_stockist_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $super_stockist_id . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "' AND city_id='" . $city_id . "'", 0);
						if ($super_stockist_has_area_or_not <= 0) {
							$mapping_id_ss = $this->db->rp_insert($this->ctableMap, array($super_stockist_id, "super_stockist", $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
						}
					}
				}

				$mapping_id = $this->db->rp_insert($this->ctableMap, array($uid, $type_of_executive, $class_id, $area_id, $city_id), array("executive_id", "executive_type", "class_id", "area_id", "city_id"), 0);
				// echo "hello"; exit();
				/*add arae code*/

				//$ack=$this->addArea($uid,$type_of_executive,$class_id,$item);
				/*$activationCode=$this->generateActivationCode();
				$activationCode_md5=md5($activationCode);
				$executive_r=$this->db->rp_getData($this->ctable,"*","id ='".$uid."'");
				$executive=mysqli_fetch_assoc($executive_r);
					$sms="Hello ".$executive['cname']."\nWelcome to ".SITETITLE.", Your login credentials for account given below:\nMobile:".$executive['phone']."\npassword:".$activationCode."\nTeam ".SITETITLE;
				$a=$this->aj_sendSMS($executive['phone'],$sms);
				$rows 	= array(
							"password"	=> $activationCode_md5,
							);
				$this->db->rp_update($this->ctable,$rows,"id='".$uid."'",0);*/
				$reply = array("ack" => 1, "developer_msg" => "insert Successfully", "ack_msg" => "Success! Insert Customer Successfully.", "inserted_id" => $uid, "areas" => $ack['areas']);
				return $reply;
			} else {
				$dbError = isset($this->db->myconn) ? mysqli_error($this->db->myconn) : '';
				$reply = array(
					"ack" => 0,
					"developer_msg" => "Database error!!" . ($dbError != '' ? " " . $dbError : ""),
					"ack_msg" => "Failed! Insert Record Failed."
				);
				return $reply;
			}
		}
	}
	//-------------------------------------------------------------------------------//
	//---------#Update Executive Detail-----------------------------------------------//	 
	public function UpdateExecutive($executive_id, $end_user_type, $type_of_executive, $company_type, $company_name, $address, $address2, $super_stockist_id, $zone, $city, $state, $country, $image_path, $email, $email_cc, $dealer_distributor_id, $cname, $cst, $pan, $phone, $gst, $vat, $inquiry_date, $zip, $excise, $class_id, $mobile_no1, $whatsapp_no, $area_id, $discount, $price_list_id, $vendor_desk, $office_supplier, $gst_detail, $other_image, $latitude, $longitude, $seid, $client_code, $industry_type, $cash_discount, $additional_discount, $credit_limit, $credit_day, $shipping_address, $billing_address, $remark, $file, $update_entry_flag, $credit_debit_type, $openinig_balance, $top_cat_id, $customer_insert_flag, $customer_update_flag, $order_view_flag, $order_insert_flag, $order_update_flag, $dealer_order_view_flag, $dealer_order_insert_flag, $outlets_order_insert_flag, $main_city, $order_approve_flag = "", $top_category_id, $outlets_order_view_flag, $type_of_company, $booking_place, $transporter_id, $transport_by_id, $purchasing_from, $customer_name, $turnover, $turnover_year, $channel_partner_flag = 0)
	{
		// echo $outlets_order_view_flag;exit;

		// echo $shipping_address; ;
		// echo '<pre>';
		// print_r($outlets_order_insert_flag);exit;

		// exit;
		/*$dup_where = "phone = '" . $phone . "' AND isDelete=0 AND customer_flag=0 AND isActive=1 AND id!='" . $_REQUEST['id'] . "'";*/

		//$dup_where = "(gst = '" . $gst . "' AND phone = '" . $phone . "' OR client_code = '".$client_code."') AND isDelete=0 AND id!='".$_REQUEST['id']."'";
		//$dup_where = "(gst = '" . $gst . "' AND client_code = '".$client_code."') AND isDelete=0 AND id!='".$_REQUEST['id']."'";
		// $dup_where = "(mobile_no1 = '" . $mobile_no1 . "' AND company_name ='".$company_name."') AND isDelete=0 AND id!='".$_REQUEST['id']."'";
		// $dup_where = " (mobile_no1 = '" . $mobile_no1 . "' OR client_code = '" . $client_code . "') AND company_name = '" . $company_name . "' AND isDelete=0 AND id!='" . $_REQUEST['id'] . "'";
		$dup_where = $this->executiveDupWhere($mobile_no1, $client_code, $executive_id);
		$r = ($dup_where != "") ? $this->db->rp_dupCheck($this->ctable, $dup_where, 0) : 0;
		//echo $r;exit;
		if ($r > 0) {
			$reply = array("ack" => 0, "developer_msg" => "Mobile number OR Client Code already Exists in another customer!! Please Check.", "ack_msg" => "A mobile number or client code already exists, or the company name is already associated with another customer. Please check.");
			return $reply;
		} else {
			//$country=$this->db->rp_getValue("country","name","id='".$country."'",0);
			//$state=$this->db->rp_getValue("state","name","id='".$state."'",0);
			if ($city == "") {
				$city = $this->db->rp_getValue($this->ctable, "city", "id='" . $_REQUEST['id'] . "'", 0);
			}
			$rows 	= array(
				"company_type"          => $company_type,
				"company_name"          => $company_name,
				"cname"                 => $cname,
				"super_stockist_id"     => $super_stockist_id,
				"dealer_distributor_id" => $dealer_distributor_id,
				"email"                 => $email,
				"email_cc"              => $email_cc,
				"cst"                   => isset($cst) ? $cst : "",
				"pan"                   => $pan,
				"gst"                   => $gst,
				"vat"                   => isset($vat) ? $vat : "",
				"excise"                => isset($excise) ? $excise : "",
				//"phone"                 => $phone,
				"address"               => $address,
				"address2"               => $address2,
				"zip"                   => $zip,
				"country"               => $country,
				"state"                 => $state,
				"city"                  => $city,
				"main_city"                  => $main_city,
				"zone"					=> $zone,
				// "image_path"            => $image_path,
				"type_of_executive"     => $type_of_executive,
				"class_id"              => $class_id,
				"mobile_no1"            => $mobile_no1,
				"whatsapp_no"           => $whatsapp_no,
				"discount"              => 0,
				//"password"		=> $password,
				"modify_date"     => date("Y-m-d H:i:s"),
				"price_list_id"   => $price_list_id,
				"latitude"        => $latitude,
				"longitude"       => $longitude,
				"vendor_desk"     => $vendor_desk,
				"office_supplier" => $office_supplier,
				"gst_detail"      => $gst_detail,
				"other_image"     => $other_image,
				"seid"            => $seid,
				"client_code"     => $client_code,
				"industry_type_id"     => $industry_type,
				"cash_discount"     => $cash_discount,
				"additional_discount"     => $additional_discount,
				"credit_limit"     => $credit_limit,
				"credit_day"     => $credit_day,
				// "shipping_address"     => $shipping_address,
				"billing_address"     	=> $billing_address,
				"remark"     			=> $remark,
				"update_entry_flag"     => $update_entry_flag,
				"credit_debit_type"     => $credit_debit_type,
				"openinig_balance"     => $openinig_balance,
				"top_category_id"     => $top_category_id,
				// "category_id"     => $category_id,
				"customer_insert_flag"     => ($customer_insert_flag) ? $customer_insert_flag : "",
				"customer_update_flag"     => ($customer_update_flag) ? $customer_update_flag : "",
				"order_view_flag"     => ($order_view_flag) ? $order_view_flag : "",
				"order_insert_flag"     => ($order_insert_flag) ? $order_insert_flag : "",
				"order_update_flag"     => ($order_update_flag) ? $order_update_flag : "",
				"dealer_order_view_flag"     => ($dealer_order_view_flag) ? $dealer_order_view_flag : "",
				"dealer_order_insert_flag"     => ($dealer_order_insert_flag) ? $dealer_order_insert_flag : "",
				"outlets_order_view_flag"     => ($outlets_order_view_flag) ? $outlets_order_view_flag : "",
				"outlets_order_insert_flag"     => ($outlets_order_insert_flag) ? $outlets_order_insert_flag : "",
				"order_approve_flag"     => ($order_approve_flag) ? $order_approve_flag : "",
				"type_of_company"     => $type_of_company,
				"booking_place"     => $booking_place,
				"transport_by_id"     => $transport_by_id,
				"transporter_id"     => $transporter_id,
				"purchasing_from"     => $purchasing_from,
				"booking_pincode"     => $zip,
				"turnover" => $turnover,
				"turnover_year" => $turnover_year,
				"channel_partner_flag" => ($channel_partner_flag) ? $channel_partner_flag : 0,


			);
			// print_r($rows)
			$where	= "id='" . $executive_id . "'";
			$isUpdated = $this->db->rp_update($this->ctable, $rows, $where, 0);

			// echo $isUpdated; exit;
			/*add image*/
			// $this->db->rp_delete("media","reference_id='".$executive_id."'",0);
			if ($isUpdated != 0) {

				// update account transaction openinig balance

				// get financial year date1
				// $date = "01";
				// $month = "04";
				// $year  = date('Y');
				//$time = date('h:i:s');
				// $created_date = $year."-".$month."-".$date." ".$time;
				// $created_date = $year."-".$month."-".$date;
				// get financial year date1

				$id = $_REQUEST['id'];
				$created_date = $this->db->rp_getValue("account_transaction", "transaction_date", "cid='" . $id . "' AND opening=1");

				$IsAvaiLable = $this->db->rp_getValue("account_transaction", "COUNT(*)", "cid='" . $id . "' AND opening=1");
				if ($IsAvaiLable > 0) {
					$debit = $openinig_balance * -1;
					$description = $created_date . " Opening Balance RS " . $openinig_balance;
					if ($credit_debit_type == 1) {
						$rows = array(
							"opening" => 1,
							"credit" => $openinig_balance,
							"debit" => 0,
							"amount" => $openinig_balance,
							"type" => 1,
							"description" => $description,
						);
					} else {
						$rows = array(
							"opening" => 1,
							"credit" => 0,
							"debit" => $debit,
							"amount" => $openinig_balance,
							"type" => 2,
							"description" => $description,
						);
					}
					$update = $this->db->rp_update("account_transaction", $rows, "cid='" . $id . "' AND opening=1", 0);
				} else {
					$date = "01";
					$month = "04";
					$year  = date('Y');
					// $time = date('h:i:s');
					$created_date = $year . "-" . $month . "-" . $date;
					$transaction_date = $year . "-" . $month . "-" . $date;
					// get financial year date

					$debit = $openinig_balance * -1;
					$description = $created_date . " Opening Balance RS " . $openinig_balance;

					$acc_id = $this->rp_getValue("account", "id", "cid='" . $id . "'");
					$acc_no = $this->rp_getValue("account", "acc_no", "cid='" . $id . "'");

					if ($credit_debit_type == 1) {
						$rows = array(
							"reference_table",
							"reference_id",
							"cid",
							"account_id",
							"account_no",
							"opening",
							"amount",
							"credit",
							"debit",
							"type",
							"description",
							"payment_date",
						);
						$value =  array(
							"customer",
							$id,
							$id,
							$acc_id,
							$acc_no,
							1,
							$openinig_balance,
							$openinig_balance,
							"",
							1,
							$description,
							$transaction_date,
						);

						$insert = $this->rp_insert("account_transaction", $value, $rows, 0);
					} else {
						$rows = array(
							"reference_table",
							"reference_id",
							"cid",
							"account_id",
							"account_no",
							"opening",
							"amount",
							"credit",
							"debit",
							"type",
							"description",
							"payment_date",
						);
						$value =  array(
							"customer",
							$id,
							$id,
							$acc_id,
							$acc_no,
							1,
							$openinig_balance,
							"",
							$debit,
							2,
							$description,
							$transaction_date,
						);

						$insert = $this->rp_insert("account_transaction", $value, $rows, 0);
					}
				}
				// update account transaction openinig balance

				$customer_id = $_REQUEST['id'];

				if (!empty($_REQUEST['shipping_address'])) {
					$this->db->rp_delete("customer_vs_shipping_address", "customer_id='" . $customer_id . "'");
					foreach ($_REQUEST['shipping_address'] as $key) {
						// print_r($_REQUEST['shipping_address']); 
						$item_rows = array("customer_id", "shipping_address");
						// $item_values = array($customer_id,$key);
						$item_values = array($customer_id, addslashes(html_entity_decode($key)));
						$item_id = $this->db->rp_insert("customer_vs_shipping_address", $item_values, $item_rows, 0);
					}

					// exit;
				}
				if (!empty($_REQUEST['phone']) || !empty($_REQUEST['customer_name'])) {
					$count_value = 0;
					$this->db->rp_delete("customer_vs_phone_no", "customer_id='" . $customer_id . "'");
					foreach ($_REQUEST['phone'] as $key) {
						$item_rows_m = array("customer_id", "phone_no", "name", "ref_table");
						// $item_values = array($customer_id,$key);
						$item_values_m = array($customer_id, addslashes(html_entity_decode($key)), $_REQUEST['customer_name'][$count_value], "executive");
						$count_value++;
						$item_id = $this->db->rp_insert("customer_vs_phone_no", $item_values_m, $item_rows_m, 0);
					}

					// exit;
				}


				if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
					$ri = $executive_id;
					$rt = "executive";
					$tc = "executive";
					$rc = "id";
					//for ($i = 0; $i < sizeof($file["image_path"]['name']); $i++) {
					//print_r($file["image_path"]);
					$file_name = $file['image_path']['name'];
					$file_size = $file['image_path']['size'];
					$file_tmp = $file['image_path']['tmp_name'];
					$file_type = $file['image_path']['type'];
					$extension = explode(".", $file_name);

					$allowed_extentions = array("jpg", "jpeg", "png", "JPEG", "JPEG", "PNG");
					$extension = $extension[sizeof($extension) - 1];
					if (!in_array($extension, $allowed_extentions)) {
						$file_error = true;
					}
					$orignal_file_name = $extension[0];
					if (in_array($extension, $allowed_extentions)) {
						$attachment = "../resource/image/";
						move_uploaded_file($file_tmp, $attachment . $file_name);
					}
					$MediaTitle = $file_name;
					$MediaOrignalTitle = $file_name;

					$MediaFileName = $file_name;
					// $MediaType=User::$ValidMediaType[$extension];
					$UploadDate = date("Y-m-d H:i:s");

					//$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
					//echo $file_name;exit;
					$Values = array($MediaTitle, $MediaOrignalTitle, $MediaFileName, $extension, $UploadDate, $ri, $rt, $tc);
					// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
					$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");

					$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);

					$image_path = $MediaID;
					//}
					//$image_path = implode(",", $image_path);
					$upadateid = $this->db->rp_update($this->ctable, array("image_path" => $image_path), "id='" . $executive_id . "'", 0);
				}
			}
			/*add image*/


			if ($isUpdated) {
				//echo $uid;exit;
				//$ack=$this->addArea($executive_id,$type_of_executive,$class_id,$item);
				$reply = array("ack" => 1, "developer_msg" => "Customer Update Successfull!!.", "ack_msg" => "Success! Update Customer Successfully.", "areas" => $ack['areas']);
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Update Failed.");
				return $reply;
			}
		}
	}
	//-----------------------------------------------------------------------------//
	public function UpdateExecutiveAPI($detail, $file)
	{
		extract($detail);
		// echo $shipping_address; exit;
		// echo '<pre>';
		// print_r($file);

		// exit;
		/*$dup_where = "phone = '" . $phone . "' AND isDelete=0 AND customer_flag=0 AND isActive=1 AND id!='" . $_REQUEST['id'] . "'";*/

		//$dup_where = "(gst = '" . $gst . "' AND phone = '" . $phone . "' OR client_code = '".$client_code."') AND isDelete=0 AND id!='".$_REQUEST['id']."'";
		//$dup_where = "(gst = '" . $gst . "' AND client_code = '".$client_code."') AND isDelete=0 AND id!='".$_REQUEST['id']."'";
		// $dup_where = "(mobile_no1 = '" . $mobile_number . "' AND company_name='".$company_name."') AND isDelete=0 AND id!='".$_REQUEST['id']."'";
		$dup_where = $this->executiveDupWhere($mobile_number, $client_code, $id, $company_name);
		$r = ($dup_where != "") ? $this->db->rp_dupCheck($this->ctable, $dup_where, 0) : 0;
		// $r=0;
		//echo $r;exit;

		if ($r) {
			$reply = array("ack" => 0, "developer_msg" => "Mobile number OR client Code already Exists OR else Company Name Same already Exists in another customer!! Please Check.", "ack_msg" => "A mobile number or client code already exists, or the company name is already associated with another customer. Please check.");
			return $reply;
		} else {
			//$country=$this->db->rp_getValue("country","name","id='".$country."'",0);
			//$state=$this->db->rp_getValue("state","name","id='".$state."'",0);
			if ($city == "") {
				$city = $this->db->rp_getValue($this->ctable, "city", "id='" . $_REQUEST['id'] . "'", 0);
			}
			/*$last_zone=$this->db->rp_getValue("executive","zone","isDelete=0 AND id='".$_REQUEST['id']."'");
			if($last_zone==$zone)
			{
				$client_code=$this->db->rp_getValue("executive","client_code","isDelete=0 AND id='".$_REQUEST['id']."'");
			}
			else
			{
			$lastInsertIds=$this->db->rp_getValue("executive","MAX(`client_code`)","zone='".$zone_id."' AND isDelete=0",0); 

	         $lastInsertId=substr($lastInsertIds,4);
			//$lastInsertId=$this->db->rp_getTotalRecord("executive","isDelete=0 AND zone='".$zone."'",0);  
			$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
			if($zone=="1")
			{
				$client_code="RCSW".($code);
			}
			else if($zone=="2")
			{
				$client_code="RCSE".($code);
			}
			else if($zone=="5")
			{
				$client_code="RCSN".($code);
			}
			else if($zone=="6")
			{
				$client_code="RCSS".($code);
			}
			}*/
			$last_company = $this->db->rp_getValue("executive", "type_of_company", "isDelete=0 AND id='" . $_REQUEST['id'] . "'");
			if ($last_company == $type_of_company) {
				$client_code = $this->db->rp_getValue("executive", "client_code", "isDelete=0 AND id='" . $_REQUEST['id'] . "'");
			} else {
				$lastInsertIds = $this->db->rp_getValue("executive", "MAX(`client_code`)", "type_of_company='" . $type_of_company . "' AND isDelete=0", 0);

				$lastInsertId = substr($lastInsertIds, 4);
				//$lastInsertId=$this->db->rp_getTotalRecord("executive","isDelete=0 AND zone='".$zone."'",0);  
				$code = str_pad(($lastInsertId + 1), 4, '0', STR_PAD_LEFT);
				$client_code_prefix = $this->db->rp_getValue("company_master", "prefix", "id='" . $type_of_company . "' AND isDelete=0", 0);
				$client_code = $client_code_prefix . ($code);
			}

			$rows 	= array(
				"company_type"          => $company_type,
				"company_name"          => $company_name,
				"cname"                 => $person_name,
				"super_stockist_id"     => $super_stockist_id,
				"dealer_distributor_id" => $dealer_id,
				"email"                 => $email,
				"email_cc"              => $email_cc,
				"pan"                   => $pan,
				"gst"                   => $gst_no,
				"phone"                 => $phone,
				"address"               => $address,
				"address2"               => $address1,
				"zip"                   => $pincode,
				"booking_pincode"      => $pincode,
				"country"               => $country,
				"state"                 => $state,
				"main_city"                  => $main_city,
				"city"                  => $city,
				"zone"					=> $zone,
				// "image_path"            => $image_path,
				//"type_of_executive"     => $type_of_executive,
				"class_id"              => $class_id,
				"mobile_no1"            => $mobile_number,
				"whatsapp_no"           => $whatsapp_no,
				//"password"		=> $password,
				"modify_date"     => date("Y-m-d H:i:s"),
				"price_list_id"   => $price_list_id,
				"industry_type_id"     => $industry_type_id,

				"shipping_address"     => $shipping_address,
				"billing_address"     => $billing_address,
				"remark"     			=> $remark,
				"top_category_id"     => $top_category_id,
				"update_entry_flag"     => "5",
				"type_of_company"     => $type_of_company,
				"client_code"		=> $client_code,
				"booking_place"		=> $booking_place,
				"transport_by_id"		=> $transport_by_id,
				"transporter_id"		=> $transporter_id,
				"purchasing_from"		=> $purchasing_from,
				"turnover"		=> $turnover,
				"turnover_year"		=> $turnover_year,
				"channel_partner_flag" => (isset($channel_partner_flag) && $channel_partner_flag == 1) ? 1 : 0,


			);
			$where	= "id='" . $id . "'";
			$isUpdated = $this->db->rp_update($this->ctable, $rows, $where, 0);

			// echo $isUpdated; exit;
			/*add image*/
			// $this->db->rp_delete("media","reference_id='".$executive_id."'",0);
			if ($isUpdated != 0) {

				// update account transaction openinig balance

				// get financial year date1
				// $date = "01";
				// $month = "04";
				// $year  = date('Y');
				//$time = date('h:i:s');
				// $created_date = $year."-".$month."-".$date." ".$time;
				// $created_date = $year."-".$month."-".$date;
				// get financial year date1

				$id = $_REQUEST['id'];



				// update account transaction openinig balance

				$customer_id = $_REQUEST['id'];

				/*if(!empty($_REQUEST['shipping_address']))
				{
					$this->db->rp_delete("customer_vs_shipping_address","customer_id='".$customer_id."'");
					foreach ($_REQUEST['shipping_address'] as $key)
					{
						// print_r($_REQUEST['shipping_address']); 
						$item_rows = array("customer_id","shipping_address");
						// $item_values = array($customer_id,$key);
						$item_values = array($customer_id,addslashes(html_entity_decode($key)));
						$item_id = $this->db->rp_insert("customer_vs_shipping_address",$item_values,$item_rows,0);
					}

					// exit;
				}*/


				$image_path = array();
				if (isset($file["image_path"]) && $file["image_path"]['size'] != 0) {
					$ri = $customer_id;
					$rt = "executive";
					$tc = "executive";
					$rc = "id";
					for ($i = 0; $i < sizeof($file["image_path"]['name']); $i++) {
						//print_r($file["image_path"]);
						$file_name = $file['image_path']['name'][$i];
						$file_size = $file['image_path']['size'][$i];
						$file_tmp = $file['image_path']['tmp_name'][$i];
						$file_type = $file['image_path']['type'][$i];
						$extension = explode(".", $file_name);

						$allowed_extentions = array("jpg", "jpeg", "png", "JPEG", "JPEG", "PNG");
						$extension = $extension[sizeof($extension) - 1];
						if (!in_array($extension, $allowed_extentions)) {
							$file_error = true;
						}
						$orignal_file_name = $extension[0];
						if (in_array($extension, $allowed_extentions)) {
							$attachment = "../resource/image/";
							$compressedImage = $this->db->compressImage($file_tmp, $attachment . $file_name);

							if ($compressedImage) {
								$compressedImageSize = filesize($compressedImage);
								$compressedImageSize = $this->db->convert_filesize($compressedImageSize);

								$status = 'success';
								$statusMsg = "Image compressed successfully.";
							} else {
								$statusMsg = "Image compress failed!";
							}
							//move_uploaded_file($file_tmp, $attachment . $file_name);
						}
						$MediaTitle = $file_name;
						$MediaOrignalTitle = $file_name;

						$MediaFileName = $file_name;
						//echo "".$MediaFileName;exit;
						// $MediaType=User::$ValidMediaType[$extension];
						$UploadDate = date("Y-m-d H:i:s");

						// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
						$Values = array($MediaTitle, $MediaOrignalTitle, $MediaFileName, $extension, $UploadDate, $ri, $rt, $tc);
						// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
						$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");
						$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);

						$image_path[] = $MediaID;
					}
					//echo "ejej";
					//print_r($image_path);exit;
					$image_path = implode(",", $image_path);
					$upadateid = $this->db->rp_update($this->ctable, array("image_path" => $image_path), "id='" . $_REQUEST['id'] . "'", 0);
				}
			}

			/*add image*/


			if ($isUpdated) {
				//echo $uid;exit;
				//$ack=$this->addArea($executive_id,$type_of_executive,$class_id,$item);
				$reply = array("ack" => 1, "developer_msg" => "Customer Update Successfull!!.", "ack_msg" => "Success! Update Customer Successfully.", "areas" => $ack['areas']);
				return $reply;
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Update Failed.");
				return $reply;
			}
		}
	}
	//------#Edit Executive Detail#------------------------------------------------//		
	public function EditExecutive($detail)
	{
		// echo '<pre>';
		// print_r($detail);
		// echo '</pre>';
		// exit;
		$where = " id='" . $detail['id'] . "' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable, "*", $where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result = array();
		$result['cname']		= htmlentities($ctable_d['cname']);
		$result['company_type']	= htmlentities($ctable_d['company_type']);
		$result['super_stockist_id']	= htmlentities($ctable_d['super_stockist_id']);
		$result['dealer_distributor_id']	= htmlentities($ctable_d['dealer_distributor_id']);
		$result['company_name']	= htmlentities($ctable_d['company_name']);
		$result['cpname']		= htmlentities($ctable_d['cpname']);
		$result['email']		= stripslashes($ctable_d['email']);
		$result['email_cc']		= stripslashes($ctable_d['email_cc']);
		$result['cst']			= stripslashes(isset($ctable_d['cst'])) ? $ctable_d['cst'] : "";
		$result['pan']			= stripslashes($ctable_d['pan']);
		$result['gst']			= stripslashes($ctable_d['gst']);
		$result['vat']			= stripslashes(isset($ctable_d['vat'])) ? $ctable_d['vat'] : "";
		$result['excise']		= stripslashes(isset($ctable_d['excise'])) ? $ctable_d['excise'] : "";
		$result['phone']		= stripslashes($ctable_d['phone']);
		$result['address']		= htmlentities($ctable_d['address']);
		$result['address2']		= htmlentities($ctable_d['address2']);
		$result['zip']			= stripslashes($ctable_d['zip']);
		$result['country']		= $ctable_d['country'];
		$result['state'] 		= stripslashes($ctable_d['state']);
		$result['city'] 		= stripslashes($ctable_d['city']);
		$result['main_city'] 		= stripslashes($ctable_d['main_city']);
		$result['zone']			= stripslashes($ctable_d['zone']);
		$result['image_path'] 		= stripslashes($ctable_d['image_path']);
		$result['class_id'] 		= stripslashes($ctable_d['class_id']);
		$result['mobile_no1'] 		= stripslashes($ctable_d['mobile_no1']);
		$result['whatsapp_no'] 		= stripslashes($ctable_d['whatsapp_no']);
		$result['area_id'] 		= stripslashes($ctable_d['area_id']);
		$result['discount'] 		= stripslashes($ctable_d['discount']);
		$result['password'] 		= stripslashes($ctable_d['password']);
		$result['price_list_id'] 		= stripslashes($ctable_d['price_list_id']);
		$result['latitude'] 		= stripslashes($ctable_d['latitude']);
		$result['longitude'] 		= stripslashes($ctable_d['longitude']);
		$result['seid'] 		= stripslashes($ctable_d['seid']);
		$result['client_code'] 		= stripslashes($ctable_d['client_code']);
		$result['cash_discount'] 		= stripslashes($ctable_d['cash_discount']);
		$result['additional_discount'] 		= stripslashes($ctable_d['additional_discount']);
		$result['credit_limit'] 		= stripslashes($ctable_d['credit_limit']);
		$result['credit_day'] 		= stripslashes($ctable_d['credit_day']);
		$result['shipping_address'] 		= stripslashes($ctable_d['shipping_address']);
		$result['mobile_no1'] 			= stripslashes($ctable_d['mobile_no1']);
		$result['billing_address'] 		= stripslashes($ctable_d['billing_address']);
		$result['remark'] 				= stripslashes($ctable_d['remark']);
		$result['industry_type'] 		= stripslashes($ctable_d['industry_type_id']);
		$result['credit_debit_type'] 		= stripslashes($ctable_d['credit_debit_type']);
		$result['openinig_balance'] 		= stripslashes($ctable_d['openinig_balance']);
		// $result['category_id'] 		= stripslashes($ctable_d['category_id']);
		$result['customer_insert_flag'] 		= stripslashes($ctable_d['customer_insert_flag']);
		$result['customer_update_flag'] 		= stripslashes($ctable_d['customer_update_flag']);
		$result['order_view_flag'] 		= stripslashes($ctable_d['order_view_flag']);
		$result['order_insert_flag'] 		= stripslashes($ctable_d['order_insert_flag']);
		$result['order_update_flag'] 		= stripslashes($ctable_d['order_update_flag']);
		$result['dealer_order_view_flag'] 		= stripslashes($ctable_d['dealer_order_view_flag']);
		$result['dealer_order_insert_flag'] 		= stripslashes($ctable_d['dealer_order_insert_flag']);
		$result['outlets_order_view_flag'] 		= stripslashes($ctable_d['outlets_order_view_flag']);
		$result['outlets_order_insert_flag'] 		= stripslashes($ctable_d['outlets_order_insert_flag']);
		$result['order_approve_flag'] 		= stripslashes($ctable_d['order_approve_flag']);
		$result['top_category_id'] 		= stripslashes($ctable_d['top_category_id']);
		$result['type_of_company'] 		= stripslashes($ctable_d['type_of_company']);
		$result['booking_place'] 		= stripslashes($ctable_d['booking_place']);
		$result['transport_by_id'] 		= stripslashes($ctable_d['transport_by_id']);
		$result['transporter_id'] 		= stripslashes($ctable_d['transporter_id']);
		$result['purchasing_from'] 		= stripslashes($ctable_d['purchasing_from']);
		$result['turnover'] 		= stripslashes($ctable_d['turnover']);
		$result['turnover_year'] 		= stripslashes($ctable_d['turnover_year']);
		$result['channel_partner_flag'] = stripslashes($ctable_d['channel_partner_flag']);


		$area_id_r = $this->db->rp_getData("executive_map_area", "area_id", "executive_id='" . $detail['id'] . "' AND isDelete=0", "", 0);
		while ($w = mysqli_fetch_array($area_id_r)) {
			//$area_id=array();
			$area_id[] = $w['area_id'];
		}
		$reply = array("ack" => 1, "developer_msg" => "User detail fetched!!.", "ack_msg" => "Success! Update Customer Successfully.", "result" => $result, "area_id" => $area_id);
		return $reply;
	}
	//--------------------------------------------------------------------------------//
	//---------#Add Area Information(executive_map_area)-------------------------------//
	function addArea($executive_id, $type_of_executive, $class_id, $item)
	{
		$this->db->rp_delete($this->ctableMap, "executive_id=" . $executive_id . "", 0);
		foreach ($item as $b) {
			$area_id = $b['area_id'];

			if ($type_of_executive == 'outlets') {
				// Check if dealer and its super stockist has this area or not if not then add to them first_area
				$outlet_detail = $this->db->rp_getData("executive", "super_stockist_id,dealer_distributor_id", "id='" . $executive_id . "'");
				if ($outlet_detail) {
					$outlet_detail = mysqli_fetch_assoc($outlet_detail);
					$dealer_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $outlet_detail['dealer_distributor_id'] . "' AND area_id='" . $area_id . "' AND class='" . $class_id . "'");
					if ($dealer_has_area_or_not <= 0) {
						$mapping_id_dd = $this->db->rp_insert($this->ctableMap, array($outlet_detail['dealer_distributor_id'], "dealer", $class_id, $area_id), array("executive_id", "executive_type", "class_id", "area_id"), 0);
					}
					$super_stockist_id = $this->db->rp_getValue("executive", "super_stockist_id", "id='" . $outlet_detail['dealer_distributor_id'] . "'");
					$super_stockist_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $super_stockist_id . "' AND area_id='" . $area_id . "' AND class='" . $class_id . "'");
					if ($super_stockist_has_area_or_not <= 0) {
						$mapping_id_ss = $this->db->rp_insert($this->ctableMap, array($super_stockist_id, "super_stockist", $class_id, $area_id), array("executive_id", "executive_type", "class_id", "area_id"), 0);
					}
				}
			}
			$mapping_id = $this->db->rp_insert($this->ctableMap, array($executive_id, $type_of_executive, $class_id, $area_id), array("executive_id", "executive_type", "class_id", "area_id"), 0);
			$areas = array();
			$r['class_id'] = $class_id;
			$r['area_id'] = $area_id;
			$r['id'] = $mapping_id;
			$areas[] = $r;
		}

		return $ack = array('ack' => 1, 'ack_msg' => "Success!", "developer_msg" => "Empty Mobile Number", "areas" => $areas);
	}
	//--------------------------------------------------------------------------------------//
	//-----------#Delete Executive Information------------------------------------------------//	
	public function ExecutiveDelete($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='" . $_REQUEST['id'] . "'";
		$uid = $this->db->rp_update($this->ctable, $rows, $where);
		if ($uid != 0) {
			$reply = array("ack" => 1, "developer_msg" => "deleted data.", "ack_msg" => "Success! Delete Customer Successfully.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Delete record Failed.");
			return $reply;
		}
	}

	public function changetocustomer($detail)
	{
		$rows 	= array(
			"customer_flag"	=> "0",
			"customer_flag_change_date"	=> date('Y-m-d H:i:s'),
		);
		$where	= "id='" . $_REQUEST['id'] . "'";
		$uid = $this->db->rp_update($this->ctable, $rows, $where);
		if ($uid != 0) {
			$reply = array("ack" => 1, "developer_msg" => "customer type changed.", "ack_msg" => "Success! prospect Customer changed into Customer.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! prospect Customer changed into Customer failed.");
			return $reply;
		}
	}
	//-------------------------------------------------------------------------------//
	//------------#Add Outlets Branch Info(outlets_branch)-------------------------------------------//	
	function addOutletsBranch($cid = "", $branch_name = "", $debug = 0)
	{
		if ($branch_name != "" && $cid != "") {
			$adate	= date('Y-m-d H:i:s');
			$rows = array("cid", "branch_name", "adate", "isDelete");
			$values = array($cid, $branch_name, $adate, 0);
			$cbid = $this->db->rp_insert("outlets_branch", $values, $rows, $debug);
			if ($cbid != 0) {
				return $response = array('ack' => 1, 'ack_msg' => 'Branch added Successfully !!!');
			} else {
				return $response = array('ack' => 0, 'ack_msg' => 'Branch name can not be empty !!!');
			}
		} else {
			return $response = array('ack' => 0, 'ack_msg' => 'Branch name can not be empty !!!');
		}
	}
	//------------------------------------------------------------------------------//
	//--------------Generate Activation Code---------------------------------------//	
	function generateActivationCode()
	{
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$randStr = "";
		for ($i = 0; $i <= 5; $i++) {
			$randStr = $randStr . $characters[rand(0, strlen($characters) - 1)];
		}
		return $randStr;
	}
	//-----------------------------------------------------------------------------//
	//-----------#Send SMS To mobile on Executive's registered Number-----------------//
	function aj_sendSMS($number, $sms)
	{
		require_once('notification.class.php');
		$nt = new Notification();
		$msgId = "NO";
		if ($number != "") {
			$msgId = $nt->aj_sendSMSSecurity($number, $sms);
			if ($msgId != 0) {

				return $deliveryStatus = array("ack" => 1, "ack_msg" => "SMS sent to " . $number . " successfully");
			}
			//$deliveryStatus=$nt->aj_getDeliveryReport($msgId);
			else
				$deliveryStatus = array("ack" => 0, "ack_msg" => "SMS sending failed on" . $number, "reason" => "Invalid mobile number or mobile switched off or out of coverage area!!");
			return $deliveryStatus;
		}
		return array('ack' => 0, 'ack_msg' => "Internal Error!", "developer_msg" => "Empty Mobile Number");
	}
	//------------------------------------------------------------------------------------//	
	function getRequiredColumns($required_columns = array())
	{
		if (!empty($required_columns)) {
			$required_columns_string = implode(",", $required_columns);
			return $required_columns_string;
		} else {
			return "*";
		}
	}
	//24-04-2017-sejal------------------#this function is used for service get outlets list(API)----------//	
	function getOutletList($required_columns, $sales_executive_id)
	{
		//find area list for salse executive
		$result = array();
		$outlet_id = array();
		$sales_area_id = array();
		$sales_area_r = $this->db->rp_getData("sales_executive_map_area", "*", "sales_executive_id=" . $sales_executive_id . "", "", 0);
		if ($sales_area_r) {
			while ($sales_area_d = mysqli_fetch_assoc($sales_area_r)) {

				$sales_area_id[] = $sales_area_d['area_id'];
			}

			if (!empty($sales_area_id)) {
				$area_ids = implode(",", $sales_area_id);
				//find area list for outlet and get outlet ids---------------------//
				$outlet_area_r = $this->db->rp_getData("executive_map_area", "*", "area_id IN (" . $area_ids . ")  AND  executive_type='outlets'", "", 0);
				while ($outlet_area_d = mysqli_fetch_assoc($outlet_area_r)) {
					$outlet_id[] = $outlet_area_d['executive_id'];
				}
			}
			$required_columns = $this->getRequiredColumns($required_columns);
			$limit = $this->getLimit();
			$result = array();
			if (!empty($outlet_id)) {
				$ids = implode(",", $outlet_id);
				$where = "type_of_executive='outlets' AND id IN (" . $ids . ") AND isActive=1 AND isDelete=0";
				$data    = $this->db->rp_getData('executive', $required_columns, $where, "id DESC", 0, $limit);

				while ($row = mysqli_fetch_assoc($data)) {
					$row['dealer_distributor_id'] = $this->db->rp_getValue("executive", "cname", "id=" . $row['dealer_distributor_id'] . "", 0);
					$row['super_stockist_id'] = $this->db->rp_getValue("executive", "cname", "id=" . $row['super_stockist_id'] . "");
					$row['city'] = $this->db->rp_getValue("city", "name", "id='" . $row['city'] . "'");
					$row['state'] = $this->db->rp_getValue("state", "name", "id='" . $row['state'] . "'");
					$row['country'] = $this->db->rp_getValue("country", "name", "id='" . $row['country'] . "'");
					$result[] = $row;
				}
			}
		}

		return $result;
	}
	//--------------------------------------------------------------------------//	

	function getLimit($limit = array())
	{
		$limit = $this->db->getLimit();
		if (!empty($limit) && array_key_exists("ul", $limit)) {
			$ul = $limit['ul'];
			if (array_key_exists("ll", $limit) && $limit['ll'] != "") {
				$ll = $limit['ll'];
			} else {
				$ll = "18446744073709551615";
			}
			$limit_string = "" . $ul . "," . $ll;
			return $limit_string;
		} else {
			return "";
		}
	}
	//--------------------Get Customer List(API)--------------------------------------------//
	function getCustomer($sales_executive_id = "")
	{

		$result = array();
		$customer_id = array();
		$sales_area_id = array();
		$sales_area_r = $this->db->rp_getData("sales_executive_map_area", "*", "sales_executive_id=" . $sales_executive_id . "", "", 0);
		if ($sales_area_r) {
			while ($sales_area_d = mysqli_fetch_assoc($sales_area_r)) {
				$sales_area_id[] = $sales_area_d['area_id'];
			}
			//print_r($sales_area_id);exit;
			if (!empty($sales_area_id)) {
				$area_ids = implode(",", $sales_area_id);
				//find area list for outlet and get outlet ids---------------------//
				$outlet_area_r = $this->db->rp_getData("executive_map_area", "*", "area_id IN (" . $area_ids . ")", "", 0);
				while ($outlet_area_d = mysqli_fetch_assoc($outlet_area_r)) {
					$customer_id[] = $outlet_area_d['executive_id'];
				}
			}
			//print_r($customer_id);exit;
			if (!empty($customer_id)) {
				$ids = implode(",", $customer_id);
				//
				$data    = $this->db->rp_getData('executive', "*", "id IN (" . $ids . ") AND isDelete=0 AND isActive=1 ", "adate DESC", 0);
				if ($data) {
					while ($r = mysqli_fetch_assoc($data)) {
						$r['other_contact'] = $r['mobile_no1'];

						$r['cname'] 	= $r['cname'];
						$r['phone'] 	= $r['phone'];
						$r['adate'] = date('d-m-Y', strtotime($r['adate']));
						$r['created_date'] = date('d-m-Y', strtotime($r['created_date']));
						//$r['order_date']=array_key_exists('order_date',$r)?date('d-m-Y',strtotime($r['order_date'])):0;

						$first_area = $this->db->rp_getData("executive_map_area", "area_id", "executive_id='" . $r['id'] . "'", "id ASC LIMIT 1");
						if ($first_area) {
							$first_area = mysqli_fetch_assoc($first_area);
							$first_area = $first_area['area_id'];
						}


						///// get all detail of executive_map_area
						$executive_areas = $this->db->rp_getData("executive_map_area", "*", "executive_id='" . $r['id'] . "'", "id ASC");
						if ($executive_areas) {
							$area = array();
							while ($executive_area = mysqli_fetch_assoc($executive_areas)) {
								$areas['id'] = $executive_area['id'];
								$areas['class_id'] = $executive_area['class_id'];
								$areas['area_id'] = $executive_area['area_id'];
								$areas['executive_id'] = $executive_area['executive_id'];
								$areas['isDelete'] = $executive_area['isDelete'];
								$areas['isActive'] = $executive_area['isActive'];
								$areas['executive_type'] = $executive_area['executive_type'];
								$area[] = $areas;
							}
							$r['area'] = $area;
						}
						$r['area_id'] 		= $first_area;
						$result[] = $r;
					}
					if (!empty($result)) {
						$ack = array("ack" => 1, "ack_msg" => "Successfully Get Customer !!", "developer_msg" => "You got it!!", "result" => $result,);
						return $ack;
					} else {
						$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!", "result" => $result,);
						return $ack;
					}
				} else {
					$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!", "result" => $result,);
					return $ack;
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!", "result" => $result,);
				return $ack;
			}
		}
	}

	public function MapExecutiveArea($areas)
	{
		$areas_result = array();

		foreach ($areas['values'] as $b) {
			$b = $b['nameValuePairs'];
			$local_id = $b['local_id'];
			$area_id = $b['area_id'];
			$class_id = $b['class_id'];
			$executive_id = $b['executive_id'];
			$executive_detail = $this->db->rp_getData($this->ctable, "*", "id='" . $executive_id . "'", "", 0);

			if ($executive_detail) {

				$executive_detail = mysqli_fetch_assoc($executive_detail);
				if ($executive_detail['type_of_executive'] == "dealer" || $executive_detail['type_of_executive'] == "super_stockist") {
					$executive_has_area_or_not = $this->db->rp_getTotalRecord($this->ctableMap, "executive_id='" . $executive_id . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "'");
					if ($executive_has_area_or_not <= 0) {
						$mapping_id = $this->db->rp_insert($this->ctableMap, array($executive_id, "dealer", $class_id, $area_id), array("executive_id", "executive_type", "class_id", "area_id"), 0);
					} else {
						$mapping_id = $this->db->rp_getValue($this->ctableMap, "id", "executive_id='" . $executive_id . "' AND area_id='" . $area_id . "' AND class_id='" . $class_id . "'", 0);
					}
				}
			}

			$r['class_id'] = $class_id;
			$r['area_id'] = $area_id;
			$r['server_id'] = $mapping_id;
			$r['local_id'] = $local_id;
			$r['executive_id'] = $executive_id;
			$areas_result[] = $r;
		}
		if (!empty($areas_result))
			return $ack = array('ack' => 1, 'ack_msg' => "Success!", "developer_msg" => "Dealer and Superstockist Mapped", "areas" => $areas_result);
		else
			return $ack = array('ack' => 0, 'ack_msg' => "No Mapping Found!!", "developer_msg" => "No Mapping Found!!");
	}

	public function GetDealer($detail)
	{
		extract($detail);
		$result = array();
		$customer_id = array();
		$sales_area_id = array();
		$executive_id = array();
		$searchName = isset($_REQUEST['searchName']) ? $_REQUEST['searchName'] : "";
		$limit = $this->getLimit();
		$where = "isDelete=0 AND isActive=1 ";
		$new_sales_id = "";
		$get_sales_type = "";
		// Top roles (MD / Regional Sales Manager etc.) should see all customers like web Manage Customer — type + location filter only
		$see_all_sales_types = array('sales_manager', 'dispatch_sales_manager');
		$see_all_customers = false;

		if ($sales_id != "") {
			$WhereCondition = "isDelete=0 AND isActive=1 ";
			$check_id = $sales_id;
			$get_sales_type = $this->db->rp_getValue("sales_executive", "type", "isDelete=0 AND id='" . $check_id . "'", 0);
			$see_all_customers = in_array($get_sales_type, $see_all_sales_types, true);

			if (!$see_all_customers) {
				if ($get_sales_type == "sales_manager") {
					$key = "sm_id";
					$WhereCondition .= ' AND ' . $key . '=' . $check_id;
				} else if ($get_sales_type == "area_sales_manager") {
					$key = "asm_id";
					$WhereCondition .= ' AND ' . $key . '=' . $check_id;
				} else if ($get_sales_type == "sales_officer") {
					$key = "so_id";
					$WhereCondition .= ' AND ' . $key . '=' . $check_id;
				} else if ($get_sales_type == "sales_executive") {
					$key = "se_id";
					$WhereCondition .= ' AND ' . $key . '=' . $check_id;
				} else if ($get_sales_type == "area_manager") {
					$key = "se_id";
					$WhereCondition .= ' AND ' . $key . '=' . $check_id;
				}

				$data = $this->db->rp_getData("sales_executive", "id", $WhereCondition, "", 0);

				$SALEID1 = array();
				if ($data) {
					while ($data_d = mysqli_fetch_assoc($data)) {
						$SALEID1[] = $data_d['id'];
					}
				}

				$SALEID1 = implode(",", $SALEID1);
				if ($SALEID1) {
					$new_sales_id = $SALEID1 . ',' . $sales_id;
				} else {
					$new_sales_id = $sales_id;
				}
			} else {
				$new_sales_id = $sales_id;
			}
		}

		if ($detail['superstokist_id'] != "") {
			$where .= " AND super_stockist_id= " . $detail['superstokist_id'];
		}

		if ($searchName != "") {
			$where .= " AND (company_name like '%" . $searchName . "%' OR cname like '%" . $searchName . "%' OR phone  LIKE '%" . $searchName . "%' OR zip LIKE '%" . $searchName . "%' OR client_code LIKE '%" . $searchName . "%')";
		}
		if ($type != "") {
			$where .= " AND type_of_executive='" . $this->db->clean($type) . "'";
		}
		if ($customer_flag != "") {
			$where .= " AND customer_flag=" . $customer_flag;
		}
		if ($type_of_company != "") {
			$where .= " AND type_of_company=" . $type_of_company;
		}

		if ($see_all_customers) {
			// MD / top manager: same as web — all customers of selected type, filtered by class/city/area (not by seid chain)
			$mapWhere = "";
			if ($area_id != "" && $class_id != "") {
				$mapWhere = "class_id='" . $class_id . "' AND area_id='" . $area_id . "' AND isDelete=0";
				if ($city_id != "") {
					$mapWhere .= " AND city_id='" . $city_id . "'";
				}
			} else if ($city_id != "" && $class_id != "") {
				$mapWhere = "class_id='" . $class_id . "' AND city_id='" . $city_id . "' AND isDelete=0";
			} else if ($class_id != "") {
				$mapWhere = "class_id='" . $class_id . "' AND isDelete=0";
			}

			if ($mapWhere != "") {
				$outlet_area_r = $this->db->rp_getData("executive_map_area", "DISTINCT(executive_id)", $mapWhere, "", 0);
				if ($outlet_area_r) {
					while ($outlet_area_d = mysqli_fetch_assoc($outlet_area_r)) {
						$executive_id[] = $outlet_area_d['executive_id'];
					}
				}
				if (!empty($executive_id)) {
					$executive_id = array_unique($executive_id);
					$ids = implode(",", $executive_id);
					$where .= " AND id IN (" . $ids . ")";
				} else if ($class_id != "") {
					// fallback: customers tagged with this class on executive row
					$where .= " AND class_id='" . $class_id . "'";
				}
			}
		} else {
			if ($area_id != "") {
				$where1 = "sales_executive_id=" . $sales_id . " AND class_id='" . $class_id . "'  AND isDelete=0 AND isActive=1 AND city_id='" . $city_id . "' AND area_id='" . $area_id . "'";
			} else if ($city_id != "") {
				$where1 = "sales_executive_id=" . $sales_id . " AND city_id='" . $city_id . "' AND class_id='" . $class_id . "'  AND isDelete=0 AND isActive=1";
			} else if ($class_id != "") {
				$where1 = "sales_executive_id=" . $sales_id . " AND class_id='" . $class_id . "'  AND isDelete=0 AND isActive=1";
			} else {
				$where1 = "";
			}

			if (($area_id != "" || $class_id != "") && $where1 != "") {
				$sales_area_r = $this->db->rp_getData("sales_executive_map_area", "area_id", $where1, "", 0);
				if ($sales_area_r) {
					while ($sales_area_d = mysqli_fetch_assoc($sales_area_r)) {
						$sales_area_id[] = $sales_area_d['area_id'];
					}
				}
			}

			if (!empty($sales_area_id)) {
				$area_ids = implode(",", $sales_area_id);

				if ($area_id == "") {
					$outlet_area_r = $this->db->rp_getData("executive_map_area", "DISTINCT(executive_id)", "area_id IN (" . $area_ids . ") AND isDelete=0", "", 0);
				} else {
					$outlet_area_r = $this->db->rp_getData("executive_map_area", "*", "class_id=" . $class_id . " AND area_id = '" . $area_id . "' AND isDelete=0", "", 0);
				}

				if ($outlet_area_r) {
					while ($outlet_area_d = mysqli_fetch_assoc($outlet_area_r)) {
						$executive_id[] = $outlet_area_d['executive_id'];
					}
				}

				if (!empty($executive_id)) {
					$executive_id = array_unique($executive_id);
					$ids = implode(",", $executive_id);
					if ($is_class_area_filter == '1') {
						$where .= " AND id IN (" . $ids . ") ";
					} else {
						$where .= " AND ((id IN (" . $ids . ") AND seid='' ) OR seid IN (" . $new_sales_id . "))";
					}
				} else {
					if ($sales_id != "" && $new_sales_id != "") {
						$where .= " AND seid IN (" . $new_sales_id . ") ";
					}
				}
			} else {
				if ($sales_id != "" && $new_sales_id != "") {
					$where .= " AND seid IN (" . $new_sales_id . ") ";
				}
			}
		}

		$data = $this->db->rp_getData('executive', "*", $where, "company_name ASC", 0, $limit);
		$customer_flag_array = array("0" => "Customer", "1" => "Prospect Customer");
		if ($data) {
			while ($r = mysqli_fetch_assoc($data)) {
				$r['cname'] = $r['cname'];
				$r['phone'] = $r['phone'];
				$img = explode(",", $r['image_path']);
				$imgpath = array();
				for ($i = 0; $i < sizeof($img); $i++) {
					$imgpath[] = SITEURL . "resource/image/" . $this->db->rp_getValue("media", "url", "reference_id='" . $r['id'] . "' AND id='" . $img[$i] . "'", 0);
				}
				$r['image_path'] = ($r['image_path'] != "") ? $imgpath : [];
				$r['adate'] = date('d-m-Y', strtotime($r['created_date']));
				$r['created_date'] = date('d-m-Y', strtotime($r['created_date']));
				if ($r['whatsapp_no'] != "") {
					$r['whatsapp_no'] = "91" . $r['whatsapp_no'] . "";
				}
				$r['address'] = isset($r['address']) ? htmlentities($r['address']) : "";
				$r['shipping_address'] = isset($r['shipping_address']) ? htmlentities($r['shipping_address']) : "";
				$r['billing_address'] = isset($r['billing_address']) ? htmlentities($r['billing_address']) : "";

				$r['password'] = isset($r['password']) ? $r['password'] : "";
				$r['cst'] = isset($r['cst']) ? $r['cst'] : "";
				$r['vat'] = isset($r['vat']) ? $r['vat'] : "";
				$r['excise'] = isset($r['excise']) ? $r['excise'] : "";
				$r['bank_name'] = isset($r['bank_name']) ? $r['bank_name'] : "";
				$r['account_no'] = isset($r['account_no']) ? $r['account_no'] : "";
				$r['forgot_pass_string'] = isset($r['forgot_pass_string']) ? $r['forgot_pass_string'] : "";
				$r['ifsc_code'] = isset($r['ifsc_code']) ? $r['ifsc_code'] : "";
				$r['customer_type'] = $customer_flag_array[$r['customer_flag']];

				$is_quotation = $this->db->rp_getTotalRecord("quotation_detail", "customer_id='" . $r['id'] . "' AND isDelete=0", 0);
				$is_order = $this->db->rp_getTotalRecord("orders", "customer_id='" . $r['id'] . "' AND isDelete=0", 0);

				// echo "is_quotation=".$is_quotation;
				// echo "is_order=".$is_order;

				$is_order_approve = $this->db->rp_getTotalRecord("orders", "customer_id='" . $r['id'] . "' AND isDelete=0 AND status=1", 0);
				$is_order_pending = $this->db->rp_getTotalRecord("orders", "customer_id='" . $r['id'] . "' AND isDelete=0 AND status=0", 0);

				if ($is_quotation == 0 && $is_order == 0 && $r['customer_flag'] == 1) {
					// light pink
					$r['color_code'] = '#FFB6C1';
				} else if ($is_quotation == 0 && $is_order == 0) {
					// sky blue
					$r['color_code'] = '#ADD8E6';
				} else if ($is_order_approve > 0 && $is_order_pending > 0) {
					$r['color_code'] = '#ffffff';
				} else if ($is_order_approve > 0) {
					// light green
					$r['color_code'] = '#AEDCAE';
				} else if ($is_order_pending > 0) {
					// light maroon
					$r['color_code'] = '#FF9377';
				} else {
					$r['color_code'] = '';
				}

				if ($area_id != "") {
					$r['area_id'] = $area_id;
				} else {
					$first_area = $this->db->rp_getData("executive_map_area", "area_id", "executive_id='" . $r['id'] . "'", "id ASC LIMIT 1", 0);
					if ($first_area) {
						$first_area = mysqli_fetch_assoc($first_area);
						$first_area = $first_area['area_id'];
					}
					$r['area_id'] = $first_area;
				}

				/* Sales person name get */
				$r['sales_person_name'] = $this->db->rp_getValue("sales_executive", "name", "isDelete=0 AND id = '" . $r['seid'] . "' ");
				/* Sales person name get */

				$result[] = $r;
			}
			if (!empty($result)) {
				$visitToUppercase = $this->db->toUpperCaseAssocArray($result);
				$ack = array(
					"ack" => 1,
					"ack_msg" => "Successfully Get Customer !!",
					"developer_msg" => "You got it!!",
					"result" => $visitToUppercase,
				);
				return $ack;
			} else {
				$visitToUppercase = $this->db->toUpperCaseAssocArray($result);
				$ack = array(
					"ack" => 0,
					"ack_msg" => "No Customer Found !!",
					"developer_msg" => "No Customer found!!",
					"result" => $visitToUppercase,
				);
				return $ack;
			}
		} else {
			$visitToUppercase = $this->db->toUpperCaseAssocArray($result);
			$ack = array(
				"ack" => 0,
				"ack_msg" => "No Customer Found !!",
				"developer_msg" => "No Customer found!!",
				"result" => $visitToUppercase
			);
			return $ack;
		}
	}


	public function GetCustomerDetail($detail)
	{
		// echo "hello";exit();
		extract($detail);
		$result = array();
		$customer_id = array();
		$sales_area_id = array();

		$where = "isDelete=0 AND isActive=1";
		if ($cid != "") {
			$where .= " AND id=" . $cid;
		}

		$data  = $this->db->rp_getData('executive', "*", $where, "", 0);
		//$data  = $this->db->rp_getData('executive', "*", $where, "created_date DESC", 0, $limit);
		if ($data) {

			while ($r = mysqli_fetch_assoc($data)) {
				$r['cname'] 	= $r['cname'];
				$r['phone'] 	= $r['phone'];
				if ($r['phone'] == "") {
					$r['phone'] = "";
				}
				$r['type_of_company_name'] = $this->db->rp_getValue("company_master", "name", "isDelete=0 AND id='" . $r['type_of_company'] . "'");
				$r['pricelist_name'] = $this->db->rp_getValue("price_list", "pricelist_name", "isDelete=0 AND id='" . $r['price_list_id'] . "'");
				$r['transport_by_name'] = $this->db->rp_getValue("transport_by", "name", "isDelete=0 AND id='" . $r['transport_by_id'] . "'");
				$r['transporter_name'] = $this->db->rp_getValue("transport_master", "name", "isDelete=0 AND id='" . $r['transporter_id'] . "'");
				if ($r['transporter_name'] == "") {
					$r['transporter_name'] = "";
				}
				if ($r['transport_by_name'] == "") {
					$r['transport_by_name'] = "";
				}

				$r['address'] = isset($r['address']) ? htmlentities($r['address']) : "";
				$r['shipping_address'] = isset($r['shipping_address']) ? htmlentities($r['shipping_address']) : "";
				$r['billing_address'] = isset($r['billing_address']) ? htmlentities($r['billing_address']) : "";

				$img = explode(",", $r['image_path']);
				$imgpath = array();
				for ($i = 0; $i < sizeof($img); $i++) {
					$imgpath[] = SITEURL . "resource/image/" . $this->db->rp_getValue("media", "url", "reference_id='" . $r['id'] . "' AND id='" . $img[$i] . "'", 0);
				}
				$r['image_path'] = ($r['image_path'] != "") ? $imgpath : [];
				$r['adate'] = date('d-m-Y', strtotime($r['created_date']));
				$r['created_date'] = date('d-m-Y', strtotime($r['created_date']));
				if ($r['whatsapp_no'] != "") {
					$r['whatsapp_no'] = "91" . $r['whatsapp_no'] . "";
				}

				if ($r['category_id'] != "") {
					$cat_ids = explode(',', $r['category_id']);
					//echo "dssd".sizeof($cat_ids);exit;
					$cat_name = array();
					for ($ji = 0; $ji < sizeof($cat_ids); $ji++) {

						$tcid = $this->db->rp_getValue("category_master", "tcid", "isDelete=0 AND id='" . $cat_ids[$ji] . "'");
						$top_cat_name = $this->db->rp_getValue("top_category_master", "name", "isDelete=0 AND id='" . $tcid . "'");
						$cat_name1 = $this->db->rp_getValue("category_master", "name", "isDelete=0 AND id='" . $cat_ids[$ji] . "'");


						$cat_name[] = $top_cat_name . " " . $cat_name1;
					}
					//print_r($cat_name);exit;
					$r['category_name'] = implode(',', $cat_name);
				} else {
					$r['category_name'] = "";
				}
				if ($r['top_category_id'] != "") {
					$tc_ids = explode(',', $r['top_category_id']);
					//echo "dssd".sizeof($cat_ids);exit;
					$tc_name = array();
					for ($l = 0; $l < sizeof($tc_ids); $l++) {


						$top_cat_name = $this->db->rp_getValue("top_category_master", "name", "isDelete=0 AND id='" . $tc_ids[$l] . "'");


						$tc_name[] = $top_cat_name;
					}
					//print_r($cat_name);exit;
					$r['top_category_name'] = implode(',', $tc_name);
				} else {
					$r['top_category_name'] = "";
				}

				//$r['address']=strval($r['address']);
				$r['country_id'] = $this->db->rp_getValue("country", "id", "name='" . $r['country'] . "' AND isDelete=0");
				$r['state_id'] = $this->db->rp_getValue("class", "id", "name='" . $r['state'] . "' AND isDelete=0");
				$r['main_city_id'] = $this->db->rp_getValue("city", "id", "name='" . $r['main_city'] . "' AND isDelete=0");
				$r['city_id'] = $this->db->rp_getValue("area", "id", "name='" . $r['city'] . "' AND isDelete=0");
				$r['zone_id'] = $this->db->rp_getValue("zone", "id", "name='" . $r['zone'] . "' AND isDelete=0");

				if ($r['latitude'] != "" && $r['latitude'] != "0.0" && $r['longitude'] != "0.0" && $r['longitude'] != "") {
					$r['is_location'] = "1";
				} else {
					$r['is_location'] = "0";
				}

				if ($area_id != "") {
					$r['area_id'] = $area_id;
				} else {
					$first_area = $this->db->rp_getData("executive_map_area", "area_id", "executive_id='" . $r['id'] . "'", "id ASC LIMIT 1", 0);
					if ($first_area) {
						$first_area = mysqli_fetch_assoc($first_area);
						$first_area = $first_area['area_id'];
					}
					$r['area_id'] = $first_area;
				}
				/* Sales person name get */
				$r['sales_person_name'] = $this->db->rp_getValue("sales_executive", "name", "isDelete=0 AND id = '" . $r['seid'] . "' ");
				/* Sales person name get */
				$result[] = $r;
			}
			//print_r($result);exit();
			if (!empty($result)) {
				$visitToUppercase = $this->db->toUpperCaseAssocArray($result);
				$ack = array("ack" => 1, "ack_msg" => "Successfully Get Customer !!", "developer_msg" => "You got it!!", "result" => $visitToUppercase,);
				return $ack;
			} else {
				$visitToUppercase = $this->db->toUpperCaseAssocArray($result);

				$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!", "result" => $visitToUppercase,);
				return $ack;
			}
		} else {
			$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!", "result" => $result);
			return $ack;
		}
	}
	public function GetCustomerForChain($detail)
	{
		extract($detail);
		$result = array();
		$customer_id = array();
		$sales_area_id = array();
		$searchName = $_REQUEST['searchName'] ? $_REQUEST['searchName'] : "";
		$limit = $this->getLimit();

		$where = "isDelete=0 AND isActive=1 AND type_of_executive=" . $type;

		if ($detail['superstokist_id'] != "" && $detail['superstokist_id'] != "null") {
			$where .= " AND super_stockist_id= " . $detail['superstokist_id'];
		}

		if ($detail['dealer_id'] != "") {
			$where .= " AND dealer_distributor_id= " . $detail['dealer_id'];
		}
		if ($detail['type_of_company'] != "") {
			$where .= " AND type_of_company= " . $detail['type_of_company'];
		}

		/*if($detail['superstokist_id']!=""){
			$where .= " AND super_stockist_id= ".$detail['superstokist_id'];
		}*/

		//search 
		if ($searchName != "") {
			$where .= " AND (company_name like '%" . $searchName . "%' OR cname like '%" . $searchName . "%' OR phone  LIKE '%" . $searchName . "%' OR zip LIKE '%" . $searchName . "%' OR client_code LIKE '%" . $searchName . "%')";
		}

		$data  = $this->db->rp_getData('executive', "*", $where . " AND customer_flag=" . $detail['customer_flag'], "cname ASC", 0, $limit);
		//$data  = $this->db->rp_getData('executive', "*", $where, "created_date DESC", 0, $limit);
		if ($data) {
			while ($r = mysqli_fetch_assoc($data)) {
				$r['cname'] 	= $r['cname'];
				$r['phone'] 	= $r['phone'];
				$img = explode(",", $r['image_path']);
				$imgpath = array();
				for ($i = 0; $i < sizeof($img); $i++) {
					$imgpath[] = SITEURL . "resource/image/" . $this->db->rp_getValue("media", "url", "reference_id='" . $r['id'] . "' AND id='" . $img[$i] . "'", 0);
				}
				$r['image_path'] = ($r['image_path'] != "") ? $imgpath : [];
				$r['adate'] = date('d-m-Y', strtotime($r['created_date']));
				$r['created_date'] = date('d-m-Y', strtotime($r['created_date']));
				if ($r['whatsapp_no'] != "") {
					$r['whatsapp_no'] = "91" . $r['whatsapp_no'] . "";
				}

				$r['address'] = isset($r['address']) ? htmlentities($r['address']) : "";
				$r['shipping_address'] = isset($r['shipping_address']) ? htmlentities($r['shipping_address']) : "";
				$r['billing_address'] = isset($r['billing_address']) ? htmlentities($r['billing_address']) : "";

				$r['country_id'] = $this->db->rp_getValue("country", "id", "name='" . $r['country'] . "' AND isDelete=0");
				$r['state_id'] = $this->db->rp_getValue("state", "id", "name='" . $r['state'] . "' AND isDelete=0");
				$r['district_id'] = $this->db->rp_getValue("district", "id", "name='" . $r['district'] . "' AND isDelete=0");
				$r['main_city_id'] = $this->db->rp_getValue("city", "id", "name='" . $r['main_city'] . "' AND isDelete=0");
				$r['city_id'] = $this->db->rp_getValue("area", "id", "name='" . $r['city'] . "' AND isDelete=0");


				if ($area_id != "") {
					$r['area_id'] = $area_id;
				} else {
					$first_area = $this->db->rp_getData("executive_map_area", "area_id", "executive_id='" . $r['id'] . "'", "id ASC LIMIT 1", 0);
					if ($first_area) {
						$first_area = mysqli_fetch_assoc($first_area);
						$first_area = $first_area['area_id'];
					}
					$r['area_id'] = $first_area;
				}
				/* Sales person name get */
				$r['sales_person_name'] = $this->db->rp_getValue("sales_executive", "name", "isDelete=0 AND id = '" . $r['seid'] . "' ");
				/* Sales person name get */
				$result[] = $r;
			}
			if (!empty($result)) {
				$visitToUppercase = $this->db->toUpperCaseAssocArray($result);
				$ack = array("ack" => 1, "ack_msg" => "Successfully Get Customer !!", "developer_msg" => "You got it!!", "result" => $visitToUppercase,);
				return $ack;
			} else {
				$visitToUppercase = $this->db->toUpperCaseAssocArray($result);

				$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!", "result" => $visitToUppercase,);
				return $ack;
			}
		} else {
			$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!", "result" => $result);
			return $ack;
		}
		/*} else {
			$ack = array("ack" => 0, "ack_msg" => "No Customer Found !!", "developer_msg" => "No Customer found!!");
			return $ack;
		}*/
	}

	/*Account create function*/
	function CreateCustomerAccount($cid)
	{
		$date = date('Y-m-d H:i:s');
		$customer_type = $this->db->rp_getValue("application_login", "customer_type", "isDelete=0", 0);
		$account_name = $this->db->rp_getValue("executive", "company_name", "isDelete=0 AND id='" . $cid . "'", 0);
		$customer_type = $this->db->rp_getValue("executive", "type_of_executive", "isDelete=0 AND id='" . $cid . "'", 0);
		if ($customer_type == '1') {
			$account_type = '29';
		} else {
			$account_type = '12';
		}
		$last_account_id = $this->db->rp_getValue("account", "MAX(id)", "isDelete=0", 0);
		$last_account_no = $this->db->rp_getValue("account", "acc_no", "id=" . $last_account_id . "", 0);
		if ($last_account_no == "") {
			$last_account_no = "0001";
		} else {
			$last_account_no = str_pad($last_account_no + 1, 4, 0, STR_PAD_LEFT);
		}
		$rows = array(
			"cid",
			"acc_no",
			"account_type",
			"account_name",
			"customer_type",
		);
		$values = array(
			$cid,
			$last_account_no,
			$account_type,
			$account_name,
			$customer_type,
		);

		$account_info_insert = $this->db->rp_insert("account", $values, $rows, 0);
		return $account_info_insert;
	}
	/*Account create function*/

	public function UpdateExecutiveProfile($detail, $file)
	{
		extract($detail);
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

			$fileName  = $this->db->clean($file["image_path"]["name"]);
			$fileSize  = round($file["image_path"]["size"]); // BYTES

			$adate   = date('Y-m-d H:i:m');

			$extension = end(explode(".", $fileName));
			$fileName  = 'image_' . substr(sha1(time()), 0, 6) . "." . $extension;
			$tempPath  = "../images/super_stockist/" . $fileName;

			//move_uploaded_file($fileName,$tempPath);
			move_uploaded_file($file["image_path"]['tmp_name'], $tempPath);
			$image_path = $fileName;
			$rows 	= array(
				"image_path" => $image_path,
			);

			$uid1 = $this->db->rp_update("executive", $rows, "id='" . $customer_id . "'", 0);

			// unset($old_image_path);
		} else {
			$image_path = "";
			//unset($old_image_path);
		}
		//$image_path = isset($image_path)?$image_path:"";
		//$adate	= date('Y-m-d H:i:s');
		$rows 	= array(
			"company_name" => $company_name,
			"cname"  	   => $cname,
			"email" 	   => $email,
			"address" 	   => $address,
			"country"      => $country,
			"state"        => $state,
			"city"         => $city,

		);

		$uid = $this->db->rp_update("executive", $rows, "id='" . $customer_id . "'", 0);
		if ($uid != 0) {
			$reply = array("ack" => 1, "developer_msg" => "Update Successfully", "ack_msg" => "Success! Update Customer Successfully.");
			return $reply;
		} else {
			$reply = array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Update Record Failed.");
			return $reply;
		}
	}
	//--------------------------------------------------------------//

	public function InsertMyRoute($detail)
	{
		extract($detail);
		//   	$isDuplicate = false;
		//   	$count = $this->db->rp_getTotalRecord("leave_request","sales_executive_id='".$sales_executive_id."' AND start_date='".$start_date."' AND start_time='".$start_time."' AND end_date='".$end_date."' AND  end_time='".$end_time."' AND isDelete=0",0);
		// if($count>=1)
		// {
		// 	$isDuplicate = true;
		// }


		//   	if($isDuplicate)
		//   	{
		//   		$ack=array("ack"=>0,"ack_msg"=>"Already Data Exist!!","developer_msg"=>"Already Data Exist");
		// 	return $ack;
		//   	}

		//   	$sales_executive_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_executive_id."'",0);
		$rows	 = array(
			"date",
			// "class_id",
			// "area_id",
			"route_id",
			"customer_id",
			"sales_id",
			"remark",
			"no_order_inq_id",
		);
		$values	 = array(
			$date,
			// $class_id,
			// $area_id,
			$route_id,
			$customer_id,
			$sales_id,
			$remark,
			$no_order_inq_id,
		);

		// $isduplicat=$this->db->rp_dupCheck("my_route","date='".$date."' AND customer_id='".$customer_id."' AND sales_id='".$sales_id."'");
		// if($isduplicat){
		// 	$ack=array("ack"=>0,"ack_msg"=>"Root Already Created","developer_msg"=>"not inserted!!","result"=>array(),);
		// 	return $ack;
		// }else{

		$uid = $this->db->rp_insert("my_route", $values, $rows, 0);

		// }
		if ($uid != 0) {
			$ack = array("ack" => 1, "ack_msg" => "Successfully Inserted Sales Route Request !!", "developer_msg" => "You got it!!", "result" => $result, "id" => $uid);
			return $ack;
		} else {
			$ack = array("ack" => 0, "ack_msg" => "not inserted !!", "developer_msg" => "not inserted!!", "result" => array(),);
			return $ack;
		}
	}


	public function InsertMasterRoute($detail)
	{
		// echo "<pre>"; print_r($detail); exit();
		extract($detail);


		$state = $this->db->rp_getValue("class", "name", " isDelete=0 AND  id='" . $class_id . "'", 0);
		$main_city = $this->db->rp_getValue("city", "name", " isDelete=0 AND  id='" . $main_city_id . "'", 0);
		$city = $this->db->rp_getValue("area", "name", " isDelete=0 AND  id='" . $area_id . "'", 0);

		$rows	 = array(
			"start_date",
			"end_date",
			"class_id",
			"main_city_id",
			"area_id",
			"sales_id",
			"state",
			"main_city",
			"city",
		);
		$values	 = array(
			$start_date,
			$end_date,
			$class_id,
			$main_city_id,
			$area_id,
			$sales_id,
			$state,
			$main_city,
			$city,
		);

		// $max_end_date = $this->db->rp_getValue("master_route","MAX(CAST(end_date AS DATE))","sales_id='".$sales_id."' AND state='".$state."' AND city='".$city."' AND isDelete=0",0);

		$isduplicat = $this->db->rp_dupCheck("master_route", "sales_id='" . $sales_id . "' AND ((start_date = '" . $start_date . "' AND end_date = '" . $end_date . "') OR end_date >= '" . date('Y-m-d', strtotime($start_date)) . "') AND state='" . $state . "' AND city='" . $city . "' AND isDelete=0", 0);

		if ($isduplicat) {
			$ack = array("ack" => 0, "ack_msg" => "Route Already Created", "developer_msg" => "not inserted!!", "result" => array(),);
			return $ack;
		} else {
			$uid = $this->db->rp_insert("master_route", $values, $rows, 0);
		}
		if ($uid != 0) {
			$ack = array("ack" => 1, "ack_msg" => "Successfully Inserted Master Route Request !!", "developer_msg" => "You got it!!", "result" => $result, "id" => $uid);
			return $ack;
		} else {
			$ack = array("ack" => 0, "ack_msg" => "not inserted !!", "developer_msg" => "not inserted!!", "result" => array(),);
			return $ack;
		}
	}
}
