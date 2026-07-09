<?php
require_once("main.class.php");
require_once("function.class.php");
class Department extends Functions
{
	public $db;
	public $ctable="department";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertDepartment($detail,$process) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"already exist department","ack_msg"=>"Duplicate Department name !");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"code",
						"isDelete"
					);
			$values = array(
						$name,
						$code,		
						$isDelete
					);
			/*log entry*/
				$module_name = "Department";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				 $current_process=array();
			    // Insert Purchase Item
				if(!empty($process))
				{
					for($i=0;$i<sizeof($process);$i++)
					{
						$current_process=$process[$i];
					      $adate	= date('Y-m-d H:i:s');
							$rows 	= array(
							"process_id",
							"department_id",
							"isDelete"
						);
						$values = array(
						   $current_process['process_id'],
						   $uid,
                           0
						);
					    $department_map_process_id = $this->db->rp_insert("department_map_process",$values,$rows,0);
					}
                     $reply=array("ack"=>1,"developer_msg"=>"Department Added.","ack_msg"=>"Success! Department Insert Successfully.");
					 return $reply;
				}
				$reply=array("ack"=>1,"developer_msg"=>"Department Added.","ack_msg"=>"Success! Department Insert Successfully.");
						return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Department added Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateDepartment($detail,$process)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Department","ack_msg"=>"Duplicate Department name.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						"code"				=>	$code,
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Department";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$this->db->rp_delete("department_map_process","department_id='".$_REQUEST['id']."'",0);
					$department_id=$_REQUEST['id'];
					// For loop
                    for($i=0;$i<sizeof($process);$i++)
					{
						$current_process=$process[$i];
					      $adate	= date('Y-m-d H:i:s');
								$rows 	= array(
							"process_id",
							"department_id",
							"isDelete"
						);
						$values = array(
						   $current_process['process_id'],
						   $department_id,
                           0
						);
					    $department_map_process_id = $this->db->rp_insert("department_map_process",$values,$rows,0);
					 }
				
                  
				$reply=array("ack"=>1,"developer_msg"=>"Department Name Added.","ack_msg"=>"Success! Department Update Successfully.");
						return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Department Update Failed.");
					return $reply;
				}
			}	
		}
    public function GetProcess($detail)
	{

			$where = "department_id='".$_REQUEST['id']."' AND isDelete=0";
			$ctable_user = $this->db->rp_getData("department_map_process","*",$where,"",0);
			if($ctable_user)
			{

			while($ctable_user_d = mysqli_fetch_array($ctable_user))
			{
				$result_item=array();

				$result_item['id']				= htmlentities($ctable_user_d['id']);
				$result_item['process_id']		= htmlentities($ctable_user_d['process_id']);
				$result_item['department_id']		= htmlentities($ctable_user_d['department_id']);
				$result[]=$result_item;
				//print_r($result);
			}

			$reply=array("ack"=>1,"developer_msg"=>"Event User fetched!!.","ack_msg"=>"Success! Update Event User Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Event User not fetched!!.","ack_msg"=>"Success! Event User Fetched"	);
			return $reply;
		}

	}		
	public function DepartGetEditData($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['code']	= stripslashes($ctable_d['code']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Department detail fetched!!.","ack_msg"=>"Department Successfull.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteDepartment($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				$name = $this->db->rp_getValue("department","name","id='".$_REQUEST['id']."'");
				$module_name = "Department";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Department Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Delete Department Failed.");
				return $reply;
			}
	}
}

?>