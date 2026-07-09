<?php
$page_id        = 572;
$ctable         = "customer_inquiry";
include("connect_in.php");
$id=$_REQUEST['inquiry_id'];
$m=$_REQUEST['m'];
$where="id='".$id."'";
if($m=="check_customer")
{	
	$get_customer_type=$db->rp_getValue("no_order_inquiry","executive_type",$where,0);
	$customer=$db->rp_getValue("no_order_inquiry","dealer_id",$where,0);
	$check_customer=$db->rp_getTotalRecord("executive","id='".$customer."'",0);
	if($check_customer>0)
	{
		$replay=array("ack"=>1,"ack_msg"=>"Customer Found");
	}
	else
	{
		$replay=array("ack"=>0,"ack_msg"=>"No such a Customer Found!","ask"=>"Are You want to Create Customer???");
	}
	echo json_encode($replay);
}

else if($m=="create_customer")
{
	$R=$db->rp_getData("no_order_inquiry","*","isDelete=0 AND ".$where,"",0);
	if($D=mysqli_fetch_assoc($R))
	{
		// print_r($D);
		$type_of_executive		= htmlentities($D['executive_type']);;
	    $dealer_distributor_id	= htmlentities($D['dealer_id']);
	    $super_stockist_id		= htmlentities($D['sales_executive_id']);
	    $adate		= date("Y-m-d H:i:s");
	    $company_name=htmlentities($D['company_name']);
	    $cname=htmlentities($D['person_name']);
	    $phone=htmlentities($D['mobile_number']);
	    $email=htmlentities($D['email_address']);
	    $address=htmlentities($D['address']);
	    $country=htmlentities($D['country']);
	    $state=htmlentities($D['state']);
	    $city=htmlentities($D['city']);
	    $zone=htmlentities($D['zone']);
	    $industry_type_id=htmlentities($D['industry_type_id']);
	    $inquiry_assign_to=htmlentities($D['inquiry_assign_to']);
	    $check_dealer=1;
	    
	    /*if($type_of_executive==3)
	    {
	    	if($dealer_distributor_id=="")
	    	{
	    		$check_dealer=0;
	    	}
	    }   */ 
	    
    	/*if($super_stockist_id!="" && $company_name!="" && $cname!="" && $phone!="" && $address!="")*/
    	/*if($company_name!="" && $cname!="" && $phone!="" && $address!="")*/
    	if($company_name!="" && $phone!="")
    	{  
    		$rows=array(
    			"type_of_executive",
    			// "dealer_distributor_id",
    			"super_stockist_id",
    			"adate",
    			"company_name",
    			"cname",
    			"phone",
    			"email",
    			"address",
    			"country",
    			"state",
    			"city",
    			"zone",
    			"industry_type_id",
    			"seid",
    		);
    		$values=array(
    			$type_of_executive,
    			// $dealer_distributor_id,
    			$super_stockist_id,
    			$adate,
    			$company_name,
    			$cname,
    			$phone,
    			$email,
    			$address,
    			$country,
    			$state,
    			$city,
    			$zone,
    			$industry_type_id,
    			$inquiry_assign_to,
    		);

    		$insert_id=$db->rp_insert("executive",$values,$rows,0);

    		if($insert_id>0)
    		{
    			require_once("../include/class.executive.php");
				$objClass= new Executive();
				$objClass->CreateCustomerAccount($insert_id); 

    			$update_inquiry=$db->rp_update("no_order_inquiry",array("dealer_id"=>$insert_id),$where);
    			$replay=array("ack"=>1,"ack_msg"=>"Customer Created Successfully");
    		}
    		else
    		{    			
    			$replay=array("ack"=>0,"ack_msg"=>"Customer Created Failed!! Please Try again");
    		}
    	}
    	else
    	{
		   /* if($check_dealer==0)
		    {
		    	$replay=array("ack"=>0,"ack_msg"=>"Dealer,Name of Company,Contact Person,Contact Number,Address Required.. Please Fill the required Detail");
		    }
		    else
		    {*/
    			$replay=array("ack"=>0,"ack_msg"=>"Name of Company,Contact Person,Contact Number,Address Required.. Please Fill the required Detail");
		    // }
    	}
	    	   
	}
	else
	{
		$replay=array("ack"=>0,"ack_msg"=>"No Such a Inquiry Detail Found!!!");
	}
	echo json_encode($replay);
}

