<?php
error_reporting(0);
include("../include/define.php");
include("../include/function.class.php");
require_once("../include/notification.class.php");
$db = new Functions();
$nt = new Notification();
$conn = $db->connect(); 

$where		= "phone='".$db->clean($_REQUEST['phone'])."' AND isDelete=0";
$ctable_r 	= $db->rp_getData("executive","*",$where,"",0);
if(mysqli_num_rows($ctable_r)>0){
	$ctable_d 	= mysqli_fetch_array($ctable_r);
	$username 	= $ctable_d['cname'];
				$activationCode=generateActivationCode();
				$sms="Hello ".$ctable_d['cname']."\nWelcome to ".SITETITLE.", Your Otp is:".$activationCode."\nTeam ".SITETITLE;
				$a=$nt->aj_sendSMSSecurity($_REQUEST['phone'],$sms);
				$rows 	= array(
							"forgot_pass_string"	=> $activationCode,
							);
				$db->rp_update("executive",$rows,"phone='".$_REQUEST['phone']."'",0);
				if($_REQUEST['flag']==1)
				{
					$db->rp_location("otp_varification.php?phone=".$_REQUEST['phone']."&msg=10");
				}
				else{
				$db->rp_location("otp_varification.php?phone=".$_REQUEST['phone']."");
				}
}
else{
	$db->rp_location("index.php?msg=2");
}
	function generateActivationCode()
	{
		$characters='0123456789';
		$randStr="";
		for($i=0;$i<=5;$i++)
		{
			$randStr=$randStr.$characters[rand(0,strlen($characters)-1)];
		}
		return $randStr;
	}
	
?>