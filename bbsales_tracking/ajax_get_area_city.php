<?php
$page_id=555;$page_slug='page_executive';
$page_slug = "find Bill";
include("connect.php");	
?>
<select class="form-control city_id" name="city_id[]" id="city_id" multiple="multiple">

<?php
	if(!empty($_POST["class_id"]))
	{
		$area_r = $db->rp_getData("city","*","state_id IN (".$_POST['class_id'].") AND isDelete=0","",0);
		?>
		<option value="">Select City</option>
		<?php
		while($area_d = mysqli_fetch_array($area_r))
		{
			//print_r($_POST['class_id']); exit;
		?>
			<option <?php echo (in_array($area_d['id'],$_POST['class_id']))?"selected":""; ?> value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>
		<?php
		}
	}
	else
	{
	?>
	<?php
	}


?>
<?php require_once 'disconnect.php';  ?>

      
	