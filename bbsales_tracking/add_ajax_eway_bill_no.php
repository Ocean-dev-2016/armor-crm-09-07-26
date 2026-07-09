<?php
$page_id=588;$page_slug='add_invoice';
include("connect.php"); 
if($_REQUEST['mode']=="ewaybillno")
{
	$invoice_id = $_REQUEST['invoice_id'];
	$way_bill_no = $_REQUEST['way_bill_no'];
	$update = $db->rp_update("invoice_new",array("way_bill_no"=>$way_bill_no),"id='".$invoice_id."'",0);
	/*log entry*/
		$invoice_no = $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'");
		$ctable = "invoice_new";
		$last_id = $invoice_id;
		$flag = "Web";
	    $module_name = "Invoice";
	    $customer_id = $db->rp_getValue("invoice_new","customer_id","id='".$invoice_id."'");
	    $log_description = $module_name." ".$invoice_no." Update E-way Bill No By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
	    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/
	$reply=array("ack"=>1,"developer_msg"=>"E-way Bill No Update Successfully.","ack_msg"=>"E-way Bill No Update Successfully.");
	echo json_encode($reply);
}
elseif ($_REQUEST['mode']=="addmaterialreceivedate") 
{
	$invoice_id = $_REQUEST['invoice_id'];
	$material_receive_date = $_REQUEST['material_receive_date'];
	$update = $db->rp_update("invoice_new",array("material_receive_date"=>$material_receive_date),"id='".$invoice_id."'",0);
	/*log entry*/
		$invoice_no = $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'");
		$ctable = "invoice_new";
		$last_id = $invoice_id;
		$flag = "Web";
	    $module_name = "Invoice";
	    $customer_id = $db->rp_getValue("invoice_new","customer_id","id='".$invoice_id."'");
	    $log_description = $module_name." ".$invoice_no." Update Material Receive Date By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
	    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/
	$reply=array("ack"=>1,"developer_msg"=>"Material Receive Date Update Successfully.","ack_msg"=>"Material Receive Date Update Successfully.");
	echo json_encode($reply);
}

