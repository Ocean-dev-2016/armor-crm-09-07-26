<?php
require_once("main.class.php");
require_once("function.class.php");
class Meeting extends Functions
{
	public $db;
	public $ctable="meeting";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertMeeting($detail,$file) 
	{	
		extract($detail);
		$dup_where = "title1 = '".$title1."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist This Title","ack_msg"=>"Duplication! Already Exist This Title");
			return $reply;
		}
		else
		{
			$meeting_date= date('Y-m-d H:i:s',strtotime($meeting_date));
			$customer_type = $this->db->rp_getValue("executive","type_of_executive","id='".$customer_id."'"); 
			$rows 	= array(
						"dealer_id",
						"customer_id",
						"customer_type",
						"meeting_type",
						/*"meeting_host",
						"meeting_host_name",
						"title",*/
						"meeting_date",
						"meeting_venue",
						"gift_details",
						"expence",
						"sales_id",
					);
			$values = array(
						$dealer_id,
						$customer_id,
						$customer_type,
						$meeting_type,	
						/*$meeting_host,	
						$meeting_host_name,	
						$title,	*/
						$meeting_date,	
						$meeting_venue,	
						$gift_details,	
						$expence,	
						$sales_id,	
					);
					// print_r($customer_type);exit();
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $uid;
				$rt = "meeting";
				$tc = "meeting";
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
				$upadateid = $this->db->rp_update("meeting",array("image_path"=>$image_path),"id='".$uid."'",0);
			}
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Meeting Added.","ack_msg"=>"Success! Meeting Insert Successfully.","id"=>$uid);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Meeting Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateMeeting($detail)
	  {
	  	// print_r($detail);exit;
			extract($detail);
			$dup_where = "title1 = '".$title."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist This Title","ack_msg"=>"Duplication! Already Exist This Title");
				return $reply;
				
			}else{
				$meeting_date= date('Y-m-d H:i:s',strtotime($meeting_date));
				$customer_type = $this->db->rp_getValue("executive","type_of_executive","id='".$customer_id."'");
				// $meeting_date= date('Y-m-d H:i:s',strtotime($meeting_date));
				$rows 	= array(
						"dealer_id"                 => $dealer_id,
						"customer_id"               => $customer_id,
						"meeting_type"				=> $meeting_type,
						"customer_type"				=> $customer_type,
						/*"meeting_host"				=> $meeting_host,
						"meeting_host_name"			=> $meeting_host_name,
						"title"						=> $title,*/
						"meeting_date"				=> $meeting_date,
						"gift_details"				=> $gift_details,
						"expence"				=> $expence,
						"meeting_venue"				=> $meeting_venue,
						"sales_id"				    => $sales_id,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
				$image_path=array();
				if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
				{
					$ri = $uid;
					$rt = "meeting";
					$tc = "meeting";
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
					$upadateid = $this->db->rp_update("meeting",array("image_path"=>$image_path),"id='".$uid."'",0);
				}
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Meeting Update Successfull!!.","ack_msg"=>"Success! Meeting Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Meeting Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataMeeting($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['customer_id']		= $ctable_d['customer_id'];
		$result['customer_type']		= $ctable_d['customer_type'];
		$result['meeting_type']		= htmlentities($ctable_d['meeting_type']);
		$result['meeting_host']		= htmlentities($ctable_d['meeting_host']);
		$result['meeting_host_name']		= htmlentities($ctable_d['meeting_host_name']);
		$result['title']		= htmlentities($ctable_d['title']);
		$result['meeting_date']		= date("d-m-Y h:i A",strtotime($ctable_d['meeting_date']));
		$result['meeting_venue']		= htmlentities($ctable_d['meeting_venue']);
		$result['gift_details']		= htmlentities($ctable_d['gift_details']);
		$result['expence']		= htmlentities($ctable_d['expence']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Meeting detail fetched!!.","ack_msg"=>"Success! Meeting Edit Successfully.","result"=>$result);
		return $reply;
	
	}

	public function upload_member_image($meeting_id,$member_id,$file)
	{
		$image_path=array();
		if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
		{
			$ri = $member_id;
			$rt = "meeting_member";
			$tc = "meeting_member";
			$rc = "id";
			for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
			{
				//print_r($file["image_path"]);
				$file_name = $file['image_path']['name'];
				$file_size = $file['image_path']['size'];
				$file_tmp = $file['image_path']['tmp_name'];
				$file_type = $file['image_path']['type'];
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
			$upadateid1 = $this->db->rp_update("meeting_member",array("image_path"=>$image_path),"meeting_id='".$meeting_id."' AND member_id='".$member_id."'",0);
			if($upadateid1!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Meeting Update Successfully!!.","ack_msg"=>"Success! Meeting Update Successfully.");
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Meeting Update Failed.");
			}
			return $reply;
		}
	}
	
	
}

?>