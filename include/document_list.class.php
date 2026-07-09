<?php
require_once("main.class.php");
require_once("function.class.php");
class DocumentList extends Functions
{

	public $db;
    public $ctable="document_list";
    function __construct($id="") 
    {

		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
	} 

	public function InsertDocument($detail,$file) 
	{

		extract($detail);
		if (isset($_FILES["image_path"]) ) 
	    {
		    $allowedExts = array("jpg","JPG","pdf","PDF");
		    $temp = explode(".", $_FILES["image_path"]["name"]);
		    $extension = end($temp);
	 
			$fileName 	= $this->db->clean($_FILES["image_path"]["name"]);	
			if($fileName!=""){
			$fileSize 	= round($_FILES["image_path"]["size"]); // BYTES									
			$adate 		= date('Y-m-d H:i:m');
			
			$extension	= end(explode(".", $fileName));		
			if(!in_array($extension,$allowedExts))
			{
				$file_error=true;
			}
								
			$image_path	= 'gst_'.substr(sha1(time()), 0, 6).".".$extension;
			$filePath 	= GST_VISITING_DETAIL_A.$image_path;	
			$_FILES['image_path']['tmp_name'];
			move_uploaded_file($_FILES['image_path']['tmp_name'], $filePath);
			
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
					"class_id",
					"document_type",
					"image_path",
					"isDelete",
					"customer_type",
					"sales_type",
					"document_name",
				);

		$values = array(
					$class_id,
					$document_type,
					$image_path,
					$isDelete,
					$customer_type,
					$sales_type,
					$document_name,
				);
		$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Document List Added.","ack_msg"=>"Success! Document List Insert Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Document List Insert Failed.");
			return $reply;
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
					"customer_type"	=> $customer_type,
					"sales_type"	=> $sales_type,
					"document_name"	=> $document_name,
				);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Document List Update Successfull!!.","ack_msg"=>"Success! Notification Update Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Document List Update Failed.");
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
		$result['customer_type']	= $ctable_d['customer_type'];
		$result['sales_type']	    = $ctable_d['sales_type'];
		$result['document_name']	= $ctable_d['document_name'];

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
			$reply=array("ack"=>1,"developer_msg"=>"Document List Delete.","ack_msg"=>"Success! Delete Document List Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Document List Failed.");
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
// 		print_r($data);exit;
		////which_notification=>1=delivery, 2=customer
		if($which_notification==1){
			$apiKey = 'AAAAO6_HWps:APA91bEYJAA-ARfGf-aPw-UxnDUEqEA8vMr6Qg6US27HG5EqNK-HdiEg87R-qIu1lqOlbd7im-RD5VR8knZ7xuP7xXeqRnpZHTUrO-YLCSKWINCAhYk3uzZ7HbhUK1kUFNH4ZeD40xeF'; // This is Server Legacy Key From Cloud Messaging Firebase
		}else{
			$apiKey = 'AAAAO6_HWps:APA91bEYJAA-ARfGf-aPw-UxnDUEqEA8vMr6Qg6US27HG5EqNK-HdiEg87R-qIu1lqOlbd7im-RD5VR8knZ7xuP7xXeqRnpZHTUrO-YLCSKWINCAhYk3uzZ7HbhUK1kUFNH4ZeD40xeF'; // This is Server Legacy Key From Cloud Messaging Firebase
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
		//print_r($result); exit;
		curl_close( $ch );
		return $result;
	}
}
?>