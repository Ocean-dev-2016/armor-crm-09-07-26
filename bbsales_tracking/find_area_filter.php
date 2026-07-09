<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
 
if(!empty($_POST["class_id"]))
{
	$stateArr = explode(",",$_POST["class_id"]);
	foreach ($stateArr as $value) {  
	    $state_id[]=$db->rp_getValue("class","id","id = '".$value ."'",0);
	}
	 
	$area_r = $db->rp_getData("city","*","state_id IN (".implode(",",$state_id).") AND isDelete=0","",0);
	// $area_r = $db->rp_getData("area","*","class_id IN (".$_POST['class_id'].")","",0);

?>
<option value="">Select Area</option>
	
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
