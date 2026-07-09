<?php
require_once("main.class.php");
require_once("function.class.php");
class Payment extends Functions
{
	public $db;
	public $ctable="payment";
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
			"invoice_id",
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
			$invoice_id,
		);

		/*log entry*/
		$module_name = "Customer Receipt";
		$flag = "Web";
		$log_description = $module_name." ".$receipt_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/	
				
	 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"",$customer_id);
		if($uid!=0)
		{   
			/*$AccountID = $this->db->rp_getValue("account","id","cid='".$customer_id."' AND isDelete=0");
			$AccountNo = $this->db->rp_getValue("account","acc_no","cid='".$customer_id."' AND isDelete=0");
		 
			$cid = $this->db->rp_getValue("account","cid","isDelete=0 AND id='".$AccountID."'");
			
			$Columns=array("cid","account_id","account_no","type","credit","amount","reference_id","reference_table","description","payment_date");  
			$Values=array($cid,$AccountID,$AccountNo,$payment_type,$paid_amount,$paid_amount,$uid,"payment",$remark,$payment_date);
			$TransctionID=$this->rp_insert("account_transaction",$Values,$Columns,0);*/ 
			/*entry account transaction*/
			$reply=array("ack"=>1,"developer_msg"=>"Payment Add Successfully","ack_msg"=>"Success!Payment Add Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Payment Failed.");
			return $reply;
		} 
	}
	//-------------------------------------------------------------------------------------------//
	//--------#Update Payment Informstion------------------------------------------------//	


	public function UpdatePayment($detail,$id)
  	{
  		// print_r($detail);exit;

  		$old_invoice_id = $this->db->rp_getValue("payment","invoice_id","id='".$id."' AND isDelete=0",0);
  		$old_customer_id = $this->db->rp_getValue("payment","customer_id","id='".$id."' AND isDelete=0");
		 
		$where	= "id='".$id."'";
  		// echo $old_invoice_id;exit;
		$isUpdated=$this->db->rp_update($this->ctable,$detail,$where,0);
		if($isUpdated)
		{
			/*updte account transaction*/
			$account_id = $this->db->rp_getValue("account","id","cid='".$detail['customer_id']."' AND isDelete=0");
			$account_no = $this->db->rp_getValue("account","acc_no","cid='".$detail['customer_id']."' AND isDelete=0"); 

			
			$payment_detail = $this->db->rp_getData("payment","*","id='".$id."' AND isDelete=0");
			$payment_detail_R = mysqli_fetch_assoc($payment_detail);
			$paymentTypeArray = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
			$remark = "Payment receipt of ".$payment_detail_R['receipt_no']." ".$paymentTypeArray[$payment_detail_R['payment_type']]." ".$payment_detail_R['remark'];


			$update_array = array(
				"reference_table" => "payment",
				"reference_id"    => $id,
				"cid"             => $detail['customer_id'],
				"account_id"      => $account_id,
				"account_no"      => $account_no,
				"amount"          => $detail['paid_amount'],
				"credit"		  => $detail['paid_amount'],
				"type"            => 1,
				"description"     => $remark,
				"payment_date"     => $detail['payment_date'],
				"invoice_id"     => $detail['invoice_id'], 
			);
			
			$where1 = "cid='".$old_customer_id."' AND reference_id='".$id."' AND reference_table='payment'"; 

			$this->db->rp_update("account_transaction",$update_array,$where1,0);
			/*updte account transaction*/

			// upate invoice remaining
			if($detail['receipt_type']==2)
			{
				$invoice_amt = $this->db->rp_getValue("orders","grand_total_rounded","id='".$detail['invoice_id']."' AND isDelete=0");

				$invoice_tot_receipt_amt = $this->db->rp_getValue("account_transaction","SUM(credit)","invoice_id='".$detail['invoice_id']."' AND isDelete=0 AND reference_table='payment'");

				$invoice_remaining_amt = $invoice_amt - $invoice_tot_receipt_amt;

				$this->db->rp_update("orders",array("receipt_amount"=>$invoice_tot_receipt_amt,"remaining_amount"=>$invoice_remaining_amt),"id='".$detail['invoice_id']."'",0);

				$this->db->rp_update("account_transaction",array("remaining_amount"=>$invoice_remaining_amt),"reference_id='".$detail['invoice_id']."' AND reference_table='orders' AND isDelete=0",0);
			}
			// upate invoice remaining

			// old invoice entry update
			if($old_invoice_id!="" && $old_invoice_id>0 && $old_invoice_id!=$detail['invoice_id'])
			{
				// echo $old_invoice_id;exit;
				$invoice_amt = $this->db->rp_getValue("orders","grand_total_rounded","id='".$old_invoice_id."' AND isDelete=0");

				$invoice_tot_receipt_amt = $this->db->rp_getValue("account_transaction","SUM(credit)","invoice_id='".$old_invoice_id."' AND isDelete=0 AND reference_table='payment'",0);

				$invoice_tot_receipt_amt = ($invoice_tot_receipt_amt)?$invoice_tot_receipt_amt:0;

				$invoice_remaining_amt = $invoice_amt - $invoice_tot_receipt_amt;

				$this->db->rp_update("orders",array("receipt_amount"=>$invoice_tot_receipt_amt,"remaining_amount"=>$invoice_remaining_amt),"id='".$old_invoice_id."'",0);

				$this->db->rp_update("account_transaction",array("remaining_amount"=>$invoice_remaining_amt),"reference_id='".$old_invoice_id."' AND reference_table='orders' AND isDelete=0",0);
			}
			// old invoice entry update
 

			$reply=array("ack"=>1,"developer_msg"=>"Payment Update Successfull!!.","ack_msg"=>"Success! Payment Detail Updated Successfully.");
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
		$receipt_no = $this->db->rp_getValue("payment","receipt_no","id='".$id."'",0);
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

			
			$payment_detail = $this->db->rp_getData("payment","*","id='".$id."' AND isDelete=0");
			$payment_detail_R = mysqli_fetch_assoc($payment_detail);
			$paymentTypeArray = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
			$remark = "Payment receipt of ".$payment_detail_R['receipt_no']." ".$paymentTypeArray[$payment_detail_R['payment_type']]." ".$payment_detail_R['remark'];


			$update_array = array(
				"reference_table" => "payment",
				"reference_id"    => $id,
				"cid"             => $customer_id,
				"account_id"      => $account_id,
				"account_no"      => $account_no,
				"amount"          => $paid_amount,
				"credit"		  => $paid_amount,
				"type"            => 1,
				"description"     => $remark,
				"payment_date"     => $payment_date,
			);
			
			$where1 = "cid='".$customer_id."' AND reference_id='".$id."' AND reference_table='payment'";




			$isUpdated=$this->db->rp_update("account_transaction",$update_array,$where1,0);
			$reply=array("ack"=>1,"developer_msg"=>"Payment Update Successfull!!.","ack_msg"=>"Success! Payment Detail Updated Successfully.");
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
			$result['invoice_id']		= htmlentities($ctable_d['invoice_id']); 
		$result['receipt_type']		= htmlentities($ctable_d['receipt_type']); 

		$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Sales Officer Successfully.","result"=>$result);
		return $reply;
	
	}
	//------------------------------------------------------------------------------------//+
	//--------Delete  payment------------------------------------------------------//	
	public function DeletePayment($detail)
	{
		$old_invoice_id = $this->db->rp_getValue("payment","invoice_id","id='".$_REQUEST['id']."' AND isDelete=0");
		$receipt_type = $this->db->rp_getValue("payment","receipt_type","id='".$_REQUEST['id']."' AND isDelete=0");

		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='".$_REQUEST['id']."'";
		$uid=$this->db->rp_update($this->ctable,$rows,$where);
		if($uid!=0)
		{
			$customer_id = $this->db->rp_getValue("payment","customer_id","id='".$_REQUEST['id']."'",0);
			$where1 = "cid='".$customer_id."' AND reference_id='".$_REQUEST['id']."' AND reference_table='payment'";

			$isUpdated=$this->db->rp_delete("account_transaction",$where1,0);


			// old invoice entry update
			if($old_invoice_id!="" && $old_invoice_id>0 && $receipt_type==2)
			{
				$invoice_amt = $this->db->rp_getValue("orders","grand_total_rounded","id='".$old_invoice_id."' AND isDelete=0");

				$invoice_tot_receipt_amt = $this->db->rp_getValue("account_transaction","SUM(credit)","invoice_id='".$old_invoice_id."' AND isDelete=0 AND reference_table='payment'");

				$invoice_tot_receipt_amt = ($invoice_tot_receipt_amt)?$invoice_tot_receipt_amt:0;

				$invoice_remaining_amt = $invoice_amt - $invoice_tot_receipt_amt;

				$this->db->rp_update("orders",array("receipt_amount"=>$invoice_tot_receipt_amt,"remaining_amount"=>$invoice_remaining_amt),"id='".$old_invoice_id."'",0);

				$this->db->rp_update("account_transaction",array("remaining_amount"=>$invoice_remaining_amt),"reference_id='".$old_invoice_id."' AND reference_table='invoice' AND isDelete=0",0);
			}
			// old invoice entry update
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