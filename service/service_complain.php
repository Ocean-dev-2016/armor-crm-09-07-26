<?php
// Connect to Database
include('connect.php');
require_once('../include/notification.class.php');
$status_type = array("0" => "Generate", "1" => "In Progress", "2" => "Complete", "-1" => "Reject", "-2" => "Not Done", "3" => "Cancel");
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
		include('../include/class.complain.php');

		$objComplain = new Complain();

		if ($service == 'Add_complain' || $service == 79) {
			$detail['user_id'] 			   = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$detail['complain_id'] 			   = isset($_REQUEST['complain_id']) ? $db->clean($_REQUEST['complain_id']) : "";
			$detail['customer_id'] 		   = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			//$detail['dealer_id'] 		   = isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
			$detail['latitude'] 		   = isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";
			$detail['longitude'] 		   = isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
			$detail['remark'] 			   = isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
			$detail['app_address'] 		   = isset($_REQUEST['app_address']) ? $db->clean($_REQUEST['app_address']) : "";
			$detail['title'] 			   = isset($_REQUEST['title']) ? $db->clean($_REQUEST['title']) : "";
			$detail['created_date'] 	   = date("Y-m-d");
			$detail['entry_flag'] 		   = isset($_REQUEST['entry_flag']) ? $db->clean($_REQUEST['entry_flag']) : "";
			$detail['complain_type'] 	   = isset($_REQUEST['complain_type']) ? $db->clean($_REQUEST['complain_type']) : "";
			$detail['complain_cat_id'] 	   = isset($_REQUEST['complain_cat_id']) ? $db->clean($_REQUEST['complain_cat_id']) : "";
			$detail['complain_subcat_id']  = isset($_REQUEST['complain_subcat_id']) ? $db->clean($_REQUEST['complain_subcat_id']) : "";
			$detail['complain_created_by'] = isset($_REQUEST['complain_created_by']) ? $db->clean($_REQUEST['complain_created_by']) : "";
			$detail['complain_assign_to']  = isset($_REQUEST['complain_assign_to']) ? $db->clean($_REQUEST['complain_assign_to']) : "";
			$detail['product_id']       = isset($_REQUEST['product_id']) ? $db->clean($_REQUEST['product_id']) : "";
			$detail['customer_requirement']       = isset($_REQUEST['customer_requirement']) ? $db->clean($_REQUEST['customer_requirement']) : "";
			$detail['complain_assign_date'] = isset($_REQUEST['complain_assign_date']) ? $db->clean($_REQUEST['complain_assign_date']) : date("Y-m-d");
			$detail['application_type'] = isset($_REQUEST['application_type']) ? $db->clean($_REQUEST['application_type']) : "0";
			$detail['type_of_company'] 			= isset($_REQUEST['company_id']) ? $db->clean($_REQUEST['company_id']) : "";

			if ($detail['complain_id'] != "") {
				$reply = $objComplain->UpdateComplainApi($detail);
			} else {
				$reply = $objComplain->AddComplain($detail, $_FILES);
			}
			if ($reply['ack'] == 1) {
				echo json_encode($reply);
			} else {
				echo json_encode($reply);
			}
		} else if ($service == 'get_complain' || $service == 80) {
			$system = new System();
			$limit = $system->getLimit();
			$complian = array();
			$user_id = isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : ""; //sales
			$complain_no = isset($_REQUEST['complain_no']) ? $db->clean($_REQUEST['complain_no']) : ""; //sales
			$dealer_id = isset($_REQUEST['dealer_id']) ? $db->clean($_REQUEST['dealer_id']) : ""; //sales
			$customer_id = isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$status = isset($_REQUEST['status']) ? $db->clean($_REQUEST['status']) : "";
			$class_id = isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
			$area_id = isset($_REQUEST['area_id']) ? $db->clean($_REQUEST['area_id']) : "";
			$complain_id = isset($_REQUEST['complain_id']) ? $db->clean($_REQUEST['complain_id']) : "";
			$type_of_company = isset($_REQUEST['company_id']) ? $db->clean($_REQUEST['company_id']) : "";
			/*if($user_id)
			{*/

			if (isset($_REQUEST['ToDate']) && $_REQUEST['ToDate'] != "" && $_REQUEST['ToDate'] != NULL) {
				$where .= " DATE(created_date) <= '" . date("Y-m-d", strtotime($_REQUEST['ToDate'])) . "' AND";
			}

			if (isset($_REQUEST['FromDate']) && $_REQUEST['FromDate'] != "" && $_REQUEST['FromDate'] != NULL) {
				$where .= " DATE(created_date) >= '" . date("Y-m-d", strtotime($_REQUEST['FromDate'])) . "' AND ";
			}

			if ($status != "") {
				$where .= "status='" . $status . "' AND ";
			}

			if ($customer_id != "") {
				$where .= "customer_id='" . $customer_id . "' AND ";
			}
			if ($type_of_company != "") {
				$where .= "type_of_company='" . $type_of_company . "' AND ";
			}

			if ($dealer_id != "") {
				$where .= "dealer_id='" . $dealer_id . "' AND ";
			}

			if ($class_id != "") {
				$where .= "class_id='" . $class_id . "' AND ";
			}

			if ($area_id != "") {
				$where .= "area_id='" . $area_id . "' AND ";
			}
			if ($complain_id != "") {
				$where .= "id='" . $complain_id . "' AND ";
			}
			if ($complain_no != "") {
				$where .= "complain_no LIKE '%" . $complain_no . "%' AND isDelete=0 AND isActive=1";
			} else {
				$where .= "complain_assign_to='" . $user_id . "' AND isDelete=0 AND isActive=1";
			}

			$complian_data = $db->rp_getData("complain", "*", $where, "id DESC", 0, $limit);
			if ($complian_data) {
				//$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
				$ENTRY_FLAG = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
				$customer_flag_array = array("0" => "Customer", "1" => "Prospect Customer");
				while ($complian_d = mysqli_fetch_assoc($complian_data)) {
					$complian_d['complain_type_slug'] =  $db->rp_getValue("source_of_inquiry", "name", "id='" . $complian_d['complain_type'] . "'");

					$complian_d['complain_cat_name'] = $db->rp_getValue("complain_category", "name", "id='" . $complian_d['complain_cat_id'] . "'");

					$complian_d['complain_subcat_name'] = $db->rp_getValue("complain_sub_category", "name", "id='" . $complian_d['complain_subcat_id'] . "'");

					$complian_d['complain_created_by_name'] = $db->rp_getValue("sales_executive", "name", "id='" . $complian_d['complain_created_by'] . "'");

					$complian_d['complain_assign_to_name'] = $db->rp_getValue("sales_executive", "name", "id='" . $complian_d['complain_assign_to'] . "'");

					$weight_name = $db->rp_getValue("weight", "name", "id='" . $complian_d['product_id'] . "' AND isDelete=0", 0);

					$complian_d['prouct_name']	= $db->rp_getValue("product", "name", "id='" . $complian_d['product_id'] . "'", 0) . "-" . $weight_name;

					$get_customer_data_r = $db->rp_getData("executive", "cname,company_name,phone,address,state,city,cname,type_of_executive,client_code,customer_flag", "isDelete=0 AND id = '" . $complian_d['customer_id'] . "' ");

					$get_customer_data_d = mysqli_fetch_assoc($get_customer_data_r);

					$complian_d['customer_name'] = $get_customer_data_d['cname'];
					$complian_d['company_name'] = $get_customer_data_d['company_name'];
					$complian_d['phone'] = $get_customer_data_d['phone'];
					$complian_d['address'] = $get_customer_data_d['address'];
					$complian_d['state'] = $get_customer_data_d['state'];
					$complian_d['city'] = $get_customer_data_d['city'];
					$complian_d['dealer_name'] = $get_customer_data_d['cname'];
					$complian_d['client_code'] = $get_customer_data_d['client_code'];
					$complian_d['customer_flag'] = $customer_flag_array[$get_customer_data_d['customer_flag']];

					$complian_d['status_slug'] = $status_type[$complian_d['status']];
					$complian_d['color_code'] = $db->complain_status_color[$complian_d['status_slug']];
					if ($complian_d['entry_flag'] != "" && $complian_d['entry_flag'] != "null" && $complian_d['entry_flag'] != "NULL"  && $complian_d['entry_flag'] != null && $complian_d['entry_flag'] != NULL) {
						$complian_d['entry_flag'] = $ENTRY_FLAG[$complian_d['entry_flag']];
					}
					if ($complian_d['update_entry_flag'] != "" && $complian_d['update_entry_flag'] != "null" && $complian_d['update_entry_flag'] != "NULL"  && $complian_d['update_entry_flag'] != null && $complian_d['update_entry_flag'] != NULL) {
						$complian_d['update_entry_flag'] = $ENTRY_FLAG[$complian_d['update_entry_flag']];
					}

					$img = explode(",", $complian_d['image_path']);
					$imgpath = array();
					for ($i = 0; $i < sizeof($img); $i++) {
						$imgpath[] = SITEURL . "resource/image/" . $db->rp_getValue("media", "url", "reference_id='" . $complian_d['id'] . "' AND id='" . $img[$i] . "'");
					}
					$complian_d['image_path'] = ($complian_d['image_path'] != "") ? $imgpath : "";
					$complian_d['created_date'] = date('d F Y h:i A', strtotime($complian_d['created_date']));
					$complian_d['complain_assign_date'] = date('d F Y', strtotime($complian_d['complain_date']));

					$customer_type_get = $get_customer_data_d['type_of_executive'];
					$complian_d['customer_type_id'] = $customer_type_get;
					$complian_d['customer_type'] = $db->rp_getValue("customer_type", "name", "id='" . $customer_type_get . "'", 0);

					$complain[] = $complian_d;
				}


				/*Get Complain Status*/
				$complain_status = array();
				$ComplainData = $db->rp_getData('complain', "DISTINCT(status)", "complain_assign_to='" . $user_id . "' AND isDelete=0", "id DESC", 0);
				$complain_status_array = array("0" => "Generate", "1" => "In Progress", "2" => "Complete", "-1" => "Reject", "-2" => "Not Done");
				/*$complain_status_array=array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested");*/

				$complain_status_key = array("0", "1", "2", "-1", "-2");
				$ENTRY_FLAG = array("1" => "Admin Panel", "2" => "customer", "3" => "Web Sales", 4 => "Web Customer", 5 => "Sales App", 6 => "Customer App");
				while ($Complain_d = mysqli_fetch_assoc($ComplainData)) {
					if ($user_id != "") {
						$Complain_d['count'] = $db->rp_getTotalRecord("complain", "complain_assign_to='" . $user_id . "'  AND status='" . $Complain_d['status'] . "' AND isDelete=0");
					} else {
						$Complain_d['count'] = $db->rp_getTotalRecord("complain", "customer_id ='" . $customer_id . "' AND status='" . $Complain_d['status'] . "' AND isDelete=0");
					}

					if (($key_complain = array_search($Complain_d['status'], $complain_status_key)) !== false) {
						unset($complain_status_key[$key_complain]);
					}

					$Complain_d['status_slug'] = $complain_status_array[$Complain_d['status']];
					$Complain_d['status'] = $Complain_d['status'];

					$Complain_d['color_code'] = $db->complain_status_color[$Complain_d['status_slug']];
					if ($complian_d['entry_flag'] != "" && $complian_d['entry_flag'] != "null" && $complian_d['entry_flag'] != "NULL"  && $complian_d['entry_flag'] != null && $complian_d['entry_flag'] != NULL) {
						$complian_d['entry_flag'] = $ENTRY_FLAG[$complian_d['entry_flag']];
					}
					if ($complian_d['update_entry_flag'] != "" && $complian_d['update_entry_flag'] != "null" && $complian_d['update_entry_flag'] != "NULL"  && $complian_d['update_entry_flag'] != null && $complian_d['update_entry_flag'] != NULL) {
						$complian_d['update_entry_flag'] = $ENTRY_FLAG[$complian_d['update_entry_flag']];
					}

					if ($Complain_d['color_code'] == "") {
						$Complain_d['color_code'] = "";
					}

					if ($Complain_d['status_slug'] == "") {
						$Complain_d['status_slug'] = "";
					}
					$complain_status[] = $Complain_d;
				}
				foreach ($complain_status_key as $key => $remainval_complain) {
					$Complain_d['count'] = 0;
					$Complain_d['status'] = $remainval_complain;
					$Complain_d['status_slug'] = $complain_status_array[$remainval_complain];
					$Complain_d['color_code'] = $db->complain_status_color[$complain_status_array[$remainval_complain]];
					$complain_status[] = $Complain_d;
				}
				/*Get Complain Status*/
			}

			if ($complian_data) {
				$reply = array("ack" => 1, "developer_msg" => "Complain Detail Get successfully!!", "ack_msg" => "Complain Detail Get successfully!!", "result" => $complain, "complain_status" => $complain_status);
			} else {
				$reply = array("ack" => 0, "developer_msg" => "No Complain Found!!", "ack_msg" => "No Complain Found!!");
			}
			echo json_encode($reply);
			/*}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Complain Detail Not Get!!","ack_msg"=>"Complain Detail Not Get!!");
				echo json_encode($reply);
			}*/
		} else if ($service == 'update_complain' || $service == 123) {
			$detail['id'] 		     	= isset($_REQUEST['id']) ? $db->clean($_REQUEST['id']) : "";
			$detail['user_id'] 			= isset($_REQUEST['user_id']) ? $db->clean($_REQUEST['user_id']) : "";
			$detail['customer_id'] 		= isset($_REQUEST['customer_id']) ? $db->clean($_REQUEST['customer_id']) : "";
			$detail['dealer_id'] 		= isset($_REQUEST['dealer_id']) ? $db->clean($_REQUEST['dealer_id']) : "";
			$detail['latitude'] 		= isset($_REQUEST['latitude']) ? $db->clean($_REQUEST['latitude']) : "";
			$detail['longitude'] 		= isset($_REQUEST['longitude']) ? $db->clean($_REQUEST['longitude']) : "";
			$detail['remark'] 			= isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";
			$detail['app_address'] 		= isset($_REQUEST['app_address']) ? $db->clean($_REQUEST['app_address']) : "";
			$detail['title'] 			= isset($_REQUEST['title']) ? $db->clean($_REQUEST['title']) : "";
			$detail['created_date'] 	= date("Y-m-d");

			$reply = $objComplain->UpdateComplain($detail, $_FILES);
			if ($reply['ack'] == 1) {
				echo json_encode($reply);
			} else {
				echo json_encode($reply);
			}
		} else if ($service == "get_service_form" || $service == 170) {
			$complain_id = isset($_REQUEST['complain_id']) ? $db->clean($_REQUEST['complain_id']) : "";
			if ($complain_id) {
				$complainR = $db->rp_getData("complain_service", "*", "complain_id='" . $complain_id . "' AND isDelete=0");
				$complainData = array();
				while ($complainD = mysqli_fetch_assoc($complainR)) {
					$complainD['status'] = $db->rp_getValue("complain", "status", "id = '" . $complain_id . "' AND isDelete=0", 0);
					$complainD['complain_date'] = date('d-m-Y', strtotime($complainD['complain_date']));
					$complain_assign_to = $db->rp_getValue("complain", "complain_assign_to", "id = '" . $complain_id . "' AND isDelete=0", 0);
					$complainD['servicemen']  = $db->rp_getValue("sales_executive", "GROUP_CONCAT(name)", "id IN(" . $complain_assign_to . ") AND isDelete=0", 0);
					if ($complainD['problem_report_date'] == "0000-00-00" || $complainD['problem_report_date'] == "1970-01-01") {
						$complainD['problem_report_date'] = "";
					} else {
						$complainD['problem_report_date'] = date('d-m-Y', strtotime($complainD['problem_report_date']));
					}

					if ($complainD['serviceman_sign'] != "") {
						$complainD['serviceman_sign'] =
							$complainD['serviceman_sign'] = SITEURL . "resource/complain_service/" . $complainD['serviceman_sign'];
					} else {
						$complainD['serviceman_sign'] = "";
					}

					if ($complainD['customer_sign'] != "") {
						$complainD['customer_sign'] = SITEURL . "resource/complain_service/" . $complainD['customer_sign'];
					} else {
						$complainD['customer_sign'] = "";
					}

					/*item array*/
					$makearray = array("1" => "CMK", "2" => "Other");
					$warrantyarray = array("1" => "Yes", "2" => "No");
					$serviceitem = array();
					$service_item = $db->rp_getData("complain_service_item", "*", "complain_id='" . $complainD['complain_id'] . "' AND isDelete=0");
					while ($service_item_r = mysqli_fetch_assoc($service_item)) {
						$service_item_r['sell_date'] = date('d-m-Y', strtotime($service_item_r['sell_date']));
						$service_item_r['make_name'] = $makearray[$service_item_r['make']];
						$service_item_r['warranty_name'] = $warrantyarray[$service_item_r['warranty']];
						$serviceitem[] = $service_item_r;
					}
					$complainD['item'] = $serviceitem;
					/*item array*/
					$complainData[] = $complainD;
				}
				if (!empty($complainData)) {
					$ack = array("ack" => 1, "ack_msg" => "Successfully Fetch Data!!", "developer_msg" => "You got it!!", "result" => $complainData);
					$db->printJSON($ack);
				} else {
					$ack = array("ack" => 0, "ack_msg" => "No Data Available!!", "developer_msg" => "No Data Available!!",);
					$db->printJSON($ack);
				}
			} else {
				$ack = array("ack" => 0, "ack_msg" => "Complain Id Required", "developer_msg" => "Complain Id Required");
				$db->printJSON($ack);
			}
		} else if ($service == "add_service_form" || $service == 171) {
			$detail['complain_id'] = isset($_REQUEST['complain_id']) ? $db->clean($_REQUEST['complain_id']) : "";
			$detail['problem_report_date'] = isset($_REQUEST['problem_report_date']) ? $db->clean($_REQUEST['problem_report_date']) : "";
			$detail['designation'] = isset($_REQUEST['designation']) ? $db->clean($_REQUEST['designation']) : "";
			$detail['problem_report_by_client'] = isset($_REQUEST['problem_report_by_client']) ? $db->clean($_REQUEST['problem_report_by_client']) : "";
			$detail['problem_report_observed_on_site'] = isset($_REQUEST['problem_report_observed_on_site']) ? $db->clean($_REQUEST['problem_report_observed_on_site']) : "";
			$detail['corrective_action_taken'] = isset($_REQUEST['corrective_action_taken']) ? $db->clean($_REQUEST['corrective_action_taken']) : "";
			$detail['service_start_time'] = isset($_REQUEST['service_start_time']) ? $db->clean($_REQUEST['service_start_time']) : "";
			$detail['service_end_time'] = isset($_REQUEST['service_end_time']) ? $db->clean($_REQUEST['service_end_time']) : "";
			$detail['remark'] = isset($_REQUEST['remark']) ? $db->clean($_REQUEST['remark']) : "";

			$body1 = file_get_contents('php://input');
			//print_r($body);exit;
			$reply = $objComplain->AddComplainServiceApi($detail, $body1);
			if ($reply['ack'] == 1) {
				echo json_encode($reply);
			} else {
				echo json_encode($reply);
			}
		} else if ($service == 'add_service_form_images' || $service == 172) {
			$detail['complain_id'] = isset($_REQUEST['complain_id']) ? $db->clean($_REQUEST['complain_id']) : "";
			$body = file_get_contents('php://input');
			$reply = $objComplain->AddImagesComplainServiceApi($detail, $body, $_FILES, $_FILES);
			if ($reply['ack'] == 1) {
				echo json_encode($reply);
			} else {
				echo json_encode($reply);
			}
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
