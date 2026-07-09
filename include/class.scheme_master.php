<?php
require_once("main.class.php");
require_once("function.class.php");
class Topscheme extends Functions
{
	public $db;
	public $ctable="scheme_master";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function Insertscheme($detail,$file) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist scheme Name","ack_msg"=>"Duplication! Already Exist scheme Name.");
			return $reply;
		}
		else
		{
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"name",
						"start_date",
						"end_date",
						"isDelete"
					);
			$values = array(
						$name,	
						date('Y-m-d',strtotime($start_date)),
						date('Y-m-d',strtotime($end_date)),
						$isDelete
					);
			/*log entry*/
				$module_name = "scheme";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/	
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
		
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>" scheme Added.","ack_msg"=>"Success! Top scheme Insert Successfully.","inserted_id"=>$uid);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! scheme Insert Failed.");
				return $reply;
			}
		}
	}
	 
	 public function Updatescheme($detail,$file)
	  {
			extract($detail);
			$dup_where = "name = '".$name."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Already Exist Topscheme Name","ack_msg"=>"Duplication! Already Top Exist scheme Name.");
				return $reply;
				
			}else{



				$rows 	= array(
						"name"				=> $name,
						"start_date"		=> date('Y-m-d',strtotime($start_date)),
						"end_date"			=> date('Y-m-d',strtotime($end_date)),
						);
				$where	= "id='".$_REQUEST['id']."'";
				/*log entry*/
					$module_name = "scheme";
					$flag = "Web";
					$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
				/*log entry*/
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>" scheme Update Successfull!!.","ack_msg"=>"Success!  scheme Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! scheme Update Failed.");
					return $reply;
				}
			}	
		}	
	public function GetEditDatascheme($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']		= htmlentities($ctable_d['name']);
		$result['start_date']		= date('d-m-Y',strtotime($ctable_d['start_date']));
		$result['end_date']		= date('d-m-Y',strtotime($ctable_d['end_date']));
		$result['name']		= htmlentities($ctable_d['name']);
		$result['image_path'] = $ctable_d['image_path'];
		
		$reply=array("ack"=>1,"developer_msg"=>"Top scheme detail fetched!!.","ack_msg"=>"Success! Top scheme Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function Deletescheme($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
		$where	= "id='".$_REQUEST['id']."'";
		/*log entry*/
			$name = $this->db->rp_getValue("scheme_master","name","id='".$_REQUEST['id']."'");
			$module_name = "scheme";
			$flag = "Web";
			$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/
		$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
		if($uid!=0)
		{
			$is_delete=$this->db->rp_Delete("scheme_master_item","isDelete=0 AND scheme_id='".$_REQUEST['id']."'");

			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Top scheme Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Top scheme Failed.");
			return $reply;
		}
	}
}

?>