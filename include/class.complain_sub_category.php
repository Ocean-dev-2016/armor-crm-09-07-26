<?php
require_once("main.class.php");
require_once("function.class.php");
class ComplainSubCategory extends Functions
{
	public $db;
	public $ctable="complain_sub_category";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertComplainSubCategory($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND complain_category_id = '".$complain_category_id."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Complain Sub Category","ack_msg"=>"Duplication! Already Exist Complain Sub Category.");
			return $reply;
		}
		else
		{
			$maximum_display_order=$this->db->rp_getValue($this->ctable,"MAX(display_order)","isDelete=0");
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"complain_category_id",
						"display_order",
						"isDelete"
					);
			$values = array(
						$name,
						$complain_category_id,
						$maximum_display_order+1,	
						$isDelete
					);

			/*log entry*/
				$module_name = "Complain Sub Category";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Complain Sub Category Added.","ack_msg"=>"Success! Complain Sub Category Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Sub Category Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateComplainSubCategory($detail)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND complain_category_id='".$complain_category_id."'  AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Complain Sub Category","ack_msg"=>"Duplication! Already Exist Complain Sub Category.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						"complain_category_id"				=> $complain_category_id,
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "complain Sub Category";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Complain Sub Category Update Successfull!!.","ack_msg"=>"Success! Complain Sub Category Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Sub Category Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataComplainSubCategory($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['complain_category_id']		= htmlentities($ctable_d['complain_category_id']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Complain Sub Category detail fetched!!.","ack_msg"=>"Success! Complain Sub Category Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteComplainSubCategory($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("complain_sub_category","name","id='".$_REQUEST['id']."'");
				$module_name = "Complain Sub Category";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Complain Sub Category Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Complain Sub Category Failed.");
				return $reply;
			}
	}
}

?>