<?php
require_once("main.class.php");
require_once("function.class.php");
class SalesExecutive extends Functions
{
	public $db;
	public $pin_type=array("create_order"=>"Order Creation","update_order"=>"Update Order","login"=>"Login","logout"=>"Logout","attandance"=>"Attendance","add_customer"=>"Customer Creation","add_expense"=>"Expense Added","add_inquiry"=>"Add Inquiry","edit_inquiry"=>"Edit Inquiry","delete_inquiry"=>"Deleted Inquiry","sync"=>"Data Sync","profile"=>"Profile","password"=>"Password Changed","tracking"=>"Tracking","sync_hour"=>"Hourly Sync data","sync hour"=>"Hourly Sync data","visit"=>"Visit");

	public $pin_icon=array("create_order"=>"a.png","update_order"=>"b.png","login"=>"c.png","logout"=>"d.png","attandance"=>"e.png","add_customer"=>"f.png","add_expense"=>"g.png","add_inquiry"=>"h.png","edit_inquiry"=>"i.png","delete_inquiry"=>"j.png","sync"=>"k.png","profile"=>"l.png","password"=>"m.png","tracking"=>"n.png","sync_hour"=>"n.png","sync hour"=>"n.png","visit"=>"j.png");
	
	public $ctable="sales_executive";
	public $ctableMap="sales_executive_map_area";
	public $ctableTracking="salesexecutive_tracking";
	public $ctableNoOrderInquiry="no_order_inquiry";
	//public $sales_type_title=array("sales_manager"=>"Sales Manager","area_sales_manager"=>"Area Sales Manager","sales_officer"=>"Area Sales Manager","sales_executive"=>"Sales Officer");
	public $sales_type_title=array("sales_manager"=>"SM","area_sales_manager"=>"ASM","sales_officer"=>"SO","sales_executive"=>"SE");
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
    } 
//------------#Insert Sales Officer Information------------------------------------------------------------------------------// 	
	 public function InsertSalesExecutive($id,$name,$email,$username,$password,$phone,$address,$zip,$country,$state,$city,$zone,$imei,$type,$regDate,$sm_id,$asm_id,$so_id,$se_id,$class_id,$item,$refreshToken,$executive_in_min,$executive_in_max,$executive_out,$super_stokist_order_view_flag="",$super_stokist_order_insert_flag="",$super_stokist_order_update_flag="",$super_stokist_order_delete_flag="",$outlets_order_view_flag="",$outlets_order_insert_flag="",$outlets_order_update_flag="",$outlets_order_delete_flag="",$dealer_order_view_flag="",$dealer_order_insert_flag="",$dealer_order_update_flag="",$dealer_order_delete_flag="",$project_order_view_flag="",$project_order_insert_flag="",$project_order_update_flag="",$project_order_delete_flag="",$oem_order_view_flag="",$oem_order_insert_flag="",$oem_order_update_flag="",$oem_order_delete_flag="",$quotation_view_flag="",$quotation_insert_flag="",$quotation_update_flag="",$quotation_delete_flag="",$survey_customer_view_flag="",$survey_customer_insert_flag="",$survey_customer_update_flag="",$survey_customer_delete_flag="",$customer_leads_view_flag="",$customer_leads_insert_flag="",$customer_leads_update_flag="",$customer_leads_delete_flag="",$customer_view_flag="",$customer_insert_flag="",$customer_update_flag="",$customer_delete_flag="",$followup_view_flag="",$followup_insert_flag="",$followup_update_flag="",$followup_delete_flag="",$create_order_view_flag="",$create_order_insert_flag="",$create_order_update_flag="",$create_order_delete_flag="",$order_history_view_flag="",$order_history_insert_flag="",$order_history_update_flag="",$order_history_delete_flag="",$complain_view_flag="",$complain_insert_flag="",$complain_update_flag="",$complain_delete_flag="",$request_view_flag="",$request_insert_flag="",$request_update_flag="",$request_delete_flag="",$customer_meeting_view_flag="",$customer_meeting_insert_flag="",$customer_meeting_update_flag="",$customer_meeting_delete_flag="",$near_by_me_view_flag="",$change_root_view_flag="",$change_root_insert_flag="",$change_root_update_flag="",$change_root_delete_flag="",$expense_view_flag="",$expense_insert_flag="",$expense_update_flag="",$expense_delete_flag="",$leave_view_flag="",$leave_insert_flag="",$leave_update_flag="",$leave_delete_flag="",$area_view_flag="",$area_insert_flag="",$area_update_flag="",$area_delete_flag="",$visit_view_flag="",$visit_insert_flag="",$visit_update_flag="",$visit_delete_flag="",$price_list_view_flag="",$bank_detail_view_flag="",$scheme_view_flag="",$discount_dealer_view_flag="",$discount_distributor_view_flag="",$gst_view_flag="",$visit_card_view_flag="",$traveling_view_flag="",$tracking_flag="",$attendance_insert_flag="",$prospact_view_flag="",$prospact_insert_flag="",$prospact_update_flag="",$prospact_delete_flag="",$marchent_customer_view_flag="",$marchent_customer_insert_flag="",$marchent_customer_update_flag="",$marchent_customer_delete_flag="",$promotional_customer_view_flag="",$promotional_customer_insert_flag="",$promotional_customer_update_flag="",$promotional_customer_delete_flag="",$corporate_customer_view_flag="",$corporate_customer_insert_flag="",$corporate_customer_update_flag="",$corporate_customer_delete_flag="",$my_route_view_flag="",$my_route_insert_flag="",$insentive_percentage,$image_path,$file_path,$top_cat_id="",$customer_stock_add_flag="",$deepfreezscheme_flag="",$tradercontractor_view_flag="",$tradercontractor_insert_flag="",$tradercontractor_update_flag="",$tradercontractor_delete_flag="",$mep_consultant_view_flag="",$mep_consultant_insert_flag="",$mep_consultant_update_flag="",$mep_consultant_delete_flag="",$builder_view_flag="",$builder_insert_flag="",$builder_update_flag="",$builder_delete_flag="",$brand_approval_visit_view_flag="",$brand_approval_visit_insert_flag="",$brand_approval_visit_update_flag="",$brand_approval_visit_delete_flag="",$main_city="",$top_category_id,$travel_by_bike_flag,$travel_by_bus_flag,$travel_by_car_flag,$type_of_company,$am_id,$weekday,$create_order_approve_flag,$quotation_approve_flag,$chain_wise_view_order_history_flag)
	 {
		//print_r($item);exit;


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
	 	// echo $brand_approval_visit_delete_flag."<br>sdjkfd";exit
		$dup_where = "username = '".$username."' OR  phone = '".$phone."' AND isDelete=0 AND isActive=1";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate executive","ack_msg"=>"Phone number/ Username already assigned to another user please try another!!");
			//$this->db->rp_location("sales_executive_crud.php?mode=add");
			return $reply;
		}
		
		else
		{
			
			 $type_of_executive=$type;
				 if($type=='sales_executive')
				 {
					 $type_slug='SalesExecutive';
				 }
				 else if($type=='sales_manager')
				 {
					 $type_slug='SalesManager';
				 }
				 else if($type=='sales_officer')
				 {
					 $type_slug='SalesOfficer';
				 }
				 else if($type=='area_sales_manager')
				 {
					 $type_slug='AreaSalesManager';
				 }
				  else if($type=='area_manager')
				 {
					 $type_slug='AreaManager';
				 }
				 else if($type=='service_executive')
				 {
					 $type_slug='ServiceExecutive';
				 }

			 $adate	= date('Y-m-d H:i:s');
				$rows 	= array(
						"class_id",
						"name",
						"email",
						"username",
						"password",
						"phone",
						"address",
						"zip",
						"country",
						"state",
						"city",
						"zone",
						"imei",
						"type",
						"regDate",
						"sm_id",
						"asm_id",
						"so_id",
						"am_id",
						"se_id",
						"refreshToken",
						"isActive",
						"executive_in_min",
						"executive_in_max",
						"executive_out",
						"super_stokist_order_view_flag",
						"super_stokist_order_insert_flag",
						"super_stokist_order_update_flag",
						"super_stokist_order_delete_flag",
						"outlets_order_view_flag",
						"outlets_order_insert_flag",
						"outlets_order_update_flag",
						"outlets_order_delete_flag",
						"dealer_order_view_flag",
						"dealer_order_insert_flag",
						"dealer_order_update_flag",
						"dealer_order_delete_flag",
						"project_order_view_flag",
						"project_order_insert_flag",
						"project_order_update_flag",
						"project_order_delete_flag",
						"oem_order_view_flag",
						"oem_order_insert_flag",
						"oem_order_update_flag",
						"oem_order_delete_flag",

						"quotation_view_flag",
						"quotation_insert_flag",
						"quotation_update_flag",
						"quotation_delete_flag",

						"survey_customer_view_flag",
						"survey_customer_insert_flag",
						"survey_customer_update_flag",
						"survey_customer_delete_flag",
						"customer_leads_view_flag",
						"customer_leads_insert_flag",
						"customer_leads_update_flag",
						"customer_leads_delete_flag",
						"customer_view_flag",
						"customer_insert_flag",
						"customer_update_flag",
						"customer_delete_flag",
						"followup_view_flag",
						"followup_insert_flag",
						"followup_update_flag",
						"followup_delete_flag",
						"create_order_view_flag",
						"create_order_insert_flag",
						"create_order_update_flag",
						"create_order_delete_flag",
						"order_history_view_flag",
						"order_history_insert_flag",
						"order_history_update_flag",
						"order_history_delete_flag",
						"complain_view_flag",
						"complain_insert_flag",
						"complain_update_flag",
						"complain_delete_flag",
						"request_view_flag",
						"request_insert_flag",
						"request_update_flag",
						"request_delete_flag",
						"customer_meeting_view_flag",
						"customer_meeting_insert_flag",
						"customer_meeting_update_flag",
						"customer_meeting_delete_flag",
						"near_by_me_view_flag",
						"change_root_view_flag",
						"change_root_insert_flag",
						"change_root_update_flag",
						"change_root_delete_flag",
						"expense_view_flag",
						"expense_insert_flag",
						"expense_update_flag",
						"expense_delete_flag",
						"leave_view_flag",
						"leave_insert_flag",
						"leave_update_flag",
						"leave_delete_flag",
						"area_view_flag",
						"area_insert_flag",
						"area_update_flag",
						"area_delete_flag",
						"visit_view_flag",
						"visit_insert_flag",
						"visit_update_flag",
						"visit_delete_flag",
						"price_list_view_flag",
						"bank_detail_view_flag",
						"scheme_view_flag",
						"discount_dealer_view_flag",
						"discount_distributor_view_flag",
						"gst_view_flag",
						"visit_card_view_flag",
						"traveling_view_flag",	
						"tracking_flag",	
						"attendance_insert_flag",

							"prospact_view_flag",
						"prospact_insert_flag",
						"prospact_update_flag",
						"prospact_delete_flag",
						"marchent_customer_view_flag",
						"marchent_customer_insert_flag",
						"marchent_customer_update_flag",
						"marchent_customer_delete_flag",

						"promotional_customer_view_flag",
						"promotional_customer_insert_flag",
						"promotional_customer_update_flag",
						"promotional_customer_delete_flag",

						"corporate_customer_view_flag",
						"corporate_customer_insert_flag",
						"corporate_customer_update_flag",
						"corporate_customer_delete_flag",

						"my_route_view_flag",
						"my_route_insert_flag",
						"customer_stock_add_flag",
						"deepfreezscheme_flag",
						"insentive_percentage",
						"gst_file_path",
						"visiting_card_file_path",
						// "category_id",
						"main_city",
						"top_category_id",
						"travel_by_bike_flag",
						"travel_by_bus_flag",
						"travel_by_car_flag",
						"type_of_company",
						"tradercontractor_view_flag",
						"tradercontractor_insert_flag",
						"tradercontractor_update_flag",
						"tradercontractor_delete_flag",
						"mep_consultant_view_flag",
						"mep_consultant_insert_flag",
						"mep_consultant_update_flag",
						"mep_consultant_delete_flag",
						"builder_view_flag",
						"builder_insert_flag",
						"builder_update_flag",
						"builder_delete_flag",
						"brand_approval_visit_view_flag",
						"brand_approval_visit_insert_flag",
						"brand_approval_visit_update_flag",
						"brand_approval_visit_delete_flag",
						"weekday",
						"create_order_approve_flag",
						"quotation_approve_flag",
						"chain_wise_view_order_history_flag",

					);
			$values = array(
						$class_id,
						$name,
						$email,
						$username,
						$password,
						$phone,
						$address,
						$zip,
						$country,
						$state,
						$city,
						$zone,
						$imei,
						$type,
						$adate,
						$sm_id,
						$asm_id,
						$so_id,
						$am_id,
						$se_id,
						$refreshToken,
						"1",
						$executive_in_min,
						$executive_in_max,
						$executive_out,
						$super_stokist_order_view_flag,
						$super_stokist_order_insert_flag,
						$super_stokist_order_update_flag,
						$super_stokist_order_delete_flag,
						$outlets_order_view_flag,
						$outlets_order_insert_flag,
						$outlets_order_update_flag,
						$outlets_order_delete_flag,
						$dealer_order_view_flag,
						$dealer_order_insert_flag,
						$dealer_order_update_flag,
						$dealer_order_delete_flag,
						$project_order_view_flag,
						$project_order_insert_flag,
						$project_order_update_flag,
						$project_order_delete_flag,
						$oem_order_view_flag,
						$oem_order_insert_flag,
						$oem_order_update_flag,
						$oem_order_delete_flag,

						$quotation_view_flag,
						$quotation_insert_flag,
						$quotation_update_flag,
						$quotation_delete_flag,

						$survey_customer_view_flag,
						$survey_customer_insert_flag,
						$survey_customer_update_flag,
						$survey_customer_delete_flag,
						$customer_leads_view_flag,
						$customer_leads_insert_flag,
						$customer_leads_update_flag,
						$customer_leads_delete_flag,
						$customer_view_flag,
						$customer_insert_flag,
						$customer_update_flag,
						$customer_delete_flag,
						$followup_view_flag,
						$followup_insert_flag,
						$followup_update_flag,
						$followup_delete_flag,
						$create_order_view_flag,
						$create_order_insert_flag,
						$create_order_update_flag,
						$create_order_delete_flag,
						$order_history_view_flag,
						$order_history_insert_flag,
						$order_history_update_flag,
						$order_history_delete_flag,
						$complain_view_flag,
						$complain_insert_flag,
						$complain_update_flag,
						$complain_delete_flag,
						$request_view_flag,
						$request_insert_flag,
						$request_update_flag,
						$request_delete_flag,
						$customer_meeting_view_flag,
						$customer_meeting_insert_flag,
						$customer_meeting_update_flag,
						$customer_meeting_delete_flag,
						$near_by_me_view_flag,
						$change_root_view_flag,
						$change_root_insert_flag,
						$change_root_update_flag,
						$change_root_delete_flag,
						$expense_view_flag,
						$expense_insert_flag,
						$expense_update_flag,
						$expense_delete_flag,
						$leave_view_flag,
						$leave_insert_flag,
						$leave_update_flag,
						$leave_delete_flag,
						$area_view_flag,
						$area_insert_flag,
						$area_update_flag,
						$area_delete_flag,
						$visit_view_flag,
						$visit_insert_flag,
						$visit_update_flag,
						$visit_delete_flag,
						$price_list_view_flag,
						$bank_detail_view_flag,
						$scheme_view_flag,
						$discount_dealer_view_flag,
						$discount_distributor_view_flag,
						$gst_view_flag,
						$visit_card_view_flag,
						$traveling_view_flag,
						$tracking_flag,
						$attendance_insert_flag,

							$prospact_view_flag,
						$prospact_insert_flag,
						$prospact_update_flag,
						$prospact_delete_flag,

						$marchent_customer_view_flag,
						$marchent_customer_insert_flag,
						$marchent_customer_update_flag,
						$marchent_customer_delete_flag,

						$promotional_customer_view_flag,
						$promotional_customer_insert_flag,
						$promotional_customer_update_flag,
						$promotional_customer_delete_flag,

						$corporate_customer_view_flag,
						$corporate_customer_insert_flag,
						$corporate_customer_update_flag,
						$corporate_customer_delete_flag,

						$my_route_view_flag,
						$my_route_insert_flag,
						$customer_stock_add_flag,
						$deepfreezscheme_flag,
						
						$insentive_percentage,
						$image_path,
						$file_path,
						// $category_id,
						$main_city,
						$top_category_id,
						$travel_by_bike_flag,
						$travel_by_bus_flag,
						$travel_by_car_flag,
						$type_of_company,
						$tradercontractor_view_flag,
						$tradercontractor_insert_flag,
						$tradercontractor_update_flag,
						$tradercontractor_delete_flag,
						$mep_consultant_view_flag,
						$mep_consultant_insert_flag,
						$mep_consultant_update_flag,
						$mep_consultant_delete_flag,
						$builder_view_flag,
						$builder_insert_flag,
						$builder_update_flag,
						$builder_delete_flag,
						$brand_approval_visit_view_flag,
						$brand_approval_visit_insert_flag,
						$brand_approval_visit_update_flag,
						$brand_approval_visit_delete_flag,
						$weekday,
						$create_order_approve_flag,
						$quotation_approve_flag,
						$chain_wise_view_order_history_flag,
					);


					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
			if($uid!=0)
			{
				if ($city != "" && !empty($city)) {
					
				$class_id = $this->db->rp_getValue( "class", "id", "name LIKE '%".strtolower(trim($state))."%'", 0 );
				$area_id = $this->db->rp_getValue( "area", "id", "name LIKE '%".strtolower(trim($city))."%'", 0 );
				$city_id = $this->db->rp_getValue( "city", "id", "name LIKE '%".strtolower(trim($main_city))."%'", 0 );

				$mapping_id = $this->db->rp_insert("sales_executive_map_area",array($uid,$type,$class_id,$area_id,$city_id,$type_slug),array("sales_executive_id","executive_type","class_id","area_id","city_id","type_slug"),0);

				}


				//$ack=$this->addArea($uid,$type_of_executive,$type_slug,$class_id,$item);
				
				$reply=array("ack"=>1,"developer_msg"=>"insert Successfully","ack_msg"=>"Success! Insert Executive Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Insert Record Failed.");
				return $reply;
			}
		}
	 }
