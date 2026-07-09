<?php
require_once("main.class.php");
require_once("function.class.php");
class ComplainCategory extends Functions
{
	public $db;
	public $ctable="complain_category";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertComplainCategory($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Complain Category","ack_msg"=>"Duplication! Already Exist Complain Category.");
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
				$module_name = "Complain Category";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Complain Category Added.","ack_msg"=>"Success! Complain Category Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Category Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateComplainCategory($detail)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Complain Category","ack_msg"=>"Duplication! Already Exist Complain Category.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Complain Category";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/

				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Complain Category Update Successfull!!.","ack_msg"=>"Success! Complain Category Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Category Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataComplainCategory($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Complain Category detail fetched!!.","ack_msg"=>"Success! Complain Category Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteComplainCategory($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("complain_category","name","id='".$_REQUEST['id']."'");
				$module_name = "Complai Category";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Complain Category Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Complain Category Failed.");
				return $reply;
			}
	}
}

?>