<?php
$page_id=408;$page_slug='page_vendor';
include("connect.php");
require_once("../include/vendor.class.php");
$objVendor= new Vendor;
$ctable 	= "vendor_contact_person";
if(isset($_REQUEST['vid']) && $_REQUEST['vid']!="")
{
	$vid=$_REQUEST['vid'];
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		$service=$_REQUEST['mode'];
		if($service=="add_contact")
		{
			if(
				isset($_REQUEST['vid']) && $_REQUEST['vid']!="" &&
				isset($_REQUEST['contact_name']) && $_REQUEST['contact_name']!="" &&
				isset($_REQUEST['contact_designation']) && $_REQUEST['contact_designation']!=""&&
				isset($_REQUEST['contact_email']) && $_REQUEST['contact_email']!=""&&
				isset($_REQUEST['contact_phone']) && $_REQUEST['contact_phone']!=""&&
				isset($_REQUEST['contact_branch']) && $_REQUEST['contact_branch']!=""			
			)
			{
				$vid=$_REQUEST['vid'];
				$contact_name=$_REQUEST['contact_name'];
				$contact_designation=$_REQUEST['contact_designation'];
				$contact_email=$_REQUEST['contact_email'];
				$contact_phone=$_REQUEST['contact_phone'];
				$contact_branch=$_REQUEST['contact_branch'];
				$adate	= date('Y-m-d H:i:s');
				
				$dup_where = "name = '".$_REQUEST['contact_name']."' AND vid='".$vid."' AND isDelete=0";
				$r = $db->rp_dupCheck($ctable,$dup_where);
				if(!$r)
				{
					$rows=array("vid","name","branch","designation","email","phone","adate","isDelete");
					$values=array($vid,$contact_name,$contact_branch,$contact_designation,$contact_email,$contact_phone,$adate,0);
					$cbid=$db->rp_insert($ctable,$values,$rows);
					if($cbid!=0)
					{
						$response=array('ack'=>1,'ack_msg'=>'Contact added Successfully !!!');
						echo json_encode($response);
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Contact could not added !!!');
						echo json_encode($response);
					}
				}
				else{
					$response=array('ack'=>0,'ack_msg'=>'Contact name already there try another name !!!');
					echo json_encode($response);
					
				}
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Contact Informations can not be empty !!!');
				echo json_encode($response);
			}
			
		}
		else if($service=="edit_contact")
		{
			if(isset($_REQUEST['vid']) && $_REQUEST['vid']!="" &&			
				isset($_REQUEST['contact_name']) && $_REQUEST['contact_name']!="" &&
				isset($_REQUEST['contact_designation']) && $_REQUEST['contact_designation']!="" &&
				isset($_REQUEST['contact_email']) && $_REQUEST['contact_email']!="" &&
				isset($_REQUEST['contact_phone']) && $_REQUEST['contact_phone']!="" &&
				isset($_REQUEST['contact_branch']) && $_REQUEST['contact_branch']!=""				)
			{
				$contact_name=$_REQUEST['contact_name'];
				$contact_designation=$_REQUEST['contact_designation'];
				$contact_email=$_REQUEST['contact_email'];
				$contact_phone=$_REQUEST['contact_phone'];
				$contact_branch=$_REQUEST['contact_branch'];
				
				$rows 	= array(
					"name"=>$contact_name,				
					"designation"=>$contact_designation,				
					"email"=>$contact_email,				
					"phone"=>$contact_phone,				
					"branch"=>$contact_branch,				
				);
				$r=$db->rp_dupCheck($ctable,"name='".$_REQUEST['contact_name']."' AND id!='".$_REQUEST['cpid']."' AND vid='".$_REQUEST['vid']."' AND isDelete=0",0);
				if(!$r)
				{
					$where	= "id='".$_REQUEST['cpid']."'";
					if($db->rp_update($ctable,$rows,$where,0))
					{
						$response=array('ack'=>1,'ack_msg'=>'Contact inforamtion edited Successfully !!!');
						echo json_encode($response);
					}	
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Contact couldn\'t be edited try later !!!');
						echo json_encode($response);
					}
				}
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Contact name already there try another name !!!');
					echo json_encode($response);
				}
									
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Contact Information can\'t be empty or contact not found !!!');
				echo json_encode($response);			
			}
		}
		else if($service=='delete_contact')
		{
			if(isset($_REQUEST['vid']) && $_REQUEST['vid']!="" && isset($_REQUEST['cpid']) && $_REQUEST['cpid']!="")
			{
				$rows 	= array(
					"isDelete"	=> "1"
				);
				$where	= "id='".$_REQUEST['cpid']."' AND vid='".$_REQUEST['vid']."'";
				if($db->rp_update($ctable,$rows,$where,0))
				{
					$response=array('ack'=>1,'ack_msg'=>'Contact removed Successfully !!!');
					echo json_encode($response);
				}	
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Contact can\'t be deleted !!!');
					echo json_encode($response);
				}					
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
				echo json_encode($response);				
			}
		}		
		else if($service=='get_contact')
		{
			if(isset($_REQUEST['cpid']) && $_REQUEST['cpid']!="")
			{
				$cpid=$_REQUEST['cpid'];
				$detail=mysqli_fetch_assoc($db->rp_getData($ctable,"*","id='".$cpid."'"));
				if(!empty($detail))
				{
					$branches=$objVendor->getVendorBranches($vid);
					foreach($branches as $b)
					{
						if($b['id']==$detail['branch'])
						{
							$b['selected']="selected";
						}
						else
						{
							$b['selected']="";
						}
						$result_branches[]=$b;
					}
					$detail['branches']=$result_branches;
					$response=array('ack'=>1,'ack_msg'=>'Contact successfully fetched!!',"result"=>$detail);
					echo json_encode($response);
					
				}
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Contact not found!!');
					echo json_encode($response);			
				}
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Contact not found try later!!');
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
?>
<?php require_once 'disconnect.php';  ?>