//-------------------------------------------------------------------------------------------//
//--------#Update Sales Officer Informstion------------------------------------------------//	 
	public function UpdateSalesExecutive($executive_id,$id,$name,$email,$username,$phone,$address,$zip,$country,$state,$city,$zone,$imei,$type,$regDate,$sm_id,$asm_id,$so_id,$se_id,$class_id,$item,$refreshToken,$executive_in_min,$executive_in_max,$executive_out,$super_stokist_order_view_flag="",$super_stokist_order_insert_flag="",$super_stokist_order_update_flag="",$super_stokist_order_delete_flag="",$outlets_order_view_flag="",$outlets_order_insert_flag="",$outlets_order_update_flag="",$outlets_order_delete_flag="",$dealer_order_view_flag="",$dealer_order_insert_flag="",$dealer_order_update_flag="",$dealer_order_delete_flag="",$project_order_view_flag="",$project_order_insert_flag="",$project_order_update_flag="",$project_order_delete_flag="",$oem_order_view_flag="",$oem_order_insert_flag="",$oem_order_update_flag="",$oem_order_delete_flag="",$quotation_view_flag="",$quotation_insert_flag="",$quotation_update_flag="",$quotation_delete_flag="",$survey_customer_view_flag="",$survey_customer_insert_flag="",$survey_customer_update_flag="",$survey_customer_delete_flag="",$customer_leads_view_flag="",$customer_leads_insert_flag="",$customer_leads_update_flag="",$customer_leads_delete_flag="",$customer_view_flag="",$customer_insert_flag="",$customer_update_flag="",$customer_delete_flag="",$followup_view_flag="",$followup_insert_flag="",$followup_update_flag="",$followup_delete_flag="",$create_order_view_flag="",$create_order_insert_flag="",$create_order_update_flag="",$create_order_delete_flag="",$order_history_view_flag="",$order_history_insert_flag="",$order_history_update_flag="",$order_history_delete_flag="",$complain_view_flag="",$complain_insert_flag="",$complain_update_flag="",$complain_delete_flag="",$request_view_flag="",$request_insert_flag="",$request_update_flag="",$request_delete_flag="",$customer_meeting_view_flag="",$customer_meeting_insert_flag="",$customer_meeting_update_flag="",$customer_meeting_delete_flag="",$near_by_me_view_flag="",$change_root_view_flag="",$change_root_insert_flag="",$change_root_update_flag="",$change_root_delete_flag="",$expense_view_flag="",$expense_insert_flag="",$expense_update_flag="",$expense_delete_flag="",$leave_view_flag="",$leave_insert_flag="",$leave_update_flag="",$leave_delete_flag="",$area_view_flag="",$area_insert_flag="",$area_update_flag="",$area_delete_flag="",$visit_view_flag="",$visit_insert_flag="",$visit_update_flag="",$visit_delete_flag="",$price_list_view_flag="",$bank_detail_view_flag="",$scheme_view_flag="",$discount_dealer_view_flag="",$discount_distributor_view_flag="",$gst_view_flag="",$visit_card_view_flag="",$traveling_view_flag="",$tracking_flag="",$attendance_insert_flag="",$prospact_view_flag="",$prospact_insert_flag="",$prospact_update_flag="",$prospact_delete_flag="",$marchent_customer_view_flag="",$marchent_customer_insert_flag="",$marchent_customer_update_flag="",$marchent_customer_delete_flag="",$promotional_customer_view_flag="",$promotional_customer_insert_flag="",$promotional_customer_update_flag="",$promotional_customer_delete_flag="",$corporate_customer_view_flag="",$corporate_customer_insert_flag="",$corporate_customer_update_flag="",$corporate_customer_delete_flag="",$my_route_view_flag="",$my_route_insert_flag="",$insentive_percentage,$image_path,$file_path,$top_cat_id="",$customer_stock_add_flag="",$deepfreezscheme_flag="",$main_city,$top_category_id,$travel_by_bike_flag,$travel_by_bus_flag,$travel_by_car_flag,$type_of_company,$tradercontractor_view_flag="",$tradercontractor_insert_flag="",$tradercontractor_update_flag="",$tradercontractor_delete_flag="",$mep_consultant_view_flag="",$mep_consultant_insert_flag="",$mep_consultant_update_flag="",$mep_consultant_delete_flag="",$builder_view_flag="",$builder_insert_flag="",$builder_update_flag="",$builder_delete_flag="",$brand_approval_visit_view_flag="",$brand_approval_visit_insert_flag="",$brand_approval_visit_update_flag="",$brand_approval_visit_delete_flag="",$am_id,$weekday,$create_order_approve_flag,$quotation_approve_flag,$chain_wise_view_order_history_flag)
	  {
	  		// echo $tradercontractor_view_flag;die;

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
	  	// 	echo "sdf";die;

		 $dup_where = " id!='".$_REQUEST['id']."' AND (username = '".$username."' OR phone = '".$phone."') AND isDelete=0 AND isActive=1";
		
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
			if($r > 0){
			$reply=array("ack"=>0,"developer_msg"=>"Phone number/ Username already assigned to another user please try another!!","ack_msg"=>"Phone number/ Username already assigned to another user please try another!!");
			return $reply;
			}
			else{
				 $type_of_executive=$type;
				 if($type=='sales_executive')
				 {
					 $type_slug='SalesExecutive';
				 }
				 else if($type=='sales_manager')
				 {
					 $type_slug='SalesManager';
				 }
				 else if($type=='sales_officer')
				 {
					 $type_slug='SalesOfficer';
				 }
				 else if($type=='area_sales_manager')
				 {
					 $type_slug='AreaSalesManager';
				 }
				 if($city=="")
			     {
			        $city=$this->db->rp_getValue($this->ctable,"city","id='".$_REQUEST['id']."'",0);    
			     }
				$rows 	= array(
							"name"	=> $name,
							"email"			=> $email,	
							"username"			=> $username,	
							//"password"			=> $password,	
							"phone"			=> $phone,
							"address"		=> $address,
							"zip"			=> $zip,
							"country"		=> $country,
							"state"			=> $state,
							"city"			=> $city,
							"zone"			=> $zone,
							"imei"			=> $imei,
							"type"		=> $type,
							"sm_id"		=> $sm_id,
							"asm_id"		=> $asm_id,
							"so_id"		=> $so_id,
							"am_id"		=> $am_id,
							"se_id"		=> $se_id,
							"class_id"		=> $class_id,
							"refreshToken"		=> $refreshToken,
							"executive_in_min"		=> $executive_in_min,
							"executive_in_max"		=> $executive_in_max,
							"executive_out"		=> $executive_out,
							"super_stokist_order_view_flag"  => $super_stokist_order_view_flag,
							"super_stokist_order_insert_flag"  => $super_stokist_order_insert_flag,
							"super_stokist_order_update_flag"  => $super_stokist_order_update_flag,
							"super_stokist_order_delete_flag"  => $super_stokist_order_delete_flag,
							"outlets_order_view_flag"  => $outlets_order_view_flag,
							"outlets_order_insert_flag"  => $outlets_order_insert_flag,
							"outlets_order_update_flag"  => $outlets_order_update_flag,
							"outlets_order_delete_flag"  => $outlets_order_delete_flag,
							"dealer_order_view_flag"  => $dealer_order_view_flag,
							"dealer_order_insert_flag"  => $dealer_order_insert_flag,
							"dealer_order_update_flag"  => $dealer_order_update_flag,
							"dealer_order_delete_flag"  => $dealer_order_delete_flag,
							"project_order_view_flag"  => $project_order_view_flag,
							"project_order_insert_flag"  => $project_order_insert_flag,
							"project_order_update_flag"  => $project_order_update_flag,
							"project_order_delete_flag"  => $project_order_delete_flag,
							"oem_order_view_flag"  => $oem_order_view_flag,
							"oem_order_insert_flag"  => $oem_order_insert_flag,
							"oem_order_update_flag"  => $oem_order_update_flag,
							"oem_order_delete_flag"  => $oem_order_delete_flag,
							"quotation_view_flag"  => $quotation_view_flag,
							"quotation_insert_flag"  => $quotation_insert_flag,
							"quotation_update_flag"  => $quotation_update_flag,
							"quotation_delete_flag"  => $quotation_delete_flag,
							"survey_customer_view_flag"  => $survey_customer_view_flag,
							"survey_customer_insert_flag"  => $survey_customer_insert_flag,
							"survey_customer_update_flag"  => $survey_customer_update_flag,
							"survey_customer_delete_flag"  => $survey_customer_delete_flag,
							"customer_leads_view_flag"     => $customer_leads_view_flag,
							"customer_leads_insert_flag"     => $customer_leads_insert_flag,
							"customer_leads_update_flag"     => $customer_leads_update_flag,
							"customer_leads_delete_flag"     => $customer_leads_delete_flag,
							"customer_view_flag"  => $customer_view_flag,
							"customer_insert_flag"  => $customer_insert_flag,
							"customer_update_flag"  => $customer_update_flag,
							"customer_delete_flag"  => $customer_delete_flag,
							"followup_view_flag"  => $followup_view_flag,
							"followup_insert_flag"  => $followup_insert_flag,
							"followup_update_flag"  => $followup_update_flag,
							"followup_delete_flag"  => $followup_delete_flag,
							"create_order_view_flag"  => $create_order_view_flag,
							"create_order_insert_flag"  => $create_order_insert_flag,
							"create_order_update_flag"  => $create_order_update_flag,
							"create_order_delete_flag"  => $create_order_delete_flag,
							"order_history_view_flag"  => $order_history_view_flag,
							"order_history_insert_flag"  => $order_history_insert_flag,
							"order_history_update_flag"  => $order_history_update_flag,
							"order_history_delete_flag"  => $order_history_delete_flag,
							"complain_view_flag"  => $complain_view_flag,
							"complain_insert_flag"  => $complain_insert_flag,
							"complain_update_flag"  => $complain_update_flag,
							"complain_delete_flag"  => $complain_delete_flag,
							"request_view_flag"  => $request_view_flag,
							"request_insert_flag"  => $request_insert_flag,
							"request_update_flag"  => $request_update_flag,
							"request_delete_flag"  => $request_delete_flag,
							"customer_meeting_view_flag"  => $customer_meeting_view_flag,
							"customer_meeting_insert_flag"  => $customer_meeting_insert_flag,
							"customer_meeting_update_flag"  => $customer_meeting_update_flag,
							"customer_meeting_delete_flag"  => $customer_meeting_delete_flag,
							"near_by_me_view_flag"  => $near_by_me_view_flag,
							"change_root_view_flag"  => $change_root_view_flag,
							"change_root_insert_flag"  => $change_root_insert_flag,
							"change_root_update_flag"  => $change_root_update_flag,
							"change_root_delete_flag"  => $change_root_delete_flag,
							"expense_view_flag"  => $expense_view_flag,
							"expense_insert_flag"  => $expense_insert_flag,
							"expense_update_flag"  => $expense_update_flag,
							"expense_delete_flag"  => $expense_delete_flag,
							"leave_view_flag"  => $leave_view_flag,
							"leave_insert_flag"  => $leave_insert_flag,
							"leave_update_flag"  => $leave_update_flag,
							"leave_delete_flag"  => $leave_delete_flag,
							"area_view_flag"  => $area_view_flag,
							"area_insert_flag"  => $area_insert_flag,
							"area_update_flag"  => $area_update_flag,
							"area_delete_flag"  => $area_delete_flag,
							"visit_view_flag"  => $visit_view_flag,
							"visit_insert_flag"  => $visit_insert_flag,
							"visit_update_flag"  => $visit_update_flag,
							"visit_delete_flag"  => $visit_delete_flag,
							"price_list_view_flag"  => $price_list_view_flag,
							"bank_detail_view_flag"  => $bank_detail_view_flag,
							"scheme_view_flag"  => $scheme_view_flag,
							"discount_dealer_view_flag"  => $discount_dealer_view_flag,
							"discount_distributor_view_flag"  => $discount_distributor_view_flag,
							"gst_view_flag"  => $gst_view_flag,
							"visit_card_view_flag"  => $visit_card_view_flag,
							"traveling_view_flag"  => $traveling_view_flag,
							"tracking_flag"  => $tracking_flag,
							"attendance_insert_flag"=>$attendance_insert_flag,

							"prospact_view_flag"=>$prospact_view_flag,
							"prospact_insert_flag"=>$prospact_insert_flag,
							"prospact_update_flag"=>$prospact_update_flag,
							"prospact_delete_flag"=>$prospact_delete_flag,

							"marchent_customer_view_flag"=>$marchent_customer_view_flag,
							"marchent_customer_insert_flag"=>$marchent_customer_insert_flag,
							"marchent_customer_update_flag"=>$marchent_customer_update_flag,
							"marchent_customer_delete_flag"=>$marchent_customer_delete_flag,

							"promotional_customer_view_flag"=>$promotional_customer_view_flag,
							"promotional_customer_insert_flag"=>$promotional_customer_insert_flag,
							"promotional_customer_update_flag"=>$promotional_customer_update_flag,
							"promotional_customer_delete_flag"=>$promotional_customer_delete_flag,

							"corporate_customer_view_flag"=>$corporate_customer_view_flag,
							"corporate_customer_insert_flag"=>$corporate_customer_insert_flag,
							"corporate_customer_update_flag"=>$corporate_customer_update_flag,
							"corporate_customer_delete_flag"=>$corporate_customer_delete_flag,

							"my_route_view_flag"=>$my_route_view_flag,
							"my_route_insert_flag"=>$my_route_insert_flag,
							"customer_stock_add_flag"=>$customer_stock_add_flag,
							"deepfreezscheme_flag"=>$deepfreezscheme_flag,

							"insentive_percentage"=>$insentive_percentage,
							"gst_file_path"        =>$image_path,
							"visiting_card_file_path"=>$file_path,
							// "category_id"=>$category_id,
							"main_city"=>$main_city,
							"top_category_id"=>$top_category_id,
							"travel_by_bike_flag"=>$travel_by_bike_flag,
							"travel_by_bus_flag"=>$travel_by_bus_flag,
							"travel_by_car_flag"=>$travel_by_car_flag,
							"type_of_company"=>$type_of_company,
							"tradercontractor_view_flag"=>$tradercontractor_view_flag,
							"tradercontractor_insert_flag"=>$tradercontractor_insert_flag,
							"tradercontractor_update_flag"=>$tradercontractor_update_flag,
							"tradercontractor_delete_flag"=>$tradercontractor_delete_flag,
							"mep_consultant_view_flag"=>$mep_consultant_view_flag,
							"mep_consultant_insert_flag"=>$mep_consultant_insert_flag,
							"mep_consultant_update_flag"=>$mep_consultant_update_flag,
							"mep_consultant_delete_flag"=>$mep_consultant_delete_flag,
							"builder_view_flag"=>$builder_view_flag,
							"builder_insert_flag"=>$builder_insert_flag,
							"builder_update_flag"=>$builder_update_flag,
							"builder_delete_flag"=>$builder_delete_flag,
							"brand_approval_visit_view_flag"=>$brand_approval_visit_view_flag,
							"brand_approval_visit_insert_flag"=>$brand_approval_visit_insert_flag,
							"brand_approval_visit_update_flag"=>$brand_approval_visit_update_flag,
							"brand_approval_visit_delete_flag"=>$brand_approval_visit_delete_flag,
							"weekday"=>$weekday,
							"quotation_approve_flag"=>$quotation_approve_flag,
							"create_order_approve_flag"=>$create_order_approve_flag,
							"chain_wise_view_order_history_flag"=>$chain_wise_view_order_history_flag,
					);

				// echo "<pre>"; print_r($rows); echo "<br>";
				// echo "<pre>"; print_r($values); exit;
				$where	= "id='".$executive_id."'";
				$isUpdated=$this->db->rp_update($this->ctable,$rows,$where,0);
				
				if($isUpdated)
				{
					//$ack=$this->addArea($executive_id,$type_of_executive,$type_slug,$class_id,$item);
					$reply=array("ack"=>1,"developer_msg"=>"User Update Successfull!!.","ack_msg"=>"Success! Update Executive Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Failed.");
					return $reply;
				}
			}	
		}
