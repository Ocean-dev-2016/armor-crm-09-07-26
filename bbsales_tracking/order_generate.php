<?php

/*
 * @author Ravi Patel
 */

if($_REQUEST['staic']==2)
{
	$page_id=420;$page_slug='page_customer';
	require_once("connect_in.php");
	$order_id	= $_REQUEST['order_id'];
}

$relCertFileNames = array();
$merge_file = array();
// $string ="<style>span{padding-right:70px}</style>";

//$d=file_get_contents(ADMINSITEURL."print_purchase_order.php?id=".$id);
/*old*/
// $d=file_get_contents(ADMINSITEURL.'order_view_download.php?order_id='.$order_id.'');
/*old*/

$d=file_get_contents(ADMINSITEURL.'order_view_download_1.php?order_id='.$order_id.'');

//$d=file_get_contents(ADMINSITEURL.'order_view_new.php?order_id='.$order_id.'');
//$d.=$string;
require('mpdf60/mpdf.php');



$mpdf = new mPDF('',    // mode - default ''

 'A4',    // format - A4, for example, default ''

 15,     // font size - default 0

 'sans-serif',    // default font family

 3,    // margin_left

 3,    // margin right

 3,     // margin top

 3,    // margin bottom

 0,     // margin header

 0,     // margin footer

 'P');  // L - landscape, P - portrait

$mpdf->WriteHTML($d);

$last_id = $order_id;
$quotation_no = $db->rp_getValue("orders","order_no","id='".$order_id."'");
$customer_id = $db->rp_getValue("orders","customer_id","id='".$order_id."'");
$flag = "Web";
$ctable = "orders";
$module_name = "Orders";
$log_description = $module_name." ".$quotation_no." PDF Download By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);

$uname	= str_replace(" ","-",stripslashes($db->rp_getValue("orders","company_name","id='".$order_id."'",0)));
$uname  = str_replace("/","-",stripslashes($db->rp_getValue("orders","company_name","id='".$order_id."'",0)));
$order_no	= str_replace("/","-",stripslashes($db->rp_getValue("orders","order_no","id='".$order_id."'",0)));

// $fileName = "Sales_Order".SITENAME."_".date('d_m_Y')."_".$order_no."_".$uname.'.pdf';  
$fileName = $uname."_".date('d_m_Y')."_"."Order_".$order_no.'pdf'; 


if(!is_dir($fileName)){

	mkdir(ORDERS_PDF.$fileName);

}

$pdf_file_path	= ORDERS_PDF.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);


if($_REQUEST['staic']==2)
{
	echo $pdf_file_path;
}
else
{
	$file_path = $pdf_file_path;
}

?>