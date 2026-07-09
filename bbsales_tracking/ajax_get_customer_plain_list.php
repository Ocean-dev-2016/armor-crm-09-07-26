<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$customer_type 		= $_POST["customer_type"];
$where = "type_of_executive = '".$customer_type."' AND isDelete=0";

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

$customer_r = $db->rp_getData("executive","*",$where,"",0);
if(mysqli_num_rows($customer_r)>0)
{
?>
    <option value="">Select Customer</option>
    <?php
    while($customer_d = mysqli_fetch_array($customer_r))
    {
?>
<option value="<?=$customer_d['id'];?>"><?=$customer_d['company_name']." - ".$customer_d['cname'];?></option>
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
<?php require_once 'disconnect.php';  ?>