else if($m=="create_customer_inquiry")
{
	$R=$db->rp_getData("customer_inquiry","*","isDelete=0 AND ".$where,"",0);
	if($D=mysqli_fetch_assoc($R))
	{
		// print_r($D);
		$type_of_executive		= htmlentities($D['executive_type']);;
	    $dealer_distributor_id	= htmlentities($D['dealer_id']);
	    $super_stockist_id		= htmlentities($D['sales_executive_id']);
	    $adate		= date("Y-m-d H:i:s");
	    $company_name=htmlentities($D['company_name']);
	    $cname=htmlentities($D['person_name']);
	    $phone=htmlentities($D['mobile_number']);
	    $email=htmlentities($D['email_address']);
	    $address=htmlentities($D['address']);
	    $country=htmlentities($D['country']);
	    $state=htmlentities($D['state']);
	    $city=htmlentities($D['city']);
	    $zone=htmlentities($D['zone']);
	    $check_dealer=1;
	    /*if($type_of_executive==3)
	    {
	    	if($dealer_distributor_id=="")
	    	{
	    		$check_dealer=0;
	    	}
	    }   */ 
	    
    	/*if($super_stockist_id!="" && $company_name!="" && $cname!="" && $phone!="" && $address!="")*/
    	/*if($company_name!="" && $cname!="" && $phone!="" && $address!="")*/
    	if($company_name!="" && $phone!="")
    	{  
    		$rows=array(
    			"type_of_executive",
    			// "dealer_distributor_id",
    			"super_stockist_id",
    			"adate",
    			"company_name",
    			"cname",
    			"phone",
    			"email",
    			"address",
    			"country",
    			"state",
    			"city",
    			"zone",
    			"customer_from",
    		);
    		$values=array(
    			$type_of_executive,
    			// $dealer_distributor_id,
    			$super_stockist_id,
    			$adate,
    			$company_name,
    			$cname,
    			$phone,
    			$email,
    			$address,
    			$country,
    			$state,
    			$city,
    			$zone,
    			1,
    		);

    		$insert_id=$db->rp_insert("executive",$values,$rows,0);

    		if($insert_id>0)
    		{
    			require_once("../include/class.executive.php");
				$objClass= new Executive();
				$objClass->CreateCustomerAccount($insert_id); 

    			$update_inquiry=$db->rp_update("customer_inquiry",array("dealer_id"=>$insert_id),$where);
    			$replay=array("ack"=>1,"ack_msg"=>"Customer Created Successfully");
    		}
    		else
    		{    			
    			$replay=array("ack"=>0,"ack_msg"=>"Customer Created Failed!! Please Try again");
    		}
    	}
    	else
    	{
		   /* if($check_dealer==0)
		    {
		    	$replay=array("ack"=>0,"ack_msg"=>"Dealer,Name of Company,Contact Person,Contact Number,Address Required.. Please Fill the required Detail");
		    }
		    else
		    {*/
    			$replay=array("ack"=>0,"ack_msg"=>"Name of Company,Contact Person,Contact Number,Address Required.. Please Fill the required Detail");
		    // }
    	}
	    	   
	}
	else
	{
		$replay=array("ack"=>0,"ack_msg"=>"No Such a Inquiry Detail Found!!!");
	}
	echo json_encode($replay);
}

