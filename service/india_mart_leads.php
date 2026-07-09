<?php
//file_get_contents("https://cmkcrm.cmkgroup.in/service/service_crone.php?key=1226&s=176"); 




/*include ("connect.php");
$mart_json=file_get_contents('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/MTYxNjU4NDk4Ny4zNjIyIzY1MjIyMzM=/');
$result=($mart_json!="")?(array)json_decode($mart_json,true):array();
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


			//auto assign inquiry
			// $state_id = $db->rp_getValue("state","id","name LIKE '%".$state."%' AND isDelete=0",0);
			// $state_id = $db->rp_getValue("class","id","name LIKE '%".$state."%' AND isDelete=0",0);
			//$state_id = $db->rp_getValue("class","id","LOWER(name) = '%".strtolower($state)."%' AND isDelete=0");
			
			// $city_id = $db->rp_getValue("area","id","name LIKE '%".$city."%' AND isDelete=0",0);
			//$city_id = $db->rp_getValue("area","id","class_id = '".$state_id."' AND LOWER(name) = '%".strtolower($city)."%' AND isDelete=0");

			//$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$state_id."' AND area_id='".$city_id."' AND executive_type!='sales_manager' AND isDelete=0",0);
			//auto assign inquiry



			//for add inquiry class area
			//$class_id = $db->rp_getValue("class","id","LOWER(name) = '%".strtolower($state)."%' AND isDelete=0");
			//$area_id = $db->rp_getValue("area","id","class_id = '".$class_id."' AND LOWER(name) = '%".strtolower($city)."%' AND isDelete=0");

			//if($class_id=="")
			//{

			//	$values = array($state);
			//	$rows = array("name");
			//	$class_id = $db->rp_insert("class",$values,$rows,0);
			//}

			//if($area_id=="")
			//{

				//$area_slug=$db->rp_createslug($city);
				//$area_name=$city;
				//$rows=array("class_id","name","area_slug","isDelete");
				//$values=array($class_id,$area_name,$area_slug,0);
				//$area_id=$db->rp_insert("area",$values,$rows,0);
			//}

			
			
			// new Added
			$classArea = $db->getCassAreaIdFromName($state,$city);
			$class_id = $classArea['class_id'];
			$area_id = $classArea['area_id'];
			// new Added



			//for add inquiry class area

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
				//india mart inquiry notification
				if($inquiry_assign_to!="" && $inquiry_assign_to!=0)
				{
					$inquiry_assign_name = $db->rp_getValue("sales_executive","name","id='".$inquiry_assign_to."'",0);
					$type = "inquiry";
					$title   = "India Mart Inquiry Assigned To ".$inquiry_assign_name.;
					$inq_no  = $db->getLastInsertId("no_order_inquiry");		
					$body    = "India Mart Inquiry Assigned To ".$inquiry_assign_name." ON ".date("Y-m-d H:i:s");
					$click_action="no_order_inquiry_grid.php?type=0";
					
					$Data = [
						'type'	=> $type,
			            'title' => $title,
			            'body' =>  $body,
			            'description' => $body,
			            "user_id"  => $inquiry_assign_to,
			            "reference_id"   => $uid,
						"item_id"        => $uid,
						"reference_type" => 'no_order_inquiry',
			            'icon' => NOTIFICATIONICON,
			            'image' => NOTIFICATIONIMAGE,
			            'click_action'=> ADMINSITEURL.$click_action,
					];

					$ReferanceArray = [
			            'reference_id' => 	$uid,
			            'reference_table' => "no_order_inquiry",
					];

					$id = $inquiry_assign_to;
			    	if($id!="")
				    {
					    //panel
					    $Upperlevel1 = '1';
					    $UpperlevelAll = '1';
						$db->send_notificationpanel($Data,$id,$ReferanceArray,$Upperlevel1,$UpperlevelAll);
					    //panel
					}
				}
				//india mart inquiry notification
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
echo json_encode($response);*/
?>