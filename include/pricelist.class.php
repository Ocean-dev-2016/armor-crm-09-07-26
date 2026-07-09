<?php
require_once("main.class.php");
require_once("function.class.php");
class Pricelist extends Functions
{
	public $db;
	public $ctable="price_list";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertPricelist($detail) 
	{
		extract($detail);
		$dup_where = "pricelist_name = '".$name."' AND isDelete=0";
		$pricelist_slug=$this->db->clean($this->db->rp_createProSlug($name));
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Pricelist Name","ack_msg"=>"Duplication! Already Exist Pricelist Name.");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"pricelist_name",
				"pricelist_slug",
				"tcid",
				"state_id",
				"is_premium",
				"isDelete"
			);
			$values = array(
				$name,	
				$pricelist_slug,	
				$tcid,	
				$state_id,	
				$is_premium,	
				$isDelete
			);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Pricelist Added.","ack_msg"=>"Success! Pricelist Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Pricelist Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdatePricelist($detail)
	  {
			extract($detail);
			$dup_where = "pricelist_name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$pricelist_slug=$this->db->clean($this->db->rp_createProSlug($name));
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Pricelist Name","ack_msg"=>"Duplication! Already Exist Pricelist Name.");
				return $reply;
				
			}else{
				$rows 	= array(
					"pricelist_name"	=> $name,
					"pricelist_slug"	=> $pricelist_slug,
					"tcid"				=> $tcid,
					"state_id"			=> $state_id,
					"is_premium"		=> $is_premium,
				);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Pricelist Update Successfull!!.","ack_msg"=>"Success! Pricelist Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Pricelist Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataPricelist($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['pricelist_name']	= htmlentities($ctable_d['pricelist_name']);
		$result['is_premium']		= htmlentities($ctable_d['is_premium']);
		$result['state_id']		= htmlentities($ctable_d['state_id']);
		$result['tcid']		= htmlentities($ctable_d['tcid']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Pricelist detail fetched!!.","ack_msg"=>"Success! Pricelist Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
}

?>