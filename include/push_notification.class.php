<?php
require_once("main.class.php");
require_once("function.class.php");
class PushNotification extends Functions
{

	public $db;
    public $ctable="push_notification";
    function __construct($id="") 
    {

		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
	} 

	public function InsertNotification($detail,$file) 
	{

		extract($detail);
		$dup_where = "pricelist_name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Pricelist Name","ack_msg"=>"Duplication! Already Exist Pricelist Name.");
			return $reply;
		}
		else
		{

		    if (isset($file["image_path"]))
		    {
		    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
		    	$temp = explode(".", $file["image_path"]["name"]);
		    	$extension = end($temp);
		    	$fileName 	= $this->db->clean($file["image_path"]["name"]);	
		    	if($fileName!="")
		    	{
		    		$fileSize 	= round($file["image_path"]["size"]); // BYTES
		    		$adate 		= date('Y-m-d H:i:m');
		    		$extension	= end(explode(".", $fileName));
		    		if(!in_array($extension,$allowedExts))
		    		{
		    			$file_error=true;
		    		}
			    	$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
			    	$filePath 	= NOTIFICATION_A.$image_path;
			    	$file['image_path']['tmp_name'];
			    	move_uploaded_file($file['image_path']['tmp_name'], $filePath);
			    	$new_image=true;
		    	}
		    	else{
		    		$image_path="";
		    	}
		    }
		    else
		    {
		    	$new_image=false;
		    	$image_path="";
		    }

			$adate	= date('Y-m-d H:i:s');
			$slug   =$this->db->clean($this->db->rp_createProSlug($title));
			$rows 	= array(
						"type",
						"title",
						"slug",
						"descr",
						"image_path",
						"default_sound",
						"isDelete"
					);

			$values = array(
						$type,
						$title,
						$slug,
						$descr,
						$image_path,
						$default_sound,
						$isDelete,
					);
			$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Notification Added.","ack_msg"=>"Success! Notification Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Notification Insert Failed.");
				return $reply;
			}
		}
	}
	public function UpdateNotification($detail,$file)
	{
		extract($detail);
		$dup_where = "pricelist_name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Pricelist Name","ack_msg"=>"Duplication! Already Exist Pricelist Name.");
			return $reply;
		}else{
			if(isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
				$extension = end($temp);
				$fileName 	= $this->db->clean($file["image_path"]["name"]);
				if($fileName!=""){
					$fileSize 	= round($file["image_path"]["size"]); // BYTES
					$adate 		= date('Y-m-d H:i:m');
					$extension	= end(explode(".", $fileName));
					if(!in_array($extension,$allowedExts))
					{
						$file_error=true;
					}
					$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
					$filePath 	= NOTIFICATION_A.$image_path;	
					$file['image_path']['tmp_name'];
					move_uploaded_file($file['image_path']['tmp_name'], $filePath);
					$new_image=true;
				}
				else
				{
					$image_path=$detail['old_image_path'];
					$image_path="";
				}
			}
			else
			{
				$image_path=$detail['old_image_path'];
				unset($detail['old_image_path']);
			}

			$slug   =$this->db->clean($this->db->rp_createProSlug($title));
			$rows 	= array(
					"type"			=> $type,
					"title"			=> $title,
					"slug"			=> $slug,
					"descr"			=> $descr,
					"default_sound"	=> $default_sound,
					"image_path"	=> $image_path,
				);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Notification Update Successfull!!.","ack_msg"=>"Success! Notification Update Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Notification Update Failed.");
				return $reply;
			}
		}
	}	

	public function GetEditDataNotification($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();

		$result['title']		    = $ctable_d['title'];
		$result['descr']	 	    = $ctable_d['descr'];
		$result['default_sound']	= $ctable_d['default_sound'];
		$result['image_path']	    = $ctable_d['image_path'];
		$result['type']	            = $ctable_d['type'];

		$reply=array("ack"=>1,"developer_msg"=>"Notification detail fetched!!.","ack_msg"=>"Success! Notification Edit Successfully.","result"=>$result);
		return $reply;
	}

	public function DeleteNotification($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='".$_REQUEST['id']."'";
		$uid=$this->db->rp_update($this->ctable,$rows,$where);
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Notification Delete.","ack_msg"=>"Success! Delete Notification Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Notification Failed.");
			return $reply;
		}
	}

	

	// 	new notification function for panel add by yagnik on 16-4-2020

	public function notificationInsert($detail)
	{
        $uid		              = addslashes(trim($detail['uid']));
		$notification_title		  = addslashes(trim($detail['notification_title']));
		$notification_description = addslashes(trim($detail['notification_description']));
		$notification_type		  = addslashes(trim($detail['notification_type']));
		$image_path		          = addslashes(trim($detail['image_path']));
		$respective_date          = date('Y-m-d H:i:s');
		$created_date             = date('Y-m-d H:i:s');
		$notification_type		  = addslashes(trim($detail['notification_type']));
		$item_type		          = addslashes(trim($detail['item_type']));
		$item_id		          = addslashes(trim($detail['item_id']));
		$notification_sound       = $this->db->rp_getValue("push_notification","default_sound","id='".$detail['id']."'",0);
		$refresh_token_table      = $detail['get_refresh_token_table'];

		$refreshTokens=array();
		$rows 	= array(
				"user_id",
				"title",
				"description",
				"type",
			);

		$values = array(
				$uid,
				$notification_title,
				$notification_description,
				$notification_type,
			);

		$inserted_id = $this->rp_insert("application_notification",$values,$rows,0);
		$msg = array(
				"notification_id"	 => $inserted_id,
				"type"		         => $notification_type,
				"title"		         => $notification_title,
				"description"        => $notification_description,
				"uid"                => $uid,
				"respective_date"	 =>	$respective_date,
				"image_path"		 =>	$image_path,
				"notification_sound" =>$notification_sound,
				"item_type"          =>$item_type,
				"item_id"            =>$item_id,
			);

		if($detail['uid']!="")
		{
			// $where="refresh_token!='' AND user_id='".$detail['uid']."'";
			$where="refreshToken!='' AND id='".$uid."'";
			//$refreshTokens[]=$this->db->rp_getValue("sales_executive","refreshToken",$where,0);
			$refreshTokens[]=$this->db->rp_getValue($refresh_token_table,"refreshToken",$where,0);
			//$notification_flag=$this->db->rp_getValue("user","notification_flag",$where,0);
		}
		/*else
		{
			$notification_flag=0;
		}*/
		$result=$this->send_notification1($msg,$refreshTokens,1);
	}

	public function send_notification1($data,$ids,$which_notification)
	{
		if($which_notification==1){
			//$apiKey = 'AAAA-CHrgVk:APA91bGLSu8YnhPDZzKuHDSJIHaRrfWM7Mgx3yiglkL5W97OXCOdxl3flloSIP6gClSLQNkUZZppvJliy_aSap270LPQnHJEDmWb-iL2l1MQKyHttc7U6_L2pomaf_81xJMw9Cv2SUnG'; // This is Server Legacy Key From Cloud Messaging Firebase
			$apiKey = 'AAAAUIk9V4k:APA91bFFHkvLlnxxOk9QV7Ua6d1zlonsfbJIcEf0yoF1a9g2Dte_zvk2i7sgpW_kz2bDz2CAR0cuyi4Y2qG1AeZkWT_LPErU0NbB2o6yT7ZQueHYQ1mVEoYt-JxQjacdp9zQvtnD8eUW'; // This is Server Legacy Key From Cloud Messaging Firebase
		}else{
			$apiKey = 'AAAAUIk9V4k:APA91bFFHkvLlnxxOk9QV7Ua6d1zlonsfbJIcEf0yoF1a9g2Dte_zvk2i7sgpW_kz2bDz2CAR0cuyi4Y2qG1AeZkWT_LPErU0NbB2o6yT7ZQueHYQ1mVEoYt-JxQjacdp9zQvtnD8eUW'; // This is Server Legacy Key From Cloud Messaging Firebase
		}
		//$url = 'https://android.googleapis.com/gcm/send';
		$url = 'https://fcm.googleapis.com/fcm/send';
		$post = array(
			'registration_ids'  => $ids,
			'data'              => $data,
		);

		$headers = array( 
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//////// SSL Verifier False ////////
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );
		$result = curl_exec( $ch );
		 // print_r($result);exit;
		curl_close( $ch );
		return $result;
	}

	public function send_CustomerNotification($data1,$ids1,$which_notification1)
	{   
		////which_notification=>1=delivery, 2=customer
		if($which_notification==1){
			$apiKey = 'AAAAJl0EIXQ:APA91bHJVECDU-0pJGfz3dhIaqOrh_vPgW3j4OKetpJaP8UrrXoxK6xicv_5gtwSb0Z8eu7gx9RqSJm7_Je3qiH9kaFvY5HJmoUeUSe79yUMFMI6X49Qtl_fkniFPAn9QJiESqHCVgBm'; // This is Server Legacy Key From Cloud Messaging Firebase
		}else{
			$apiKey = 'AAAAJl0EIXQ:APA91bHJVECDU-0pJGfz3dhIaqOrh_vPgW3j4OKetpJaP8UrrXoxK6xicv_5gtwSb0Z8eu7gx9RqSJm7_Je3qiH9kaFvY5HJmoUeUSe79yUMFMI6X49Qtl_fkniFPAn9QJiESqHCVgBm'; // This is Server Legacy Key From Cloud Messaging Firebase
		}
		//$url = 'https://android.googleapis.com/gcm/send';
		$url = 'https://fcm.googleapis.com/fcm/send';
		$post = array(
			'registration_ids'  => $ids1,
			'data'              => $data1,
		);

		$headers = array( 
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//////// SSL Verifier False ////////
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );
		$result = curl_exec( $ch );
		curl_close( $ch );
		return $result;
	}

	public function commonNotification($user_id,$reference_id,$reference_table,$title,$descr,$user_type,$notification_type)
	{
		$rows 	= array("user_id","referance_id","referance_type","notification_title","notification_description","user_type","notification_type","isDelete","isActive");
	    $values = array($user_id,$reference_id,$reference_table,$title,$descr,$user_type,$notification_type,0,1);
	    $insert = $this->db->rp_insert("notification",$values,$rows,0);

		$where11="refreshToken!='' AND id='".$user_id."'";

		// for all order department 
	    if($notification_type=='orders')
		{
			$refreshTokens11 = $this->getTokenByAdminType("19","dealer_distributor_network","admin_type");
		}
		// for all order department 
		
		// for all hr
	    if($notification_type=='leave_request' || $notification_type=='expense' || $notification_type=='followup')
		{
			$refreshTokens11 = $this->getTokenByAdminType("14","dealer_distributor_network","admin_type");
		}
		// for all hr

	    if($user_type=="customer")
	    {	     	
			$refreshTokens11[]=$this->db->rp_getValue("executive","refreshToken",$where11,0);
			$refreshTokens11[] = $this->db->rp_getValue("dealer_distributor_network","refresh_token_web","refresh_token_web!='' AND customer_id='".$user_id."'",0); 
			// print_r($refreshTokens11);exit; 
	    }
	    else if($user_type=="sales_executive")
	    {	    	
			$refreshTokens11[]=$this->db->rp_getValue("sales_executive","refreshToken",$where11,0);
			$refreshTokens11[] = $this->db->rp_getValue("dealer_distributor_network","refresh_token_web","refresh_token_web!='' AND sales_executive_id='".$user_id."'",0);
	    }
 	
 		if(NOTIFICATION_SEND)
 		{ 
		    $msg = array(
				"type"		     => $notification_type,
				"title"		     => $title,
				"description"    => $descr,
				"body"    		 => $descr,
				"user_id"        => $user_id,
				"reference_id"   => $reference_id,
				"item_id"        => $reference_id,
				"reference_type" => $reference_table,
			);
			$result1=$this->send_notification1($msg,$refreshTokens11,0);
		}
	}
	public function getTokenByAdminType($admin_type_id,$reference_table,$column_name)
	{
		$getDataR = $this->db->rp_getData("dealer_distributor_network","refresh_token_web","refresh_token_web!='' AND ".$column_name."=".$admin_type_id,"",0);
		while($getDataD=mysqli_fetch_assoc($getDataR))
		{
			$refresh_token_web_array[] = $getDataD['refresh_token_web'];
		}
		return $refresh_token_web_array;
	}
}
?>	