<?php
$page_id=598;$page_slug='attendance_report_page';
include("connect.php");
$searchName=$_REQUEST['searchName'];
$month_id=$_REQUEST['month_id'];
$filter_year=$_REQUEST['filter_year'];
$sales_executive=$_REQUEST['sales_executive'];
$sales_executive_type=$_REQUEST['sales_executive_type'];
$df1=$_REQUEST['df1'];
$io=$_REQUEST['io'];
$bid = date('d-m-Y');
$d = file_get_contents(ADMINSITEURL_STATIC . "bbsales_tracking/attendance_report_view.php?searchName=".urlencode($searchName)."&df1=".urlencode($df1)."&se_id=".urlencode($sales_executive)."&month_id=".urlencode($month_id)."&io=".urlencode($io)."&filter_year=".urlencode($filter_year)."&sales_executive_type=".urlencode($sales_executive_type));

// echo $d;exit;
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

$fileName = ATTENDANCE_REPORT_EXPORT_EXCEL;

if (!is_dir($fileName)) {
    mkdir(ATTENDANCE_INFO_FILES . $fileName);
}

$pdf_file_path    = ATTENDANCE_INFO_FILES . $fileName . "/" . $fileName . '.pdf';



if (file_exists($pdf_file_path)) {
    unlink($pdf_file_path);
}

$mpdf->Output($pdf_file_path);

// $Reply = array("file_name" => $fileName . ".pdf", "file_path" => "../" . ADMINFOLDER . "/" . $pdf_file_path);
// echo json_encode($Reply);

$xl_file_path    = ATTENDANCE_INFO_FILES . $fileName . "/" . $fileName . '.xls';

if (file_exists($xl_file_path)) {
    unlink($xl_file_path);
}
file_put_contents($xl_file_path, $d);
echo $xl_file_path;
require_once 'disconnect.php'; 
