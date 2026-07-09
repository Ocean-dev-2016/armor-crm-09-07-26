<?php
$page_id=635;$page_slug='employee_payment';
include("connect.php");
require_once("../include/class.employee_payment.php");
$objEmployeePayment= new EmployeePayment();
$m=$_REQUEST['m'];
$id=$_REQUEST['id'];
if($id!="" && $m!="")
{	
	if($m=="approve")
	{	
	    if($id)
		{  
			/*log entry*/
			$receipt_no = $db->rp_getValue("employee_payment","receipt_no","id='".$id."'",0);
			$sales_id = $db->rp_getValue("employee_payment","sales_id","id='".$id."'");
			$sales_executive_id = $db->rp_getValue("employee_payment","sales_executive_id","id='".$id."'");
			$ctable = "employee_payment";
			$last_id = $id;
			$flag = "Web";
		    $module_name = "Employee Receipt";
		    $log_description = $module_name." ".$receipt_no." Status Change To Approve By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
		    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$sales_id);
			/*log entry*/

			$update = $db->rp_update("employee_payment",array("payment_status"=>1),"id='".$id."'",0,$log_description,$flag,$module_name,$sales_executive_id,$sales_id);
			if($update)
			{

				$payment_detail = $db->rp_getData("employee_payment","*","id='".$id."' AND isDelete=0");
				$payment_detail_R = mysqli_fetch_assoc($payment_detail);

				$sales_id =  $db->rp_getValue("employee_payment","sales_id","id='".$id."' AND isDelete=0");

				// $AccountID   = $db->rp_getValue("account","id","cid='".$customer_id."' AND isDelete=0");
				// $AccountNo   = $db->rp_getValue("account","acc_no","cid='".$customer_id."' AND isDelete=0");
			 
				// $cid = $db->rp_getValue("account","cid","isDelete=0 AND id='".$AccountID."'");
				
				$Columns=array("sales_id","account_id","sales_type","type","debit","amount","reference_id","reference_table","description","payment_date");  

				$paymentTypeArray = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
				$payment_detail_R['remark'] = "Payment receipt of ".$payment_detail_R['receipt_no']." ".$paymentTypeArray[$payment_detail_R['payment_type']]." ".$payment_detail_R['remark'];


				$Values=array($sales_id,$sales_id,$payment_detail_R['sales_type'],1,"-".$payment_detail_R['paid_amount'],$payment_detail_R['paid_amount'],$id,"employee_payment",$payment_detail_R['remark'],$payment_detail_R['payment_date']);
				/*entry account transaction*/
				$TransctionID=$db->rp_insert("employee_account_transaction",$Values,$Columns,0);
			}
			$reply=array("ack"=>1,"ack_msg"=>"Employee Payment Approve Successfully");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"Employee Payment Not Approve.Please Try again!!");
		} 
	}

	if($m=="sales_payment_approve")
	{	
	    if($id)
		{  
			$Sales_update = $db->rp_update("sales_payment",array("payment_status"=>1),"id='".$id."'",0);
			if($Sales_update)
			{

				$sales_payment_detail = $db->rp_getData("sales_payment","*","id='".$id."' AND isDelete=0");
				$sales_payment_detail_R = mysqli_fetch_assoc($sales_payment_detail);

				$sales_executive_id =  $db->rp_getValue("sales_payment","sales_executive_id","id='".$id."' AND isDelete=0");

				$Sales_AccountID   = $db->rp_getValue("sales_account","id","cid='".$sales_executive_id."' AND isDelete=0");
				$Sales_AccountNo   = $db->rp_getValue("sales_account","acc_no","cid='".$sales_executive_id."' AND isDelete=0");
			 
				$salesid = $db->rp_getValue("sales_account","cid","isDelete=0 AND id='".$Sales_AccountID."'");
				
				$Columns=array("cid","account_id","account_no","type","debit","amount","reference_id","reference_table","description","payment_date");  
				
				$Values=array($salesid,$Sales_AccountID,$Sales_AccountNo,1,(-$sales_payment_detail_R['paid_amount']),$sales_payment_detail_R['paid_amount'],$id,"sales_payment",$sales_payment_detail_R['remark'],$sales_payment_detail_R['payment_date']);
				/*entry account transaction*/
				$TransctionID=$db->rp_insert("sales_account_transaction",$Values,$Columns,0);
			}
			$reply=array("ack"=>1,"ack_msg"=>"Payment Approve Successfully");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"Payment Not Approve.Please Try again!!");
		} 
	}
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Something went wrong!! Please Try again!!");
}
echo json_encode($reply);
?>
<?php require_once 'disconnect.php';  ?>