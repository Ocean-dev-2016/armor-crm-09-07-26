<?php
require_once("main.class.php");
require_once("function.class.php");
class ClassType extends Functions
{
	public $db;
	public $ctable="country";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function ClassTypeInsert($detail) 
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

			/*log entry*/
				$module_name = "State";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		 
			if($uid!=0)
			{
				/*insert to state*/
				/*	$inserted_row = array("name","country_id","isDelete");
					$inserted_value = array($name,1,0);
					$insert = $this->db->rp_insert("state",$inserted_value,$inserted_row,0,$log_description,$flag,$module_name,"","");*/
				/*insert to state*/
				$reply=array("ack"=>1,"developer_msg"=>"Class Added.","ack_msg"=>"Success! Country Added Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Country Could Not Be Added. ");
				return $reply;
			}
		}
	}	 
	public function ClassTypeUpdate($detail)
	{
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Duplicate Class","ack_msg"=>"Duplication! Already Exist Name.");
				return $reply;
				
			}else{
				$rows 	= array(
						"name"				=> $name,
						);
				$where	= "id='".$_REQUEST['id']."'";

				/*log entry*/
					$module_name = "State";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/

				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$update_rows 	= array("name"=> $name,);
					$where	= "id='".$_REQUEST['id']."'";
					$uid=$this->db->rp_update("state",$update_rows,$where,0,$log_description,$flag,$module_name,"","");
					$reply=array("ack"=>1,"developer_msg"=>"Class Updated Successfully!!.","ack_msg"=>"Success! Country Updated Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Country Could Not Be Updated .");
					return $reply;
				}
			}	
		}	
	public function ClassTypeGetEditData($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		if($ctable_r)
		{
			$ctable_d = mysqli_fetch_array($ctable_r);
			$result=array();
			
			$result['name']		= htmlentities($ctable_d['name']);
			
			$reply=array("ack"=>1,"developer_msg"=>"Class detail fetched!!.","ack_msg"=>"Success! Class Record Fetched Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Sales Type detail not fetched!!.","ack_msg"=>"Error! Sales Type Record Not Found");
			return $reply;
		}
	}	
	public function ClassTypeDelete($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$detail['id']."'";

			/*log entry*/
				$name = $this->db->rp_getValue("class","name","id='".$_REQUEST['id']."'");
				$module_name = "State";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/

			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			$uid=$this->db->rp_update("state",$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted Class Type.","ack_msg"=>"Success! Country Record Deleted Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Country Record Could Not Be Deleted.");
				return $reply;
			}
	}
	public function ClassTypeActive($detail)
	{
		$rows 	= array(
			"isActive"	=> $detail['isActive']
		);
			$where	= "id='".$detail['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Active status changed of Class.","ack_msg"=>"Success! Class Record Status Updated Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Class Record Status Could Not Be Updated.");
				return $reply;
			}
	}
	function getRequiredColumns($required_columns=array())
	{
		if(!empty($required_columns))
		{
			$required_columns_string=implode(",",$required_columns);
			return $required_columns_string;
		}
		else
		{
			return "*";
		}
	}
	function getClassDetail($required_columns)
	{	
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();		
		$result=array();
		$where="1=1";
		$data    = $this->db->rp_getData('class',$required_columns,$where,"",0,$limit);
		
		if($data)
		{
			while($row=mysqli_fetch_assoc($data))
			{
				$result[]=$row;
			}			
			return $result;
		}
		else
		{
			return $result;
		}	
		
	}
	
	function getAreaDetail($required_columns)
	{	
		$required_columns=$this->getRequiredColumns($required_columns);	
		$limit=$this->getLimit();		
		$result=array();
		$where="1=1";
		$data    = $this->db->rp_getData('area',$required_columns,$where,"",0,$limit);
		
		if($data)
		{
			while($row=mysqli_fetch_assoc($data))
			{
				$result[]=$row;
			}			
			return $result;
		}
		else
		{
			return $result;
		}	
		
	}
	function getAreaDetail_usingClassId($class_id,$sales_id)
	{
		/*$result=array();
		$where="class_id=".$_REQUEST['class_id']."";
		$data    = $this->db->rp_getData('area',"id,name,class_id",$where,"",0,$limit);
		
		if($data)
		{
			while($row=mysqli_fetch_assoc($data))
			{
				$result[]=$row;
			}			
			return $result;
		}
		else
		{
			return $result;
		}	
		*/


		if($sales_id!="")
		{
		 	$area_ids=array();
		 	$area_d=$this->db->rp_getData("sales_executive_map_area","area_id","sales_executive_id='".$sales_id."' AND isDelete=0","",0);
		 	if($area_d)
		 	{
				while($area=mysqli_fetch_assoc($area_d))
				{
				 $area_ids[]=$area['area_id'];
				}
		 	}
			else
			{
				$area_ids[]=0;
			}
			$area_ids=implode(",",$area_ids); 
			$where="id IN(".$area_ids.") AND isDelete=0 AND class_id='".$class_id."'";
		}
		else
		{
		 	$where="isDelete=0 AND class_id='".$class_id."'";
		}
		$result=$this->rp_getData("area","id,name,class_id,isDelete,isActive",$where,"name ASC",0);
        while($detail=mysqli_fetch_assoc($result))
       	{
           $p[]=$detail;
        }
		if(!empty($p))
		$reply=array("ack"=>1,"developer_msg"=>"Area detail found","ack_msg"=>"Area detail found.","result"=>$p);
		else
		$reply=array("ack"=>0,"developer_msg"=>"Area not mapped","ack_msg"=>"Area not mapped");
		return $reply;
	}
	function getLimit($limit=array())
	{
		//$limit=$this->db->getLimit();	
		if(!empty($limit) && array_key_exists("ul",$limit))
		{
			$ul=$limit['ul'];
			if(array_key_exists("ll",$limit) && $limit['ll']!="")
			{
				$ll=$limit['ll'];
			}
			else
			{
				$ll="18446744073709551615";
			}			
			$limit_string="".$ul.",".$ll;
			return $limit_string;
		}
		else
		{
			return "";
		}
	}
	
}

?>