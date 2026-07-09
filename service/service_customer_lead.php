<?php 
// Connect to Database
include('connect.php');
$trade_json=file_get_contents('https://www.tradeindia.com/utils/my_inquiry.html?userid=2838890&profile_id=2487099&key=f15d143f59fba3d54c734cf0156bd23c&from_date=2020-01-01&to_date=2020-09-25');					
$result=($trade_json!="")?(array)json_decode($trade_json,true):array();
// print_r($result);exit;

foreach($result as $V)
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

	$rows 	= array(
			"company_name",
			"person_name",
			"mobile_number",
			"whatsapp_number",
			"date_of_call",
			"country",
			"state",
			"city",
			"executive_type",
			"source_of_inquiry",
			"inquiry_created_by",
			"inquiry_assign_to",
			"remark",
			"quotation_flag",
			"customer_requirement",
			"email_address",
			"address",
		);
	$values = array(
			$company_name,
			$contact_person,
			$mobile_number,
			$whatsapp_number,
			$date_of_call,
			$country,
			$state,
			$city,
			6,
			6,
			3,
			3,
			addslashes($remark),
			0,
			addslashes($customer_requirement),
			$email_address,
			addslashes($address),
		);

	$uid = $db->rp_insert("customer_inquiry",$values,$rows,0);				
}
if($uid)
{
	$response=array('ack' => 1,'ack_message' =>'Data Insert Successfully');
}
else
{
	$response=array('ack' => 0,'ack_message' =>'Not Insert');
}
echo json_encode($response);
?>