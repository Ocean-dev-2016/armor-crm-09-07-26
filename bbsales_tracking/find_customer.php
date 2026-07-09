<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$customer_type = $_POST["type_of_executive"];

$where ="type_of_executive='".$customer_type."' AND isDelete=0 AND isActive=1 "
?>
<?php
if(!empty($customer_type))
{
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3) // customer and its chain wise order
	{ 
	    // for customer panel only
	    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
	    $get_customer_type=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='". $check_id."'",0);
	    if ($get_customer_type==$customer_type)  //super stockist
	    {  
	        $where.=" AND id='".$check_id."'";
	        $selected_value=$check_id;
	    } 
	     
	    if ($get_customer_type==1 && $customer_type==2) 
	    {
	        $where.=" AND super_stockist_id='".$check_id."'";
	    } 
	    else if ($get_customer_type==2 && $customer_type==3)  
	    {
	        $where.=" AND dealer_distributor_id='".$check_id."'";
	    } 
	    // for customer panel only
	}

	$dd_r = $db->rp_getData("executive","*",$where,"",0);

?>
<option value="">Select Customer</option>	
<?php
	while($dd_d = mysqli_fetch_assoc($dd_r))
	{
?>
<option <?= ($customer_id==$dd_d['id'])?"selected":""; ?> value="<?php echo $dd_d['id']; ?>" ><?php echo $dd_d['company_name']." - ".$dd_d['cname']; ?></option>

<?php
	}
}
else
{
?>
<option value="">Select Customer</option>
 <?php
}
?>
