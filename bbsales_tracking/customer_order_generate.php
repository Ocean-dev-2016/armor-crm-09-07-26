<?php

/*
 * @author Ravi Patel
 */

$page_id=420;$page_slug='page_customer';
include("connect_in.php");
$id=$_REQUEST['request_id'];
$relCertFileNames = array();
$merge_file = array();
$string ="<style>span{padding-right:70px}</style>";

//$d=file_get_contents(ADMINSITEURL."print_purchase_order.php?id=".$id);
$d=file_get_contents(ADMINSITEURL.'customer_order_view.php?request_id='.$id.'');
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
$uname	= str_replace(" ","-",stripslashes($db->rp_getValue("orders","customer_name","id='".$request_id."'",0)));
$fileName = $id."-".$id."-".$uname.'.pdf';

if(!is_dir($fileName)){

	mkdir(ORDERS_PDF.$fileName);

}

$pdf_file_path	= ORDERS_PDF.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);


echo $pdf_file_path;

?>