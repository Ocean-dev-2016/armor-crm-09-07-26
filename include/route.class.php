<?php
require_once("main.class.php");
require_once("function.class.php");
class Department extends Functions
{
	public $db;
	public $ctable="my_route";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertDepartment($detail,$process) 
	{

		// echo "<pre>"; print_r($detail); exit;
		

		extract($detail);
		
		// $dup_where = "date = '".date('Y-m-d',strtotime($date))."'  AND customer_id='".$seid."' AND isDelete=0";
		
		// $r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		// if($r){
		// 	$reply=array("ack"=>0,"developer_msg"=>"already exist Route","ack_msg"=>"Duplicate Route !");
		// 	return $reply;
		// }
		// else
		// {	
			// $state_name = $this->db->rp_getValue("executive","state"," isDelete=0 AND  id='".$seid."'",0);
			// $class_id = $this->db->rp_getValue("class","id"," isDelete=0 AND  name='".$state."'",0);
			// $city_name = $this->db->rp_getValue("executive","city"," isDelete=0 AND  id='".$seid."'",0);
			// $area_id = $this->db->rp_getValue("area","id"," isDelete=0 AND  name='".$city."'",0);

			if (!empty($seid)) {
				$isInsertSucces=0;
				$is_duplication_check=0;
				for ($k=0; $k < sizeof($seid) ; $k++) { 
					$sales_id1 = $this->db->rp_getValue("master_route","sales_id"," isDelete=0 AND  id='".$m_route_id."'",0);

					$is_duplication = $this->db->rp_dupCheck("my_route","isDelete=0 AND sales_id='".$sales_id1."' AND customer_id='".$seid[$k]."' AND DATE(date)='".date('Y-m-d',strtotime($date))."'");

					if ($is_duplication) {
						$is_duplication_check++;
					} else {

						$adate	= date('Y-m-d H:i:s');
						$rows 	= array(
									"route_id",
									"sales_id",
									"customer_id",
									"date",

									// "class_id",
									// "area_id",
									// "state",
									// "city",

									"isDelete",
									"remark"
								);
						$values = array(
									$m_route_id,
									$sales_id1,
									$seid[$k],
									
									date('Y-m-d',strtotime($date)),	

									// $class_id,	
									// $area_id,
										
									// $state,
									// $city,
									$isDelete,
									$remark
								);
						/*log entry*/
							$module_name = "Department";
							$flag = "Web";
							$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
						/*log entry*/
					 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
					
						if($uid!=0)
						{
							$isInsertSucces++;
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
			                     // $reply=array("ack"=>1,"developer_msg"=>"Route Added.","ack_msg"=>"Success! Route Insert Successfully.");
								 // return $reply;
							}
							// $reply=array("ack"=>1,"developer_msg"=>"Route Added.","ack_msg"=>"Success! Route Insert Successfully.");
									// return $reply;
						}
						else
						{
							// $reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Route added Failed.");
							// return $reply;
						}
					}
				}
				if ($is_duplication_check != 0) {
					$dup_msg = " And ".$is_duplication_check." Customer's Data Already Inserted";
				}
				$reply=array("ack"=>1,"developer_msg"=>"Route Added.","ack_msg"=>"Total ".$isInsertSucces." Customer Route Insert Successfully.".$dup_msg);
				return $reply;
				
			} else {
				$reply=array("ack"=>0,"developer_msg"=>"Invalid Customer Array!!","ack_msg"=>"Something went wrong!!!");
				return $reply;
			}
		// }
	}
	 
	 public function UpdateDepartment($detail,$process)
	  {
	  	// echo "gg";exit();
			extract($detail);
			// $dup_where = "date = '".$date."' AND id!='".$_REQUEST['id']."' AND customer_id!='".$seid."' AND isDelete=0";
			// $r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
			// if($r){
			// 	$reply=array("ack"=>0,"developer_msg"=>"Already Exist Department","ack_msg"=>"Duplicate Department name.");
			// 	return $reply;
				
			// }else{

			// $state_name = $this->db->rp_getValue("executive","state"," isDelete=0 AND  id='".$seid."'",0);
			// $class_id = $this->db->rp_getValue("class","id"," isDelete=0 AND  name='".$state."'",0);
			// $city_name = $this->db->rp_getValue("executive","city"," isDelete=0 AND  id='".$seid."'",0);
			// $area_id = $this->db->rp_getValue("area","id"," isDelete=0 AND  name='".$city."'",0);

				$sales_id1 = $this->db->rp_getValue("master_route","sales_id"," isDelete=0 AND  id='".$m_route_id."'",0);
				
				$rows 	= array(
						"route_id"				=> $m_route_id,
						"sales_id"				=> $sales_id1,
						"customer_id"			=>	$seid,
						// "class_id"			=>	$class_id,
						// "area_id"			=>	$area_id,
						// "state"			=>	$state,
						// "city"			=>	$city,
						"date"				    =>	date('Y-m-d',strtotime($date)),
						"remark"				=> $remark,
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
				
                  
				$reply=array("ack"=>1,"developer_msg"=>"Route  Added.","ack_msg"=>"Success! Route Update Successfully.");
						return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Route Update Failed.");
					return $reply;
				}
			// }	
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
			
		$result['m_route_id']		= htmlentities($ctable_d['route_id']);
			
		$result['sales_id']		= htmlentities($ctable_d['sales_id']);
		$result['customer_id']	= stripslashes($ctable_d['customer_id']);


		// $result['state']	= stripslashes($ctable_d['class_id']);
		// $result['city']	= stripslashes($ctable_d['area_id']);

		$result['state']	= stripslashes($ctable_d['state']);
		$result['city']	= stripslashes($ctable_d['city']);
		// $result['customer_id']	= stripslashes($ctable_d['seid']);
		$result['date']	        = htmlentities(date('d-m-Y',strtotime($ctable_d['date'])));
		$result['remark']	= stripslashes($ctable_d['remark']);
		
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
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Route Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Delete Route Failed.");
				return $reply;
			}
	}
}

?>