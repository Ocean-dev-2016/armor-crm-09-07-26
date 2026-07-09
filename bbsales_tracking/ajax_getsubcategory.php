<?php
$page_id=580;$page_slug='price_list_master';
require_once("connect.php");
$subcategory="";
$id 		= $_REQUEST['id'];
$selected 		= isset($_POST['selected'])?$_POST['selected']:"";
$where = "tcid = '".$id."' AND isDelete=0";
$state_r = $db->rp_getData("category_master","name,id,tcid",$where,"",0);
if($state_r)
{
?>
    <option value="">Select Category</option>
    <?php
	while($state_d = mysqli_fetch_assoc($state_r))
	{
		if($selected!="" && $selected==$state_d['name'])
		{
			?>
			<option value="<?php echo $state_d['id']; ?>" selected=""><?php echo $state_d['name']; ?></option>
			<?php
		}
		else
		{
		?>
		    <option value="<?php echo $state_d['id']; ?>"><?php echo $state_d['name']; ?></option>
		<?php

		}
    }
}else{
?>
    <option value="">Select Category</option>
<?php
}
?>
<?php require_once 'disconnect.php';  ?>