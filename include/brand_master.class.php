<?php
require_once("main.class.php");
require_once("function.class.php");
class TopCategory extends Functions
{
	public $db;
	public $ctable="brand";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertCategory($detail,$file) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Category Name","ack_msg"=>"Duplication! Already Exist Brand Name.");
			return $reply;
		}
		else
		{

			if (isset($file["image_path"]) ) {
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
					$filePath 	= TOP_CATEGORY_A.$image_path;	
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
			$rows 	= array(
						"name",
						"image_path",
						"isDelete"
					);
			$values = array(
						$name,	
						$image_path,
						$isDelete
					);

			/*log entry*/
				$module_name = "Brand";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Top Category Added.","ack_msg"=>"Success! Brand Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Brand Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateCategory($detail,$file)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist TopCategory Name","ack_msg"=>"Duplication! Already  Exist Brand Name.");
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
							$filePath 	= TOP_CATEGORY_A.$image_path;	
							$file['image_path']['tmp_name'];
							move_uploaded_file($file['image_path']['tmp_name'], $filePath);
							
							$new_image=true;
						}
						else{
							$image_path=$detail['old_image_path'];
							$image_path="";
						}
					}
					else
					{
						$image_path=$detail['old_image_path'];
  						unset($detail['old_image_path']);
					}

				$rows 	= array(
						"name"				=> $name,
						"image_path"		=> $image_path,
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Brand";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Top Category Update Successfull!!.","ack_msg"=>"Success! Brand Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Brand Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataCategory($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['image_path'] = $ctable_d['image_path'];
		
		$reply=array("ack"=>1,"developer_msg"=>"Top Category detail fetched!!.","ack_msg"=>"Success! Top Category Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteCategory($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("brand","name","id='".$_REQUEST['id']."'");
				$module_name = "Brand";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Brand Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Brand Failed.");
				return $reply;
			}
	}
}
