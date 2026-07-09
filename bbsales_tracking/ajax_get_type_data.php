<?php
$page_id=617;$page_slug='daily_activity_report';
include("connect.php");
$type=$_REQUEST['id'];
$mode=$_REQUEST['mode'];
if($_REQUEST['mode']=="customer")
{
	?>
	<option value="">select Customer</option>
	<?php
	$executiveR=$db->rp_getData('executive',"*","isDelete=0","",0);
	if($executiveR)
	{
		while($executiveD=mysqli_fetch_assoc($executiveR))
		{
			?>
				<option value="<?php echo $executiveD['id']?>"><?php echo $executiveD['company_name']." - ".$executiveD['cname']?></option>
			<?php
		}	
	}
	else
	{
		?>
		<option value="">select Customer</option>
		<?php
	}
}
else if($_REQUEST['mode']=="sales_executive")
{
	?>
	<option value="">Select Sales Person</option>
	<?php
	$SalesexecutiveR=$db->rp_getData('dealer_distributor_network',"*","isDelete=0","",0);
	if($SalesexecutiveR)
	{
		while($SalesexecutiveD=mysqli_fetch_assoc($SalesexecutiveR))
		{
			?>
				<option value="<?php echo $SalesexecutiveD['id']?>"><?php echo $SalesexecutiveD['name']?></option>
			<?php
		}	
	}
	else
	{
		?>
		<option value="">Select Sales Person</option>
		<?php
	}
}
else
{
	?>
	<option value="">Select Inquiry</option>
	<?php
	$InquiryR=$db->rp_getData('no_order_inquiry',"*","isDelete=0","",0);
	if($InquiryR)
	{
		while($inquiryD=mysqli_fetch_assoc($InquiryR))
		{
			?>
				<option value="<?php echo $inquiryD['id']?>"><?php echo "#INQ/".$inquiryD['id']?></option>
			<?php
		}	
	}
	else
	{
		?>
		<option value="">Select Inquiry</option>
		<?php
	}
}
?>
<?php require_once 'disconnect.php';  ?>