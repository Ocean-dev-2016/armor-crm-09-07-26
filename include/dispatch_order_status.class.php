<?php
require_once("main.class.php");
require_once("function.class.php");
class Unit extends Functions
{
	public $db;
	public $ctable="dispatch_order_status";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertUnit($detail) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Name","ack_msg"=>"Duplication! Already Exist Name.");
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

			/*log entry*/
				$module_name = "Dispatch Order Status";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Unit insert successfully!!.","ack_msg"=>"success! Insert Dispatch Order Status Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Dispatch Order Status insert Failed.");
				return $reply;
			}
		}
	}
	 
	 // public function UpdateUnit($detail)
	 //  {
		// 	extract($detail);
		// 	$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		// 	$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		// 	if($r){
		// 		$reply=array("ack"=>1,"developer_msg"=>"Already Exist Name","ack_msg"=>"Duplication! Already Exist Name.");
		// 		return $reply;
				
		// 	}else{
		// 		$rows 	= array(
		// 				"name"				=> $name,
		// 				);
		// 		$where	= "id='".$_REQUEST['id']."'";
		// 		$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
		// 		if($uid!=0)
		// 		{
		// 			$reply=array("ack"=>1,"developer_msg"=>"Unit Update Successfull!!.","ack_msg"=>"Success! Update Unit Successfully.");
		// 			return $reply;
		// 		}
		// 		else
		// 		{
		// 			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Unit Update Failed.");
		// 			return $reply;
		// 		}
		// 	}	
		// }	


		public function UpdateUnit($detail)
	{
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Duplicate Hsn Code","ack_msg"=>"Duplication! Already Exist Dispatch Order Status.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						
						);
				$where	= "id='".$_REQUEST['id']."'";

				/*log entry*/
					$module_name = "Dispatch Order Status";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/

				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$update_rows 	= array("name"=> $name,);
					$where	= "id='".$_REQUEST['id']."'";
					$uid=$this->db->rp_update($this->ctable,$update_rows,$where,0,$log_description,$flag,$module_name,"","");
					$reply=array("ack"=>1,"developer_msg"=>"Class Updated Successfully!!.","ack_msg"=>"Success!Dispatch Order Status Updated Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Dispatch Order Status Could Not Be Updated .");
					return $reply;
				}
			}	
		}	
	public function EditUnit($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Unit detail fetched!!.","ack_msg"=>"Unit Successfull.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteUnit($detail)
	{
		// echo "ggg";exit();
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";

			/*log entry*/
				$name = $this->db->rp_getValue("brand","name","id='".$_REQUEST['id']."'");
				$module_name = "Dispatch Order Status";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Unit Record deleted!!","ack_msg"=>"Success! Delete Dispatch Order Status Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Dispatch Order Status Failed.");
				return $reply;
			}
	}


	// public function DeleteUnit($detail)
	// {
	// 	$rows 	= array(
	// 	"isDelete"	=> "1"
	// 	);
	// 		$where	= "id='".$_REQUEST['id']."'";
	// 		$uid=$this->db->rp_update($this->ctable,$rows,$where);
	// 		if($uid!=0)
	// 		{
	// 			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete HSN Data Successfully.");
	// 			return $reply;
	// 		}
	// 		else
	// 		{
	// 			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete HSN Data Failed.");
	// 			return $reply;
	// 		}
	// }
}

?>