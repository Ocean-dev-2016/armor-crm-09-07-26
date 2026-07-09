<?php
$page_id=555;$page_slug='page_executive';
$page_slug = "find Bill";
include("connect.php");	
$where = "isDelete=0";
// print_r($_REQUEST);exit();
if($_POST['city_id']!="" && $_POST['city_id']!=0)
{
	$where .= " AND city_id IN (".$_POST['city_id'].")";
}
if (isset($_POST['class_id']) && !empty($_POST['class_id'])) {
	$where .= " AND class_id = '".$_POST['class_id']."'";
}
?>
<select class="form-control" name="area_id" id="area_id" multiple="multiple">
<?php
	// if(!empty($_POST["city_id"]))
	// {
		$area_r = $db->rp_getData("area","*",$where,"",0);
		?>
		<option value="">Select Area</option>
		<?php
		while($area_d = mysqli_fetch_array($area_r))
		{
			//print_r($_POST['class_id']); exit;
		?>
			<option <?php echo (in_array($area_d['id'],$_POST['class_id']))?"selected":""; ?> value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>
		<?php
		}
	// }
	// else
	// {
	?>
	<?php
	// }
?>
</select>
<?php require_once 'disconnect.php';  ?>
      
	