<?php
require_once("main.class.php");
require_once("function.class.php");
class SelfAnalysis extends Functions
{
	public $db;
	public $ctable="self_analysis_master";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertSelfAnalysis($detail) 
	{
		// print_r($file);exit;
		extract($detail);
		$dup_where = "questions = '".$questions."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Questions","ack_msg"=>"Duplication! Already Exist!!!");
			return $reply;
		}
		else
		{
			$rows 	= array(
						"questions",
						"isDelete"
					);
			$values = array(
						$questions,
						$isDelete
					);

			/*log entry*/
				$module_name = "Self Analysis";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,10,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Questions Added.","ack_msg"=>"Success!  Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed!  Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateSelfAnalysis($detail)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist ","ack_msg"=>"Duplication! Already Exist");
				return $reply;
				
			}else{

				
				
				$rows 	= array(
						"questions"					=> $questions,
						
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Self Analysis";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Company Update Successfull!!.","ack_msg"=>"Success! Company Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Company Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataCompany($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['questions']		= htmlentities($ctable_d['questions']);
		
		
		$reply=array("ack"=>1,"developer_msg"=>" detail fetched!!.","ack_msg"=>"Success!  Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteSelfAnalysis($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("self_analysis_master","questions","id='".$_REQUEST['id']."'");
				$module_name = "Self Analysis";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete  Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete  Failed.");
				return $reply;
			}
	}
}

?>