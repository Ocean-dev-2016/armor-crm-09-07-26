<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
include("../include/class.sales_executive.php");
if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
{
	$service=$_REQUEST['mode'];
	//echo $_REQUEST['mode'];exit;
	if($service=="sales_executive_tracking")
	{
		$sales_id=isset($_REQUEST['sid'])?$_REQUEST['sid']:"";
		$date=isset($_REQUEST['date'])?$_REQUEST['date']:"";
		$sales_executive=new SalesExecutive();
		$response=$sales_executive->trackSales($sales_id,$date);
		echo json_encode($response);
	}
	else if($service=="executive_class")
	{
		$type=isset($_REQUEST['type'])?$_REQUEST['type']:"";
		$eid=isset($_REQUEST['eid'])?$_REQUEST['eid']:"";
		if($type!="" && $eid!="")
		{
			if($type!="sales_manager")
			$classes = $db->rp_getData("sales_executive","class_id","id=".$eid."","",0);
			else
			$classes = $db->rp_getData("class","*","1=1","",0);	
			if($classes)
			{
				?>
				<option value=""> Select Class </option>
				<?php 
				while($class = mysqli_fetch_assoc($classes))
				{
					if($type!="sales_manager")
					$class_name = $db->rp_getValue("class","name","id = '".$class['class_id']."'",0);
					else
					{
						$class_name=$class['name'];
						$class['class_id']=$class['id'];
					}
						
					?>	
						<option  value="<?php echo $class['class_id']; ?>"><?php echo $class_name; ?></option>
					<?php 
				}
			}
			else
			{
				?>
				<option value=""> No Class Assigned To Your Superior </option>
				<?php 
			}
		}
		else
		{
		?>
			<option value=""> No Class Assigned To Your Superior </option>
		 <?php
		}
	}
	else if($service=="executive_area")
	{
		$type=isset($_REQUEST['type'])?$_REQUEST['type']:"";
		$eid=isset($_REQUEST['eid'])?$_REQUEST['eid']:"";
		$cid=isset($_REQUEST['cid'])?$_REQUEST['cid']:"";
		if($type!="" && $eid!="" && $cid!="")
		{
			if($type==0)
			$area_r =$db->rp_getData("sales_executive_map_area","area_id","class_id = '".$cid."' AND sales_executive_id = '".$eid."'","",0);
			else
			$area_r =$db->rp_getData("area","*","class_id = '".$cid."'","",0);	
			if($area_r)
			{
				while($area_d = mysqli_fetch_assoc($area_r))
				{
					if($type==0)
					$area=$db->rp_getValue("area","name","id='".$area_d['area_id']."'");
					else
					{
						$area=$area_d['name'];
						$area_d['area_id']=$area_d['id'];
					}	
					?>
					<option value="<?php echo $area_d['area_id']; ?>" ><?php echo $area; ?></option>
					<?php
				}
			}
			
		}
		else
		{
		?>
			<option value=""> No Class Assigned To Your Superior </option>
		 <?php
		}
	}
	else if($service=="under_executive")
	{
		$eid=isset($_REQUEST['eid'])?$_REQUEST['eid']:"";
		$suprior_type=isset($_REQUEST['suprior_type'])?$_REQUEST['suprior_type']:"";
		$executive_type=isset($_REQUEST['executive_type'])?$_REQUEST['executive_type']:"";
		if($eid!="")
		{
			$where.="type='".$executive_type."' AND ";
			
			if($suprior_type=="sales_manager")
			{
				$where.="sm_id='".$eid."'";
			}
			else if($suprior_type=="area_sales_manager")
			{
				$where.="asm_id='".$eid."'";
			}
			else if($suprior_type=="sales_officer")
			{
				$where.="so_id='".$eid."'";
			}
			else if($suprior_type=="sales_executive")
			{
				$where.="se_id='".$eid."'";
			}
			else if($suprior_type=="area_manager")
			{
				$where.="am_id='".$eid."'";
			}
			$sales_executives =$db->rp_getData("sales_executive","*",$where,"",0);
			if($sales_executives)
			{
				?>
				<option value=""> Select Executive --</option>
				<?php 
				while($sales_executive = mysqli_fetch_assoc($sales_executives))
				{
				
					?>
					<option value="<?php echo $sales_executive['id']; ?>" ><?php echo $sales_executive['username']." (".$sales_executive['name'].")";?></option>
					<?php
				}
			}
			else
			{
				?>
				<option value=""> No Executive Available  </option>
			 <?php
			}
			
		}
		else
		{
		?>
			<option value=""> No Executive Available  </option>
		 <?php
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
require_once("disconnect.php");
?>