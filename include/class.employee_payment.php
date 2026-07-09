<?php
require_once("main.class.php");
require_once("function.class.php");
class EmployeePayment extends Functions
{
	public $db;
	public $ctable="employee_payment";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
    } 
	//----------#Insert Payment Information--------------------------------// 	
	public function InsertPayment($detail)
	{
		// echo "<pre>"; print_r($detail); exit;
	    extract($detail);
		$payment_date=date('Y-m-d',strtotime($detail['payment_date']));
		 
		$value=$this->db->getlastInsertId($this->ctable);
		$receipt_no=EMPLOYEE_RECEIPT_NO.str_pad($value, 3, '0', STR_PAD_LEFT); 
 
		$adate	= date('Y-m-d H:i:s');
		$rows 	= array(
			"sales_type",
			"sales_id",
			"sales_executive_id", 
			"paid_amount",
			"payment_date",
			"payment_type",
			"remark", 
			"receipt_no",
			"cheque_no",
		);
		$values = array(
			$sales_type,
			$sale_id,
			$sales_executive_id, 
			$paid_amount,
			$payment_date,
			$payment_type,
			$remark, 
			$receipt_no,
			$cheque_no,
		);

		/*log entry*/
		$module_name = "Employee Receipt";
		$flag = "Web";
		$log_description = $module_name." ".$receipt_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/	
				
	 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"",$sale_id);
		if($uid!=0)
		{   
			/*$AccountID = $this->db->rp_getValue("account","id","cid='".$sale_id."' AND isDelete=0");
			$AccountNo = $this->db->rp_getValue("account","acc_no","cid='".$customer_id."' AND isDelete=0");
		 
			$cid = $this->db->rp_getValue("account","cid","isDelete=0 AND id='".$AccountID."'");
			
			$Columns=array("cid","account_id","account_no","type","credit","amount","reference_id","reference_table","description","payment_date");  
			$Values=array($cid,$AccountID,$AccountNo,$payment_type,$paid_amount,$paid_amount,$uid,"payment",$remark,$payment_date);
			$TransctionID=$this->rp_insert("account_transaction",$Values,$Columns,0);*/ 
			/*entry account transaction*/
			$reply=array("ack"=>1,"developer_msg"=>"Employee Payment Add Successfully","ack_msg"=>"Success! Employee Payment Add Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Employee Payment Failed.");
			return $reply;
		} 
	}
	//-------------------------------------------------------------------------------------------//
	//--------#Update Payment Informstion------------------------------------------------//	 
	public function UpdatePayment($detail)
  	{
  		// print_r($detail);exit;
		extract($detail); 
		$rows 	= array(
			"sales_type"			=> $sales_type,	
			"sales_id"			=> $sale_id,	
			"sales_executive_id"	=> $sales_executive_id,	
			"paid_amount"			=> $paid_amount,	
			"payment_date"			=> date('Y-m-d',strtotime($payment_date)),	
			"payment_type"			=> $payment_type,
			"remark"		        => $remark,
			"cheque_no"		        => $cheque_no,
		);

		/*log entry*/
		$receipt_no = $this->db->rp_getValue("employee_payment","receipt_no","id='".$id."'",0);
		$module_name = "Employee Receipt";
		$flag = "Web";
		$log_description = $module_name." ".$receipt_no." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/

		$where	= "id='".$id."'";
		$isUpdated=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,$sales_executive_id,$customer_id);
		if($isUpdated)
		{
			// $account_id = $this->db->rp_getValue("account","id","cid='".$sale_id."' AND isDelete=0");
			// $account_no = $this->db->rp_getValue("account","acc_no","cid='".$sale_id."' AND isDelete=0"); 

			
			$payment_detail = $this->db->rp_getData("employee_payment","*","id='".$id."' AND isDelete=0");
			$payment_detail_R = mysqli_fetch_assoc($payment_detail);
			$paymentTypeArray = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
			$remark = "Payment receipt of ".$payment_detail_R['receipt_no']." ".$paymentTypeArray[$payment_detail_R['payment_type']]." ".$payment_detail_R['remark'];


			$update_array = array(
				"reference_table" => "employee_payment",
				"reference_id"    => $id,
				"sales_id"        => $sale_id,
				"account_id"      => $sale_id,
				// "account_no"      => $account_no,
				"amount"          => $paid_amount,
				// "credit"		  => $paid_amount,
				"debit"		  => "-".$paid_amount,
				
				"type"            => 1,
				"description"     => $remark,
				"payment_date"     => date('Y-m-d',strtotime($payment_date)),
			);
			
			$where1 = "sales_id='".$sale_id."' AND reference_id='".$id."' AND reference_table='employee_payment'";




			$isUpdated=$this->db->rp_update("employee_account_transaction",$update_array,$where1,0);
			$reply=array("ack"=>1,"developer_msg"=>"Employee Payment Update Successfull!!.","ack_msg"=>"Success! Employee Payment Detail Updated Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Failed.");
			return $reply;
		}
			
	}
	//---- ----------------------------------------------------------------------------------------// 
	public function EditPayment($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		
		$result=array();
		$result['sales_executive_type']			= htmlentities($ctable_d['sales_type']);
		$result['sales_id']				= htmlentities($ctable_d['sales_id']);
		$result['sales_executive_id']		= stripslashes($ctable_d['sales_executive_id']);
		$result['paid_amount']				= stripslashes($ctable_d['paid_amount']);
		$result['payment_date']				= date('d-m-Y',strtotime($ctable_d['payment_date']));
		$result['remark']					= stripslashes($ctable_d['remark']);
		$result['payment_type']				= htmlentities($ctable_d['payment_type']); 
		$result['cheque_no']				= htmlentities($ctable_d['cheque_no']); 

		$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Sales Officer Successfully.","result"=>$result);
		return $reply;
	
	}
	//------------------------------------------------------------------------------------//+
	//--------Delete  payment------------------------------------------------------//	
	public function DeletePayment($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='".$_REQUEST['id']."'";
		$uid=$this->db->rp_update($this->ctable,$rows,$where);
		if($uid!=0)
		{
			$sale_id = $this->db->rp_getValue("employee_payment","sale_id","id='".$_REQUEST['id']."'",0);
			$where1 = "sales_id='".$sale_id."' AND reference_id='".$_REQUEST['id']."' AND reference_table='employee_payment'";

			$isUpdated=$this->db->rp_delete("account_transaction",$where1,0);
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Sales Officer Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete record Failed.");
			return $reply;
		}
	}
	//-----------------------------------------------------------------------------------------------// 
} 
?>