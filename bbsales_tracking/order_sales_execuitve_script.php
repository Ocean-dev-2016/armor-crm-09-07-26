<?php
include("connect.php");
$get_order_data_r=$db->rp_getData("orders","*","isDelete=0","",0);
while($get_order_data_d=mysqli_fetch_assoc($get_order_data_r))
{

   $order_id=$get_order_data_d['id'];
   $created_by=$get_order_data_d['created_by'];

   if($get_order_data_d['sales_id']  == "" || $get_order_data_d['sales_id'] == 0)
   {
      $get_sales_executive_id=$db->rp_getValue("dealer_distributor_network","sales_executive_id","isDelete=0 AND id='". $created_by ."'",0);

      $rows=array("sales_id" => $get_sales_executive_id);

      $is_update=$db->rp_update("orders",$rows,"isDelete=0 AND id='".$order_id."'");
   }
}
?>