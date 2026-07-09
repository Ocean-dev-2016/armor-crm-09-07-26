<?php
require_once("main.class.php");
require_once("function.class.php");
class PackingType extends Functions
{
	public $db;
	public $ctable="packing_type";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 

	public function AddPackingType($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND weight = '".$weight."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r)
		{
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist This Data","ack_msg"=>"Duplication! Already Exist This Data.");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"weight",
						"isDelete"
					);
			$values = array(
						$name,	
						$weight,	
						$isDelete
					);

			/*log entry*/
				$module_name = "Packing Type";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		 	if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Data Added.","ack_msg"=>"Success! Data Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Insert Failed.");
				return $reply;
			}
		}
	}
	 
	// public function UpdatePackingType($detail)
	// {
	// 	extract($detail);
	// 	$dup_where = "name = '".$name."' AND weight = '".$weight."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
	// 	$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
	// 	if($r)
	// 	{
	// 		$reply=array("ack"=>1,"developer_msg"=>"Already Exist This Data","ack_msg"=>"Duplication! Already Exist This Data.");
	// 		return $reply;
	// 	}
	// 	else
	// 	{
	// 		$rows 	= array("name" => $name,"weight" => $weight);
	// 		$where	= "id='".$_REQUEST['id']."'";
	// 		/*log entry*/
	// 			$module_name = "Packing Type";
	// 			$flag = "Web";
	// 			$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
	// 		/*log entry*/
	// 		$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
	// 		if($uid!=0)
	// 		{
	// 			$reply=array("ack"=>1,"developer_msg"=>"Data Update Successfull!!.","ack_msg"=>"Success! Data Update Successfully.");
	// 			return $reply;
	// 		}
	// 		else
	// 		{
	// 			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Update Failed.");
	// 			return $reply;
	// 		}
	// 	}	
	// }

	public function UpdatePackingType($detail)
	{
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Duplicate Title Name","ack_msg"=>"Duplication! Already Exist Packing Type.");
				return $reply;
				
			}else{
				$rows 	= array("name" => $name,"weight" => $weight);
				$where	= "id='".$_REQUEST['id']."'";

				/*log entry*/
					$module_name = "Packing Type";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/

				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$update_rows 	= array("name"=> $name,);
					$where	= "id='".$_REQUEST['id']."'";
					$uid=$this->db->rp_update("state",$update_rows,$where,0,$log_description,$flag,$module_name,"","");
					$reply=array("ack"=>1,"developer_msg"=>"Packing Type Updated Successfully!!.","ack_msg"=>"Success! Packing Type Updated Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Packing Type Could Not Be Updated .");
					return $reply;
				}
			}	
		}	

	public function GetPackingType($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['weight']		= htmlentities($ctable_d['weight']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Data fetched!!.","ack_msg"=>"Success! Data Fetch Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeletePackingType($detail)
	{
		$rows 	= array("isDelete" => "1");
		$where	= "id='".$_REQUEST['id']."'";
		/*log entry*/
			$name = $this->db->rp_getValue("packing_type","name","id='".$_REQUEST['id']."'");
			$module_name = "Packing Type";
			$flag = "Web";
			$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/
		$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Data Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Data Failed.");
			return $reply;
		}
	}
}
