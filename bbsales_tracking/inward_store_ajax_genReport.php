<?php
$page_id=413;$page_slug='inward_store_page';
include("connect.php");
$inward_id=$_REQUEST['inward_id'];
$relCertFileNames = array();
$merge_file = array();
$string ="<style>table,th,tr,td{border:1px solid #000;border-collapse:collapse; padding:10px;width:100px;}</style>";
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

$fileName = "inward_store_info_".$inward_id;

if(!is_dir($fileName)){

	mkdir(INWARD_STORE_FILES.$fileName);

}
$pdf_file_path	= INWARD_STORE_FILES.$fileName."/".$fileName.'.pdf';

if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);
require_once 'disconnect.php';
echo $pdf_file_path;

?>