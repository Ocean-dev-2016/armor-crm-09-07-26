<?php
$page_id=400;$page_slug='dashboard';
include('connect_in.php');
include('../include/notification.class.php');
$nt_obj = new Notification();

$ref_id = $_REQUEST['id'];
$type=$_REQUEST['mail_type'];


// need to set dynamic
$customer_id =$db->rp_getValue($type,"customer_id","id='".$ref_id ."'",0);
$Data['default_to'] = $_REQUEST['to_email'];
$default_cc = $_REQUEST['cc_email'];
if($default_cc!="")
{
	$Data['default_cc'] = $default_cc;
}
$Data['default_bcc']=CLIENT_EMAIL;

$Data['from_mail'] = $db->rp_getValue(CTABLE_ADMIN,"email","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'",0);
$Data['from_email_password'] = $db->rp_getValue(CTABLE_ADMIN,"email_password","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'",0); 
$Data['from_name'] = $db->rp_getValue(CTABLE_ADMIN,"name","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'","",0); 

// need to set dynamic 
$staic = 1;
if($type=="quotation_detail")
{
	$quotation_id = $ref_id;
	$staic = 2;
	include("quotation_generate.php");
	$Data['filename'] = $fileName; 

	$Data['subject'] = "Quotation".$Data['filename'];
	$Data['body'] = "<h4>".$_REQUEST['description']."</h4>";
}
else if($type=="orders")
{ 
	$order_id = $ref_id;
	$staic = 2;
	include("order_generate.php");
	$Data['filename'] = $fileName;

	$Data['subject'] = "Sales Order".$Data['filename'];
	$Data['body'] = "<h4>".$_REQUEST['description']."</h4>";
}
else if($type=="dispatch_detail")
{ 
	$dispatch_id = $ref_id;
	$staic = 2;
	include("dispatch_generate.php");
	$Data['filename'] = $fileName;

	$Data['subject'] = "Dispatch Order".$Data['filename'];
	$Data['body'] = "<h4>".$_REQUEST['description']."</h4>";
}
else if($type=="sales_invoice_detail")
{ 
	$order_id = $ref_id;
	$staic = 2;
	$format_type = 1;
	include("invoice_generate.php");
	$Data['filename'] = $fileName;

	$Data['subject'] = "Invoice".$Data['filename'];
	$Data['body'] = "<h4>".$_REQUEST['description']."</h4>";
}

$Data['file_path'] = $file_path;
$reply = $nt_obj->rp_sendEmailSmtp($Data);
if($reply)
{
	/*add entry in inbox table*/
	$insert_rows = array("mail_type","to_email","cc_email","description","mail_date","isDelete","isActive");
	$insert_value = array($type,$_REQUEST['to_email'],$_REQUEST['cc_email'],$_REQUEST['description'],date('Y-m-d'),0,1);
	$insert = $db->rp_insert("inbox",$insert_value,$insert_rows);
	/*add entry in inbox table*/
	$result = array("ack"=>1,"ack_msg"=>"Sent Successfully!");
}
else
{ 
	$result = array("ack"=>0,"ack_msg"=>"Something went to wrong!Please try again!");
}
echo json_encode($result);
?>