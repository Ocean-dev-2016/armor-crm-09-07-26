<?php
$page_id=555;$page_slug='page_executive';
include("connect.php");
$ctable 	= "executive_branch";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
{
	$cid=$_REQUEST['cid'];
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		$service=$_REQUEST['mode'];
		if($service=="add_branch")
		{
			if(isset($_REQUEST['branch_name']) && $_REQUEST['branch_name']!="")
			{
				$branch_name=$_REQUEST['branch_name'];
				$dup_where = "branch_name = '".$branch_name."' AND isDelete=0 AND cid='".$cid."'";
				$r = $db->rp_dupCheck($ctable,$dup_where);
				if($r)
				{
						$response=array('ack'=>0,'ack_msg'=>'Branch name already exists !!!');
						echo json_encode($response);
				}
				else
				{
					$adate	= date('Y-m-d H:i:s');
					$rows=array("cid","branch_name","adate","isDelete");
					$values=array($cid,$branch_name,$adate,0);
					$cbid=$db->rp_insert($ctable,$values,$rows);
					if($cbid!=0)
					{
						$response=array('ack'=>1,'ack_msg'=>'Branch added Successfully !!!');
						echo json_encode($response);
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Branch name can not be empty !!!');
						echo json_encode($response);
					}
				}
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Branch name can not be empty !!!');
				echo json_encode($response);
			}
			
		}
		else if($service=="edit_branch")
		{
			if(isset($_REQUEST['cbid']) && $_REQUEST['cbid']!="" && isset($_REQUEST['branch_name']) && $_REQUEST['branch_name']!="")
			{
				$branch_name=$_REQUEST['branch_name'];
				$cid=$_REQUEST['cid'];
				$dup_where = "branch_name = '".$branch_name."' AND isDelete=0 AND cid='".$cid."' AND  id!='".$_REQUEST['cbid']."'";
				$r = $db->rp_dupCheck($ctable,$dup_where);
				if($r)
				{
						$response=array('ack'=>0,'ack_msg'=>'Branch name already exists !!!');
						echo json_encode($response);
				}
				else
				{
					$rows 	= array(
						"branch_name"=>$branch_name,				
					);
					$where	= "id='".$_REQUEST['cbid']."'";
					if($db->rp_update($ctable,$rows,$where,0))
					{
						$response=array('ack'=>1,'ack_msg'=>'Branch inforamtion edited Successfully !!!');
						echo json_encode($response);
					}	
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Branch couldn\'t be edited try later !!!');
						echo json_encode($response);
					}	
				}					
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Branch name can\'t be empty or branch not found !!!');
				echo json_encode($response);			
			}
		}
		else if($service=='delete_branch')
		{
			if(isset($_REQUEST['cbid']) && $_REQUEST['cbid']!="")
			{
				$rows 	= array(
					"isDelete"	=> "1"
				);
				$where	= "id='".$_REQUEST['cbid']."'";
				if($db->rp_update($ctable,$rows,$where))
				{
					$response=array('ack'=>1,'ack_msg'=>'Branch removed Successfully !!!');
					echo json_encode($response);
				}	
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Branch can\'t be deleted !!!');
					echo json_encode($response);
				}					
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
				echo json_encode($response);				
			}
		}		
		else
		{
			$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
			echo json_encode($response);
		}
	}
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
		echo json_encode($response);
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}
require_once 'disconnect.php'; 
?>