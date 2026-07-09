<?php
require_once("main.class.php");
require_once("function.class.php");
class GeneralCredit extends Functions
{
	public $db;
	public $ctable="general_credit_note";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
    } 
	//----------#Insert Payment Information--------------------------------// 	
	public function InsertPayment($detail)
	{
	    extract($detail);
		 
		$value=$this->db->getlastInsertId($this->ctable);
		$receipt_no=RECEIPT_NO.str_pad($value, 3, '0', STR_PAD_LEFT); 
 
		$adate	= date('Y-m-d H:i:s');
		$rows 	= array(
			"customer_type",
			"customer_id",
			"sales_executive_id", 
			"paid_amount",
			"payment_date",
			"payment_type",
			"remark", 
			"receipt_no",
			"cheque_no",
			"receipt_type", 
			"discount_type_id", 
			"ref_no", 
		);
		$values = array(
			$customer_type,
			$customer_id,
			$sales_executive_id, 
			$paid_amount,
			$payment_date,
			$payment_type,
			$remark, 
			$receipt_no,
			$cheque_no,
			$receipt_type, 
			$discount_type_id, 
			$ref_no, 
		);

		/*log entry*/
		$module_name = "General Credit";
		$flag = "Web";
		$log_description = $module_name." ".$receipt_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/	
				
	 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"",$customer_id);
		if($uid!=0)
		{     
			$reply=array("ack"=>1,"developer_msg"=>"Data Add Successfully","ack_msg"=>"Success!Data Add Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Failed.");
			return $reply;
		} 
	}
	//-------------------------------------------------------------------------------------------//
	//--------#Update Payment Informstion------------------------------------------------//	


	public function UpdatePayment($detail,$id)
  	{ 
		$where	= "id='".$id."'";
  		// echo $old_invoice_id;exit;
		$isUpdated=$this->db->rp_update($this->ctable,$detail,$where,0);
		if($isUpdated)
		{
			/*updte account transaction*/
			$account_id = $this->db->rp_getValue("account","id","cid='".$detail['customer_id']."' AND isDelete=0");
			$account_no = $this->db->rp_getValue("account","acc_no","cid='".$detail['customer_id']."' AND isDelete=0"); 

			
			$payment_detail = $this->db->rp_getData($this->ctable,"*","id='".$id."' AND isDelete=0");
			$payment_detail_R = mysqli_fetch_assoc($payment_detail);
			$paymentTypeArray = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
			$remark = "General Credit of ".$payment_detail_R['receipt_no']." ".$paymentTypeArray[$payment_detail_R['payment_type']]." ".$payment_detail_R['remark'];


			$update_array = array(
				"reference_table" => "general_credit_note",
				"reference_id"    => $id,
				"cid"             => $detail['customer_id'],
				"account_id"      => $account_id,
				"account_no"      => $account_no,
				"amount"          => $detail['paid_amount'],
				"debit"		  	  => "-".$detail['paid_amount'],
				"type"            => 2,
				"description"     => $remark,
				"payment_date"     => $detail['payment_date'],
				"discount_type_id"     => $detail['discount_type_id'],
				"ref_no"     => $detail['ref_no'],

			);
			
			$where1 = "cid='".$old_customer_id."' AND reference_id='".$id."' AND reference_table='general_credit_note'"; 

			$this->db->rp_update("account_transaction",$update_array,$where1,0);
			/*updte account transaction*/

			$reply=array("ack"=>1,"developer_msg"=>"Data Update Successfull!!.","ack_msg"=>"Success! Data Detail Updated Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Failed.");
			return $reply;
		}
			
	}

	public function UpdatePayment1($detail)
  	{
  		// print_r($detail);exit;
		extract($detail); 
		$rows 	= array(
			"customer_type"			=> $customer_type,	
			"customer_id"			=> $customer_id,	
			"sales_executive_id"	=> $sales_executive_id,	
			"paid_amount"			=> $paid_amount,	
			"payment_date"			=> date('Y-m-d',strtotime($payment_date)),	
			"payment_type"			=> $payment_type,
			"remark"		        => $remark,
			"cheque_no"		        => $cheque_no,
		);

		/*log entry*/
		$receipt_no = $this->db->rp_getValue($this->ctable,"receipt_no","id='".$id."'",0);
		$module_name = "Customer Receipt";
		$flag = "Web";
		$log_description = $module_name." ".$receipt_no." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/

		$where	= "id='".$id."'";
		$isUpdated=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,$sales_executive_id,$customer_id);
		if($isUpdated)
		{
			$account_id = $this->db->rp_getValue("account","id","cid='".$customer_id."' AND isDelete=0");
			$account_no = $this->db->rp_getValue("account","acc_no","cid='".$customer_id."' AND isDelete=0"); 

			
			$payment_detail = $this->db->rp_getData($this->ctable,"*","id='".$id."' AND isDelete=0");
			$payment_detail_R = mysqli_fetch_assoc($payment_detail);
			$paymentTypeArray = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
			$remark = "Payment receipt of ".$payment_detail_R['receipt_no']." ".$paymentTypeArray[$payment_detail_R['payment_type']]." ".$payment_detail_R['remark'];


			$update_array = array(
				"reference_table" => "general_credit_note",
				"reference_id"    => $id,
				"cid"             => $customer_id,
				"account_id"      => $account_id,
				"account_no"      => $account_no,
				"amount"          => $paid_amount,
				"debit"		      => $paid_amount,
				"type"            => 1,
				"description"     => $remark,
				"payment_date"     => $payment_date,
			);
			
			$where1 = "cid='".$customer_id."' AND reference_id='".$id."' AND reference_table='general_credit_note'";

			$isUpdated=$this->db->rp_update("account_transaction",$update_array,$where1,0);
			$reply=array("ack"=>1,"developer_msg"=>"Data Update Successfull!!.","ack_msg"=>"Success! Data Detail Updated Successfully.");
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
		$result['customer_type']			= htmlentities($ctable_d['customer_type']);
		$result['customer_id']				= htmlentities($ctable_d['customer_id']);
		$result['sales_executive_id']		= stripslashes($ctable_d['sales_executive_id']);
		$result['paid_amount']				= stripslashes($ctable_d['paid_amount']);
		$result['payment_date']				= date('d-m-Y',strtotime($ctable_d['payment_date']));
		$result['remark']					= stripslashes($ctable_d['remark']);
		$result['payment_type']				= htmlentities($ctable_d['payment_type']); 
		$result['cheque_no']				= htmlentities($ctable_d['cheque_no']);  
		$result['receipt_type']		= htmlentities($ctable_d['receipt_type']); 
		$result['discount_type_id']		= htmlentities($ctable_d['discount_type_id']); 
		$result['ref_no']				= stripslashes($ctable_d['ref_no']);

		$reply=array("ack"=>1,"developer_msg"=>"Data detail fetched!!.","ack_msg"=>"Success! Update Data Successfully.","result"=>$result);
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
			$customer_id = $this->db->rp_getValue($this->ctable,"customer_id","id='".$_REQUEST['id']."'",0);
			$where1 = "cid='".$customer_id."' AND reference_id='".$_REQUEST['id']."' AND reference_table='general_credit_note'";

			$isUpdated=$this->db->rp_delete("account_transaction",$where1,0);
 
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Data Successfully.");
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