<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");	
if(!empty($_POST["city_id"]))
{
	$state_r = $db->rp_getData("state","*","country_id = '".$city_id."'","",0);
	?>
	<option value="">Select State</option>
	<?php
	while($state_d = mysqli_fetch_assoc($state_r))
	{
		?>
		<option <?= (strtolower($state_d['name'])==strtolower($_REQUEST['state']))?"selected":""; ?> value="<?php echo $state_d['name']; ?>" ><?php echo $state_d['name']; ?></option>
		<?php
	}
}
else
{
	?>
	<?php
}
?>