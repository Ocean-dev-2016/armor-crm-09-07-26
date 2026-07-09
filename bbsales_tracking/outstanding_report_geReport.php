<?php
// echo "string"; exit;
$PageConfig = array("id" => 618, "navigation" => false);

include("connecting.php");
$bid              = date('d-m-Y');
$name             = "Outstanding Report";
$relCertFileNames = array();
$merge_file       = array();
$customer_id=$_REQUEST['customer_id'];
$d = file_get_contents(ADMINSITEURL."outstanding_report_view_ajax.php?cid=".$customer_id);
require('mpdf60/mpdf.php');


$mpdf = new mPDF(
    '',    // mode - default ''

    'A4-L',    // format - A4, for example, default ''

    10,     // font size - default 0

    'sans-serif',    // default font family

    3,    // margin_left

    3,    // margin right

    3,     // margin top

    3,    // margin bottom

    0,     // margin header

    0,     // margin footer

    'L'
);  // L - landscape, P - portrait

$mpdf->WriteHTML($d);

$fileName = "Outstanding-report-" . $bid;
if(!is_dir($fileName)){

    mkdir(TARGET_ARCHIVE_REPORT_FILES.$fileName);

}

$pdf_file_path  = TARGET_ARCHIVE_REPORT_FILES.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){
    unlink($pdf_file_path);
}

$mpdf->Output($pdf_file_path);
$Reply=array("file_name"=>$fileName.".pdf","file_path"=>ADMINSITEURL.$pdf_file_path);
echo json_encode($Reply);
/*
$xl_file_path   = TARGET_ARCHIVE_REPORT_FILES.$fileName."/".$fileName.'.xls';

if(file_exists($xl_file_path)){

    unlink($xl_file_path);

}

file_put_contents($xl_file_path, $d);

$xl_file_path;*/
?>