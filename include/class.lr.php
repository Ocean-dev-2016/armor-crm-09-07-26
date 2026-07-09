\<?php
require_once("main.class.php");
require_once("function.class.php");
class LRDetail extends Functions
{
	public $db;
	public $ctable="lr_detail";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertLR($detail,$file) 
	{	
		
		extract($detail);
		$dup_where = "dispatch_no = '".$dispatch_id."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r)
		{
			if (isset($file["image_path"])) 
			{
				// print_r($file);exit();
				// $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
			 	$extension = end($temp);
			 
					$fileName 	= $this->db->clean($file["image_path"]["name"]);	
						// echo "heelo";exit();
					if($fileName!="")
					{
						$fileSize 	= round($file["image_path"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');
						
						$extension	= end(explode(".", $fileName));		
						if(!in_array($extension,$allowedExts))
						{
							$file_error=true;
						}
											
						$image_path	= 'lr_documents'.substr(sha1(time()), 0, 6).".".$extension;
						$filePath 	= LRCOPY_A.$image_path;	
						$file['image_path']['tmp_name'];
						// print_r($filePath); exit;
						move_uploaded_file($file['image_path']['tmp_name'], $filePath);
						$image_path=$image_path;
						unset($old_image_path);
					}
					else{
						$image_path=$old_image_path;	
    					unset($old_image_path);
					}
			}

			$rows 	= array(
						"image_path"=> $image_path,
						);

			$where	= "dispatch_no='".$dispatch_id."'";

			/*log entry*/
				$module_name = "Company";
				$flag = "Web";
				$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");

		}
		else
		{
			if (isset($file["image_path"])) 
			{
				// print_r($file);exit;
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
											
					$image_path	= 'lr_documents'.substr(sha1(time()), 0, 6).".".$extension;
					$filePath 	=  LRCOPY_A.$image_path;	
					$file['image_path']['tmp_name'];
					move_uploaded_file($file['image_path']['tmp_name'], $filePath);
					$new_image=true;
				}
				else
				{
					$image_path="";
				}
			}
			else
			{
				$new_image=false;
				$image_path="";
			}

			$adate	= date('Y-m-d H:i:s');
			$rows 	= array("dispatch_no","invoice_id","lr_number","image_path","remark","isDelete");
			$values = array($dispatch_id,"","",$image_path,"",$isDelete);
						
			$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Lr detail Added.","ack_msg"=>"Success! lr Detail Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Lr detail  Insert Failed.");
				return $reply;
			}
		}
	}

	public function DeleteLR($detail)
	{
	    $rows 	= array(
		"isDelete"	=> "1"
		);
		$where	= "id='".$_REQUEST['id']."'";
		$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Leave Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Leave Failed.");
			return $reply;
		}
	}
}

?>