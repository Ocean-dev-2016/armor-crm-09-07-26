<?php
$page_id=503;$page_slug='page_customer';
include("connect.php");
$ctable 	= "executive_map_area";
if(isset($_REQUEST['super_stockist_id']) && $_REQUEST['super_stockist_id']!="")
{
	$super_stockist_id=$_REQUEST['super_stockist_id'];
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		$service=$_REQUEST['mode'];
		if($service=="get_class")
		{
			if(
				isset($_REQUEST['super_stockist_id']) && $_REQUEST['super_stockist_id']!="" 
			)
			{
				$super_stockist_id=$_REQUEST['super_stockist_id'];
				$class_r=$db->rp_getData($ctable,"DISTINCT class_id  ","executive_id='".$_REQUEST['super_stockist_id']."'","",0);
				if($class_r)
				{
					while($class_d = mysqli_fetch_assoc($class_r))
					{
					$class = $db->rp_getValue("class","name","id = '".$class_d["class_id"]."'",0);
					?>
					<option value="<?php echo $class_d['class_id']; ?>" <?php echo ($class_d['class_id'])?"selected":""; ?>><?php echo $class; ?></option>
					<?php
					}
				}
				/*$response=array('ack'=>1,'ack_msg'=>'success !!!');
				echo json_encode($response);*/
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'executive Informations can not be empty !!!');
				echo json_encode($response);
			}
			
		}
		
		
		
	}
	
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again2!!');
		echo json_encode($response);
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again3!!');
	echo json_encode($response);
}
?>