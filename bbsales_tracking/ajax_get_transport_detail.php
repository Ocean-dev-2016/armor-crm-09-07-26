<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$id=$_REQUEST['id'];
$selected_id=$_REQUEST['selected_id'];
?>
<option value="">Select Transporter Detail</option>
<?php
$transport_d=$db->rp_getData('transport_master',"*","transport_by_id='".$id."' AND isDelete=0","name ASC",0);
while($transport_detail=mysqli_fetch_assoc($transport_d))
{
	?>
		<option <?php if($transport_detail['id']==$selected_id){ echo "selected"; } else { echo ""; }  ?> value="<?php echo $transport_detail['id']?>"><?php echo $transport_detail['name']?></option>
	<?php
}
?>
<?php require_once 'disconnect.php';  ?>