elseif ($_REQUEST['mode']=="invoce_detail") 
{


	$invoice_id = $_REQUEST['invoice_id'];
	$invoice_no = $_REQUEST['invoice_no'];
	$invoice_date = $_REQUEST['invoice_date'];
	$way_bill_no1 = $_REQUEST['way_bill_no1'];
	$prefix_id = $_REQUEST['prefix_id'];
	$status = 1;

	$invoice_data = $db->rp_getData("invoice_new","*","id='".$invoice_id."' AND isDelete=0");
	$invoice_data_d=mysqli_fetch_assoc($invoice_data);

	$invoice_no1     = $db->rp_getValue("invoice_new","MAX(`invoice_sr_no`)","isDelete=0 AND type_id='".$prefix_id."'",0);
	$invoice_no2    = $invoice_no1+1;

	$update_array = array(
		"status"=>$status,
		"invoice_no"=>$invoice_no,
		"invoice_date"=>date('Y-m-d',strtotime($invoice_date)),
		"way_bill_no"=>$way_bill_no1,
		"invoice_sr_no"=>$invoice_no2
	);		

	$update = $db->rp_update("invoice_new",$update_array,"id='".$invoice_id."'",0);
	$dispatch_id = $db->rp_getValue("invoice_new","dispatch_ids","id='".$invoice_id."'",0);
	$db->rp_update("dispatch_detail",array("status"=>1),"id='".$dispatch_id."'",0);


	/*add transaction*/
	require_once('../include/class.system.php');
	$system = new System();
	
	$AccountInfo=$db->rp_getData("account","*","cid='".$invoice_data_d['customer_id']."'","",0);
	$AccountInfo=mysqli_fetch_assoc($AccountInfo);
	// $AccountInfo=$system->GetAccountInfo($invoice_data_d['customer_id']);
	if($AccountInfo)
	{
		$AccountID=$AccountInfo['id'];
		$AccountNo=$AccountInfo['acc_no'];
		$Columns=array("cid","account_id","account_no","type","debit","amount","reference_id","reference_table","description","payment_date");
		$debit="-".$db->rp_getValue("invoice_new","grand_total","id='".$invoice_id."'",0);
		$grand_total=$db->rp_getValue("invoice_new","grand_total","id='".$invoice_id."'",0);
		$payment_date=date('Y-m-d');
		$payment_type = 0;
		$remark = "Invoice Entry Of Invoice No. <a target='_blank' href='invoice_viewer.php?invoice_id=".$invoice_id."'>". $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'",0)."</a>";
		$Values=array($invoice_data_d['customer_id'],$AccountID,$AccountNo,$payment_type,$debit,$grand_total,$invoice_id,"invoice",$remark,$payment_date);
		/*entry account transaction*/
		$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);
	}
	/*add transaction*/

	$reply=array("ack"=>1,"developer_msg"=>"Material Receive Date Update Successfully.","ack_msg"=>"Material Receive Date Update Successfully.");
	echo json_encode($reply);
}
elseif ($_REQUEST['mode']=="sales_return_detail") 
{


	$invoice_id = $_REQUEST['invoice_id'];
	$invoice_no = $_REQUEST['invoice_no'];
	$invoice_date = $_REQUEST['invoice_date'];
	$way_bill_no1 = $_REQUEST['way_bill_no1'];
	$prefix_id = $_REQUEST['prefix_id'];
	$status = 1;

	$invoice_data = $db->rp_getData("sales_return","*","id='".$invoice_id."' AND isDelete=0");
	$invoice_data_d=mysqli_fetch_assoc($invoice_data);

	$invoice_no1     = $db->rp_getValue("sales_return","MAX(`invoice_sr_no`)","isDelete=0 AND type_id='".$prefix_id."'",0);
	$invoice_no2    = $invoice_no1+1;

	$update_array = array(
		"status"=>$status,
		"invoice_no"=>$invoice_no,
		"invoice_date"=>date('Y-m-d',strtotime($invoice_date)),
		"way_bill_no"=>$way_bill_no1,
		"invoice_sr_no"=>$invoice_no2
	);		

	$update = $db->rp_update("sales_return",$update_array,"id='".$invoice_id."'",0);
	 

	/*add transaction*/
	require_once('../include/class.system.php');
	$system = new System();
	
	$AccountInfo=$db->rp_getData("account","*","cid='".$invoice_data_d['customer_id']."'","",0);
	$AccountInfo=mysqli_fetch_assoc($AccountInfo);
	// $AccountInfo=$system->GetAccountInfo($invoice_data_d['customer_id']);
	if($AccountInfo)
	{
		$AccountID=$AccountInfo['id'];
		$AccountNo=$AccountInfo['acc_no'];
		$Columns=array("cid","account_id","account_no","type","credit","amount","reference_id","reference_table","description","payment_date");
		$credit= $db->rp_getValue("sales_return","grand_total","id='".$invoice_id."'",0);
		$grand_total=$db->rp_getValue("sales_return","grand_total","id='".$invoice_id."'",0);
		$payment_date=date('Y-m-d');
		$payment_type = 0;
		$remark = "Sales Return Entry Of Sales Return No. <a target='_blank' href='sales_return_viewer.php?sales_return_id=".$invoice_id."'>". $db->rp_getValue("sales_return","invoice_no","id='".$invoice_id."'",0)."</a>";
		$Values=array($invoice_data_d['customer_id'],$AccountID,$AccountNo,$payment_type,$credit,$grand_total,$invoice_id,"sales_return",$remark,$payment_date);
		/*entry account transaction*/
		$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);
	}
	/*add transaction*/

	$reply=array("ack"=>1,"developer_msg"=>"Material Receive Date Update Successfully.","ack_msg"=>"Material Receive Date Update Successfully.");
	echo json_encode($reply);
}
else
{
	$reply=array("ack"=>0,"developer_msg"=>"Failed! Something went to wrong!!","ack_msg"=>"Failed! Something went to wrong!!");
	echo json_encode($reply);
}
require_once 'disconnect.php'; 
?>