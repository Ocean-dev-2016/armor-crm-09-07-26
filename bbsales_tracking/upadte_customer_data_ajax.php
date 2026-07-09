<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$mode = $_REQUEST['mode'];
$sub_mode = $_REQUEST['sub_mode'];
if($mode=="quotation")
{
	if($sub_mode=="only_quotation")
	{
		$company_name    = ($_REQUEST['company_name'])?$_REQUEST['company_name']:"";
		$customer_name    = ($_REQUEST['person_name'])?$_REQUEST['person_name']:"";
		$contact_number   = ($_REQUEST['phone'])?$_REQUEST['phone']:"";
		$email   		  = ($_REQUEST['email'])?$_REQUEST['email']:"";
		$address          = ($_REQUEST['address'])?$_REQUEST['address']:"";
		$state            = ($_REQUEST['state'])?$_REQUEST['state']:"";
		$gst              = ($_REQUEST['gst'])?$_REQUEST['gst']:"";
		$shipping_address = ($_REQUEST['billing_address'])?$_REQUEST['billing_address']:"";
		$billing_address  = ($_REQUEST['shipping_address'])?$_REQUEST['shipping_address']:"";

		$quotation_count = $db->rp_getTotalRecord("quotation_detail","id='".$_REQUEST['edit_id']."' AND customer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
		if($quotation_count>0)
		{
			$update_Rows = array("company_name"=>$company_name,"customer_name"=>$customer_name,"contact_number"=>$contact_number,"email"=>$email,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address);
			$update = $db->rp_update("quotation_detail",$update_Rows,"id='".$_REQUEST['edit_id']."' AND isDelete=0",0);
			if($update)
			{
				$reply=array("ack"=>1,"ack_msg"=>"Data Updated Successfully");
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Data Updated Failed");	
			}
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Such Data Found");	
		}	
	}

	if($sub_mode=="quotation_with_customer")
	{
		$company_name    = ($_REQUEST['company_name'])?$_REQUEST['company_name']:"";
		$customer_name    = ($_REQUEST['person_name'])?$_REQUEST['person_name']:"";
		$contact_number   = ($_REQUEST['phone'])?$_REQUEST['phone']:"";
		$email   		  = ($_REQUEST['email'])?$_REQUEST['email']:"";
		$address          = ($_REQUEST['address'])?$_REQUEST['address']:"";
		$state            = ($_REQUEST['state'])?$_REQUEST['state']:"";
		$gst              = ($_REQUEST['gst'])?$_REQUEST['gst']:"";
		$shipping_address = ($_REQUEST['billing_address'])?$_REQUEST['billing_address']:"";
		$billing_address  = ($_REQUEST['shipping_address'])?$_REQUEST['shipping_address']:"";


		$quotation_count = $db->rp_getTotalRecord("quotation_detail","id='".$_REQUEST['edit_id']."' AND customer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
		if($quotation_count>0)
		{
			$update_Rows = array("company_name"=>$company_name,"customer_name"=>$customer_name,"contact_number"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
			$update = $db->rp_update("quotation_detail",$update_Rows,"id='".$_REQUEST['edit_id']."' AND isDelete=0",0);
			if($update)
			{
				/*update in customer */
				$update_Customer_Rows = array("company_name"=>$company_name,"cname"=>$customer_name,"phone"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
				$updateCustomer = $db->rp_update("executive",$update_Customer_Rows,"id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
				/*update in customer */
				$reply=array("ack"=>1,"ack_msg"=>"Data Updated Successfully");
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Data Updated Failed");	
			}
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Such Data Found");	
		}	
	}
	
	echo json_encode($reply);
}

if($mode=="order")
{
	if($sub_mode=="only_order")
	{
		$company_name    = ($_REQUEST['company_name'])?$_REQUEST['company_name']:"";
		$customer_name    = ($_REQUEST['person_name'])?$_REQUEST['person_name']:"";
		$contact_number   = ($_REQUEST['phone'])?$_REQUEST['phone']:"";
		$email   		  = ($_REQUEST['email'])?$_REQUEST['email']:"";
		$address          = ($_REQUEST['address'])?$_REQUEST['address']:"";
		$state            = ($_REQUEST['state'])?$_REQUEST['state']:"";
		$gst              = ($_REQUEST['gst'])?$_REQUEST['gst']:"";
		$shipping_address = ($_REQUEST['billing_address'])?$_REQUEST['billing_address']:"";
		$billing_address  = ($_REQUEST['shipping_address'])?$_REQUEST['shipping_address']:"";


		$quotation_count = $db->rp_getTotalRecord("orders","id='".$_REQUEST['edit_id']."' AND customer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
		if($quotation_count>0)
		{
			$update_Rows = array("company_name"=>$company_name,"customer_name"=>$customer_name,"contact_number"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
			$update = $db->rp_update("orders",$update_Rows,"id='".$_REQUEST['edit_id']."' AND isDelete=0",0);
			if($update)
			{
				$reply=array("ack"=>1,"ack_msg"=>"Data Updated Successfully");
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Data Updated Failed");	
			}
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Such Data Found");	
		}	
	}

	if($sub_mode=="order_with_customer")
	{
		$company_name    = ($_REQUEST['company_name'])?$_REQUEST['company_name']:"";
		$customer_name    = ($_REQUEST['person_name'])?$_REQUEST['person_name']:"";
		$contact_number   = ($_REQUEST['phone'])?$_REQUEST['phone']:"";
		$email   		  = ($_REQUEST['email'])?$_REQUEST['email']:"";
		$address          = ($_REQUEST['address'])?$_REQUEST['address']:"";
		$state            = ($_REQUEST['state'])?$_REQUEST['state']:"";
		$gst              = ($_REQUEST['gst'])?$_REQUEST['gst']:"";
		$shipping_address = ($_REQUEST['billing_address'])?$_REQUEST['billing_address']:"";
		$billing_address  = ($_REQUEST['shipping_address'])?$_REQUEST['shipping_address']:"";


		$quotation_count = $db->rp_getTotalRecord("orders","id='".$_REQUEST['edit_id']."' AND customer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
		if($quotation_count>0)
		{
			$update_Rows = array("company_name"=>$company_name,"customer_name"=>$customer_name,"contact_number"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
			$update = $db->rp_update("orders",$update_Rows,"id='".$_REQUEST['edit_id']."' AND isDelete=0",0);
			if($update)
			{
				/*update in customer */
				$update_Customer_Rows = array("company_name"=>$company_name,"cname"=>$customer_name,"phone"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
				$updateCustomer = $db->rp_update("executive",$update_Customer_Rows,"id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
				/*update in customer */
				$reply=array("ack"=>1,"ack_msg"=>"Data Updated Successfully");
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Data Updated Failed");	
			}
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Such Data Found");	
		}	
	}
	
	echo json_encode($reply);
}

if($mode=="invoice")
{
	if($sub_mode=="only_invoice")
	{
		$company_name    = ($_REQUEST['company_name'])?$_REQUEST['company_name']:"";
		$customer_name    = ($_REQUEST['person_name'])?$_REQUEST['person_name']:"";
		$contact_number   = ($_REQUEST['phone'])?$_REQUEST['phone']:"";
		$email   		  = ($_REQUEST['email'])?$_REQUEST['email']:"";
		$address          = ($_REQUEST['address'])?$_REQUEST['address']:"";
		$state            = ($_REQUEST['state'])?$_REQUEST['state']:"";
		$gst              = ($_REQUEST['gst'])?$_REQUEST['gst']:"";
		$shipping_address = ($_REQUEST['billing_address'])?$_REQUEST['billing_address']:"";
		$billing_address  = ($_REQUEST['shipping_address'])?$_REQUEST['shipping_address']:"";


		$quotation_count = $db->rp_getTotalRecord("invoice_new","id='".$_REQUEST['edit_id']."' AND customer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
		if($quotation_count>0)
		{
			$update_Rows = array("company_name"=>$company_name,"customer_name"=>$customer_name,"contact_number"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
			$update = $db->rp_update("invoice_new",$update_Rows,"id='".$_REQUEST['edit_id']."' AND isDelete=0",0);
			if($update)
			{
				$reply=array("ack"=>1,"ack_msg"=>"Data Updated Successfully");
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Data Updated Failed");	
			}
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Such Data Found");	
		}	
	}

	if($sub_mode=="invoice_with_customer")
	{
		$company_name    = ($_REQUEST['company_name'])?$_REQUEST['company_name']:"";
		$customer_name    = ($_REQUEST['person_name'])?$_REQUEST['person_name']:"";
		$contact_number   = ($_REQUEST['phone'])?$_REQUEST['phone']:"";
		$email   		  = ($_REQUEST['email'])?$_REQUEST['email']:"";
		$address          = ($_REQUEST['address'])?$_REQUEST['address']:"";
		$state            = ($_REQUEST['state'])?$_REQUEST['state']:"";
		$gst              = ($_REQUEST['gst'])?$_REQUEST['gst']:"";
		$shipping_address = ($_REQUEST['billing_address'])?$_REQUEST['billing_address']:"";
		$billing_address  = ($_REQUEST['shipping_address'])?$_REQUEST['shipping_address']:"";


		$quotation_count = $db->rp_getTotalRecord("invoice_new","id='".$_REQUEST['edit_id']."' AND customer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
		if($quotation_count>0)
		{
			$update_Rows = array("company_name"=>$company_name,"customer_name"=>$customer_name,"contact_number"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
			$update = $db->rp_update("invoice_new",$update_Rows,"id='".$_REQUEST['edit_id']."' AND isDelete=0",0);
			if($update)
			{
				/*update in customer */
				$update_Customer_Rows = array("company_name"=>$company_name,"cname"=>$customer_name,"phone"=>$contact_number,"address"=>$address,"state"=>$state,"gst"=>$gst,"shipping_address"=>$shipping_address,"billing_address"=>$billing_address,"email"=>$email);
				$updateCustomer = $db->rp_update("executive",$update_Customer_Rows,"id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
				/*update in customer */
				$reply=array("ack"=>1,"ack_msg"=>"Data Updated Successfully");
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Data Updated Failed");	
			}
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No Such Data Found");	
		}	
	}
	
	echo json_encode($reply);
}
require_once 'disconnect.php'; 
?>