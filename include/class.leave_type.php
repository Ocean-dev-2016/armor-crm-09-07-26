<?php
require_once("main.class.php");
require_once("function.class.php");
class LeaveType extends Functions
{
	public $db;
	public $ctable="leave_type";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertLeaveType($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Leave Type","ack_msg"=>"Duplication! Already Exist Leave Type.");
			return $reply;
		}
		else
		{
			$maximum_display_order=$this->db->rp_getValue($this->ctable,"MAX(display_order)","isDelete=0");
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"display_order",
						"isDelete"
					);
			$values = array(
						$name,
						$maximum_display_order+1,	
						$isDelete
					);

			/*log entry*/
				$module_name = "Leav Type";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Leave Type Added.","ack_msg"=>"Success! Leave Type Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Leave Type Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateLeaveType($detail)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Leave Type","ack_msg"=>"Duplication! Already Exist Leave Type.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Leave Type";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/

				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Leave Type Update Successfull!!.","ack_msg"=>"Success! Leave Type Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Leave Type Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataLeaveType($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Leave Type detail fetched!!.","ack_msg"=>"Success! Leave Type Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteLeaveType($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("leave_type","name","id='".$_REQUEST['id']."'");
				$module_name = "Leave Type";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Leave Type Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Leave Type Failed.");
				return $reply;
			}
	}
}

?>