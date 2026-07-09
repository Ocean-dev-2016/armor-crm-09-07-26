<?php
$page_id=556;$page_slug='page_inquiry_report';
include("connect_in.php");
$status_id=$_REQUEST['status_id'];
$searchName=$_REQUEST['searchName'];
$type=$_REQUEST['type'];
$string ="<style>th,tr,td{border:1px solid #000; padding:10px;}</style>";
$url=ADMINSITEURL."inquiry_info.php?status_id=".$status_id."&searchName=".$searchName."&type=".$type;
$d=file_get_contents($url);
$d=html_entity_decode($d);
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

/*$mpdf->SetHTMLFooter('
<hr><span width="100%" style="vertical-align: bottom; border:0px; font-family: serif; font-size: 12px; color: #000000; font-weight: bold; font-style: italic;">
Downloaded On:-{DATE d-m-Y H:i:s}
</span>');*/
$mpdf->WriteHTML($d);

$fileName = "INQUIRY_REPORT_FILES".$cid;

if(!is_dir($fileName)){

	mkdir(INQUIRY_REPORT_FILES.$fileName);

}

$pdf_file_path	= INQUIRY_REPORT_FILES.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);

require_once 'disconnect.php';
echo $pdf_file_path;

?>