else if($m=="change_to_inquiry")
{	
	$IsInquiry = $db->rp_getTotalRecord("no_order_inquiry","id='".$_REQUEST['inquiry_id']."' AND inquiry_lead_flag='-1' AND isDelete=0",0);
	if($IsInquiry>0)
	{
		$inquiry_assign_to = $db->rp_getValue("no_order_inquiry","inquiry_assign_to","id='".$_REQUEST['inquiry_id']."' AND isDelete=0",0);
		if($inquiry_assign_to == "")
		{
			/*auto assign inquiry*/
			$class_id =  $db->rp_getValue("no_order_inquiry","class_id","id='".$_REQUEST['inquiry_id']."' AND isDelete=0",0);

			$area_id  = $db->rp_getValue("no_order_inquiry","area_id","id='".$_REQUEST['inquiry_id']."' AND isDelete=0",0);

			//$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND area_id='".$area_id."' AND executive_type!='sales_manager' AND isDelete=0",0);
			
			$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND executive_type!='sales_manager' AND isDelete=0",0);
			$raw_created_by = $db->rp_getValue("no_order_inquiry","inquiry_created_by","isDelete=0 AND isActive=1 AND id= '".$_REQUEST['inquiry_id']."' ");
			if ($raw_created_by == null || $raw_created_by == NULL || $raw_created_by == "" || empty($raw_created_by) || $raw_created_by == 0) 
			{
				$inquiry_created_by =  $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
			}

			$inquiry_assign_to =  ($sales_id!="" || $sales_id!=0)?$sales_id:4;
			//$inquiry_created_by = ($sales_id!="" || $sales_id!=0)?$sales_id:4;
			$sales_executive_id = ($sales_id!="" || $sales_id!=0)?$sales_id:4;
			/*auto assign inquiry*/
			// $update = $db->rp_update("no_order_inquiry",array("inquiry_lead_flag"=>0,"inquiry_type"=>0,"inquiry_date"=>date('Y-m-d'),"inquiry_assign_to"=>$inquiry_assign_to,"inquiry_created_by"=>$inquiry_created_by,"sales_executive_id"=>$sales_executive_id,"inq_status"=>2,"entry_flag"=>1),"id='".$_REQUEST['inquiry_id']."'",0);
			$update = $db->rp_update("no_order_inquiry",array("inquiry_lead_flag"=>0,"inquiry_type"=>0,"inquiry_date"=>date('Y-m-d'),"prospect_to_inquiry_date"=>date("Y-m-d H:i:s"),"inquiry_assign_to"=>$inquiry_assign_to,"sales_executive_id"=>$sales_executive_id,"inq_status"=>2,"entry_flag"=>1,"inquiry_created_by"=>$inquiry_created_by),"id='".$_REQUEST['inquiry_id']."'",0);
		}
		else
		{
			$module_name = "Raw Data";
			$flag = "Web";
			$log_description = $module_name." #INQ/".$_REQUEST['inquiry_id']." Convert To Inquiry By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			$update_array = array("inquiry_lead_flag"=>0,"inquiry_type"=>0,"inquiry_date"=>date('Y-m-d'),"inq_status"=>2,"entry_flag"=>1);
			$update = $db->rp_update("no_order_inquiry",$update_array,"id='".$_REQUEST['inquiry_id']."'",0,$log_description,$flag,$module_name,"","");
		}
		if($update)
		{
			$replay = array("ack"=>1,"ack_msg"=>"Raw Data Convert To Inquiry Successfully");
		}
		else
		{
			$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");	
		}
	} 
	else
	{
		$replay = array("ack"=>0,"ack_msg"=>"There Is No Such Raw Data Found.Please Check And Try Again.");
	}
	echo json_encode($replay);
}

// else if($m=="change_to_lead")
// {	

// 	$no_company = $db->rp_getTotalRecord("no_order_inquiry","id='".$_REQUEST['inquiry_id']."' AND type_of_company='0' AND isDelete=0",0);
// 	if($no_company>0){
// 		$replay = array("ack"=>0,"ack_msg"=>"Please Select Company Type First");
// 	}else{
// 		$IsInquiry = $db->rp_getTotalRecord("no_order_inquiry","id='".$_REQUEST['inquiry_id']."' AND inquiry_lead_flag='0' AND isDelete=0",0);
// 		if($IsInquiry>0)
// 		{
// 			$R1 = $db->rp_getData("no_order_inquiry","*","isDelete=0 AND id='".$_REQUEST['inquiry_id']."' AND inquiry_lead_flag='0' ","",0);
// 			$D1 = mysqli_fetch_assoc($R1);
// 			// print_r($D1);exit();
// 			//if($D1['status']!=0 && $D1['status']!=1)
// 			if($D1['status']!=0)
// 			{
// 				if($D1['company_name']!="" && $D1['mobile_number']!="")
// 				// if($D1['company_name']!="" && $D1['state']!="" && $D1['city']!="" && $D1['mobile_number']!="")
// 				{
// 					if($D1['gst_no']!="")
// 					{
// 						$customer_count = $db->rp_getTotalRecord("executive","gst='".$D1['gst_no']."' AND isDelete=0",0);
// 					}
// 					else
// 					{
// 						//$customer_count = $db->rp_getTotalRecord("executive","company_name='".$D1['company_name']."' AND phone='".$D1['mobile_number']."' AND isDelete=0",0);
// 						$customer_count = $db->rp_getTotalRecord("executive","company_name='".$D1['company_name']."' AND city='".$D1['city']."' AND isDelete=0",0);
// 					}

