<?php
require_once("main.class.php");
require_once("function.class.php");
class Production extends Functions
{
	public $db;
	public $ctable="production_process";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertProcess($detail) 
	{
		extract($detail);
		$dup_where = "process_name = '".$process_name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"already exist Process","ack_msg"=>"already exist this process name !");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"process_name",
						"description"
						
					);
			$values = array(
						$process_name,
						$description
						
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Production Process Added.","ack_msg"=>"Production Process Added Successfull.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Production Process added Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateProcess($detail)
	  {
			extract($detail);
			$rows 	= array(
						"process_name"				=> $process_name,
						"description"				=>	$description,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where);
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Production Process Update Successfull!!.","ack_msg"=>"Production Process Update Successfull.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Production Process Update Failed.");
					return $reply;
				}
		}	
	public function ProcessGetEditData($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['process_name']		= htmlentities($ctable_d['process_name']);
		$result['description']	= stripslashes($ctable_d['description']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Production Process detail fetched!!.","ack_msg"=>"Production Process detail fetched Successfull.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteProcess($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Production Process Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Delete Production Process Failed.");
				return $reply;
			}
	}
}

?>