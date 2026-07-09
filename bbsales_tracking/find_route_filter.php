<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
?>

<?php


if(!empty($_POST["area"]))
{
	//$area_r = $db->rp_getData("area","*","area = '".$_POST["area"]."'",0);
	$area_r = $db->rp_getData("area","*","city_id IN (".$_POST['area'].")","",0);

?>
<option value="">Select Rought</option>
	
<?php
	while($area_d = mysqli_fetch_assoc($area_r))
	{
?>
	<option <?= ($area_d['id']==$_REQUEST['area'])?"selected":""; ?> value="<?php echo $area_d['id']; ?>" ><?php echo $area_d['name']; ?></option>

<?php
	}
}
else
{
?>
<option value="">Select Class First</option>
 <?php
}
?>
 <?php require_once("disconnect.php"); ?>