// 					if($customer_count<=0)
// 					{

// 						if($D1['type_of_company']!=0){

// 								$getclientcodeData = file_get_contents(ADMINSITEURL."executive_auto_code_ajax.php?type_of_company=".$D1['type_of_company']."&api=1");
// 								//print_r($getclientcodeData);exit;
// 								$c_data = html_entity_decode($getclientcodeData);
// 								$data_C = json_decode($c_data, true);
// 								$client_code = $data_C['client_code'];
// 								$client_code_sr_by_type = $data_C['client_code_sr_by_type'];
// 								//	echo $client_code."---".$client_code_sr_by_type;exit;
// 							}else{
							
// 								$random_num = mysqli_query("SELECT random_num FROM ( SELECT FLOOR(RAND() * 9999) AS random_num ) AS numbers_mst_plus_1 WHERE random_num NOT IN (SELECT client_code FROM executive WHERE client_code IS NOT NULL) LIMIT 1");
// 								$random_num_d = mysqli_fetch_array($random_num);
// 								$client_code=$random_num_d[0];
// 								$client_code_sr_by_type=0;

// 							}

// 						// code for random client code // 
						
// 						$add_rows = array("type_of_executive","type_of_company","company_name","cname","email","phone","whatsapp_no","address","country","state","city","zone","customer_flag","gst","industry_type_id","entry_flag","billing_address","client_code","client_code_sr_by_type","seid","type_of_company");
					    
// 					    $seid=($_SESSION[SITE_SESS.'REFERANCE_ID'])?$_SESSION[SITE_SESS.'REFERANCE_ID']:"";
// 						$add_values = array($D1['executive_type'],$D1['type_of_company'],$D1['company_name'],$D1['person_name'],$D1['email_address'],$D1['mobile_number'],$D1['other_mobile_no'],$D1['address'],$D1['country'],$D1['state'],$D1['city'],$D1['zone'],1,$D1['gst_no'],$D1['industry_type_id'],1,$D1['billing_address'],$client_code,$client_code_sr_by_type,$seid,$$D1['type_of_company']);
					
// 						// echo "<pre>"; print_r($add_rows); echo "<br>";
// 						// echo "<pre>"; print_r($add_values); exit;

// 						$InsretId = $db->rp_insert("executive",$add_values,$add_rows,1);

// 						if ($InsretId != 0) {

// 								$item_rows = array("customer_id","shipping_address");
// 								$item_values = array($InsretId,$D1['shipping_address']);
// 								$item_id = $db->rp_insert("customer_vs_shipping_address",$item_values,$item_rows,0);

// 						}

// 						require_once("../include/class.executive.php");
// 						$objClass= new Executive();
// 						$objClass->CreateCustomerAccount($InsretId);

// 						$Isupdate = $db->rp_update("no_order_inquiry",array("dealer_id"=>$InsretId),"id='".$_REQUEST['inquiry_id']."'");

// 						/*add class area*/

// 						if($D1['class_id']!=0 && $D1['area_id']!=0)
// 						{
// 							$class_id = $D1['class_id'];
// 							$area_id = $D1['area_id'];
// 						}
// 						else
// 						{
// 							$classArea = $db->getCassAreaIdFromName($D1['state'],$D1['city']);
// 							$class_id = $classArea['class_id'];
// 							$area_id = $classArea['area_id'];
// 						}

// 						/*$class_id = $db->rp_getValue("class","id","LOWER(name) LIKE '%".strtolower($D1['state'])."%' AND isDelete=0",0);
						
// 						$area_id = $db->rp_getValue("area","id","class_id = '".$class_id."' AND LOWER(name) LIKE '%".strtolower($D1['city'])."%' AND isDelete=0",0);*/
						
