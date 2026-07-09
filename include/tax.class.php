<?php
require_once("main.class.php");
require_once("function.class.php");
class Tax extends Functions
{
	public $db;
	public $ctable="tax";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function TaxInsert($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate Tax","ack_msg"=>"Duplication! Already Exist Name.");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"value",
						"isDelete",
						"adate"
					);
			$values = array(
						$name,
						$value,		
						$isDelete,
						$adate
					);

			/*log entry*/
				$module_name = "Tax";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Tax Added.","ack_msg"=>"Success! Insert Tax Record Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Insert Tax Record Failed.");
				return $reply;
			}
		}
	}
	 
	 public function TaxUpdate($detail)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Duplicate Tax","ack_msg"=>"Duplication! Already Exist Name.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						"value"				=>	$value,
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Tax";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Tax Update Successfull!!.","ack_msg"=>"Success! Update Tax Record Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Update Tax Failed.");
					return $reply;
				}
			}	
		}	
	public function TaxGetEditData($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['value']	= stripslashes($ctable_d['value']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Tax detail fetched!!.","ack_msg"=>"Success! Update Tax Record Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function TaxDelete($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("tax","name","id='".$_REQUEST['id']."'");
				$module_name = "Tax";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Tax Record Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Tax Record Failed.");
				return $reply;
			}
	}
}

?>