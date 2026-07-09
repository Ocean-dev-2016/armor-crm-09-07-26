<?php

/*
 * @author Ravi Patel
 */

$page_id=556;$page_slug='page_sales_executive';
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
$mpdf->SetHTMLFooter('
<hr><span width="100%" style="vertical-align: bottom; border:0px; font-family: serif; font-size: 12px; color: #000000; font-weight: bold; font-style: italic;">
Downloaded On:-{DATE d-m-Y H:i:s}
</span>');
$mpdf->WriteHTML($d);

$fileName = "executive_information_".$cid;

if(!is_dir($fileName)){

	mkdir(SALES_EXECUTIVE_FILES.$fileName);

}

$pdf_file_path	= SALES_EXECUTIVE_FILES.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);
require_once("disconnect.php");
echo $pdf_file_path;

?>