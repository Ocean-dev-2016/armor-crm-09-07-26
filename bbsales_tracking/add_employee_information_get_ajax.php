<?php

	$page_id=658;$page_slug='sales_executive_info_form';
	include("connect.php");
	$detail = array();
	// echo "<pre>";
	// print_r($_FILES);
	// echo "<hr>";
	// print_r($_REQUEST);die;
	$detail['post_applied']	        	= $_REQUEST['post_applied'] != "" ? $db->clean($_REQUEST['post_applied']) : "";
	$detail['reference']	        	= $_REQUEST['reference'] != "" ? $db->clean($_REQUEST['reference']) : "";
	$detail['first_name']	        	= $_REQUEST['first_name'] != "" ? $db->clean($_REQUEST['first_name']) : "";
	$detail['middle_name']	        	= $_REQUEST['middle_name'] != "" ? $db->clean($_REQUEST['middle_name']) : "";
	$detail['surname']	        		= $_REQUEST['surname'] != "" ? $db->clean($_REQUEST['surname']) : "";
	$detail['gender']	        		= $_REQUEST['gender'] != "" ? $db->clean($_REQUEST['gender']) : "";
	$detail['religion']	        		= $_REQUEST['religion'] != "" ? $db->clean($_REQUEST['religion']) : "";
	$detail['cast']	        			= $_REQUEST['cast'] != "" ? $db->clean($_REQUEST['cast']) : "";
	$detail['mother_tongue']	    	= $_REQUEST['mother_tongue'] != "" ? $db->clean($_REQUEST['mother_tongue']) : "";
	$detail['marital_status']	    	= $_REQUEST['marital_status'] != "" ? $db->clean($_REQUEST['marital_status']) : "";
	$detail['plaece_of_birth']	    	= $_REQUEST['plaece_of_birth'] != "" ? $db->clean($_REQUEST['plaece_of_birth']) : "";
	$detail['present_address']	    	= $_REQUEST['present_address'] != "" ? $db->clean($_REQUEST['present_address']) : "";
	$detail['permanent_address']		= $_REQUEST['permanent_address'] != "" ? $db->clean($_REQUEST['permanent_address']) : "";
	$detail['contact_no']	        	= $_REQUEST['contact_no'] != "" ? $db->clean($_REQUEST['contact_no']) : "";
	$detail['emergency_contact_person']	= $_REQUEST['emergency_contact_person'] != "" ? $db->clean($_REQUEST['emergency_contact_person']) : "";
	$detail['contact_person_relation']	= $_REQUEST['contact_person_relation'] != "" ? $db->clean($_REQUEST['contact_person_relation']) : "";
	$detail['blood_group']	        	= $_REQUEST['blood_group'] != "" ? $db->clean($_REQUEST['blood_group']) : "";
	$detail['email']	        		= $_REQUEST['email'] != "" ? $db->clean($_REQUEST['email']) : "";
	$detail['type_of_vehicle']	        = $_REQUEST['type_of_vehicle'] != "" ? $db->clean($_REQUEST['type_of_vehicle']) : "";
	$detail['vehicle_model_no']	        = $_REQUEST['vehicle_model_no'] != "" ? $db->clean($_REQUEST['vehicle_model_no']) : "";
	$detail['physical_disability']	    = $_REQUEST['physical_disability'] != "" ? $db->clean($_REQUEST['physical_disability']) : "";
	$detail['major_illness']	        = $_REQUEST['major_illness'] != "" ? $db->clean($_REQUEST['major_illness']) : "";
	$detail['rp1_name']	        		= $_REQUEST['rp1_name'] != "" ? $db->clean($_REQUEST['rp1_name']) : "";
	$detail['rp1_relation']	        	= $_REQUEST['rp1_relation'] != "" ? $db->clean($_REQUEST['rp1_relation']) : "";
	$detail['rp1_occupation']	        = $_REQUEST['rp1_occupation'] != "" ? $db->clean($_REQUEST['rp1_occupation']) : "";
	$detail['rp1_contact_no']	        = $_REQUEST['rp1_contact_no'] != "" ? $db->clean($_REQUEST['rp1_contact_no']) : "";
	$detail['rp2_name']	       			= $_REQUEST['rp2_name'] != "" ? $db->clean($_REQUEST['rp2_name']) : "";
	$detail['rp2_relation']	        	= $_REQUEST['rp2_relation'] != "" ? $db->clean($_REQUEST['rp2_relation']) : "";
	$detail['rp2_occupation']	        = $_REQUEST['rp2_occupation'] != "" ? $db->clean($_REQUEST['rp2_occupation']) : "";
	$detail['rp2_contact_no']	        = $_REQUEST['rp2_contact_no'] != "" ? $db->clean($_REQUEST['rp2_contact_no']) : "";
	$detail['date']           			= $_REQUEST['date'] != "" ? date("Y-m-d",strtotime($_REQUEST['date'])) : "";
	$detail['birth_date']           	= $_REQUEST['birth_date'] != "" ? date("Y-m-d", strtotime($_REQUEST['birth_date'])) : "";

	$values = array_values($detail);
	$keys = array_keys($detail);
	$add_data = $db->rp_insert("sales_executive_information",$values,$keys,0);
	// print_r($add_data);
	if($add_data && $add_data > 0){
		if(sizeof($_FILES['image_path']['name']) > 0){
			$ri = $add_data;
			$rt = "sales_executive_information";
			$tc = "image_path";
			$rc = "id";
			// $file = $_FILES['image_path'];
			for($i=0;$i<sizeof($_FILES["image_path"]['name']);$i++)
			{
				$file_name = $_FILES['image_path']['name'][$i];
				$file_size = $_FILES['image_path']['size'][$i];
				$file_tmp = $_FILES['image_path']['tmp_name'][$i];
				$file_type = $_FILES['image_path']['type'][$i];
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
					$attachment="../resource/image/employee_information/";
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
				$MediaID=$db->rp_insert("media",$Values,$Columns,0);

				$image_path[] = $MediaID;
			}
			// print_r($image_path);die;
			$image_path = implode(",", $image_path);
			$upadateid = $db->rp_update("sales_executive_information",array("image_path"=>$image_path),"id='".$add_data."'",0);
			if($upadateid){
				$reply=array("ack"=>1,"ack_msg"=>"Data Add Successfully!!!","dmg"=>"Data Add Successfully!!!","data_id"=>$add_data);
			}else{
				$reply=array("ack"=>0,"ack_msg"=>"Error!!!");
			}
		}
	}else{
		$reply=array("ack"=>0,"ack_msg"=>"Error!!!");
	}

		echo json_encode($reply);

?>