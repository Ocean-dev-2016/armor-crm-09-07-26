<?php

	$page_id=658;$page_slug='sales_executive_info_form';
	include("connect.php");
	$mode = $_REQUEST['mode'];
	$sales_executive_id = $_REQUEST['sales_executive_id'];

// ---------------------------------- For Work Experience----------------------

	if ($mode == "add_pre_joining_connections_details") {
		// echo $_REQUEST['pjcd_employee_name'];die();
		$employee_name 		= $_REQUEST['pjcd_employee_name'] != "" ? $_REQUEST['pjcd_employee_name'] : "";
		$designation 			= $_REQUEST['pjcd_designation'] != "" ? $_REQUEST['pjcd_designation'] : "";
		$department 	= $_REQUEST['pjcd_department'] != "" ? $_REQUEST['pjcd_department'] : "";
		$relatioship 		= $_REQUEST['pjcd_relatioship'] != "" ? $_REQUEST['pjcd_relatioship'] : "";
		$insert = $db->rp_insert("sal_person_info_vs_pre_joining_connections_details",array($employee_name,$designation,$department,$relatioship,$sales_executive_id),array("employee_name","designation","department","relationship","sales_person_id"),0);
		if($insert)
		{
			
			$ack=array("ack"=>1,"ack_msg"=>"Pre Joining Connections Details Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Pre Joining Connections Details Added Failed");
		}
		echo json_encode($ack);
	}

	if ($mode == "delete_pre_joining_connections_details" && $sales_executive_id != "") {
		$delete=$db->rp_update("sal_person_info_vs_pre_joining_connections_details",array("isDelete"=>1),"id='".$sales_executive_id."'",0);
		if($delete)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Pre Joining Connections Details Delete Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Pre Joining Connections Details Delete Failed");
		}
		echo json_encode($ack);
	}

// ---------------------------------- For Family Background-----------------------

	if ($mode == "add_family_background") {
		// print_r($_REQUEST);die;
		$family_mamber_name 		= $_REQUEST['family_mamber_name'] != "" ? $_REQUEST['family_mamber_name'] : "";
		$family_mamber_relation 	= $_REQUEST['family_mamber_relation'] != "" ? $_REQUEST['family_mamber_relation'] : "";
		$family_mamber_education 	= $_REQUEST['family_mamber_education'] != "" ? $_REQUEST['family_mamber_education'] : "";
		$family_mamber_profession 	= $_REQUEST['family_mamber_profession'] != "" ? $_REQUEST['family_mamber_profession'] : "";
		
		$family_mamber_dob 			= $_REQUEST['family_mamber_dob'] != "" && $_REQUEST['family_mamber_dob'] != "undefined-undefined-" ? date("Y-m-d",strtotime($_REQUEST['family_mamber_dob'])) : "";
			// echo $family_mamber_dob;die();
		$insert = $db->rp_insert("sal_person_info_vs_family_background",array($sales_executive_id,$family_mamber_name,$family_mamber_relation,$family_mamber_education,$family_mamber_profession,$family_mamber_dob),array("sales_person_id","name","relation","education","profession","date_of_birth"),0);
		if($insert)
		{
			
			$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Data Added Failed");
		}
		echo json_encode($ack);
	}

	if ($mode == "delete_family_background" && $sales_executive_id != "") {
		$delete=$db->rp_update("sal_person_info_vs_family_background",array("isDelete"=>1),"id='".$sales_executive_id."'",0);
		if($delete)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
		}
		echo json_encode($ack);
	}

//--------------------------------- For Educational Details ----------------------

	if ($mode == "add_educational_details") {
		$education 			= $_REQUEST['sped_education'] != "" ? $_REQUEST['sped_education'] : "";
		$year 				= $_REQUEST['sped_year'] != "" ? $_REQUEST['sped_year'] : "";
		$percentage 		= $_REQUEST['sped_percentage'] != "" ? $_REQUEST['sped_percentage'] : "";
		$place_name 		= $_REQUEST['sped_place_name'] != "" ? $_REQUEST['sped_place_name'] : "";
		$city 				= $_REQUEST['sped_city'] != "" ? $_REQUEST['sped_city'] : "";
		$special_remarks	= $_REQUEST['sped_special_remarks'] != "" ? $_REQUEST['sped_special_remarks'] : "";
		$insert = $db->rp_insert("sal_person_info_vs_educational_details",array($sales_executive_id,$education,$year,$percentage,$place_name,$city,$special_remarks),array("sales_person_id","education","passing_year","percentage","place_name","city","special_remarks"),0);
		if($insert)
		{
			
			$ack=array("ack"=>1,"ack_msg"=>"Educational Details Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Educational Details Added Failed");
		}
		echo json_encode($ack);
	}

	if ($mode == "delete_educational_details" && $sales_executive_id != "") {
		$delete=$db->rp_update("sal_person_info_vs_educational_details",array("isDelete"=>1),"id='".$sales_executive_id."'",0);
		if($delete)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Educational Details Delete Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Educational Details Delete Failed");
		}
		echo json_encode($ack);
	}

