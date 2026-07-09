<?php
require_once("main.class.php");
require_once("function.class.php");
class ExpenceSubCategory extends Functions
{
	public $db;
	public $ctable="expence_sub_category";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertExpenceSubCategory($detail,$file) 
	{
		// print_r($detail);exit;
		extract($detail);
		$dup_where = "name = '".$name."' AND expense_category_id = '".$expense_category_id."' AND sales_executive_id='".$sales_executive_id."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Expence Sub Category Name","ack_msg"=>"Duplication! Already Exist Expence Sub Category Name.","sales_executive_id"=>$sales_executive_id);
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
					$filePath 	= EXPENCE_SUB_CATEGORY_A.$image_path;	
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
						"slug",
						"expense_type",
						"image_path",
						"isDelete",
						"expense_category_id",
						"image_flag",
						"fix_amount",
						"min_time",
						"max_time",
						"sales_executive_id",
					);
			$values = array(
						$name,	
						$slug,
						$expense_type,
						$image_path,
						$isDelete,
						$expense_category_id,
						$image_flag,
						$fix_amount,
						$min_time,
						$max_time,
						$sales_executive_id,
					);

			/*log entry*/
				$module_name = "Expense Sub Category";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Expence Sub Category Added.","ack_msg"=>"Success! Expence Sub Category Insert Successfully.","sales_executive_id"=>$sales_executive_id);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expence Sub Category Insert Failed.","sales_executive_id"=>$sales_executive_id);
				return $reply;
			}
		}
	}


	
	 
	 public function UpdateExpenceSubCategory($detail,$file)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND expense_category_id='".$expense_category_id."' AND sales_executive_id='".$sales_executive_id."' AND id!='".$_REQUEST['id']."' AND isDelete=0  ";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Expence Sub Category Name","ack_msg"=>"Duplication! Already Exist Expence Sub Category Name.","sales_executive_id"=>$sales_executive_id);
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
							$filePath 	= EXPENCE_SUB_CATEGORY_A.$image_path;	
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
						"name"					=> $name,
						"slug"					=> $slug,
						"expense_type"          => $expense_type,
						"image_path"			=> $image_path,
						"expense_category_id"	=> $expense_category_id,
						"image_flag"			=> $image_flag,
						"fix_amount"			=> $fix_amount,
						"min_time"				=> $min_time,
						"max_time"				=> $max_time,
						"sales_executive_id"	=> $sales_executive_id,
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Expense Sub Category";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Expence Sub Category Update Successfull!!.","ack_msg"=>"Success! Expence Sub Category Update Successfully.","sales_executive_id"=>$sales_executive_id);
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expence Sub Category Update Failed.","sales_executive_id"=>$sales_executive_id);
					return $reply;
				}
			}	
		}	
	public function GetEditDataExpenceSubCategory($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['image_path'] = $ctable_d['image_path'];
		$result['expense_category_id'] = htmlentities($ctable_d['expense_category_id']);
		$result['expense_type'] = $ctable_d['expense_type'];
		$result['image_flag'] = $ctable_d['image_flag'];
		$result['fix_amount'] = $ctable_d['fix_amount'];
		$result['min_time'] = $ctable_d['min_time'];
		$result['max_time'] = $ctable_d['max_time'];
		$result['sales_executive_id'] = $ctable_d['sales_executive_id'];
		
		$reply=array("ack"=>1,"developer_msg"=>"Expence Sub Category detail fetched!!.","ack_msg"=>"Success! Category Edit Expence Sub Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteExpenceSubCategory($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("expence_sub_category","name","id='".$_REQUEST['id']."'");
				$module_name = "Expense Sub Category";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Expence Sub Category Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Expence Sub Category Failed.");
				return $reply;
			}
	}
}

?>