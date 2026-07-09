<?php
include("connect_in.php");
require('mpdf60/mpdf.php');
$dispatch_id = $_REQUEST['dispatch_id'];
$mpdf=new mPDF('',
				'A4','','',
				2,2,10,10,
				5,5); 
$data=$mpdf->WriteHTML(file_get_contents(ADMINSITEURL.'dispatch_view.php?dispatch_id='.$dispatch_id.''));

$uname	= str_replace(" ","-",stripslashes($db->rp_getValue("dispatch_detail","customer_name","id='".$dispatch_id."'",0)));
$fileName = $dispatch_id."-"."Dispatch-".$uname.'.pdf';
$mpdf->Output($fileName,'I');
/*$mpdf = new mPDF();
$filename1 = dirname(__FILE__).'/pdf/'.$fileName;
unlink($fileName);*/
if(!is_dir($fileName)){

	mkdir(DISPATCH_PDF.$fileName);

}

$pdf_file_path	= DISPATCH_PDF.$fileName."/".$fileName;



if(file_exists($pdf_file_path)){

	unlink($pdf_file_path);

}
$mpdf->Output($pdf_file_path);

?>