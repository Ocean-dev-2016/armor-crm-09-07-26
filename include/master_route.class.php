<?php
require_once("main.class.php");
require_once("function.class.php");
class MasterRoute extends Functions
{
	public $db;
	public $ctable="master_route";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertMasterRoute($detail,$process) 
	{

		extract($detail);

		$dup_where = "sales_id='".$sales_id."' AND ((start_date = '".$start_date."' AND end_date = '".$end_date."') OR end_date >= '".date('Y-m-d',strtotime($start_date))."') AND state='".$state."' AND city='".$city."' AND isDelete=0";

		// "sales_id='".$sales_id."' AND ((start_date = '".$start_date."' AND end_date = '".$end_date."') OR end_date >= '".date('Y-m-d',strtotime($start_date))."') AND state='".$state."' AND city='".addslashes($city)."' AND isDelete=0",0

		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"already exist Route","ack_msg"=>"already exist Route!!");
			return $reply;
		}
		else
		{	
			
			$class_id = $this->db->rp_getValue("class","id"," isDelete=0 AND  name='".$state."'",0);
			$area_id = $this->db->rp_getValue("area","id"," isDelete=0 AND  name='".$city."'",0);
			$main_city_name = $this->db->rp_getValue("city","name"," isDelete=0 AND  id='".$main_city."'",0);

			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"sales_id",
						"start_date",
						"end_date",

						"class_id",
						"area_id",
						"state",
						"city",
						"main_city_id",
						"main_city",
						"isDelete",
					);
			$values = array(
						$sales_id,
						date('Y-m-d',strtotime($start_date)),	
						date('Y-m-d',strtotime($end_date)),	

						$class_id,	
						$area_id,
						$state,
						$city,
						$main_city,
						$main_city_name,
						$isDelete
					);
			/*log entry*/
				$module_name = "Master Route";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Master Route Added.","ack_msg"=>"Success! Master Route Insert Successfully.");
						return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Master Route added Failed.");
				return $reply;
			}
		}
	}
	 
	 public function UpdateMasterRoute($detail,$process)
	  {
			extract($detail);
			// $dup_where = "id!='".$_REQUEST['id']."' AND sales_id!='".$sales_id."' AND isDelete=0";


			$dup_where = "id!='".$_REQUEST['id']."' AND sales_id='".$sales_id."' AND ((start_date = '".$start_date."' AND end_date = '".$end_date."') OR end_date >= '".date('Y-m-d',strtotime($start_date))."') AND state='".$state."' AND city='".$city."' AND isDelete=0";


			$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Route","ack_msg"=>"Already Exist Route");
				return $reply;
				
			}else{

			$class_id = $this->db->rp_getValue("class","id"," isDelete=0 AND  name='".$state."'",0);
			$area_id = $this->db->rp_getValue("area","id"," isDelete=0 AND  name='".$city."'",0);
			$main_city_name = $this->db->rp_getValue("area","name"," isDelete=0 AND  id='".$main_city."'",0);
				$rows 	= array(
					"sales_id"			=> $sales_id,
					"start_date"		=>	date('Y-m-d',strtotime($start_date)),
					"end_date"			=>	date('Y-m-d',strtotime($end_date)),

					"class_id"			=>	$class_id,
					"area_id"			=>	$area_id,
					"state"			=>	$state,
					"city"			=>	$city,
					"main_city"			=>	$main_city_name,
					"main_city_id"			=>	$main_city,				
				);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "Master Route";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
                  
				$reply=array("ack"=>1,"developer_msg"=>"Master Route  Added.","ack_msg"=>"Success! Master Route Update Successfully.");
						return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Master Route Update Failed.");
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
	public function MasterRouteGetEditData($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['sales_id']		= htmlentities($ctable_d['sales_id']);
		$result['start_date']	        = htmlentities(date('d-m-Y',strtotime($ctable_d['start_date'])));
		$result['end_date']	        = htmlentities(date('d-m-Y',strtotime($ctable_d['end_date'])));
		$result['state']	= stripslashes($ctable_d['state']);
		$result['city']	= stripslashes($ctable_d['city']);
		$result['main_city']	= stripslashes($ctable_d['main_city']);
		$result['main_city_id']	= stripslashes($ctable_d['main_city_id']);
			
		$reply=array("ack"=>1,"developer_msg"=>"Master Route detail fetched!!.","ack_msg"=>"Master Route Successfull.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteMasterRoute($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			/*log entry*/
				// $name = $this->db->rp_getValue("department","name","id='".$_REQUEST['id']."'");
				$module_name = "Master Route";
				$flag = "Web";
				$log_description = $module_name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Master Route Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Delete Master Route Failed.");
				return $reply;
			}
	}
}

?>