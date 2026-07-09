<?php
$page_id=592;$page_slug='expense_page';
include("connect.php");

require_once("../include/push_notification.class.php");
$objPushNotification=new PushNotification();

$m=$_REQUEST['m'];
$id=$_REQUEST['id'];

$cat_name = $_REQUEST['cat_name'];
$sales_id = $_REQUEST['sales_id'];
$sales_type = $_REQUEST['sales_type'];

if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
{
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{   
		$status_change_by_name=$db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
	} 
}
else{
	$status_change_by_name="Admin";
}
// echo "<pre>"; print_r($_REQUEST); exit;

if($id!="" && $m!="")
{	
    
	if($m=="send_to_all")
	{	
	    $expense_status1      = $_REQUEST['expense_status1'];
	    $pass_expense_amount = $_REQUEST['pass_expense_amount'];
	    $pass_remark         = $_REQUEST['pass_remark'];
	    $reject_remark         = $_REQUEST['reject_remark'];
 
		if($expense_status1==1)
		{
		    $check = $db->rp_getTotalRecord("expense","id='".$id."' AND isDelete=0 AND isActive=1");
		    if($check > 0)
		    {
		        $row = array(
		            "expense_status"      => $expense_status1,
		            "pass_expense_amount" => $pass_expense_amount,
		            "pass_remark"         => $pass_remark,
		        );
		       	$Where = "id='".$id."'";
		       	$update = $db->rp_update("expense",$row,$Where,0);

		       	$ExpenseR = $db->rp_getData("expense","*","id='".$id."' AND isDelete=0 AND isActive=1");
			    $ExpenseData = mysqli_fetch_assoc($ExpenseR);

		       	// add sales_account_transaction entry
		       	if($update)
				{
					$payment_date=date('Y-m-d');
					$date_now = date("d-m-Y h:i a");
					$payment_type = 0;

					$remark = $cat_name.' Passed Amount Rs.<b>'.$pass_expense_amount.' </b> Request Amount Rs.<b>'.$ExpenseData['total'].'</b>  Dated <b>'.date("d-m-Y",strtotime($ExpenseData['expense_date'])).'</b> By <b>'.$_SESSION[SITE_SESS.'SESS_NAME'].'</b>  '.$ExpenseData['pass_remark'];

					$Columns=array("sales_id","account_id","sales_type","type","credit","amount","reference_id","reference_table","description","payment_date");

					$Values=array($sales_id,$sales_id,$sales_type,$payment_type,$pass_expense_amount,$pass_expense_amount,$id,"expense",$remark,$payment_date);

					/*entry Sales account transaction*/
					$TransctionID=$db->rp_insert("employee_account_transaction",$Values,$Columns,0);
				}
		       	// add sales_account_transaction entry

		       	//notification
			    // $ExpenseR = $db->rp_getData("expense","*","id='".$id."' AND isDelete=0 AND isActive=1");
			    // $ExpenseData = mysqli_fetch_assoc($ExpenseR);

			    $expence_category = $db->rp_getValue("expence_category","name","id='".$ExpenseData['category_id']."'",0);

			    $notification_description= "Your <b>".$expence_category."</b> "." for date <b>".date("d-m-Y",strtotime($ExpenseData['expense_date']))."</b> has been Passed by ".$status_change_by_name;
 	
				$result_sales=$objPushNotification->commonNotification($ExpenseData['sales_executive_id'],$id,"expense","Expense Passed",$notification_description,"sales_executive","expense");
			    //notification
			    	
		        $reply=array("ack"=>1,"ack_msg"=>"Expese Pass Successfully");
		    }
		}
		if($expense_status1==2)
		{
			$check = $db->rp_getTotalRecord("expense","id='".$id."' AND isDelete=0 AND isActive=1");
		    if($check > 0)
		    {
		        $row = array(
		            "expense_status"      => $expense_status1,
		            "reject_remark"         => $reject_remark,
		        );
		       	$Where = "id='".$id."'";
		       	$update = $db->rp_update("expense",$row,$Where,0);

		       	//notification
			    $ExpenseR = $db->rp_getData("expense","*","id='".$id."' AND isDelete=0 AND isActive=1");
			    $ExpenseData = mysqli_fetch_assoc($ExpenseR);

			    $expence_category = $db->rp_getValue("expence_category","name","id='".$ExpenseData['category_id']."'",0);

			   	$notification_description= "Your <b>".$expence_category."</b> "." for date <b>".date("d-m-Y",strtotime($ExpenseData['expense_date']))."</b> has been Rejected by ".$status_change_by_name;
 					
				$result_sales=$objPushNotification->commonNotification($ExpenseData['sales_executive_id'],$id,"expense","Expense Rejected",$notification_description,"sales_executive","expense_reject");
			    //notification

		       if($update)
		       {
		           $reply=array("ack"=>1,"ack_msg"=>"Expese Reject Successfully");
		       }
		    }
		}
	}

	if($m=="delete_expense")
	{	

		if($id!="")
		{
		    
	        $row = array("isDelete"=> 1,);
	       	$Where = "id='".$id."'";
	       	$update = $db->rp_update("expense",$row,$Where,0);

	        $reply=array("ack"=>1,"ack_msg"=>"Expese Deleted Successfully");
		}
	}
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Something went wrong!! Please Try again!!");
}
require_once("disconnect.php");
echo json_encode($reply);
?>