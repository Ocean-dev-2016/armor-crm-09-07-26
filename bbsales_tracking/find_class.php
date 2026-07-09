<?php
$page_id=563;$page_slug='page_find_class';
include("connect.php");
$ss_id=isset($_POST["super_stockist_id"])?$_POST["super_stockist_id"]:0;
$dd_id=isset($_POST["dealer_distributor_id"])?$_POST["dealer_distributor_id"]:0;	
$class_id=isset($_POST["class_id"])?$_POST["class_id"]:0;
	
	if($ss_id!="")
		{
			$area_r = $db->rp_getData("executive","class_id","id=".$ss_id."","",0);
			//$executive_id=$_POST["super_stockist_id"];
		?>
		<option value=""> Select Class </option>
		<?php
			while($area_d = mysqli_fetch_assoc($area_r))
			{
			$class = $db->rp_getValue("class","name","id = '".$area_d["class_id"]."'",0);
			?>	
			<option  value="<?php echo $area_d['class_id']; ?>" <?php echo ($area_d['class_id'])?"selected":""; ?>><?php echo $class; ?></option>
			
		<?php
			}
		}
		else
		{
		?>
		
		
		 <?php
		}
		if($dd_id!="")
		{
			$areadeale_r = $db->rp_getData("executive","class_id","id =".$dd_id."","",0);
			
		?>
			<option value=""> Select Class </option>
		<?php
			while($areadeale_d = mysqli_fetch_assoc($areadeale_r))
			{
			$class = $db->rp_getValue("class","name","id = '".$areadeale_d["class_id"]."'",0);
			?>	
			<option  value="<?php echo $areadeale_d['class_id']; ?>" <?php echo ($areadeale_d['class_id'])?"selected":""; ?>><?php echo $class; ?></option>
			
		<?php
			}
		}
		else
		{
		?>
		
		
		 <?php
		}
	
	