//--------------------------------- For Present Course Learning ----------------------

	if ($mode == "add_present_course_learning") {
		// print_r($_REQUEST);die;
		$course_name 	= $_REQUEST['pcl_course_name'] != "" ? $_REQUEST['pcl_course_name'] : "";
		$institute 		= $_REQUEST['pcl_institute'] != "" ? $_REQUEST['pcl_institute'] : "";
		$city 			= $_REQUEST['pcl_city'] != "" ? $_REQUEST['pcl_city'] : "";
		$start_date 	= $_REQUEST['pcl_start_date'] != "" && $_REQUEST['pcl_start_date'] != "undefined-undefined-" ? date("Y-m-d",strtotime($_REQUEST['pcl_start_date'])) : "";
		$end_date 		= $_REQUEST['pcl_end_date'] != "" && $_REQUEST['pcl_end_date'] != "undefined-undefined-" ? date("Y-m-d",strtotime($_REQUEST['pcl_end_date'])) : "";
		// echo $_REQUEST['pcl_start_date'];die;
		$insert = $db->rp_insert("sal_person_info_vs_present_course_learning",array($sales_executive_id,$course_name,$institute,$city,$start_date,$end_date),array("sales_person_id","name_of_course","institute","city","start_date","end_data"),0);
		if($insert)
		{
			
			$ack=array("ack"=>1,"ack_msg"=>"Present Course Learning Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Present Course Learning Added Failed");
		}
		echo json_encode($ack);
	}

	if ($mode == "delete_present_course_learning" && $sales_executive_id != "") {
		$delete=$db->rp_update("sal_person_info_vs_present_course_learning",array("isDelete"=>1),"id='".$sales_executive_id."'",0);
		if($delete)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Present Course Learning Delete Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Present Course Learning Delete Failed");
		}
		echo json_encode($ack);
	}


//------------------ For Computer Proficiency ----------------------

	if ($mode == "add_computer_proficiency") {
		// echo "<pre>";
		// print_r(sizeof($_REQUEST['cp_language_skills']));die;
		$course 			= $_REQUEST['cp_course'] != "" ? $_REQUEST['cp_course'] : "";
		$rating 			= $_REQUEST['cp_rating'] != "" ? $_REQUEST['cp_rating'] : "";

		$insert = $db->rp_insert("sal_person_info_vs_computer_proficiency",array($sales_executive_id,$course,$rating),array("sales_person_id","course","rating"),0);
		if($insert)
		{
			
			$ack=array("ack"=>1,"ack_msg"=>"Computer Proficiency Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Computer Proficiency Added Failed");
		}
		echo json_encode($ack);
	}

	if ($mode == "delete_computer_proficiency" && $sales_executive_id != "") {
		$delete=$db->rp_update("sal_person_info_vs_computer_proficiency",array("isDelete"=>1),"id='".$sales_executive_id."'",0);
		if($delete)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Computer Proficiency Delete Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Computer Proficiency Delete Failed");
		}
		echo json_encode($ack);
	}

//------------------ For Language Details ----------------------

	if ($mode == "add_language_details") {
		// print_r($_REQUEST);die;
		$language_name		= $_REQUEST['ld_language_name'] != "" ? $_REQUEST['ld_language_name'] : "";
		$language_skills	= sizeof($_REQUEST['ld_language_skills']) > 0 ? implode(",",$_REQUEST['ld_language_skills']) : "";

		$insert = $db->rp_insert("sal_person_info_vs_language_details",array($sales_executive_id,$language_name,$language_skills),array("sales_person_id","language_name","language_skills"),0);
		if($insert)
		{
			
			$ack=array("ack"=>1,"ack_msg"=>"Language Details Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Language Details Added Failed");
		}
		echo json_encode($ack);
	}

	if ($mode == "delete_language_details" && $sales_executive_id != "") {
		$delete=$db->rp_update("sal_person_info_vs_language_details",array("isDelete"=>1),"id='".$sales_executive_id."'",0);
		if($delete)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Language Details Delete Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Language Details Delete Failed");
		}
		echo json_encode($ack);
	}

// ---------------------------------- For Work Experience----------------------

	if ($mode == "add_work_experience") {
		$company_name 		= $_REQUEST['company_name'] != "" ? $_REQUEST['company_name'] : "";
		$location 			= $_REQUEST['location'] != "" ? $_REQUEST['location'] : "";
		$kind_of_business 	= $_REQUEST['kind_of_business'] != "" ? $_REQUEST['kind_of_business'] : "";
		$designation 		= $_REQUEST['designation'] != "" ? $_REQUEST['designation'] : "";
		$from_date = $_REQUEST['from_date'] != "" && $_REQUEST['from_date'] != NULL ? date("Y-m-d", strtotime(str_replace('/', '-', $_REQUEST['from_date']))) : "";
		$to_date = $_REQUEST['to_date'] != "" && $_REQUEST['to_date'] != NULL ? date("Y-m-d", strtotime(str_replace('/', '-', $_REQUEST['to_date']))) : "";
		// var_dump($to_date);die;
		$insert = $db->rp_insert("sal_person_info_vs_work_experience",array($company_name,$location,$kind_of_business,$sales_executive_id,$designation,$from_date,$to_date),array("company_name","location","kind_of_business","sales_person_id","designation","from_date","to_date"),0);
		if($insert)
		{
			
			$ack=array("ack"=>1,"ack_msg"=>"Work Experience Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Work Experience Added Failed");
		}
		echo json_encode($ack);
	}

	if ($mode == "delete_work_experience" && $sales_executive_id != "") {
		$delete=$db->rp_update("sal_person_info_vs_work_experience",array("isDelete"=>1),"id='".$sales_executive_id."'",0);
		if($delete)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Work Experience Delete Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Work Experience Delete Failed");
		}
		echo json_encode($ack);
	}
?>