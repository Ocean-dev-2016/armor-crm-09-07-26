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
	
	1 	login_customer
	
*/
if ($is_valid_api_key) {
	if ($is_valid_service) {
		include('../include/class.visit.php');

		$objVisit = new Visit();

		if ($service == 'Add_visit' || $service == 75) {
			//exit;
			$detail['user_id'] 			= isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$detail['customer_id'] 		= isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$detail['inquiry_id'] 		= isset($_REQUEST['inquiry_id']) ? $db->clean($_REQUEST['inquiry_id']) : "";
			$detail['latitude'] 		= isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";
			$detail['longitude'] 		= isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
			$detail['remark'] 			= isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
			$detail['app_address'] 			= isset($_REQUEST['app_address']) ? $db->clean($_REQUEST['app_address']) : "";
			$detail['created_date'] 	= date("Y-m-d");
			$detail['start_date_time'] 	= date("Y-m-d H:i");

			$detail['purpose_id'] 			= isset($_REQUEST['purpose_id']) ? $db->clean($_REQUEST['purpose_id']) : "";
			$detail['flag'] 			= isset($_REQUEST['flag']) ? $db->clean($_REQUEST['flag']) : "";
			$detail['type_of_company'] 			= isset($_REQUEST['company_id']) ? $db->clean($_REQUEST['company_id']) : "";

			$count = $db->rp_getTotalRecord("visit", "user_id='" . $detail['user_id'] . "' AND DATE(stop_date_time)=0000-00-00 AND DATE(start_date_time)!=0000-00-00 AND isDelete=0", 0);

			if ($count == 0) {
				if ($detail['flag'] != '1') {
					$reply = $objVisit->AddVisit($detail, $_FILES);
				} else {
					$reply = array("ack" => 1);
				}
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Other visit alredy started", "ack_msg" => "Your Other Visit Is Already Started Please Stop That First");
			}
			echo json_encode($reply);
		} else if ($service == 'get_visit' || $service == 76) {
			$system = new System();
			$limit = $system->getLimit();
			$visit = array();
			$user_id = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$visit_no = isset($_REQUEST['visit_no']) ? $db->clean($_REQUEST['visit_no']) : "";
			$type_of_company = isset($_REQUEST['company_id']) ? $db->clean($_REQUEST['company_id']) : "";

			if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
				$Where .= " DATE(created_date) <= '" . date("Y-m-d", strtotime($_REQUEST['ToDate'])) . "' AND";
			}

			if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
				$Where .= " DATE(created_date) >= '" . date("Y-m-d", strtotime($_REQUEST['FromDate'])) . "' AND ";
			}

			if ($customer_id != "") {
				$Where .= "customer_id='" . $customer_id . "' AND  user_id='" . $user_id . "' AND ";
			}
			if ($type_of_company != "") {
				$Where .= "type_of_company='" . $type_of_company . "' AND ";
			}

			if ($visit_no != "") {
				$Where .= "id='" . $visit_no . "' AND user_id='" . $user_id . "' AND ";
			} else {
				$Where .= "user_id='" . $user_id . "' AND ";
			}

			if ($user_id) {
				$visit_data = $db->rp_getData("visit", "*", $Where . "isDelete=0 AND isActive=1", "id DESC", 0, $limit);
				if ($visit_data) {
					$ENTRY_FLAG = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
					while ($visit_d = mysqli_fetch_assoc($visit_data)) {
						$visit_d['stop_remark'] = htmlentities($visit_d['stop_remark']);
						// $visit_d['customer_name'] = $db->rp_getValue("customer","name","id='".$visit_d['customer_id']."'",0);
						$visit_d['customer_type'] = "";
						$visit_d['client_code'] = "";
						$visit_d['company_name'] = "";
						$visit_d['customer_flag'] = "";
						$customer_flag_array = array("0" => "Customer", "1" => "Prospect Customer");
						if ($visit_d['visit_stop_flag'] == 4) {

							$visit_d['company_name'] = $visit_d['firm_name'] != "" ? $visit_d['firm_name'] : "";
							$visit_d['customer_name'] = $visit_d['client_name'] != "" ? $visit_d['client_name'] : "";
						} else {
							if ($visit_d['customer_id'] != 0) {
								$get_customer_d = $db->rp_getData("executive", "cname,type_of_executive,client_code,customer_flag,company_name", "id='" . $visit_d['customer_id'] . "'", 0);
								$get_customer_e = mysqli_fetch_assoc($get_customer_d);
								$visit_d['customer_name'] = $get_customer_e['cname'];
								$visit_d['customer_type'] = $get_customer_e['type_of_executive'];
								$visit_d['client_code'] = $get_customer_e['client_code'];
								$visit_d['company_name'] = $get_customer_e['company_name'];
								$visit_d['customer_flag'] = $customer_flag_array[$get_customer_e['customer_flag']];
							} else if ($visit_d['inquiry_id'] != 0) {
								$visit_d['company_name'] = $db->rp_getValue("no_order_inquiry", "company_name", "id='" . $visit_d['inquiry_id'] . "'", 0);

								$visit_d['contact_person'] = $db->rp_getValue("no_order_inquiry", "person_name", "id='" . $visit_d['inquiry_id'] . "'", 0);

								$visit_d['customer_name'] = $visit_d['company_name'] . " - " . $visit_d['contact_person'];
							} else {
								$visit_d['customer_name'] = "";
							}
						}
						// echo "<pre>";
						// print_r($visit_d);die;
						$visit_d['inquiry_no'] = "#INQ/" . $visit_d['inquiry_id'];
						$img = explode(",", $visit_d['image_path']);
						$imgpath = array();
						for ($i = 0; $i < sizeof($img); $i++) {
							$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $visit_d['id'] . "' AND id='" . $img[$i] . "'");
						}
						$visit_d['image_path'] = ($visit_d['image_path'] != "") ? $imgpath : "";


						$stop_img = explode(",", $visit_d['stop_image_path']);
						$stop_imgpath = array();
						for ($j = 0; $j < sizeof($stop_img); $j++) {
							$stop_imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $visit_d['id'] . "' AND id='" . $stop_img[$j] . "'");
						}
						$visit_d['stop_image_path'] = ($visit_d['stop_image_path'] != "") ? $stop_imgpath : "";


						$visit_d['created_date'] = date('d F Y h:i A', strtotime($visit_d['created_date']));
						$customer_type_get = $db->rp_getValue("executive", "type_of_executive", "id='" . $visit_d['customer_id'] . "'", 0);

						// if ($visit_d['customer_id']!=0) 
						// {
						// 	$type_of_executive = $db->rp_getValue("executive","type_of_executive","id='".$visit_d['customer_id']."'",0);
						// }
						// else
						// {	
						// 	if ($visit_d['inquiry_id']!=0) 
						// 	{
						// 		$executive_type = $db->rp_getValue("no_order_inquiry","executive_type","id='".$visit_d['inquiry_id']."'",0);
						// 	}

						// }




						if ($visit_d['customer_id'] != 0) {
							$type_of_executive = $db->rp_getValue("executive", "type_of_executive", "id='" . $visit_d['customer_id'] . "'", 0);

							$visit_d['customer_type'] = $db->rp_getValue("customer_type", "name", "id='" . $type_of_executive . "'", 0);
							$visit_d['customer_type_id'] = $db->rp_getValue("customer_type", "id", "id='" . $type_of_executive . "'", 0);
							$visit_d['state'] = $db->rp_getValue("executive", "state", "id='" . $visit_d['customer_id'] . "'", 0);
							$visit_d['city'] = $db->rp_getValue("executive", "city", "id='" . $visit_d['customer_id'] . "'", 0);
							if ($visit_d['visit_stop_flag'] == 4) {
								$visit_d['c_mobile_no'] = $visit_d['contact_number'] != "" ? $visit_d['contact_number'] : "";
							} else {
								$visit_d['c_mobile_no'] = $db->rp_getValue("executive", "mobile_no1", "id='" . $visit_d['customer_id'] . "'", 0);
							}
						} else if ($visit_d['inquiry_id'] != 0) {
							$executive_type = $db->rp_getValue("no_order_inquiry", "executive_type", "id='" . $visit_d['inquiry_id'] . "'", 0);

							$visit_d['customer_type'] = $db->rp_getValue("customer_type", "name", "id='" . $executive_type . "'", 0);
						} else {
							$visit_d['customer_type'] = "";
						}


						// $visit_d['customer_type'] = $db->rp_getValue("customer_type","name","id='".$customer_type_get."'",0);
						$visit_d['purpose_name'] = $db->rp_getValue("purpose_master", "name", "id='" . $visit_d['purpose_id'] . "'", 0);


						if ($visit_d['start_date_time'] == "1970-01-01 00:00:00" || $visit_d['start_date_time'] == "0000-00-00 00:00:00" || $visit_d['start_date_time'] == "") {

							$visit_d['start_date_time'] = "";
						} else {
							$visit_d['start_date_time'] = date('d-m-Y H:i:s', strtotime($visit_d['start_date_time']));
						}
						if ($visit_d['stop_date_time'] == "1970-01-01 00:00:00" || $visit_d['stop_date_time'] == "0000-00-00 00:00:00" || $visit_d['stop_date_time'] == "") {

							$visit_d['stop_date_time'] = "";
						} else {
							$visit_d['stop_date_time'] = date('d-m-Y H:i:s', strtotime($visit_d['stop_date_time']));
						}

						if ($visit_d['stop_date_time'] == "") {
							$visit_d['is_pending'] = "1";
							$visit_d['color'] = "#CF4F4F";
						} else {
							$visit_d['color'] = "#898181";
							$visit_d['is_pending'] = "0";
						}
						if ($visit_d['start_date_time'] != "" && 	$visit_d['stop_date_time'] != "") {
							$date1 = strtotime($visit_d['start_date_time']);
							$date2 = strtotime($visit_d['stop_date_time']);

							// Formulate the Difference between two dates
							$diff = abs($date2 - $date1);
							// To get the hour, subtract it with years,
							// months & seconds and divide the resultant
							// date into total seconds in a hours (60*60)
							$hours = floor(($diff - $years * 365 * 60 * 60 * 24
								- $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24)
								/ (60 * 60));

							// To get the minutes, subtract it with years,
							// months, seconds and hours and divide the
							// resultant date into total seconds i.e. 60
							$minutes = floor(($diff - $years * 365 * 60 * 60 * 24
								- $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
								- $hours * 60 * 60) / 60);

							// To get the minutes, subtract it with years,
							// months, seconds, hours and minutes
							$seconds = floor(($diff - $years * 365 * 60 * 60 * 24
								- $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
								- $hours * 60 * 60 - $minutes * 60));

							// Print the result
							/*printf("%d hours, "
						       . "%d minutes, %d seconds",$hours, $minutes, $seconds);*/

							$visit_d['total_visit_duration'] = $hours . ":" . $minutes . ":" . $seconds;
						} else {
							$visit_d['total_visit_duration'] = "";
						}

						if ($visit_d['entry_flag'] != "" && $visit_d['entry_flag'] != "null" && $visit_d['entry_flag'] != "NULL"  && $visit_d['entry_flag'] != null && $visit_d['entry_flag'] != NULL) {
							$visit_d['entry_flag'] = $ENTRY_FLAG[$visit_d['entry_flag']];
						}

						if ($visit_d['update_entry_flag'] != "" && $visit_d['update_entry_flag'] != "null" && $visit_d['update_entry_flag'] != "NULL"  && $visit_d['update_entry_flag'] != null && $visit_d['update_entry_flag'] != NULL) {
							$visit_d['update_entry_flag'] = $ENTRY_FLAG[$visit_d['update_entry_flag']];
						}

						$visit_d['designation_name'] = $db->rp_getValue("visit_designation", "name", "isDelete=0 AND id = '" . $visit_d['designation'] . "' ");
						$visit_d['designation_name'] = $visit_d['designation_name'] ? $visit_d['designation_name'] : "";

						$visit[] = $visit_d;
					}
				}
				if (!empty($visit_data)) {
					$visitToUppercase = $db->toUpperCaseAssocArray($visit);
					$reply = array("ack" => 1, "developer_msg" => "Visit Detail Get successfully!!", "ack_msg" => "Visit Detail Get successfully!!", "result" => $visitToUppercase);
				} else {
					$reply = array("ack" => 0, "developer_msg" => "No Visit Found!!", "ack_msg" => "No Visit Found!!");
				}
				echo json_encode($reply);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "No Visit Found!!", "ack_msg" => "No Visit Found!!");
				echo json_encode($reply);
			}
		} else if ($service == 'get_expence_category' || $service == 77) {
			$visit = array();
			$where = "isDelete=0 AND isActive=1";
			$expense_claim_type = isset($_REQUEST['expense_claim_type']) ? $db->clean($_REQUEST['expense_claim_type']) : "";
			if ($expense_claim_type !== "") {
				$where .= " AND expense_claim_type='" . $expense_claim_type . "'";
			}
			$selectFields = "id,name,expense_claim_type";
			$expence_category = $db->rp_getData("expence_category", $selectFields, $where);
			if ($expence_category) {
				while ($visit_d = mysqli_fetch_assoc($expence_category)) {
					if (!isset($visit_d['expense_claim_type'])) {
						$visit_d['expense_claim_type'] = "1";
					}
					$visit[] = $visit_d;
				}
			}

			if (!empty($visit)) {
				$reply = array("ack" => 1, "developer_msg" => "Expence Category Get successfully!!", "ack_msg" => "Expence Category Get successfully!!", "result" => $visit);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Expence Category Not Get!!", "ack_msg" => "Expence Category Not Get!!");
			}
			echo json_encode($reply);
		} else if ($service == 'get_expense_claim_type' || $service == 229) {
			$claimTypes = array(
				array("id" => "1", "name" => "Regular Expense"),
				array("id" => "2", "name" => "Advance Expense"),
			);
			$reply = array(
				"ack" => 1,
				"developer_msg" => "Expense claim type list fetched successfully!!",
				"ack_msg" => "Expense claim type list fetched successfully!!",
				"result" => $claimTypes,
			);
			echo json_encode($reply);
		} else if ($service == 'get_expence_subcategory' || $service == 108) {
			$type_array = array("1" => "General", "2" => "Kilometer", "3" => "Food");
			$expense_subcat = array();
			$expense_category_id = isset($_REQUEST['expense_category_id']) ? $db->clean($_REQUEST['expense_category_id']) : "";
			$sales_executive_id = isset($_REQUEST['sales_id']) ? $db->clean($_REQUEST['sales_id']) : "";

			if ($expense_category_id && $sales_executive_id) {
				$expence_subcategory = $db->rp_getData("expence_sub_category", "id,name,expense_category_id,expense_type,image_flag,min_time,max_time,fix_amount", "expense_category_id='" . $expense_category_id . "' AND sales_executive_id='" . $sales_executive_id . "' AND slug!='bike' AND slug!='car' AND isDelete=0 AND isActive=1", "", 0);

				if ($expence_subcategory) {
					while ($expence_subcategory_d = mysqli_fetch_assoc($expence_subcategory)) {
						if ($expence_subcategory_d['expense_type'] == 0) {
							$expence_subcategory_d['expense_type_name'] = "";
						} else {
							$expence_subcategory_d['expense_type_name'] = $type_array[$expence_subcategory_d['expense_type']];
						}

						/*get General type parametre*/
						if ($expence_subcategory_d['expense_type'] == 1) {
							if ($expence_subcategory_d['image_flag'] == 0) {
								$expence_subcategory_d['image_flag'] = "";
							} else {
								$expence_subcategory_d['image_flag'] = $expence_subcategory_d['image_flag'];
							}
						}
						/*get General wise parametre*/


						/*get Kilometer type parametre*/
						if ($expence_subcategory_d['expense_type'] == 2) {
							if ($expence_subcategory_d['image_flag'] == 0) {
								$expence_subcategory_d['image_flag'] = "";
							} else {
								$expence_subcategory_d['image_flag'] = $expence_subcategory_d['image_flag'];
							}
							$expence_subcategory_d['fix_amount'] = $expence_subcategory_d['fix_amount'];
						}
						/*get Kilometer wise parametre*/


						/*get Food type parametre*/
						if ($expence_subcategory_d['expense_type'] == 2) {
							if ($expence_subcategory_d['image_flag'] == 0) {
								$expence_subcategory_d['image_flag'] = "";
							} else {
								$expence_subcategory_d['image_flag'] = $expence_subcategory_d['image_flag'];
							}
							$expence_subcategory_d['min_time'] = $expence_subcategory_d['min_time'];
							$expence_subcategory_d['max_time'] = $expence_subcategory_d['max_time'];
							$expence_subcategory_d['fix_amount'] = $expence_subcategory_d['fix_amount'];
						}
						/*get Food wise parametre*/

						$expense_subcat[] = $expence_subcategory_d;
					}
				}

				if (!empty($expense_subcat)) {
					$reply = array("ack" => 1, "developer_msg" => "Expence Subcategory Detail Get successfully!!", "ack_msg" => "Expence Subcategory Detail Get successfully!!", "result" => $expense_subcat);
				} else {
					$reply = array("ack" => 0, "developer_msg" => "No Expence Subcategory Found!!", "ack_msg" => "No Expence Subcategory Found!!");
				}
				echo json_encode($reply);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "No Expence Subcategory Found!!", "ack_msg" => "No Expence Subcategory Found!!");
				echo json_encode($reply);
			}
		} else if ($service == 'update_visit' || $service == 122) {
			$detail['id'] 			    = isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
			$detail['user_id'] 			= isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			//$detail['customer_id'] 		= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
			$detail['stop_latitude'] 		= isset($_REQUEST['stop_latitude']) ? $db->clean($_REQUEST['stop_latitude']) : "";
			$detail['stop_longitude'] 		= isset($_REQUEST['stop_longitude']) ? $db->clean($_REQUEST['stop_longitude']) : "";
			$detail['stop_remark'] 			= isset($_REQUEST['stop_remark']) ? $db->clean($_REQUEST['stop_remark']) : "";
			$detail['stop_app_address'] 	= isset($_REQUEST['stop_app_address']) ? $db->clean($_REQUEST['stop_app_address']) : "";
			$detail['stop_date_time'] 		= date("Y-m-d H:i");
			$detail['customer_id'] 			= isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$detail['inquiry_id'] 			= isset($_REQUEST['inquiry_id']) ? $db->clean($_REQUEST['inquiry_id']) : "";
			$detail['visit_type'] 			= isset($_REQUEST['visit_type']) ? $db->clean($_REQUEST['visit_type']) : "";
			$detail['visit_stop_flag'] 			= isset($_REQUEST['visit_stop_flag']) ? $db->clean($_REQUEST['visit_stop_flag']) : "";
			$detail['product_name'] 			= isset($_REQUEST['product_name']) ? $db->clean($_REQUEST['product_name']) : "";
			$detail['firm_name'] 			= isset($_REQUEST['firm_name']) ? $db->clean($_REQUEST['firm_name']) : "";
			$detail['client_name'] 			= isset($_REQUEST['client_name']) ? $db->clean($_REQUEST['client_name']) : "";
			$detail['contact_number'] 			= isset($_REQUEST['contact_number']) ? $db->clean($_REQUEST['contact_number']) : "";
			$detail['name'] 			= isset($_REQUEST['name']) ? $db->clean($_REQUEST['name']) : "";
			$detail['mobile_no'] 			= isset($_REQUEST['mobile_no']) ? $db->clean($_REQUEST['mobile_no']) : "";
			$detail['email_id'] 			= isset($_REQUEST['email_id']) ? $db->clean($_REQUEST['email_id']) : "";
			$detail['designation_id'] 			= isset($_REQUEST['designation_id']) ? $db->clean($_REQUEST['designation_id']) : "";

			/*if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!=null)
				{*/

			if (empty($detail['name']) || empty($detail['mobile_no']) || empty($detail['email_id']) || empty($detail['designation_id'])) {
				$reply = ['ack' => 0, "ack_msg" => "Customer Person Name, Customer Person Mobile No, Customer Person Email ID, Customer Person Designation are mandatory please fill up first. "];
				echo json_encode($reply);
			} else {
				$reply = $objVisit->UpdateVisit($detail, $_FILES);
				if ($reply['ack'] == 1) {
					echo json_encode($reply);
				} else {
					echo json_encode($reply);
				}
			}
		} else if ($service == "visit_pdf_download" || $service == 198) {
			$visit_id = isset($_REQUEST['visit_id']) ? $_REQUEST['visit_id'] : "";
			if (!empty($visit_id) && $visit_id != "") {
				$ack = $objVisit->DownloadVisit($visit_id);
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Internal error!!", "developer_msg" => "Service Parameter missing or not valid!!", "extra" => array("requested_params" => $_REQUEST, "other" => array()));
			}
			$db->printJSON($ack);
		} else if ($service == 'get_customer_visit_count' || $service == 213) {
			$user_id = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$type = isset($_REQUEST['type']) ? $db->clean($_REQUEST['type']) : "";
			$type_of_company = isset($_REQUEST['company_id']) ? $db->clean($_REQUEST['company_id']) : "";

			if ($customer_id != "" && $type != "") {
				$where = "isDelete=0 AND isActive=1 AND customer_id='" . $customer_id . "'";

				if ($type == 'visit') {
					$dateColumn = "created_date";
				} else if ($type == 'orders') {
					$dateColumn = "order_date";
					$where .= " AND (status=4 OR status=5 OR status=6)";
				} else if ($type == 'quotation_detail') {
					$dateColumn = "quotation_date";
					$where .= " AND (status=1 OR status=4)";
				}

				$total_customer_cnt = $db->rp_getTotalRecord($type, $where, 0);

				$where_month = $where . " AND DATE(" . $dateColumn . ")>='" . date('Y-m-01') . "' AND DATE(" . $dateColumn . ")<='" . date('Y-m-t') . "'";
				$current_month_customer_cnt = $db->rp_getTotalRecord($type, $where_month, 0);

				$total_customer_cnt = ($total_customer_cnt) ? $total_customer_cnt : 0;
				$current_month_customer_cnt = ($current_month_customer_cnt) ? $current_month_customer_cnt : 0;

				$reply = array("ack" => 1, "developer_msg" => "Fetch successfully!!", "ack_msg" => "Fetch successfully!!", "total_customer_cnt" => strval($total_customer_cnt), "current_month_customer_cnt" => strval($current_month_customer_cnt));
			} else {
				$reply = array("ack" => 0, "developer_msg" => "Required Parameter missing!!", "ack_msg" => "Required Parameter missing!!");
			}
			echo json_encode($reply);
		} else if ($service == 'get_visit_company_person_detail_when_visit_start' || $service == 221) {
			$sales_id = isset($_REQUEST['sales_id']) ? $_REQUEST['sales_id'] : "";
			$mobile_no = isset($_REQUEST['mobile_no']) ? $_REQUEST['mobile_no'] : "";
			$get_visit_detail = $db->rp_getData("visit", "mobile_no,name,email_id,designation", "isDelete=0 AND mobile_no LIKE '%" . $mobile_no . "%' AND user_id = '" . $sales_id . "' ", "id DESC", 0, "1");
			if ($get_visit_detail) {
				$get_visit_detail = mysqli_fetch_assoc($get_visit_detail);
				$get_visit_detail['designation_name'] = $db->rp_getValue("visit_designation", "name", "isDelete=0 AND id = '" . $get_visit_detail['designation'] . "' ");
				$get_visit_detail['designation_name'] = $get_visit_detail['designation_name'] ? $get_visit_detail['designation_name'] : '';
				$ack = ['ack' => 1, "ack_msg" => "data fetched", "result" => $get_visit_detail];
			} else {
				$ack = ['ack' => 0, "ack_msg" => "data not fetched"];
			}
			$db->printJSON($ack);
		} else if ($service == 'get_visit_designation' || $service == 222) {
			$get_visit_designation = $db->rp_getData("visit_designation", "id,name", "isDelete=0");

			if ($get_visit_designation) {
				$result = array();
				while ($get_visit_designation_D = mysqli_fetch_assoc($get_visit_designation)) {
					$result[] = $get_visit_designation_D;
				}
				$ack = ['ack' => 1, "ack_msg" => "success", "result" => $result];
			} else {
				$ack = ['ack' => 0, "ack_msg" => "No Data Found"];
			}
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
$db->disconnect();
