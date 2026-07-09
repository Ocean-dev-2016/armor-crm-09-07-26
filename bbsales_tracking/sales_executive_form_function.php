<?php
$page_id = 556;
$page_slug = 'page_sales_executive';
include('connect.php');
include('../include/class.sales_executive.php');
//include('../include/notification.class.php');

//-----------#Insert Data For Sales Officer -----------------------------------//
if (isset($_REQUEST['submit']) && isset($_REQUEST['mode']) && $_REQUEST['mode'] != "") {
	// var_dump($_REQUEST);exit;
	// echo "<pre>";
	// print_r($_REQUEST);exit;
	$mode = $_REQUEST['mode'];
	if (isset($_REQUEST['username']) && isset($_REQUEST['phone'])) {
		$item = array();
		$id						= $_SESSION[SITE_SESS . '_ADMIN_SESS_ID'];
		$name					= isset($_REQUEST['name']) ? $db->clean($_REQUEST['name']) : "";
		$email					= isset($_REQUEST['email']) ? $db->clean($_REQUEST['email']) : "";
		$username				= isset($_REQUEST['username']) ? $db->clean($_REQUEST['username']) : "";
		$password				= isset($_REQUEST['password']) ? md5($db->clean($_REQUEST['password'])) : "";
		$phone					= isset($_REQUEST['phone']) ? $db->clean($_REQUEST['phone']) : "";
		$address				= isset($_REQUEST['address']) ? $db->clean($_REQUEST['address']) : "";
		$zip					= isset($_REQUEST['zip']) ? $db->clean($_REQUEST['zip']) : "";
		$country				= isset($_REQUEST['country']) ? $db->clean($_REQUEST['country']) : "";
		$state					= isset($_REQUEST['state']) ? $db->clean($_REQUEST['state']) : "";
		$city					= isset($_REQUEST['city']) ? $db->clean($_REQUEST['city']) : "";
		$zone					= isset($_REQUEST['zone']) ? $db->clean($_REQUEST['zone']) : "";
		$imei					= isset($_REQUEST['imei']) ? $db->clean($_REQUEST['imei']) : "";
		$type					= isset($_REQUEST['type_of_inquiry']) ? $db->clean($_REQUEST['type_of_inquiry']) : "";
		$regDate				= date("Y-m-d");
		$sm_id					= isset($_REQUEST['sm_id']) ? $db->clean($_REQUEST['sm_id']) : "";
		$asm_id					= isset($_REQUEST['asm_id']) ? $db->clean($_REQUEST['asm_id']) : "";
		$so_id					= isset($_REQUEST['so_id']) ? $db->clean($_REQUEST['so_id']) : "";
		$am_id					= isset($_REQUEST['am_id']) ? $db->clean($_REQUEST['am_id']) : "";
		$se_id					= isset($_REQUEST['se_id']) ? $db->clean($_REQUEST['se_id']) : "";
		$se_id					= isset($_REQUEST['se_id']) ? $db->clean($_REQUEST['se_id']) : "";
		$executive_in_min		= isset($_REQUEST['executive_in_min']) ? date('H:i', strtotime($db->clean($_REQUEST['executive_in_min']))) : "";
		$executive_in_max		= isset($_REQUEST['executive_in_max']) ? date('H:i', strtotime($db->clean($_REQUEST['executive_in_max']))) : "";
		$executive_out			= isset($_REQUEST['executive_out']) ? date('H:i', strtotime($db->clean($_REQUEST['executive_out']))) : "";
		$main_city					= isset($_REQUEST['main_city']) ? $db->clean($_REQUEST['main_city']) : "";


		$super_stokist_order_view_flag          = isset($_REQUEST['super_stokist_order_view_flag']) ? $db->clean($_REQUEST['super_stokist_order_view_flag']) : 0;
		$super_stokist_order_insert_flag		= isset($_REQUEST['super_stokist_order_insert_flag']) ? $db->clean($_REQUEST['super_stokist_order_insert_flag']) : 0;
		$super_stokist_order_update_flag		= isset($_REQUEST['super_stokist_order_update_flag']) ? $db->clean($_REQUEST['super_stokist_order_update_flag']) : 0;
		$super_stokist_order_delete_flag		= isset($_REQUEST['super_stokist_order_delete_flag']) ? $db->clean($_REQUEST['super_stokist_order_delete_flag']) : 0;


		$outlets_order_view_flag	= isset($_REQUEST['outlets_order_view_flag']) ? $db->clean($_REQUEST['outlets_order_view_flag']) : 0;
		$outlets_order_insert_flag	= isset($_REQUEST['outlets_order_insert_flag']) ? $db->clean($_REQUEST['outlets_order_insert_flag']) : 0;
		$outlets_order_update_flag	= isset($_REQUEST['outlets_order_update_flag']) ? $db->clean($_REQUEST['outlets_order_update_flag']) : 0;
		$outlets_order_delete_flag	= isset($_REQUEST['outlets_order_delete_flag']) ? $db->clean($_REQUEST['outlets_order_delete_flag']) : 0;


		$dealer_order_view_flag	    = isset($_REQUEST['dealer_order_view_flag']) ? $db->clean($_REQUEST['dealer_order_view_flag']) : 0;
		$dealer_order_insert_flag	= isset($_REQUEST['dealer_order_insert_flag']) ? $db->clean($_REQUEST['dealer_order_insert_flag']) : 0;
		$dealer_order_update_flag	= isset($_REQUEST['dealer_order_update_flag']) ? $db->clean($_REQUEST['dealer_order_update_flag']) : 0;
		$dealer_order_delete_flag	= isset($_REQUEST['dealer_order_delete_flag']) ? $db->clean($_REQUEST['dealer_order_delete_flag']) : 0;


		$project_order_view_flag	    = isset($_REQUEST['project_order_view_flag']) ? $db->clean($_REQUEST['project_order_view_flag']) : 0;
		$project_order_insert_flag	= isset($_REQUEST['project_order_insert_flag']) ? $db->clean($_REQUEST['project_order_insert_flag']) : 0;
		$project_order_update_flag	= isset($_REQUEST['project_order_update_flag']) ? $db->clean($_REQUEST['project_order_update_flag']) : 0;
		$project_order_delete_flag	= isset($_REQUEST['project_order_delete_flag']) ? $db->clean($_REQUEST['project_order_delete_flag']) : 0;


		$oem_order_view_flag	    = isset($_REQUEST['oem_order_view_flag']) ? $db->clean($_REQUEST['oem_order_view_flag']) : 0;
		$oem_order_insert_flag	= isset($_REQUEST['oem_order_insert_flag']) ? $db->clean($_REQUEST['oem_order_insert_flag']) : 0;
		$oem_order_update_flag	= isset($_REQUEST['oem_order_update_flag']) ? $db->clean($_REQUEST['oem_order_update_flag']) : 0;
		$oem_order_delete_flag	= isset($_REQUEST['oem_order_delete_flag']) ? $db->clean($_REQUEST['oem_order_delete_flag']) : 0;


		$quotation_view_flag	    = isset($_REQUEST['quotation_view_flag']) ? $db->clean($_REQUEST['quotation_view_flag']) : 0;
		$quotation_insert_flag	= isset($_REQUEST['quotation_insert_flag']) ? $db->clean($_REQUEST['quotation_insert_flag']) : 0;
		$quotation_update_flag	= isset($_REQUEST['quotation_update_flag']) ? $db->clean($_REQUEST['quotation_update_flag']) : 0;
		$quotation_delete_flag	= isset($_REQUEST['quotation_delete_flag']) ? $db->clean($_REQUEST['quotation_delete_flag']) : 0;
		$quotation_approve_flag	= isset($_REQUEST['quotation_approve_flag']) ? $db->clean($_REQUEST['quotation_approve_flag']) : 0;


		$survey_customer_view_flag	= isset($_REQUEST['survey_customer_view_flag']) ? $db->clean($_REQUEST['survey_customer_view_flag']) : 0;
		$survey_customer_insert_flag = isset($_REQUEST['survey_customer_insert_flag']) ? $db->clean($_REQUEST['survey_customer_insert_flag']) : 0;
		$survey_customer_update_flag = isset($_REQUEST['survey_customer_update_flag']) ? $db->clean($_REQUEST['survey_customer_update_flag']) : 0;
		$survey_customer_delete_flag = isset($_REQUEST['survey_customer_delete_flag']) ? $db->clean($_REQUEST['survey_customer_delete_flag']) : 0;

		$customer_leads_view_flag = isset($_REQUEST['customer_leads_view_flag']) ? $db->clean($_REQUEST['customer_leads_view_flag']) : 0;
		$customer_leads_insert_flag = isset($_REQUEST['customer_leads_insert_flag']) ? $db->clean($_REQUEST['customer_leads_insert_flag']) : 0;
		$customer_leads_update_flag = isset($_REQUEST['customer_leads_update_flag']) ? $db->clean($_REQUEST['customer_leads_update_flag']) : 0;
		$customer_leads_delete_flag = isset($_REQUEST['customer_leads_delete_flag']) ? $db->clean($_REQUEST['customer_leads_delete_flag']) : 0;


		$customer_view_flag				= isset($_REQUEST['customer_view_flag']) ? $db->clean($_REQUEST['customer_view_flag']) : 0;
		$customer_insert_flag			= isset($_REQUEST['customer_insert_flag']) ? $db->clean($_REQUEST['customer_insert_flag']) : 0;
		$customer_update_flag			= isset($_REQUEST['customer_update_flag']) ? $db->clean($_REQUEST['customer_update_flag']) : 0;
		$customer_delete_flag			= isset($_REQUEST['customer_delete_flag']) ? $db->clean($_REQUEST['customer_delete_flag']) : 0;

		$followup_view_flag				= isset($_REQUEST['followup_view_flag']) ? $db->clean($_REQUEST['followup_view_flag']) : 0;
		$followup_insert_flag			= isset($_REQUEST['followup_insert_flag']) ? $db->clean($_REQUEST['followup_insert_flag']) : 0;
		$followup_update_flag			= isset($_REQUEST['followup_update_flag']) ? $db->clean($_REQUEST['followup_update_flag']) : 0;
		$followup_delete_flag			= isset($_REQUEST['followup_delete_flag']) ? $db->clean($_REQUEST['followup_delete_flag']) : 0;

		$create_order_view_flag			= isset($_REQUEST['create_order_view_flag']) ? $db->clean($_REQUEST['create_order_view_flag']) : 0;
		$create_order_insert_flag		= isset($_REQUEST['create_order_insert_flag']) ? $db->clean($_REQUEST['create_order_insert_flag']) : 0;
		$create_order_update_flag		= isset($_REQUEST['create_order_update_flag']) ? $db->clean($_REQUEST['create_order_update_flag']) : 0;
		$create_order_delete_flag		= isset($_REQUEST['create_order_delete_flag']) ? $db->clean($_REQUEST['create_order_delete_flag']) : 0;
		$create_order_approve_flag		= isset($_REQUEST['create_order_approve_flag']) ? $db->clean($_REQUEST['create_order_approve_flag']) : 0;
		$chain_wise_view_order_history_flag		= isset($_REQUEST['chain_wise_view_order_history_flag']) ? $db->clean($_REQUEST['chain_wise_view_order_history_flag']) : 0;

		$order_history_view_flag		= isset($_REQUEST['order_history_view_flag']) ? $db->clean($_REQUEST['order_history_view_flag']) : 0;
		$order_history_insert_flag		= isset($_REQUEST['order_history_insert_flag']) ? $db->clean($_REQUEST['order_history_insert_flag']) : 0;
		$order_history_update_flag		= isset($_REQUEST['order_history_update_flag']) ? $db->clean($_REQUEST['order_history_update_flag']) : 0;
		$order_history_delete_flag		= isset($_REQUEST['order_history_delete_flag']) ? $db->clean($_REQUEST['order_history_delete_flag']) : 0;


		$complain_view_flag			    = isset($_REQUEST['complain_view_flag']) ? $db->clean($_REQUEST['complain_view_flag']) : 0;
		$complain_insert_flag		    = isset($_REQUEST['complain_insert_flag']) ? $db->clean($_REQUEST['complain_insert_flag']) : 0;
		$complain_update_flag		    = isset($_REQUEST['complain_update_flag']) ? $db->clean($_REQUEST['complain_update_flag']) : 0;
		$complain_delete_flag		    = isset($_REQUEST['complain_delete_flag']) ? $db->clean($_REQUEST['complain_delete_flag']) : 0;

		$request_view_flag			    = isset($_REQUEST['request_view_flag']) ? $db->clean($_REQUEST['request_view_flag']) : 0;
		$request_insert_flag		    = isset($_REQUEST['request_insert_flag']) ? $db->clean($_REQUEST['request_insert_flag']) : 0;
		$request_update_flag		    = isset($_REQUEST['request_update_flag']) ? $db->clean($_REQUEST['request_update_flag']) : 0;
		$request_delete_flag		    = isset($_REQUEST['request_delete_flag']) ? $db->clean($_REQUEST['request_delete_flag']) : 0;

		$customer_meeting_view_flag		= isset($_REQUEST['customer_meeting_view_flag']) ? $db->clean($_REQUEST['customer_meeting_view_flag']) : 0;
		$customer_meeting_insert_flag	= isset($_REQUEST['customer_meeting_insert_flag']) ? $db->clean($_REQUEST['customer_meeting_insert_flag']) : 0;
		$customer_meeting_update_flag   = isset($_REQUEST['customer_meeting_update_flag']) ? $db->clean($_REQUEST['customer_meeting_update_flag']) : 0;
		$customer_meeting_delete_flag	= isset($_REQUEST['customer_meeting_delete_flag']) ? $db->clean($_REQUEST['customer_meeting_delete_flag']) : 0;


		$near_by_me_view_flag			= isset($_REQUEST['near_by_me_view_flag']) ? $db->clean($_REQUEST['near_by_me_view_flag']) : 0;

		$change_root_view_flag			= isset($_REQUEST['change_root_view_flag']) ? $db->clean($_REQUEST['change_root_view_flag']) : 0;
		$change_root_insert_flag		= isset($_REQUEST['change_root_insert_flag']) ? $db->clean($_REQUEST['change_root_insert_flag']) : 0;
		$change_root_update_flag		= isset($_REQUEST['change_root_update_flag']) ? $db->clean($_REQUEST['change_root_update_flag']) : 0;
		$change_root_delete_flag		= isset($_REQUEST['change_root_delete_flag']) ? $db->clean($_REQUEST['change_root_delete_flag']) : 0;

		$expense_view_flag				= isset($_REQUEST['expense_view_flag']) ? $db->clean($_REQUEST['expense_view_flag']) : 0;
		$expense_insert_flag		    = isset($_REQUEST['expense_insert_flag']) ? $db->clean($_REQUEST['expense_insert_flag']) : 0;
		$expense_update_flag			= isset($_REQUEST['expense_update_flag']) ? $db->clean($_REQUEST['expense_update_flag']) : 0;
		$expense_delete_flag			= isset($_REQUEST['expense_delete_flag']) ? $db->clean($_REQUEST['expense_delete_flag']) : 0;

		$leave_view_flag				= isset($_REQUEST['leave_view_flag']) ? $db->clean($_REQUEST['leave_view_flag']) : 0;
		$leave_insert_flag		    	= isset($_REQUEST['leave_insert_flag']) ? $db->clean($_REQUEST['leave_insert_flag']) : 0;
		$leave_update_flag				= isset($_REQUEST['leave_update_flag']) ? $db->clean($_REQUEST['leave_update_flag']) : 0;
		$leave_delete_flag				= isset($_REQUEST['leave_delete_flag']) ? $db->clean($_REQUEST['leave_delete_flag']) : 0;

		$area_view_flag					= isset($_REQUEST['area_view_flag']) ? $db->clean($_REQUEST['area_view_flag']) : 0;
		$area_insert_flag		    	= isset($_REQUEST['area_insert_flag']) ? $db->clean($_REQUEST['area_insert_flag']) : 0;
		$area_update_flag				= isset($_REQUEST['area_update_flag']) ? $db->clean($_REQUEST['area_update_flag']) : 0;
		$area_delete_flag				= isset($_REQUEST['area_delete_flag']) ? $db->clean($_REQUEST['area_delete_flag']) : 0;

		$visit_view_flag				= isset($_REQUEST['visit_view_flag']) ? $db->clean($_REQUEST['visit_view_flag']) : 0;
		$visit_insert_flag		    	= isset($_REQUEST['visit_insert_flag']) ? $db->clean($_REQUEST['visit_insert_flag']) : 0;
		$visit_update_flag				= isset($_REQUEST['visit_update_flag']) ? $db->clean($_REQUEST['visit_update_flag']) : 0;
		$visit_delete_flag				= isset($_REQUEST['visit_delete_flag']) ? $db->clean($_REQUEST['visit_delete_flag']) : 0;

		$price_list_view_flag		    = isset($_REQUEST['price_list_view_flag']) ? $db->clean($_REQUEST['price_list_view_flag']) : 0;
		$bank_detail_view_flag		    = isset($_REQUEST['bank_detail_view_flag']) ? $db->clean($_REQUEST['bank_detail_view_flag']) : 0;
		$scheme_view_flag		        = isset($_REQUEST['scheme_view_flag']) ? $db->clean($_REQUEST['scheme_view_flag']) : 0;
		$discount_dealer_view_flag	    = isset($_REQUEST['discount_dealer_view_flag']) ? $db->clean($_REQUEST['discount_dealer_view_flag']) : 0;
		$discount_distributor_view_flag = isset($_REQUEST['discount_distributor_view_flag']) ? $db->clean($_REQUEST['discount_distributor_view_flag']) : 0;
		$gst_view_flag = isset($_REQUEST['gst_view_flag']) ? $db->clean($_REQUEST['gst_view_flag']) : 0;
		$visit_card_view_flag = isset($_REQUEST['visit_card_view_flag']) ? $db->clean($_REQUEST['visit_card_view_flag']) : 0;
		$traveling_view_flag = isset($_REQUEST['traveling_view_flag']) ? $db->clean($_REQUEST['traveling_view_flag']) : 0;

		$tracking_flag = isset($_REQUEST['tracking_flag']) ? $db->clean($_REQUEST['tracking_flag']) : 0;
		$attendance_insert_flag = isset($_REQUEST['attendance_insert_flag']) ? $db->clean($_REQUEST['attendance_insert_flag']) : 0;

		$prospact_view_flag = isset($_REQUEST['prospact_view_flag']) ? $db->clean($_REQUEST['prospact_view_flag']) : 0;
		$prospact_insert_flag = isset($_REQUEST['prospact_insert_flag']) ? $db->clean($_REQUEST['prospact_insert_flag']) : 0;
		$prospact_update_flag = isset($_REQUEST['prospact_update_flag']) ? $db->clean($_REQUEST['prospact_update_flag']) : 0;
		$prospact_delete_flag = isset($_REQUEST['prospact_delete_flag']) ? $db->clean($_REQUEST['prospact_delete_flag']) : 0;

		$marchent_customer_view_flag = isset($_REQUEST['marchent_customer_view_flag']) ? $db->clean($_REQUEST['marchent_customer_view_flag']) : 0;
		$marchent_customer_insert_flag = isset($_REQUEST['marchent_customer_insert_flag']) ? $db->clean($_REQUEST['marchent_customer_insert_flag']) : 0;
		$marchent_customer_update_flag = isset($_REQUEST['marchent_customer_update_flag']) ? $db->clean($_REQUEST['marchent_customer_update_flag']) : 0;
		$marchent_customer_delete_flag = isset($_REQUEST['marchent_customer_delete_flag']) ? $db->clean($_REQUEST['marchent_customer_delete_flag']) : 0;

		$promotional_customer_view_flag = isset($_REQUEST['promotional_customer_view_flag']) ? $db->clean($_REQUEST['promotional_customer_view_flag']) : 0;
		$promotional_customer_insert_flag = isset($_REQUEST['promotional_customer_insert_flag']) ? $db->clean($_REQUEST['promotional_customer_insert_flag']) : 0;
		$promotional_customer_update_flag = isset($_REQUEST['promotional_customer_update_flag']) ? $db->clean($_REQUEST['promotional_customer_update_flag']) : 0;
		$promotional_customer_delete_flag = isset($_REQUEST['promotional_customer_delete_flag']) ? $db->clean($_REQUEST['promotional_customer_delete_flag']) : 0;

		$corporate_customer_view_flag = isset($_REQUEST['corporate_customer_view_flag']) ? $db->clean($_REQUEST['corporate_customer_view_flag']) : 0;
		$corporate_customer_insert_flag = isset($_REQUEST['corporate_customer_insert_flag']) ? $db->clean($_REQUEST['corporate_customer_insert_flag']) : 0;
		$corporate_customer_update_flag = isset($_REQUEST['corporate_customer_update_flag']) ? $db->clean($_REQUEST['corporate_customer_update_flag']) : 0;
		$corporate_customer_delete_flag = isset($_REQUEST['corporate_customer_delete_flag']) ? $db->clean($_REQUEST['corporate_customer_delete_flag']) : 0;

		$my_route_view_flag = isset($_REQUEST['my_route_view_flag']) ? $db->clean($_REQUEST['my_route_view_flag']) : 0;
		$my_route_insert_flag = isset($_REQUEST['my_route_insert_flag']) ? $db->clean($_REQUEST['my_route_insert_flag']) : 0;
		$customer_stock_add_flag = isset($_REQUEST['customer_stock_add_flag']) ? $db->clean($_REQUEST['customer_stock_add_flag']) : 0;
		$deepfreezscheme_flag = isset($_REQUEST['deepfreezscheme_flag']) ? $db->clean($_REQUEST['deepfreezscheme_flag']) : 0;

		$tradercontractor_view_flag = isset($_REQUEST['tradercontractor_view_flag']) ? $db->clean($_REQUEST['tradercontractor_view_flag']) : 0;
		$tradercontractor_insert_flag = isset($_REQUEST['tradercontractor_insert_flag']) ? $db->clean($_REQUEST['tradercontractor_insert_flag']) : 0;
		$tradercontractor_update_flag = isset($_REQUEST['tradercontractor_update_flag']) ? $db->clean($_REQUEST['tradercontractor_update_flag']) : 0;
		$tradercontractor_delete_flag = isset($_REQUEST['tradercontractor_delete_flag']) ? $db->clean($_REQUEST['tradercontractor_delete_flag']) : 0;

		$mep_consultant_view_flag = isset($_REQUEST['mep_consultant_view_flag']) ? $db->clean($_REQUEST['mep_consultant_view_flag']) : 0;
		$mep_consultant_insert_flag = isset($_REQUEST['mep_consultant_insert_flag']) ? $db->clean($_REQUEST['mep_consultant_insert_flag']) : 0;
		$mep_consultant_update_flag = isset($_REQUEST['mep_consultant_update_flag']) ? $db->clean($_REQUEST['mep_consultant_update_flag']) : 0;
		$mep_consultant_delete_flag = isset($_REQUEST['mep_consultant_delete_flag']) ? $db->clean($_REQUEST['mep_consultant_delete_flag']) : 0;

		$builder_view_flag = isset($_REQUEST['builder_view_flag']) ? $db->clean($_REQUEST['builder_view_flag']) : 0;
		$builder_insert_flag = isset($_REQUEST['builder_insert_flag']) ? $db->clean($_REQUEST['builder_insert_flag']) : 0;
		$builder_update_flag = isset($_REQUEST['builder_update_flag']) ? $db->clean($_REQUEST['builder_update_flag']) : 0;
		$builder_delete_flag = isset($_REQUEST['builder_delete_flag']) ? $db->clean($_REQUEST['builder_delete_flag']) : 0;

		$brand_approval_visit_view_flag = isset($_REQUEST['brand_approval_visit_view_flag']) ? $db->clean($_REQUEST['brand_approval_visit_view_flag']) : 0;
		$brand_approval_visit_insert_flag = isset($_REQUEST['brand_approval_visit_insert_flag']) ? $db->clean($_REQUEST['brand_approval_visit_insert_flag']) : 0;
		$brand_approval_visit_update_flag = isset($_REQUEST['brand_approval_visit_update_flag']) ? $db->clean($_REQUEST['brand_approval_visit_update_flag']) : 0;
		$brand_approval_visit_delete_flag = isset($_REQUEST['brand_approval_visit_delete_flag']) ? $db->clean($_REQUEST['brand_approval_visit_delete_flag']) : 0;

		$monthlyorder_planner_view = isset($_REQUEST['monthlyorder_planner_view']) ? $db->clean($_REQUEST['monthlyorder_planner_view']) : 0;
		$monthlyorder_planner_add = isset($_REQUEST['monthlyorder_planner_add']) ? $db->clean($_REQUEST['monthlyorder_planner_add']) : 0;
		$monthlyorder_planner_edit = isset($_REQUEST['monthlyorder_planner_edit']) ? $db->clean($_REQUEST['monthlyorder_planner_edit']) : 0;
		$monthlyorder_planner_delete = isset($_REQUEST['monthlyorder_planner_delete']) ? $db->clean($_REQUEST['monthlyorder_planner_delete']) : 0;

		$consultant_process_view = isset($_REQUEST['consultant_process_view']) ? $db->clean($_REQUEST['consultant_process_view']) : 0;
		$consultant_process_add = isset($_REQUEST['consultant_process_add']) ? $db->clean($_REQUEST['consultant_process_add']) : 0;
		$consultant_process_edit = isset($_REQUEST['consultant_process_edit']) ? $db->clean($_REQUEST['consultant_process_edit']) : 0;
		$consultant_process_delete = isset($_REQUEST['consultant_process_delete']) ? $db->clean($_REQUEST['consultant_process_delete']) : 0;

		$class_id			            = isset($_REQUEST['class_id']) ? $db->clean($_REQUEST['class_id']) : "";
		$area_id		                = $_REQUEST['area_id'];
		$refreshToken		            = isset($_REQUEST['refreshToken']) ? $db->clean($_REQUEST['refreshToken']) : "";
		$insentive_percentage		    = isset($_REQUEST['insentive_percentage']) ? $db->clean($_REQUEST['insentive_percentage']) : "";

		$image_path				        = isset($_FILES);
		$file_path				        = isset($_FILES);

		$top_category_id		    = isset($_REQUEST['top_category_id']) ? $db->clean($_REQUEST['top_category_id']) : "";

		if (sizeof($_REQUEST['top_category_id']) != 0) {
			$top_category_id = implode(',', $_REQUEST['top_category_id']);
		} else {
			$top_category_id = "";
			//$category_id="";
		}

		$travel_by_bike_flag = isset($_REQUEST['travel_by_bike_flag']) ? $db->clean($_REQUEST['travel_by_bike_flag']) : 0;

		$travel_by_bus_flag = isset($_REQUEST['travel_by_bus_flag']) ? $db->clean($_REQUEST['travel_by_bus_flag']) : 0;

		$travel_by_car_flag = isset($_REQUEST['travel_by_car_flag']) ? $db->clean($_REQUEST['travel_by_car_flag']) : 0;

		$type_of_company = isset($_REQUEST['type_of_company']) ? $db->clean(implode(',', $_REQUEST['type_of_company'])) : "";

		$weekday		    = isset($_REQUEST['weekday']) ? $db->clean($_REQUEST['weekday']) : "";
		// isset($_REQUEST['type_of_company'])?$db->clean($_REQUEST['type_of_company']):"";
		//print_r($type_of_company);exit();


		// if(sizeof($_REQUEST['category_id']) != 0)
		// {

		// 	$top_cat_id=array();
		// 	for($i=0; $i <sizeof($_REQUEST['category_id']); $i++) 
		// 	{ 
		// 		$tcid=$db->rp_getValue("category_master","tcid","
		// 			isDelete=0 AND id=".$_REQUEST['category_id'][$i],0);
		// 		if(!in_array($tcid, $top_cat_id))
		// 		{
		// 			$top_cat_id[]=$tcid;
		// 		}
		// 	}

		// 	$top_cat_id=implode(',', $top_cat_id);
		// 	$category_id=implode(',',$_REQUEST['category_id']);

		// 	// echo $top_cat_id;exit;
		// 	// echo "test";exit;
		// }
		// else
		// {
		// 	$top_cat_id="";
		// 	$category_id="";


		// }


		// print_r($_REQUEST['category_id']);exit();

		$detail = array();
		$detail['id'] = $id;
		$detail['name'] = $name;
		$detail['email'] = $email;
		$detail['username'] = $username;
		$detail['password'] = $password;
		$detail['phone'] = $phone;
		$detail['address'] = $address;
		$detail['zip'] = $zip;
		$detail['country'] = $country;
		$detail['state'] = $state;
		$detail['city'] = $city;
		$detail['zone'] = $zone;
		$detail['type'] = $type;
		$detail['sm_id'] = $sm_id;
		$detail['asm_id'] = $asm_id;
		$detail['so_id'] = $so_id;
		$detail['am_id'] = $am_id;
		$detail['se_id'] = $se_id;
		$detail['class_id'] = $class_id;
		$detail['area_id'] = $area_id;
		$detail['area_id'] = $area_id;
		$detail['area_id'] = $area_id;
		$detail['executive_in_min'] = $executive_in_min;
		$detail['executive_in_max'] = $executive_in_max;
		$detail['executive_out'] = $executive_out;
		$detail['main_city'] = $main_city;


		$detail['super_stokist_order_view_flag'] = $super_stokist_order_view_flag;
		$detail['super_stokist_order_insert_flag'] = $super_stokist_order_insert_flag;
		$detail['super_stokist_order_update_flag'] = $super_stokist_order_update_flag;
		$detail['super_stokist_order_delete_flag'] = $super_stokist_order_delete_flag;

		$detail['outlets_order_view_flag'] = $outlets_order_view_flag;
		$detail['outlets_order_insert_flag'] = $outlets_order_insert_flag;
		$detail['outlets_order_update_flag'] = $outlets_order_update_flag;
		$detail['outlets_order_delete_flag'] = $outlets_order_delete_flag;


		$detail['dealer_order_view_flag'] = $dealer_order_view_flag;
		$detail['dealer_order_insert_flag'] = $dealer_order_insert_flag;
		$detail['dealer_order_update_flag'] = $dealer_order_update_flag;
		$detail['dealer_order_delete_flag'] = $dealer_order_delete_flag;


		$detail['project_order_view_flag'] = $project_order_view_flag;
		$detail['project_order_insert_flag'] = $project_order_insert_flag;
		$detail['project_order_update_flag'] = $project_order_update_flag;
		$detail['project_order_delete_flag'] = $project_order_delete_flag;


		$detail['oem_order_view_flag'] = $oem_order_view_flag;
		$detail['oem_order_insert_flag'] = $oem_order_insert_flag;
		$detail['oem_order_update_flag'] = $oem_order_update_flag;
		$detail['oem_order_delete_flag'] = $oem_order_delete_flag;

		$detail['quotation_view_flag'] = $quotation_view_flag;
		$detail['quotation_insert_flag'] = $quotation_insert_flag;
		$detail['quotation_update_flag'] = $quotation_update_flag;
		$detail['quotation_delete_flag'] = $quotation_delete_flag;
		$detail['quotation_approve_flag'] = $quotation_approve_flag;

		$detail['survey_customer_view_flag'] = $survey_customer_view_flag;
		$detail['survey_customer_insert_flag'] = $survey_customer_insert_flag;
		$detail['survey_customer_update_flag'] = $survey_customer_update_flag;
		$detail['survey_customer_delete_flag'] = $survey_customer_delete_flag;

		$detail['customer_leads_view_flag'] = $customer_leads_view_flag;
		$detail['customer_leads_insert_flag'] = $customer_leads_insert_flag;
		$detail['customer_leads_update_flag'] = $customer_leads_update_flag;
		$detail['customer_leads_delete_flag'] = $customer_leads_delete_flag;

		$detail['customer_view_flag'] = $customer_view_flag;
		$detail['customer_insert_flag'] = $customer_insert_flag;
		$detail['customer_update_flag'] = $customer_update_flag;
		$detail['customer_delete_flag'] = $customer_delete_flag;

		$detail['followup_view_flag'] = $followup_view_flag;
		$detail['followup_insert_flag'] = $followup_insert_flag;
		$detail['followup_update_flag'] = $followup_update_flag;
		$detail['followup_delete_flag'] = $followup_delete_flag;

		$detail['create_order_view_flag'] = $create_order_view_flag;
		$detail['create_order_insert_flag'] = $create_order_insert_flag;
		$detail['create_order_update_flag'] = $create_order_update_flag;
		$detail['create_order_delete_flag'] = $create_order_delete_flag;
		$detail['create_order_approve_flag'] = $create_order_approve_flag;
		$detail['chain_wise_view_order_history_flag'] = $chain_wise_view_order_history_flag;

		$detail['order_history_view_flag'] = $order_history_view_flag;
		$detail['order_history_insert_flag'] = $order_history_insert_flag;
		$detail['order_history_update_flag'] = $order_history_update_flag;
		$detail['order_history_delete_flag'] = $order_history_delete_flag;

		$detail['complain_view_flag'] = $complain_view_flag;
		$detail['complain_insert_flag'] = $complain_insert_flag;
		$detail['complain_update_flag'] = $complain_update_flag;
		$detail['complain_delete_flag'] = $complain_delete_flag;

		$detail['request_view_flag'] = $request_view_flag;
		$detail['request_insert_flag'] = $request_insert_flag;
		$detail['request_update_flag'] = $request_update_flag;
		$detail['request_delete_flag'] = $request_delete_flag;

		$detail['customer_meeting_view_flag'] = $customer_meeting_view_flag;
		$detail['customer_meeting_insert_flag'] = $customer_meeting_insert_flag;
		$detail['customer_meeting_update_flag'] = $customer_meeting_update_flag;
		$detail['customer_meeting_delete_flag'] = $customer_meeting_delete_flag;

		$detail['near_by_me_view_flag'] = $near_by_me_view_flag;

		$detail['change_root_view_flag'] = $change_root_view_flag;
		$detail['change_root_insert_flag'] = $change_root_insert_flag;
		$detail['change_root_update_flag'] = $change_root_update_flag;
		$detail['change_root_delete_flag'] = $change_root_delete_flag;

		$detail['expense_view_flag'] = $expense_view_flag;
		$detail['expense_insert_flag'] = $expense_insert_flag;
		$detail['expense_update_flag'] = $expense_update_flag;
		$detail['expense_delete_flag'] = $expense_delete_flag;

		$detail['leave_view_flag'] = $leave_view_flag;
		$detail['leave_insert_flag'] = $leave_insert_flag;
		$detail['leave_update_flag'] = $leave_update_flag;
		$detail['leave_delete_flag'] = $leave_delete_flag;

		$detail['area_view_flag'] = $area_view_flag;
		$detail['area_insert_flag'] = $area_insert_flag;
		$detail['area_update_flag'] = $area_update_flag;
		$detail['area_delete_flag'] = $area_delete_flag;

		$detail['visit_view_flag'] = $visit_view_flag;
		$detail['visit_insert_flag'] = $visit_insert_flag;
		$detail['visit_update_flag'] = $visit_update_flag;
		$detail['visit_delete_flag'] = $visit_delete_flag;

		$detail['price_list_view_flag'] = $price_list_view_flag;
		$detail['bank_detail_view_flag'] = $bank_detail_view_flag;
		$detail['scheme_view_flag'] = $scheme_view_flag;
		$detail['discount_dealer_view_flag'] = $discount_dealer_view_flag;
		$detail['discount_distributor_view_flag'] = $discount_distributor_view_flag;
		$detail['gst_view_flag'] = $gst_view_flag;
		$detail['visit_card_view_flag'] = $visit_card_view_flag;
		$detail['traveling_view_flag'] = $traveling_view_flag;
		$detail['tracking_flag'] = $tracking_flag;

		$detail['attendance_insert_flag'] = $attendance_insert_flag;

		$detail['prospact_view_flag'] = $prospact_view_flag;
		$detail['prospact_insert_flag'] = $prospact_insert_flag;
		$detail['prospact_update_flag'] = $prospact_update_flag;
		$detail['prospact_delete_flag'] = $prospact_delete_flag;

		$detail['marchent_customer_view_flag'] = $marchent_customer_view_flag;
		$detail['marchent_customer_insert_flag'] = $marchent_customer_insert_flag;
		$detail['marchent_customer_update_flag'] = $marchent_customer_update_flag;
		$detail['marchent_customer_delete_flag'] = $marchent_customer_delete_flag;

		$detail['promotional_customer_view_flag'] = $promotional_customer_view_flag;
		$detail['promotional_customer_insert_flag'] = $promotional_customer_insert_flag;
		$detail['promotional_customer_update_flag'] = $promotional_customer_update_flag;
		$detail['promotional_customer_delete_flag'] = $promotional_customer_delete_flag;

		$detail['corporate_customer_view_flag'] = $corporate_customer_view_flag;
		$detail['corporate_customer_insert_flag'] = $corporate_customer_insert_flag;
		$detail['corporate_customer_update_flag'] = $corporate_customer_update_flag;
		$detail['corporate_customer_delete_flag'] = $corporate_customer_delete_flag;

		$detail['my_route_view_flag'] = $my_route_view_flag;
		$detail['my_route_insert_flag'] = $my_route_insert_flag;
		$detail['customer_stock_add_flag'] = $customer_stock_add_flag;
		$detail['deepfreezscheme_flag'] = $deepfreezscheme_flag;

		$detail['tradercontractor_view_flag'] = $tradercontractor_view_flag;
		$detail['tradercontractor_insert_flag'] = $tradercontractor_insert_flag;
		$detail['tradercontractor_update_flag'] = $tradercontractor_update_flag;
		$detail['tradercontractor_delete_flag'] = $tradercontractor_delete_flag;

		$detail['mep_consultant_view_flag'] = $mep_consultant_view_flag;
		$detail['mep_consultant_insert_flag'] = $mep_consultant_insert_flag;
		$detail['mep_consultant_update_flag'] = $mep_consultant_update_flag;
		$detail['mep_consultant_delete_flag'] = $mep_consultant_delete_flag;

		$detail['builder_view_flag'] = $builder_view_flag;
		$detail['builder_insert_flag'] = $builder_insert_flag;
		$detail['builder_update_flag'] = $builder_update_flag;
		$detail['builder_delete_flag'] = $builder_delete_flag;

		$detail['brand_approval_visit_view_flag'] = $brand_approval_visit_view_flag;
		$detail['brand_approval_visit_insert_flag'] = $brand_approval_visit_insert_flag;
		$detail['brand_approval_visit_update_flag'] = $brand_approval_visit_update_flag;
		$detail['brand_approval_visit_delete_flag'] = $brand_approval_visit_delete_flag;

		$detail['travel_by_bike_flag'] = $travel_by_bike_flag;
		$detail['travel_by_bus_flag'] = $travel_by_bus_flag;
		$detail['travel_by_car_flag'] = $travel_by_car_flag;


		$detail['old_image_path'] = isset($_REQUEST['old_image_path']) ? $db->clean($_REQUEST['old_image_path']) : "";
		$detail['old_file_path'] = isset($_REQUEST['old_file_path']) ? $db->clean($_REQUEST['old_file_path']) : "";

		$detail['monthlyorder_planner_view'] = $monthlyorder_planner_view;
		$detail['monthlyorder_planner_add'] = $monthlyorder_planner_add;
		$detail['monthlyorder_planner_edit'] = $monthlyorder_planner_edit;
		$detail['monthlyorder_planner_delete'] = $monthlyorder_planner_delete;

		$detail['consultant_process_view'] = $consultant_process_view;
		$detail['consultant_process_add'] = $consultant_process_add;
		$detail['consultant_process_edit'] = $consultant_process_edit;
		$detail['consultant_process_delete'] = $consultant_process_delete;

	

		$detail['top_category_id'] = $_REQUEST['top_category_id'];
		$detail['type_of_company'] = $type_of_company;
		$detail['weekday'] = $weekday;

		// echo "<pre>";
		// print_r($detail);die;
		// echo $brand_approval_visit_view_flag;die;

		$size[] = sizeof($area_id);
		$value_check = sizeof($area_id);
		if (in_array($value_check, $size)) {
			$isValidArray = true;
		} else {
			$isValidArray = false;
		}

		if ($isValidArray && !empty($area_id)) {
			for ($i = 0; $i < sizeof($area_id); $i++) {
				$item[] = array("area_id" => $area_id[$i]);
			}
		}
		/*print_r($item);exit;*/
		$inquiry = new SalesExecutive();
		if ($mode == "add") {
			//insert Data Using Function InsertSalesExecutive in class file.


			/*upload image For Gst Detaill*/
			if (isset($_FILES["image_path"])) {
				$allowedExts = array("jpg", "JPG", "pdf", "PDF");
				$temp = explode(".", $_FILES["image_path"]["name"]);
				$extension = end($temp);

				$fileName 	= $db->clean($_FILES["image_path"]["name"]);
				if ($fileName != "") {
					$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
					$adate 		= date('Y-m-d H:i:m');

					$extension	= end(explode(".", $fileName));
					if (!in_array($extension, $allowedExts)) {
						$file_error = true;
					}

					$image_path	= 'gst_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= GST_VISITING_DETAIL_A . $image_path;
					$_FILES['image_path']['tmp_name'];
					move_uploaded_file($_FILES['image_path']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$image_path = "";
				}
			} else {
				$new_image = false;
				$image_path = "";
			}
			/*upload image For Gst Detaill*/

			/*upload image for visiting card*/
			if (isset($_FILES["file_path"])) {
				$allowedExts = array("jpg", "JPG", "pdf", "PDF");
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

					$file_path	= 'visiting_card_' . substr(sha1(time()), 0, 6) . "." . $extension;
					$filePath 	= GST_VISITING_DETAIL_A . $file_path;
					$_FILES['file_path']['tmp_name'];
					move_uploaded_file($_FILES['file_path']['tmp_name'], $filePath);

					$new_image = true;
				} else {
					$file_path = "";
				}
			} else {
				$new_image = false;
				$image_path = "";
			}
			/*upload image for visiting card*/

			// echo $quotation_approve_flag;exit;

			$ack = $inquiry->InsertSalesExecutive($id, $name, $email, $username, $password, $phone, $address, $zip, $country, $state, $city, $zone, $imei, $type, $regDate, $sm_id, $asm_id, $so_id, $se_id, $class_id, $item, $refreshToken, $executive_in_min, $executive_in_max, $executive_out, $super_stokist_order_view_flag, $super_stokist_order_insert_flag, $super_stokist_order_update_flag, $super_stokist_order_delete_flag, $outlets_order_view_flag, $outlets_order_insert_flag, $outlets_order_update_flag, $outlets_order_delete_flag, $dealer_order_view_flag, $dealer_order_insert_flag, $dealer_order_update_flag, $dealer_order_delete_flag, $project_order_view_flag, $project_order_insert_flag, $project_order_update_flag, $project_order_delete_flag, $oem_order_view_flag, $oem_order_insert_flag, $oem_order_update_flag, $oem_order_delete_flag, $quotation_view_flag, $quotation_insert_flag, $quotation_update_flag, $quotation_delete_flag, $survey_customer_view_flag, $survey_customer_insert_flag, $survey_customer_update_flag, $survey_customer_delete_flag, $customer_leads_view_flag, $customer_leads_insert_flag, $customer_leads_update_flag, $customer_leads_delete_flag, $customer_view_flag, $customer_insert_flag, $customer_update_flag, $customer_delete_flag, $followup_view_flag, $followup_insert_flag, $followup_update_flag, $followup_delete_flag, $create_order_view_flag, $create_order_insert_flag, $create_order_update_flag, $create_order_delete_flag, $order_history_view_flag, $order_history_insert_flag, $order_history_update_flag, $order_history_delete_flag, $complain_view_flag, $complain_insert_flag, $complain_update_flag, $complain_delete_flag, $request_view_flag, $request_insert_flag, $request_update_flag, $request_delete_flag, $customer_meeting_view_flag, $customer_meeting_insert_flag, $customer_meeting_update_flag, $customer_meeting_delete_flag, $near_by_me_view_flag, $change_root_view_flag, $change_root_insert_flag, $change_root_update_flag, $change_root_delete_flag, $expense_view_flag, $expense_insert_flag, $expense_update_flag, $expense_delete_flag, $leave_view_flag, $leave_insert_flag, $leave_update_flag, $leave_delete_flag, $area_view_flag, $area_insert_flag, $area_update_flag, $area_delete_flag, $visit_view_flag, $visit_insert_flag, $visit_update_flag, $visit_delete_flag, $price_list_view_flag, $bank_detail_view_flag, $scheme_view_flag, $discount_dealer_view_flag, $discount_distributor_view_flag, $gst_view_flag, $visit_card_view_flag, $traveling_view_flag, $tracking_flag, $attendance_insert_flag, $prospact_view_flag, $prospact_insert_flag, $prospact_update_flag, $prospact_delete_flag, $marchent_customer_view_flag, $marchent_customer_insert_flag, $marchent_customer_update_flag, $marchent_customer_delete_flag, $promotional_customer_view_flag, $promotional_customer_insert_flag, $promotional_customer_update_flag, $promotional_customer_delete_flag, $corporate_customer_view_flag, $corporate_customer_insert_flag, $corporate_customer_update_flag, $corporate_customer_delete_flag, $my_route_view_flag, $my_route_insert_flag, $insentive_percentage, $image_path, $file_path, $top_cat_id, $customer_stock_add_flag, $deepfreezscheme_flag, $tradercontractor_view_flag, $tradercontractor_insert_flag, $tradercontractor_update_flag, $tradercontractor_delete_flag, $mep_consultant_view_flag, $mep_consultant_insert_flag, $mep_consultant_update_flag, $mep_consultant_delete_flag, $builder_view_flag, $builder_insert_flag, $builder_update_flag, $builder_delete_flag, $brand_approval_visit_view_flag, $brand_approval_visit_insert_flag, $brand_approval_visit_update_flag, $brand_approval_visit_delete_flag, $main_city, $top_category_id, $travel_by_bike_flag, $travel_by_bus_flag, $travel_by_car_flag, $type_of_company, $am_id, $weekday, $create_order_approve_flag, $quotation_approve_flag, $chain_wise_view_order_history_flag, $monthlyorder_planner_view, $monthlyorder_planner_add, $monthlyorder_planner_edit, $monthlyorder_planner_delete,$consultant_process_view,$consultant_process_add,$consultant_process_edit,$consultant_process_delete);

			if ($ack['ack'] == 1) {
				$result = $ack['result'][0];
				$db->addSuccessMessage("Person successfully saved!!");
				$db->addSuccessMessage("Person successfully saved !!");
				$db->rp_location("sales_executive_manage.php");
			} else {
				$db->addErrorMessage("Form submission failed Try again!!");
				$db->addErrorMessage($ack['ack_msg']);
				$_SESSION['detail'] = $detail;
				$db->rp_location("sales_executive_crud.php?mode=add&type=" . $type . "");
			}
		} else {
			if (isset($_REQUEST['id'])) {

				/*upload image For Gst Detaill*/
				if (isset($_FILES["image_path"]) && $_FILES["image_path"]['size'] != 0) {
					$allowedExts = array("jpg", "JPG", "pdf", "PDF");
					$temp = explode(".", $_FILES["image_path"]["name"]);
					$extension = end($temp);

					$fileName 	= $db->clean($_FILES["image_path"]["name"]);
					if ($fileName != "") {
						$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');

						$extension	= end(explode(".", $fileName));
						if (!in_array($extension, $allowedExts)) {
							$file_error = true;
						}

						$image_path	= 'gst_' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	= GST_VISITING_DETAIL_A . $image_path;
						$_FILES['image_path']['tmp_name'];
						move_uploaded_file($_FILES['image_path']['tmp_name'], $filePath);

						$new_image = true;
					} else {
						$image_path = $detail['old_image_path'];
						$image_path = "";
					}
				} else {
					$image_path = $detail['old_image_path'];
					unset($detail['old_image_path']);
				}
				/*upload image For Gst Detaill*/

				/*upload image for visiting card*/
				if (isset($_FILES["file_path"]) && $_FILES["file_path"]['size'] != 0) {
					$allowedExts = array("jpg", "JPG", "pdf", "PDF");
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

						$file_path	= 'visiting_card_' . substr(sha1(time()), 0, 6) . "." . $extension;
						$filePath 	= GST_VISITING_DETAIL_A . $file_path;
						$_FILES['file_path']['tmp_name'];
						move_uploaded_file($_FILES['file_path']['tmp_name'], $filePath);

						$new_image = true;
					} else {
						$file_path = $detail['old_file_path'];
						$file_path = "";
					}
				} else {
					$file_path = $detail['old_file_path'];
					unset($detail['old_file_path']);
				}
				/*upload image for visiting card*/


				//UPdate Sales Officer information Using UpdateSalesExecutive in class file.
				// echo $top_category_id;exit;
				// echo $tradercontractor_view_flag."<br>sdjkfd";
				// echo $tradercontractor_insert_flag."<br>sdjkfd";
				// echo $tradercontractor_update_flag."<br>sdjkfd";
				// echo $tradercontractor_delete_flag."<br>sdjkfd";
				// echo $mep_consultant_view_flag."<br>sdjkfd";
				// echo $mep_consultant_insert_flag."<br>sdjkfd";
				// echo $mep_consultant_update_flag."<br>sdjkfd";
				// echo $mep_consultant_delete_flag."<br>sdjkfd";
				// echo $builder_view_flag."<br>sdjkfd";
				// echo $builder_insert_flag."<br>sdjkfd";
				// echo $builder_update_flag."<br>sdjkfd";
				// echo $builder_delete_flag."<br>sdjkfd";
				// echo $brand_approval_visit_view_flag."<br>sdjkfd";
				// echo $brand_approval_visit_insert_flag."<br>sdjkfd";
				// echo $brand_approval_visit_update_flag."<br>sdjkfd";
				// echo $brand_approval_visit_delete_flag."<br>sdjkfd";exit;
				$executive_id = $_REQUEST['id'];
				$ack = $inquiry->UpdateSalesExecutive($executive_id, $id, $name, $email, $username, $phone, $address, $zip, $country, $state, $city, $zone, $imei, $type, $regDate, $sm_id, $asm_id, $so_id, $se_id, $class_id, $item, $refreshToken, $executive_in_min, $executive_in_max, $executive_out, $super_stokist_order_view_flag, $super_stokist_order_insert_flag, $super_stokist_order_update_flag, $super_stokist_order_delete_flag, $outlets_order_view_flag, $outlets_order_insert_flag, $outlets_order_update_flag, $outlets_order_delete_flag, $dealer_order_view_flag, $dealer_order_insert_flag, $dealer_order_update_flag, $dealer_order_delete_flag, $project_order_view_flag, $project_order_insert_flag, $project_order_update_flag, $project_order_delete_flag, $oem_order_view_flag, $oem_order_insert_flag, $oem_order_update_flag, $oem_order_delete_flag, $quotation_view_flag, $quotation_insert_flag, $quotation_update_flag, $quotation_delete_flag, $survey_customer_view_flag, $survey_customer_insert_flag, $survey_customer_update_flag, $survey_customer_delete_flag, $customer_leads_view_flag, $customer_leads_insert_flag, $customer_leads_update_flag, $customer_leads_delete_flag, $customer_view_flag, $customer_insert_flag, $customer_update_flag, $customer_delete_flag, $followup_view_flag, $followup_insert_flag, $followup_update_flag, $followup_delete_flag, $create_order_view_flag, $create_order_insert_flag, $create_order_update_flag, $create_order_delete_flag, $order_history_view_flag, $order_history_insert_flag, $order_history_update_flag, $order_history_delete_flag, $complain_view_flag, $complain_insert_flag, $complain_update_flag, $complain_delete_flag, $request_view_flag, $request_insert_flag, $request_update_flag, $request_delete_flag, $customer_meeting_view_flag, $customer_meeting_insert_flag, $customer_meeting_update_flag, $customer_meeting_delete_flag, $near_by_me_view_flag, $change_root_view_flag, $change_root_insert_flag, $change_root_update_flag, $change_root_delete_flag, $expense_view_flag, $expense_insert_flag, $expense_update_flag, $expense_delete_flag, $leave_view_flag, $leave_insert_flag, $leave_update_flag, $leave_delete_flag, $area_view_flag, $area_insert_flag, $area_update_flag, $area_delete_flag, $visit_view_flag, $visit_insert_flag, $visit_update_flag, $visit_delete_flag, $price_list_view_flag, $bank_detail_view_flag, $scheme_view_flag, $discount_dealer_view_flag, $discount_distributor_view_flag, $gst_view_flag, $visit_card_view_flag, $traveling_view_flag, $tracking_flag, $attendance_insert_flag, $prospact_view_flag, $prospact_insert_flag, $prospact_update_flag, $prospact_delete_flag, $marchent_customer_view_flag, $marchent_customer_insert_flag, $marchent_customer_update_flag, $marchent_customer_delete_flag, $promotional_customer_view_flag, $promotional_customer_insert_flag, $promotional_customer_update_flag, $promotional_customer_delete_flag, $corporate_customer_view_flag, $corporate_customer_insert_flag, $corporate_customer_update_flag, $corporate_customer_delete_flag, $my_route_view_flag, $my_route_insert_flag, $insentive_percentage, $image_path, $file_path, $top_cat_id, $customer_stock_add_flag, $deepfreezscheme_flag, $main_city, $top_category_id, $travel_by_bike_flag, $travel_by_bus_flag, $travel_by_car_flag, $type_of_company, $tradercontractor_view_flag, $tradercontractor_insert_flag, $tradercontractor_update_flag, $tradercontractor_delete_flag, $mep_consultant_view_flag, $mep_consultant_insert_flag, $mep_consultant_update_flag, $mep_consultant_delete_flag, $builder_view_flag, $builder_insert_flag, $builder_update_flag, $builder_delete_flag, $brand_approval_visit_view_flag, $brand_approval_visit_insert_flag, $brand_approval_visit_update_flag, $brand_approval_visit_delete_flag, $am_id, $weekday, $create_order_approve_flag, $quotation_approve_flag, $chain_wise_view_order_history_flag, $monthlyorder_planner_view, $monthlyorder_planner_add, $monthlyorder_planner_edit, $monthlyorder_planner_delete,$consultant_process_view,$consultant_process_add,$consultant_process_edit,$consultant_process_delete);


				if ($ack['ack'] == 1) {
					$result['inquiry_status_slug'] = intval($result['inquiry_status_slug']) + 1;
					$result = $ack['result'][0];
					$db->addSuccessMessage("Person successfully updated!!");
					$db->rp_location("sales_executive_manage.php");
				} else {
					$db->addErrorMessage("Form submission failed Try again!!");
					$db->addErrorMessage($ack['ack_msg']);
					$db->rp_location("sales_executive_crud.php?mode=edit&id=" . $_REQUEST['id'] . "&type=" . $_REQUEST['type_of_inquiry'] . "");
				}
			} else {
				$db->addErrorMessage("Form submission failed Try again!!");
				$db->addErrorMessage("No Person found to update!!");
				$db->rp_location("sales_executive_manage.php");
			}
		}
	} else {
		$db->addErrorMessage("Form submission failed Try again!!");
		$db->addErrorMessage("Person name, contact number and type of Executive are compalsary!!");
		$db->rp_location("manage_inquiry.php");
	}
} else {

	$db->addErrorMessage("Form submission failed Try later!!");
	$db->rp_location("manage_inquiry.php");
}
