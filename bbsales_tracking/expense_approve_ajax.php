<?php
$page_id=592;$page_slug='expense_page';
include("connect.php");
// print_r($_REQUEST);exit;
$date=$_REQUEST['date'];
$sales_id=$_REQUEST['sales_id'];


require_once("../include/push_notification.class.php");
$objPushNotification=new PushNotification();

// echo $date;exit;
if($date!="" && $sales_id!="")
{	

		$expense_data_r=$db->rp_getData("expense","*","isDelete=0 AND sales_executive_id='".$sales_id."' AND DATE(expense_date)='".$date."' AND expense_status=0","",0);

		while($expense_data_d = mysqli_fetch_array($expense_data_r))
		{
			// echo "hello";exit;
    		$expense_status1      = 1;
	    	$total_amount = $expense_data_d['total'];
	    	$pass_remark  = "Auto Approved Expense";
	    	$id=$expense_data_d['id'];
	    	// echo $id;exit;

	    	$row = array(
		            "expense_status"      => $expense_status1,
		            "pass_expense_amount" => $total_amount,
		            "pass_remark"         => $pass_remark,
		            );
	    	$Where = "id='".$id."'";
		    $update = $db->rp_update("expense",$row,$Where,0);




		    //notification
			 //    $ExpenseR = $db->rp_getData("expense","*","id='".$id."' AND isDelete=0 AND isActive=1","",0);
			 //    $ExpenseData = mysqli_fetch_array($ExpenseR);

			 //    $expence_category = $db->rp_getValue("expence_category","name","id='".$ExpenseData['category_id']."'",0);

			 //    $notification_title="Your ".$expence_category." for date ".date("d-m-Y",strtotime($ExpenseData['expense_date']))." is Passed";
				// if($ExpenseData['pass_remark']!="" && $ExpenseData['pass_remark']!=null && $ExpenseData['pass_remark']!=NULL)
				// {
			 //    	$ExpenseData['pass_remark'] = " remark for this is <b>'".$ExpenseData['pass_remark']."' <b/>";
			 //    }
			 //    else
			 //    {
			 //    	$ExpenseData['pass_remark'] = "";
			 //    }

		  //   	if($ExpenseData['remark']!="" && $ExpenseData['remark']!=null && $ExpenseData['remark']!=NULL){
		  //   		$ExpenseData['remark'] = " (".$ExpenseData['remark'].") ";
		  //   	}
		  //   	else
		  //   	{
		  //   		$ExpenseData['remark'] = "";
		  //   	}


			 //    $notification_description= "Your <b>".$expence_category."</b> ".$ExpenseData['remark']."  for date <b>".date("d-m-Y",strtotime($ExpenseData['expense_date']))."</b> is Passed of Rs.<b>".$pass_expense_amount."</b> from Rs.<b>".$ExpenseData['total']."</b>".$ExpenseData['pass_remark'];

			 //    // echo $notification_description;exit;

			    
					
				// $result_sales=$objPushNotification->commonNotification($ExpenseData['sales_executive_id'],$id,"expense",$notification_title,$notification_description,"sales_executive","expense");
			    //notification


			    $reply=array("ack"=>1,"ack_msg"=>"All Expense Passed For This Day");

		}

}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Pending Expense Not Found For This Day");
}
echo json_encode($reply);
?>