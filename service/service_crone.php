<?php 
// Connect to Database
include('connect.php');
//require_once('../include/notification.class.php');
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
if($is_valid_api_key)
{	
	if($is_valid_service)
	{
 		if($service=='send_followup_notification' || $service==175)
		{
				$ack=$objUser->SendFollowupNotification();
				$db->printJSON($ack);
		}
		else if($service=='india_mart_cron_api' || $service==176)
		{
			
		      /*$characters1='0123456789';
              $randStr1="";
              for($j=0;$j<=3;$j++)
              {
               		$randStr1=$randStr1.$characters1[rand(0,strlen($characters1)-1)];
              }
        	  $add_rows = array("random_number");
              $add_values = array($randStr1."india-cmk");
              $InsretId = $db->rp_insert("cron_table",$add_values,$add_rows,0);*/
        //echo "hello";exit;
        
                $endtime_india=date("d-M-Y");
                $Start_Time_india=date('d-M-Y', strtotime('-1 day', strtotime($endtime_india)));
                
                //echo 'https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/MTYzMzA2MTI3NS4yNDU0IzY1MjIyMzM=/Start_Time/'.$Start_Time_india.'/End_Time/'.$endtime_india."/" ;exit;
            $mart_json=file_get_contents('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/MTYzMzA2MTI3NS4yNDU0IzY1MjIyMzM=/Start_Time/'.$Start_Time_india.'/End_Time/'.$endtime_india.'/');
           //$mart_json=file_get_contents('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/MTYzMzA2MTI3NS4yNDU0IzY1MjIyMzM=/Start_Time/03-DEC-2021/End_Time/03-DEC-2021/');
            $result=($mart_json!="")?(array)json_decode($mart_json,true):array();
            //print_r($result); exit;
            $count_total=sizeof($result);
            $total_insert=0;
            $total_not_insert=0;
            if($count_total>0)
            {	
            	foreach($result as $V)
            	{
            		$rfi_id=$V['QUERY_ID'];
            		$r=$db->rp_getTotalRecord("no_order_inquiry","rfi_id='".$rfi_id."' AND isDelete=0",0);
            		if($r==0)
            		{
            			$mobile_number=(isset($V['MOB']) && $V['MOB']!="")?$V['MOB']:"";
            			$country=(isset($V['COUNTRY_ISO']) && $V['COUNTRY_ISO']!="")?$db->rp_getValue("country","name","code='".$V['COUNTRY_ISO']."'"):"";
            			$state=(isset($V['ENQ_STATE']) && $V['ENQ_STATE']!="")?$V['ENQ_STATE']:"";
            			$city=(isset($V['ENQ_CITY']) && $V['ENQ_CITY']!="")?$V['ENQ_CITY']:"";
            			$remark=(isset($V['ENQ_MESSAGE']) && $V['ENQ_MESSAGE']!="")?$V['ENQ_MESSAGE']:"";
            			$customer_requirement=(isset($V['ENQ_MESSAGE']) && $V['ENQ_MESSAGE']!="")?$V['ENQ_MESSAGE']:"";
            			$email_address=(isset($V['SENDEREMAIL']) && $V['SENDEREMAIL']!="")?$V['SENDEREMAIL']:"";
            			$address=(isset($V['ENQ_ADDRESS']) && $V['ENQ_ADDRESS']!="")?$V['ENQ_ADDRESS']:"";
            			$whatsapp_number=(isset($V['MOBILE_ALT']) && $V['MOBILE_ALT']!="")?$V['MOBILE_ALT']:"";
            			$contact_person=(isset($V['SENDERNAME']) && $V['SENDERNAME']!="")?$V['SENDERNAME']:"";
            			$company_name=(isset($V['SENDERNAME']) && $V['SENDERNAME']!="")?$V['SENDERNAME']:"";
            			$date_of_call=(isset($V['DATE_RE']) && $V['DATE_RE']!="")?date("Y-m-d",strtotime($V['DATE_RE'])):"";
            			$call_duration=(isset($V['ENQ_CALL_DURATION']) && $V['ENQ_CALL_DURATION']!="" && $V['ENQ_CALL_DURATION']!="null")?$V['ENQ_CALL_DURATION']:"";
            			$subject=(isset($V['SUBJECT']) && $V['SUBJECT']!="")?$V['SUBJECT']:"";
            
            
            			/*auto assign inquiry*/
            			// $state_id = $db->rp_getValue("state","id","name LIKE '%".$state."%' AND isDelete=0",0);
            			// $state_id = $db->rp_getValue("class","id","name LIKE '%".$state."%' AND isDelete=0",0);
            			/*$state_id = $db->rp_getValue("class","id","LOWER(name) = '%".strtolower($state)."%' AND isDelete=0");
            			
            			// $city_id = $db->rp_getValue("area","id","name LIKE '%".$city."%' AND isDelete=0",0);
            			$city_id = $db->rp_getValue("area","id","class_id = '".$state_id."' AND LOWER(name) = '%".strtolower($city)."%' AND isDelete=0");
            
            			$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$state_id."' AND area_id='".$city_id."' AND executive_type!='sales_manager' AND isDelete=0",0);*/
            			/*auto assign inquiry*/
            
            
            
            			/*for add inquiry class area*/
            			/*$class_id = $db->rp_getValue("class","id","LOWER(name) = '%".strtolower($state)."%' AND isDelete=0");
            			$area_id = $db->rp_getValue("area","id","class_id = '".$class_id."' AND LOWER(name) = '%".strtolower($city)."%' AND isDelete=0");
            
            			if($class_id=="")
            			{
            
            				$values = array($state);
            				$rows = array("name");
            				$class_id = $db->rp_insert("class",$values,$rows,0);
            			}
            
            			if($area_id=="")
            			{
            
            				$area_slug=$db->rp_createslug($city);
            				$area_name=$city;
            				$rows=array("class_id","name","area_slug","isDelete");
            				$values=array($class_id,$area_name,$area_slug,0);
            				$area_id=$db->rp_insert("area",$values,$rows,0);
            			}*/
            
            			
            			
            			// new Added
            			$classArea = $db->getCassAreaIdFromName($state,$city,$city);
            			$class_id = $classArea['class_id'];
            			$area_id = $classArea['area_id'];
            			// new Added
            
            
            
            			/*for add inquiry class area*/
            
            			$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND area_id='".$area_id."' AND executive_type!='sales_manager' AND isDelete=0",0);
            			$inquiry_assign_to =  ($sales_id!="" || $sales_id!=0)?$sales_id:4;
            			$inquiry_created_by = ($sales_id!="" || $sales_id!=0)?$sales_id:4;
            			$sales_executive_id = ($sales_id!="" || $sales_id!=0)?$sales_id:4;
                    
            			
            			$rows 	= array(
            					"company_name",
            					"person_name",
            					"mobile_number",
            					"other_mobile_no",
            					"date_of_call",
            					"datetime",
            					"inquiry_date",
            					"country",
            					"state",
            					"city",
            					"executive_type",
            					"source_of_inquiry",
            					"sales_executive_id",
            					"inquiry_created_by",
            					"inquiry_assign_to",
            					"description",
            					"quotation_flag",
            					"customer_requirement",
            					"email_address",
            					"address",
            					"rfi_id",
            					"dealer_id",
            					"call_duration",
            					"subject",
            					"class_id",
            					"area_id",
            				);
            			$values = array(
            					$company_name,
            					$contact_person,
            					$mobile_number,
            					$whatsapp_number,
            					$date_of_call,
            					$date_of_call,
            					$date_of_call,
            					$country,
            					$state,
            					$city,
            					4,
            					4,
            					$sales_executive_id,
            					$inquiry_created_by,
            					$inquiry_assign_to,
            					addslashes($remark),
            					0,
            					addslashes($customer_requirement),
            					$email_address,
            					addslashes($address),
            					$rfi_id,
            					-1,
            					$call_duration,
            					$subject,
            					$class_id,
            					$area_id,
            				);
            
            			$uid = $db->rp_insert("no_order_inquiry",$values,$rows,0);	
            			if($uid)
            			{	
            				$total_insert++;			
            			}
            			else
            			{
            				$total_not_insert++;
            			}
            		}			
            	}
            }
            if($total_insert>0)
            {
            	if($total_not_insert==0)
            	{		
            		$rows1=array(
            			"type",
            			"date_time"
            		);
            		$values1=array(
            			"india_mart",
            			date("Y-m-d H:i:s",strtotime($endtime))
            		);
            		$insert=$db->rp_insert("leads_cron_data",$values1,$rows1,0);
            	}
            	$response=array('ack' => 1,'ack_message' =>'Data Insert Successfully',"total_record"=>$count_total,"total_insert_record"=>$total_insert,"total_not_insert_record"=>$total_not_insert);
            }
            else
            {
            	$response=array('ack' => 0,'ack_message' =>'Not Insert',"total_record"=>$count_total,"total_insert_record"=>$total_insert,"total_not_insert_record"=>$total_not_insert);
            }
                      //  echo json_encode($response);

        	$db->printJSON($response);
			
		}
		
		else if($service=='trad_india_cron_api' || $service==177)
		{
		     /* $characters1='0123456789';
              $randStr1="";
              for($j=0;$j<=3;$j++)
              {
               		$randStr1=$randStr1.$characters1[rand(0,strlen($characters1)-1)];
              }
        	  $add_rows = array("random_number");
              $add_values = array($randStr1."trade-cmk");
              $InsretId = $db->rp_insert("cron_table",$add_values,$add_rows,0);
        */
                $Start_Time=date("Y-m-d");
                $endtime=date('Y-m-d', strtotime('+1 day', strtotime($Start_Time)));
                //$url="https://www.tradeindia.com/utils/my_inquiry.html?userid=4922269&profile_id=6598457&key=23d1ce6c4fe90c10aa80e5860dcd51d3&from_date='.$Start_Time.'&to_date='.$endtime";
                $trade_json=file_get_contents('https://www.tradeindia.com/utils/my_inquiry.html?userid=4922269&profile_id=6598457&key=23d1ce6c4fe90c10aa80e5860dcd51d3&from_date='.$Start_Time.'&to_date='.$endtime);					
                //$trade_json=file_get_contents('https://www.tradeindia.com/utils/my_inquiry.html?userid=4922269&profile_id=6598457&key=23d1ce6c4fe90c10aa80e5860dcd51d3&from_date=2021-10-19&to_date=2021-10-19');					
                $result=($trade_json!="")?(array)json_decode($trade_json,true):array();
                //print_r($result);exit;
                $count_total=sizeof($result);
                $total_insert=0;
                $total_not_insert=0;
                foreach($result as $V)
                {
                	$rfi_id=$V['rfi_id'];
                	$r=$db->rp_getTotalRecord("no_order_inquiry","rfi_id='".$rfi_id."' AND isDelete=0");
                	if($r==0)
                	{		
                		$mobile_number=(isset($V['sender_mobile']) && $V['sender_mobile']!="")?$V['sender_mobile']:"";
                		$country=(isset($V['sender_country']) && $V['sender_country']!="")?$V['sender_country']:"";
                		$state=(isset($V['sender_state']) && $V['sender_state']!="")?$V['sender_state']:"";
                		$city=(isset($V['sender_city']) && $V['sender_city']!="")?$V['sender_city']:"";
                		$remark=(isset($V['message']) && $V['message']!="")?$V['message']:"";
                		$customer_requirement=(isset($V['message']) && $V['message']!="")?$V['message']:"";
                		$email_address=(isset($V['sender_email']) && $V['sender_email']!="")?$V['sender_email']:"";
                		$address=(isset($V['address']) && $V['address']!="")?$V['address']:"";
                		$whatsapp_number=(isset($V['sender_other_mobiles']) && $V['sender_other_mobiles']!="")?$V['sender_other_mobiles']:"";
                		$contact_person=(isset($V['sender_name']) && $V['sender_name']!="")?$V['sender_name']:"";
                		$company_name=(isset($V['sender_name']) && $V['sender_name']!="")?$V['sender_name']:"";
                		$date_of_call=(isset($V['generated_date']) && $V['generated_date']!="")?date("Y-m-d",strtotime($V['generated_date'])):"";
                
                		// new Added
                			$classArea = $db->getCassAreaIdFromName($state,$city,$city);
                			$class_id = $classArea['class_id'];
                			$area_id = $classArea['area_id'];
                		// new Added
                
                		$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND area_id='".$area_id."' AND executive_type!='sales_manager' AND isDelete=0",0);
                		$inquiry_assign_to  = ($sales_id!="" || $sales_id!=0)?$sales_id:4;
                		$inquiry_created_by = ($sales_id!="" || $sales_id!=0)?$sales_id:4;
                		$sales_executive_id = ($sales_id!="" || $sales_id!=0)?$sales_id:4;
                
                		$rows 	= array(
                				"company_name",
                				"person_name",
                				"mobile_number",
                				"other_mobile_no",
                				"date_of_call",
                				"datetime",
                				"inquiry_date",
                				"country",
                				"state",
                				"city",
                				"executive_type",
                				"source_of_inquiry",
                				"sales_executive_id",
                				"inquiry_created_by",
                				"inquiry_assign_to",
                				"description",
                				"quotation_flag",
                				"customer_requirement",
                				"email_address",
                				"address",
                				"rfi_id",
                				"dealer_id",
                			);
                		$values = array(
                				$company_name,
                				$contact_person,
                				$mobile_number,
                				$whatsapp_number,
                				$date_of_call,
                				$date_of_call,
                				$date_of_call,
                				$country,
                				$state,
                				$city,
                				4,
                				6,
                				$sales_executive_id,
                				$inquiry_created_by,
                				$inquiry_assign_to,
                				addslashes($remark),
                				0,
                				addslashes($customer_requirement),
                				$email_address,
                				addslashes($address),
                				$rfi_id,
                				-1
                			);
                
                		$uid = $db->rp_insert("no_order_inquiry",$values,$rows,0);
                		if($uid)
                		{	
                			$total_insert++;			
                		}
                		else
                		{
                			$total_not_insert++;
                		}
                
                	}
                }
                if($total_insert==$count_total)
                {
                	$rows1=array(
                		"type",
                		"date_time"
                	);
                	$values1=array(
                		"trade_india",
                		$enddttime
                	);
                	$insert=$db->rp_insert("leads_cron_data",$values1,$rows1,0);
                	$response=array('ack' => 1,'ack_message' =>'Data Insert Successfully',"total_record"=>$count_total,"total_insert_record"=>$total_insert,"total_not_insert_record"=>$total_not_insert);
                }
                else
                {
                	$response=array('ack' => 0,'ack_message' =>'Not Insert',"total_record"=>$count_total,"total_insert_record"=>$total_insert,"total_not_insert_record"=>$total_not_insert);
                }
              //  echo json_encode($response);

        	$db->printJSON($response);
			
		}
			

	}
	else
	{
		$ack=array( "ack"=>0,
					"ack_msg"=>"Internal error!!",
					"developer_msg"=>"Check your API Key or contact Admin",
					"extra"=>array("requested_params"=>$_REQUEST,
									"other"=>array()));
		$db->printJSON($ack);
	}
}
else
{
	$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
				"developer_msg"=>"Check your API Key or contact Admin",
				"extra"=>array("requested_params"=>$_REQUEST,
								"other"=>array())
			);
	$db->printJSON($ack);
}




?>