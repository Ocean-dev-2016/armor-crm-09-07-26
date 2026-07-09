<?php
require_once('notification.class.php');
class User extends Functions
{
	
	public $detail=array();
	public $db;
	public $note_status= array("Pending","Cancelled","Done");
	
	function __construct($id="") {
	   $db = new Functions();
	   $conn = $db->connect();
	   $this->db=$db;
       
	   if($id!="")
	   {
		   $this->detail=$this->getSalesExecutiveFromId($id);
		   
	   }
   }
   
   function getSalesExecutiveFromId($id)
   {
		$where 	 = "id='".$id."'AND isDelete=0";
		$data    = $this->db->rp_getData('sales_executive',"*",$where);
		return $data;
   }
   
   function getSalesExecutiveFromEmail($email)
   {	
		$where = "email='".$email."'AND isDelete=0";
		$data    = mysqli_fetch_assoc($this->db->rp_getData('sales_executive',"*",$where));
		return $data;
   }
   
   function changeProfile($id,$name,$email,$phone,$address)
   {	
		if($this->db->rp_getTotalRecord("sales_executive","id = '".$id."'AND isDelete=0")>0)
		{
			$values	 = array(	
		
						"name" 		=>	$name,
						"email" 	=>	$email,								
						"phone"  	=>	$phone,
						"address"	=>	$address,
						);
				
					$where = "id='".$id."'";
					$uid = $this->db->rp_update("sales_executive",$values,$where);
					
					$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully Updated Your Profile !!",
					"developer_msg"=>"You got it!!",
					"result"=>array($uid),
					);
					
					return $ack;
		}
		else
		{
			return array('ack'=>0,
						 "ack_msg"=>"ID Is Not Match!!",
						 "developer_msg"=>"please pass the correct id!!");
		}
   }
   function changePhone($id,$phone)
   {	if($this->db->rp_getTotalRecord("sales_executive","id ='".$id."' AND isDelete=0")>0)
		{
			$values	 = array(	
		
						"phone"  	=>	$phone,
						);
				
					$where = "id='".$id."'";
					$uid = $this->db->rp_update("sales_executive",$values,$where);
					/*$activation_code=generateActivationCodes();			
					$sendSMS=aj_sendOTPs($phone,$activation_code);*/
					
					$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully Updated Your mobile Number !!",
					"developer_msg"=>"You got it!!",
					"result"=>array($uid),
					);
					
					return $ack;
		}
		else
		{
			return array('ack'=>0,
						 "ack_msg"=>"ID Is Not Match!!",
						 "developer_msg"=>"please pass the correct id!!");
		}
   }
   function getSalesExecutiveGetNotes($id,$type)
   {
	   // $type=1(Today Task) ,2(Urgent Task),3(All Task)
		$result = array();
		$date = date('Y-m-d');
		if($date!="")
		{
			if($type==2)
			{
				$where = "note_date <'".$date."' AND status!=2";
			}
			else if($type==1)
			{
				$where = "note_date='".date('Y-m-d',strtotime($date))."'";
			}
			else
			{
				$where = "1=1 AND isDelete=0";
			}
			
		}
		else
		{
			$where = "1=1 AND isDelete=0";
		}
		$data    = $this->db->rp_getData('notes',"*",$where,"id DESC",0);
		if($data)
		{
		while($r= mysqli_fetch_assoc($data))
		{
			$sales_executive= explode(",",$r['sales_executive']);
			
			
			if(in_array($id,$sales_executive))
			{
				$r['created_by_slug'] = $r['created_by'];
				if($r['created_by']!=0)
				$r['created_by'] = $this->db->rp_getValue(CTABLE_ADMIN,"name","id='".$r['created_by']."'",0);
				else
				$r['created_by'] ="";	
				$r['note_status_slug']= $r['status'];
				$r['note_status']= $this->note_status[intval($r['status'])];
				$result[] = $r;	
			}
			
		}
		}
		if(!empty($result))
		{
			$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully Get Sales Officer Notes !!",
					"developer_msg"=>"You got it!!",
					"result"=>$result,
					);
					return $ack;
		}
		else
		{
			$ack=array( "ack"=>0,
					"ack_msg"=>"No Notes Found !!",
					"developer_msg"=>"No notes found!!",
					"result"=>$result,
					);
					return $ack;
		}
			
		
		
   }
   function getSalesExecutiveGetNoticeBoard($id)
   {	
		$result = array();
		$data    = $this->db->rp_getData('notice_board',"*","1=1 AND isDelete=0");
		if($data)
		{
			while($r= mysqli_fetch_assoc($data))
			{
			
				$sales_executive= explode(",",$r['sales_executive']);
			
				if(in_array($id,$sales_executive))
				{
					$r['date'] = date('d-m-Y H:i:s',strtotime($r['date']));
					$result[] = $r;	
				}
			
			}
			
			
		}
		if(!empty($result))
		{
			$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully Get Sales Officer Notice Board !!",
					"developer_msg"=>"You got it!!",
					"result"=>$result,
					);
					return $ack;
		}
		else
		{
			$ack=array( "ack"=>0,
					"ack_msg"=>"No Notes Found !!",
					"developer_msg"=>"No notes found!!",
					"result"=>$result,
					);
					return $ack;
		}
	   
   }
   function getSalesExecutiveGetMessage($sid,$rid)
   {
		$result = array();
		$where 	 = "(rid='".$rid."' AND sid='".$sid."') OR (rid='".$sid."' AND sid='".$rid."') AND isDelete=0";
		$data    = $this->db->rp_getData('message',"distinct id,message.*",$where);
		if(!empty($data))
		{
			while($row = mysqli_fetch_assoc($data))
			{
				$result[] = $row;				
			
			}
			$ack=array( "ack"=>1,
					"ack_msg"=>"Successfully Get Sales Officer Message !!",
					"developer_msg"=>"You got it!!",
					"result"=>$result,
					);
					return $ack;
			
		}
		else
		{
			$ack=array("ack"=>0,
					"ack_msg"=>"Does not Found any Message !!",
					"developer_msg"=>"not found!!",
					"result"=>$result,
					);
					return $ack;
		}
			
		
		
	}
	function addNotes($id,$title,$note_date,$detail,$priority)
    {	
			$date = date('Y-m-d H-i-s');
			$rows	 = array(	
		
						"title",
						"date",						
						"note_date" ,											
						"detail",
						"sales_executive",
						"priority",
						);
			$values	 = array(
						
						$title,
						$date,
						date('Y-m-d H-i-s',strtotime($note_date)),
						$detail,
						$id,
						$priority,
						);
				
					$add = $this->db->rp_insert("notes",$values,$rows);
					if($add)
					{
							$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully Added Your Notes !!",
						"developer_msg"=>"You got it!!",
						"result"=>array($add),
						);
						return $ack;
					}
					else
					{
								$ack=array("ack"=>0,
							"ack_msg"=>"not inserted !!",
							"developer_msg"=>"not inserted!!",
							"result"=>array(),
							);
							return $ack;
					}
					
		
	}
	function sendNotification($sales_executive,$notification_title,$notification_description,$notification_type,$notification_extra)
    {
		$adate = date('Y-m-d');
		$refreshTokens=array();
			foreach($sales_executive as $s)
			{
					$rows 	= array(
						"notification_title",
						"notification_description",
						"notification_type",
						"notification_extra",					
						"sales_id",
						"adate",									
					);
					$values = array(
						$notification_title,
						$notification_description,
						$notification_type,
						$notification_extra,
						$s,	
						$adate
						
					);
					$this->db->rp_insert("notification",$values,$rows);
					
					$id=$this->db->rp_getValue("sales_executive","refreshToken","refreshToken!='' AND id='".$s."' AND isDelete=0");
					$refreshTokens[]=$id;
								
						
			}			
			$msg = array(
					"type"		 => $notification_type,
					"title"		 => $notification_title,
					"description"=> $notification_description,					
					"extra"		 =>	$notification_extra,
					);
			
			$result=$this->db->send_notification($msg,$refreshTokens);				
				return $result;
		
	}
	
   
	
	
}
function generateActivationCodes()
{
	$characters='0123456789';
	$randStr="";
	for($i=0;$i<=5;$i++)
	{
		$randStr=$randStr.$characters[rand(0,strlen($characters)-1)];
	}
	return $randStr;
}
function aj_sendOTPs($number,$activationCode)
{
	$msgId = "";
	$nt = new Notification();							
	if($number!="")
	{
		$sms=$activationCode." is Your One Time Password!!";
		$msgId=$nt->rp_checkSMS($number,$sms);	
	}
	return array('ack'=>1,'status'=>"msgId".$msgId."&OTP=".$activationCode);
}
?>