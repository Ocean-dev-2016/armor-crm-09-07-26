<?php
 /*
 * @author Ravi Patel
 */

include("connect.php");

$relCertFileNames = array();
$merge_file = array();
$d = html_entity_decode($_REQUEST['rc']);
require('mpdf60/mpdf.php');

$mpdf = new mPDF('',    // mode - default ''

 'A4L',    // format - A4, for example, default ''

 15,     // font size - default 0

 'sans-serif',    // default font family

 3,    // margin_left

 3,    // margin right

 3,     // margin top

 3,    // margin bottom

 0,     // margin header

 0,     // margin footer

 'L');  // L - landscape, P - portrait

$mpdf->WriteHTML($d);

$fileName = "api_document_".SITETITLE."_".date('Y-m-d');

if(!is_dir($fileName)){

	mkdir(API_REPORT_FILES.$fileName);
}

$pdf_file_path	= API_REPORT_FILES.$fileName."/".$fileName.'.pdf';

if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);

echo $pdf_file_path;

$xl_file_path	= API_REPORT_FILES.$fileName."/".$fileName.'.xls';

if(file_exists($xl_file_path)){

	unlink($xl_file_path);

}

file_put_contents($xl_file_path, $d);

?>
<?php require_once 'disconnect.php';  ?>