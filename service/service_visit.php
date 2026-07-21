<?php
/*
 * Mobile API contract: always return a JSON response body.
 * The Android client must still null-check Retrofit body/errorBody, but this
 * prevents PHP fatal errors from producing an empty response.
 */
header('Content-Type: application/json; charset=utf-8');
ob_start();
register_shutdown_function(function () {
	$error = error_get_last();
	$fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
	if ($error && in_array($error['type'], $fatalTypes, true)) {
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		http_response_code(200);
		echo json_encode(array(
			"ack" => 0,
			"ack_msg" => "Server error. Please try again.",
			"developer_msg" => "Visit API fatal error: " . $error['message'],
		));
	}
});
set_exception_handler(function ($exception) {
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	http_response_code(200);
	echo json_encode(array(
		"ack" => 0,
		"ack_msg" => "Server error. Please try again.",
		"developer_msg" => "Visit API exception: " . $exception->getMessage(),
	));
	exit;
});

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

			/* Open/pending visit = started but not stopped */
			$pendingWhere = "user_id='" . $detail['user_id'] . "' AND isDelete=0 AND start_date_time IS NOT NULL AND start_date_time!='0000-00-00 00:00:00' AND (stop_date_time IS NULL OR stop_date_time='0000-00-00 00:00:00' OR stop_date_time='')";
			$count = $db->rp_getTotalRecord("visit", $pendingWhere, 0);

			if ($count == 0) {
				if ($detail['flag'] != '1') {
					$reply = $objVisit->AddVisit($detail, $_FILES);
				} else {
					$reply = array(
						"ack" => 1,
						"developer_msg" => "Visit start validation successful.",
						"ack_msg" => "Visit start validation successful.",
						"id" => "",
					);
				}
			} else {
				$pendingVisit = array();
				$pending_r = $db->rp_getData("visit", "id,customer_id,inquiry_id,start_date_time,app_address,purpose_id,visit_type", $pendingWhere, "id DESC", 0, "1");
				if ($pending_r) {
					$pendingVisit = mysqli_fetch_assoc($pending_r);
					if ($pendingVisit) {
						$pendingVisit['is_pending'] = "1";
					}
				}
				$reply = array(
					"ack" => 0,
					"developer_msg" => "Other visit already started",
					"ack_msg" => "Your Other Visit Is Already Started Please Stop That First",
					"pending_visit_id" => isset($pendingVisit['id']) ? (string) $pendingVisit['id'] : "",
					"pending_visit" => $pendingVisit,
				);
			}
			if (!is_array($reply)) {
				$reply = array(
					"ack" => 0,
					"developer_msg" => "Start Visit returned an invalid server response.",
					"ack_msg" => "Unable to start visit. Please try again.",
				);
			}
			echo json_encode($reply);
		} else if ($service == 'get_visit' || $service == 76) {
			$system = new System();
			$limit = $system->getLimit();
			$visit = array();
			$Where = "";
			$user_id = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$customer_id = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$visit_no = isset($_REQUEST['visit_no']) ? $db->clean($_REQUEST['visit_no']) : "";
			$type_of_company = isset($_REQUEST['company_id']) ? $db->clean($_REQUEST['company_id']) : "";

			/* Ignore Postman/placeholder sample values so list is not filtered to empty */
			$invalidPlaceholders = array("sample", "undefined", "null", "NULL", "0");
			if (in_array($customer_id, $invalidPlaceholders, true)) {
				$customer_id = "";
			}
			if (in_array($visit_no, $invalidPlaceholders, true) || !is_numeric($visit_no)) {
				$visit_no = "";
			}
			if (in_array($type_of_company, $invalidPlaceholders, true)) {
				$type_of_company = "";
			}

			if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL && !in_array($_REQUEST['ToDate'], $invalidPlaceholders, true)) {
				$toDateVal = date("Y-m-d", strtotime($_REQUEST['ToDate']));
			} else {
				$toDateVal = "";
			}

			if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL && !in_array($_REQUEST['FromDate'], $invalidPlaceholders, true)) {
				$fromDateVal = date("Y-m-d", strtotime($_REQUEST['FromDate']));
			} else {
				$fromDateVal = "";
			}

			/* If App sends dates swapped (FromDate > ToDate), auto-correct */
			if ($fromDateVal != "" && $toDateVal != "" && strtotime($fromDateVal) > strtotime($toDateVal)) {
				$tmpDate = $fromDateVal;
				$fromDateVal = $toDateVal;
				$toDateVal = $tmpDate;
			}
			if ($toDateVal != "") {
				$Where .= " DATE(created_date) <= '" . $toDateVal . "' AND";
			}
			if ($fromDateVal != "") {
				$Where .= " DATE(created_date) >= '" . $fromDateVal . "' AND ";
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
				/* Show all non-deleted visits (do not hide inactive rows from App list) */
				$visit_data = $db->rp_getData("visit", "*", $Where . "isDelete=0", "id DESC", 0, $limit);
				if ($visit_data) {
					$ENTRY_FLAG = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
					while ($visit_d = mysqli_fetch_assoc($visit_data)) {
						$visit_d['stop_remark'] = htmlentities(isset($visit_d['stop_remark']) ? $visit_d['stop_remark'] : "");
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
								$get_customer_e = ($get_customer_d) ? mysqli_fetch_assoc($get_customer_d) : false;
								if ($get_customer_e) {
									$visit_d['customer_name'] = $get_customer_e['cname'];
									$visit_d['customer_type'] = $get_customer_e['type_of_executive'];
									$visit_d['client_code'] = $get_customer_e['client_code'];
									$visit_d['company_name'] = $get_customer_e['company_name'];
									$visit_d['customer_flag'] = isset($customer_flag_array[$get_customer_e['customer_flag']]) ? $customer_flag_array[$get_customer_e['customer_flag']] : "";
								} else {
									$visit_d['customer_name'] = "";
								}
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
							$years = 0;
							$months = 0;
							$days = 0;
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
				if (!empty($visit)) {
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
		} else if ($service == 'get_visit_remark_reason' || $service == 231) {
			/*
			 * Visit End — Select Remark / Reason
			 * action_type guide for Android:
			 *   none            = select only, no further UI
			 *   select_reason   = show child reasons list
			 *   open_remark_box = open free-text Remark Box (code F)
			 *   consultant_form = open consultant form (C1 Private / C2 Government)
			 *   open_form       = High Rate Form (E1)
			 * On Visit End (#122) every selected remark creates a Follow-up.
			 * C1/C2/E1 also require form submit + backend store.
			 * C1/C2/E1 form UI is App-side only (no form schema in this API).
			 */
			$remarkReasons = array(
				array(
					"code" => "A",
					"name" => "OLD CUSTOMER VISIT",
					"display_name" => "(A) OLD CUSTOMER VISIT",
					"action_type" => "select_reason",
					"reasons" => array(
						array("code" => "A1", "name" => "Next Week Order", "action_type" => "none"),
						array("code" => "A2", "name" => "Next Month Order", "action_type" => "none"),
					),
				),
				array(
					"code" => "B",
					"name" => "PAYMENT COLLECTION VISIT",
					"display_name" => "(B) PAYMENT COLLECTION VISIT",
					"action_type" => "select_reason",
					"reasons" => array(
						array("code" => "B1", "name" => "Payment Collection With Order", "action_type" => "none"),
					),
				),
				array(
					"code" => "C",
					"name" => "NEED APPROVAL",
					"display_name" => "(C) NEED APPROVAL",
					"action_type" => "select_reason",
					"reasons" => array(
						array(
							"code" => "C1",
							"name" => "Private Consultant",
							"display_name" => "(C1) Private Consultant",
							"action_type" => "consultant_form",
							"consultant_type" => "private",
							"approval_type" => "1",
						),
						array(
							"code" => "C2",
							"name" => "Government Consultant",
							"display_name" => "(C2) Government Consultant",
							"action_type" => "consultant_form",
							"consultant_type" => "government",
							"approval_type" => "2",
						),
					),
				),
				array(
					"code" => "D",
					"name" => "NEW CUSTOMER",
					"display_name" => "(D) NEW CUSTOMER",
					"action_type" => "select_reason",
					"reasons" => array(
						array("code" => "D1", "name" => "Next Week Order", "action_type" => "none"),
						array("code" => "D2", "name" => "Next Month Order", "action_type" => "none"),
					),
				),
				array(
					"code" => "E",
					"name" => "HIGH RATE",
					"display_name" => "(E) HIGH RATE",
					"action_type" => "select_reason",
					"reasons" => array(
						array(
							"code" => "E1",
							"name" => "High Rate Form",
							"action_type" => "open_form",
						),
					),
				),
				array(
					"code" => "F",
					"name" => "SHORT NOTE",
					"display_name" => "(F) SHORT NOTE",
					"action_type" => "open_remark_box",
					"reasons" => array(),
				),
				array(
					"code" => "G",
					"name" => "CALL TO ORDER",
					"display_name" => "(G) CALL TO ORDER",
					"action_type" => "none",
					"reasons" => array(),
				),
			);
			$reply = array(
				"ack" => 1,
				"developer_msg" => "Visit remark / reason list fetched successfully!!",
				"ack_msg" => "Visit remark / reason list fetched successfully!!",
				"result" => $remarkReasons,
			);
			echo json_encode($reply);
		} else if ($service == 'get_visit_approval_type' || $service == 232) {
			/* Types of Approval — show after Need Approval (C) is selected */
			$approvalTypes = array(
				array("id" => "1", "name" => "Private Consultant", "display_name" => "Private Consultant"),
				array("id" => "2", "name" => "Government Consultant", "display_name" => "Government Consultant"),
			);
			$reply = array(
				"ack" => 1,
				"developer_msg" => "Visit approval type list fetched successfully!!",
				"ack_msg" => "Visit approval type list fetched successfully!!",
				"result" => $approvalTypes,
			);
			echo json_encode($reply);
		} else if ($service == 'save_visit_consultant_form' || $service == 233) {
			/*
			 * SAVE AND NEXT — Consultant Detail form (App UI)
			 * Types of Approval → Private (C1) / Government (C2) → this form
			 * Fields: Firm Name, Address, City, State, Pincode, Contact Person, Mo, Mail ID
			 */
			$detail = array();
			$detail['visit_id'] = isset($_REQUEST['visit_id']) ? $db->clean($_REQUEST['visit_id']) : (isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "");
			$detail['user_id'] = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$detail['customer_id'] = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$detail['inquiry_id'] = isset($_REQUEST['inquiry_id']) ? $db->clean($_REQUEST['inquiry_id']) : "";
			$detail['approval_type'] = isset($_REQUEST['approval_type']) ? $db->clean($_REQUEST['approval_type']) : "";
			$detail['reason_code'] = isset($_REQUEST['reason_code']) ? strtoupper($db->clean($_REQUEST['reason_code'])) : "";

			/* Accept both short keys (App form) and consultant_* keys */
			$detail['firm_name'] = isset($_REQUEST['firm_name']) ? $db->clean($_REQUEST['firm_name']) : (isset($_REQUEST['consultant_firm_name']) ? $db->clean($_REQUEST['consultant_firm_name']) : "");
			$detail['address'] = isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : (isset($_REQUEST['consultant_address']) ? $db->clean($_REQUEST['consultant_address']) : "");
			$detail['city'] = isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : (isset($_REQUEST['consultant_city']) ? $db->clean($_REQUEST['consultant_city']) : "");
			$detail['state'] = isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : (isset($_REQUEST['consultant_state']) ? $db->clean($_REQUEST['consultant_state']) : "");
			$detail['pincode'] = isset($_REQUEST['pincode']) ? $db->clean($_REQUEST['pincode']) : (isset($_REQUEST['consultant_pincode']) ? $db->clean($_REQUEST['consultant_pincode']) : "");
			$detail['contact_person'] = isset($_REQUEST['contact_person']) ? $db->clean($_REQUEST['contact_person']) : (isset($_REQUEST['consultant_contact_person']) ? $db->clean($_REQUEST['consultant_contact_person']) : "");
			$detail['mobile'] = isset($_REQUEST['mobile']) ? $db->clean($_REQUEST['mobile']) : "";
			if ($detail['mobile'] == "" && isset($_REQUEST['mo'])) {
				$detail['mobile'] = $db->clean($_REQUEST['mo']);
			}
			if ($detail['mobile'] == "" && isset($_REQUEST['consultant_mobile'])) {
				$detail['mobile'] = $db->clean($_REQUEST['consultant_mobile']);
			}
			$detail['email'] = isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "";
			if ($detail['email'] == "" && isset($_REQUEST['mail_id'])) {
				$detail['email'] = $db->clean($_REQUEST['mail_id']);
			}
			if ($detail['email'] == "" && isset($_REQUEST['consultant_email'])) {
				$detail['email'] = $db->clean($_REQUEST['consultant_email']);
			}

			/* Map Types of Approval selection if only name sent */
			if ($detail['approval_type'] == "" && $detail['reason_code'] == "" && isset($_REQUEST['consultant_type'])) {
				$ctype = strtolower(trim($_REQUEST['consultant_type']));
				if ($ctype == "government" || $ctype == "2" || $ctype == "c2") {
					$detail['approval_type'] = "2";
					$detail['reason_code'] = "C2";
				} else {
					$detail['approval_type'] = "1";
					$detail['reason_code'] = "C1";
				}
			}

			$reply = $objVisit->SaveConsultantDetailForm($detail);
			echo json_encode($reply);
		} else if ($service == 'save_visit_high_rate_form' || $service == 234) {
			/*
			 * SAVE AND NEXT — High Rate Analysis (E1) — ONE-SHOT submit by visit_id
			 * Android does NOT send high_rate_form_id. Backend creates it from visit_id.
			 * Send header + all product rows together in one API call.
			 */
			$detail = array();
			$detail['visit_id'] = isset($_REQUEST['visit_id']) ? $db->clean($_REQUEST['visit_id']) : (isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "");
			$detail['user_id'] = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$detail['customer_id'] = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$detail['inquiry_id'] = isset($_REQUEST['inquiry_id']) ? $db->clean($_REQUEST['inquiry_id']) : "";
			$detail['customer_name'] = isset($_REQUEST['customer_name']) ? $db->clean($_REQUEST['customer_name']) : "";
			if ($detail['customer_name'] == "" && isset($_REQUEST['high_rate_customer_name'])) {
				$detail['customer_name'] = $db->clean($_REQUEST['high_rate_customer_name']);
			}
			$detail['payment_option'] = isset($_REQUEST['payment_option']) ? $db->clean($_REQUEST['payment_option']) : "";
			if ($detail['payment_option'] == "" && isset($_REQUEST['high_rate_payment_option'])) {
				$detail['payment_option'] = $db->clean($_REQUEST['high_rate_payment_option']);
			}
			$detail['payment_remark'] = isset($_REQUEST['payment_remark']) ? $db->clean($_REQUEST['payment_remark']) : "";
			if ($detail['payment_remark'] == "" && isset($_REQUEST['high_rate_payment_remark'])) {
				$detail['payment_remark'] = $db->clean($_REQUEST['high_rate_payment_remark']);
			}

			$itemsRaw = "";
			if (isset($_REQUEST['items'])) {
				$itemsRaw = $_REQUEST['items'];
			} else if (isset($_REQUEST['high_rate_items'])) {
				$itemsRaw = $_REQUEST['high_rate_items'];
			}
			if (is_array($itemsRaw)) {
				$detail['items'] = $itemsRaw;
			} else if ($itemsRaw != "") {
				$decoded = json_decode($itemsRaw, true);
				$detail['items'] = is_array($decoded) ? $decoded : array();
			} else {
				/* Fallback: accept per-slug fields from App, e.g. branch_pipe_heavy = {given_rate, qty...} */
				$detail['items'] = array();
				foreach ($objVisit->getHighRateProductsMaster() as $p) {
					$slug = $p['slug'];
					if (!isset($_REQUEST[$slug])) {
						continue;
					}
					$row = $_REQUEST[$slug];
					if (is_string($row)) {
						$decodedRow = json_decode($row, true);
						$row = is_array($decodedRow) ? $decodedRow : array();
					}
					if (!is_array($row)) {
						continue;
					}
					$row['slug'] = $slug;
					$detail['items'][] = $row;
				}
			}

			$reply = $objVisit->SaveHighRateDetailForm($detail);
			echo json_encode($reply);
		} else if ($service == 'get_visit_high_rate_products' || $service == 235) {
			/* Fixed High Rate product list with slug + Android camelCase keys */
			$products = $objVisit->getHighRateProductsForApi();
			$reply = array(
				"ack" => 1,
				"developer_msg" => "High Rate product list fetched successfully.",
				"ack_msg" => "High Rate product list fetched successfully.",
				"form_title" => "High Rate Analysis",
				"columns" => array(
					array("key" => "productName", "label" => "Product"),
					array("key" => "givenRate", "label" => "Given Rate"),
					array("key" => "qty", "label" => "Qty"),
					array("key" => "customerRate", "label" => "Customer rate"),
					array("key" => "remark", "label" => "Remark"),
				),
				"payment_options" => $objVisit->getHighRatePaymentOptions(),
				"result" => $products,
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
			if ($detail['id'] == "" && isset($_REQUEST['visit_id'])) {
				$detail['id'] = $db->clean($_REQUEST['visit_id']);
			}
			$detail['user_id'] 			= isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			//$detail['customer_id'] 		= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
			$detail['stop_latitude'] 		= isset($_REQUEST['stop_latitude']) ? $db->clean($_REQUEST['stop_latitude']) : "";
			$detail['stop_longitude'] 		= isset($_REQUEST['stop_longitude']) ? $db->clean($_REQUEST['stop_longitude']) : "";
			$detail['stop_remark'] 			= isset($_REQUEST['stop_remark']) ? $db->clean($_REQUEST['stop_remark']) : "";
			$detail['remark_code'] 			= isset($_REQUEST['remark_code']) ? strtoupper($db->clean($_REQUEST['remark_code'])) : "";
			$detail['reason_code'] 			= isset($_REQUEST['reason_code']) ? strtoupper($db->clean($_REQUEST['reason_code'])) : "";
			/*
			 * App form: only stop_remark (display text). remark_code/reason_code optional.
			 * Parse codes from stop_remark e.g. "(E) HIGH RATE ( (E1)High Rate Form)"
			 */
			$parsedCodes = $objVisit->parseVisitStopRemarkCodes($detail['stop_remark'], $detail['remark_code'], $detail['reason_code']);
			$detail['remark_code'] = $parsedCodes['remark_code'];
			$detail['reason_code'] = $parsedCodes['reason_code'];
			$detail['approval_type'] 		= isset($_REQUEST['approval_type']) ? $db->clean($_REQUEST['approval_type']) : "";
			if ($detail['remark_code'] == "C" && $detail['approval_type'] == "") {
				if ($detail['reason_code'] == "C1") {
					$detail['approval_type'] = "1";
				} else if ($detail['reason_code'] == "C2") {
					$detail['approval_type'] = "2";
				}
			}
			$detail['stop_app_address'] 	= isset($_REQUEST['stop_app_address']) ? $db->clean($_REQUEST['stop_app_address']) : "";
			$detail['stop_date_time'] 		= date("Y-m-d H:i");
			$detail['customer_id'] 			= isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$detail['inquiry_id'] 			= isset($_REQUEST['inquiry_id']) ? $db->clean($_REQUEST['inquiry_id']) : "";
			$detail['visit_type'] 			= isset($_REQUEST['visit_type']) ? $db->clean($_REQUEST['visit_type']) : "";
			$detail['visit_stop_flag'] 			= isset($_REQUEST['visit_stop_flag']) ? $db->clean($_REQUEST['visit_stop_flag']) : "";
			$detail['product_name'] 			= isset($_REQUEST['product_name']) ? $db->clean($_REQUEST['product_name']) : "";
			if ($detail['product_name'] == "" && isset($_REQUEST['purchasing_from'])) {
				$detail['product_name'] = $db->clean($_REQUEST['purchasing_from']);
			}
			$detail['firm_name'] 			= isset($_REQUEST['firm_name']) ? $db->clean($_REQUEST['firm_name']) : "";
			$detail['client_name'] 			= isset($_REQUEST['client_name']) ? $db->clean($_REQUEST['client_name']) : "";
			$detail['contact_number'] 			= isset($_REQUEST['contact_number']) ? $db->clean($_REQUEST['contact_number']) : "";
			$detail['name'] 			= isset($_REQUEST['name']) ? $db->clean($_REQUEST['name']) : "";
			$detail['mobile_no'] 			= isset($_REQUEST['mobile_no']) ? $db->clean($_REQUEST['mobile_no']) : "";
			$detail['email_id'] 			= isset($_REQUEST['email_id']) ? $db->clean($_REQUEST['email_id']) : "";
			$detail['designation_id'] 			= isset($_REQUEST['designation_id']) ? $db->clean($_REQUEST['designation_id']) : "";

			/* C1/C2 Private / Government Consultant Detail form fields */
			$detail['consultant_firm_name'] = isset($_REQUEST['consultant_firm_name']) ? $db->clean($_REQUEST['consultant_firm_name']) : "";
			$detail['consultant_address'] = isset($_REQUEST['consultant_address']) ? $db->clean($_REQUEST['consultant_address']) : "";
			$detail['consultant_city'] = isset($_REQUEST['consultant_city']) ? $db->clean($_REQUEST['consultant_city']) : "";
			$detail['consultant_state'] = isset($_REQUEST['consultant_state']) ? $db->clean($_REQUEST['consultant_state']) : "";
			$detail['consultant_pincode'] = isset($_REQUEST['consultant_pincode']) ? $db->clean($_REQUEST['consultant_pincode']) : "";
			$detail['consultant_contact_person'] = isset($_REQUEST['consultant_contact_person']) ? $db->clean($_REQUEST['consultant_contact_person']) : "";
			$detail['consultant_mobile'] = isset($_REQUEST['consultant_mobile']) ? $db->clean($_REQUEST['consultant_mobile']) : "";
			$detail['consultant_email'] = isset($_REQUEST['consultant_email']) ? $db->clean($_REQUEST['consultant_email']) : "";
			if (isset($_REQUEST['consultant_form']) && $_REQUEST['consultant_form'] != "") {
				$consultantFormJson = json_decode($_REQUEST['consultant_form'], true);
				if (is_array($consultantFormJson)) {
					if ($detail['consultant_firm_name'] == "" && isset($consultantFormJson['firm_name'])) {
						$detail['consultant_firm_name'] = $db->clean($consultantFormJson['firm_name']);
					}
					if ($detail['consultant_address'] == "" && isset($consultantFormJson['address'])) {
						$detail['consultant_address'] = $db->clean($consultantFormJson['address']);
					}
					if ($detail['consultant_city'] == "" && isset($consultantFormJson['city'])) {
						$detail['consultant_city'] = $db->clean($consultantFormJson['city']);
					}
					if ($detail['consultant_state'] == "" && isset($consultantFormJson['state'])) {
						$detail['consultant_state'] = $db->clean($consultantFormJson['state']);
					}
					if ($detail['consultant_pincode'] == "" && isset($consultantFormJson['pincode'])) {
						$detail['consultant_pincode'] = $db->clean($consultantFormJson['pincode']);
					}
					if ($detail['consultant_contact_person'] == "" && isset($consultantFormJson['contact_person'])) {
						$detail['consultant_contact_person'] = $db->clean($consultantFormJson['contact_person']);
					}
					if ($detail['consultant_mobile'] == "" && isset($consultantFormJson['mobile'])) {
						$detail['consultant_mobile'] = $db->clean($consultantFormJson['mobile']);
					}
					if ($detail['consultant_email'] == "" && isset($consultantFormJson['email'])) {
						$detail['consultant_email'] = $db->clean($consultantFormJson['email']);
					}
				}
			}

			/* E1 High Rate Analysis form fields */
			$detail['high_rate_customer_name'] = isset($_REQUEST['high_rate_customer_name']) ? $db->clean($_REQUEST['high_rate_customer_name']) : "";
			if ($detail['high_rate_customer_name'] == "" && isset($_REQUEST['customer_name'])) {
				$detail['high_rate_customer_name'] = $db->clean($_REQUEST['customer_name']);
			}
			$detail['high_rate_items'] = isset($_REQUEST['high_rate_items']) ? $_REQUEST['high_rate_items'] : "";
			if ($detail['high_rate_items'] == "" && isset($_REQUEST['items'])) {
				$detail['high_rate_items'] = $_REQUEST['items'];
			}
			$detail['payment_option'] = isset($_REQUEST['payment_option']) ? $db->clean($_REQUEST['payment_option']) : "";
			$detail['payment_remark'] = isset($_REQUEST['payment_remark']) ? $db->clean($_REQUEST['payment_remark']) : "";

			/* If App selected High Rate / Consultant but forgot stop_remark — infer from saved form */
			if ($detail['stop_remark'] == "" && $detail['remark_code'] == "" && !empty($detail['id'])) {
				$hrId = $db->rp_getValue("visit", "high_rate_form_id", "id='" . $detail['id'] . "' AND isDelete=0", 0);
				if ($hrId == "" || $hrId == "0") {
					$hrId = $db->rp_getValue("visit_high_rate_form", "id", "visit_id='" . $detail['id'] . "' AND isDelete=0", 0);
				}
				if ($hrId != "" && $hrId != "0" && $hrId !== false) {
					$detail['remark_code'] = "E";
					$detail['reason_code'] = "E1";
					$detail['stop_remark'] = "(E) HIGH RATE - E1: High Rate Form";
				} else {
					$cfId = $db->rp_getValue("visit", "consultant_form_id", "id='" . $detail['id'] . "' AND isDelete=0", 0);
					if ($cfId == "" || $cfId == "0") {
						$cfId = $db->rp_getValue("visit_consultant_form", "id", "visit_id='" . $detail['id'] . "' AND isDelete=0", 0);
					}
					if ($cfId != "" && $cfId != "0" && $cfId !== false) {
						$cReason = $db->rp_getValue("visit_consultant_form", "reason_code", "id='" . $cfId . "'", 0);
						$detail['remark_code'] = "C";
						$detail['reason_code'] = ($cReason != "" && $cReason !== false) ? strtoupper($cReason) : "C1";
						$detail['stop_remark'] = ($detail['reason_code'] == "C2")
							? "(C) NEED APPROVAL - C2: Government Consultant"
							: "(C) NEED APPROVAL - C1: Private Consultant";
					}
				}
			}

			/*if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!=null)
				{*/

			if (empty($detail['id']) || $detail['id'] == "0") {
				$reply = ['ack' => 0, "ack_msg" => "Visit id is required.", "developer_msg" => "id / visit_id missing"];
				echo json_encode($reply);
			} else if (empty($detail['name']) || empty($detail['mobile_no']) || empty($detail['email_id']) || empty($detail['designation_id'])) {
				$reply = ['ack' => 0, "ack_msg" => "Customer Person Name, Customer Person Mobile No, Customer Person Email ID, Customer Person Designation are mandatory please fill up first. "];
				echo json_encode($reply);
			} else if ($detail['stop_remark'] == "" && $detail['remark_code'] == "") {
				/* Require stop_remark text from App (codes optional — parsed from stop_remark) */
				$reply = ['ack' => 0, "ack_msg" => "Please select Remark / Reason."];
				echo json_encode($reply);
			} else if ($detail['remark_code'] == "F" && $detail['stop_remark'] == "") {
				$reply = ['ack' => 0, "ack_msg" => "Please enter Short Note remark."];
				echo json_encode($reply);
			} else if ($detail['remark_code'] == "C" && !in_array($detail['reason_code'], array("C1", "C2"))) {
				/* Infer C1/C2 from saved consultant form */
				$cfId = $db->rp_getValue("visit", "consultant_form_id", "id='" . $detail['id'] . "' AND isDelete=0", 0);
				if ($cfId == "" || $cfId == "0") {
					$cfId = $db->rp_getValue("visit_consultant_form", "id", "visit_id='" . $detail['id'] . "' AND isDelete=0", 0);
				}
				if ($cfId != "" && $cfId != "0" && $cfId !== false) {
					$cReason = $db->rp_getValue("visit_consultant_form", "reason_code", "id='" . $cfId . "'", 0);
					if ($cReason != "" && $cReason !== false) {
						$detail['reason_code'] = strtoupper($cReason);
					}
				}
				if (!in_array($detail['reason_code'], array("C1", "C2"))) {
					$reply = ['ack' => 0, "ack_msg" => "Please select Private Consultant or Government Consultant."];
					echo json_encode($reply);
				} else {
					$reply = $objVisit->UpdateVisit($detail, $_FILES);
					echo json_encode($reply);
				}
			} else if ($detail['remark_code'] == "C") {
				/* Allow visit end if Consultant Detail already saved via #233 */
				$existingConsultantFormId = $db->rp_getValue("visit", "consultant_form_id", "id='" . $detail['id'] . "' AND isDelete=0", 0);
				$formAlreadySaved = ($existingConsultantFormId != "" && $existingConsultantFormId != "0");
				if (!$formAlreadySaved) {
					$existingConsultantFormId = $db->rp_getValue("visit_consultant_form", "id", "visit_id='" . $detail['id'] . "' AND isDelete=0", 0);
					$formAlreadySaved = ($existingConsultantFormId != "" && $existingConsultantFormId != "0" && $existingConsultantFormId !== false);
				}
				if (!$formAlreadySaved && (
					$detail['consultant_firm_name'] == "" ||
					$detail['consultant_address'] == "" ||
					$detail['consultant_city'] == "" ||
					$detail['consultant_state'] == "" ||
					$detail['consultant_pincode'] == "" ||
					$detail['consultant_contact_person'] == "" ||
					$detail['consultant_mobile'] == ""
				)) {
					$reply = ['ack' => 0, "ack_msg" => "Please fill Consultant Detail form (Firm Name, Address, City, State, Pincode, Contact Person, Mo)."];
					echo json_encode($reply);
				} else {
					$reply = $objVisit->UpdateVisit($detail, $_FILES);
					echo json_encode($reply);
				}
			} else if ($detail['remark_code'] == "E" && $detail['reason_code'] != "" && $detail['reason_code'] != "E1") {
				$reply = ['ack' => 0, "ack_msg" => "Please select High Rate Form."];
				echo json_encode($reply);
			} else if ($detail['remark_code'] == "E") {
				/*
				 * High Rate form already submitted via #234 (App shows "Selected High Rate Form is Submitted")
				 * → allow Stop Visit without sending form fields again.
				 * Check visit.high_rate_form_id OR visit_high_rate_form by visit_id.
				 */
				if ($detail['reason_code'] == "") {
					$detail['reason_code'] = "E1";
				}
				$existingHighRateFormId = $db->rp_getValue("visit", "high_rate_form_id", "id='" . $detail['id'] . "' AND isDelete=0", 0);
				$formAlreadySaved = ($existingHighRateFormId != "" && $existingHighRateFormId != "0" && $existingHighRateFormId !== false);
				if (!$formAlreadySaved) {
					$existingHighRateFormId = $db->rp_getValue("visit_high_rate_form", "id", "visit_id='" . $detail['id'] . "' AND isDelete=0", 0);
					$formAlreadySaved = ($existingHighRateFormId != "" && $existingHighRateFormId != "0" && $existingHighRateFormId !== false);
					if ($formAlreadySaved) {
						$db->rp_update("visit", array("high_rate_form_id" => $existingHighRateFormId), "id='" . $detail['id'] . "'", 0);
					}
				}
				if (!$formAlreadySaved && $detail['high_rate_customer_name'] == "") {
					$reply = ['ack' => 0, "ack_msg" => "Please fill High Rate Analysis form (Customer name).", "developer_msg" => "High Rate form not found for this visit_id. Call #234 first."];
					echo json_encode($reply);
				} else {
					$reply = $objVisit->UpdateVisit($detail, $_FILES);
					echo json_encode($reply);
				}
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
