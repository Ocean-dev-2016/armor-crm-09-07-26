<?php
include("connect_in.php");
require('mpdf60/mpdf.php');
$id = $_REQUEST['id'];
$mpdf=new mPDF('',
				'A4','','',
				2,2,10,10,
				5,5); 
				//echo ADMINSITEURL.'bill_view_pdf.php?id='.$id.''; exit;
$mpdf->SetHTMLFooter('
<hr><span width="100%" style="vertical-align: bottom; border:0px; font-family: serif; font-size: 12px; color: #000000; font-weight: bold; font-style: italic;">
Downloaded On:-{DATE d-m-Y H:i:s}
</span>');
$data=$mpdf->WriteHTML(file_get_contents(ADMINSITEURL.'bill_view_pdf.php?id='.$id.''));
$date=date('d-m-Y');
$Receipt_no	= str_replace(" ","-",stripslashes($db->rp_getValue("payment","receipt_no","id='".$id."'",0)));
$fileName = "payment"."-".$Receipt_no."/-".$date.'.pdf';

$mpdf->Output($fileName,'I');
if(!is_dir($fileName)){

	mkdir(BILL_PDF.$fileName);

}
$pdf_file_path	= BILL_PDF.$fileName."/".$fileName;
if(file_exists($pdf_file_path)){
	unlink($pdf_file_path);
}
$mpdf->Output($pdf_file_path);

?>