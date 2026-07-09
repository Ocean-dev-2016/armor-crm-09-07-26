<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$customer_type 		= $_POST["customer_type"];
$selected_value      = $_POST["selected_value"];
$where = "id = '$selected_value' AND isDelete=0";
$customer_r = $db->rp_getData("executive","*",$where,"",0);
if(mysqli_num_rows($customer_r)>0)
{
	while($customer_d = mysqli_fetch_assoc($customer_r))
    {
        if($customer_d['price_list_id']!=0)
        {
            $customer_d['price_list_name']=$db->rp_getValue("price_list","pricelist_name","id='".$customer_d['price_list_id']."'");
        }
        else
        {
            $customer_d['price_list_name']="N/A";
        }
        $result = $customer_d;
    }
    echo json_encode($result);
}
?>
<?php require_once 'disconnect.php';  ?>