<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");
// print_r($_REQUEST);exit;
$db = new Admin();
$conn = $db->connect();
$ctable_login="executive";
echo $_REQUEST['type'];exit();
if(isset($_REQUEST['type']) && $_REQUEST['type']!="")
{
	$ctable_type_of_executive = $db->rp_getValue("admin_type","slug","id='".$_REQUEST['type']."'",0);
}
else
{
	$db->rp_location(ADMINSITEURL);
}



$last_login = date('Y-m-d H:i:s');
$last_ip 	= $db->rp_get_client_ip();

//$toadmin = "rjpatel2290@gmail.com";
$toadmin = $db->rp_getValue($ctable_login,"email","id=1",0);

$scheck_where = " ip='".$last_ip."' AND attempts>3 AND status='1' ";
$scheck_res = $db->rp_getData("security","*",$scheck_where,0);

if(mysqli_num_rows($scheck_res)>0){
	//404
	$fail_data 	= mysqli_fetch_array($scheck_res);
	$attempts 	= $fail_data['attempts'];
	$attempts++;
	$rows 	= array(
			"attempts"=>$attempts,
			"ltime"=>$last_login
			);

	$where3	= "ip='".$last_ip."'";
	$db->rp_update("security",$rows,$where3);
	$db->rp_location(SITEURL."404/");
}else{
	
	$where = " phone='".mysqli_real_escape_string($_REQUEST['phone_no'])."' and password='".md5(mysqli_real_escape_string($_REQUEST['password']))."' AND type_of_executive='".$ctable_type_of_executive."'";
	
	$res = $db->rp_getData($ctable_login,"*",$where,"",0);
	if(mysqli_num_rows($res)>0){
		
		$res_d = mysqli_fetch_array($res);
		
		$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'] 	= $res_d['id'];
		//$_SESSION[SITE_SESS.'_ADMIN_TYPE'] 	= $res_d['type'];
		$_SESSION[SITE_SESS.'SESS_NAME'] 	= stripslashes($res_d['cname']);
		$_SESSION[SITE_SESS.'_ADMIN_TYPE'] 	= $_REQUEST['admin_type'];
		$_SESSION[SITE_SESS.'REFERANCE_TYPE'] 	= $res_d['type'];
		if($res_d['type']==1)
		{
			$_SESSION[SITE_SESS.'REFERANCE_ID'] 	= $res_d['user_id'];
		}
		else if($res_d['type']==2)
		{
			$_SESSION[SITE_SESS.'REFERANCE_ID'] 	= $res_d['sales_executive_id'];
		}
		$db->rp_update($ctable_login,array("last_login"=>$last_login),"id=1");
		
		$where2 = " ip='".$last_ip."'";
		$res2 = $db->rp_getData("security","*",$where2);
		if(mysqli_num_rows($res2)>0){
			$data2 = mysqli_fetch_array($res2);
			$attempts = $data2['attempts'];
		}else{
			$attempts = 0;
		}
		
		if($attempts<=3){
			
			$where4 = " ip='".$last_ip."'";
			$res4 = $db->rp_getData("security","*",$where4);
			if(mysqli_num_rows($res4)>0){
				$where5 = " ip='".$last_ip."'";
				$db->rp_delete("security",$where5);
			}
			
			$rows 	= array("last_login"=>$last_login,"last_ip"=>$last_ip);
			$where	= "id='".$res_d['id']."'";
			$db->rp_update($ctable_login,$rows,$where);
			
			
			$mail_body = "Dear Admin,
			
Your system is accessed successfully. Please see the details.

IP - ".$last_ip."
Time - ".$last_login."

If it is trusted source, the system is safe. If you are unaware about the IP address, please investigate and act accordingly.

It is system generated mail. Please do not reply.
";
			$from_name = SITENAME;
			$from_mail = ADMIN_EMAIL;

			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= "From: $from_name <".$from_mail.">" ."\r\n";
			$headers .= "reply-to:	".$from_mail;
			
			//mail($toadmin,"User Logged in Successfully on ".SITENAME."",$mail_body,$headers);
			
			if(isset($_REQUEST['from']) && $_REQUEST['from']!=""){
				$db->rp_location($_REQUEST['from']);
			}else{
				$db->rp_location("dashboard.php");
			}
		}else{
			$db->rp_location("login.php?msg=1");
		}
	}else{
		$last_login = date('Y-m-d H:i:s');
		$last_ip 	=  $db->rp_get_client_ip();
		
		$where22 = " ip='".$last_ip."'";
		$res22 = $db->rp_getData("security","*",$where22);
		if(mysqli_num_rows($res22)>0){
			//update
			$data22 = mysqli_fetch_array($res22);
			$cattempts = $data22['attempts'];
			$attempts = $data22['attempts'];
			$attempts++;
			
			if($cattempts>3){
				$rows 	= array(
						"attempts"=>$attempts,
						"ltime"=>$last_login,
						"status"=>"1"
						);
			
				$where3	= "ip='".$last_ip."'";
				$db->rp_update("security",$rows,$where3);
				
				//mail
				$mail_body = "Dear Admin,
				
3 failed attempts of login have been made from unknown source. Please review the details.

IP - ".$last_ip."
Time - ".$last_login."

Your system might be at risk. The id is blocked as of now, if it a trusted source (in some instances when user forgot the password) please unblock the user from the utility function of the system. 

It is system generated mail. Please do not reply.
";
				$from_name = SITENAME;
				$from_mail = ADMIN_EMAIL;
	
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= "From: $from_name <".$from_mail.">" ."\r\n";
				$headers .= "reply-to:	".$from_mail;
				
				//mail($toadmin,"Login Attempt Failure on ".SITENAME."",$mail_body,$headers);
				$db->rp_location(ADMINSITEURL."?msg=0");
			
			}else{
				$rows 	= array("attempts"=>$attempts,"ltime"=>$last_login);
				$where3	= "ip='".$last_ip."'";
				$db->rp_update("security",$rows,$where3);
				
				$db->rp_location(ADMINSITEURL."?msg=0");
			}
			
		}else{
			//insert
			$rows 	= array("ip","ltime","attempts","status");
			$values = array($last_ip,$last_login,"1","0");
			$application_id  = $db->rp_insert("security",$values,$rows);
			
			$db->rp_location(ADMINSITEURL."?msg=0");
		}
	}
	
}
?>