<?php

/*
 * @author Ravi Patel
 */

if($_REQUEST['staic']==2)
{
	//echo "hello";exit;
	$page_id=420;$page_slug='page_customer';
	require_once("connect_in.php");
	$invoice_id	= $_REQUEST['invoice_id'];
	$format_type=$_REQUEST['format_type'];
}
	$invoice_id	= $_REQUEST['invoice_id'];
	$format_type=$_REQUEST['format_type'];
	

$relCertFileNames = array();
$merge_file = array();
// $string ="<style>span{padding-right:70px}</style>";

	//echo ADMINSITEURL; exit();
//$d=file_get_contents(ADMINSITEURL."print_purchase_order.php?id=".$id);
// $d=file_get_contents(ADMINSITEURL.'invoice_view_new.php?invoice_id='.$invoice_id.'&format_type='.$format_type);

$d=file_get_contents(ADMINSITEURL.'invoice_view_new_dwonlod.php?invoice_id='.$invoice_id.'&format_type='.$format_type);
//print_r($d); exit;
//$d=file_get_contents(ADMINSITEURL.'order_view_new.php?order_id='.$order_id.'');
//$d.=$string;
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

$mpdf->shrink_tables_to_fit = 1;
$mpdf->WriteHTML($d);

$last_id = $invoice_id;
$invoice_no = $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'");
$exe_id = $db->rp_getValue("invoice_new","customer_id","isDelete=0 AND id='".$invoice_id."' ");
$flag = "Web";
$ctable = "invoice_new";
$module_name = "Invoice";
if($invoice_no=="")
{
	$invoice_no = "";
}
$log_description = $module_name." ".$invoice_no." PDF Download By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$db->insertLog($ctable,$last_id,"insert","",$insert,0,$log_description,$flag,$module_name,$user_id,$exe_id);

// $exe_id = $db->rp_getValue("invoice_new","customer_id","isDelete=0 AND id='".$invoice_id."' ");
$uname	= str_replace(" ","-",stripslashes($db->rp_getValue("invoice_new","company_name","id='".$invoice_id."'",0)));
$order_no	= str_replace("/","-",stripslashes($db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'",0)));

// $fileName = "Invoice".SITENAME."_".date('d_m_Y')."_".$order_no."_".$uname.'.pdf'; 
$fileName = $uname."_".date('d_m_Y')."_"."Invoice_".$order_no.'pdf';   


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