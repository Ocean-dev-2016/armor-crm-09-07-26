<?php
$page_id=571;$page_slug='payment';
include("connect.php"); 
$m=$_REQUEST['m'];
$id=$_REQUEST['id'];
if($id!="" && $m!="")
{	
	if($m=="approve")
	{	
	    if($id)
		{  
			/*log entry*/
			$receipt_no = $db->rp_getValue("general_credit_note","receipt_no","id='".$id."'",0);
			$customer_id = $db->rp_getValue("general_credit_note","customer_id","id='".$id."'");
			$sales_executive_id = $db->rp_getValue("general_credit_note","sales_executive_id","id='".$id."'");
			$ctable = "general_credit_note";
			$last_id = $id;
			$flag = "Web";
		    $module_name = "General Credit";
		    $log_description = $module_name." ".$receipt_no." Status Change To Approve By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
		    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
			/*log entry*/

			$update = $db->rp_update("general_credit_note",array("payment_status"=>1),"id='".$id."'",0,$log_description,$flag,$module_name,$sales_executive_id,$customer_id);
			if($update)
			{

				$payment_detail = $db->rp_getData("general_credit_note","*","id='".$id."' AND isDelete=0");
				$payment_detail_R = mysqli_fetch_assoc($payment_detail);

				$customer_id =  $db->rp_getValue("general_credit_note","customer_id","id='".$id."' AND isDelete=0");

				$AccountID   = $db->rp_getValue("account","id","cid='".$customer_id."' AND isDelete=0");
				$AccountNo   = $db->rp_getValue("account","acc_no","cid='".$customer_id."' AND isDelete=0");
			 
				$cid = $db->rp_getValue("account","cid","isDelete=0 AND id='".$AccountID."'");
				
				$Columns=array("cid","account_id","account_no","type","debit","amount","reference_id","reference_table","description","payment_date");  

				$paymentTypeArray = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");

				$get_discount_type_r=$db->rp_getData("discount_type","name","isDelete=0 AND id IN(".$payment_detail_R['discount_type_id'].")");
				$get_names=array();
				while ($get_discount_type_d=mysqli_fetch_assoc($get_discount_type_r)) 
				{
						$get_names[]=$get_discount_type_d["name"];
				}
				$get_names=implode(",", $get_names);

				$payment_detail_R['remark'] = "General Credit of ".$payment_detail_R['receipt_no']." ".$paymentTypeArray[$payment_detail_R['payment_type']]." ".$payment_detail_R['remark']." ".$get_names;


				$Values=array($cid,$AccountID,$AccountNo,2,"-".$payment_detail_R['paid_amount'],$payment_detail_R['paid_amount'],$id,"general_credit_note",$payment_detail_R['remark'],$payment_detail_R['payment_date']);
				/*entry account transaction*/
				$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);
 
			}
			$reply=array("ack"=>1,"ack_msg"=>"General Credit Approve Successfully");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"General Credit Not Approve.Please Try again!!");
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