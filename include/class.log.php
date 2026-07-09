<?php
//require_once("main.class.php");
require_once("function.class.php");
class Log extends Functions
{
	public $db;
	public $ctable="activity_log";
	public $activity_type=array("insert","update","delete","view");
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
    } 
	public function insertLog($table_name,$ref_id,$activity_type,$activity_description) 
	{ 
		$ip=$this->db->rp_get_client_ip();
		if(isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!="" && 		isset($_SESSION[SITE_SESS.'_ADMIN_TYPE']) && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!="")
		{
				$user_id=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
				$user_type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];				
		}
		else
		{
			if(is_writable(LOG_FILE))
			{
			
				fopen(LOG_FILE,"a");
				fwrite(LOG_FILE,"From IP ".$ip." Entry is modified or inserted but Session not created DATETIME ".date("Y-m-d H:i:s").PHP_EOL);
				fclose(LOG_FILE);
			}			
			$user_id=5895;
			$user_type=0712;	
		}
		$rows=array("table_name","ref_id","activity_type","activity_description","activity_date","user_id","user_type","ip");
		$values=array($table_name,$ref_id,$activity_type,$activity_description,date("Y-m-d H:i:s"),$user_id,$user_type,$ip);
		$this->rp_insert($this->ctable,$values,$rows,0);	
	}
	
	public function viewLog($user_id="")
	{
		$items=array();
		if($user_id!="")
		{
			$where="user_id='".$user_id."'";
		}
		else
		{
			$where="1=1";
		}
		
		$ctable_r=$this->db->rp_getData($ctable,"*",$where);
		if($ctable_r)
		{
			while($ctable_d=mysqli_fetch_assoc($ctable_r))
			{
				$items[]=$ctable_d;
			}				
		}
		return $items;
	}
}

?>