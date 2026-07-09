<?php
$page_id=503;$page_slug='page_customer';
$ctable 	= "executive_branch";
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
{
	$cid=$_REQUEST['cid'];
	include("connect.php");
	$ctable_r = $db->rp_getData($ctable,"*","cid='".$cid."' AND isDelete=0");
	if($ctable_r>0){
		?>
		<option value="">-- Select Branch -- </option>
		<?php
		while($ctable_d = mysqli_fetch_array($ctable_r)){
			?>
				<option value="<?php echo $ctable_d['id']?>"  <?php echo ($contact_branch==$ctable_d['id'])?"selected":"" ; ?>><?php echo $ctable_d['branch_name']?></option>
			<?php 
		}
		
	}
	else
	{
		?>
		<option value="">-- Select Branch -- </option>
		<?php
	}
	
	
}
else
{
	?>
	<option value="">-- Select Branch -- </option>
	<?php
}
?>
<?php require_once 'disconnect.php';  ?>