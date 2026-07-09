<?php
require_once("main.class.php");
require_once("function.class.php");
class SalesType extends Functions
{
	public $db;
	public $ctable="sales_executive_type";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function SalesTypeInsert($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate Valve Type","ack_msg"=>"Duplication! Already Exist Name.");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"isDelete"
					);
			$values = array(
						$name,		
						$isDelete
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Valve Type Added.","ack_msg"=>"Success! Sales Type Added Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Sales Type Could Not Be Added. ");
				return $reply;
			}
		}
	}	 
	public function SalesTypeUpdate($detail)
	{
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Duplicate Sales Type","ack_msg"=>"Duplication! Already Exist Name.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where);
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Sales Type Updated Successfully!!.","ack_msg"=>"Success! Sales Type Updated Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Sales Type Could Not Be Updated .");
					return $reply;
				}
			}	
		}	
	public function SalesTypeGetEditData($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		if($ctable_r)
		{
			$ctable_d = mysqli_fetch_array($ctable_r);
			$result=array();
			
			$result['name']		= htmlentities($ctable_d['name']);
			
			$reply=array("ack"=>1,"developer_msg"=>"Sales Type detail fetched!!.","ack_msg"=>"Success! Sales Type Record Fetched Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Sales Type detail not fetched!!.","ack_msg"=>"Error! Sales Type Record Not Found");
			return $reply;
		}
	}	
	public function SalesTypeDelete($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$detail['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted Sales Type.","ack_msg"=>"Success! Sales Type Record Deleted Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Sales Type Record Could Not Be Deleted.");
				return $reply;
			}
	}
	public function SalesTypeActive($detail)
	{
		$rows 	= array(
			"isActive"	=> $detail['is_active']
		);
			$where	= "id='".$detail['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Active status changed of Sales Type.","ack_msg"=>"Success! Sales Type Record Status Updated Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Sales Type Record Status Could Not Be Updated.");
				return $reply;
			}
	}
	
}

?>