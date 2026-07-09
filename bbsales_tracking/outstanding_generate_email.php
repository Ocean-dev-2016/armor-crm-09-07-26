<?php
$page_id=618;$page_slug='outstanding_report';
include("connect.php");
include('../include/notification.class.php');
$nt_obj = new Notification();
$ctable 	= "account_transaction";
$ctable1 	= "Account Transaction";
$credit_total='';
$debit_total='';
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$customer_id=$db->rp_getValue("customer","id","isDelete=0 AND lname='".$_REQUEST['searchName']."'",0);
	$ctable_where .= " ( remark like '%".$_REQUEST['searchName']."%' ) AND ";
}

$final = date("Y-m-d", strtotime("-1 month"));
$ctable_where .= " isDelete=0 AND isActive=1 ";

if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="" && $_REQUEST['cid']!=NULL)
{
 // $ctable_where .= " AND account_id ='".$_REQUEST['cid']."'";
 $ctable_where .= " AND cid ='".$_REQUEST['cid']."'";
}

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where." GROUP BY account_id","payment_date ASC",0);

?>
<?php

if($ctable_r)
{
	$count = 0;
	$credit=0;
	$debit=0;
	$total=0;
	$total_credit=0;
	$mailcount = 0;
	$Data = array();
	while($ctable_d = mysqli_fetch_assoc($ctable_r))
    {
    	++$count;
		//payment due date 
    	$pay_date = date('Y-m-d',strtotime($ctable_d['created_date']));
			$before_date=date('Y-m-d', strtotime('-30 days'));
		$current_date = date('Y-m-d');

		$credit = $db->rp_getValue($ctable,"SUM(credit)","isDelete=0 AND isActive=1 AND account_id='".$ctable_d['account_id']."'",0);
		
		$debit = $db->rp_getValue($ctable,"SUM(debit)","isDelete=0 AND isActive=1 AND account_id='".$ctable_d['account_id']."'",0);

		if($debit=="null")
		{
			$debit = 0;
		}
		if($credit=="null")
		{
			$credit = 0;
		}
		$total_credit = $credit;
		// echo $total_credit;
		// echo $debit;
		$pending=$total_credit-(-0-$debit);

		if($pending<0)
		{
			
			$customer_name = $db->rp_getValue("executive","cname","id=".$ctable_d['cid']."");
			$to_mail = $db->rp_getValue("executive","email","id=".$ctable_d['cid']."",0);
			$outstanding = $db->rp_num(round(-0-$pending,2));
			$Data['default_cc'] = $to_mail;
			$Data['default_to'] = $to_mail;
			$Data['from_mail'] = $db->rp_getValue(CTABLE_ADMIN,"email","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'",0);
			$Data['from_email_password'] = $db->rp_getValue(CTABLE_ADMIN,"email_password","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'",0); 
			$Data['from_name'] = $db->rp_getValue(CTABLE_ADMIN,"name","id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] ."'","",0);  
			$Data['subject'] = "Outstanding Reminder From : ".SITENAME;
			$Data['body'] = "<h4>"."Dear"." ".$customer_name."<br/>"." "." This is a gentle reminder that you have an outstanding amount of Rs."." ".$outstanding."</h4>"."<br/>"."<h4>"."Thanks & Regards,"."<br/>".CLIENT_NAME."</h4>";
			$Data['file_path'] = "";
			if($Data['default_to']!="")
			{	
				for ($i = 0; $i < sizeof($Data['default_to']); $i++) 
				{
					//print_r($Data)." <br/>";
					$reply = $nt_obj->rp_sendEmailOutstanding($Data);
				}
				// Email Sent
				
				if($reply=1)
				{
					$mailcount++;
				}
				//echo json_encode($result);
			}	
		}
	}
	$result = array("ack"=>1,"ack_msg"=>$mailcount." mail Sent Successfully!");
	//echo '{"data":'.json_encode($result1).'}';
	echo json_encode($result);
}

?>
		