//--------------------------------------------------------------------------------------------------------//
//------Add Area for Sales Officer on(sales_executive_map_area)---------------------------------------------//	
		function addArea($executive_id,$type_of_executive,$type_slug,$class_id,$item)
		{
             $this->db->rp_delete($this->ctableMap,"sales_executive_id='".$executive_id."'",0);
             foreach($item as $b)
             {
				$area_id=$b['area_id'];
                $this->db->rp_insert($this->ctableMap,array($executive_id,$type_of_executive,$type_slug,$class_id,$area_id),array("sales_executive_id","executive_type","type_slug","class_id","area_id"),0);
             }
             return true;
        
       
		}
//-----------------------------------------------------------------------------//
//--------#Edit Sales Officer -----------------------------------------------//		
	public function EditSalesExecutive($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		
		$result=array();
		$result['name']		= htmlentities($ctable_d['name']);
		$result['email']		= stripslashes($ctable_d['email']);
		$result['username']		= stripslashes($ctable_d['username']);
		$result['password']		= stripslashes($ctable_d['password']);
		$result['phone']		= stripslashes($ctable_d['phone']);
		$result['address']		= htmlentities($ctable_d['address']);
		$result['zip']			= stripslashes($ctable_d['zip']);
		$result['country']		= $ctable_d['country'];
		$result['state'] 		= stripslashes($ctable_d['state']);
		$result['city'] 		= stripslashes($ctable_d['city']);
		$result['zone'] 		= stripslashes($ctable_d['zone']);
		$result['imei'] 		= stripslashes($ctable_d['imei']);
		$result['type'] 		= stripslashes($ctable_d['type']);
		$result['sm_id'] 		= stripslashes($ctable_d['sm_id']);
		$result['asm_id'] 		= stripslashes($ctable_d['asm_id']);
		$result['so_id'] 		= stripslashes($ctable_d['so_id']);
		$result['am_id'] 		= stripslashes($ctable_d['am_id']);
		$result['se_id'] 		= stripslashes($ctable_d['se_id']);
		$result['class_id'] 		= stripslashes($ctable_d['class_id']);
		$result['refreshToken'] 		= stripslashes($ctable_d['refreshToken']);
		$result['executive_in_min'] 		= stripslashes($ctable_d['executive_in_min']);
		$result['executive_in_max'] 		= stripslashes($ctable_d['executive_in_max']);
		$result['executive_out'] 		= stripslashes($ctable_d['executive_out']);
		$result['super_stokist_order_view_flag'] 		= stripslashes($ctable_d['super_stokist_order_view_flag']);
		$result['super_stokist_order_insert_flag'] 		= stripslashes($ctable_d['super_stokist_order_insert_flag']);
		$result['super_stokist_order_update_flag'] 		= stripslashes($ctable_d['super_stokist_order_update_flag']);
		$result['super_stokist_order_delete_flag'] 		= stripslashes($ctable_d['super_stokist_order_delete_flag']);
		$result['outlets_order_view_flag'] 				= stripslashes($ctable_d['outlets_order_view_flag']);
		$result['outlets_order_insert_flag'] 			= stripslashes($ctable_d['outlets_order_insert_flag']);
		$result['outlets_order_update_flag'] 			= stripslashes($ctable_d['outlets_order_update_flag']);
		$result['outlets_order_delete_flag'] 			= stripslashes($ctable_d['outlets_order_delete_flag']);
		$result['dealer_order_view_flag'] 				= stripslashes($ctable_d['dealer_order_view_flag']);
		$result['dealer_order_insert_flag'] 		= stripslashes($ctable_d['dealer_order_insert_flag']);
		$result['dealer_order_update_flag'] 		= stripslashes($ctable_d['dealer_order_update_flag']);
		$result['dealer_order_delete_flag'] 		= stripslashes($ctable_d['dealer_order_delete_flag']);
		$result['project_order_view_flag'] 				= stripslashes($ctable_d['project_order_view_flag']);
		$result['project_order_insert_flag'] 		= stripslashes($ctable_d['project_order_insert_flag']);
		$result['project_order_update_flag'] 		= stripslashes($ctable_d['project_order_update_flag']);
		$result['project_order_delete_flag'] 		= stripslashes($ctable_d['project_order_delete_flag']);
		$result['oem_order_view_flag'] 				= stripslashes($ctable_d['oem_order_view_flag']);
		$result['oem_order_insert_flag'] 		= stripslashes($ctable_d['oem_order_insert_flag']);
		$result['oem_order_update_flag'] 		= stripslashes($ctable_d['oem_order_update_flag']);
		$result['oem_order_delete_flag'] 		= stripslashes($ctable_d['oem_order_delete_flag']);

		$result['quotation_view_flag'] 				= stripslashes($ctable_d['quotation_view_flag']);
		$result['quotation_insert_flag'] 		= stripslashes($ctable_d['quotation_insert_flag']);
		$result['quotation_update_flag'] 		= stripslashes($ctable_d['quotation_update_flag']);
		$result['quotation_delete_flag'] 		= stripslashes($ctable_d['quotation_delete_flag']);

		$result['survey_customer_view_flag'] 		= stripslashes($ctable_d['survey_customer_view_flag']);
		$result['survey_customer_insert_flag'] 		= stripslashes($ctable_d['survey_customer_insert_flag']);
		$result['survey_customer_update_flag'] 		= stripslashes($ctable_d['survey_customer_update_flag']);
		$result['survey_customer_delete_flag'] 		= stripslashes($ctable_d['survey_customer_delete_flag']);
		$result['customer_view_flag'] 		= stripslashes($ctable_d['customer_view_flag']);
		$result['customer_insert_flag'] 		= stripslashes($ctable_d['customer_insert_flag']);
		$result['customer_update_flag'] 		= stripslashes($ctable_d['customer_update_flag']);
		$result['customer_delete_flag'] 		= stripslashes($ctable_d['customer_delete_flag']);
		$result['followup_view_flag'] 		= stripslashes($ctable_d['followup_view_flag']);
		$result['followup_insert_flag'] 		= stripslashes($ctable_d['followup_insert_flag']);
		$result['followup_update_flag'] 		= stripslashes($ctable_d['followup_update_flag']);
		$result['followup_delete_flag'] 		= stripslashes($ctable_d['followup_delete_flag']);
		$result['create_order_view_flag'] 		= stripslashes($ctable_d['create_order_view_flag']);
		$result['create_order_insert_flag'] 		= stripslashes($ctable_d['create_order_insert_flag']);
		$result['create_order_update_flag'] 		= stripslashes($ctable_d['create_order_update_flag']);
		$result['create_order_delete_flag'] 		= stripslashes($ctable_d['create_order_delete_flag']);
		$result['order_history_view_flag'] 		= stripslashes($ctable_d['order_history_view_flag']);
		$result['order_history_insert_flag'] 		= stripslashes($ctable_d['order_history_insert_flag']);
		$result['order_history_update_flag'] 		= stripslashes($ctable_d['order_history_update_flag']);
		$result['order_history_delete_flag'] 		= stripslashes($ctable_d['order_history_delete_flag']);
		$result['complain_view_flag'] 		= stripslashes($ctable_d['complain_view_flag']);
		$result['complain_insert_flag'] 		= stripslashes($ctable_d['complain_insert_flag']);
		$result['complain_update_flag'] 		= stripslashes($ctable_d['complain_update_flag']);
		$result['complain_delete_flag'] 		= stripslashes($ctable_d['complain_delete_flag']);
		$result['request_view_flag'] 		= stripslashes($ctable_d['request_view_flag']);
		$result['request_insert_flag'] 		= stripslashes($ctable_d['request_insert_flag']);
		$result['request_update_flag'] 		= stripslashes($ctable_d['request_update_flag']);
		$result['request_delete_flag'] 		= stripslashes($ctable_d['request_delete_flag']);
		$result['customer_meeting_view_flag'] 		= stripslashes($ctable_d['customer_meeting_view_flag']);
		$result['customer_meeting_insert_flag'] 		= stripslashes($ctable_d['customer_meeting_insert_flag']);
		$result['customer_meeting_update_flag'] 		= stripslashes($ctable_d['customer_meeting_update_flag']);
		$result['customer_meeting_delete_flag'] 		= stripslashes($ctable_d['customer_meeting_delete_flag']);
		$result['near_by_me_view_flag'] 		= stripslashes($ctable_d['near_by_me_view_flag']);
		$result['change_root_view_flag'] 		= stripslashes($ctable_d['change_root_view_flag']);
		$result['change_root_insert_flag'] 		= stripslashes($ctable_d['change_root_insert_flag']);
		$result['change_root_update_flag'] 		= stripslashes($ctable_d['change_root_update_flag']);
		$result['change_root_delete_flag'] 		= stripslashes($ctable_d['change_root_delete_flag']);
		$result['expense_view_flag'] 		= stripslashes($ctable_d['expense_view_flag']);
		$result['expense_insert_flag'] 		= stripslashes($ctable_d['expense_insert_flag']);
		$result['expense_update_flag'] 		= stripslashes($ctable_d['expense_update_flag']);
		$result['expense_delete_flag'] 		= stripslashes($ctable_d['expense_delete_flag']);
		$result['leave_view_flag'] 		    = stripslashes($ctable_d['leave_view_flag']);
		$result['leave_insert_flag'] 		= stripslashes($ctable_d['leave_insert_flag']);
		$result['leave_update_flag'] 		= stripslashes($ctable_d['leave_update_flag']);
		$result['leave_delete_flag'] 		= stripslashes($ctable_d['leave_delete_flag']);
		$result['area_view_flag'] 		    = stripslashes($ctable_d['area_view_flag']);
		$result['area_insert_flag'] 		= stripslashes($ctable_d['area_insert_flag']);
		$result['area_update_flag'] 		= stripslashes($ctable_d['area_update_flag']);
		$result['area_delete_flag'] 		= stripslashes($ctable_d['area_delete_flag']);
		$result['visit_view_flag'] 		    = stripslashes($ctable_d['visit_view_flag']);
		$result['visit_insert_flag'] 		= stripslashes($ctable_d['visit_insert_flag']);
		$result['visit_update_flag'] 		= stripslashes($ctable_d['visit_update_flag']);
		$result['visit_delete_flag'] 		= stripslashes($ctable_d['visit_delete_flag']);
		$result['price_list_view_flag'] 		= stripslashes($ctable_d['price_list_view_flag']);
		$result['bank_detail_view_flag'] 		= stripslashes($ctable_d['bank_detail_view_flag']);
		$result['scheme_view_flag'] 		= stripslashes($ctable_d['scheme_view_flag']);
		$result['discount_dealer_view_flag'] 		= stripslashes($ctable_d['discount_dealer_view_flag']);
		$result['discount_distributor_view_flag'] 		= stripslashes($ctable_d['discount_distributor_view_flag']);
		$result['gst_view_flag'] 		= stripslashes($ctable_d['gst_view_flag']);
		$result['visit_card_view_flag'] 		= stripslashes($ctable_d['visit_card_view_flag']);
		$result['traveling_view_flag'] 		= stripslashes($ctable_d['traveling_view_flag']);
		$result['tracking_flag'] 		= stripslashes($ctable_d['tracking_flag']);

		$result['attendance_insert_flag'] 		= stripslashes($ctable_d['attendance_insert_flag']);

			$result['prospact_view_flag'] 		= stripslashes($ctable_d['prospact_view_flag']);
		$result['prospact_insert_flag'] 		= stripslashes($ctable_d['prospact_insert_flag']);
		$result['prospact_update_flag'] 		= stripslashes($ctable_d['prospact_update_flag']);
		$result['prospact_delete_flag'] 		= stripslashes($ctable_d['prospact_delete_flag']);

		$result['marchent_customer_view_flag'] 		= stripslashes($ctable_d['marchent_customer_view_flag']);
		$result['marchent_customer_insert_flag'] 		= stripslashes($ctable_d['marchent_customer_insert_flag']);
		$result['marchent_customer_update_flag'] 		= stripslashes($ctable_d['marchent_customer_update_flag']);
		$result['marchent_customer_delete_flag'] 		= stripslashes($ctable_d['marchent_customer_delete_flag']);
		
		$result['promotional_customer_view_flag'] 		= stripslashes($ctable_d['promotional_customer_view_flag']);
		$result['promotional_customer_insert_flag'] 		= stripslashes($ctable_d['promotional_customer_insert_flag']);
		$result['promotional_customer_update_flag'] 		= stripslashes($ctable_d['promotional_customer_update_flag']);
		$result['promotional_customer_delete_flag'] 		= stripslashes($ctable_d['promotional_customer_delete_flag']);
			
		$result['corporate_customer_view_flag'] 		= stripslashes($ctable_d['corporate_customer_view_flag']);
		$result['corporate_customer_insert_flag'] 		= stripslashes($ctable_d['corporate_customer_insert_flag']);
		$result['corporate_customer_update_flag'] 		= stripslashes($ctable_d['corporate_customer_update_flag']);
		$result['corporate_customer_delete_flag'] 		= stripslashes($ctable_d['corporate_customer_delete_flag']);
		
		$result['insentive_percentage'] 			= stripslashes($ctable_d['insentive_percentage']);
		$result['image_path'] 			            = stripslashes($ctable_d['gst_file_path']);
		$result['file_path'] 			            = stripslashes($ctable_d['visiting_card_file_path']);

		$result['customer_leads_view_flag'] 		= stripslashes($ctable_d['customer_leads_view_flag']);
		$result['customer_leads_insert_flag'] 		= stripslashes($ctable_d['customer_leads_insert_flag']);
		$result['customer_leads_update_flag'] 		= stripslashes($ctable_d['customer_leads_update_flag']);
		$result['customer_leads_delete_flag'] 		= stripslashes($ctable_d['customer_leads_delete_flag']);
	
		$result['my_route_view_flag'] 		= stripslashes($ctable_d['my_route_view_flag']);
		$result['my_route_insert_flag'] 		= stripslashes($ctable_d['my_route_insert_flag']);
		$result['customer_stock_add_flag'] 		= stripslashes($ctable_d['customer_stock_add_flag']);
		$result['deepfreezscheme_flag'] 		= stripslashes($ctable_d['deepfreezscheme_flag']);
		// $result['category_id'] 		= stripslashes($ctable_d['category_id']);
				$result['main_city'] 		= stripslashes($ctable_d['main_city']);
		$result['top_category_id'] 		= stripslashes($ctable_d['top_category_id']);
		$result['travel_by_bike_flag'] 		= stripslashes($ctable_d['travel_by_bike_flag']);
		$result['travel_by_bus_flag'] 		= stripslashes($ctable_d['travel_by_bus_flag']);
		$result['travel_by_car_flag'] 		= stripslashes($ctable_d['travel_by_car_flag']);
		$result['type_of_company'] 		= stripslashes($ctable_d['type_of_company']);
		$result['tradercontractor_view_flag'] = stripslashes($ctable_d['tradercontractor_view_flag']);
		$result['tradercontractor_insert_flag'] = stripslashes($ctable_d['tradercontractor_insert_flag']);
		$result['tradercontractor_update_flag'] = stripslashes($ctable_d['tradercontractor_update_flag']);
		$result['tradercontractor_delete_flag'] = stripslashes($ctable_d['tradercontractor_delete_flag']);
		$result['mep_consultant_view_flag'] = stripslashes($ctable_d['mep_consultant_view_flag']);
		$result['mep_consultant_insert_flag'] = stripslashes($ctable_d['mep_consultant_insert_flag']);
		$result['mep_consultant_update_flag'] = stripslashes($ctable_d['mep_consultant_update_flag']);
		$result['mep_consultant_delete_flag'] = stripslashes($ctable_d['mep_consultant_delete_flag']);
		$result['builder_view_flag'] = stripslashes($ctable_d['builder_view_flag']);
		$result['builder_insert_flag'] = stripslashes($ctable_d['builder_insert_flag']);
		$result['builder_update_flag'] = stripslashes($ctable_d['builder_update_flag']);
		$result['builder_delete_flag'] = stripslashes($ctable_d['builder_delete_flag']);
		$result['brand_approval_visit_view_flag'] = stripslashes($ctable_d['brand_approval_visit_view_flag']);
		$result['brand_approval_visit_insert_flag'] = stripslashes($ctable_d['brand_approval_visit_insert_flag']);
		$result['brand_approval_visit_update_flag'] = stripslashes($ctable_d['brand_approval_visit_update_flag']);
		$result['brand_approval_visit_delete_flag'] = stripslashes($ctable_d['brand_approval_visit_delete_flag']);
		$result['weekday'] = stripslashes($ctable_d['weekday']);
		$result['create_order_approve_flag'] = stripslashes($ctable_d['create_order_approve_flag']);
		$result['quotation_approve_flag'] = stripslashes($ctable_d['quotation_approve_flag']);
		$result['chain_wise_view_order_history_flag'] = stripslashes($ctable_d['chain_wise_view_order_history_flag']);
		// $result['customer_leads_update_flag'] 		= stripslashes($ctable_d['customer_leads_update_flag']);
		// $result['customer_leads_delete_flag'] 		= stripslashes($ctable_d['customer_leads_delete_flag']);

		
		$area_id_r=$this->db->rp_getData("sales_executive_map_area","area_id","sales_executive_id='".$detail['id']."' AND isDelete=0","",0);
		while($w=mysqli_fetch_array($area_id_r))
		{
			$area_id[]=$w['area_id'];
			
		}
		$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Sales Officer Successfully.","result"=>$result,"area_id"=>$area_id);
		return $reply;
	
	}
	
	public function getsalesDetail($sales_id,$from_date,$to_date,$customer_type="")
	{	
		$order_date="";
		$where = " id='".$sales_id."' AND isDelete=0";
		$sales_type = $this->db->rp_getValue($this->ctable,"type",$where_type,0);
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		/*if($sales_type=='sales_manager')
		{
			$where = "sm_id='".$sales_id."' AND isDelete=0 AND isActive=1";
			$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		}
		else if($sales_type=='area_sales_manager')
		{
			$where = "asm_id='".$sales_id."' AND isDelete=0 AND isActive=1";
			$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		}
		else if($sales_type=='sales_officer')
		{
			$where = "so_id='".$sales_id."' AND isDelete=0 AND isActive=1";
			$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		}
		*/
		while($ctable_d = mysqli_fetch_array($ctable_r))
		{
			//print_r($ctable_d);
		$result=array();
		$result=array("id"=>$ctable_d['id'],"name"=>$ctable_d['name'],"sales_type"=>$ctable_d['type']);
		$r[] = $result;
		}
		$FromDate=date('Y-m-d',strtotime($from_date));
		$ToDate=date('Y-m-d',strtotime($to_date));
		foreach($r as $data)
		{
			$result1=array();
			$result1['id']=$data['id'];
			$result1['name']=$data['name'];
			$result1['sales_type']=$data['sales_type'];
			if(!array_key_exists($data['sales_type'],$result_data))
			{
				$result_data[$data['sales_type']]=array("title"=>$this->sales_type_title[$data['sales_type']],'type'=>$data['sales_type'],"orders"=>array());
			}
			$data['amount']=$this->db->rp_getValue("orders","SUM(grand_total)","sales_id='".$result1['id']."' AND order_date>='".$FromDate."' AND order_date<='".$ToDate."' AND  customer_type='".$customer_type."'",0);
			if($data['amount']!="")
			{
				$result1['amount']=$data['amount'];
			}
			else
			{
				$result1['amount']="";
			}
			//$result1['amount']=
			//$result1['amount']=$data['amount'];
			
			$result_data[$data['sales_type']]['orders'] []= $result1;
		}
		foreach($result_data as $d)
		{
			$final_result[]=$d;
		}
		if(!empty($result_data))
		{
			$ack=array( "ack"=>1,"ack_msg"=>"Successfully Get sales Detail  !!","developer_msg"=>"You got it!!","result"=>$final_result,);
			return $ack;
		}
		else
		{
			$ack=array( "ack"=>0,"ack_msg"=>"No Report Found !!","developer_msg"=>"No Report Found!!","result"=>$final_result,);
			return $ack;
		}
	}
				
	public function getInquiryReport($sales_id,$from_date,$to_date)
	{	
		$order_date="";
		$where_type = " id='".$sales_id."' AND isDelete=0";
		$sales_type = $this->db->rp_getValue($this->ctable,"type",$where_type,0);
		if($sales_type=='sales_manager')
		{
			$where = "sm_id='".$sales_id."' AND isDelete=0 AND isActive=1";
			$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		}
		else if($sales_type=='area_sales_manager')
		{
			$where = "asm_id='".$sales_id."' AND isDelete=0 AND isActive=1";
			$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		}
		else if($sales_type=='sales_officer')
		{
			$where = "so_id='".$sales_id."' AND isDelete=0 AND isActive=1";
			$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		}
		else
		{
			$ctable_r="sales_manager";
		}
		while($ctable_d = mysqli_fetch_array($ctable_r))
		{
			//print_r($ctable_d);
		$result=array();
		$result=array("id"=>$ctable_d['id'],"name"=>$ctable_d['name'],"sales_type"=>$ctable_d['type']);
		$r[] = $result;
		}
		$FromDate=date('Y-m-d',strtotime($from_date));
		$ToDate=date('Y-m-d',strtotime($to_date));
		$result_data=array();
		foreach($r as $data)
		{
			$result1=array();
			$result1['id']=$data['id'];
			$result1['name']=$data['name'];
			$result1['sales_type']=$data['sales_type'];
			
			if(!array_key_exists($data['sales_type'],$result_data))
			{
				$result_data[$data['sales_type']]=array("title"=>$this->sales_type_title[$data['sales_type']],'type'=>$data['sales_type'],"inquiry"=>array());
			}
			$data['count']=$this->db->rp_getValue("no_order_inquiry","COUNT(id)","sales_executive_id='".$result1['id']."' AND created_date>='".$FromDate."' AND created_date<='".$ToDate."'",0);
			if($data['count']!="")
			{
				$result1['count']=$data['count'];
			}
			else
			{
				$result1['count']="";
			}
			//$result1['amount']=
			//$result1['amount']=$data['amount'];
			
			$result_data[$data['sales_type']]['inquiry'] []= $result1;
		}
		foreach($result_data as $d)
		{
			$final_result[]=$d;
		}
		if(!empty($result_data))
		{
			$ack=array( "ack"=>1,"ack_msg"=>"Successfully Get Inquiries  !!","developer_msg"=>"You got it!!","result"=>$final_result,);
			return $ack;
		}
		else
		{
			$ack=array( "ack"=>0,"ack_msg"=>"No Inquiry Report Found !!","developer_msg"=>"No Inquiry Found!!","result"=>$final_result,);
			return $ack;
		}
	}
		
		
	
	
