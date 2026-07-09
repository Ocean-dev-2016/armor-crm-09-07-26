<?php

/*
 * @author Ravi Patel
 */

$page_id=510;$page_slug='page_bill';
include("connect.php");
$cid=$_REQUEST['cid'];
$relCertFileNames = array();
$merge_file = array();
$string ="<style>th,tr,td{border:1px solid #000; padding:10px;}</style>";
$d = html_entity_decode($_REQUEST['rc']);
$d.=$string;
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

$fileName = "order_information_".$cid;

if(!is_dir($fileName)){

	mkdir(ORDERS_FILES.$fileName);

}

$pdf_file_path	= ORDERS_FILES.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);

require_once "disconnect.php";
echo $pdf_file_path;

?>