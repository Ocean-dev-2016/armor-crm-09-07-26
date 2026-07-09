<?php

class Notification extends Functions
{
	/*
		*** Notification Function Developed By Jai Acharya :/ <<<
	*/
	
	public $db;
	public $ctable="notification";
	
	
	function __construct() {
		
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;	
	}
	public function NotificationDelete($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete notification Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete record Failed.");
				return $reply;
			}
	}
}
?>