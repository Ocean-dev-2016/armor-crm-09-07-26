<?php

/*
 * @author Ravi Patel
 */

$page_id=530;$page_slug='page_expense_report_view';

include("connect.php");
$cid=date('M_Y');
$relCertFileNames = array();
$merge_file = array();
$string ="<style>table,th,tr,td{border:1px solid #000; padding:10px;font-size:15px;}</style>";
$d = html_entity_decode($_REQUEST['rc']);
$d.=$string;
require('mpdf60/mpdf.php');
$mpdf = new mPDF('',    // mode - default ''

 'A4-L',    // format - A4, for example, default ''

 10,     // font size - default 0

 'sans-serif',    // default font family

 3,    // margin_left

 3,    // margin right

 3,     // margin top

 3,    // margin bottom

 0,     // margin header

 0,     // margin footer
 
 'L');  // L - landscape, P - portrait
 
$mpdf->SetHTMLFooter('
<hr><span width="100%" style="vertical-align: bottom; border:0px; font-family: serif; font-size: 12px; color: #000000; font-weight: bold; font-style: italic;">
Downloaded On:-{DATE d-m-Y H:i:s}
</span>');
$mpdf->WriteHTML($d);

$fileName = "SALESEXECUTIVE-PERFORMANCE-REPORT-FILE-".$cid;

if(!is_dir($fileName)){

	mkdir(ORDERS_FILES.$fileName);

}

$pdf_file_path	= ORDERS_FILES.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);

$xl_file_path	= ORDERS_FILES.$fileName."/".$fileName.'.xls';

if(file_exists($xl_file_path)){

	unlink($xl_file_path);

}

file_put_contents($xl_file_path, $d);
require_once 'disconnect.php';
echo $xl_file_path;


?>