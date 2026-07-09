<?php
require_once("main.class.php");
require_once("function.class.php");
class Shift extends Functions
{
	public $db;
	public $ctable="working_shift";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertShift($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate Tax","ack_msg"=>"Duplicate name !");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"start_time",
						"end_time",
						"isDelete"
					);
			$values = array(
						$name,
						$start_time,		
						$end_time,		
						$isDelete
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"working shift Added.","ack_msg"=>"working shift Added Successfull.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"working shift Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateShift($detail)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>1,"developer_msg"=>"Duplicate shift","ack_msg"=>"Duplicate shift name.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						"start_time"		=>	$start_time,
						"end_time"			=>	$end_time,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where);
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Shift Update Successfull!!.","ack_msg"=>"Shift Successfull.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Failed.");
					return $reply;
				}
			}	
		}	
	public function EditShift($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']			= htmlentities($ctable_d['name']);
		$result['start_time']	= stripslashes($ctable_d['start_time']);
		$result['end_time']		= stripslashes($ctable_d['end_time']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Shift detail fetched!!.","ack_msg"=>"Shift Successfull.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteShift($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Delete Successfull.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Delete record Failed.");
				return $reply;
			}
	}
}

?>