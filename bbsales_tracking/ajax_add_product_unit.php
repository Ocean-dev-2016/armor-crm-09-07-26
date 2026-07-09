<?php 
$page_id=400;$page_slug='dashboard';
include('connect.php');  

$ctable_where="isDelete=0";
if(isset($_REQUEST['tcid']) && $_REQUEST['tcid']!="" && $_REQUEST['tcid']!=NULL && $_REQUEST['tcid']!=undefined)
{
 	$ctable_where .= " AND tcid = '".$_REQUEST['tcid']."' "; 
}
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="" && $_REQUEST['cid']!=NULL && $_REQUEST['cid']!=undefined)
{
 	$ctable_where .= " AND cid = '".$_REQUEST['cid']."' ";  
}


$unit_id = ($_REQUEST['unit_id'])?$_REQUEST['unit_id']:"";
$customer_unit_id = ($_REQUEST['customer_unit_id'])?$_REQUEST['customer_unit_id']:"";
$inner_unit = ($_REQUEST['inner_unit'])?$_REQUEST['inner_unit']:"";
$outer_unit = ($_REQUEST['outer_unit'])?$_REQUEST['outer_unit']:"";

$proR = $db->rp_getData("product","*",$ctable_where,"",0);
while ($proD = mysqli_fetch_assoc($proR)) 
{ 
	$update = $db->rp_update("product",array("unit_id"=>$unit_id,"customer_unit_id"=>$customer_unit_id),"id='".$proD['id']."'",0);

	$update = $db->rp_update("product_weight_price",array("inner_unit"=>$inner_unit,"outer_unit"=>$outer_unit),"product_id='".$proD['id']."'",0);
	
	/*if($update)
	{
		$proR = $db->rp_getData("product","*",$ctable_where,"",0);
		while ($proD = mysqli_fetch_assoc($proR)) 
		{
		}
	}*/
}
?>  
<?php require_once 'disconnect.php';  ?>     

 