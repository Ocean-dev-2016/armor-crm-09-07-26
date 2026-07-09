<?php
include ("connect.php");

// $mart_json=file_get_contents('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/MTYzMzA2MTI3NS4yNDU0IzY1MjIyMzM=/Start_Time/".$date1."/End_Time/".$date1."/');
// echo "Sd";exit;
$date1 = date("d-M-Y");
$date2 = date("d-M-Y");

$company_r=$db->rp_getData("company_master","*","isDelete=0 AND india_mart_api_key!=''");
while($company_d=mysqli_fetch_assoc($company_r))
{ 
	$apiKey = $company_d['india_mart_api_key'];

	$mart_json=file_get_contents('https://mapi.indiamart.com/wservce/crm/crmListing/v2/?glusr_crm_key='.$apiKey.'&start_time='.$date1.'&end_time='.$date2);
	// $mart_json=file_get_contents('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/'.$apiKey.'/Start_Time/29-NOV-2021/End_Time/29-NOV-2021/');
			
	$result=($mart_json!="")?(array)json_decode($mart_json,true):array();
	// print_r($result); 
	// print_r($result['RESPONSE']); exit;
	$result=$result['RESPONSE'];
	$count_total=sizeof($result);
	$total_insert=0;
	$total_not_insert=0;
	if($count_total>0)
	{	
		foreach($result as $V)
		{
			$rfi_id=$V['UNIQUE_QUERY_ID'];
			$r=$db->rp_getTotalRecord("no_order_inquiry","rfi_id='".$rfi_id."' AND isDelete=0",0);
			if($r==0)
			{
				$date_of_call=(isset($V['QUERY_TIME']) && $V['QUERY_TIME']!="")?date("Y-m-d",strtotime($V['QUERY_TIME'])):"";
				$contact_person=(isset($V['SENDER_NAME']) && $V['SENDER_NAME']!="")?$V['SENDER_NAME']:"";
				$company_name=(isset($V['SENDER_COMPANY']) && $V['SENDER_COMPANY']!="")?$V['SENDER_COMPANY']:"";
				$mobile_number=(isset($V['SENDER_MOBILE']) && $V['SENDER_MOBILE']!="")?$V['SENDER_MOBILE']:"";
				$email_address=(isset($V['SENDER_EMAIL']) && $V['SENDER_EMAIL']!="")?$V['SENDER_EMAIL']:"";
				$subject=(isset($V['SUBJECT']) && $V['SUBJECT']!="")?$V['SUBJECT']:"";
				$address=(isset($V['SENDER_ADDRESS']) && $V['SENDER_ADDRESS']!="")?$V['SENDER_ADDRESS']:"";
				$city=(isset($V['SENDER_CITY']) && $V['SENDER_CITY']!="")?$V['SENDER_CITY']:"";
				$state=(isset($V['SENDER_STATE']) && $V['SENDER_STATE']!="")?$V['SENDER_STATE']:"";
				$country=(isset($V['SENDER_COUNTRY_ISO']) && $V['SENDER_COUNTRY_ISO']!="")?$db->rp_getValue("country","name","code='".$V['SENDER_COUNTRY_ISO']."'"):"";
				$whatsapp_number=(isset($V['SENDER_MOBILE_ALT']) && $V['SENDER_MOBILE_ALT']!="")?$V['SENDER_MOBILE_ALT']:"";
				$remark=(isset($V['QUERY_MESSAGE']) && $V['QUERY_MESSAGE']!="")?$V['QUERY_MESSAGE']:"";
				$customer_requirement=(isset($V['QUERY_MESSAGE']) && $V['QUERY_MESSAGE']!="")?$V['QUERY_MESSAGE']:"";

				$call_duration=(isset($V['CALL_DURATION']) && $V['CALL_DURATION']!="" && $V['CALL_DURATION']!="null")?$V['CALL_DURATION']:"";

				// new Added
				$classArea = $db->getCassAreaIdFromName($state,$city,$city);
				$class_id = $classArea['class_id'];
				$area_id = $classArea['area_id'];
				$city_id = $classArea['city_id'];
				// new Added

				/*for add inquiry class area*/
				$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND (city_id='".$city_id."' OR area_id='".$area_id."') AND isDelete=0",0);
				$inquiry_assign_to =  ($sales_id!="" || $sales_id!=0)?$sales_id:0;
				$inquiry_created_by = ($sales_id!="" || $sales_id!=0)?$sales_id:0;
				$sales_executive_id = ($sales_id!="" || $sales_id!=0)?$sales_id:0;
				
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
						"type_of_company",
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
						16,
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
						addslashes($subject),
						$class_id,
						$area_id,
						$company_d['id'],
					);

				$uid = $db->rp_insert("no_order_inquiry",$values,$rows,0);	
				if($uid)
				{	
					$total_insert++;	
					$db->addStatusTimelineEntry($uid,0);		
				}
				else
				{
					$total_not_insert++;
				}
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
echo json_encode($response);
?>