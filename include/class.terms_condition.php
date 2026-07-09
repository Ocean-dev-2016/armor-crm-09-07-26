<?php
require_once("main.class.php");
require_once("function.class.php");
class Designation extends Functions
{
	public $db;
	public $ctable="terms_condition";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertData($detail) 
	{ 
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Name","ack_msg"=>"Duplication! Already Exist Name.");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"name",
				"description",
				"isDelete",
			);
			$values = array(
				$name,	
				$description,	
				$isDelete,
			);

			/*log entry*/
			$module_name = "Terms & Condition";
			$flag = "Web";
			$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Designation Added.","ack_msg"=>"Success! Data Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateData($detail)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Name","ack_msg"=>"Duplication! Already Exist Name.");
				return $reply;
				
			}else{
				$rows 	= array(
					"name"				=> $name,
					"description"		=> $description,
				);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
				$module_name = "Terms & Condition";
				$flag = "Web";
				$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Designation Update Successfull!!.","ack_msg"=>"Success! Data Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditData1($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_assoc($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['description']		= htmlentities($ctable_d['description']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Data detail fetched!!.","ack_msg"=>"Success! Data Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteData($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='".$_REQUEST['id']."'";
		/*log entry*/
		$name = $this->db->rp_getValue("terms_condition","name","id='".$_REQUEST['id']."'");
		$module_name = "Terms & Condition";
		$flag = "Web";
		$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/
		$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Purpose Failed.");
			return $reply;
		}
	}
}
