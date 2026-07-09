<?php
$page_id=555;$page_slug='page_executive';
$page_slug = "find Bill";
include("connect.php");	
?>
<select class="form-control sales_executive_id" name="sales_executive_id[]" id="sales_executive_id" multiple="multiple">
<?php
	if(!empty($_POST["customer_id"]))
	{
		$area_r = $db->rp_getData("invoice","*","customer_id = '".$_POST["customer_id"]."' AND isDelete=0","",0);
		?>
		<option value="">Select Sales Officer</option>
		<?php
		$name = array();
		while($area_d = mysqli_fetch_array($area_r))
		{
		    $sales_executive_id = array();
		    $name = $db->rp_getData("sales_executive","name","id IN (".$area_d['sales_executive_id'].") AND isDelete=0",0);
		    while($name_d = mysqli_fetch_array($name))
		    {
		?>
		<option <?php echo (in_array($area_d['id'],$_POST['customer_id']))?"selected":""; ?> value="<?php echo $area_d['sales_executive_id']; ?>" ><?php echo $name_d['name']; ?></option>
		<?php
		    }
		    
		}
	}
	else
	{
	?>
	<?php
	}
?>
</select>