//------------------------------------------------------------------------------------//+
//--------Delete Sales Officer------------------------------------------------------//	
	public function SaledExecutiveDelete($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Sales Officer Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete record Failed.");
				return $reply;
			}
	}
//-----------------------------------------------------------------------------------------------//
	
	
	function generateActivationCode()
	{
		$characters='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$randStr="";
		for($i=0;$i<=5;$i++)
		{
			$randStr=$randStr.$characters[rand(0,strlen($characters)-1)];
		}
		return $randStr;
	}
	function aj_sendSMS($number,$sms)
	{
		require_once('notification.class.php');
	    $nt = new Notification();
		$msgId="NO";
		if($number!="")
		{
		   	$msgId=$nt->aj_sendSMSSecurity($number,$sms);
			if($msgId!=0)
			{
				return $deliveryStatus=array("ack"=>1,"ack_msg"=>"SMS sent to ".$number." successfully");	
			}
			//$deliveryStatus=$nt->aj_getDeliveryReport($msgId);
			else
			$deliveryStatus=array("ack"=>0,"ack_msg"=>"SMS sending failed on".$number,"reason"=>"Invalid mobile number or mobile switched off or out of coverage area!!");	
			return $deliveryStatus;			
		}		
		return array('ack'=>0,'ack_msg'=>"Internal Error!","developer_msg"=>"Empty Mobile Number");
	}
