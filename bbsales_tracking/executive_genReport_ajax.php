<?php

/*
 * @author Ravi Patel
 */

$page_id=555;$page_slug='page_executive';
require_once("connect_in.php");
$cid=$_REQUEST['cid'];
$type=$_REQUEST['type'];
$relCertFileNames = array();
$merge_file = array();

$d=file_get_contents(ADMINSITEURL."task_info_print.php?id=".$cid);

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

$fileName = "executive_information_".$cid;

if(!is_dir($fileName)){

	mkdir(EXECUTIVE_FILES.$fileName);

}

$pdf_file_path	= EXECUTIVE_FILES.$fileName."/".$fileName.'.pdf';



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}

$mpdf->Output($pdf_file_path);


echo $pdf_file_path;
require_once 'disconnect.php';
?>