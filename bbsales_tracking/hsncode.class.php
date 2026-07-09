<?php
require_once("main.class.php");
require_once("function.class.php");
class Category extends Functions
{
	public $db;
	public $ctable="hsncode_master";
	// echo "<pre>"; print_r($ctable); exit;
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertCategory($detail,$file) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Tax Category Name","ack_msg"=>"Duplication! Already Exist Category Name.");
			return $reply;
		}
		else
		{


			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"isDelete",
						"tax_id"
					);
			$values = array(
						$name,	
						$isDelete,
						$tcid
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Category Added.","ack_msg"=>"Success! Tax Category Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Tax Category Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateCategory($detail,$file)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND tax_id='".$tcid."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>1,"developer_msg"=>"Already Exist Category Name","ack_msg"=>"Duplication! Already Exist Tax Category Name.");
				return $reply;
				
			}else{


				$rows 	= array(
						"name"				=> $name,
						"tax_id"				=> $tcid,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Category Update Successfull!!.","ack_msg"=>"Success! Tax Category Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Tax Category Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDataCategory($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['tax_id']		= htmlentities($ctable_d['tax_id']);
		
		
		$reply=array("ack"=>1,"developer_msg"=>"Tax Category detail fetched!!.","ack_msg"=>"Success! Tax Category Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteCategory($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Tax Category Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Tax Category Failed.");
				return $reply;
			}
	}
}

?>