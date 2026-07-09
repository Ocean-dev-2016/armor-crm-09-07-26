<?php

$page_id=400;$page_slug='dashboard';
include("connect.php");
// echo $_POST['dealer_distributor_id'];exit();
	
?>

	<?php


		if(!empty($_POST["super_stockist_id"]))
		{
			$dd_r = $db->rp_getData("executive","*","super_stockist_id = '".$_POST["super_stockist_id"]."' AND type_of_executive='2' AND isDelete=0 AND isActive=1",0);

		?>
		<option value="">Select Distributor</option>	
		<?php
		// echo $_POST['dealer_distributor_id'];exit;
			while($dd_d = mysqli_fetch_assoc($dd_r))
			{
		?>
			<option value="<?php echo $dd_d['id']; ?>" <?php echo ($dd_d['id']==$_POST['dealer_distributor_id'])?"selected":""; ?>><?php echo $dd_d['company_name']."-".$dd_d['cname']; ?></option>

		<?php
			}
		}
		else
		{
		?>
		<option value="">Select Super Stockist First</option>
		 <?php
		}
      ?>
      