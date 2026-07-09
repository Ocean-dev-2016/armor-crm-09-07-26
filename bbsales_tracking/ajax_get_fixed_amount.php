<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$subcategory="";
$id = $_REQUEST['id'];
$sales_id = $_REQUEST['sales_id'];
$expence_subcat_r=$db->rp_getData("expence_sub_category","*","expense_category_id=".$id." AND sales_executive_id=".$sales_id." AND isDelete=0","",0);

//$expence_subcat_r=$db->rp_getData("expence_sub_category","*","expense_category_id=".$id." AND isDelete=0","",0);
if($expence_subcat_r)
{
	?>
	<option value="">-- Select Expence Sub Category --</option>
	<?php

	while($expence_subcat_d = mysqli_fetch_assoc($expence_subcat_r))
	{
		
		?>
		    <option data-expense_amount="<?php echo $expence_subcat_d['fix_amount']?>" value="<?php echo $expence_subcat_d['id']; ?>"><?php echo $expence_subcat_d['name']; ?></option>
		<?php
    }
}
else
{
?>
    <option value="">Select Category</option>
<?php
}
?>
<?php require_once 'disconnect.php';  ?>