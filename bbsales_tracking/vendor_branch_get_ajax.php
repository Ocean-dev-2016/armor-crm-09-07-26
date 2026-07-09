<?php
$page_id=408;$page_slug='page_vendor';
$ctable 	= "vendor_branch";
if(isset($_REQUEST['vid']) && $_REQUEST['vid']!="")
{
	$vid=$_REQUEST['vid'];
	include("connect.php");
	$ctable_r = $db->rp_getData($ctable,"*","vid='".$vid."' AND isDelete=0");
	if($ctable_r>0){
		?>
		<option value="">-- Select Branch -- </option>
		<?php
		while($ctable_d = mysqli_fetch_array($ctable_r)){
			?>
				<option value="<?php echo $ctable_d['id']?>"><?php echo $ctable_d['branch_name']?></option>
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