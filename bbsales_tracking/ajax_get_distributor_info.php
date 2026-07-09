<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$customer_id = $_POST["customer_id"];
$customer = array();
$where = "id = '$customer_id' AND isDelete=0";
$customer_r = $db->rp_getData("executive","*",$where,"",0);
if(mysqli_num_rows($customer_r)>0)
{
    $customer = mysqli_fetch_assoc($customer_r);
}
?>

<?php
echo json_encode($customer);
?>