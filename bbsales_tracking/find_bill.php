<?php
$page_id=555;$page_slug='page_executive';
$page_slug = "find Bill";
include("connect.php");	
?>

	<?php
		if(!empty($_POST["customer_id"]))
		{
			$bill_r = $db->rp_getData("invoice","*","customer_id = '".$_POST["customer_id"]."' AND isDelete=0","",0);

		?>
			<option value="">Select Bill No.</option>
		<?php
			while($bill_d = mysqli_fetch_assoc($bill_r))
			{
		?>
			<option <?php echo ($dispatch_id==$bill_d['id'])?"selected":"" ; ?> data-grand_total="<?php echo $bill_d['amount'];?>" data-sales_executive_id="<?php echo $bill_d['sales_executive_id']; ?>" value="<?php echo $bill_d['id']; ?>" ><?php echo $bill_d['invoice_no']; ?></option>

		<?php
			}
		}
		else
		{
		?>
		 <?php
		}
      ?>
      
	