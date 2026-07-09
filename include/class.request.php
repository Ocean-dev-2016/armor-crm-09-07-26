<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("notification.class.php");
require_once("class.system.php");
class Request extends Functions
{
	public $db;
	public $ctable="request";
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
	public function AddRequest($detail,$file)
	{
		// print_r($file);exit;
		extract($detail);
		$value=$this->db->getlastInsertId($this->ctable);
		$request_no=REQUEST_NO.str_pad($value, 3, '0', STR_PAD_LEFT);
		$request_date = date('Y-m-d');
		$state = $this->db->rp_getValue("executive","state","id='".$customer_id."'");
		$city = $this->db->rp_getValue("executive","city","id='".$customer_id."'");
		$zone = $this->db->rp_getValue("executive","zip","id='".$customer_id."'");


		$rows 	= array(
				"user_id",
				"customer_id",
				"dealer_id",
				"latitude",
				"longitude",
				"app_address",
				"remark",
				"class_id",
				"area_id",
				"title",
				"request_no",
				"isActive",
				"entry_flag",
				"request_type",
				"request_cat_id",
				"request_subcat_id",
				"request_created_by",
				"request_assign_to",
				"request_date",
				"state",
				"city",
				"zone"
			);
		$values = array(
				$user_id,
				$customer_id,
				$dealer_id,
				$latitude,
				$longitude,
				$app_address,
				$remark,
				$class_id,
				$area_id,
				$title,
				$request_no,
				1,
				$entry_flag,
				$request_type,
				$request_cat_id,
				$request_subcat_id,
				$request_created_by,
				$request_assign_to,
				$request_date,
				$state,
				$city,
				$zone,
			);

		$eid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		$image_path=array();
		if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
		{
			$ri = $eid;
			$rt = "request";
			$tc = "request";
			$rc = "id";
			for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
			{
				//print_r($file["image_path"]);
				$file_name = $file['image_path']['name'][$i];
				$file_size = $file['image_path']['size'][$i];
				$file_tmp = $file['image_path']['tmp_name'][$i];
				$file_type = $file['image_path']['type'][$i];
				$extension=explode(".",$file_name);
				
				$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
				$extension=$extension[sizeof($extension)-1];
				if(!in_array($extension,$allowed_extentions))
				{
					$file_error=true;
				}
				$orignal_file_name=$extension[0];
				if(in_array($extension,$allowed_extentions))
				{
					$attachment="../resource/image/";
					move_uploaded_file($file_tmp,$attachment.$file_name);
				}
				$MediaTitle=$file_name;
		    	$MediaOrignalTitle=$file_name;

				$MediaFileName=$file_name;
				// $MediaType=User::$ValidMediaType[$extension];
				$UploadDate=date("Y-m-d H:i:s");
				
				// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
				$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
				// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
				$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
				$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);

				$image_path[] = $MediaID;
			}
			$image_path = implode(",", $image_path);
			$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$eid."'",0);
		}		

		if($eid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Request Add successfully!!","ack_msg"=>"Request Add successfully!!","id"=>$eid);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Request Not Add");
			return $reply;
		}
	}
	
	public function UpdateComplain($detail,$file)
	{
		// print_r($file);exit;
		extract($detail);
		$rows 	= array(
			"user_id"     =>$user_id,
			"customer_id" =>$customer_id,
			"dealer_id"   =>$dealer_id,
			"latitude"    =>$latitude,
			"longitude"   =>$longitude,
			"app_address" =>$app_address,
			"remark"      =>$remark,
			"title"       =>$title,
		);
		
		$Where = "id='".$id."'";
		$eid = $this->db->rp_update($this->ctable,$rows,$Where,0);
		
		$image_path=array();
		/*if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
		{
			$ri = $eid;
			$rt = "complain";
			$tc = "complain";
			$rc = "id";
			for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
			{
				//print_r($file["image_path"]);
				$file_name = $file['image_path']['name'][$i];
				$file_size = $file['image_path']['size'][$i];
				$file_tmp = $file['image_path']['tmp_name'][$i];
				$file_type = $file['image_path']['type'][$i];
				$extension=explode(".",$file_name);
				
				$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
				$extension=$extension[sizeof($extension)-1];
				if(!in_array($extension,$allowed_extentions))
				{
					$file_error=true;
				}
				$orignal_file_name=$extension[0];
				if(in_array($extension,$allowed_extentions))
				{
					$attachment="../resource/image/";
					move_uploaded_file($file_tmp,$attachment.$file_name);
				}
				$MediaTitle=$file_name;
		    	$MediaOrignalTitle=$file_name;

				$MediaFileName=$file_name;
				// $MediaType=User::$ValidMediaType[$extension];
				$UploadDate=date("Y-m-d H:i:s");
				
				// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
				$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
				// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
				$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
				$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);

				$image_path[] = $MediaID;
			}
			$image_path = implode(",", $image_path);
			$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$eid."'",0);
		}*/		

		if($eid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Complain Update successfully!!","ack_msg"=>"Complain Update successfully!!");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Update");
			return $reply;
		}
	}

	public function AddRequestPanel($detail,$file)
	{	
		extract($detail);
		$request_date = date('Y-m-d',strtotime($request_date));
		$value1=$this->db->getlastInsertId($this->ctable);
		$request_no1=REQUEST_NO.str_pad($value1, 3, '0', STR_PAD_LEFT);
		$rows_insert 	= array("user_id","customer_id","request_type","request_cat_id","request_subcat_id","remark","state","city","zone","request_created_by","request_assign_to","request_date","request_no","isActive","entry_flag");
		$values_insert = array($request_created_by,$customer_id,$request_type,$request_cat_id,$request_subcat_id,$remark,$state,$city,$zone,$request_created_by,$request_assign_to,$request_date,$request_no1,1,$entry_flag);
		$insert = $this->db->rp_insert($this->ctable,$values_insert,$rows_insert,0);
		if($insert!=0)
		{
			/*image code*/
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0)
			{
				$ri = $insert;
				$rt = "request";
				$tc = "request";
				$rc = "id";
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					$file_name = $file['image_path']['name'][$i];
					$file_size = $file['image_path']['size'][$i];
					$file_tmp = $file['image_path']['tmp_name'][$i];
					$file_type = $file['image_path']['type'][$i];
					$extension=explode(".",$file_name);

					$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
					$extension=$extension[sizeof($extension)-1];
					if(!in_array($extension,$allowed_extentions))
					{
						$file_error=true;
					}
					$orignal_file_name=$extension[0];
					if(in_array($extension,$allowed_extentions))
					{
						$attachment="../resource/image/";
						move_uploaded_file($file_tmp,$attachment.$file_name);
					}

					$MediaTitle=$file_name;
		    		$MediaOrignalTitle=$file_name;
		    		$MediaFileName=$file_name;
		    		$UploadDate=date("Y-m-d H:i:s");


					$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
					$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
					$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);
					$image_path[] = $MediaID;
				}

				$image_path = implode(",", $image_path);
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$insert."'",0);
			}
			/*image code*/
			$reply=array("ack"=>1,"developer_msg"=>"Complain Add successfully!!","ack_msg"=>"Complain Add successfully!!","id"=>$insert);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Add");
			return $reply;
		}
	}
}
?>