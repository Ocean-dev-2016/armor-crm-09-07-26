<?php
$page_id=400;$page_slug='dashboard';
$page_slug = "find Bill";
include("connect.php");	
$_POST['area_id']= explode(",",$_POST['area_id']);
?>
<select class="form-control area_id" name="area_id[]" id="area_id" multiple="multiple">
<?php
	if(!empty($_POST["class_id"]))
	{
		$area_r = $db->rp_getData("area","*","class_id = '".$_POST["class_id"]."' AND isDelete=0","",0);
		?>
		<option value="">Select Area</option>
		<?php
		while($area_d = mysqli_fetch_array($area_r))
		{
			//print_r($_POST['class_id']); exit;
		?>
			<option <?php echo (in_array($area_d['id'],$_POST['area_id']))?"selected":""; ?> value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>
		<?php
		}
	}
	else
	{
	?>
	<?php
	}
?>
</select>
<?php require_once 'disconnect.php';  ?>
      
	