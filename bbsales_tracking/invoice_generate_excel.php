<?php
if($_REQUEST['staic']==2)
{
	$page_id=420;$page_slug='page_customer';
	require_once("connect_in.php");
	$invoice_id	= $_REQUEST['invoice_id'];
}
$relCertFileNames = array();
$merge_file = array();
$d=file_get_contents(ADMINSITEURL.'invoice_view_new.php?invoice_id='.$invoice_id.'');
require('mpdf60/mpdf.php');
$mpdf = new mPDF('',    // mode - default ''

 'A4',    // format - A4, for example, default ''

 15,     // font size - default 0

 'sans-serif',    // default font family

 8,    // margin_left

 8,    // margin right

 8,     // margin top

 8,    // margin bottom

 0,     // margin header

 0,     // margin footer

 'P');  // L - landscape, P - portrait

$mpdf->WriteHTML($d);
$uname	= str_replace(" ","-",stripslashes($db->rp_getValue("invoice_new","company_name","id='".$invoice_id."'",0)));
$uname  = str_replace("/","-",stripslashes($uname));
$order_no	= $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'",0);
$fileName = $uname."_".date('d_m_Y_h_i_s')."_"."Invoice_".$order_no; 
if(!is_dir($fileName))
{
	mkdir(ORDERS_PDF.$fileName);
}
$excel_file_path	= ORDERS_PDF.$fileName."/".$fileName.'.xls';
if(file_exists($excel_file_path))
{
	unlink($excel_file_path);
}
file_put_contents($excel_file_path, $d);
if($_REQUEST['staic']==2)
{
	echo $excel_file_path;
}
else
{
	$file_path = $excel_file_path;
}
require_once 'disconnect.php'; 
?>