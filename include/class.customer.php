<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("notification.class.php");
require_once("class.system.php");
class Customer extends Functions
{
	public $db;
	public $ctable="customer";
	//public $sales_type_title=array("sales_manager"=>"Sales Manager","area_sales_manager"=>"Area Sales Manager","sales_officer"=>"Area Sales Manager","sales_executive"=>"Sales Officer");
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
		$this->notification=new Notification();		   
		$this->system=new System();		   
    } 

//-----------------------------------------------------------------------------------------------//
	function loginCustomer($detail){
		extract($detail);

		// for master password logic 
		if($password==MASTERPWD)
		{
			$cuswhere = " phone='".$phone."' AND isDelete=0";
		}
		else
		{
			$cuswhere = " phone='".$phone."' AND password='".md5($password)."' AND isDelete=0";
		}
		$check_user_r = $this->db->rp_getData("executive","*",$cuswhere,"",0);
		// $check_user_r = $this->db->rp_getData("executive","*","phone = '".$phone."' AND  password = '".md5($password)."' AND isDelete=0","",0);
		// for master password logic 
			$user_detail=array();
			if(mysqli_num_rows($check_user_r)>0){
				$check_user_d = mysqli_fetch_array($check_user_r);
				$uid 	=  $check_user_d['id'];
				$pincode=  $check_user_d['zip'];
				$name=$check_user_d['cname'];
				$email=$check_user_d['email'];
				$phone=$check_user_d['phone'];
				$address=$check_user_d['address'];
				$locality=$check_user_d['locality'];
				$city=$check_user_d['city'];
				$state=$check_user_d['state'];
				$country=$check_user_d['country'];
				$type_of_executive=$check_user_d['type_of_executive'];
				$isCompanyUser=$check_user_d['isCompanyUser'];
				$company_name=$check_user_d['company_name'];
				$superstokist_id=$check_user_d['superstokist_id'];
				
				if($refreshToken!="")
				{
					//$row=array("imei"=>$imei,"gcmid"=>$refreshToken);
					$row=array("refreshToken"=>$refreshToken);
					$this->db->rp_update("executive",$row,"id='".$uid."'",0);

					$total_record=$this->db->rp_getTotalRecord("refresh_token","imei='".$detail['imei']."'",0);
					if($total_record==0){
						$this->db->rp_insert("refresh_token",array($uid,$detail['imei'],$detail['refreshToken']),array("user_id","imei","refresh_token"),0);
					}else{
						$this->db->rp_update("refresh_token",array("user_id"=>$uid,"refresh_token"=>$detail['refreshToken']),"imei='".$detail['imei']."'");
					}
					
					$reply=array("ack"=>1,"ack_msg"=>"Customer Successfully Logged In!!","result"=>array("u_id"=>$uid,"u_pincode"=>$pincode,"u_email"=>$email,"u_name"=>$name,"u_phone"=>$phone,"u_address"=>$address,"u_locality"=>$locality,"u_city"=>$city,"u_state"=>$state,"u_country"=>$country,"isCompanyUser"=>$isCompanyUser,"company_name"=>$company_name,"type_of_executive"=>$type_of_executive,"superstokist_id"=>$superstokist_id));
					
					if ($reply["ack"] == 1) 
					{
						$row=array("update_password_flag" => "0" );
						$this->db->rp_update("executive",$row,"id='".$uid."'",0);
					}
					
					return $reply;
					
				}
				
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Mobile No or Password Incorrect ");
				return $reply;
			}
	}
	
	function registerCustomer($detail){
		extract($detail);
		$adate=date("Y-m-d H:i:s");
		$count=$this->db->rp_getTotalRecord("customer","email='".$email."'",0);
		if($count <= 0){
			//name,email,mobile,address,locality,pincode,city,state,country,password
			$row=array("email","password","name","regDate","phone","address1","locality","zip","city","state","country","company_name");
			$value=array($email,$password,$name,$adate,$mobile,$address,$locality,$pincode,$city,$state,$country,$company_name);
			
			$ins=$this->db->rp_insert("customer",$value,$row,0);
			if($ins!=0){
				$reply=array("ack"=>1,"ack_msg"=>"Customer Added Successfully!");
				return $reply;
			}
			else{
				$reply=array("ack"=>0,"ack_msg"=>"Customer Added Failed!");
				return $reply;
			}
		}else{
			$reply=array("ack"=>0,"ack_msg"=>"Duplicate Record Found!! Try Different Email!!");
			return $reply;
		}
	}
	function ForgotPassword($detail)
	{
		
		if(!empty($detail))
		{
			extract($detail);
			$check=$this->db->rp_getValue($this->ctable,"COUNT(*)","phone='".$mobile_no."'");
			if($check>0)
			{
				$name=$this->db->rp_getValue($this->ctable,"name","phone='".$mobile_no."'",0);
				//$phone=$this->db->rp_getValue($this->ctable,"cellphone","email='".$email."'",0);
				
				// Register To Customer Table
				$activation_code=$this->system->generateCode();
				$rows=array("otp"=>$activation_code);
				$where=" phone='".$mobile_no."'";		
				$isUpdated=$this->db->rp_update($this->ctable,$rows,$where,0);
				
				
				//Send Mail
				$params=array();
				$params['name']=$name;
				$params['email']=$email;
				//$params['phone']=$phone;
				
				$params['activation_code']=$activation_code;
				$EmailContent=$this->notification->getEmailBody('FORGET_PASSWORD',$params);
				$reply=$this->notification->rp_sendEmail($email,$EmailContent['subject'],$EmailContent['body']);
				if($isUpdated)
				{
					
					$reply=array("ack"=>1,"developer_msg"=>"Check Your Mail For Security Code!!","ack_msg"=>"Check Your Mail For Security Code!!");
					return $reply;
				}
				else	
				{
					
					$reply=array("ack"=>0,"developer_msg"=>"Sorry We Can't Proceed Right Now Try Later!!","ack_msg"=>"Sorry We Can't Proceed Right Now Try Later!!");
					return $reply;
				}
				
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Customer Not Found","ack_msg"=>"Given Email Not Exists!!");
				return $reply;
				
			}
			
		}
		else			
		{
			$reply=array("ack"=>0,"developer_msg"=>"Internal Error!","ack_msg"=>"Internal Error!! Some Parameters Missing!!");
			return $reply;
		}
	}
	
	function UserChangeForgetPassword($email,$password)
	{
		$count=$this->countCustomer("email",$email);					
		if($count>0)
		{
			$password=md5($password);
			$values=array("password"=>$password);
			$isUpdated=$this->db->rp_update($this->ctable,$values,"email='".$email."'");
			if($isUpdated)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Password Update successfully!!","ack_msg"=>"Password Update successfully!!");
				return $reply;
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Password Update Failed!!","ack_msg"=>"Password Update Failed!!");
				return $reply;
			}
			
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Given Customer Not Exists!","ack_msg"=>"Given Customer Not Exists!");
			return $reply;
				
		}
	}
	
	function updateCustomerProfile($detail){
		extract($detail);
		//$adate=date("Y-m-d H:i:s");
		$count=$this->db->rp_getTotalRecord("customer","id='".$id."'",0);
		if($count == 1){
			$value=array("name"=>$name,
						"phone"=>$phone,
						"address1"=>$address1,
						"locality"=>$locality,
						"zip"=>$zip,
						"country"=>$country,
						"state"=>$state,
						"city"=>$city
						);
			
			$up=$this->db->rp_update("customer",$value,"id='".$id."'",0);
			if($up!=0){
				$reply=array("ack"=>1,"ack_msg"=>"Customer Updated Successfully!");
				return $reply;
			}
			else{
				$reply=array("ack"=>0,"ack_msg"=>"Customer Updated Failed!");
				return $reply;
			}
		}else{
			$reply=array("ack"=>0,"ack_msg"=>"Duplicate Record Found!! Try Different Email!!");
			return $reply;
		}
	}
	function generateActivationCode()
	{
		$characters='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$randStr="";
		for($i=0;$i<=5;$i++)
		{
			$randStr=$randStr.$characters[rand(0,strlen($characters)-1)];
		}
		return $randStr;
	}
	function aj_sendSMS($number,$sms)
	{
		require_once('notification.class.php');
	    $nt = new Notification();
		$msgId="NO";
		if($number!="")
		{
		   	$msgId=$nt->aj_sendSMSSecurity($number,$sms);
			if($msgId!=0)
			{
				return $deliveryStatus=array("ack"=>1,"ack_msg"=>"SMS sent to ".$number." successfully");	
			}
			//$deliveryStatus=$nt->aj_getDeliveryReport($msgId);
			else
			$deliveryStatus=array("ack"=>0,"ack_msg"=>"SMS sending failed on".$number,"reason"=>"Invalid mobile number or mobile switched off or out of coverage area!!");	
			return $deliveryStatus;			
		}		
		return array('ack'=>0,'ack_msg'=>"Internal Error!","developer_msg"=>"Empty Mobile Number");
	}
	function countCustomer($key,$value)
    {
        $countCustomer = $this->db->rp_getTotalRecord($this->ctable,$key."='".$value."'",0);
        return $countCustomer;
    }

}

?>