//---------------------Track Sales--------------------------------------------------//	
	function trackSales($sales_id,$date="")
	{
		$count=$this->db->rp_getTotalRecord($this->ctable,"id='".$sales_id."'");
		if($count>=1)
		{
			$where="sales_executive_id='".$sales_id."' AND isDelete=0";
			if($date!="")
			{
				$where.=" AND DATE(date)='".date("Y-m-d",strtotime($date))."'";
			}
			$sales_routes=$this->db->rp_getData($this->ctableTracking,"sales_executive_id,latitude,longitude,date,type,app_address",$where,"date ASC",0);
			// $sales_routes=$this->db->rp_getData($this->ctableTracking,"*",$where,"date ASC",0);
			if($sales_routes)
			{
				while($route=mysqli_fetch_assoc($sales_routes))
				{
					$result[]=array("lat"=>$route['latitude'],"lng"=>$route['longitude'],"date"=>date("d M H:i",strtotime($route['date'])),"type"=>$common_type[$route['type']],"type_slug"=>$route['type'],"icon"=>$this->pin_icon[$route['type']]);
				}
				$reply=array("ack"=>1,"ack_msg"=>"Sales Tracking Fetched!!","result"=>$result);
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"No Route Found!!");
			}
			
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Sales Found!!");
		}
		return $reply;
	}

	function trackSalesPin($sales_id,$date="")
	{
		$where = "isDelete=0";
		if($sales_id != "" && $sales_id != 0)
		{
			$where.=" AND sales_executive_id='".$sales_id."'";
		}
		if($date!="")
		{ 
			$where.=" AND DATE(date)='".date("Y-m-d",strtotime($date))."'";
		}
		$sales_routes=$this->db->rp_getData($this->ctableTracking,"sales_executive_id,latitude,longitude,date,type,app_address",$where,"date ASC",0);
		 
		if($sales_routes)
		{
			while($route=mysqli_fetch_assoc($sales_routes))
			{ 
				$status = "";
				$name = $this->db->rp_getValue("sales_executive","name","id = '".$route['sales_executive_id']."'",0);
				$result[]=array("lat"=>$route['latitude'],"lng"=>$route['longitude'],"name"=>$name,"date"=>date("d M H:i",strtotime($route['date'])),"type"=>$this->db->common_type[$route['type']],"type_slug"=>$route['type'],"icon"=>$this->pin_icon[$route['type']],"status"=>$status,"mytype"=>$route['type'],"address"=>$route['app_address']);
			}
			$reply=array("ack"=>1,"ack_msg"=>"Sales Tracking Fetched!!","result"=>$result);
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Route Found!!");
		}
		return $reply;
	}
	
	function trackSalesAll($date="",$id="")
	{
		$where="isDelete=0";
		if($id!="")
		{
			$where .=" AND sales_executive_id=".$id.""; 
			$limit = "1";
		}
		if($date!="")
		{
			$where.=" AND DATE(date)='".date("Y-m-d",strtotime($date))."'";
			$limit = "1";
		}
		 
		$sales_routes=$this->db->rp_getData($this->ctableTracking,"sales_executive_id,latitude,longitude,date,type,app_address",$where,"date DESC",0,$limit);
		 
		while($route=mysqli_fetch_assoc($sales_routes))
		{ 
			$status = ""; 
			$address=$route['app_address']; 
			$name = $this->db->rp_getValue("sales_executive","name","id = '".$route['sales_executive_id']."'",0);
			$result[]=array("lat"=>$route['latitude'],"lng"=>$route['longitude'],"name"=>$name,"date"=>date("d M h:i A",strtotime($route['date'])),"type"=>"","address"=>$route['app_address'],"type_slug"=>$route['type'],"icon"=>$this->pin_icon[$route['type']],"status"=>$status); 
		}
		if($result == "")
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Data Available!!","result"=>$result);
		}
		else
		{
			$reply=array("ack"=>1,"ack_msg"=>"Sales Tracking Fetched!!","result"=>$result);
		}     
		return $reply;
	}
	
	
	function addNoOrderInquiry($data,$file){

		if(!empty($data))
		{		
			/*$Check_Customer = $this->db->rp_getTotalRecord("executive","phone='".$data['mobile_number']."' AND isDelete=0 AND isActive=1",0);

			if($Check_Customer>0)
			{
				$reply=array( "ack"=>0,"ack_msg"=>"This Number Already Add In Customer List.Please Take Visit.","developer_msg"=>"This Number Already Add In Customer List.Please Take Visit.");
				return $reply;
			}
			else
			{*/
				/*$dup_where = "mobile_number = '".$data['mobile_number']."' AND isDelete=0";
				$r = $this->db->rp_dupCheck($this->ctableNoOrderInquiry,$dup_where,0);
				if($r){
					$reply=array("ack"=>0,"developer_msg"=>"Already Exist This Mobile Number","ack_msg"=>"Already Exist This Mobile Number");
					return $reply;
				}
				else
				{*/


					/*if($data['dealer_id']=="")
					{
						$add_rows = array("type_of_executive","company_name","cname","email","phone","mobile_no1","address","country","state","city","whatsapp_no");
						$add_values = array($data['executive_type'],$data['company_name'],$data['person_name'],$data['email_address'],$data['mobile_number'],$data['other_mobile_no'],$data['address'],$data['country'],$data['state'],$data['city'],$data['other_mobile_no']);
						$InsretId = $this->db->rp_insert("executive",$add_values,$add_rows,0);

						if($InsretId)
						{
							require_once("class.executive.php");
							$objClass= new Executive();
							$objClass->CreateCustomerAccount($InsretId);
						}
						
						$dealer_id = $InsretId;
						
							$mapping_id=$this->db->rp_insert("executive_map_area",array($InsretId,$data['executive_type'],$data['class_id'],$data['area_id']),array("executive_id","executive_type","class_id","area_id"),0);
						
					}
					else
					{
						$data['dealer_id'] = $dealer_id;
					}*/

					$data['modify_date']=date("Y-m-d H:i:s");
					$data['modify_track']=date("Y-m-d H:i:s");
					$columns=array_keys($data);
					$data_values=array_values($data);

					$inq_no     = $this->db->getLastInsertId("no_order_inquiry");
					$sales_name = $this->db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_executive_id']."'",0);
					$module_name = "Inquiry";
					$flag = "Application";
					$log_description = $module_name." #INQ/".$inq_no." Created By ".$sales_name." ON ".date("Y-m-d H:i:s");

					$id=$this->db->rp_insert($this->ctableNoOrderInquiry,$data_values,$columns,0,$log_description,$flag,$module_name,$_REQUEST['sales_executive_id']);

					$inquiry_assign_to=$data['inquiry_assign_to'];
					$dealer_id=($data['dealer_id'])?$data['dealer_id']:"";
					//	echo $inquiry_assign_to;exit;
					if($inquiry_assign_to!="" && $inquiry_assign_to!=0)
				 	{
				 		/*auto assign inquiry*/
				 		// $sales_id = $this->db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND area_id='".$area_id."' AND executive_type!='sales_manager' AND isDelete=0",0);
				 		$sales_id = $this->db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND (city_id='".$city_id."' OR area_id='".$area_id."') AND isDelete=0",0);

				 		if($inquiry_assign_to=="")
						{
					 		$inquiry_assign_to =  ($sales_id!="" || $sales_id!=0)?$sales_id:0;
					 	}
						
						if($inquiry_created_by=="")
						{
							if(isset($_SESSION[SITE_SESS.'REFERANCE_TYPE']) && isset($_SESSION[SITE_SESS.'REFERANCE_ID']) && $_SESSION[SITE_SESS.'REFERANCE_TYPE']==2 && $_SESSION[SITE_SESS.'REFERANCE_ID']!=0)
							{
								$inquiry_created_by = $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$sales_executive_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
							}
							else
							{
								$inquiry_created_by = ($sales_id!="" || $sales_id!=0)?$sales_id:0;
								$sales_executive_id = ($sales_id!="" || $sales_id!=0)?$sales_id:0;
							}
						}
						else
						{
							$sales_executive_id = $inquiry_created_by;
						}

						// $inquiry_created_by = ($sales_id!="" || $sales_id!=0)?$sales_id:4;


						// $update = $this->db->rp_update("no_order_inquiry",array("inquiry_assign_to"=>$inquiry_assign_to,"inquiry_created_by"=>$inquiry_created_by,"sales_executive_id"=>$sales_executive_id),"id='".$uid."'");
						$update = $this->db->rp_update("no_order_inquiry",array("inquiry_assign_to"=>$inquiry_assign_to,"sales_executive_id"=>$sales_executive_id),"id='".$id."'",0);
						/*auto assign inquiry*/
					}
					if($inquiry_assign_to!="" && $inquiry_assign_to!=0)
					{

						$inquiry_assign_name = $this->db->rp_getValue("sales_executive","name","id='".$inquiry_assign_to."'",0);

						if($data['inquiry_lead_flag']=="-1")
						{	$type="prospect";
							$title="Prospect Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
							$Prospect_no     = $this->db->getLastInsertId("no_order_inquiry");			
							$body = " #INQ/".$Prospect_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
								$click_action="no_order_inquiry_grid.php?type=-1";
						}
						else if($data['inquiry_lead_flag']=="0")
						{
							$type="inquiry";
							$title="Inquiry Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
							$inq_no     = $this->db->getLastInsertId("no_order_inquiry");		
							$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
								$click_action="no_order_inquiry_grid.php?type=0";
						}
						else if($data['inquiry_lead_flag']=="1"){
							$type="lead";
							$title="Lead Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
							$inq_no     = $this->db->getLastInsertId("no_order_inquiry");				
							$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
							$click_action="no_order_inquiry_grid.php?type=1";
						}
						//echo "dfh44g44445r";exit();
						/*send Notification*/
						// $Data = [
					 //            'title' => $title,
					 //            'body' =>  $body,
					 //            'icon' => NOTIFICATIONICON,
					 //            'image' => NOTIFICATIONIMAGE,
					 //            'click_action'=> SITEURL.$click_action,
						//     	];
					 //    	$ReferanceArray = 
					 //    		[
					 //            'reference_id' => 	$id,
					 //            'reference_table' => "no_order_inquiry",
						// 		];

						// 		$msg = array(
						// 			"type"		     => $type,
						// 			"title"		     => $title,
						// 			"description"    => $body,
						// 			"user_id"        => $inquiry_assign_to,
						// 			"reference_id"   => $id,
						// 			"item_id"        => $id,
						// 			"reference_type" => 'no_order_inquiry',
						// 		);
					 //    	$user_id = $inquiry_assign_to;
					 //    	if($user_id!="")
						// 	    {
						// 		    /*panel*/
						// 			$this->db->send_notificationpanel($Data,$user_id,$ReferanceArray);

						// 			/*application*/
						// 			$where="refreshToken!='' AND id='".$inquiry_assign_to."'";
						// 			$refreshTokens[]=$this->db->rp_getValue("sales_executive","refreshToken",$where,0);

						// 			$this->db->send_notificationApplication($msg,$refreshTokens,1);  
						// 	    }
						/*send Notification*/
					}
					

					// print_r($_REQUEST);exit;
					if($_REQUEST['application_type']=="1"){
					$image_path=array();

					if (isset($_POST['image_path'])) 
					{	
						// echo "string1";exit;
						$allowedExts = array("jpg","jpeg","png","JPG","JPEG");
						//$doc = ['zip', 'rar', 'pdf', 'doc', 'docx', 'xls','xlsx','ppt','pptx'];
						$whitelistExt = array_merge($img);
						//print_r($_POST['attachment']);
						$a=$_POST['image_path'];
						$a = json_decode($a, TRUE);

							for($i=0; $i<sizeof($a); $i++){
							    $fn = $a[$i]['fileName'];
							    $ext = pathinfo($fn, PATHINFO_EXTENSION);
							    if(!in_array($extension, $allowedExts)){
								$error .= "Extension not allowed. ";
								}
							    $f = base64_decode( $a[$i]['encoded']);
							   	$extension = end(explode(".", $fn));
							   	$attachment="../resource/image/";
								//$fileName	 = 'image_path'.substr(sha1(time()), 0, 6).".".$extension;
							    file_put_contents($attachment.$fn, $f);

							    	$MediaTitle=$fn;
							    	$MediaOrignalTitle=$fn;
							    	$MediaFileName=$fn;
							    	$UploadDate=date("Y-m-d H:i:s");
					    			$ri = $id;
									$rt = "no_order_inquiry";
									$tc = "no_order_inquiry";
									$rc = "id";

									$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
						// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
						$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
						$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);

							$image_path[] = $MediaID;
						}
					}
					if($image_path!=null)
					{
						$image_path1 = implode(",", $image_path);
					}else
					{
						$image_path1="";
					}

					//$image_path = implode(",", $image_path);
					$upadateid = $this->db->rp_update($this->ctableNoOrderInquiry,array("image_path"=>$image_path1),"id='".$id."'",0);
			
					}else{
					
						$image_path=array();
						if (isset($file["image_path"]) && $file["image_path"]['size']!=0)
						{
							$ri = $id;
							$rt = "inquiry";
							$tc = "inquiry";
							$rc = "id";

							for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
							{
								$file_name = $file['image_path']['name'][$i];
								$file_size = $file['image_path']['size'][$i];
								$file_tmp = $file['image_path']['tmp_name'][$i];
								$file_type = $file['image_path']['type'][$i];
								$extension=explode(".",$file_name);

								$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
								$extension=$extension[sizeof($extension)-1];
								if(!in_array($extension,$allowed_extentions))
								{
									$file_error=true;
								}
								$orignal_file_name=$extension[0];
								if(in_array($extension,$allowed_extentions))
								{
									$attachment="../resource/image/";
									move_uploaded_file($file_tmp,$attachment.$file_name);
								}
								$MediaTitle=$file_name;
						    	$MediaOrignalTitle=$file_name;
						    	$MediaFileName=$file_name;
						    	$UploadDate=date("Y-m-d H:i:s");

						    	$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
						    	$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
						    	$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);
						    	$image_path[] = $MediaID;
							}
							$image_path = implode(",", $image_path);
							$upadateid = $this->db->rp_update($this->ctableNoOrderInquiry,array("image_path"=>$image_path),"id='".$id."'",0);
						}
					}
				//}
			//}

			if($id!=0)
			{
				$status_t = 0; 
				if($_REQUEST['followup_date']!="")
				{
					$followup_date = date("Y-m-d H:i:s",strtotime($_REQUEST['followup_date']));
					$followup_description = $_REQUEST['followup_detail'];
				    
				    $Values=array('no_order_inquiry',$data['sales_executive_id'],$dealer_id,$id,0,$followup_description,1,$followup_date,0,1,0,0,2,$inquiry_assign_to,$inquiry_created_by);

			        $Columns=array("reference_table","user_id","visitor_id","reference_id","project_manager_id","description","through","followup_date","isDelete","isActive","next_followup_id","refrence_media_id","entry_type","inquiry_assign_to","inquiry_created_by");

			        $ContentID=$this->db->rp_insert("followup",$Values,$Columns,0);
			        $upadateid = $this->db->rp_update($this->ctableNoOrderInquiry,array("status"=>1),"id='".$id."'",0);
			        $status_t = 1;
				}
				/* Status Time Line Logic Added Dinesh */
				$this->db->addStatusTimelineEntry($id,$status_t,$data['sales_executive_id']);
				/* Status Time Line Logic Added Dinesh */
				$reply=array("ack"=>1,"developer_msg"=>"Inquiry Successfully Submitted","ack_msg"=>"Inquiry Successfully Submitted","inserted_id"=>$id);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"No data inserted check database","ack_msg"=>"Internal Error!!ADDNOI1");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"No data found","ack_msg"=>"Internal Error!!ADDNOI1");
			return $reply;
		}

	}
	
	function updateNoOrderInquiry($data,$id,$type)
	{
		if(!empty($data))
		{
			if($type==0)
			{
				$sales_name = $this->db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_executive_id']."'",0);
				$module_name = "Inquiry";
				$flag = "Application";
				$log_description = $module_name." #INQ/".$id." Edited By ".$sales_name." ON ".date("Y-m-d H:i:s");
			}
			else
			{
				$sales_name = $this->db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_executive_id']."'",0);
				$module_name = "Lead";
				$flag = "Application";
				$log_description = $module_name." #INQ/".$id." Edited By ".$sales_name." ON ".date("Y-m-d H:i:s");
			}
			$last_track=$this->db->rp_getValue($this->ctableNoOrderInquiry,"modify_track","id='".$id."'");
			$data['modify_date']=date("Y-m-d H:i:s");
			$data['modify_track']=$last_track."&5895;".date("Y-m-d H:i:s");

			$UPDATE_array = $data;

			$this->db->rp_update($this->ctableNoOrderInquiry,$UPDATE_array,"id='".$id."'",0,$log_description,$flag,$module_name,$_REQUEST['sales_executive_id']);


			$inquiry_assign_to=$data['inquiry_assign_to'];
		//	echo $inquiry_assign_to;exit;
					if($inquiry_assign_to!="" && $inquiry_assign_to!=0)
					{

						$inquiry_assign_name = $this->db->rp_getValue("sales_executive","name","id='".$inquiry_assign_to."'",0);

						if($data['inquiry_lead_flag']=="-1")
						{	$type="prospect";
							$title="Prospect Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
							$Prospect_no     = $this->db->getLastInsertId("no_order_inquiry");			
							$body = " #INQ/".$Prospect_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
								$click_action="no_order_inquiry_grid.php?type=-1";
						}
						else if($data['inquiry_lead_flag']=="0")
						{
							$type="inquiry";
							$title="Inquiry Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
							$inq_no     = $this->db->getLastInsertId("no_order_inquiry");		
							$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
								$click_action="no_order_inquiry_grid.php?type=0";
						}
						else if($data['inquiry_lead_flag']=="1"){
							$type="lead";
							$title="Lead Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
							$inq_no     = $this->db->getLastInsertId("no_order_inquiry");				
							$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
							$click_action="no_order_inquiry_grid.php?type=1";

						}

						/*send Notification*/
						// $Data = [
					 //            'title' => $title,
					 //            'body' =>  $body,
					 //            'icon' => NOTIFICATIONICON,
					 //            'image' => NOTIFICATIONIMAGE,
					 //            'click_action'=> SITEURL.$click_action,
						//     	];
					 //    	$ReferanceArray = 
					 //    		[
					 //            'reference_id' => 	$id,
					 //            'reference_table' => "no_order_inquiry",
						// 		];

						// 		$msg = array(
						// 			"type"		     => $type,
						// 			"title"		     => $title,
						// 			"description"    => $body,
						// 			"user_id"        => $inquiry_assign_to,
						// 			"reference_id"   => $id,
						// 			"item_id"        => $id,
						// 			"reference_type" => 'no_order_inquiry',
						// 		);
					 //    	$id = $inquiry_assign_to;
					 //    	if($id!="")
						// 	    {
						// 		    /*panel*/
						// 			$this->db->send_notificationpanel($Data,$id,$ReferanceArray);

						// 			/*application*/
						// 			$where="refreshToken!='' AND id='".$inquiry_assign_to."'";
						// 			$refreshTokens[]=$this->db->rp_getValue("sales_executive","refreshToken",$where,0);

						// 			$this->db->send_notificationApplication($msg,$refreshTokens,1);  
						// 	    }
						/*send Notification*/
					}

					
			$reply=array("ack"=>1,"developer_msg"=>"Inquiry Successfully Updated","ack_msg"=>"Data Successfully Updated","inserted_id"=>$id);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"No data found","ack_msg"=>"Internal Error!! ERROR UPNOI1");
			return $reply;
		}
	}
	
	function deleteNoOrderInquiry($id)
	{
		$last_track=$db->rp_getValue($this->ctableNoOrderInquiry,"modify_track","id='".$id."'");
		$data['modify_date']=date("Y-m-d H:i:s");
		$data['modify_track']=$last_track."&5895;".date("Y-m-d H:i:s");
		$data['isDelete']=0;
		$this->db->rp_update($this->ctableNoOrderInquiry,$data,"id='".$id."'");
		$reply=array("ack"=>1,"developer_msg"=>"Inquiry Successfully Deleted","ack_msg"=>"Inquiry Successfully Deleted");
		return $reply;
	}
	
	function listNoOrderInquiry($id)
	{
		if($id!="")
		{
			$noOrderInquiryR=$db->rp_getData($this->ctableNoOrderInquiry,"*","id='".$id."'");
			if($noOrderInquiryR)
			{
				$result=array();
				while($noOrderInquiry=mysqli_fetch_assoc($noOrderInquiryR))
				{
					$result[]=$noOrderInquiry;
				}
				$reply=array("ack"=>1,"developer_msg"=>"Inquiry Get Successfully Order Inquiry","ack_msg"=>"Inquiry Successfully Fetched","result"=>$result);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"No Inquiry Found!!","ack_msg"=>"No Inquiry Found!!");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"No data found","ack_msg"=>"Internal Error!! ERROR LISTNOI1");
			return $reply;
		}
	}
	public function DownloadOrder($order_id)
	{
		$order_id=$this->db->rp_getValue("orders","id","id='".$order_id."'",0);
		$uname=$this->db->rp_getValue("orders","customer_name","id='".$order_id."'",0);
		$uname=$this->db->rp_createSlug($uname);
		$lr_image=$this->db->rp_getValue("orders","lr_image","id='".$order_id."'",0);
		$company_name=$this->db->rp_getValue("orders","company_name","id='".$order_id."'",0);
		//$company_name=$this->db->rp_createSlug($company_name);
		$city=$this->db->rp_getValue("orders","city","id='".$order_id."'",0);
		//$city=$this->db->rp_createSlug($city);

		if($lr_image!="")
    	{
    		$file_path = SITEURL.LRCOPY.$lr_image;
		}
		else
		{
			$file_path = "";
		}
		
		if($order_id){
			
			$count=$this->db->rp_getTotalRecord("orders","id='".$order_id."'",0);
			
			if($count >0){
				$body_url=ADMINSITEURL_STATIC."bbsales_tracking/order_view_download_1.php?order_id=".$order_id; 
				$d=file_get_contents($body_url);
				//print_r($d); exit;
				$d = html_entity_decode($d);
				$relCertFileNames = array();
				$merge_file = array();
				require('../bbsales_tracking/mpdf60/mpdf.php');
				$mpdf = new mPDF('', // mode - default ''

				'A4', // format - A4, for example, default ''

				10,     // font size - default 0

				'sans-serif',  // default font family

				1,    // margin_left

				1,    // margin right

				10,   // margin top

				5,    // margin bottom

				0,    // margin header

				0,    // margin footer

				'P'); // L - landscape, P - portrait

				/*$mpdf->use_kwt = true;*/
		
				/*$mpdf->autoPageBreak = false;*/
				$mpdf->autoScriptToLang = true;
				$mpdf->baseScript = 1; // Use Gujarati script
				$mpdf->autoLangToFont = true;
				// $mpdf->showImageErrors = true;
				$mpdf->WriteHTML($d);

  				/*log entry*/
				/*$sales_id = $this->db->rp_getValue("orders","sales_id","id='".$order_id."'",0);
				$sales_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_id."'",0);
				$customer_id = $this->db->rp_getValue("orders","customer_id","id='".$order_id."'");
				$order_no = $this->db->rp_getValue("orders","order_no","id='".$order_id."'");

				$last_id = $order_id;
				$flag = "Application";
				$ctable = "orders";
				$module_name = "Orders";
				$log_description = $module_name." ".$order_no." PDF Download By ".$sales_name." ON ".date("Y-m-d H:i:s");
				$this->db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,$sales_id,$customer_id);*/
				/*log entry*/

				//$fileName = "orders".$order_id;
				$date=date("d-m-Y");
				$fileName = $date."-".$this->rp_createSlug($company_name)."-".$this->rp_createSlug($city);
				//$fileName = $date."-".$uname."-".$order_id;

				// if(!is_dir("../bbsales_tracking/pdf/orders/".$fileName)){

					// mkdir("../bbsales_tracking/pdf/orders/".$fileName);
				// }

				$pdf_file_path	= "../bbsales_tracking/pdf/orders/".$fileName.'.pdf';
				if(file_exists($pdf_file_path)){

					unlink($pdf_file_path);
				}

				$mpdf->Output($pdf_file_path);
				$pdf_file_path;

				$result=array();
				$result['pdf']=ADMINSITEURL."pdf/orders/".$fileName.'.pdf';
				$result['lr_image']=$file_path;
				// $result['fileName']=$fileName.'.pdf';

				$result['pdf']=ADMINSITEURL."pdf/orders/".$fileName.'.pdf';

				$pdf_attachment_id = $this->db->rp_getValue("orders","pdf_attachment","isDelete=0 AND id='".$order_id."' ",0);
				
				$pdf_attach_r = $this->db->rp_getData("media","url","reference_id='".$order_id."' AND id IN(".$pdf_attachment_id.") ","",0);
				
				$result['pdf_attachment'] = array();

				while ($pdf_attach_d = mysqli_fetch_assoc($pdf_attach_r))
				{
					$result['pdf_attachment'][] = ADMINSITEURL.'order_documents/'.$pdf_attach_d['url'];
				}	

				$reply=array("ack"=>1,"developer_msg"=>"Order Generate Successfully","ack_msg"=>"Order Generate Successfully","result"=>$result);
				return $reply;
			}
			else{
				$reply=array("ack"=>0,"developer_msg"=>"Order Not Generate!!","ack_msg"=>"Order Not Generate!!");
				return $reply;
			}
		}
		else{
			$reply=array("ack"=>0,"developer_msg"=>"Order Id Require!!","ack_msg"=>"Order Id Require!!");
			return $reply;
		}
	}
	
	public function UpdateSalesExecutiveProfile($detail,$file) 
    {
        extract($detail);
	 	
		$adate	= date('Y-m-d H:i:s');
		$rows 	= array(
				"name"    => $name,
				"email"   => $email,
				"address" => $address,
				"country" => $country,
				"state"   => $state,
				"city"    => $city,
			);	
					
		$uid = $this->db->rp_update("sales_executive",$rows,"id='".$id."'",0);
		/*add image*/
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $id;
				$rt = "sales_executive";
				$tc = "sales_executive";
				$rc = "id";
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					//print_r($file["image_path"]);
					$file_name = $file['image_path']['name'];
					$file_size = $file['image_path']['size'];
					$file_tmp = $file['image_path']['tmp_name'];
					$file_type = $file['image_path']['type'];
					$extension=explode(".",$file_name);
					
					$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
					$extension=$extension[sizeof($extension)-1];
					if(!in_array($extension,$allowed_extentions))
					{
						$file_error=true;
					}
					$orignal_file_name=$extension[0];
					if(in_array($extension,$allowed_extentions))
					{
						$file_name = $orignal_file_name.substr(sha1(time()).rand(), 0, 6) . "." . $extension;
						$attachment="../resource/image/";
						move_uploaded_file($file_tmp,$attachment.$file_name);
					}
					$MediaTitle=$file_name;
			    	$MediaOrignalTitle=$file_name;

					$MediaFileName=$file_name;
					// $MediaType=User::$ValidMediaType[$extension];
					$UploadDate=date("Y-m-d H:i:s");
					
					// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
					$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
					// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
					$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
					$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);

					$image_path[] = $MediaID;
				}
				$image_path = implode(",", $image_path);
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$id."'",0);
			}
		/*add image*/

		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Update Successfully","ack_msg"=>"Success! Update Customer Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Update Record Failed.");
			return $reply;
		}
	}
	function find_total_distance_of_sales_executive($sales_id,$date)
	{
		$distance_where ="isDelete=0 AND sales_executive_id='".$sales_id."' AND DATE(date)='".date('Y-m-d',strtotime($date))."'";
		
		// april month tbale chagne by ravi
			if(date("m",strtotime($date))=="04")
			{
			    $tableTracking="salesexecutive_tracking_april";
			}
			else
			{
				$tableTracking="salesexecutive_tracking";
			}

		
		$totData=$this->db->rp_getTotalRecord($tableTracking,$distance_where,0);
		// echo $totData;
		$tracking_r= $this->db->rp_getData($tableTracking,"*",$distance_where,"",0);
		$tot_distace=0;
		$cnt=0;
		while($tracking_d=mysqli_fetch_assoc($tracking_r)){ 
		  $cnt++;

		  if($totData!=$cnt)
		  {
		    $first_lat=$tracking_d['latitude'];
		    $first_long=$tracking_d['longitude'];
		    $next_id=$tracking_d['date'];
		    $second_lat=$this->db->rp_getValue($tableTracking,"latitude",$distance_where." AND date>'".$next_id."' LIMIT 1",0);
		    //echo $second_lat; echo "<br>";
		    $second_long=$this->db->rp_getValue($tableTracking,"longitude",$distance_where." AND date>'".$next_id."' LIMIT 1"); 
		    //echo $second_long;
		
		    $distance = $this->find_distance_from_lat_long($first_lat,$first_long,$second_lat,$second_long,"K");
		  // echo "<br/>distance=".$distance;
		    $tot_distace+=$distance;
		  }  
		} 
		
		return round($tot_distace,4);
	}

	function find_distance_from_lat_long($lat1, $lon1, $lat2, $lon2, $unit) {
	  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
	    return 0;
	  }
	  else {
	    $theta = $lon1 - $lon2;
	    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	    $dist = acos($dist);
	    $dist = rad2deg($dist);
	    $miles = $dist * 60 * 1.1515;
	    $unit = strtoupper($unit);

	    if ($unit == "K") {
	      return ($miles * 1.609344);
	    } else if ($unit == "N") {
	      return ($miles * 0.8684);
	    } else {
	      return $miles;
	    }
	  }
	} 
} 
?>