// 						$mapping_id=$db->rp_insert("executive_map_area",array($InsretId,$D1['executive_type'],$class_id,$area_id),array("executive_id","executive_type","class_id","area_id"),0);
						
// 						/*add class area*/
// 					}
// 					else
// 					{
// 						if($D1['gst_no']!="")
// 						{
// 							$dealer_id = $db->rp_getValue("executive","id","gst='".$D1['gst_no']."'  AND isDelete=0",0);
// 						}
// 						else
// 						{
// 							$dealer_id = $db->rp_getValue("executive","id","company_name='".$D1['company_name']."' AND phone='".$D1['mobile_number']."'  AND isDelete=0",0);
// 						}
// 						$Isupdate = $db->rp_update("no_order_inquiry",array("dealer_id"=>$dealer_id),"id='".$_REQUEST['inquiry_id']."'");
// 					}

// 					$module_name = "Inquiry";
// 					$flag = "Web";
// 					$log_description = $module_name." #INQ/".$_REQUEST['inquiry_id']." Convert To Lead By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
// 					$update_Data = array("inquiry_lead_flag"=>1,"inquiry_type"=>1,"lead_date"=>date('Y-m-d'),"inq_status"=>3,"entry_flag"=>1);
// 					$update = $db->rp_update("no_order_inquiry",$update_Data,"id='".$_REQUEST['inquiry_id']."'",0,$log_description,$flag,$module_name,"","");
// 					if($update)
// 					{
// 						$replay = array("ack"=>1,"ack_msg"=>"Inquiry Convert To Lead Successfully");
// 					}
// 					else
// 					{
// 						$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");	
// 					}
// 				}
// 				else
// 				{
// 					$replay = array("ack"=>0,"ack_msg"=>"Compay Name, Contact Number is Required. Please Fill the required Detail For Create Customer.");
// 				}
// 			}
// 			else
// 			{
// 				$replay = array("ack"=>2,"ack_msg"=>"Select Status.");
// 			}
// 		} 
// 		else
// 		{
// 			$replay = array("ack"=>0,"ack_msg"=>"There Is No Such Inquiry Found.Please Check And Try Again.");
// 		}
// 	}
// 	echo json_encode($replay);
// }
else if($m=="change_to_lead")
{	
	$IsInquiry = $db->rp_getTotalRecord("no_order_inquiry","id='".$_REQUEST['inquiry_id']."' AND inquiry_lead_flag='0' AND isDelete=0",0);
	if($IsInquiry>0)
	{
		$R1 = $db->rp_getData("no_order_inquiry","*","isDelete=0 AND id='".$_REQUEST['inquiry_id']."' AND inquiry_lead_flag='0' ","",0);
		$D1 = mysqli_fetch_assoc($R1);
		$M_IDS=array();
		$CONTACT_IDS=array();
		$M1=$db->rp_getData("customer_vs_phone_no","*","isDelete=0 AND customer_id='".$_REQUEST['inquiry_id']."' AND ref_table='no_order_inquiry'","",0);
		while($MD1=mysqli_fetch_assoc($M1))
		{
			$M_IDS[]=$MD1['phone_no'];
			$CONTACT_IDS[]=$MD1['name'];

		}
		$M_IDS_P=implode(",",$M_IDS);

		//if($D1['status']!=0 && $D1['status']!=1)
		if($D1['status']!=0)
		{
			// if($D1['state']!="" && $D1['city']!="" && $D1['mobile_number']!="") // old condition
			if($D1['state']!="" && $D1['mobile_number']!="")
			{
				if($D1['gst_no']!="" && $D1['gst_no'] != 'NA')
				{
					$customer_count =$db->rp_getTotalRecord("executive","gst='".$D1['gst_no']."' AND isDelete=0",0);
				}
				else
				{
					//$customer_count = $db->rp_getTotalRecord("executive","company_name='".$D1['company_name']."' AND phone='".$D1['mobile_number']."' AND isDelete=0",0);
					$customer_count = $db->rp_getTotalRecord("executive","company_name='".$D1['company_name']."' AND mobile_no1='".$D1['mobile_number']."'  AND isDelete=0",0);
				}

				if($customer_count<=0)
				{  
					// code for client code // 
					$client_code_prefix=$db->rp_getValue("company_master","prefix","id='".$D1['type_of_company']."' AND isDelete=0",0);
					$lastInsertIds=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_company='".$D1['type_of_company']."' AND isDelete=0",0); 

					$code=str_pad(($lastInsertIds+1), 4, '0', STR_PAD_LEFT); 
					$client_code = $client_code_prefix.($code); 
					// code for client code // 

					$dup_where = " (mobile_no1 = '" . $D1['mobile_number'] . "' OR client_code = '" . $client_code . "') AND company_name = '".$D1['company_name']."' AND isDelete=0";
					//$dup_where = " ( client_code = '" . $client_code . "') AND company_name = '".$D1['company_name']."' AND isDelete=0";

					$r = $db->rp_dupCheck('executive', $dup_where,0);
					if ($r) 
					{
						$reply = array("ack" => 0, "developer_msg" => "Mobile number already assigned to another customer!! Try another number.", "ack_msg" => "A mobile number or client code already exists, or the company name is already associated with another customer. Please check.");
						$db->printJSON($reply,1);
					} else {
						
						$category_data_r=$db->rp_getData("top_category_master","id","isDelete=0 AND id IN (".$D1['top_category_id'].") ","",0); 
						while($category_data_d=mysqli_fetch_assoc($category_data_r)) 
						{
							$catArr[]=$category_data_d['id'];
						}
						$catids=implode(',',$catArr); 

						$add_rows = array("type_of_executive","company_name","email","whatsapp_no","address","country","state","city","zone","customer_flag","gst","industry_type_id","entry_flag","billing_address","client_code","main_city","client_code_sr_by_type","seid","type_of_company","top_category_id","purchasing_from","zip","booking_pincode","mobile_no1","cname");
						
						$seid=($_SESSION[SITE_SESS.'REFERANCE_ID'])?$_SESSION[SITE_SESS.'REFERANCE_ID']:""; 
						$D1['executive_type']=($D1['executive_type']!="")?$D1['executive_type']:""; 
						$D1['company_name']=($D1['company_name']!="")?$D1['company_name']:""; 
						$D1['person_name']=($D1['person_name']!="")?$D1['person_name']:""; 
						$D1['email_address']=($D1['email_address']!="")?$D1['email_address']:""; 
						$D1['mobile_number']=($D1['mobile_number']!="")?$D1['mobile_number']:""; 
						$D1['other_mobile_no']=($D1['other_mobile_no']!="")?$D1['other_mobile_no']:""; 
						$D1['address']=($D1['address']!="")?$D1['address']:""; 
						$D1['country']=($D1['country']!="")?$D1['country']:""; 
						$D1['state']=($D1['state']!="")?$D1['state']:""; 
						$D1['city']=($D1['city']!="")?$D1['city']:""; 
						$D1['zone']=($D1['zone']!="")?$D1['zone']:""; 
						$D1['gst_no']=($D1['gst_no']!="")?$D1['gst_no']:""; 
						$D1['billing_address']=($D1['billing_address']!="")?$D1['billing_address']:""; 
						$D1['main_city']=($D1['main_city']!="")?$D1['main_city']:""; 
						$D1['type_of_company']=($D1['type_of_company']!="")?$D1['type_of_company']:""; 
						$catids=($catids!="")?$catids:"";  
						$D1['purchasing_from']=($D1['purchasing_from']!="")?$D1['purchasing_from']:""; 
						
						$add_values = array($D1['executive_type'],$D1['company_name'],$D1['email_address'],$D1['other_mobile_no'],$D1['address'],$D1['country'],$D1['state'],$D1['city'],$D1['zone'],1,$D1['gst_no'],$D1['gst_no'],1,$D1['billing_address'],$client_code,$D1['main_city'],$code,$seid,$D1['type_of_company'],$catids,$D1['purchasing_from'],$D1['pincode'],$D1['pincode'],$D1['mobile_number'],$D1['person_name']);
					
						// echo "<pre>"; print_r($add_rows); echo "<br>";
						// echo "<pre>"; print_r($add_values); exit;

							$InsretId = $db->rp_insert("executive",$add_values,$add_rows,0);

						if ($InsretId != 0) {

							$item_rows = array("customer_id","shipping_address");
							$item_values = array($InsretId,$D1['shipping_address']);
							$item_id = $db->rp_insert("customer_vs_shipping_address",$item_values,$item_rows,0);

							$count_value=0;
							
							foreach ($M_IDS as $key)
							{
								$item_rows1 = array("customer_id","phone_no","name","ref_table");
								$item_values1 = array($InsretId,addslashes(html_entity_decode($key)),$CONTACT_IDS[$count_value],"executive");
								$count_value++;
								$item_id = $db->rp_insert("customer_vs_phone_no",$item_values1,$item_rows1,0);
							}

							// $update=$db->rp_update("customer_vs_phone_no",array("ref_table"=>"executive"),"isDelete=0 AND customer_id='".$InsretId."'");

						}

						require_once("../include/class.executive.php");
						$objClass= new Executive();
						$objClass->CreateCustomerAccount($InsretId);

						$Isupdate = $db->rp_update("no_order_inquiry",array("dealer_id"=>$InsretId),"id='".$_REQUEST['inquiry_id']."'");

						if ($D1['city'] != "" && !empty($D1['city'])) {
							/*add class area*/

							/* Added Code By DINESH */
							if ($D1['area_id'] == "" || $D1['area_id'] == null || $D1['area_id'] == NULL || empty($D1['area_id'])) {
								
								$D1['area_id'] = $db->rp_getValue( "area", "id", " class_id='".$D1['class_id']."' AND city_id='".$D1['city_id']."' AND name LIKE '%".strtolower(trim($D1['main_city']))."%'", 0 );
							}
							/* Added Code By DINESH */

							if($D1['class_id']!=0 && $D1['area_id']!=0 && $D1['city_id'])
							{
								$class_id = $D1['class_id'];
								$area_id = $D1['area_id'];
								$city_id = $D1['city_id'];
							}
							else
							{
								$classArea = $db->getCassAreaIdFromName($D1['state'],$D1['main_city'],$D1['city']);
								$class_id = $classArea['class_id'];
								$area_id = $classArea['area_id'];
								$city_id =  $classArea['city_id'];
							}

							/*$class_id = $db->rp_getValue("class","id","LOWER(name) LIKE '%".strtolower($D1['state'])."%' AND isDelete=0",0);
							
							$area_id = $db->rp_getValue("area","id","class_id = '".$class_id."' AND LOWER(name) LIKE '%".strtolower($D1['city'])."%' AND isDelete=0",0);*/
							
							$mapping_id=$db->rp_insert("executive_map_area",array($InsretId,$D1['executive_type'],$class_id,$area_id,$city_id),array("executive_id","executive_type","class_id","area_id","city_id"),0);
							
							/*add class area*/
							
						}
					}
 
				}
				else
				{ 
					//	echo " 222222dxsdz";exit;
					if($D1['gst_no']!="")
					{
						$dealer_id = $db->rp_getValue("executive","id","gst='".$D1['gst_no']."'  AND isDelete=0",0);
					}
					else
					{
						$dealer_id = $db->rp_getValue("executive","id","company_name='".$D1['company_name']."' AND phone='".$D1['mobile_number']."'  AND isDelete=0",0);
					}
					$Isupdate = $db->rp_update("no_order_inquiry",array("dealer_id"=>$dealer_id),"id='".$_REQUEST['inquiry_id']."'");
				}

				$module_name = "Inquiry";
				$flag = "Web";
				$log_description = $module_name." #INQ/".$_REQUEST['inquiry_id']." Convert To Lead By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
				$update_Data = array("inquiry_lead_flag"=>1,"inquiry_type"=>1,"lead_date"=>date('Y-m-d H:i:s'),"inq_status"=>3,"entry_flag"=>1,"inquiry_date"=>date('Y-m-d'));
				$update = $db->rp_update("no_order_inquiry",$update_Data,"id='".$_REQUEST['inquiry_id']."'",0,$log_description,$flag,$module_name,"","");
				if($update)
				{
					$replay = array("ack"=>1,"ack_msg"=>"Inquiry Convert To Lead Successfully");
				}
				else
				{
					$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");	
				}
			}
			else
			{
				$replay = array("ack"=>0,"ack_msg"=>"Compay Name, State, City, Contact Number is Required. Please Fill the required Detail For Create Customer.");
			}
		}
		else
		{
			$replay = array("ack"=>2,"ack_msg"=>"Select Status.");
		}
	} 
	else
	{
		$replay = array("ack"=>0,"ack_msg"=>"There Is No Such Inquiry Found.Please Check And Try Again.");
	}
	echo json_encode($replay);
}

