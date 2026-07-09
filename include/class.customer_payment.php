<?php
require_once("main.class.php");
require_once("function.class.php");
class CustomerPayment extends Functions
{
	public $db;
	public $ctable="customer_payment";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
    } 
//----------#Insert Payment Information--------------------------------// 	
	 public function InsertCustomerPayment($detail)
	 {
		extract($detail);
		$payment_date=date('Y-m-d',strtotime($detail['payment_date']));
					$order_id=$this->db->rp_getValue("dispatch_detail","order_id","id='".$dispatch_id."'");
					$value=$this->db->getlastInsertId($this->ctable);
					$receipt_no=RECEIPT_NO.str_pad($value, 3, '0', STR_PAD_LEFT);
			 $adate	= date('Y-m-d H:i:s');
				$rows 	= array(
						"customer_id",
						"dispatch_id",
						"order_id",
						"receipt_no",
						"paid_amount",
						"payment_date",
						"payment_type",
						"remark",
					);
			$values = array(
						$customer_id,
						$dispatch_id,
						$order_id,
						$receipt_no,
						$paid_amount,
						$payment_date,
						$payment_type,
						$remark,
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
			if($uid!=0)
			{
				$remaining_payment=$this->db->rp_getValue("dispatch_detail","remaining_amount","customer_id='".$customer_id."' AND id='".$dispatch_id."'",0);
				$paid_payment=$this->db->rp_getValue("dispatch_detail","paid_amount","customer_id='".$customer_id."' AND id='".$dispatch_id."'",0);
				$final_paid_amount=$paid_payment+$paid_amount;
				$remaining_amount=$remaining_payment - $paid_amount;
				if($remaining_amount==0)
				{
					$payment_status=1;
				}
				else
				{
					$payment_status=0;
				}
				$rows 	= array(
							"remaining_amount"	=> $remaining_amount,
							"paid_amount"			=> $final_paid_amount,	
							"payment_status"			=> $payment_status,
						);
				$where	= "user_id='".$user_id."' AND id='".$dispatch_id."'";
				$isUpdated=$this->db->rp_update("dispatch_detail",$rows,$where,0);
				
							$rows 	= array(
							"amount_remaining_that_time"	=> $remaining_amount,
							"amount_paid_that_time"			=> $paid_amount,
							"payment_status"			=> $payment_status,
						);
				$where	= "id='".$uid."'";
				$Updated=$this->db->rp_update($this->ctable,$rows,$where,0);
				$reply=array("ack"=>1,"developer_msg"=>"Payment Successfully","ack_msg"=>"Success!Payment Successfully.");
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
	public function UpdateCustomerPayment($detail)
	  {
		extract($detail);
		$customer_id=$this->db->rp_getValue("customer_payment","customer_id","id='".$id."'",0);
		$dispatch_id=$this->db->rp_getValue("customer_payment","dispatch_id","id='".$id."'",0);
				$rows 	= array(
							"paid_amount"			=> $paid_amount,	
							"payment_date"			=> $payment_date,	
							"payment_type"			=> $payment_type,
							"remark"		=> $remark,
						);
				$where	= "id='".$id."'";
				$isUpdated=$this->db->rp_update($this->ctable,$rows,$where,0);
				
				if($isUpdated)
				{
					$remaining_payment=$this->db->rp_getValue("dispatch_detail","remaining_amount","customer_id='".$customer_id."' AND id='".$dispatch_id."'",0);
					
					$paid_payment=$this->db->rp_getValue("dispatch_detail","paid_amount","customer_id='".$customer_id."' AND id='".$dispatch_id."'",0);
					$paid=$paid_payment - $old_paid_amount;
					$final_paid_amount=$paid + $paid_amount;
					
					$remaining_amount=$remaining_payment + $old_paid_amount;
					$final_remaining_amount=$remaining_amount - $paid_amount;
					if($final_remaining_amount==0)
					{
						$payment_status=1;
					}
					else
					{
						$payment_status=0;
					}
					$rows 	= array(
								"remaining_amount"	=> $final_remaining_amount,
								"paid_amount"			=> $final_paid_amount,	
								"payment_status"			=> $payment_status,
							);
					$where	= "customer_id='".$customer_id."' AND id='".$dispatch_id."'";
					$isUpdated=$this->db->rp_update("dispatch_detail",$rows,$where,0);
					
								$rows 	= array(
								"amount_remaining_that_time"	=> $final_remaining_amount,
								"amount_paid_that_time"			=> $final_paid_amount,
								"payment_status"			=> $payment_status,
							);
					$where	= "id='".$_REQUEST['id']."'";
					$Updated=$this->db->rp_update($this->ctable,$rows,$where,0);
					$reply=array("ack"=>1,"developer_msg"=>"Payment Update Successfull!!.","ack_msg"=>"Success! Payment Detail Updated Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Failed.");
					return $reply;
				}
			
		}
//--------------------------------------------------------------------------------------------------------//

	public function EditCustomerPayment($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		
		$result=array();
		$result['customer_id']		= htmlentities($ctable_d['customer_id']);
		$result['dispatch_id']		= stripslashes($ctable_d['dispatch_id']);
		$result['paid_amount']		= stripslashes($ctable_d['paid_amount']);
		$result['payment_date']		= stripslashes($ctable_d['payment_date']);
		$result['remark']		= stripslashes($ctable_d['remark']);
		$result['payment_type']		= htmlentities($ctable_d['payment_type']);
		//for edit payment get remaning payment and calculate paid + remaining payment//
		$remaining_payment=$this->db->rp_getValue("dispatch_detail","remaining_amount","id='".$ctable_d['dispatch_id']."'");
		$result['amount_remaining_that_time']		= $remaining_payment + $ctable_d['paid_amount'];
		
		$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Sales Officer Successfully.","result"=>$result);
		return $reply;
	
	}
//------------------------------------------------------------------------------------//+
//--------Delete  payment------------------------------------------------------//	
	public function DeleteCustomerPayment($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$dispatch_id=$this->db->rp_getValue($this->ctable,"dispatch_id","id='".$_REQUEST['id']."'",0);
				$paid_amount=$this->db->rp_getValue($this->ctable,"paid_amount","id='".$_REQUEST['id']."'",0);
				$dispatch_detail=$this->db->rp_getValue("dispatch_detail","remaining_amount","id='".$dispatch_id."'",0);
				$paid=$this->db->rp_getValue("dispatch_detail","paid_amount","id='".$dispatch_id."'");
				$final_paid=$paid-$paid_amount;
				$remaining_amount=$dispatch_detail+$paid_amount;
					$rows 	= array(
							"remaining_amount"	=> $remaining_amount,
							"paid_amount"			=> $final_paid,
							"payment_status"			=> '0',
						);
				$where	= "id='".$dispatch_id."'";
				$updated=$this->db->rp_update("dispatch_detail",$rows,$where,0);
				
				$rows 	= array(
							"amount_remaining_that_time"	=> $remaining_amount,
							"amount_paid_that_time"			=> $final_paid,
							"payment_status"			=> '0',
						);
				$where	= "id='".$_REQUEST['id']."'";
				$updated=$this->db->rp_update("customer_payment",$rows,$where,0);
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Customer Successfully.");
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