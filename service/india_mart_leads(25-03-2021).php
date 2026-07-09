<?php
include ("connect.php");
// error_reporting(1);

function Slug_create($string)   
{   
	$string = str_replace(" ", "", $string);
	$slug = strtolower(trim(preg_replace('/-{2,}/','-',preg_replace('/[^a-zA-Z0-9-]/', '-', $string)),"-"));
	return $slug;
}

$pinal_desani=array("Rajashthan","Punjab","Haryana","Delhi","Uttar Pradesh","Gujarat","Diu & Daman","Dadra nagar haveli","Andaman nicobar","Himachal Pradesh","Bihar","Utarakhand","Zarkhand","Sikkim","Meghalay","Arunachal Pradesh","Mizoram","Manipur","Nagalend","Assam","Jammu and Kashmir");
$pinal_desani = array_map('Slug_create', $pinal_desani);


/*$bhoomi_leharu=array("Karnataka","Tamilnadu","Andhra Pradesh","Kerala","Telangana","Madhya Pradesh","Maharashtra","Chattisgadh","Odisha","West Bengal","Goa","Chhattisgarh");
$bhoomi_leharu = array_map('Slug_create', $bhoomi_leharu);*/

$Khyati=array("Karnataka","Tamilnadu","Andhra Pradesh","Kerala","Telangana","Madhya Pradesh","Maharashtra","Chattisgadh","Odish","West Bengal","Goa");
$Khyati = array_map('Slug_create', $Khyati);

$max_id=$db->rp_getValue("leads_cron_data","MAX(id)","type='india_mart' AND isDelete=0",0);
$Start_Time=$db->rp_getValue("leads_cron_data","date_time","id='".$max_id."' AND isDelete=0",0);
$Start_Time=date("d-m-Y H:i:s",strtotime($Start_Time));
$endtime=date("d-m-Y H:i:s");
// $url="india_mart_json.php";
/*
$url = 'https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/MTYwMDE2NzA2MS44ODE0IzIxNDE5MzU=/Start_Time/'.$Start_Time.'/End_Time/'.$endtime.'/';
$mart_json=file_get_contents($url);*/					

$mart_json=file_get_contents('https://mapi.indiamart.com/wservce/enquiry/listing/GLUSR_MOBILE/7042445112/GLUSR_MOBILE_KEY/MTYxNjU4NDk4Ny4zNjIyIzY1MjIyMzM=/');
$result=($mart_json!="")?(array)json_decode($mart_json,true):array();
// print_r($result);exit;
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


			$pinal_result = array();
			foreach ( $pinal_desani as $pinal_key => $pinal_value ) {
				similar_text($pinal_value, Slug_create($state), $pinal_percent);
				if ($pinal_percent > 60) {
				    $pinal_result[$pinal_key] = array($pinal_value);
				}
			}

			$Khyati_result = array();
			foreach ( $Khyati as $Khyati_key => $Khyati_value ) {
				similar_text($Khyati_value, Slug_create($state), $Khyati_percent);
				if ($Khyati_percent > 60) {
				    $Khyati_result[$Khyati_key] = array($Khyati_value);
				}
			}

			if(in_array(Slug_create($state), $pinal_desani) || sizeof($pinal_result)>0)
			{	
				$inquiry_created_by=$db->rp_getValue("sales_executive","id","name LIKE '%pinal desani%'");
				$sales_executive_id=$inquiry_created_by;
				$inquiry_assign_to=$inquiry_created_by;
			}
			else if(in_array(Slug_create($state), $Khyati) || sizeof($Khyati_result)>0)
			{
				
				$inquiry_created_by=$db->rp_getValue("sales_executive","id","name LIKE '%Khyati%'");
				$sales_executive_id=$inquiry_created_by;
				$inquiry_assign_to=$inquiry_created_by;
			}
			else
			{
				if(strtoupper($country)!="INDIA")
				{
					$inquiry_created_by=$db->rp_getValue("sales_executive","id","name LIKE '%jalpa solanki%'",0);
					$sales_executive_id=$inquiry_created_by;
					$inquiry_assign_to=$inquiry_created_by;
				}
				else
				{					
					$inquiry_created_by=$db->rp_getValue("sales_executive","id","name LIKE '%pinal desani%'",0);
					$sales_executive_id=$inquiry_created_by;
					$inquiry_assign_to=$inquiry_created_by;
				}
			}

			$inquiry_assign_to = ($inquiry_assign_to!="" || $inquiry_assign_to!=0)?$inquiry_assign_to:3;
			$inquiry_created_by = ($inquiry_created_by!="" || $inquiry_created_by!=0)?$inquiry_created_by:3;
			$sales_executive_id = ($sales_executive_id!="" || $sales_executive_id!=0)?$sales_executive_id:3;

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
echo json_encode($response);
?>