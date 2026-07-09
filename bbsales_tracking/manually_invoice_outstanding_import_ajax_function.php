<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");

$id=$_REQUEST['id'];
if($id)
{  
	$ctableR = $db->rp_getData("manually_invoice_outstanding_import","*","id='".$id."' AND isDelete=0");
	$ctable_d=mysqli_fetch_assoc($ctableR);

	// $customer_phone = $db->rp_getValue("executive","mobile_no1","id='".$customer_id."' AND isDelete=0");
	$company_name = $db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."' AND isDelete=0");
	$cname = $db->rp_getValue("executive","cname","id='".$ctable_d['customer_id']."' AND isDelete=0");
	
	$customer_phone = $ctable_d['mobile_no1'];
	// $customer_phone = $db->rp_getValue("manually_invoice_outstanding_import","mobile_no1","id='".$ctable_d['id']."' AND isDelete=0");
	
	$smsNumber="91".$customer_phone;
	// $smsNumber="919904572167";
	// $smsNumber="919998274025"; 

	$sms = 
	"Dear ".$company_name.",
	
	I hope this message finds you well!
	Please be informed that your payment against the below order is due. Please clear the dues at the earliest.
	Bill Number: ".$ctable_d['bill_no']."	
	Order Amount: ".number_format($ctable_d['bill_amount'],2)." 	
	Due Payment: ".number_format($ctable_d['balance_amt'],2)."	
	Order Date: ".date('d-m-Y',strtotime($ctable_d['bill_date']))."
	Due Days: ".$ctable_d['due_days']."
	If you have any questions or need further assistance, feel free to reach out.
	Thank you for your prompt attention!
	Regards,
	Armor Steel Industries Private Limited (Formerly Mahadev Casting)";

	/*"Dear ".$company_name.",
	I hope this message finds you well! Please find attached the invoice for your recent order.
	Order Details:
	Order Amount: ".number_format($ctable_d['bill_amount'],2)."
	Due Payment: ".number_format($ctable_d['balance_amt'],2)."
	Order Date: ".date('d-m-Y',strtotime($ctable_d['bill_date']))."
	If you have any questions or need further assistance, feel free to reach out.
	Thank you for your prompt attention!
	Regards,
	Mahadev Casting";*/
	// echo $smsNumber;
	// echo $sms;exit;
	if(WHATSAPP_SMS_SEND)
	{ 
		$reply = $db->send_whatsapp($smsNumber,$sms);
		$result = json_decode($reply);
		// print_r($result);
		// echo $result->$ErrorMessage;exit;
		// echo $reply;
		// echo $result;

		$reply=array("ack"=>1,"ack_msg"=>"Success!!");
		echo json_encode($reply);
	}
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Somethinf Went Wrong!!");
	echo json_encode($reply);
}
?>