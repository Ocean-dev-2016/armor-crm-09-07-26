<?php
else if($service=='sales_executive_change_password' || $service==5)
		{
			if(isset($_REQUEST['id']) && isset($_REQUEST['password']) && isset($_REQUEST['new_password']))
				{
					$id 		= isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";	
					if($db->rp_getTotalRecord("sales_executive","id='".$id."'"))
					{
						
						$newPassword		= md5(trim($_REQUEST['new_password']))?$db->clean(md5(trim($_REQUEST['new_password']))):"";
						$password 		= isset($_REQUEST['password'])?$db->clean($_REQUEST['password']):"";
						$check=$db->rp_getValue("sales_executive","COUNT(*)","id='".$id."' AND password='".$password."'",0);
					
						if($check>0)
						{	if($db->aj_updateUserPassword($id,$newPassword,$password))
							{
								$ack=array( "ack"=>1,
								"ack_msg"=>"Successfully Updated Your Password!!",
								"developer_msg"=>"You got it!!",
								"result"=>array($check),
								);
								$db->printJSON($ack);
							}
							else
							{
								$ack=array( "ack"=>0,
								"ack_msg"=>"Password Updation Fail!!",
								"developer_msg"=>"please pass correct password!!",
								);
								$db->printJSON($ack);
							}
						}
						else
						{
								$ack=array( "ack"=>0,
								"ack_msg"=>"Password Incorrect please Try Again Later!!",
								"developer_msg"=>"please pass correct password!!",
								);
								$db->printJSON($ack);
						}
					}
					else
					{
								$ack=array( "ack"=>0,
								"ack_msg"=>"User Not Found!!",
								"developer_msg"=>"User Not Found!!",
								);
								$db->printJSON($ack);
					}
				}
				else
				{
							$ack=array( "ack"=>0,
								"ack_msg"=>"Something went wrong!!! User id not found",
								"developer_msg"=>"User Not Found!!",
								);
								$db->printJSON($ack);
				}
			
		}
		public function aj_updateUserPassword($id,$newPassword,$password)
	{
		
			$rows=array("password"=>$newPassword);
			$where=" id='".$id."'";		
			return $this->rp_update("sales_executive",$rows,$where,0);
		
		
	}