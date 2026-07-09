<?php
require_once("main.class.php");
require_once("function.class.php");
class Area extends Functions
{
	public $db;
	public $ctable="area";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function areaInsert($detail,$item) 
	{
		$error=array();
		extract($detail);
		if(!empty($item))
		{
			$ctable_d=$this->db->rp_getData($this->ctable,"*","isDelete=0 AND class_id='".$class_id."'","",0);
			while($ctable_r=mysqli_fetch_assoc($ctable_d))
			{
				$area_all[]=array("name"=>$ctable_r['name']);
			}
			$foundflag=false;
			// For loop
			$where="class_id='".$class_id."'";
			foreach($item as $i)
			{
				$isDuplicate=$this->db->rp_getValue($this->ctable,"id","name='".$i['name']."' and class_id='".$class_id."'",0);
				if($isDuplicate)
				{
					$error[]=$i['name']." is already exist with same class.";
				}
				else{
					$rows 	= array(
						"name",
						"class_id",
						"isDelete"
					);
					$values = array(
						$i['name'],
						$class_id,
						$isDelete
					);

					/*log entry*/
						$module_name = "City";
						$flag = "Web";
						$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
					/*log entry*/

					$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
				}							
			}
			if($uid!=0)
			{
				if(!empty($error))
				$area_error_msg="Some area already exists within same class <br/>".implode("<br/>",$error);
				else
				$area_error_msg="";	
				$reply=array("ack"=>1,"developer_msg"=>"Area Updated Successfully!!.","ack_msg"=>"Success! Area Updated Successfully.".$area_error_msg);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Area Could Not Be Updated .");
				return $reply;
			}
		}	
	}
	public function areaUpdate($detail,$item)
	{
			$error=array();
			extract($detail);
			
				if(!empty($item))
				{
					
					
					$ctable_d=$this->db->rp_getData($this->ctable,"*","isDelete=0 AND class_id='".$class_id."'","",0);
					while($ctable_r=mysqli_fetch_assoc($ctable_d))
					{
						$area_all[]=array("name"=>$ctable_r['name']);
					
					}
					$foundflag=false;
					// For loop
					$where="class_id='".$class_id."'";
					
					$deleted=$this->db->rp_delete("area",$where,0);
					foreach($item as $i)
					{
							$isDuplicate=$this->db->rp_getValue($this->ctable,"id","name='".$i['name']."' and class_id='".$class_id."'",0);
							if($isDuplicate)
							{
								$error[]=$i['name']." is already exist with same class.";
							}
							else{
								$rows 	= array(
									"name",
										"class_id",
										"isDelete"
									);
								$values = array(
											$i['name'],
											$class_id,
											$isDelete
										);

								/*log entry*/
									$module_name = "City";
									$flag = "Web";
									$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
								/*log entry*/
										
								$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
							}							
								
							
							
					}
						
						
						
						//$uid=$this->db->rp_update($this->ctable,$rows,$where);
					}
					/*foreach($area_all as $area){
					
					if(!(in_array($current_item['name'],$array))){
						echo $current_item['name'];
					}
					}*/
					
				
				if($uid!=0)
				{
					if(!empty($error))
					$area_error_msg="Some area already exists within same class <br/>".implode("<br/>",$error);
					else
					$area_error_msg="";	
					$reply=array("ack"=>1,"developer_msg"=>"Area Updated Successfully!!.","ack_msg"=>"Success! Area Updated Successfully.".$area_error_msg);
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Area Could Not Be Updated .");
					return $reply;
				}
			}	
			
	public function areaGetEditData($detail)
	{		
		$where = " class_id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"id DESC");
		if($ctable_r)
		{
			while(	$ctable_d = mysqli_fetch_array($ctable_r))
			{
				$result_item=array();
				
				$result_item['name']		= htmlentities($ctable_d['name']);
				$result_item['area_id']		= htmlentities($ctable_d['id']);
				$result_item['class_id']		= htmlentities($ctable_d['class_id']);
				$result[]=$result_item;
			}
			$reply=array("ack"=>1,"developer_msg"=>"Class detail fetched!!.","ack_msg"=>"Success! Area Record Fetched Successfully.","result"=>$result);
				return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Area detail not fetched!!.","ack_msg"=>"Error! Area Record Not Found");
			return $reply;
		}
		
	}	
	public function areaDelete($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			//$where	= "id='".$detail['id']."'";
			$where	= "class_id ='".$detail['id']."'";

			/*log entry*/
				$name = $this->db->rp_getValue("area","name","id='".$_REQUEST['id']."'");
				$module_name = "City";
				$flag = "Web";
				$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/

			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted Area.","ack_msg"=>"Success! Area Record Deleted Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Area Record Could Not Be Deleted.");
				return $reply;
			}
	}
	public function areaActive($detail)
	{
		$rows 	= array(
			"isActive"	=> $detail['isActive']
		);
			$where	= "id='".$detail['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Active status changed of Area.","ack_msg"=>"Success! Area Record Status Updated Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Area Record Status Could Not Be Updated.");
				return $reply;
			}
	}
	
}

?>