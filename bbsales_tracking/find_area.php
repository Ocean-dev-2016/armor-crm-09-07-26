<?php
$page_id=564;$page_slug='page_find_area';
include("connect.php");
 $ss_id=isset($_POST["super_stockist_id"])?$_POST["super_stockist_id"]:0;
 $dd_id=isset($_POST["dealer_distributor_id"])?$_POST["dealer_distributor_id"]:0;
 $class_id=isset($_POST["class_id"])?$_POST["class_id"]:0;
 $json=isset($_POST["json"])?$_POST["json"]:false;

if($ss_id!="")
{
	$executive_d=$db->rp_getData("executive_map_area","*","executive_id='".$ss_id."' AND isDelete=0");
	$user_info=array();
	while($executive_r=mysqli_fetch_assoc($executive_d)){
		$area_name=$db->rp_getValue("area","name","id='".$executive_r['area_id']."'");
		$user_info=array("area_id"=>$executive_r['area_id']);
	}
	$result=array();
	$area_r = $db->rp_getData("executive_map_area","DISTINCT area_id","class_id = '".$_POST["class_id"]."' AND executive_id = '".$ss_id."' AND isDelete=0","",0);
	if($area_r)
	{
		while($area_d = mysqli_fetch_assoc($area_r))
			{
			$area=$db->rp_getValue("area","name","id='".$area_d['area_id']."' AND isDelete=0");
				if(!$json)
				{?>				
				<option value="<?php echo $area_d['area_id']; ?>" ><?php echo $area; ?></option>
				<?php 
				}
				else
				{
					$result[]=array("value"=>$area_d['area_id'],"text"=>$area);
				}
			}
			
			if($json)
			{
				$result=array_unique($result);
				echo json_encode($result);
			}
	}
}
else if($dd_id!="")
{
	$dealer_d=$db->rp_getData("executive_map_area","*","executive_id='".$dd_id."'");
	$user_info=array();
	while($dealer_r=mysqli_fetch_assoc($dealer_d)){
		$area_name=$db->rp_getValue("area","name","id='".$dealer_r['area_id']."'");
		$user_info=array("area_id"=>$dealer_r['area_id']);
	}
	$area_r =$db->rp_getData("executive_map_area","DISTINCT area_id","class_id = '".$_POST["class_id"]."' AND executive_id = '".$dd_id."'","",0);
	if($area_r)
	{
		while($area_d = mysqli_fetch_assoc($area_r))
			{
			$area=$db->rp_getValue("area","name","id='".$area_d['area_id']."'")
		?>
			<option value="<?php echo $area_d['area_id']; ?>" ><?php echo $area; ?></option>
		<?php
		}
	}
}
else if($class_id!="")
{
	$area_r = $db->rp_getData("area","*","class_id = '".$class_id."'","",0);
	if($area_r)
	while($area_d = mysqli_fetch_assoc($area_r))
	{
		if(!$json)
		{?>
		<option value="<?php echo $area_d['id']; ?>" <?php echo (in_array($area_d['id'],$user_info))?"selected":""; ?>><?php echo $area_d['name']; ?></option>
		
		<?php 
		}
		else
		{
			$result[]=array("value"=>$area_d['id'],"text"=>$area_d['name']);
		}
	}
	
	if($json)
	{
		$result=array_unique($result);
		echo json_encode($result);
	}
}
else
{
	?>
	<option value="">---Select City---</option>
	<?php 
}

?>