else if($m=="add_location")
{	
	$pro_count = $db->rp_getTotalRecord("product_weight_price","product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
	if($pro_count>0)
	{
		$update = $db->rp_update("product_weight_price",array("location"=>$_REQUEST['location']),"product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
		if($update)
		{
			// insert log
			/*$ref_id = $db->rp_getValue("product_weight_price","id","product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
			$activity_description = "Location Change to ".$_REQUEST['location']." By User ".$_SESSION[SITE_SESS . 'SESS_NAME']." of Product ".$db->rp_getValue("product","name","id='".$_REQUEST['product_id']."'")."(".$db->rp_getValue("product","product_code","id='".$_REQUEST['product_id']."'").") in Product Stock";
			$Log->insertLog("product_weight_price",$ref_id,"update",$activity_description);*/
			// insert log

			$replay = array("ack"=>1,"ack_msg"=>"Location Add Successfully");
		}
		else
		{
			$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");
		}
	}
	else
	{
		$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.");
	}
	echo json_encode($replay);
}

else if($m=="add_standard")
{	
	$pro_count = $db->rp_getTotalRecord("product_weight_price","product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
	if($pro_count>0)
	{
		$update = $db->rp_update("product_weight_price",array("standard_stock"=>$_REQUEST['standard']),"product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
		if($update)
		{ 
			// insert log
			/*$ref_id = $db->rp_getValue("product_weight_price","id","product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
			$activity_description = "Standard Change to ".$_REQUEST['standard']." By User ".$_SESSION[SITE_SESS . 'SESS_NAME']." of Product ".$db->rp_getValue("product","name","id='".$_REQUEST['product_id']."'")."(".$db->rp_getValue("product","product_code","id='".$_REQUEST['product_id']."'").") in Product Stock";
			$Log->insertLog("product_weight_price",$ref_id,"update",$activity_description);*/
			// insert log

			$replay = array("ack"=>1,"ack_msg"=>"Location Add Successfully");
		}
		else
		{
			$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");
		}
	}
	else
	{
		$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.");
	}
	echo json_encode($replay);
}

else if($m=="add_reorder_point")
{	
	$pro_count = $db->rp_getTotalRecord("product_weight_price","product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
	if($pro_count>0)
	{
		$update = $db->rp_update("product_weight_price",array("reorder_point"=>$_REQUEST['reorder_point']),"product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
		if($update)
		{
			// insert log
			/*$ref_id = $db->rp_getValue("product_weight_price","id","product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
			$activity_description = "Reorder Point Change to ".$_REQUEST['reorder_point']." By User ".$_SESSION[SITE_SESS . 'SESS_NAME']." of Product ".$db->rp_getValue("product","name","id='".$_REQUEST['product_id']."'")."(".$db->rp_getValue("product","product_code","id='".$_REQUEST['product_id']."'").")  in Product Stock";
			$Log->insertLog("product_weight_price",$ref_id,"update",$activity_description);*/
			// insert log
			$replay = array("ack"=>1,"ack_msg"=>"Reorder Add Successfully");
		}
		else
		{
			$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");
		}
	}
	else
	{
		$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.");
	}
	echo json_encode($replay);
}

else if($m=="add_chk_box")
{	
	$pro_count = $db->rp_getTotalRecord("product_weight_price","product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
	if($pro_count>0)
	{
		$update = $db->rp_update("product_weight_price",array("check_box"=>$_REQUEST['chkbx']),"product_id='".$_REQUEST['product_id']."' AND weight_id='".$_REQUEST['weight_id']."' AND isDelete=0",0);
		if($update)
		{
			$replay = array("ack"=>1,"ack_msg"=>"Location Add Successfully");
		}
		else
		{
			$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");
		}
	}
	else
	{
		$replay = array("ack"=>0,"ack_msg"=>"Something Went Wrong.");
	}
	echo json_encode($replay);
}
require_once "disconnect.php";

?>